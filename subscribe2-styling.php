<?php
/*
Plugin Name: Subscribe2 Styling
Description: Plugin helps to style Subscribe2 form
Version: 1.0
Author: Denis Yakimchuk
Licence: GPL3
Text Domain: subscribe2-styling

Subscribe2 Styling is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Subscribe2 Styling is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Subscribe2 Styling. If not, see {License URI}.

*/

if ( ! defined('ABSPATH') ) {
	exit();
}

class Subscribe2_Styling {
	
	private $s2s_settings;
	
	function __construct() {
		
		add_action( 'init', array( $this, 'init_plugin' ) );
		add_action( 'admin_init', array( $this, 'forced_plugin_deactivation' ) );
		add_action( 'admin_notices', array( $this, 'display_activation_error' ) );
	}
	
	function init_plugin() {
		
		define( 'S2S_TEXTDOMAIN', 'subscribe2-styling' );
		define( 'SUBSCRIBE2_IS_ACTIVE', class_exists('S2_Class') || class_exists('s2class') );
		
		if ( SUBSCRIBE2_IS_ACTIVE )
		{
			add_action( 'admin_menu', array( $this, 'add_plugin_page' ), 10 );
			
			$this->register_plugin_options();
			
			global $s2s_settings;
			$s2s_settings = $this->s2s_settings;
			
			include_once( 'includes/filters.php' );
			
		}
		
	}
	
	function forced_plugin_deactivation() {
		if ( ! SUBSCRIBE2_IS_ACTIVE )
		{
			deactivate_plugins( plugin_basename( __FILE__ ) );
			unset( $_GET['activate'] );
		}
	}
	
	function display_activation_error() {
		if ( ! SUBSCRIBE2_IS_ACTIVE && !is_plugin_active( plugin_basename( __FILE__ ) ) )
		{
		
			echo '<div id="message" class="error">'.
				wpautop( __( '"Subscribe2 Styling" requires "Subscribe2" plugin. Activate "Subscribe2" before activate "Subscribe2 Styling".', S2S_TEXTDOMAIN ) )
			.'</div>';
		
		}
	}	
	
	function register_plugin_options() {
		
		$this->s2s_settings = array(
			'form_class' 				=> array(
												'label' 		  => __( 'Form CSS Classes', S2S_TEXTDOMAIN ),
												'type' 		   	  => 'input:text',
												'filter_callback' => array($this, 'custom_sanitize_html_class'),
												'default_value'   => '',
												'description'	  => 'Enter CSS classes separated by commas.'
											   ),
			'form_subscribe_text'		=> array(
												'label' 		  => __( 'Subscribe Button Text', S2S_TEXTDOMAIN ),
												'type' 		   	  => 'input:text',
												'filter_callback' => 'esc_html',
												'default_value'   => __( 'Subscribe', 'subscribe2' ),
											   ),
			'form_unsubscribe_text'		=> array(
												'label' 		  => __( 'Unsubscribe Button Text', S2S_TEXTDOMAIN ),
												'type' 		   	  => 'input:text',
												'filter_callback' => 'esc_html',
												'default_value'   => __( 'Unsubscribe', 'subscribe2' ),
											   ),
			'form_email_field_type' 	=> array(
												'label' 		  => __( 'Email Field Type', S2S_TEXTDOMAIN ),
												'type' 		   	  => 'select',
												'filter_callback' => '',
												'default_value'   => 'text',
												'values' 		  => array( 'text' => 'Text', 'email' => 'Email' ),
											   ),
			'form_email_field_value' 	=> array(
												'label' 		  => __( 'Email field Value:', S2S_TEXTDOMAIN ),
												'type' 		   	  => 'input:text',
												'filter_callback' => 'esc_html',
												'default_value'   => 'Enter email address...',
											   ),
			'form_input_placeholder' 	=> array(
												'label' 		  => __( 'Email field Placeholder Value:', S2S_TEXTDOMAIN ),
												'type' 		   	  => 'input:text',
												'filter_callback' => 'esc_html',
												'default_value'   => '',
												'description'	  => "Recommendation: disable JS in widget or shortcode to remove 'onblur' and 'onfocus' attributes.",
											   ),
			'form_label_text'			=> array(
												'label' 		  => __( 'Label Text', S2S_TEXTDOMAIN ),
												'type' 		  	  => 'input:text',
												'filter_callback' => 'esc_html',
												'default_value'   => __( 'Your email:', 'subscribe2' ),
											   ),
			'hide_label'				=> array(
												'label' 		  => __( 'Hide Label', S2S_TEXTDOMAIN ),
												'type' 		  	  => 'input:checkbox',
												'filter_callback' => '',
												'default_value'   => 0,
											   ),
			'remove_autop'				=> array(
												'label'		   	  => __( 'Remove paragraphs and line breakes', S2S_TEXTDOMAIN ),
												'type' 		   	  => 'input:checkbox',
												'filter_callback' => '',
												'default_value'   => 0,
											   ),
			'form_email_field_wrapper'	=> array(
												'label' 		  => __( 'Email field wrapper CSS class:', S2S_TEXTDOMAIN ),
												'type' 		  	  => 'input:text',
												'filter_callback' => array($this, 'custom_sanitize_html_class'),
												'default_value'   => '',
												'description'	  => 'Fill in this field, you create a wrapper with this CSS Class only for email field input tag.<br /> Enter CSS classes separated by commas.',
											   ),
			'form_fields_wrapper_class'	=> array(
												'label' 		  => __( 'DIV wrapper CSS class:', S2S_TEXTDOMAIN ),
												'type' 		  	  => 'input:text',
												'filter_callback' => array($this, 'custom_sanitize_html_class'),
												'default_value'   => '',
												'description'	  => 'Fill in this field, you create a wrapper with this CSS Class for all form fields even for buttons.<br /> Enter CSS classes separated by commas.',
											   ),
		);
		
		foreach ( $this->s2s_settings as $option => $properties )
		{
			register_setting( 's2s_form_styling', $option,  $properties['filter_callback']);
		}
		
	}
	function custom_sanitize_html_class( $classes ) {
		
		if ( empty ( $classes ) )
		{
			
			return false;
		
		}
		
		if ( strpos( $classes, ',' ) )
		{
			
			$sanitized_classes = '';
			$class_names = explode( ',', trim( $classes ) );
			
		} else {
			
			return sanitize_html_class( $classes );
		
		}
		
		$i = 1;
		foreach ( $class_names as $class )
		{
			
			$sanitized_classes .= sanitize_html_class( $class );
			if ( $i < count( $class_names ) )
			{
				$sanitized_classes .= ',';
			}
			
			$i++;
			
		}
		
		return $sanitized_classes;
	}
	
	function add_plugin_page() {
		add_submenu_page( 's2', __('Subscribe2 Form Styling', S2S_TEXTDOMAIN), __('Form Styling', S2S_TEXTDOMAIN), 'manage_options', 's2_form_styling', array($this, 's2_form_styling_settings'));
	}
	
	function s2_form_styling_settings() {
		require_once( plugin_dir_path( __FILE__ ) . 'form-styling-page.php' );
	}

}

new Subscribe2_Styling();
