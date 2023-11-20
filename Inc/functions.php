<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * @version       1.0.0
 * @package       Formxtra_CF7
 * @license       Copyright Formxtra_CF7
 */

if ( ! function_exists( 'formxtra_cf7_option' ) ) {
	/**
	 * Get setting database option
	 *
	 * @param string $section default section name formxtra_cf7_general .
	 * @param string $key .
	 * @param string $default .
	 *
	 * @return string
	 */
	function formxtra_cf7_option( $section = 'formxtra_cf7_general', $key = '', $default = '' ) {
		$settings = get_option( $section );

		return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
	}
}

if ( ! function_exists( 'formxtra_cf7_exclude_pages' ) ) {
	/**
	 * Get exclude pages setting option data
	 *
	 * @return string|array
	 *
	 * @version 1.0.0
	 */
	function formxtra_cf7_exclude_pages() {
		return formxtra_cf7_option( 'formxtra_cf7_triggers', 'exclude_pages', array() );
	}
}

if ( ! function_exists( 'formxtra_cf7_exclude_pages_except' ) ) {
	/**
	 * Get exclude pages except setting option data
	 *
	 * @return string|array
	 *
	 * @version 1.0.0
	 */
	function formxtra_cf7_exclude_pages_except() {
		return formxtra_cf7_option( 'formxtra_cf7_triggers', 'exclude_pages_except', array() );
	}
}