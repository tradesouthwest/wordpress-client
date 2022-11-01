<?php
/**
 * Plugin Name: Business Directory File Attachments
 * Plugin URI: https://businessdirectoryplugin.com
 * Version: 5.1.3
 * Author: Business Directory Team
 * Description: Adds ability for you to upload files and attach them to listings.
 * Author URI: https://businessdirectoryplugin.com
 * Text Domain: wpbdp-attachments
 * Domain Path: /translations/
 *
 * @package Premium Modules/Attachments
 */

/**
 * Class WPBDP_ListingAttachmentsModule
 */
class WPBDP_ListingAttachmentsModule {

    public function __construct() {
        $this->id                  = 'attachments';
        $this->file                = __FILE__;
        $this->title               = 'File Attachments Module';
        $this->required_bd_version = '5.0';
 
        $this->version = '5.1.3';
    }

    /**
     * @since 5.0.7
     */
    public function get_version() {
        return $this->version;
    }

    public function init() {
        add_action( 'wpbdp_modules_init', array( $this, '_init' ) );
        add_action( 'wpbdp_submit_listing_enqueue_resources', array( $this, 'enqueue_scripts' ) );

        add_filter( 'wpbdp_listing_form_attachments_config', array( $this, 'fee_attachments_config' ), 0, 2 );
        add_filter( 'wpbdp_export_listing_objects', array( $this, 'export_attachments_objects'), 10, 3);

        add_action( 'wp_ajax_wpbdp_attachments_delete', array( $this, 'ajax_delete_attachment' ) );
        add_action( 'wp_ajax_nopriv_wpbdp_attachments_delete', array( $this, 'ajax_delete_attachment' ) );

        require_once plugin_dir_path( __FILE__ ) . 'admin.php';

        $this->admin = new WPBDP_ListingAttachmentsModule_Admin( $this );
    }

    public function enqueue_scripts() {
        wp_enqueue_script(
            'wpbdp-attachments',
            plugins_url( '/resources/attachments.min.js', __FILE__ ),
            array( 'jquery' ),
            $this->version
        );

        wp_enqueue_style(
            'wpbdp-attachments',
            plugins_url( '/resources/styles.min.css', __FILE__ ),
            array(),
            $this->version
        );
    }

    public function _init() {
        wpbdp_register_settings_group( 'listing-attachments', __( 'Attachments', 'wpbdp-attachments' ), 'modules' );
        wpbdp_register_setting(
            array(
				'id'      => 'attachments-enabled',
				'name'    => __( 'Enable listing attachments', 'wpbdp-attachments' ),
				'type'    => 'checkbox',
				'default' => true,
				'group'   => 'listing-attachments',
            )
        );
        wpbdp_register_setting(
            array(
				'id'      => 'attachments-header',
				'name'    => __( 'Attachments Header Text', 'wpbdp-attachments' ),
				'type'    => 'text',
				'default' => __( 'Listing Attachments', 'wpbdp-attachments' ),
				'desc'    => __( 'Customize the header text that appears during the submit and on listings.', 'wpbdp-attachments' ),
				'group'   => 'listing-attachments',
            )
        );
        wpbdp_register_setting(
            array(
				'id'      => 'attachments-text',
				'name'    => __( 'Use attachment description for link text?', 'wpbdp-attachments' ),
				'type'    => 'checkbox',
				'default' => false,
				'group'   => 'listing-attachments',
            )
        );
        wpbdp_register_setting(
            array(
				'id'      => 'attachments-icons',
				'name'    => __( 'Enable icons for attachments?', 'wpbdp-attachments' ),
				'type'    => 'checkbox',
				'default' => true,
				'group'   => 'listing-attachments',
            )
        );
        wpbdp_register_setting(
            array(
				'id'      => 'attachments-count',
				'name'    => __( 'Maximum number of attachments per listing', 'wpbdp-attachments' ),
				'type'    => 'number',
				'default' => '5',
				'group'   => 'listing-attachments',
            )
        );
        wpbdp_register_setting(
            array(
				'id'      => 'attachments-maxsize',
				'name'    => __( 'Maximum attachment size (in KB)', 'wpbdp-attachments' ),
				'type'    => 'text',
				'default' => '5000',
				'group'   => 'listing-attachments',
            )
        );
        wpbdp_register_setting(
            array(
				'id'      => 'attachments-allowed-types',
				'name'    => __( 'Allowed File Types', 'wpbdp-attachments' ),
				'type'    => 'multicheck',
				'default' => array( 'pdf' ),
				'options' => array(
					'pdf' => 'PDF',
					'png' => 'PNG',
					'jpg' => 'JPG',
					'gif' => 'GIF',
					'rtf' => 'RTF',
					'txt' => 'TXT',
                    'mp4' => 'MPEG-4',
                    'mp3' => 'MP3',
				),
				'group'   => 'listing-attachments',
            )
        );

    	if ( ! wpbdp_get_option( 'attachments-enabled' ) ) {
            return;
        }

        add_filter( 'wpbdp_submit_sections', array( $this, 'add_submit_section' ), 10 );
        add_filter( 'wpbdp_submit_section_attachments', array( $this, 'dispatch_submit_section' ), 10, 2 );

        add_filter( 'wpbdp_template_variables__single', array( &$this, 'single_attachments' ) );
    }

