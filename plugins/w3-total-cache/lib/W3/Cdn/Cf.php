<?php

require_once W3TC_LIB_W3_DIR . '/Cdn/S3.php';

class W3_Cdn_Cf extends W3_Cdn_S3
{
    /**
     * Returns array of CDN domains
     *
     * @return string
     */
    function get_domains()
    {
        if (!empty($this->_config['cname'])) {
            return (array) $this->_config['cname'];
        } elseif (!empty($this->_config['id'])) {
            $domain = sprintf('%s.cloudfront.net', $this->_config['id']);
            
            return array(
                $domain
            );
        }
        
        return array();
    }
    /**
     * Tests CF
     *
     * @param string $error
     * @return boolean
     */
    function test(&$error)
    {
        /**
         * Test S3 first
         */
        if (!parent::test($error)) {
            return false;
        }
        
        /**
         * Search active CF distribution
         */
        $this->set_error_handler();
        
        $dists = @$this->_s3->listDistributions();
        
        $this->restore_error_handler();
        
        if (!$dists) {
            $error = sprintf('Unable to list distributions (%s).', $this->get_last_error());
            
            return false;
        }
        
        $search = sprintf('%s.s3.amazonaws.com', $this->_config['bucket']);
        $dist = false;
        
        if ($dists) {
            foreach ((array) $dists as $_dist) {
                if (isset($_dist['origin']) && $_dist['origin'] == $search) {
                    $dist = $_dist;
                    break;
                }
            }
        }
        
        if (!$dist) {
            $error = sprintf('Distribution for bucket "%s" not found.', $this->_config['bucket']);
            
            return false;
        }
        
        if (!$dist['enabled']) {
            $error = sprintf('Distribution for bucket "%s" is disabled.', $this->_config['bucket']);
            
            return false;
        }
        
        if (!empty($this->_config['cname'])) {
            $domains = (array) $this->_config['cname'];
            $cnames = (isset($dist['cnames']) ? (array) $dist['cnames'] : array());
            
            foreach ($domains as $domain) {
                $_domains = array_map('trim', explode(',', $domain));
                
                foreach ($_domains as $_domain) {
                    if (!in_array($_domain, $cnames)) {
                        $error = sprintf('Domain name %s is not in distribution CNAME list.', $_domain);
                        
                        return false;
                    }
                }
            }
        } elseif (!empty($this->_config['id'])) {
            $domain = $this->get_domain();
            
            if ($domain != $dist['domain']) {
                $error = sprintf('Distribution domain name mismatch (%s != %s).', $domain, $dist['domain']);
                
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Create bucket
     * 
     * @param string $container_id
     * @param string $error
     * @return boolean
     */
    function create_container(&$container_id, &$error)
    {
        if (parent::create_container($container_id, $error)) {
            $cnames = array();
            
            if (!empty($this->_config['cname'])) {
                $domains = (array) $this->_config['cname'];
                
                foreach ($domains as $domain) {
                    $_domains = array_map('trim', explode(',', $domain));
                    
                    foreach ($_domains as $_domain) {
                        $cnames[] = $_domain;
                    }
                }
            }
            
            $this->set_error_handler();
            
            $dist = @$this->_s3->createDistribution($this->_config['bucket'], true, $cnames);
            
            $this->restore_error_handler();
            
            if (!$dist) {
                $error = sprintf('Unable to create distribution for bucket %s (%s).', $this->_config['bucket'], $this->get_last_error());
                
                return false;
            }
            
            $matches = null;
            
            if (preg_match('~^(.+)\.cloudfront\.net$~', $dist['domain'], $matches)) {
                $container_id = $matches[1];
            }
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Returns via string
     *
     * @return string
     */
    function get_via()
    {
        return sprintf('Amazon Web Services: CloudFront: %s', parent::get_via());
    }
}
