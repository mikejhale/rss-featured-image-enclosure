<?php

/**
 * RSS Featured Image Enclosure class file.
 *
 * @package RSS_Featured_Image_Enclosure
 */

/*
Plugin Name: RSS Featured Image Enclosure
Plugin URI: https://blackdoctor.org/plugins/rss-featured-image-enclosure
Description: Automatically add page breaks.
Version: 1.0.
Author: Mike Hale
Author URI: https://mikehale.me
License: GPL-2.0+
Text Domain: rss-featured-image-enclosure
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class RSS_Featured_Image_Enclosure
 *
 * Adds featured image enclosure tag ro RSS feed.
 */
class RSS_Featured_Image_Enclosure {

	/**
	 * RSS_Featured_Image_Enclosure class constructor.
	 */
	public function __construct() {
		add_filter( 'rss2_item', array( $this, 'add_image_enclosure') );
	}

	/**
	 * Add enclosure to RSS feed.
	 * Handles `rss2_item`
	 *
	 * @return void
	 */
	public function add_image_enclosure() {

		global $post;

		$args = array(
			'order'          => 'ASC',
			'post_type'      => 'attachment',
			'post_parent'    => $post->ID,
			'post_mime_type' => 'image',
			'post_status'    => null,
			'numberposts'    => 1,
		);

		// use featured image, fallback to first image in post.
		$thumbnail_id = get_post_thumbnail_id( $post );
		if ( $thumbnail_id ) {
			$image = wp_get_attachment_image_src( $thumbnail_id, 'full' );
			$mime  = get_post_mime_type( $thumbnail_id );
		} else {
			$attachments = get_posts( $args );
			if ( count( $attachments ) > 0 ) {
				$image = wp_get_attachment_image_src( $attachments[0]->ID, 'full' );
				$mime  = get_post_mime_type( $attachments[0]->ID );
			}
		}

		if ( $image ) {
			printf(
				'<enclosure url="%s" length="%s" type="%s"/>',
				esc_attr( $image[0] ),
				esc_attr( filesize( get_attached_file( $thumbnail_id ) ) ),
				esc_attr( $mime )
			);
		}
	}
}

new RSS_Featured_Image_Enclosure();