	/**
	 * @param string $q
	 *
	 * @return array
	 */
    public function get_supported_extensions( $q = 'ext' ) {
        static $ext_to_mime = array(
            'pdf' => array( 'application/pdf', 'application/x-pdf', 'application/vnd.pdf' ),
            'png' => array( 'image/png' ),
            'jpg' => array( 'image/jpg', 'image/jpeg', 'image/pjpeg' ),
            'gif' => array( 'image/gif' ),
            'rtf' => array( 'application/rtf', 'application/x-rtf', 'text/richtext', 'text/rtf' ),
            'txt' => array( 'text/plain' ),
            'mp4' => array( 'video/mp4' ),
            'mp3' => array( 'video/mp3' ),
        );

        if ( 'ext' === $q ) {
            return array_keys( $ext_to_mime );
        }

        return $ext_to_mime;
    }

	/**
	 * Get the file types that are turned on.
	 *
	 * @since 5.1.3
	 *
	 * @return array
	 */
	public function get_allowed_extensions() {
		$ext_to_mime = $this->get_supported_extensions( 'ext=>mime' );
		$mimetypes   = array();

		foreach ( (array) wpbdp_get_option( 'attachments-allowed-types' ) as $ext ) {
			if ( isset( $ext_to_mime[ $ext ] ) ) {
				$mimetypes[ $ext ] = $ext_to_mime[ $ext ];
			}
		}

		return $mimetypes;
	}

	/**
	 * @return array
	 */
    public function get_attachments_config() {
		$allowed_mimes = $this->get_allowed_extensions();

        $mimetypes  = array();
        $extensions = array();

		foreach ( $allowed_mimes as $ext => $type ) {
			$mimetypes    = array_merge( $mimetypes, $type );
            $extensions[] = $ext;
        }

        $config = array(
            'enabled'    => wpbdp_get_option( 'attachments-enabled' ),
            'limit'      => max( 0, intval( wpbdp_get_option( 'attachments-count' ) ) ),
            'mimetypes'  => $mimetypes,
            'extensions' => $extensions,
            'filesize'   => intval( wpbdp_get_option( 'attachments-maxsize' ) ) * 1024,
        );

        return $config;
    }

