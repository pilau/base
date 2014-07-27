<?php

/**
 * Admin interface customization
 *
 * @package	Pilau_Base
 * @since	0.1
 * @todo	Selectively remove post category / link category options on options-writing.php
 */


/**
 * Admin scripts and styles
 *
 * @since	Pilau_Base 0.1
 */
add_action( 'admin_enqueue_scripts', 'pilau_base_admin_enqueue_scripts_styles', 1 );
function pilau_base_admin_enqueue_scripts_styles() {

	wp_enqueue_style( 'pilau-base-admin-css', get_template_directory_uri() . '/styles/wp-admin.css', array(), '1.0' );
	wp_enqueue_script( 'pilau-base-admin-js', get_template_directory_uri() . '/js/wp-admin.js', array(), '1.0' );

}


/**
 * Admin notices
 *
 * @since	Pilau_Base 0.1
 */
add_action( 'admin_notices', 'pilau_base_admin_notices', 1 );
function pilau_base_admin_notices() {
	global $pilau_base_options;

	// Theme activation
	if ( ! $pilau_base_options['settings_script_run'] ) {

	}

}


/**
 * Admin menus
 *
 * @since	Pilau_Base 0.1
 */
add_action( 'admin_menu', 'pilau_base_admin_menus', 1 );
function pilau_base_admin_menus() {

	/* Register new menus
	***************************************************************************/

	// Theme plugins
	//if ( PILAU_USE_PLUGINS_PAGE )
	//	add_plugins_page( __( 'Pilau plugins' ), __( 'Pilau plugins' ), 'update_core', 'pilau-plugins', 'pilau_plugins_page' );

	// Theme settings script
	//if ( PILAU_USE_SETTINGS_SCRIPT )
	//	add_options_page( __( 'Pilau settings initialization and reset script' ), __( 'Pilau settings script' ), 'update_core', 'pilau-settings-script', 'pilau_settings_script_page' );

}


/**
 * Theme plugins page
 *
 * @since	Pilau_Base 0.1
 */
function pilau_plugins_page() {
	global $pilau_plugins_infos;

	// Output
	?>

	<div class="wrap">

		<div id="icon-plugins" class="icon32"><br></div>
		<h2><?php _e( 'Pilau plugin management' ); ?></h2>

		<form method="post" action="">

			<?php wp_nonce_field( 'pilau-plugins', 'pilau_plugins_nonce' ); ?>

			<p style="text-align:right;"><input class="button-primary" type="submit" name="submit" value="<?php _e( 'Update' ); ?>"></p>

			<table class="wp-list-table widefat plugins pilau-plugins">
				<?php foreach ( array( 'head', 'foot' ) as $row ) { ?>
					<t<?php echo $row; ?>>
						<tr>
							<th scope="col" class="manage-column column-name"><?php _e( 'Plugin' ); ?></th>
							<th scope="col" class="manage-column column-description"><?php _e( 'Description' ); ?></th>
							<th scope="col" class="manage-column column-cb check-column"><?php _e( 'Installed?' ); ?>&nbsp;&nbsp;</th>
							<th scope="col" class="manage-column column-cb check-column"><?php _e( 'Activated?' ); ?></th>
						</tr>
					</t<?php echo $row; ?>>
				<?php } ?>
				<tbody id="the-list">
					<?php

					// Loop for each record
					if ( ! empty( $pilau_plugins_infos ) ) {

						foreach ( $pilau_plugins_infos as $plugin_key => $plugin_data ) {

							$plugin_installed = pilau_is_plugin_installed( $plugin_data['path'] );
							$plugin_activated = is_plugin_active( $plugin_data['path'] );
							$row_class = 'inactive';
							if ( ! $plugin_installed ) {
								$row_class = 'not-installed';
							} else if ( $plugin_activated ) {
								$row_class = 'active';
							}

							?>

							<tr class="<?php echo $row_class; ?>">
								<td class="plugin-title">
									<strong><?php echo $plugin_data["title"];  ?></strong>
								</td>
								<td class="column-description desc">
									<div class="plugin-description"><p><?php echo $plugin_data["description"]; ?></p></div>
									<p class="plugin-meta"><?php _e( 'Status' ); ?>: <?php echo $plugin_data["status"]; ?></p>
								</td>
								<td class="check-column" style="text-align:center;">
									<?php
									if ( $plugin_installed ) {
										// Already installed
										echo '<img src="/wp-admin/images/yes.png" alt="' . __( 'Already installed' ) . '">';
									} else {
										// Not installed, checkbox default determined by status
										echo '<input type="checkbox" name="installed_' . $plugin_key . '" id="installed_' . $plugin_key . '"';
										if ( $plugin_data['status'] == 'canonical' || $plugin_data['status'] == 'recommended' )
											echo ' checked="checked"';
										echo ' />';
									}
									?>
								</td>
								<td class="check-column" style="text-align:center;">
									<?php
									if ( $plugin_installed && $plugin_activated ) {
										// Already activated
										echo '<img src="/wp-admin/images/yes.png" alt="' . __( 'Already activated' ) . '">';
									} else {
										// Not activated, checkbox default determined by status
										echo '<input type="checkbox" name="activated_' . $plugin_key . '" id="activated_' . $plugin_key . '"';
										if ( $plugin_data['status'] == 'canonical' )
											echo ' checked="checked"';
										echo ' />';
									}
									?>
								</td>
							</tr>

							<?php

						}

					} else {

						// No plugins
						?>
						<tr class="no-items"><td class="colspanchange" colspan="4"><?php _e( 'No theme plugins data supplied.' ); ?></td></tr>
						<?php

					}

					?>

				</tbody>

			</table>

			<p style="text-align:right;"><input class="button-primary" type="submit" name="submit" value="<?php _e( 'Update' ); ?>"></p>

		</form>

	</div>

	<?php

}

