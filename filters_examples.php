<?php

//Change NIF field label 
add_filter( 'woocommerce_nif_field_label', 'woocommerce_nif_field_label' );
function woocommerce_nif_field_label( $label ) {
	//Default is 'NIF / NIPC'
	return 'NIF';
}

//Change NIF field placeholder 
add_filter( 'woocommerce_nif_field_placeholder', 'woocommerce_nif_field_placeholder' );
function woocommerce_nif_field_placeholder( $placeholder ) {
	//Default is 'Portuguese VAT identification number'
	return 'VAT number';
}

//Make NIF field required 
add_filter( 'woocommerce_nif_field_required', 'woocommerce_nif_field_required' );
function woocommerce_nif_field_required( $required ) {
	//Default is false
	return true;
}

//Make NIF field wide
add_filter( 'woocommerce_nif_field_class', 'woocommerce_nif_field_class' );
function woocommerce_nif_field_class( $class ) {
	//Default is array('form-row-first')
	$class = array(
		'form-row-wide'
	);
	return $class;
}

//Make NIF field not clear
add_filter( 'woocommerce_nif_field_clear', 'woocommerce_nif_field_clear' );
function woocommerce_nif_field_clear( $clear ) {
	//Default is true
	return false;
}

//Disable autocomplete for NIF field
add_filter( 'woocommerce_nif_field_autocomplete', 'woocommerce_nif_field_autocomplete' );
function woocommerce_nif_field_autocomplete( $autocomplete ) {
	//Default is 'on'
	return 'off';
}

//Change NIF field priority
add_filter( 'woocommerce_nif_field_priority', 'woocommerce_nif_field_priority' );
function woocommerce_nif_field_priority( $priority ) {
	//Default is 120
	return 1;
}

//Change NIF field maxlength
add_filter( 'woocommerce_nif_field_maxlength', 'woocommerce_nif_field_maxlength' );
function woocommerce_nif_field_maxlength( $maxlength ) {
	//Default is 9
	return 10;
}

//Validate the NIF check digit?
add_filter( 'woocommerce_nif_field_validate', 'woocommerce_nif_field_validate' );
function woocommerce_nif_field_validate( $validate ) {
	//Default is false
	return true;
}