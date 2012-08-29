<?php

Class AddThis_addjs{
    /**
    * var bool check to see if we have added our JS already.  Ensures that we don't add it twice
    */
    private $_js_added;

    private $_options;

    private $_cuid;

    private $_atPlugins = array(
        'AddThis Social Bookmarking Widget' => array('http://wordpress.org/extend/plugins/addthis/', 'Share') ,
        'AddThis Follow Widget' => array('http://wordpress.org/extend/plugins/addthis-follow/', 'Follow'),
//        'AddThis Trending Content Widget' => array('http://wordpress.org/extend/plugins/addthis-trending', 'Trending' ),
        'AddThis Welcome Bar' => array('http://wordpress.org/extend/plugins/addthis-welcome/', 'Welcome'), 
    );
    private $_atInstalled = array();

    var $pubid;
    
    var $jsToAdd;

    var $jsAfterAdd;

    var $atversion;

    var $productCode;

    const addjs_version = 1;

    /**
    *
    */
    public function __construct ($options){
        if ( did_action('addthis_addjs_created') !== 0){
            _doing_it_wrong( 'addthis_addjs', 'Only one instance of this class should be initialized.  Look for the $addthis_addjs global first',1 ); 
        }

        $this->productCode = 'wpp-265';

        // Version of AddThis code to use
        $this->atversion = '250';

        // We haven't added our JS yet. Or at least better not have.
        $this->_js_added = false;

        $this->_options = $options;

        // set the cuid
        $base = home_url();
        $cuid = hash_hmac('md5', $base, 'addthis'); 
        $this->_cuid = $cuid;

        // If the footer option isn't set, check for it
        if (! isset($this->_options['wpfooter']) && current_user_can('manage_options'))
        {
            add_action('admin_init',array($this, 'update_wpfooter'));
        }

        $this->pubid = $this->getProfileId();

        // on theme swich, check for footer again
        add_action('switch_theme', array($this, 'switch_theme'),15);

        // In order for our wp_footer magic to work, we need to sometimes add our stuff 
        add_action('init', array($this, 'maybe_add_footer_comment'));


        // Footer
        if ( isset($this->_options['wpfooter']) && $this->_options['wpfooter'])
            add_action('wp_footer', array($this, 'output_script') );
        else
            add_filter('the_content', array($this, 'output_script_filter') );

        do_action('addthis_addjs_created');
    }

    function switch_theme(){
        $footer = $this->check_for_footer();
        $this->_options['wpfooter'] = $footer;
        update_option( 'addthis_settings', $this->_options); 
    }

    function output_script(){
        if ($this->_js_added != true)
        {
            $this->wrapJs();
            $this->addWidgetToJs();
            $this->addAfterToJs();
            echo $this->jsToAdd;
            $this->_js_added = true;
        }
    }

    function output_script_filter($content){
        if ($this->_js_added != true && ! is_admin() && ! is_feed() )
        {
            $this->wrapJs();
            $this->addWidgetToJs();
            $this->addAfterToJs();
            $content = $content . $this->jsToAdd;
            $this->_js_added = true;
        }
        return $content;
    }

    function wrapJs(){
        $this->jsToAdd .= "var addthis_product = '".$this->productCode."';\n";
        $this->jsToAdd = '<script type="text/javascript">' . $this->jsToAdd . '</script>';
    }

    /* testing for wp_footer in a theme stuff */
    function update_wpfooter(){
        $footer = $this->check_for_footer();
        $options = $this->_options;
        $options['wpfooter'] = $footer;
        update_option( 'addthis_settings', $options); 
        $this->_options = $options;
    }

    function check_for_footer(){
        $url = add_query_arg( array( 'attest' => 'true') , home_url() );
        $response = wp_remote_get( $url, array( 'sslverify' => false ) );
        $code = (int) wp_remote_retrieve_response_code( $response );
            if ( $code == 200 ) {
                $html = preg_replace( '/[   
                s]/', '', wp_remote_retrieve_body( $response ) );
                return (bool)( strstr( $html, '<!--wp_footer-->' ) );
            }
    }
    
    function maybe_add_footer_comment(){
        if ( $_GET['attest'] = 'true' )
        {
            add_action( 'wp_footer', array($this, 'test_footer' ), 99999 ); // Some obscene priority, make sure we run last
        }
    }

    function test_footer(){
        echo '<!--wp_footer-->';
    }
    
    /* END testing for wp_footer in a theme stuff */
    function addToScript($newData){
        $this->jsToAdd .= $newData;
    }
    
    function addAfterScript($newData){
        $this->jsAfterAdd .= $newData;
    }

    function addWidgetToJs(){
        $this->jsToAdd .= '<script type="text/javascript" src="//s7.addthis.com/js/'.$this->atversion.'/addthis_widget.js#pubid='. urlencode( $this->pubid ).'"></script>';
    }

    function addAfterToJs(){
        if (! empty($this->jsAfterAdd));
            $this->jsToAdd .= '<script type="text/javascript">' . $this->jsAfterAdd . '</script>';
    }


    /*  User name and other shared resources */
    function getUsername(){
        return (isset($this->_options['username']))?  $this->_options['username'] : false;

    }
    function setUsername($username){
        $this->_options['username'] = sanitize_text_field($username);
        update_option( 'addthis_settings', $options); 
    }

    function getProfileId(){
        return( isset( $this->_options['profile'] ) && ! empty($this->_options['profile']) )?  $this->_options['profile'] : $this->_cuid;
    }

    function setProfileId($profile){
        $this->_options['profile'] = sanitize_text_field($profile);
        update_option( 'addthis_settings', $this->_options); 
    }   

    function getPassword(){
        return (isset($this->_options['password']))?  $this->_options['password'] : $this->_cuid;
    }

    function setPassword($password){
        $this->_options['password'] = sanitize_text_field($password);
        update_option( 'addthis_settings', $options); 
    }

    function getAtPluginPromoText(){
        if (! did_action('admin_init') && !  current_filter('admin_init'))
        {
            _doing_it_wrong('getAtPluginPromoText', 'This function should only be called on an admin page load and no earlier the admin_init', 1);
            return null;
        }
        if (apply_filters('addthis_crosspromote', '__return_true'))
        {
            $plugins = get_plugins();
            if (empty($this->_atInstalled))
            {
                foreach($plugins as $plugin)
                {
                    if (substr($plugin['Name'], 0, 7) === 'AddThis')
                        array_push($this->_atInstalled, $plugin['Name']);
                }
            }
            $keys = array_keys($this->_atPlugins);
            $uninstalled = array_diff( $keys, $this->_atInstalled);
            if (empty($uninstalled))
                return false;

            // Get rid of our keys, we just want the names which are the keys elsewhere
            $uninstalled = array_values($uninstalled);

            $string = __('Want to increase your site traffic?  AddThis also has ');
            $count = count($uninstalled);
            if ($count == 1){
                $string .= __('a plugin for ', 'addthis');
                $string .= __( sprintf('<a href="%s" target="_blank">' .$this->_atPlugins[$uninstalled[0]][1] .'</a>', $this->_atPlugins[$uninstalled[0]][0]), 'addthis');
            }  else {
                $string . __('plugins for ');
                
                for ($i = 0; $i < $count; $i++) {
                    $string .= __( sprintf('<strong><a href="%s" target="_blank" >' .$this->_atPlugins[$uninstalled[$i]][1] .'</a></strong>', $this->_atPlugins[$uninstalled[$i]][0]), 'addthis');
                    if ($i < ($count - 2))
                        $string .= ', ';
                    else if ($i == ($count -2))
                        $string .= ' and ';
                    else if ($i == ($count -1))
                        $string .= ' plugins available.';
                    
                }


            }

            return '<p class="addthis_more_promo">' .$string . '</p>';
            





        }
    }

}

