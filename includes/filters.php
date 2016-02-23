<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class S2S_Filters {
	
	function __construct() {
		
		add_filter( 's2_subscribe_button', array( $this, 'filter_subscribe_button' ) );
		add_filter( 's2_unsubscribe_button', array( $this, 'filter_unsubscribe_button' ) );
		$forom_filters = array(
			'filter_remove_label',
			'filter_remove_autop',
			'filter_set_form_class',
			'filter_set_email_field_type',
			'filter_set_label_text',
			'filter_add_fields_wrapper',
			'filter_add_placeholder_attr',
			'filter_change_value_attr',
			'filter_add_email_field_wrapper',
		);
		foreach( $forom_filters as $filter ) {
			add_filter('s2_form', array( $this, $filter ) );
			add_filter('s2_ajax_form', array( $this, $filter ) );
		}
		
	}
	
	function filter_subscribe_button() {
		$button_text = get_option( 'form_subscribe_text' );
		return $button_text;
	}
	
	function filter_unsubscribe_button() {
		$button_text = get_option( 'form_unsubscribe_text' );
		return $button_text;
	}

	function filter_remove_autop( $form ) {
		if ( get_option( 'remove_autop' ) == true ) {
			$form = preg_replace("/<p[^>]*?>/", "", $form );
			$form = str_replace( array( '<br>', '<br />', '</p>'), '', $form );
		}
		return $form;
	}
	
	function filter_set_label_text( $form ) {
		$label_text = esc_html( get_option( 'form_label_text' ) );
		if ( $label_text ) {
			$form = preg_replace('/(<label[^>]*>).*?<\/label>/', '$1' . $label_text .'</label>', $form);
		}
		return $form;
	}
	
	function filter_remove_label( $form ) {
		if ( get_option( 'hide_label' ) == true ) {
			$form = preg_replace('/(<label[^>]*>).*?<\/label>/', '', $form);
		}
		return $form;
	}
	
	function filter_set_form_class( $form ) {
		$form_class = esc_attr( get_option( 'form_class' ) );
		$classes = explode( ',' , $form_class );
		if ( $form_class ) { 
			$form = str_replace( '<form ', '<form class="' . esc_attr ( implode( ' ', $classes ) ) . '" ', $form );
		}
		return $form;
	}
	
	function filter_set_email_field_type( $form ) {
		if ( get_option( 'form_email_field_type' ) == 'email' ) {
			$form = str_replace( 'type="text" name="email" ', 'type="email" name="email" ', $form );
		}
		return $form;
	}
	
	function filter_add_fields_wrapper( $form ) {
		$div_class = get_option( 'form_fields_wrapper_class' );
		$classes = explode( ',' , $div_class );
		if ( $div_class ) {
			$form = preg_replace( '/(<form[^>]+>?)/i', '$1<div class="' . esc_attr( implode( ' ', $classes ) ) . '">', $form );
			$form = str_replace( '</form>', '</div></form>' ,$form );
		}
		return $form;
	}
	
	function filter_add_email_field_wrapper( $form ) {
		$div_class = get_option( 'form_email_field_wrapper' );
		$classes = explode( ',' , $div_class );
		if ( $div_class ) {
			$form = preg_replace( '/(<input[^>]* name="email"[^>]*>?)/i', '<div class="' . esc_attr( implode( ' ', $classes ) ) . '">$0</div>', $form );
		}
		return $form;
	}

	function filter_add_placeholder_attr( $form ) {
		$placeholder_value = get_option( 'form_input_placeholder' );
		if ( !empty( $placeholder_value ) ) {
			$form = str_replace( 'id="s2email"', 'id="s2email" placeholder="' . $placeholder_value . '"', $form );
		}
		return $form;
	}
	
	function filter_change_value_attr( $form ) {
		$value = get_option( 'form_email_field_value' );
		if ( $value ) {
			$form = preg_replace( '/<input(.*)onblur="([^"]*)"(.*)>/','<input$1onblur="if (this.value == \'\') {this.value = \'' . $value . '\';}"$3>', $form );
			$form = preg_replace( '/<input(.*)onfocus="([^"]*)"(.*)>/','<input$1onfocus="if (this.value == \'' . $value . '\') {this.value = \'\';}"$3>', $form );
			$form = preg_replace( '/<input(.*)id="s2email" value="([^"]*)"(.*)>/','<input$1id="s2email" value="' . $value . '"$3>', $form );
		}
		return $form;
	}
	
}

new S2S_Filters();