	/**
	 * @param array              $config
	 * @param WPBDP_Listing|null $listing
	 */
    public function fee_attachments_config( $config, $listing ) {
        if ( class_exists( 'WPBDP_FeaturedLevelsModule' ) ) {
            return $config;
        }

		if ( ! $listing ) {
            return $config;
        }

        $fee_info = $listing->get_fee_plan();

        if ( ! $fee_info || ! $fee_info->fee ) {
            return $config;
        }

        $fee = $fee_info->fee;

        if ( isset( $fee->extra_data['attachments']['mode'] ) && 'custom' === $fee->extra_data['attachments']['mode'] ) {
            $config['limit']    = wpbdp_getv( $fee->extra_data['attachments'], 'count', $config['limit'] );
            $config['filesize'] = wpbdp_getv( $fee->extra_data['attachments'], 'maxsize', $config['filesize'] / 1024 ) * 1024;
        }

        return $config;
    }

    /**
	 * @param array $sections
	 *
     * @since 5.0
     */
    public function add_submit_section( $sections ) {
        $title = wpbdp_get_option( 'attachments-header' );
        $sections['attachments'] = array(
            'title' => ! empty( $title ) ? $title : _x( 'Listing Attachments', 'header-text', 'wpbdp-attachments' ),
            'html'  => '',
        );

        return $sections;
    }

    /**
	 * @param array                        $section
	 * @param WPBDP__Views__Submit_Listing $submit
	 *
     * @since 5.0
     */
    public function dispatch_submit_section( $section, $submit ) {
        $config = (array) apply_filters(
            'wpbdp_listing_form_attachments_config',
            $this->get_attachments_config(),
            $submit->get_listing()
        );

        if ( ! $config['enabled'] || 0 === $config['limit'] ) {
            $section['flags'][] = 'disabled';
            $section['flags'][] = 'hidden';

            return $section;
        }

        $attachments = WPBDP_ListingAttachmentsModule::get_attachments( $submit->get_listing()->get_id() );

        // Handle upload.
        if ( ! empty( $_FILES['upload_file'] ) && ! empty( $_POST['attachment-upload'] ) && 0 === $_FILES['upload_file']['error'] ) {
            $submit->prevent_save();

            if ( $config['limit'] > count( $attachments ) ) {
                $upload      = array(
					'file'        => $_FILES['upload_file'],
					'description' => trim( $_POST['upload_description'] ),
				);
                $constraints = array(
					'max-size'  => $config['filesize'],
					'mimetypes' => $config['mimetypes'],
				);
				$upload_err = '';
                if ( $uploaded = wpbdp_media_upload( $upload['file'], false, false, $constraints, $upload_err ) ) {
                    $upload['file_'] = _wp_relative_upload_path( $uploaded['file'] );
                    $upload['key']   = md5( $upload['file_'] . '/' . time() );

                    $attachments[ $upload['key'] ] = $upload;

                    update_post_meta( $submit->get_listing()->get_id(), '_wpbdp[attachments]', $attachments );

                    // These do not need to be saved to the meta.
                    $attachments[ $upload['key'] ]['path'] = $uploaded['file'];
                    $attachments[ $upload['key'] ]['url']  = $uploaded['url'];

                    $submit->messages( sprintf( __( 'File "%s" was uploaded successfully.', 'wpbdp-attachments' ), esc_attr( basename( $upload['file_'] ) ) ), 'notice', 'attachments' );
                } else {
                    $submit->messages( sprintf( __( 'An error was found while uploading your file: %s.', 'wpbdp-attachments' ), $upload_err ), 'error', 'attachments' );
                }
            } else {
                $submit->messages( __( 'You have reached your attachment limit. You can remove existing attachments to make space for new ones.', 'wpbdp-attachments' ), 'error', 'attachments' );
                return $section;
            }
        }

        $html = wpbdp_render_page(
            plugin_dir_path( __FILE__ ) . 'templates/listing-attachments.tpl.php',
            array(
				'config'      => $config,
				'listing'     => $submit->get_listing(),
				'attachments' => $attachments,
            )
        );

        $section['html'] = $html;

        return $section;
    }