/**
 * Process plugins page submissions
 *
 * @since	Pilau_Base 0.1
 * @todo	Installation and activation!
 */
add_action( 'admin_init', 'pilau_plugins_page_process' );
function pilau_plugins_page_process() {
	global $pilau_plugins_infos;

	// Submitted?
	if ( isset( $_POST['pilau_plugins_nonce'] ) && check_admin_referer( 'pilau-plugins', 'pilau_plugins_nonce' ) ) {

		// Loop through post data
		foreach ( $_POST as $field => $value ) {

			// Installed / activated field?
			if ( strlen( $field ) > 10 && strpos( $field, '_' ) ) {
				$field_parts = explode( '_', $field );

				if ( $field_parts[0] == 'installed' ) {
					// Need to install plugin????

				} else if ( $field_parts[0] == 'activated' ) {
					// Need to activate plugin????

				}

			}

		}

		// Redirect
		wp_redirect( admin_url( 'plugins.php?page=pilau-plugins&done=1' ) );

	}

}


/**
 * Settings initialization / reset page
 *
 * @since	Pilau_Base 0.1
 */
function pilau_settings_script_page() {

	// Output
	?>

	<div class="wrap">

		<div id="icon-options-general" class="icon32"><br></div>
		<h2><?php _e( 'Pilau settings initialization and reset script' ); ?></h2>

		<div class="error">
			<p><?php _e( 'Running this script will initialize / reset core and / or plugin settings to Pilau defaults. Use with care, and please backup your database before doing a reset!' ); ?></p>
		</div>

		<form method="post" action="">

			<?php wp_nonce_field( 'pilau-settings-script', 'pilau_settings_script_nonce' ); ?>

			<p><input class="button-primary" type="submit" name="submit" value="<?php _e( 'Run script' ); ?>"></p>

			<table class="wp-list-table widefat plugins pilau-settings-script">
				<?php foreach ( array( 'head', 'foot' ) as $row ) { ?>
				<t<?php echo $row; ?>>
					<tr>
						<th scope="col" class="manage-column column-cb check-column"><input type="checkbox"></th>
						<th scope="col" class="manage-column column-name"><?php _e( 'Settings' ); ?></th>
					</tr>
				</t<?php echo $row; ?>>
				<?php } ?>
				<tbody id="the-list">
					<tr>
						<th scope="row" class="check-column"><input type="checkbox" name="checked[]" value="core" id="checkbox_core"><label class="screen-reader-text" for="checkbox_core"><?php _e( 'Select core settings' ); ?></label></th>
						<td class="column-description desc"><?php _e( 'WordPress core settings' ); ?></td>
					</tr>
					<tr>
						<th scope="row" class="check-column"><input type="checkbox" name="checked[]" value="home-news" id="checkbox_home-news"><label class="screen-reader-text" for="checkbox_home-news"><?php _e( 'Select home + news page creation' ); ?></label></th>
						<td class="column-description desc"><?php _e( 'Create Home + News page' ); ?></td>
					</tr>
				</tbody>

			</table>

			<p><input class="button-primary" type="submit" name="submit" value="<?php _e( 'Run script' ); ?>"></p>

		</form>

	</div>

	<?php

}

/**
 * Process settings script page submissions
 *
 * @since	Pilau_Base 0.1
 * @todo	Store settings in XML config file?
 */
