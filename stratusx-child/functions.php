<?php

add_action( 'wp_enqueue_scripts', 'enqueue_child_theme_style', 9999 );
function enqueue_child_theme_style() {
	wp_enqueue_style( 'dtbwp_css_child', get_stylesheet_directory_uri() . '/style.css', array(
		'dtbwp_style',
	), 1.0 );
}

add_action( 'wp_enqueue_scripts', 'enqueue_okomo_script');
function enqueue_okomo_script() {
	wp_enqueue_script('okomo_script', get_stylesheet_directory_uri() . '/js/okomo-script.js');
}