    /**
     * @since 5.0
     */
    public function ajax_delete_attachment() {
        $listing_id = absint( $_REQUEST['listing_id'] );
        $key        = trim( $_REQUEST['key'] );
        $nonce      = trim( $_REQUEST['nonce'] );

        $res = new WPBDP_AJAX_Response();

        if ( ! wp_verify_nonce( $nonce, 'delete attachment ' . $key . ' from listing ' . $listing_id ) ) {
            return $res->send_error( __( 'Could not delete attachment.', 'wpbdp-attachments' ) );
        }

        $attachments = WPBDP_ListingAttachmentsModule::get_attachments( $listing_id );

        if ( isset( $attachments[ $key ] ) ) {
            @unlink( $attachments[ $key ]['path'] );
            unset( $attachments[ $key ] );

            update_post_meta( $listing_id, '_wpbdp[attachments]', $attachments );
        }

        return $res->send();
    }

	/**
	 * @param array $vars
	 */
    function single_attachments( $vars ) {
        $config = (array) apply_filters( 'wpbdp_listing_attachments_config', $this->get_attachments_config(), $vars['listing_id'] );

        if ( ! $config['enabled'] ) {
            $attachments = array();
        } else {
			$attachments = self::get_attachments( $vars['listing_id'] );
        }

        $this->maybe_add_attachments_icons( $attachments );

        $vars['listing_attachments'] = $attachments;

        if ( $attachments ) {
            $vars['#attachments'] = array(
				'position' => 'after',
				'weight'   => 0,
				'value'    => $this->display_attachments( '', $vars['listing_id'] ),
			);
        }

        return $vars;
    }

    /**
     * Displays attachments on a listing's single view.
     * Callback for `wpbdp_single_listing_fields` filter.
	 *
	 * @param string $html
	 * @param int    $listing_id
     */
    public function display_attachments( $html, $listing_id ) {
        $config      = (array) apply_filters( 'wpbdp_listing_attachments_config', $this->get_attachments_config(), $listing_id );
        $attachments = self::get_attachments( $listing_id );

        if ( ! $config['enabled'] || ! $attachments ) {
            return $html;
        }

        $this->maybe_add_attachments_icons( $attachments );

        $html .= wpbdp_render_page(
            plugin_dir_path( __FILE__ ) . 'templates/attachments-display.tpl.php',
            array( 'attachments' => $attachments )
        );

        return $html;
    }

	/**
	 * @param array $vars
	 * @param int   $listing_id
	 */
    function include_attachments_in_template( $vars, $listing_id ) {
        $config = (array) apply_filters( 'wpbdp_listing_attachments_config', $this->get_attachments_config(), $listing_id );

        if ( ! $config['enabled'] ) {
            $attachments = array();
        } else {
			$attachments = self::get_attachments( $listing_id );
        }

        $vars['attachments'] = (object) array(
			'html' => $this->display_attachments( '', $listing_id ),
			'data' => $this->maybe_add_attachments_icons( $attachments ),
		);
        return $vars;
    }

    /**
     * Adds icon URLs for all attachments (if the setting is enabled).
     *
	 * @param array $attachments
	 *
     * @since 3.5.1
     */
    private function maybe_add_attachments_icons( &$attachments ) {
        if ( ! $attachments || ! wpbdp_get_option( 'attachments-icons' ) ) {
            return $attachments;
        }

        static $ICONS = array(
            'default'           => 'appbar.page.png',
            'application/pdf'   => 'appbar.page.pdf.png',
            'application/rtf'   => 'appbar.book.open.text.image.png',
            'application/x-rtf' => 'appbar.book.open.text.image.png',
            'text/richtext'     => 'appbar.book.open.text.image.png',
            'text/rtf'          => 'appbar.book.open.text.image.png',
            'text/plain'        => 'appbar.page.text.png',
            'image/png'         => 'appbar.page.image.png',
            'image/jpg'         => 'appbar.page.image.png',
            'image/jpeg'        => 'appbar.page.image.png',
            'image/pjpeg'       => 'appbar.page.image.png',
            'image/gif'         => 'appbar.page.image.png',
        );

        foreach ( $attachments as &$attachment ) {
            $mimetype = wpbdp_get_mimetype( $attachment['path'] );

            if ( isset( $ICONS[ $mimetype ] ) ) {
                $attachment['icon'] = plugin_dir_url( __FILE__ ) . 'resources/icons/' . $ICONS[ $mimetype ];
            } else {
                $attachment['icon'] = plugin_dir_url( __FILE__ ) . 'resources/icons/' . $ICONS['default'];
            }
        }

        return $attachments;
    }

