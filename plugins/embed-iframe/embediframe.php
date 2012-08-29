<?php
/*
Plugin Name: Embed Iframe
Plugin URI: http://blog.deskera.com/wordpress-plugin-embed-iframe
Description: Allows the insertion of code to display an external webpage within an iframe. The tag to insert the code is: <code>[iframe url width height]</code>
Version: 1.0
Author: Deskera
Author URI: http://deskera.com

1.0   - Initial release
*/

include (dirname (__FILE__).'/plugin.php');

class EmbedIframe extends EmbedIframe_Plugin
{
	function EmbedIframe ()
	{
		$this->register_plugin ('embediframe', __FILE__);
		
		$this->add_filter ('the_content');
		$this->add_action ('wp_head');
	}
	
	function wp_head ()
	{
		
	}
	
	function replace ($matches)
	{
		$tmp = strpos ($matches[1], ' ');
		if ($tmp)
		{
			// Because the regex is such a nuisance
			$url  = substr ($matches[1], 0, $tmp);
			$rest = substr ($matches[1], strlen ($url));
			
			$width  = 400;
			$height = 500;
			

				$parts = array_values (array_filter (explode (' ', $rest)));
				$width = $parts[0];
				
				unset ($parts[0]);
				$height = implode (' ', $parts);

			
			return $this->capture ('iframe', array ('url' => $url, 'width' => $width, 'height' => $height));
		}
		
		return '';
	}

	function the_content ($text)
	{
	  return preg_replace_callback ("@(?:<p>\s*)?\[iframe\s*(.*?)\](?:\s*</p>)?@", array (&$this, 'replace'), $text);
	}
}

$embediframe = new EmbedIframe;
?>
