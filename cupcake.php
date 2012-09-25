<?php
/**
 * @package Cupcake
 */
/*
Plugin Name: Cupcake
Plugin URI: http://www.artificialhead.com
Description: Plugin to handle like function
Author: Ahmad Shah
Version: 1.0
Author URI: http://artificialhead.com
 */

define('CUPCAKE_VERSION', '1.0');
define('CUPCAKE_DIR', plugin_dir_url(__FILE__));

function cupcake_init()
{
	global $wpdb;

	$like_table = $wpdb->prefix.'cupcake_like';

	//Verify table exists or not
	if (is_null($wpdb->get_var("SHOW TABLES LIKE {$like_table}")))
	{
		$create_table = "CREATE TABLE {$like_table} (
						id INT(11) NOT NULL AUTO_INCREMENT,
						post_id INT(11) NOT NULL,
						user_id INT(11) NOT NULL,
						created_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
						PRIMARY KEY(id),
						INDEX(post_id)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

		$wpdb->query($create_table);
	}

	//Register table in wp database objects
	if ( ! isset($wpdb->cupcake_like))
	{
		$wpdb->cupcake_like = $like_table;

		$wpdb->tables[] = str_replace($wpdb->prefix, '', $like_table);
	}

	wp_enqueue_script('like_post', CUPCAKE_DIR.'js/cupcake.js', array('jquery'));
	wp_localize_script('like_post', 'cup_vars', array(
		'ajaxurl' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce('cupcake_nonce'),
	));
}

function cupcake_like()
{
	global $wpdb;

	//Get current user
	$user = wp_get_current_user();
	$user_id = $user->ID;

	$nonce = $_POST['nonce'];
	if ( ! wp_verify_nonce($nonce, 'cupcake_nonce') || ! intval($_POST['pid']) || ! intval($user_id))
	{
		echo "101";
		die();
	}

	$post_id = $_POST['pid'];
	//Check if the user has already like the post
	$exists = $wpdb->get_var("SELECT id FROM {$wpdb->cupcake_like} WHERE post_id = {$post_id} AND user_id = {$user_id}");

	if ( ! is_null($exists))
	{
		echo "102";
		die();
	}
	else
	{
		$insert_like = $wpdb->query(
			$wpdb->prepare(
				"INSERT INTO {$wpdb->cupcake_like}(post_id, user_id, created_at)
				VALUES(%d, %d, NOW())",
				$post_id, $user_id
			)
		);

		$total = $wpdb->get_var("SELECT COUNT(id) AS total_like FROM {$wpdb->cupcake_like} WHERE post_id = {$post_id}");

		echo $total;
		die();
	}

}

function cupcake_like_button($pid, $class = null, $tag = false)
{
	global $wpdb;

	//Get current user
	$user = wp_get_current_user();
	$user_id = $user->ID;

	$total = $wpdb->get_var("SELECT COUNT(id) AS total_like FROM {$wpdb->cupcake_like} WHERE post_id = {$pid}");
	$likes = is_null($total) ? 0 : $total;

	if (intval($user_id))
	{
		$exists = $wpdb->get_var("SELECT id FROM {$wpdb->cupcake_like} WHERE post_id = {$pid} AND user_id = {$user_id}");

		$class_name = is_null($class) ? '' : is_array($class) ? implode(' ', $class) : $class;

		$show_tag = ($tag != false) ? "{$likes} people like this" : $likes;

		$disabled = is_null($exists) ? '' : 'disabled=true';
		$button = "<button id='{$pid}' class='cupcake-like {$class_name}' {$disabled}>{$show_tag}</button>";
	}
	else
	{
		$button = "<p class='{$class_name}'>{$likes} likes this post</p>";
	}

	echo $button;
}

add_action('init', 'cupcake_init');
add_action('wp_ajax_cupcake-like', 'cupcake_like');
add_action('cupcake_like_button', 'cupcake_like_button', 10, 3);
