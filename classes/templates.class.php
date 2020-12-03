<?php

class MailsterTemplates {

	public $path;
	public $url;

	private $download_url = 'https://static.mailster.co/templates/mailster.zip';
	private $headers      = array(
		'name'        => 'Template Name',
		'label'       => 'Name',
		'uri'         => 'Template URI',
		'url'         => 'Template URI',
		'slug'        => 'Template Slug',
		'description' => 'Description',
		'author'      => 'Author',
		'author_uri'  => 'Author URI',
		'author_url'  => 'Author URI',
		'version'     => 'Version',
	);

	private $template_fields = array(
		'ID'               => null,
		'name'             => null,
		'slug'             => null,
		'image'            => null,
		'imagex2'          => null,
		'description'      => null,
		'index'            => null,
		'url'              => null,
		'endpoint'         => null,
		'files'            => null,
		'version'          => null,
		'new_version'      => null,
		'updated'          => null,
		'author'           => null,
		'author_profile'   => null,
		'requires'         => '2.2',
		'is_default'       => null,
		'is_verified'      => null,
		'author_profile'   => null,
		'homepage'         => null,
		'download'         => null,
		'download_url'     => null,
		'price'            => null,
		'envato_item_id'   => null,
		'gumroad_url'      => null,
		'update_available' => false,
	);

	public function __construct() {

		$this->path = MAILSTER_UPLOAD_DIR . '/templates';
		$this->url  = MAILSTER_UPLOAD_URI . '/templates';

		add_action( 'init', array( &$this, 'init' ) );
		add_action( 'mailster_get_screenshots', array( &$this, 'get_screenshots' ), 10, 4 );

	}


	public function init() {

		add_action( 'admin_menu', array( &$this, 'admin_menu' ), 50 );
		add_action( 'mailster_copy_template', array( &$this, 'copy_template' ) );
		add_action( 'mailster_copy_backgrounds', array( &$this, 'copy_backgrounds' ) );
	}