add_action( 'admin_init', 'pilau_settings_script_page_process' );
function pilau_settings_script_page_process() {
	global $pilau_wp_plugins;

	// Submitted?
	if ( isset( $_POST['pilau_settings_script_nonce'] ) && check_admin_referer( 'pilau-settings-script', 'pilau_settings_script_nonce' ) ) {
		$checked = array_values( $_POST['checked'] );

		if ( in_array( 'core', $checked ) ) {

			// Core settings
			update_option( 'date_format', 'F jS Y' );
			update_option( 'default_post_edit_rows', '30' );

		}


		// Redirect
		wp_redirect( admin_url( 'options-general.php?page=pilau-settings-script&done=1' ) );

	}

}


/**
 * Remove meta boxes
 *
 * @since	Pilau_Base 0.1
 */
add_action( 'add_meta_boxes', 'pilau_base_remove_meta_boxes', 1 );
function pilau_base_remove_meta_boxes() {

	/* Comments */
	if ( ! PILAU_USE_COMMENTS ) {
		remove_meta_box( 'commentsdiv', 'post', 'normal' );
		remove_meta_box( 'commentsdiv', 'page', 'normal' );
		remove_meta_box( 'commentstatusdiv', 'post', 'normal' );
		remove_meta_box( 'commentstatusdiv', 'page', 'normal' );
	}

}


/**
 * Customize list columns
 *
 * For the most part these should be handled by the Codepress Admin Columns plugin.
 * Include any necessary overrides here.
 *
 * @since	Pilau_Base 0.1
 */
add_action( 'admin_init', 'pilau_base_customize_list_columns' );
function pilau_base_customize_list_columns() {
	add_filter( 'manage_edit-post_columns', 'pilau_base_admin_columns', 10000, 1 );
	add_filter( 'manage_edit-page_columns', 'pilau_base_admin_columns', 10000, 1 );
	foreach ( get_post_types( array( 'public' => true ), 'names' ) as $pt ) {
		add_filter( 'manage_' . $pt . '_posts_columns', 'pilau_admin_columns', 10000, 1 );
	}
}

/**
 * Global handler for all post type columns
 *
 * @since	Pilau_Base 0.1
 *
 * @param	array $cols
 * @return	array
 */
function pilau_base_admin_columns( $cols ) {

	// Override core stuff
	if ( ! PILAU_USE_CATEGORIES && isset( $cols['categories'] ) )
		unset( $cols['categories'] );
	if ( ! PILAU_USE_TAGS && isset( $cols['tags'] ) )
		unset( $cols['tags'] );
	if ( ! PILAU_USE_COMMENTS && isset( $cols['comments'] ) )
		unset( $cols['comments'] );

	return $cols;
}


/**
 * Disable default dashboard widgets
 *
 * @since	Pilau_Base 0.1
 * @link	http://codex.wordpress.org/Dashboard_Widgets_API
 */
add_action( 'wp_dashboard_setup', 'pilau_base_disable_default_dashboard_widgets', 1 );
function pilau_base_disable_default_dashboard_widgets() {

	if ( ! PILAU_USE_COMMENTS )
		remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );

}


/* Functions to help building admin screens
-------------------------------------------------------------------------------------------*/

/**
 * Output admin tabs
 *
 * Gets the current tab from $_GET['tab']; defaults to the first tab supplied
 *
 * @since	Pilau_Base 0.1
 * @param	array	$tabs		In the format:
 * 								<code>array( 'slug1' => 'Title 1', 'slug2' => 'Title 2' )</code>
 * @param	string	$base_url	Base URL for admin screen
 */
function pilau_admin_tabs( $tabs, $base_url ) {
	echo '<h2 class="nav-tab-wrapper">';
	foreach ( $tabs as $slug => $title ) {
		$classes = array( 'nav-tab' );
		if ( $slug == pilau_current_admin_tab( $tabs ) )
			$classes[] = ' nav-tab-active';
		echo '<a class="' . implode( " ", $classes ) . '" href="' . $base_url . '&amp;tab=' . $slug . '">' . $title . '</a>';
	}
	echo '</h2>';
}

/**
 * Get the current tab in an admin screen
 *
 * @since	Pilau_Base 0.1
 * @param	array	$tabs	In the format:
 * 							<code>array( 'slug1' => 'Title 1', 'slug2' => 'Title 2' )</code>
 * @return	string
 */
function pilau_current_admin_tab( $tabs ) {
	return isset( $_GET['tab'] ) && $_GET['tab'] ? $_GET['tab'] : key( $tabs );
}