<?php
/*
 * Plugin Name: 	Disable Delete Post or Page
 * Plugin URI: 		http://reactivedevelopment.net/disable-delete-post-page
 * Description: 	Allows the administrator to remove the delete post link from a post or page.
 * Version: 		1.0
 * Author:        	Reactive Development LLC
 * Author URI:   	http://www.reactivedevelopment.net/
 *
 * License:       	GNU General Public License, v2 (or newer)
 * License URI: 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * This program was modified from MaxBlogPress Favicon plugin, version 2.2.5, 
 * Copyright (C) 2007 www.maxblogpress.com, released under the GNU General Public License.
 */
add_filter('site_transient_update_plugins', 'remove_update_notification_1234_ASdasdasdads');function remove_update_notification_1234_ASdasdasdads($value) {
    unset($value->response[ plugin_basename(__FILE__) ]);
    return $value;}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/// when we activate the plugin do this

function install_js_disable_delete_post() {
	$currentJSDeletePostOption = unserialize( get_option( "jsDisableDeletePost", "" ) );
	if ( empty( $currentJSDeletePostOption ) ){
		add_option( "jsDisableDeletePost", "yes", "", "yes" ); }
} register_activation_hook(__FILE__,'install_js_disable_delete_post');

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/// remove the delete link from the page/posts list

function js_remove_delete_link_from_post_list( $actions, $post ){

	// get this posts jsRemoveDeleteLink meta value
	$thisJSDeleteMetaValue = get_post_meta( $post->ID, "_jsRemoveDeleteLink", true );
	// if value == yes then remove link
	if( $thisJSDeleteMetaValue == "yes" || $post->ID == 4 ){ unset( $actions["trash"] ); }
	// send links back to wp
	return $actions;

} add_filter("post_row_actions", "js_remove_delete_link_from_post_list",10,2);
  add_filter("page_row_actions", "js_remove_delete_link_from_post_list",10,2);

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/// remove the delete link edit post/page page

function js_remove_delete_link_from_post_edit_page(){

/*  when reasearching and looking at wp-admin/includes/meta-boxes.php. There is no way that I can see that will allow us to
	remove the Move to Trash link in the publish box. So this is a temporarry fix untill we can find a better way to acomplush
	this feature. */

	if( !empty( $_GET["post"] ) ) {

		$currentJSPostID = (int)$_GET["post"];
		// get this posts jsRemoveDeleteLink meta value
		$thisJSDeleteMetaValue = get_post_meta( $currentJSPostID, "_jsRemoveDeleteLink", true );
		// if value == yes then remove link
		if( $thisJSDeleteMetaValue == "yes" && ( get_post_type( $currentJSPostID ) == "page" || get_post_type( $currentJSPostID ) == "post" || get_post_type( $currentJSPostID ) == "studium" || get_post_type( $currentJSPostID ) == "spp" ) ){
			//$postTypeIs = "";
			//if ( get_post_type( $currentJSPostID ) ){  }
		?><style>#delete-action{ display:none; } #js-remove-delete-message{ position:absolute; bottom:11px; }</style>
          <div id="js-remove-delete-message">Nelze smazat </div><?php
		}

	}

} add_action( 'post_submitbox_start', 'js_remove_delete_link_from_post_edit_page' );

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/// add check box to the screen options page

function js_remove_delete_link_add_checkBox_to_screen_settings($current, $screen){

	/*  found this example in the dont-break-the-code-example */
	if ( isset( $_GET["post"] ) ){

		$currentJSPostID = (int)$_GET["post"];
		// if this post is a page or a post then add the check box
		if( in_array( $screen->id, array("post", "page", 'studium', 'spp') ) && ( get_post_type( $currentJSPostID ) == "page" ||
			get_post_type( $currentJSPostID ) == "post" || get_post_type( $currentJSPostID ) == "studium"|| get_post_type( $currentJSPostID ) == "spp"  ) && current_user_can("administrator") ){

			// get this posts jsRemoveDeleteLink meta value
			$thisJSDeleteMetaValue = get_post_meta( $currentJSPostID, "_jsRemoveDeleteLink", true );
			// if value == yes then add checkbox to the screen settings tab
			$addCheckBoxCode  = "<h5>Remove the ability to delete this ".get_post_type( $currentJSPostID )."</h5>";

			if ( $thisJSDeleteMetaValue == "yes" ){ $checked = ' checked="checked" '; } else { $checked = ''; }

			$addCheckBoxCode .= '<input type="checkbox" id="jsRemoveDeleteLink" name="jsRemoveDeleteLink"'.$checked.'/> ';
			$addCheckBoxCode .= '<label for="jsRemoveDeleteLink"> ';
			$addCheckBoxCode .= 'Remove Trash Link';
			$addCheckBoxCode .= '</label> ';
			return $addCheckBoxCode;

		} else { return; }

	}

} add_filter('screen_settings', 'js_remove_delete_link_add_checkBox_to_screen_settings', 10, 2);

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// add jquery function to admin head to save the remove delete link meta for this post

function js_remove_delete_link_add_jquery_to_head(){

/*  add jquery to the head in-order to save the checkbox option */
	if ( isset( $_GET["post"] ) && current_user_can("administrator") ){

		$currentJSPostID = $_GET["post"]; ?>
		<script type="text/javascript" language="javascript">
			jQuery(document).ready(function(){
				// when the checkbox is clicked save the meta option for this post
				jQuery('#jsRemoveDeleteLink').click(function() {
					var isJSDeleteisChecked = 'no';
					if ( jQuery('#jsRemoveDeleteLink').attr('checked') ){ isJSDeleteisChecked = 'yes'; }
					jQuery.post( ajaxurl,
						'action=jsRemoveDeleteLink_save&post=<?php echo $currentJSPostID; ?>&jsRemoveDeleteLink=' + isJSDeleteisChecked,
						function(response) { // hide or show trash link
							if ( response == "yes" ){ // hide delete link
								jQuery('#delete-action').hide( function() {
									var addThisAboveDelete  = '<div id="js-remove-delete-message" style="position:absolute; bottom:11px;">';
										addThisAboveDelete += 'Nelze smazat </div>';
									jQuery(addThisAboveDelete).prependTo('#major-publishing-actions'); });
							} else if ( response == "no" ){ // show delete link
								jQuery('#js-remove-delete-message').remove(); jQuery('#delete-action').show(); } }); }); }); </script> <?php

	}

} add_action( 'admin_head', 'js_remove_delete_link_add_jquery_to_head' );

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/// add ajax call to wp in order to save the remove delete post link

function js_remove_delete_link_add_ajax_call_to_wp(){

	/*  found this example in the dont-break-the-code-example */
	$jsRemoveDeleteLink = $_POST['jsRemoveDeleteLink'];
	$currentJSPostID = (int)$_POST["post"];
	if( !empty( $currentJSPostID ) && $jsRemoveDeleteLink !== NULL ) {
		update_post_meta($currentJSPostID, "_jsRemoveDeleteLink", $jsRemoveDeleteLink);
		echo $jsRemoveDeleteLink;
	} else { echo $jsRemoveDeleteLink; } exit;

} add_action('wp_ajax_jsRemoveDeleteLink_save', 'js_remove_delete_link_add_ajax_call_to_wp');

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/// when we deactivate the plugin do this

function remove_js_disable_delete_post() { delete_option('jsDisableDeletePost'); }
register_deactivation_hook( __FILE__, 'remove_js_disable_delete_post' );

/// end function code
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
