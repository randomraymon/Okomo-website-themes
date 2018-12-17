<?php
/**
 * Suki Admin page basic functions
 *
 * @package Suki
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) exit;

class Suki_Admin {
	/**
	 * Singleton instance
	 *
	 * @var Suki_Admin
	 */
	private static $instance;

	/**
	 * Parent menu slug of all theme pages
	 *
	 * @var string
	 */
	private $_menu_id = 'suki';

	/**
	 * ====================================================
	 * Singleton & constructor functions
	 * ====================================================
	 */

	/**
	 * Get singleton instance.
	 *
	 * @return Suki_Admin
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Class constructor
	 */
	protected function __construct() {
		// General admin hooks on every admin pages
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ), 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_javascripts' ) );
		add_action( 'admin_notices', array( $this, 'add_theme_notice' ), 99 );

		// Classic editor hooks
		add_action( 'admin_init', array( $this, 'add_editor_css' ) );
		add_action( 'tiny_mce_before_init', array( $this, 'modify_tiny_mce_config' ) );

		// Suki admin page hooks
		add_action( 'suki/admin/dashboard/content', array( $this, 'render_content__welcome_panel' ), 1 );
		add_action( 'suki/admin/dashboard/content', array( $this, 'render_content__pro_modules_table' ), 20 );
		add_action( 'suki/admin/dashboard/sidebar', array( $this, 'render_sidebar__customizer' ), 10 );
		// add_action( 'suki/admin/dashboard/sidebar', array( $this, 'render_sidebar__pro' ), 20 );
		add_action( 'suki/admin/dashboard/sidebar', array( $this, 'render_sidebar__documentation' ), 30 );
		add_action( 'suki/admin/dashboard/sidebar', array( $this, 'render_sidebar__community' ), 40 );
		add_action( 'suki/admin/dashboard/sidebar', array( $this, 'render_sidebar__feedback' ), 50 );
		
		$this->_includes();
	}

	/**
	 * Include additional files.
	 */
	private function _includes() {
		require_once( SUKI_INCLUDES_DIR . '/admin/class-suki-admin-fields.php' );

		// Only include metabox on post add/edit page and term add/edit page.
		global $pagenow;
		if ( in_array( $pagenow, array( 'post.php', 'post-new.php', 'edit-tags.php', 'term.php' ) ) ) {
			require_once( SUKI_INCLUDES_DIR . '/admin/class-suki-admin-metabox-page-settings.php' );
		}
	}

	/**
	 * ====================================================
	 * Hook functions
	 * ====================================================
	 */

	/**
	 * Add admin submenu page: Appearance > Suki.
	 */
	public function register_admin_menu() {
		add_theme_page(
			suki_get_theme_info( 'name' ),
			suki_get_theme_info( 'name' ),
			'edit_theme_options',
			'suki',
			array( $this, 'render_admin_page' )
		);

		/**
		 * Hook: suki/admin/menu
		 */
		do_action( 'suki/admin/menu' );
	}

	/**
	 * Add admin notice to import data like screenshot.
	 */
	public function add_theme_notice() {
		// Abort if we are on Child Theme.
		if ( is_child_theme() ) {
			return;
		}

		global $hook_suffix;

		if ( 'themes.php' == $hook_suffix ) : ?>
			<div class="suki-admin-theme-notice notice notice-info">
				<div class="suki-admin-theme-notice-arrow"></div>
				<p>
					<?php printf(
						/* translators: link to the documentation article. */
						esc_html__( 'Fell in love with our screenshot? Learn how to replicate the same design %s.', 'suki' ),
						'<a href="' . esc_url( 'https://docs.sukiwp.com/article/getting-started/replicating-theme-screenshot/' ) . '" target="_blank" rel="noopener">' . esc_html__( 'here', 'suki' ) . '</a>'
					); ?>
				</p>
			</div>
		<?php endif;
	}

	/**
	 * Enqueue admin styles.
	 *
	 * @param string $hook
	 */
	public function enqueue_admin_styles( $hook ) {
		/**
		 * Hook: Styles to be included before admin CSS
		 */
		do_action( 'suki/admin/before_enqueue_admin_css', $hook );

		// Enqueue CSS files
		wp_enqueue_style( 'suki-admin', SUKI_CSS_URL . '/admin/admin.css', array(), SUKI_VERSION );
		wp_style_add_data( 'suki-admin', 'rtl', 'replace' );

		/**
		 * Hook: Styles to be included after admin CSS
		 */
		do_action( 'suki/admin/after_enqueue_admin_css', $hook );
	}

	/**
	 * Enqueue admin javascripts.
	 *
	 * @param string $hook
	 */
	public function enqueue_admin_javascripts( $hook ) {
		// Fetched version from package.json
		$ver = array();
		$ver['wp-color-picker-alpha'] = '2.1.3';

		/**
		 * Hook: Styles to be included before admin JS
		 */
		do_action( 'suki/admin/before_enqueue_admin_js', $hook );

		// Register JS files
		wp_register_script( 'wp-color-picker-alpha', SUKI_JS_URL . '/vendors/wp-color-picker-alpha' . SUKI_ASSETS_SUFFIX . '.js', array( 'wp-color-picker' ), $ver['wp-color-picker-alpha'], true );

		// Enqueue JS files.
		wp_enqueue_script( 'suki-admin', SUKI_JS_URL . '/admin/admin' . SUKI_ASSETS_SUFFIX . '.js', array( 'jquery' ), SUKI_VERSION, true );

		/**
		 * Hook: Styles to be included after admin JS
		 */
		do_action( 'suki/admin/after_enqueue_admin_js', $hook );
	}

	/**
	 * Add CSS for editor page.
	 */
	public function add_editor_css() {
		add_editor_style( SUKI_CSS_URL . '/admin/editor' . SUKI_ASSETS_SUFFIX . '.css' );
		add_editor_style( Suki_Customizer::instance()->generate_active_google_fonts_embed_url() );
	}

	/**
	 * Modify TinyMCE configurations.
	 * - Add dymanic CSS for editor page.
	 * - Add content layout body class.
	 *
	 * @param array $mceinit
	 * @return array
	 */
	public function modify_tiny_mce_config( $mceinit ) {
		global $post;

		if ( empty( $post ) ) return;

		/**
		 * Add dynamic CSS.
		 */

		$css_array = array(
			'global' => array(),
		);

		// Typography
		$active_google_fonts = array();
		$typography_types = array( 'body', 'blockquote', 'h1', 'h2', 'h3', 'h4' );
		$fonts = suki_get_all_fonts();

		foreach ( $typography_types as $type ) {
			$selected_font_family = suki_get_theme_mod( $type . '_font_family' );

			if ( '' === $selected_font_family || 'inherit' === $selected_font_family ) {
				$select_font_stack = $selected_font_family;
			} else {
				$chunks = explode( '|', $selected_font_family );
				if ( 2 === count( $chunks ) ) {
					$select_font_stack = suki_array_value( $fonts[ $chunks[0] ], $chunks[1], $chunks[1] );
				}
			}

			$css_array['global'][ $type ]['font-family'] = $select_font_stack;
			$css_array['global'][ $type ]['font-weight'] = suki_get_theme_mod( $type . '_font_weight' );
			$css_array['global'][ $type ]['font-style'] = suki_get_theme_mod( $type . '_font_style' );
			$css_array['global'][ $type ]['text-transform'] = suki_get_theme_mod( $type . '_text_transform' );
			$css_array['global'][ $type ]['font-size'] = suki_get_theme_mod( $type . '_font_size' );
			$css_array['global'][ $type ]['line-height'] = suki_get_theme_mod( $type . '_line_height' );
			$css_array['global'][ $type ]['letter-spacing'] = suki_get_theme_mod( $type . '_letter_spacing' );
		}

		// Content wrapper width for content layout with sidebar
		$css_array['global']['body.suki-editor-left-sidebar']['width'] =
		$css_array['global']['body.suki-editor-right-sidebar']['width'] = 'calc(' . suki_get_content_width_by_layout() . 'px + 2rem)';

		// Content wrapper width for narrow content layout
		$css_array['global']['body.suki-editor-narrow']['width'] = 'calc(' . suki_get_content_width_by_layout( 'narrow' ) . 'px + 2rem)';

		// Content wrapper width for full content layout
		$css_array['global']['body.suki-editor-wide']['width'] = 'calc(' . suki_get_content_width_by_layout( 'wide' ) . 'px + 2rem)';

		// Build CSS string.
		// $styles = str_replace( '"', '\"', suki_convert_css_array_to_string( $css_array ) );
		$styles = wp_slash( suki_convert_css_array_to_string( $css_array ) );

		// Merge with existing styles or add new styles.
		if ( ! isset( $mceinit['content_style'] ) ) {
			$mceinit['content_style'] = $styles . ' ';
		} else {
			$mceinit['content_style'] .= ' ' . $styles . ' ';
		}

		/**
		 * Add body class.
		 */

		$class = 'suki-editor-' . suki_get_page_setting_by_post_id( 'content_layout', $post->ID );

		// Merge with existing classes or add new class.
		if ( ! isset( $mceinit['body_class'] ) ) {
			$mceinit['body_class'] = $class . ' ';
		} else {
			$mceinit['body_class'] .= ' ' . $class . ' ';
		}

		return $mceinit;
	}

	/**
	 * ====================================================
	 * Render functions
	 * ====================================================
	 */

	/**
	 * Render admin page.
	 */
	public function render_admin_page() {
		?>
		<div class="wrap suki-admin-wrap">
			<div class="suki-admin-header">
				<div class="suki-admin-wrapper wp-clearfix">
					<div class="suki-admin-logo">
						<img src="<?php echo esc_url( SUKI_IMAGES_URL . '/suki-logo.svg' ); ?>" height="30" alt="<?php echo esc_attr( get_admin_page_title() ); ?>">
						<span class="suki-admin-version"><?php echo suki_get_theme_info( 'version' ); // WPCS: XSS OK ?></span>
					</div>
				</div>
			</div>

			<div class="suki-admin-notices">
				<div class="suki-admin-wrapper">
					<h1 style="display: none;"></h1>

					<?php settings_errors(); ?>
				</div>
			</div>

			<div class="suki-admin-content metabox-holder">
				<div class="suki-admin-wrapper">
					<div class="suki-admin-content-row">
						<div class="suki-admin-primary">
							<?php
							/**
							 * Hook: suki/admin/dashboard/content
							 *
							 * @hooked Suki_Admin::render_content__welcome_panel - 1
							 * @hooked Suki_Admin::render_content__pro_modules_table - 20
							 */
							do_action( 'suki/admin/dashboard/content' );
							?>
						</div>
						<div class="suki-admin-secondary">
							<?php
							/**
							 * Hook: suki/admin/dashboard/sidebar
							 *
							 * @hooked Suki_Admin::render_sidebar__customizer - 10
							 * @hooked Suki_Admin::render_sidebar__pro - 20
							 * @hooked Suki_Admin::render_sidebar__documentation - 30
							 * @hooked Suki_Admin::render_sidebar__community - 40
							 * @hooked Suki_Admin::render_sidebar__feedback - 50
							 */
							do_action( 'suki/admin/dashboard/sidebar' );
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render welcome panel on Suki admin page content.
	 */
	public function render_content__welcome_panel() {
		?>
		<div class="suki-admin-welcome-panel welcome-panel">
			<div class="welcome-panel-content">
				<h2>
					<?php printf(
						/* translators: %s: theme name. */
						esc_html__( 'Welcome to %s!', 'suki' ),
						esc_html( suki_get_theme_info( 'name' ) )
					); ?>
				</h2>
				<p class="about-description">
					<?php printf(
						/* translators: %1$s: theme name; %2$s: link to theme URL. */
						esc_html__( 'Your website is now in good hands! %1$s offers highly customizable design, and lightning fast performance. Learn more about its full features and premium modules at %2$s.', 'suki' ),
						esc_html( suki_get_theme_info( 'name' ) ),
						'<a href="' . esc_url( suki_get_theme_info( 'url' ) ) . '" target="_blank" rel="noopener">' . esc_html__( 'our website', 'suki' ) . '</a>'
					); ?>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Render pro modules table on Suki admin page content.
	 */
	public function render_content__pro_modules_table() {
		?>
		<div class="suki-admin-pro-modules postbox">
			<h2 class="hndle">
				<?php echo wp_kses_post( apply_filters( 'suki/pro/modules/list_heading', esc_html__( 'More features are available on Suki Pro', 'suki' ) ) ); ?>
			</h2>
			<div class="inside">
				<?php
				// Get all pro modules list.
				$modules = suki_get_pro_modules();
				?>
				<table class="suki-admin-pro-table widefat plugins">
					<tbody>
						<?php foreach( $modules as $module_slug => $module_data ) : ?>
							<tr class="suki-admin-pro-table-item <?php echo esc_attr( suki_is_pro() && suki_array_value( $module_data, 'active' ) ? 'active' : 'inactive' ); ?>">
								<th class="check-column"></th>
								<td class="suki-admin-pro-table-item-name plugin-title column-primary">
									<span><?php echo suki_array_value( $module_data, 'label' ); // WPCS: XSS OK ?></span>
								</td>
								<td class="suki-admin-pro-table-item-actions column-description desc">
									<?php if ( ! suki_is_pro() ) : ?>

										<a href="<?php echo esc_url( suki_array_value( $module_data, 'url' ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Learn more', 'suki' ); ?></a>

									<?php elseif ( 0 < count( suki_array_value( $module_data, 'actions' ) ) ) : ?>

										<?php foreach( suki_array_value( $module_data, 'actions' ) as $action_key => $action_data ) : ?>
											<a href="<?php echo esc_url( suki_array_value( $action_data, 'url' ) ); ?>"><?php echo suki_array_value( $action_data, 'label' ); // WPCS: XSS OK ?></a>
										<?php endforeach; ?>

									<?php else : ?>

										<span class="suki-admin-pro-table-item-coming-soon"><?php esc_html_e( 'Coming soon', 'suki' ); ?></span>

									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
		<?php
	}

	/**
	 * Render "Go to Customizer" button on Suki admin page sidebar.
	 */
	public function render_sidebar__customizer() {
		?>
		<div class="suki-admin-secondary-customize">
			<a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" class="suki-admin-customize-button button button-hero button-primary">
				<?php esc_html_e( 'Go to Customizer', 'suki' ); ?>
			</a>
		</div>
		<?php
	}

	/**
	 * Render "Suki Pro" info box on Suki admin page sidebar.
	 */
	public function render_sidebar__pro() {
		if ( suki_is_pro() ) return;
		?>
		<div class="suki-admin-secondary-pro postbox">
			<h2 class="hndle"><?php esc_html_e( 'Suki Pro', 'suki' ); ?>&nbsp;&nbsp;<span class="suki-admin-pro-coming-soon"><?php esc_html_e( 'coming soon', 'suki' ); ?></span></h2>
			<div class="inside">
				<p><?php esc_html_e( 'Make your site even better with our premium modules, available in a very affordable price.', 'suki' ); ?></p>
				<p>
					<a href="<?php echo SUKI_PRO_URL; // WPCS: XSS OK ?>" class="button button-large button-secondary" target="_blank" rel="noopener">
						<span class="dashicons dashicons-unlock"></span>
						<?php echo esc_html_x( 'More about Suki Pro', 'Suki Pro upsell', 'suki' ); ?>
					</a>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Render "Documentation" info box on Suki admin page sidebar.
	 */
	public function render_sidebar__documentation() {
		?>
		<div class="suki-admin-secondary-docs postbox">
			<h2 class="hndle"><?php esc_html_e( 'Documentation', 'suki' ); ?></h2>
			<div class="inside">
				<p><?php esc_html_e( 'Not sure how something works? Our documentation might help you figure out the solution.', 'suki' ); ?></p>
				<p>
					<a href="https://docs.sukiwp.com/" class="button button-secondary" target="_blank" rel="noopener">
						<span class="dashicons dashicons-lightbulb"></span>
						<?php esc_html_e( 'Go to our Documentation', 'suki' ); ?>
					</a>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Render "Community" info box on Suki admin page sidebar.
	 */
	public function render_sidebar__community() {
		?>
		<div class="suki-admin-secondary-fb-group postbox">
			<h2 class="hndle"><?php esc_html_e( 'Community', 'suki' ); ?></h2>
			<div class="inside">
				<p><?php esc_html_e( 'Join our Facebook group for latest updates info and discussions with other Suki users.', 'suki' ); ?></p>
				<p>
					<a href="https://facebook.com/groups/sukiwp/" class="button button-secondary" target="_blank" rel="noopener">
						<span class="dashicons dashicons-facebook"></span>
						<?php esc_html_e( 'Join our Facebook Group', 'suki' ); ?>
					</a>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Render "Feedback" info box on Suki admin page sidebar.
	 */
	public function render_sidebar__feedback() {
		?>
		<div class="suki-admin-secondary-rate postbox">
			<h2 class="hndle"><?php esc_html_e( 'Enjoy Suki?', 'suki' ); ?></h2>
			<div class="inside">
				<p><?php esc_html_e( 'Please take a minute to leave a review on Suki, we would really appreciate it!', 'suki' ); ?></p>
				<p>
					<a href="https://wordpress.org/support/theme/suki/reviews/?rate=5#new-post" class="button button-secondary" target="_blank" rel="noopener">
						<span class="dashicons dashicons-thumbs-up"></span>
						<?php esc_html_e( 'Rate us &#9733;&#9733;&#9733;&#9733;&#9733;', 'suki' ); ?>
					</a>
				</p>
			</div>
		</div>
		<?php
	}
}

Suki_Admin::instance();