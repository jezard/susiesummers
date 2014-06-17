<?php

if ( ! function_exists( 'portfolio_tinymce_plugin' ) ) :
	/**
	 * Hooks up our editor plugin, which adds some custom buttons to the editor
	 */
	function portfolio_tinymce_plugin() {
		// Don't bother loading buttons if the current user lacks permissions
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) )
			return;

		// Add only in Rich Editor mode
		if ( get_user_option( 'rich_editing' ) == 'true' ) {
			add_filter( 'mce_external_plugins', 'portfolio_add_tinymce_plugin' );
			add_filter( 'mce_buttons_3', 'portfolio_register_tinymce_buttons' );
		}
	}

endif; // portfolio_tinymce_plugin

if ( ! function_exists( 'portfolio_register_tinymce_buttons' ) ) :

	function portfolio_register_tinymce_buttons( $buttons ) {
		array_push(
			$buttons,
			"type_intro", "title_underline", "pullquote", "statement",
			"alert", "error", "success", "note"
		);
		return $buttons;
	}

endif; // portfolio_register_tinymce_buttons

if ( ! function_exists( 'portfolio_add_tinymce_plugin' ) ) :

	function portfolio_add_tinymce_plugin( $plugin_array ) {
		$plugin_array['portfolio'] = get_template_directory_uri() . '/includes/tinymce/plugin/editor_plugin.js?v=' . PORTFOLIO_VERSION;
		return $plugin_array;
	}

endif; // portfolio_add_tinymce_plugin