	public function admin_menu() {

		if ( $updates = $this->get_updates() ) {
			$updates = ' <span class="update-plugins count-' . $updates . '" title="' . sprintf( esc_html__( _n( '%d Update available', '%d Updates available', $updates, 'mailster' ) ), $updates ) . '"><span class="update-count">' . $updates . '</span></span>';
		} else {
			$updates = '';
		}

		$page = add_submenu_page( 'edit.php?post_type=newsletter', esc_html__( 'Templates', 'mailster' ), esc_html__( 'Templates', 'mailster' ) . $updates, 'mailster_manage_templates', 'mailster_templates', array( &$this, 'templates' ) );
		add_action( 'load-' . $page, array( &$this, 'scripts_styles' ) );

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_path() {
		return $this->path;
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_url() {
		return $this->url;
	}


	public function download_template( $url, $slug = null ) {

		$download_url = rawurldecode( $url );
		$slug         = isset( $slug ) ? rawurldecode( $slug ) : null;

		if ( ! function_exists( 'download_url' ) ) {
			include ABSPATH . 'wp-admin/includes/file.php';
		}

		$tempfile = download_url( $download_url );
		if ( is_wp_error( $tempfile ) ) {
			return $tempfile;
		}
		$result = $this->unzip_template( $tempfile, $slug, true, true );

		if ( is_wp_error( $result ) ) {
			return $result;
		} else {
			$redirect = admin_url( 'edit.php?post_type=newsletter&page=mailster_templates' );
			$redirect = add_query_arg( array( 'new' => $slug ), $redirect );

			$this->schedule_screenshot( $slug, 'index.html', true );

			return $redirect;
		}

		return false;

	}


	public function remove_template( $slug ) {

		$this->templatepath = $this->path . '/' . $slug;

		if ( ! file_exists( $this->templatepath . '/index.html' ) ) {
			return false;
		}

		mailster_require_filesystem();

		global $wp_filesystem;
		if ( $wp_filesystem->delete( $this->templatepath, true ) ) {

			$screenshots = MAILSTER_UPLOAD_DIR . '/screenshots/' . $slug;
			if ( is_dir( $screenshots ) ) {
				$wp_filesystem->delete( $screenshots, true );
			}

			return true;

		}

		return false;
	}


	/**
	 *
	 *
	 * @param unknown $templatefile
	 * @param unknown $renamefolder (optional)
	 * @param unknown $overwrite    (optional)
	 * @param unknown $backup_old   (optional)
	 * @return unknown
	 */
	public function unzip_template( $templatefile, $renamefolder = null, $overwrite = false, $backup_old = false ) {

		global $wp_filesystem;

		mailster_require_filesystem();

		$uploadfolder = mailster( 'helper' )->mkdir( 'uploads' );

		$uploadfolder = $uploadfolder . uniqid();

		if ( ! is_dir( $uploadfolder ) ) {
			wp_mkdir_p( $uploadfolder );
		}

		if ( ! wp_is_writable( $uploadfolder ) ) {
			return new WP_Error( 'not_writeable', esc_html__( 'The content folder is not writeable', 'mailster' ) );
		}

		if ( is_wp_error( unzip_file( $templatefile, $uploadfolder ) ) ) {
			$wp_filesystem->delete( $uploadfolder, true );
			return new WP_Error( 'unzip', esc_html__( 'Unable to unzip template', 'mailster' ) );
		}

		$templates = $this->get_templates( true );

		if ( $folders = scandir( $uploadfolder ) ) {

			foreach ( $folders as $folder ) {

				if ( in_array( $folder, array( '.', '..' ) ) ) {
					continue;
				}

				if ( ! is_null( $renamefolder ) ) {

					$renamefolder = sanitize_file_name( $renamefolder );

					if ( $renamefolder == $folder ) {
						$moved = true;
					} else {
						if ( ! ( $moved = $wp_filesystem->move( $uploadfolder . '/' . $folder, $uploadfolder . '/' . $renamefolder, true ) ) ) {
							$moved = @rename( $uploadfolder . '/' . $folder, $uploadfolder . '/' . $renamefolder );
						}
					}

					if ( $moved ) {
						$folder = $renamefolder;
					} else {
						$wp_filesystem->delete( $uploadfolder, true );
						return new WP_Error( 'not_writeable', esc_html__( 'Unable to save template', 'mailster' ) );
					}
				}

				$data = $this->get_template_data( $uploadfolder . '/' . $folder . '/index.html' );

				// need index.html file
				if ( ! $data ) {

					$all_files = list_files( $uploadfolder );
					$all_files = str_replace( trailingslashit( $uploadfolder ), '', $all_files );

					// strict search (only in filename)
					$zips = preg_grep( '#(mailster|mymail)([^\/]+)?\.zip$#i', $all_files );
					if ( empty( $zips ) ) {
						// lazy search (also in dirname)
						$zips = preg_grep( '#(mailster|mymail)(.*)?\.zip$#i', $all_files );
					}

					foreach ( $zips as $zip ) {

						$result = $this->unzip_template( trailingslashit( $uploadfolder ) . $zip, $renamefolder, $overwrite, $backup_old );
						if ( ! is_wp_error( $result ) ) {
							$wp_filesystem->delete( $uploadfolder, true );
							return $result;
						}
					}

					$wp_filesystem->delete( $uploadfolder, true );
					return new WP_Error( 'wrong_file', esc_html__( 'This is not a valid Mailster template ZIP', 'mailster' ) );

				}

				$templateslug = sanitize_title( $data['slug'], $data['name'] );

				if ( ! $overwrite && in_array( $templateslug, $templates ) ) {

					$wp_filesystem->delete( $uploadfolder, true );

					return new WP_Error( 'template_exists', sprintf( esc_html__( 'Template %s already exists!', 'mailster' ), '"' . $data['name'] . '"' ) );

				}

				$files = list_files( $uploadfolder . '/' . $folder );

				$removed_files = array();

				$allowed_mimes = array( 'text/html', 'text/xml', 'text/plain', 'image/svg+xml', 'image/svg', 'image/png', 'image/gif', 'image/jpeg', 'image/tiff', 'image/x-icon' );
				$whitelist     = array( 'json', 'woff', 'woff2', 'ttf', 'eot' );
				$blacklist     = array( 'php', 'bin', 'exe' );

				foreach ( $files as $file ) {

					$basename = wp_basename( $file );

					if ( ! is_file( $file ) ) {
						$wp_filesystem->delete( $file, true );
						continue;
					}

					if ( function_exists( 'mime_content_type' ) ) {
						$mimetype = mime_content_type( $file );
					} else {
						$validate = wp_check_filetype( $file );
						$mimetype = $validate['type'];
					}

					if ( ( ! in_array( $mimetype, $allowed_mimes ) && ! preg_match( '#\.(' . implode( '|', $whitelist ) . ')$#i', $file ) || preg_match( '#\.(' . implode( '|', $blacklist ) . ')$#i', $file ) ) ) {
						$removed_files[] = $basename;
						$wp_filesystem->delete( $file, true );
						continue;
					}
					// sanitize HTML upload
					if ( 'text/html' == $mimetype ) {
						$raw = file_get_contents( $file );
						$wp_filesystem->put_contents( $file, mailster()->sanitize_content( $raw, null, true ), FS_CHMOD_FILE );
					}
				}

				// with name value
				if ( ! empty( $data['name'] ) ) {
					wp_mkdir_p( $this->path . '/' . $templateslug );

					if ( $backup_old ) {
						$old_data  = $this->get_template_data( $this->path . '/' . $templateslug . '/index.html' );
						$old_files = list_files( $this->path . '/' . $templateslug, 1 );
						$new_files = list_files( $uploadfolder . '/' . $folder, 1 );
						foreach ( $new_files as $file ) {
							if ( is_file( $file ) && preg_match( '#\.html$#', $file ) ) {
								$old_file = str_replace( $uploadfolder, $this->path, $file );
								if ( file_exists( $old_file ) ) {
									if ( md5_file( $file ) == md5_file( $old_file ) ) {
										continue;
									}

									if ( ! $wp_filesystem->copy( $old_file, preg_replace( '#\.html$#', '-' . $old_data['version'] . '.html', $old_file ) ) ) {
										copy( $old_file, preg_replace( '#\.html$#', '-' . $old_data['version'] . '.html', $old_file ) );

									}
								}
							}
						}
					}

					copy_dir( $uploadfolder . '/' . $folder, $this->path . '/' . $templateslug );
				} else {
					$wp_filesystem->delete( $uploadfolder, true );
					return new WP_Error( 'wrong_header', esc_html__( 'The header of this template files is missing or corrupt', 'mailster' ) );
				}

				if ( ! empty( $removed_files ) ) {
					mailster_notice( '<strong>' . esc_html__( 'Following files have been removed during upload:', 'mailster' ) . '</strong><ul><li>' . implode( '</li><li>', $removed_files ) . '</li></ul>', 'info', true );
				}

				if ( file_exists( $uploadfolder . '/' . $folder . '/colors.json' ) ) {

					$colors = $wp_filesystem->get_contents( $uploadfolder . '/' . $folder . '/colors.json' );

					if ( $colors ) {
						$colorschemas = json_decode( $colors );

						$customcolors = get_option( 'mailster_colors', array() );

						if ( ! isset( $customcolors[ $folder ] ) ) {

							$customcolors[ $folder ] = array();
							foreach ( $colorschemas as $colorschema ) {
								$hash                             = md5( implode( '', $colorschema ) );
								$customcolors[ $folder ][ $hash ] = $colorschema;
							}

							update_option( 'mailster_colors', $customcolors );

						}
					}
				}
			}

			$wp_filesystem->delete( $uploadfolder, true );

			if ( isset( $templateslug ) && $templateslug ) {

				return $data;
			}
		}

		return new WP_Error( 'file_error', esc_html__( 'There was a problem progressing the file', 'mailster' ) );

	}


	/**
	 *
	 *
	 * @param unknown $slug (optional)
	 * @return unknown
	 */
	public function renew_default_template( $slug = 'mailster' ) {

		if ( ! function_exists( 'download_url' ) ) {
			include ABSPATH . 'wp-admin/includes/file.php';
		}

		$zip = download_url( $this->download_url, 60 );

		if ( is_wp_error( $zip ) ) {
			return $zip;
		}

		return $this->unzip_template( $zip, $slug );

	}


	public function templates() {

		if ( current_user_can( 'mailster_upload_templates' ) ) {
			remove_action( 'post-plupload-upload-ui', 'media_upload_flash_bypass' );
			wp_enqueue_script( 'plupload-all' );
		}

		include MAILSTER_DIR . 'views/templates.php';

	}

	public function install_templates() {

		if ( current_user_can( 'mailster_upload_templates' ) ) {
			remove_action( 'post-plupload-upload-ui', 'media_upload_flash_bypass' );
			wp_enqueue_script( 'plupload-all' );
		}

		include MAILSTER_DIR . 'views/templates-install.php';

	}


	/**
	 *
	 *
	 * @param unknown $return (optional)
	 * @param unknown $nonce  (optional)
	 */
	private function ajax_nonce( $return = null, $nonce = 'mailster_nonce' ) {
		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], $nonce ) ) {
			die( $return );
		}

	}


	private function ajax_filesystem() {
		if ( 'ftpext' == get_filesystem_method() && ( ! defined( 'FTP_HOST' ) || ! defined( 'FTP_USER' ) || ! defined( 'FTP_PASS' ) ) ) {
			$return['msg']     = esc_html__( 'WordPress is not able to access to your filesystem!', 'mailster' );
			$return['msg']    .= "\n" . sprintf( esc_html__( 'Please add following lines to the wp-config.php %s', 'mailster' ), "\n\ndefine('FTP_HOST', 'your-ftp-host');\ndefine('FTP_USER', 'your-ftp-user');\ndefine('FTP_PASS', 'your-ftp-password');\n" );
			$return['success'] = false;
			echo json_encode( $return );
			exit;
		}

	}


	/**
	 *
	 *
	 * @param unknown $slugsonly (optional)
	 * @return unknown
	 */
	public function get_templates( $slugsonly = false ) {

		$templates = array();

		if ( ! function_exists( 'list_files' ) ) {
			include ABSPATH . 'wp-admin/includes/file.php';
		}

		$files   = list_files( $this->path, 2 );
		$current = mailster_option( 'default_template' );
		sort( $files );

		foreach ( $files as $file ) {
			if ( basename( $file ) == 'index.html' && dirname( $file ) != $this->path ) {

				$filename = str_replace( $this->path . '/', '', $file );
				$slug     = dirname( $filename );
				if ( $slugsonly ) {
					$templates[] = $slug;
				} else {
					$templates[ $slug ] = $this->get_template_data( $file );
				}
			}
		}

		if ( $slugsonly ) {
			sort( $templates );
		} else {
			ksort( $templates );
			// bring the current one to the first position
			if ( $current && isset( $templates[ $current ] ) ) {
				$templates = array( $current => $templates[ $current ] ) + $templates;
			}
		}

		return $templates;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_all_files() {

		$templates = $this->get_templates();

		$files = array();

		foreach ( $templates as $slug => $data ) {
			$files[ $slug ] = $this->get_files( $slug );
		}

		return $files;

	}


	/**
	 *
	 *
	 * @param unknown $slug           (optional)
	 * @param unknown $group_versions (optional)
	 * @return unknown
	 */
	public function get_files( $slug = '', $group_versions = false ) {

		if ( empty( $slug ) ) {
			return array();
		}

		$templates = array();
		$files     = list_files( $this->path . '/' . $slug, 1 );

		sort( $files );

		$list = array(
			'index.html' => $this->get_template_data( $this->path . '/' . $slug . '/index.html' ),
		);

		if ( file_exists( $this->path . '/' . $slug . '/notification.html' ) ) {
			$list['notification.html'] = $this->get_template_data( $this->path . '/' . $slug . '/notification.html' );
		}

		foreach ( $files as $file ) {

			if ( strpos( $file, '.html' ) && is_file( $file ) ) {
				$list[ basename( $file ) ] = $this->get_template_data( $file );
			}
		}

		if ( ! $group_versions ) {
			return $list;
		}

		$group_list = array();
		foreach ( $list as $file => $data ) {
			$v = 'edge';
			if ( preg_match( '#-(([0-9.]+)\.([0-9]+))\.html$#', $file, $hits ) ) {
				$v = $hits[1];
			}
			if ( ! isset( $group_list[ $v ] ) ) {
				$group_list[ $v ] = array();
			}

			$group_list[ $v ][ $file ] = $data;
		}

		return $group_list;

	}


	/**
	 *
	 *
	 * @param unknown $slug (optional)
	 * @return unknown
	 */
	public function get_versions( $slug = null ) {

		$templates = $this->get_templates();
		$versions  = array();
		foreach ( $templates as $s => $data ) {

			$versions[ $s ] = $data['version'];
		}

		return ! is_null( $slug ) ? ( isset( $versions[ $slug ] ) ? $versions[ $slug ] : null ) : $versions;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_updates() {

		if ( ! current_user_can( 'mailster_update_templates' ) ) {
			return 0;
		}

		$updates = get_option( 'mailster_templates_updates', null );

		if ( ! is_null( $updates ) ) {
			return (int) $updates;
		}

		if ( ! $templates = get_option( 'mailster_templates' ) ) {
			return 0;
		}

		if ( empty( $templates['templates'] ) ) {
			return 0;
		}

		return array_sum( wp_list_pluck( $templates['templates'], 'update' ) );

	}


	/**
	 *
	 *
	 * @param unknown $file (optional)
	 * @return unknown
	 */
	public function get_raw_template( $file = 'index.html' ) {
		if ( ! file_exists( $this->path . '/' . $this->slug . '/' . $file ) ) {
			return false;
		}

		return file_get_contents( $this->path . '/' . $this->slug . '/' . $file );
	}


	public function scripts_styles() {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_register_style( 'mailster-templates', MAILSTER_URI . 'assets/css/templates-style' . $suffix . '.css', array( 'themes' ), MAILSTER_VERSION );
		wp_enqueue_style( 'mailster-templates' );
		wp_enqueue_style( 'mailster-codemirror', MAILSTER_URI . 'assets/css/libs/codemirror' . $suffix . '.css', array(), MAILSTER_VERSION );
		wp_enqueue_script( 'mailster-codemirror', MAILSTER_URI . 'assets/js/libs/codemirror' . $suffix . '.js', array(), MAILSTER_VERSION, true );
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_style( 'thickbox' );
		wp_enqueue_script( 'mailster-templates', MAILSTER_URI . 'assets/js/templates-script' . $suffix . '.js', array( 'mailster-script' ), MAILSTER_VERSION, true );

		mailster_localize_script(
			'templates',
			array(
				'delete_template_file' => esc_html__( 'Do you really like to remove file %1$s from template %2$s?', 'mailster' ),
				'enter_template_name'  => esc_html__( 'Please enter the name of the new template', 'mailster' ),
				'uploading'            => esc_html__( 'uploading zip file %s', 'mailster' ),
				'confirm_delete'       => esc_html__( 'You are about to delete this template %s', 'mailster' ),
				'update_note'          => esc_html__( 'You are about to update your exiting template files with a new version!', 'mailster' ) . "\n\n" . esc_html__( 'Old template files will be preserved in the templates folder.', 'mailster' ),
			)
		);

	}


	/**
	 *
	 *
	 * @param unknown $slug (optional)
	 * @return unknown
	 */
	public function remove_screenshot( $slug = null ) {

		global $wp_filesystem;

		$folder = MAILSTER_UPLOAD_DIR . '/screenshots';

		if ( ! is_null( $slug ) ) {
			$folder .= '/' . $slug;
		}

		if ( ! is_dir( $folder ) ) {
			return;
		}

		mailster_require_filesystem();

		return $wp_filesystem->delete( $folder, true );

	}


	/**
	 *
	 *
	 * @param unknown $slug
	 * @param unknown $file    (optional)
	 * @param unknown $modules (optional)
	 * @param unknown $async   (optional)
	 */
	public function get_screenshots( $slug, $file = 'index.html', $modules = false, $async = true ) {

		global $wp_filesystem;

		$slug = ( $slug );
		$file = ( $file );

		$filedir = MAILSTER_UPLOAD_DIR . '/templates/' . $slug . '/' . $file;
		$fileuri = MAILSTER_UPLOAD_URI . '/templates/' . $slug . '/' . $file;

		if ( ! file_exists( $filedir ) ) {
			return;
		}

		// prevent error output as 7.4 throws deprecate notice
		// $hash = hash( 'crc32', md5_file( $filedir ) );
		$hash = @base_convert( md5_file( $filedir ), 10, 36 );

		$screenshot_folder_base = mailster( 'helper' )->mkdir( 'screenshots' );

		$screenshot_folder         = $screenshot_folder_base . $slug . '/';
		$screenshot_modules_folder = $screenshot_folder_base . $slug . '/modules/' . $hash . '/';
		$screenshotfile            = $screenshot_folder_base . $slug . '/' . $hash . '.jpg';
		$screenshoturi             = MAILSTER_UPLOAD_URI . '/screenshots/' . $slug . '/' . $hash . '.jpg';

		mailster_require_filesystem();

		if ( ! is_dir( $screenshot_folder ) ) {
			mailster( 'helper' )->mkdir( $screenshot_folder, true );
		}

		// not on localhost
		if ( ! mailster_is_local() ) {

			$url = 'https://s.wordpress.com/mshots/v1/' . ( rawurlencode( $fileuri . '?c=' . $hash ) ) . '?w=600&h=800';

			$response = wp_remote_get(
				$url,
				array(
					'redirection' => 0,
					'method'      => 'HEAD',
				)
			);

			$code = wp_remote_retrieve_response_code( $response );

			if ( 200 == $code ) {

				if ( ! function_exists( 'download_url' ) ) {
					include ABSPATH . 'wp-admin/includes/file.php';
				}

				$tmp_file = download_url( $url );

				if ( is_file( $tmp_file ) && is_readable( $tmp_file ) ) {

					$image_data = getimagesize( $tmp_file );

					if ( 'image/jpeg' == $image_data['mime'] ) {

						if ( ! is_wp_error( $tmp_file ) ) {
							if ( ! is_dir( dirname( $screenshotfile ) ) ) {
								mailster( 'helper' )->mkdir( dirname( $screenshotfile ), true );
							}

							if ( ! $wp_filesystem->copy( $tmp_file, $screenshotfile ) ) {
								@copy( $tmp_file, $screenshotfile );
							}
						}
					} else {

						$this->schedule_screenshot( $slug, $file, false, 30 );

					}
				}
			} elseif ( 307 == $code ) {

					$this->schedule_screenshot( $slug, $file, false, 60 );

			} else {

			}
		}

		if ( ! $modules ) {
			return;
		}

		$raw = file_get_contents( $filedir );

		if ( ! preg_match( '#<modules([^>]*)>(.*)<\/modules>#is', $raw, $matches ) ) {
			return;
		}

		$modules_html = $matches[0];

		$request_url = 'https://api.mailster.co/module/v1/';

		$file_size = strlen( $raw );
		$hash      = md5( $raw );
		$blocked   = get_transient( '_mailster_screenshot_error' );

		if ( $blocked && isset( $blocked[ $hash ] ) ) {
			return;
		}

		$headers = array(
			'accept'             => 'application/json',
			'x-mailster-length'  => $file_size,
			'x-mailster-hash'    => $hash,
			'x-mailster-version' => MAILSTER_VERSION,
			'x-mailster-site'    => get_bloginfo( 'url' ),
			'x-mailster-license' => mailster()->license(),
			'x-mailster-url'     => $fileuri,
		);

		$response = wp_remote_get(
			$request_url,
			array(
				'headers' => $headers,
				'timeout' => 2,
			)
		);

		$response_headers = wp_remote_retrieve_headers( $response );
		$response_code    = wp_remote_retrieve_response_code( $response );

		// file hasn't been generated yet
		if ( 404 == $response_code ) {

			$headers['content-type']   = 'application/binary';
			$headers['content-length'] = $file_size;

			$response = wp_remote_post(
				$request_url,
				array(
					'headers'  => $headers,
					'body'     => $raw,
					'timeout'  => $async ? 1 : 20,
					'blocking' => $async ? false : true,
				)
			);

			unset( $raw );

			if ( $async ) {
				$this->schedule_screenshot( $slug, $file, true, 20, $async );
				return;

			};

			$response_headers = wp_remote_retrieve_headers( $response );
			$response_code    = wp_remote_retrieve_response_code( $response );

		}

		if ( 200 != $response_code ) {

			switch ( $response_code ) {
				case 201:
					$this->schedule_screenshot( $slug, $file, true, 20, $async );
					break;
				case 500:
				case 503:
					$this->schedule_screenshot( $slug, $file, true, 1800, $async );
					break;
				case 406:
					if ( ! is_array( $blocked ) ) {
						$blocked = array();
					}
					$blocked[ $hash ] = time();
					set_transient( '_mailster_screenshot_error', $blocked );
					mailster_notice( sprintf( esc_html__( 'Not able to create module screen shots of %1$s. Read more about this %2$s.', 'mailster' ), $slug . '/' . $file, '<a href="https://kb.mailster.co/where-are-the-module-screen-shots/" class="external">' . esc_html__( 'here', 'mailster' ) . '</a>' ), 'error', false, 'screenshot_error' );
					break;
			}

			return;

		}

		$body   = wp_remote_retrieve_body( $response );
		$result = json_decode( $body );

		if ( ! function_exists( 'download_url' ) ) {
			include ABSPATH . 'wp-admin/includes/file.php';
		}

		$processed = 0;

		if ( isset( $result->modules ) && is_array( $result->modules ) ) {
			foreach ( $result->modules as $i => $fileurl ) {
				if ( file_exists( $screenshot_modules_folder . $i . '.jpg' ) ) {
					continue;
				}

				$tempfile = download_url( $fileurl );

				if ( ! is_wp_error( $tempfile ) ) {

					if ( function_exists( 'exif_imagetype' ) && 2 != exif_imagetype( $tempfile ) ) {
						continue;
					}

					if ( ! is_dir( $screenshot_modules_folder ) ) {
						wp_mkdir_p( $screenshot_modules_folder );
					}

					if ( ! $wp_filesystem->copy( $tempfile, $screenshot_modules_folder . $i . '.jpg' ) ) {
						copy( $tempfile, $screenshot_modules_folder . $i . '.jpg' );
					}

					$processed++;

					if ( $processed >= 30 ) {
						$this->schedule_screenshot( $slug, $file, true, 10 );
						break;
					}
				}
			}
		}

	}


	/**
	 *
	 *
	 * @param unknown $slug
	 * @param unknown $file
	 * @param unknown $modules (optional)
	 * @param unknown $delay   (optional)
	 * @param unknown $async   (optional)
	 */
	public function schedule_screenshot( $slug, $file, $modules = false, $delay = 0, $async = true ) {

		if ( ! mailster_option( 'module_thumbnails' ) ) {
			$modules = false;
		}

		if ( ! wp_next_scheduled( 'mailster_get_screenshots', array( $slug, $file, $modules, $async ) ) && ! wp_next_scheduled( 'mailster_get_screenshots', array( $slug, $file, true, $async ) ) ) {
			wp_schedule_single_event( time() + $delay, 'mailster_get_screenshots', array( $slug, $file, $modules, $async ) );
		}

	}


	/**
	 *
	 *
	 * @param unknown $new
	 */
	public function on_activate( $new ) {

		if ( $new ) {
			try {
				$this->copy_template();
			} catch ( Exception $e ) {
				if ( ! wp_next_scheduled( 'mailster_copy_template' ) ) {
					wp_schedule_single_event( time(), 'mailster_copy_template' );
				}
			}
			try {
				$this->copy_backgrounds();
			} catch ( Exception $e ) {
				if ( ! wp_next_scheduled( 'mailster_copy_backgrounds' ) ) {
					wp_schedule_single_event( time(), 'mailster_copy_backgrounds' );
				}
			}
		}

	}


	public function copy_template() {

		if ( $path = mailster( 'helper' )->mkdir( 'templates' ) ) {
			copy_dir( MAILSTER_DIR . 'templates', $path );
		}

	}


	public function copy_backgrounds() {

		if ( $path = mailster( 'helper' )->mkdir( 'backgrounds' ) ) {
			copy_dir( MAILSTER_DIR . 'assets/img/bg', $path );
		}

	}


	public function query( $query_args ) {

		$endpoint = 'https://mailster.local/templates.json';
		$endpoint = 'https://mailster.dev/templates.json';

		$query_args = wp_parse_args(
			rawurlencode_deep( $query_args ),
			array(
				'type'   => 'keyword',
				'browse' => 'new',
				'page'   => 1,
			)
		);

		if ( $query_args['browse'] == 'installed' ) {
			$templates               = $this->get_templates();
			$query_args['templates'] = implode( ',', array_keys( $templates ) );
		} elseif ( $query_args['browse'] == 'purchased' ) {
			$purchased_id      = (array) get_option( 'mailster_templates_purchased', array() );
			$query_args['ids'] = implode( ',', $purchased_id );
		}

		$cache_key = 'mailster_templates_' . $query_args['browse'] . '_' . md5( serialize( $query_args ) . $endpoint );

		if ( ! ( $result = get_transient( $cache_key ) ) ) {

			$cachetime = DAY_IN_SECONDS;
			$cachetime = 12;

			$result = array(
				'total' => 0,
				'items' => array(),
				'error' => null,
			);

			if ( $query_args['browse'] == 'installed' ) {
				$result['items'] = $templates;
			}

			$args = array(
				'sslverify' => false,
				'headers'   => array( 'hash' => sha1( mailster_option( 'ID' ) ) ),
			);

			$url = add_query_arg( $query_args, $endpoint );

			$response      = wp_remote_get( $url, $args );
			$response_code = wp_remote_retrieve_response_code( $response );

			if ( $response_code != 200 || is_wp_error( $response ) ) {
				$result['error'] = esc_html__( 'We are currently not able to handle your request. Please try again later.', 'mailster' );
				$cachetime       = 120;
			} else {

				$response_body = wp_remote_retrieve_body( $response );

				$response_result = json_decode( $response_body, true );

				$result['items'] = wp_parse_args( $result['items'], $response_result['items'] );
				$result['total'] = max( count( $result['items'] ), $response_result['total'] );

			}

			$result = $this->prepare_results( $result );
			if ( $query_args['browse'] == 'installed' ) {
				$default = mailster_option( 'default_template' );

				if ( $default && isset( $result['items'][ $default ] ) ) {
					$temp = $result['items'][ $default ];
					unset( $result['items'][ $default ] );
					$result['items'] = array( $default => $temp ) + $result['items'];
				}
			}

			set_transient( $cache_key, $result, $cachetime );

		}

		return $result;

	}

	public function prepare_results( $result ) {

		$templates     = $this->get_templates();
		$purchased_ids = get_option( 'mailster_templates_purchased', false );

		foreach ( $result['items'] as $slug => $item ) {

			// fill response with default values
			$result['items'][ $slug ] = array_merge( $this->template_fields, $result['items'][ $slug ] );

			if ( $result['items'][ $slug ]['installed'] = isset( $templates[ $slug ] ) ) {

				$result['items'][ $slug ]                     = array_merge( $templates[ $slug ], $result['items'][ $slug ] );
				$result['items'][ $slug ]['update_available'] = version_compare( $templates[ $slug ]['new_version'], $templates[ $slug ]['version'], '>' );
				$result['items'][ $slug ]['files']            = $this->get_files( $slug );

			}

			$result['items'][ $slug ]['purchased'] = $purchased_ids && in_array( $result['items'][ $slug ]['ID'], $purchased_ids );
		}

		return $result;
	}

	public function result_to_html( $result ) {

		ob_start();

		foreach ( $result['items'] as $i => $item ) {
			include MAILSTER_DIR . 'views/templates/template.php';
		}

		$html = ob_get_contents();

		ob_end_clean();

		return $html;

	}

	public function get_template_data( $file ) {

		$cache_key = 'get_template_data_' . md5( $file );
		$cached    = mailster_cache_get( $cache_key );
		if ( $cached ) {
			return $cached;
		}

		$basename = false;
		$path     = dirname( $file );
		$slug     = basename( $path );
		if ( ! file_exists( $file ) && is_string( $file ) ) {
			$file_data = $file;
		} elseif ( ! file_exists( $file ) ) {
			return false;
		} else {
			$basename  = basename( $file );
			$fp        = fopen( $file, 'r' );
			$file_data = fread( $fp, 2048 );
			fclose( $fp );
		}

		// no header
		if ( 0 !== strpos( trim( $file_data ), '<!--' ) ) {
			return false;
		}

		$template_data = $this->template_fields;

		foreach ( $this->headers as $field => $regex ) {
			if ( preg_match( '/^[ \t\/*#@]*' . preg_quote( $regex, '/' ) . ':(.*)$/mi', $file_data, $match ) ) {
				$template_data[ $field ] = $match[1];
			}
		}

		$template_data['slug'] = $slug;

		$template_data['index'] = str_replace( MAILSTER_UPLOAD_DIR, MAILSTER_UPLOAD_URI, $path ) . '/index.html';
		$template_data['src']   = str_replace( MAILSTER_UPLOAD_DIR, MAILSTER_UPLOAD_URI, $file );

		if ( empty( $template_data['name'] ) ) {
			$template_data['name'] = ucwords( $slug );
		}

		if ( empty( $template_data['author'] ) ) {
			$template_data['author'] = '';
		}

		if ( preg_match( '#index(-([0-9.]+))?\.html?#', $basename, $hits ) ) {
			$template_data['label'] = esc_html__( 'Base', 'mailster' ) . ( ! empty( $hits[2] )
				? ' ' . $hits[2] : '' );
		}

		if ( preg_match( '#notification(-([0-9.]+))?\.html?#', $basename, $hits ) ) {
			$template_data['label'] = esc_html__( 'Notification', 'mailster' ) . ( ! empty( $hits[2] )
				? ' ' . $hits[2] : '' );
		}

		if ( empty( $template_data['label'] ) ) {
			$template_data['label'] = substr( $basename, 0, strrpos( $basename, '.' ) );
		}
		if ( mailster_option( 'default_template' ) == $slug ) {
			$template_data['is_default'] = true;
		}

		$template_data['update'] = date( 'Y-m-d H:i:s', filemtime( $file ) );
		$template_data['added']  = date( 'Y-m-d H:i:s', filectime( $file ) );

		if ( empty( $template_data['slug'] ) ) {
			$template_data['slug'] = sanitize_key( $template_data['name'] );
		}

		$template_data['label'] = str_replace( ' rtl', ' (RTL)', $template_data['label'] );

		mailster_cache_set( $cache_key, $template_data );
		return $template_data;

	}


	/**
	 *
	 *
	 * @param unknown $errors (optional)
	 */
	public function media_upload_form( $errors = null ) {

		global $type, $tab, $pagenow, $is_IE, $is_opera;

		if ( function_exists( '_device_can_upload' ) && ! _device_can_upload() ) {
			echo '<p>' . esc_html__( 'The web browser on your device cannot be used to upload files. You may be able to use the <a href="http://wordpress.org/extend/mobile/">native app for your device</a> instead.', 'mailster' ) . '</p>';
			return;
		}

		$upload_size_unit = $max_upload_size = wp_max_upload_size();
		$sizes            = array( 'KB', 'MB', 'GB' );

		for ( $u = -1; $upload_size_unit > 1024 && $u < count( $sizes ) - 1; $u++ ) {
			$upload_size_unit /= 1024;
		}

		if ( $u < 0 ) {
			$upload_size_unit = 0;
			$u                = 0;
		} else {
			$upload_size_unit = (int) $upload_size_unit;
		}
		?>

	<div id="media-upload-notice">
		<?php

		if ( isset( $errors['upload_notice'] ) ) {
			echo $errors['upload_notice'];
		}

		?>
		</div>
	<div id="media-upload-error">
		<?php

		if ( isset( $errors['upload_error'] ) && is_wp_error( $errors['upload_error'] ) ) {
			echo $errors['upload_error']->get_error_message();
		}

		?>
		</div>
		<?php
		if ( is_multisite() && ! is_upload_space_available() ) {
			return;
		}

		$post_params       = array(
			'action'   => 'mailster_template_upload_handler',
			'_wpnonce' => wp_create_nonce( 'mailster_nonce' ),
		);
		$upload_action_url = admin_url( 'admin-ajax.php' );

		$plupload_init = array(
			'runtimes'            => 'html5,silverlight,flash,html4',
			'browse_button'       => 'plupload-browse-button',
			'container'           => 'plupload-upload-ui',
			'drop_element'        => 'drag-drop-area',
			'file_data_name'      => 'async-upload',
			'multiple_queues'     => true,
			'max_file_size'       => $max_upload_size . 'b',
			'url'                 => $upload_action_url,
			'flash_swf_url'       => includes_url( 'js/plupload/plupload.flash.swf' ),
			'silverlight_xap_url' => includes_url( 'js/plupload/plupload.silverlight.xap' ),
			'filters'             => array(
				array(
					'title'      => esc_html__( 'Mailster Template ZIP file', 'mailster' ),
					'extensions' => 'zip',
				),
			),
			'multipart'           => true,
			'urlstream_upload'    => true,
			'multipart_params'    => $post_params,
			'multi_selection'     => false,
		);

		?>

	<script type="text/javascript">
	var wpUploaderInit = <?php echo json_encode( $plupload_init ); ?>;
	</script>

	<div id="plupload-upload-ui" class="hide-if-no-js">
	<div id="drag-drop-area">
		<div class="drag-drop-inside">
		<p class="drag-drop-info"><?php esc_html_e( 'Drop your ZIP file here to upload new template', 'mailster' ); ?></p>
		<p><?php echo esc_html_x( 'or', 'Uploader: Drop files here - or - Select Files', 'mailster' ); ?></p>
		<p class="drag-drop-buttons"><input id="plupload-browse-button" type="button" value="<?php esc_attr_e( 'Select File', 'mailster' ); ?>" class="button" /></p>
		<p class="max-upload-size"><?php printf( esc_html__( 'Maximum upload file size: %s.', 'mailster' ), esc_html( $upload_size_unit . $sizes[ $u ] ) ); ?></p>
		<p class="uploadinfo"></p>
		</div>
	</div>
	</div>

	<div id="html-upload-ui" class="hide-if-js">
		<p id="async-upload-wrap">
			<label class="screen-reader-text" for="async-upload"><?php esc_html_e( 'Upload', 'mailster' ); ?></label>
			<input type="file" name="async-upload" id="async-upload" />
			<?php submit_button( esc_html__( 'Upload', 'mailster' ), 'button', 'html-upload', false ); ?>
			<a href="#" onclick="try{top.tb_remove();}catch(e){}; return false;"><?php esc_html_e( 'Cancel', 'mailster' ); ?></a>
		</p>
		<div class="clear"></div>
	</div>

		<?php
		if ( ( $is_IE || $is_opera ) && $max_upload_size > 100 * 1024 * 1024 ) {
			?>
		<span class="big-file-warning"><?php esc_html_e( 'Your browser has some limitations uploading large files with the multi-file uploader. Please use the browser uploader for files over 100MB.', 'mailster' ); ?></span>
			<?php
		}

	}


}
