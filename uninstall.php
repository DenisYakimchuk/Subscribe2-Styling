<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
} else {
 
	s2s_uninstall();
	
}
function s2s_uninstall() {
	
	global $s2s_settings;
	
	foreach( $s2s_settings as $option => $properties ) {
		delete_option( $option );
	}
	
} // end s2s_uninstall()