	/**
	 * @param array $attachments
	 */
    private static function maybe_fix_attachments_array( &$attachments ) {
        $upload_dir = wp_upload_dir( null, false, false );

        $changed = false;

        foreach ( $attachments as &$attachment ) {
            if ( isset( $attachment['file_'] ) && file_exists( path_join( $upload_dir['basedir'], $attachment['file_'] ) ) ) {
                continue;
            }

            if ( ! empty( $attachment['path'] ) ) {
                $attachment['file_'] = _wp_relative_upload_path( $attachment['path'] );
            } elseif ( ! empty( $attachment['file']['path'] ) ) {
                $attachment['file_'] = _wp_relative_upload_path( $attachment['file']['path'] );
            } elseif ( ! empty( $attachment['file'] ) && is_string( $attachment['file'] ) ) {
                $attachment['file_'] = _wp_relative_upload_path( $attachment['file'] );
            }

            if ( ! $attachment['file_'] ) {
                continue;
            }

            unset( $attachment['path'] );
            unset( $attachment['url'] );

            $changed = true;
        }

        return $changed;
    }

    /**
     * Obtains the list of attachments for a given listing.
     *
     * @param int $listing_id the listing ID
     * @return array list of attachment items (associative array with keys: )
     */
    public static function get_attachments( $listing_id ) {
        $attachments = get_post_meta( $listing_id, '_wpbdp[attachments]', true );

        if ( ! $attachments ) {
            return array();
        }

        if ( self::maybe_fix_attachments_array( $attachments ) ) {
            update_post_meta( $listing_id, '_wpbdp[attachments]', $attachments );
        }

        $upload_dir = wp_upload_dir();

        foreach ( $attachments as &$a ) {
            $a['path'] = trailingslashit( $upload_dir['basedir'] ) . $a['file_'];
            $a['url']  = trailingslashit( $upload_dir['baseurl'] ) . $a['file_'];
        }

        return $attachments;
    }

    /**
     * @param array               $listing_items
     * @param int                 $listing_id
     * @param WPBDP_DataFormatter $data_formatter
     * @return array
     *
     * @since 5.5
     */
    public function export_attachments_objects( $listing_items, $listing_id, $data_formatter ) {
        $attachments = WPBDP_ListingAttachmentsModule::get_attachments( $listing_id );

        if ( empty( $attachments ) ) {
            return $listing_items;
        }

        $attachments_items = array(
            'URL' => __( 'Attachment URL', 'wpbdp-attachments' ),
        );

        foreach ( $attachments as $attachment ) {
            $listing_items = array_merge(
                $listing_items,
                $data_formatter->format_data(
                    $attachments_items,
                    array( 'URL' => $attachment['url'] )
                )
            );
        }

        return $listing_items;
    }

}

final class WPBDP__Attachments {
	/**
	 * @param WPBDP__Modules $modules
	 */
    public static function load( $modules ) {
        $instance = new WPBDP_ListingAttachmentsModule();
        $modules->load( $instance );
    }
}

add_action( 'wpbdp_load_modules', array( 'WPBDP__Attachments', 'load' ) );
