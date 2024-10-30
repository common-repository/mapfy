<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * @version       1.0.0
 * @package       Mapfy
 * @license       Copyright Mapfy
 */

if ( ! function_exists( 'mapfy_option' ) ) {
	/**
	 * Get setting database option
	 *
	 * @param string $section default section name mapfy_general .
	 * @param string $key .
	 * @param string $default .
	 *
	 * @return string
	 */
	function mapfy_option( $section = 'mapfy_general', $key = '', $default = '' ) {
		$settings = get_option( $section );

		return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
	}
}

if ( ! function_exists( 'mapfy_exclude_pages' ) ) {
	/**
	 * Get exclude pages setting option data
	 *
	 * @return string|array
	 *
	 * @version 1.0.0
	 */
	function mapfy_exclude_pages() {
		return mapfy_option( 'mapfy_triggers', 'exclude_pages', array() );
	}
}

if ( ! function_exists( 'mapfy_exclude_pages_except' ) ) {
	/**
	 * Get exclude pages except setting option data
	 *
	 * @return string|array
	 *
	 * @version 1.0.0
	 */
	function mapfy_exclude_pages_except() {
		return mapfy_option( 'mapfy_triggers', 'exclude_pages_except', array() );
	}
}