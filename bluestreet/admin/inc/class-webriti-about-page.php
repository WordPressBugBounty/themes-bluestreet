<?php
/**
 * @author Webriti
 */

if (!class_exists('bluestreet_About_Page')) {
	class bluestreet_About_Page {

		protected static $instance;
		private $options;
		private $version = '1.0.0';
		private $theme;
		private $demo_link;
		private $docs_link;
		private $rate_link;
		private $theme_page;
		private $pro_link;
		private $tabs;
		private $action_count;
		private $recommended_actions;

		public static function get_instance() {

			if (is_null(self::$instance)) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		function __construct() {
			$this->theme            = wp_get_theme();
			$actions                   = $this->get_recommended_actions();
			$this->action_count        = $actions['count'];
			$this->recommended_actions = $actions['actions'];

			add_action('admin_menu', array($this, 'add_theme_info_menu'));
			add_action('wp_ajax_bluestreet_update_rec_acts', array($this, 'update_recommended_actions_watch'));
			add_action('load-themes.php', array($this, 'activation_admin_notice'));
			/* enqueue script and style for welcome screen */
			add_action( 'admin_enqueue_scripts', array( $this, 'style_and_scripts' ) );
			
			/* load welcome screen */
			add_action( 'bluestreet_info_screen', array( $this, 'getting_started' ), 	    10 );
			add_action( 'bluestreet_info_screen', array( $this, 'github' ), 		            40 );
			add_action( 'bluestreet_info_screen', array( $this, 'welcome_free_pro' ), 				50 );
			add_action( 'bluestreet_info_screen', array( $this, 'recommended_actions' ), 				50 );
			add_action( 'bluestreet_info_screen', array( $this, 'support_themes' ), 				50 );
			add_action( 'bluestreet_info_screen', array( $this, 'changelog_themes' ), 				50 );
					
			}

		/**
	 * Load welcome screen css and javascript
	 * @sfunctionse  1.8.2.4
	 */
	public function style_and_scripts( $hook_suffix ) {

		if ( 'appearance_page_bluestreet-welcome' == $hook_suffix ) {
			
			
			wp_enqueue_style( 'bluestreet-info-css', BLUESTREET_PARENT_TEMPLATE_DIR_URI . '/css/bootstrap.css' );
			
			wp_enqueue_style( 'bluestreet-info-screen-css', BLUESTREET_TEMPLATE_DIR_URI . '/admin/assets/css/welcome.css' );
			
			wp_enqueue_style('bluestreet-theme-info-style', BLUESTREET_TEMPLATE_DIR_URI . '/admin/assets/css/welcome-page-styles.css');
			
			wp_enqueue_style('bluestreet-welcome_customizer', BLUESTREET_TEMPLATE_DIR_URI . '/admin/assets/css/welcome_customizer.css');
			wp_enqueue_script('plugin-install');
			wp_enqueue_script('updates');
			wp_enqueue_script('bluestreet-companion-install', BLUESTREET_TEMPLATE_DIR_URI . '/admin/assets/js/plugin-install.js', array('jquery'));
			wp_enqueue_script('bluestreet-about-tabs', BLUESTREET_TEMPLATE_DIR_URI . '/admin/assets/js/about-tabs.js', array('jquery'));
			wp_localize_script('bluestreet-companion-install', 'bluestreet_companion_install',
				array(
					'installing' => esc_html__('Installing', 'bluestreet'),
					'activating' => esc_html__('Activating', 'bluestreet'),
					'error'      => esc_html__('Error', 'bluestreet'),
					'ajax_url'   => esc_url(admin_url('admin-ajax.php')),
				)
			);
		}
	}

	/**
	 * Load scripts for customizer page
	 * @sfunctionse  1.8.2.4
	 */
	public function scripts_for_customizer() {

		wp_enqueue_style( 'bluestreet-info-screen-customizer-css', BLUESTREET_TEMPLATE_DIR_URI . '/admin/assets/css/welcome_customizer.css' );
		wp_enqueue_script( 'bluestreet-info-screen-customizer-js', BLUESTREET_TEMPLATE_DIR_URI . '/admin/assets/js/welcome_customizer.js', array('jquery'), '20120206', true );

		global $bluestreet_required_actions;

		$nr_actions_required = 0;

		/* get number of required actions */
		if( get_option('bluestreet_show_required_actions') ):
			$bluestreet_show_required_actions = get_option('bluestreet_show_required_actions');
		else:
			$bluestreet_show_required_actions = array();
		endif;

		if( !empty($bluestreet_required_actions) ):
			foreach( $bluestreet_required_actions as $bluestreet_required_action_value ):
				if(( !isset( $bluestreet_required_action_value['check'] ) || ( isset( $bluestreet_required_action_value['check'] ) && ( $bluestreet_required_action_value['check'] == false ) ) ) && ((isset($bluestreet_show_required_actions[$bluestreet_required_action_value['id']]) && ($bluestreet_show_required_actions[$bluestreet_required_action_value['id']] == true)) || !isset($bluestreet_show_required_actions[$bluestreet_required_action_value['id']]) )) :
					$nr_actions_required++;
				endif;
			endforeach;
		endif;

		wp_localize_script( 'bluestreet-info-screen-customizer-js', 'bluestreetLiteWelcomeScreenCustomizerObject', array(
			'nr_actions_required' => $nr_actions_required,
			'aboutpage' => esc_url( admin_url( 'themes.php?page=bluestreet-info' ) ),
			'customizerpage' => esc_url( admin_url( 'customize.php' ) ),
			'themeinfo' => esc_html__('View Theme Info','bluestreet'),
		) );
	}
		
		

		public function add_theme_info_menu() {
			$theme = $this->theme;
			$count = $this->action_count;
			$count = ($count > 0) ? '<span class="awaiting-mod action-count"><span>' . $count . '</span></span>' : '';
			$title = sprintf(esc_html__('About Bluestreet Theme', 'bluestreet'), esc_html($theme->get('Name')), $count);
			add_theme_page(sprintf(esc_html__('Welcome to %1$s %2$s', 'bluestreet'), esc_html($theme->get('Name')), esc_html($theme->get('Version'))), $title, 'edit_theme_options', 'bluestreet-welcome', array($this, 'print_welcome_page'));
		}

		public function activation_admin_notice() {
			global $pagenow;
			if (is_admin() && ('themes.php' == $pagenow) && isset($_GET['activated'])) {
				add_action('admin_notices', array($this, 'welcome_admin_notice'), 99);
			}
		}

		public function welcome_admin_notice() {
			$theme_info = wp_get_theme();
			?>
			<div class="updated notice is-dismissible">
				<p><?php echo sprintf( esc_html__("Bluestreet theme is installed. To take full advantage of the features this theme has to offer visit our %1\$s welcome page %2\$s", "bluestreet"), '<a href="' . esc_url( admin_url( 'themes.php?page=bluestreet-welcome' ) ) . '">', '</a>' ); ?></p>
				<p><a href="<?php echo esc_url( admin_url( 'themes.php?page=bluestreet-welcome' ) ); ?>" class="button" style="text-decoration: none;"><?php esc_html_e( 'Get started with Bluestreet theme', 'bluestreet' ); ?></a></p>
			</div>
		<?php
		}


		public function print_welcome_page() {
			$theme = $this->theme;
			?>
	  <div class="container-fluid">
		<div class="row">
		<div class="col-md-12">
			<div class="wrap theme-info-wrap bluestreet-wrap">
				<div style="clear: both;"></div>
				<div class="theme-welcome-container" style="min-height:300px;">
					<div class="theme-welcome-inner">
						<?php
							$tabs = $this->get_tabs_array();
							$tabs_head     = '';
							$tab_file_path = '';
							$active_tab    = 'getting_started';

							if (isset($_GET['tab']) && $_GET['tab']) {
								$active_tab = sanitize_text_field($_GET['tab']);
							}

							foreach ($tabs as $key => $tab) {
								$active_class = '';
								$count        = '';
								if ($active_tab == $tab['link']) {
									
									$tab_file_path = $tab['file_path'];
								}

								if ($tab['link'] == 'recommended_actions') {
									$count = $this->action_count;
									$count = ($count > 0) ? '<span class="badge-action-count">' . $count . '</span>' : '';
								}
                             
	
								$tabs_head .= sprintf('<li role="presentation"><a href="%s" class="nav-tab %s" role="tab" data-toggle="tab">%s</a></li>', esc_url(('#' . $tab['link'])), $active_class, $tab['name'] . $count);
								                    
							}
							
						?>
		
						 <ul class="bluestreet-nav-tabs" role="tablist">
							<?php echo wp_kses_post($tabs_head); ?>
						 </ul>
						 
						 	<div class="bluestreet-tab-content">

			<?php do_action( 'bluestreet_info_screen' ); ?>

		</div>
						
						 <div class="tab-content <?php echo esc_attr($active_tab); ?>">
						 	<?php
								if (!empty($tab_file_path) && file_exists($tab_file_path)) {
									require_once $tab_file_path;
								}
							?>
						 	<div style="clear: both;"></div>
						 </div>
					</div>
				</div>
			</div></div></div></div>
		
			<?php
		}
		
		public function getting_started() {
		require_once( BLUESTREET_TEMPLATE_DIR . '/admin/tab-pages/getting-started.php' );
	}

	/**
	 * Contribute
	 *
	 */
	public function github() {
		require_once( BLUESTREET_TEMPLATE_DIR . '/admin/tab-pages/useful_plugins.php' );
	}


	/**
	 * Free vs PRO
	 * 
	 */
	public function welcome_free_pro() {
		require_once( BLUESTREET_TEMPLATE_DIR . '/admin/tab-pages/free_vs_pro.php' );
	}
	
	
	/**
	 * Recommended Action
	 * 
	 */
	public function recommended_actions() {
		require_once( BLUESTREET_TEMPLATE_DIR . '/admin/tab-pages/recommended_actions.php' );
	}
	
	/**
	 * Support 
	 * 
	 */
	public function support_themes() {
		require_once( BLUESTREET_TEMPLATE_DIR . '/admin/tab-pages/support.php' );
	}

	/**
	 * Output the changelog screen.
	 */
	public function changelog_themes() {
		//global $wp_filesystem;

		?>
		<div id="changelog" class="bluestreet-tab-pane panel-close">
		<div class="wrap about-wrap">

			<?php //$this->intro(); ?>

			<p class="about-description"><?php esc_html_e( 'See changelog below:', 'bluestreet' ); ?></p>

			<?php
			$changelog_file = apply_filters( 'changelog_themes', BLUESTREET_TEMPLATE_DIR. '/changelog.txt' );

			// Check if the changelog file exists and is readable.
			if ( $changelog_file && is_readable( $changelog_file ) ) {
				//WP_Filesystem();
				$changelog      = file_get_contents( $changelog_file );
				$changelog_list = $this->parse_changelog( $changelog );

				echo wp_kses_post( $changelog_list );
			}
			?>
		</div>
		</div>
		<?php
	}

	/**
	 * Parse changelog from readme file.
	 *
	 * @param  string $content
	 *
	 * @return string
	 */
	private function parse_changelog( $content ) {
		$matches   = null;
		$regexp    = '~==\s*Changelog\s*==(.*)($)~Uis';
		$changelog = '';

		if ( preg_match( $regexp, $content, $matches ) ) {
			$changes = explode( '\r\n', trim( $matches[1] ) );

			$changelog .= '<pre class="changelog">';

			foreach ( $changes as $index => $line ) {
				$changelog .= wp_kses_post( preg_replace( '~(=\s*Version\s*(\d+(?:\.\d+)+)\s*=|$)~Uis', '<span class="title">${1}</span>', $line ) );
			}

			$changelog .= '</pre>';
		}

		return wp_kses_post( $changelog );
	}

		public function get_tabs_array() {
			$tabs_array = array();
			
			
			$tabs_array[]	= array(
					'link'      => 'getting_started',
					'name'      => esc_html__('Getting Started', 'bluestreet'),
					'file_path' => BLUESTREET_TEMPLATE_DIR . '/admin/tab-pages/getting-started.php',
				);
			$tabs_array[]	= 	array(
					'link'      => 'recommended_actions',
					'name'      => esc_html__('Recommended Actions', 'bluestreet'),
					'file_path' => BLUESTREET_TEMPLATE_DIR . '/admin/tab-pages/recommended-actions.php',
				);


			if(count($this->get_useful_plugins()) > 0){
				$tabs_array[]	= 	array(
						'link'      => 'useful_plugins',
						'name'      => esc_html__('Why Upgrade to PRO?', 'bluestreet'),
						'file_path' => BLUESTREET_TEMPLATE_DIR . '/admin/tab-pages/useful_plugins.php',
				);
			}
			
			$tabs_array[]	= 	array(
					'link'      => 'free_vs_pro',
					'name'      => esc_html__('Free vs Pro', 'bluestreet'),
					'file_path' => BLUESTREET_TEMPLATE_DIR . '/admin/tab-pages/free-vs-pro.php',
				);

			$tabs_array[]	= 	array(
					'link'      => 'support',
					'name'      => esc_html__('Support', 'bluestreet'),
					'file_path' => BLUESTREET_TEMPLATE_DIR . '/admin/tab-pages/support.php',
			);
			
			$tabs_array[]	= 	array(
					'link'      => 'changelog',
					'name'      => esc_html__('Changelog', 'bluestreet'),
					'file_path' => BLUESTREET_TEMPLATE_DIR . '/admin/tab-pages/changelog.php',
			);
			
			return $tabs_array;
			
		}

		public function get_recommended_plugins() {
			
			$plugins = apply_filters('bluestreet_recommended_plugins', array());
			return $plugins;
		}

		public function get_useful_plugins() {
			$plugins = apply_filters('bluestreet_useful_plugins', array());
			return $plugins;
		}



		public function get_recommended_actions() {

			$act_count           = 0;
			$actions_todo = get_option( 'recommended_actions', array());
			
			$plugins = $this->get_recommended_plugins();

			if ($plugins) {
				foreach ($plugins as $key => $plugin) {
					$action = array();
					if (!isset($plugin['slug'])) {
						continue;
					}

					$action['id']   = 'install_' . $plugin['slug'];
					$action['desc'] = '';
					if (isset($plugin['desc'])) {
						$action['desc'] = $plugin['desc'];
					}

					$action['name'] = '';
					if (isset($plugin['name'])) {
						$action['title'] = $plugin['name'];
					}

					$link_and_is_done  = $this->get_plugin_buttion($plugin['slug'], $plugin['name'], $plugin['function']);
					$action['link']    = $link_and_is_done['button'];
					$action['is_done'] = $link_and_is_done['done'];
					if (!$action['is_done'] && (!isset($actions_todo[$action['id']]) || !$actions_todo[$action['id']])) {
						$act_count++;
					}
					$recommended_actions[] = $action;
					$actions_todo[]        = array('id' => $action['id'], 'watch' => true);
				}
				return array('count' => $act_count, 'actions' => $recommended_actions);
			}

		}

		public function get_plugin_buttion($slug, $name, $function) {
			$is_done      = false;
			$button_html  = '';
			$is_installed = $this->is_plugin_installed($slug);
			$plugin_path  = $this->get_plugin_basename_from_slug($slug);
			$is_activeted = (function_exists($function)) ? true : false;
			if (!$is_installed) {
				$plugin_install_url = add_query_arg(
					array(
						'action' => 'install-plugin',
						'plugin' => $slug,
					),
					self_admin_url('update.php')
				);
				$plugin_install_url = wp_nonce_url($plugin_install_url, 'install-plugin_' . esc_attr($slug));
				if($slug==='webriti-companion'){
					$plugin_url = "https://webriti.com/extensions/webriti-companion.zip";
					$button_html = sprintf(
					    '<button id="install-plugin-button-options-page" class="button" data-plugin-url="%1$s">%2$s</button>',
					    esc_url($plugin_url),
					    esc_html__('Install', 'bluestreet')
					);
				}
				else{
					$button_html        = sprintf('<a class="webriti-plugin-install install-now button-secondary button" data-slug="%1$s" href="%2$s" aria-label="%3$s" data-name="%4$s">%5$s</a>',
						esc_attr($slug),
						esc_url($plugin_install_url),
						sprintf(esc_html__('Install %s Now', 'bluestreet'), esc_html($name)),
						esc_html($name),
						esc_html__('Install & Activate', 'bluestreet')
					);
				}
			} elseif ($is_installed && !$is_activeted) {

				$plugin_activate_link = add_query_arg(
					array(
						'action'        => 'activate',
						'plugin'        => rawurlencode($plugin_path),
						'plugin_status' => 'all',
						'paged'         => '1',
						'_wpnonce'      => wp_create_nonce('activate-plugin_' . $plugin_path),
					), self_admin_url('plugins.php')
				);

				$button_html = sprintf('<a class="webriti-plugin-activate activate-now button-primary button" data-slug="%1$s" href="%2$s" aria-label="%3$s" data-name="%4$s">%5$s</a>',
					esc_attr($slug),
					esc_url($plugin_activate_link),
					sprintf(esc_html__('Activate %s Now', 'bluestreet'), esc_html($name)),
					esc_html($name),
					esc_html__('Activate', 'bluestreet')
				);
			} elseif ($is_activeted) {
				$button_html = sprintf('<div class="action-link button disabled"><span class="dashicons dashicons-yes"></span> %s</div>', esc_html__('Active', 'bluestreet'));
				$is_done     = true;
			}

			return array('done' => $is_done, 'button' => $button_html);
		}

		public function get_plugin_basename_from_slug($slug) {
			$keys = array_keys($this->get_installed_plugins());
			foreach ($keys as $key) {
				if (preg_match('|^' . $slug . '/|', $key)) {
					return $key;
				}
			}
			return $slug;
		}

		public function is_plugin_installed($slug) {
			$installed_plugins = $this->get_installed_plugins(); // Retrieve a list of all installed plugins (WP cached).
			$file_path         = $this->get_plugin_basename_from_slug($slug);
			return (!empty($installed_plugins[$file_path]));
		}

		public function get_installed_plugins() {

			if (!function_exists('get_plugins')) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			return get_plugins();
		}

		public function update_recommended_actions_watch() {
			if (isset($_POST['action_id'])) {
				$action_id    = sanitize_text_field($_POST['action_id']);
				$actions_todo = get_option('recommended_actions', array());

				if ((!isset($actions_todo[$action_id]) || !$actions_todo[$action_id])) {
					$actions_todo[$action_id] = true;
				} else {
					$actions_todo[$action_id] = false;
				}
				update_option('recommended_actions', $actions_todo);
			}
			echo json_encode(get_option('recommended_actions'));
			wp_die();
		}


		public function get_plugin_info_api( $slug ) {
			require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
			$call_api = get_transient( 'wt_about_plugin_info_' . $slug );

			if ( false === $call_api ) {
				$call_api = plugins_api(
					'plugin_information', array(
						'slug'   => $slug,
						'fields' => array(
							'downloaded'        => false,
							'rating'            => false,
							'description'       => false,
							'short_description' => true,
							'donate_link'       => false,
							'tags'              => false,
							'sections'          => true,
							'homepage'          => true,
							'added'             => false,
							'last_updated'      => false,
							'compatibility'     => false,
							'tested'            => false,
							'requires'          => false,
							'downloadlink'      => false,
							'icons'             => true,
						),
					)
				);
				set_transient( 'wt_about_plugin_info_' . $slug, $call_api, 30 * MINUTE_IN_SECONDS );
			}

			return $call_api;
		}

		public function get_plugin_icon( $icons ) {

			if ( ! empty( $icons['svg'] ) ) {
				$plugin_icon_url = $icons['svg'];
			} elseif ( ! empty( $icons['2x'] ) ) {
				$plugin_icon_url = $icons['2x'];
			} elseif ( ! empty( $icons['1x'] ) ) {
				$plugin_icon_url = $icons['1x'];
			} else {
				$plugin_icon_url = BLUESTREET_TEMPLATE_DIR_URI . '/admin/assets/images/placeholder_plugin.png';
			}
			return $plugin_icon_url;
		}
	}
}


function bluestreet_useful_plugins_array($plugins){
	$plugins[] = array(
					'slug'     => 'elementor',
				);

	return $plugins;
}
add_filter('bluestreet_useful_plugins', 'bluestreet_useful_plugins_array');

function bluestreet_recommended_plugins_array($plugins){
	$plugins[] = array(
					'name'     => esc_html__('Webriti Companion', 'bluestreet'),
					'slug'     => 'webriti-companion',
					'function'     => 'webriti_companion_activate',
					'desc'     => esc_html__('It is highly recommended that you install the Webriti Companion plugin to have access to the advance Frontpage sections and other theme features', 'bluestreet'),
				);
				
	$plugins[] = array(
					'name'     => esc_html__('Contact Form 7', 'bluestreet'),
					'slug'     => 'contact-form-7',
					'function'     => 'wpcf7',
					'desc'     => esc_html__('It is recommended that you install the Contact Form 7 plugin to show contact form on pages', 'bluestreet'),
				);

	$plugins[] = array(
	                'name' => esc_html__('WooCommerce', 'bluestreet'),
	                'slug' => 'woocommerce',
	                'function' => 'WC',
	                'desc' => esc_html__('To create a shop page you just need to install this plugin & activate it', 'bluestreet'),
            	);

	$plugins[] = array(
				'name'     => esc_html__('Spice Post Slider', 'bluestreet'),
				'slug'     => 'spice-post-slider',
				'function' => 'sps_fs',
				'desc'     => esc_html__('To display the posts in a beautiful slider with multiple options, install & activate this plugin.', 'bluestreet'),
				);

	$plugins[] = array(
					'name'     => esc_html__('Seo Optimized Images', 'bluestreet'),
					'slug'     => 'seo-optimized-images',
					'function' => 'sobw_fs',
					'desc'     => esc_html__('It is recommended that you install & activate the Seo Optimized Images plugin to dynamically insert SEO Friendly alt attributes and title attributes to your Images.', 'bluestreet'),
				);			
	
	return $plugins;
}
add_filter('bluestreet_recommended_plugins', 'bluestreet_recommended_plugins_array');

function bluestreet_About_Page() {
	return bluestreet_About_Page::get_instance();
}
global $bluestreet_about_page;
$bluestreet_about_page = bluestreet_About_Page();