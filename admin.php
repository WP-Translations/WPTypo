<?php

/**
 * The user interface and activation/deactivation methods for administering
 * the WPTypo plugin
 *
 * @package   wptypo
 * @link      http://wordpress.org/extend/plugins/wptypo/
 * @license   https://raw.githubusercontent.com/fxbenard/WPTypo/master/LICENSE GPL
 * @author    WP-Translations <contact@wp-translations.com>
 * @copyright WP-Translations, 2016
 *
 */
class wptypo_admin extends wptypo
{
	/**
	 * The WP privilege level required to use the admin interface
	 * @var string
	 */
	protected $capability_required;

	/**
	 * Metadata and labels for each element of the plugin's options
	 * @var array
	 */
	protected $fields;

	/**
	 * URI for the forms' action attributes
	 * @var string
	 */
	protected $form_action;

	/**
	 * Name of the page holding the options
	 * @var string
	 */
	protected $page_options;

	/**
	 * Metadata and labels for each settings page section
	 * @var array
	 */
	protected $settings;

	/**
	 * Title for the plugin's settings page
	 * @var string
	 */
	protected $text_settings;


	/**
	 * Sets the object's properties and options
	 *
	 * @return void
	 *
	 * @uses wptypo::initialize()  to set the object's
	 *         properties
	 * @uses wptypo_admin::set_sections()  to populate the
	 *       $sections property
	 * @uses wptypo_admin::set_fields()  to populate the
	 *       $fields property
	 */
	public function __construct() {

		$this->initialize();
		$this->set_sections();
		$this->set_fields();

		// Translation already in WP combined with plugin's name.
		$this->text_settings = sprintf( __( '%s: Settings', 'wptypo' ), self::NAME );

		$this->capability_required = 'manage_options';
		$this->form_action = 'options.php';
		$this->page_options = 'options-general.php';
	}

	/*
     * ===== ACTIVATION & DEACTIVATION CALLBACK METHODS =====
     */

	/**
	 * Establishes the tables and settings when the plugin is activated
	 * @return void
	 */
	public function activate() {

		global $wpdb;

		/*
         * Create or alter the plugin's tables as needed.
         */

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		/*
         * Save this plugin's options to the database.
         */

		update_option( $this->option_name, $this->options );
	}

	/**
	 * Removes the tables and settings when the plugin is deactivated
	 * if the deactivate_deletes_data option is turned on
	 * @return void
	 */
	public function deactivate() {

		global $wpdb;

		$prior_error_setting = $wpdb->show_errors;
		$wpdb->show_errors = false;
		$denied = 'command denied to user';

		$wpdb->show_errors = $prior_error_setting;

		$package_id = self::ID;
		$wpdb->escape_by_ref( $package_id );

		$wpdb->query(
			"DELETE FROM `$wpdb->options`
    		WHERE option_name LIKE '$package_id%'"
		);
	}

	/*
     * ===== ADMIN USER INTERFACE =====
     */

	/**
	 * Sets the metadata and labels for each settings page section
	 *
	 * Settings pages have sections for grouping related fields.  This plugin
	 * uses the $sections property, below, to define those sections.
	 *
	 * The $sections property is a two-dimensional, associative array.  The top
	 * level array is keyed by the section identifier (<sid>) and contains an
	 * array with the following key value pairs:
	 *
	 * + title:  a short phrase for the section's header
	 * + callback:  the method for rendering the section's description.  If a
	 *   description is not needed, set this to "section_blank".  If a
	 *   description is helpful, use "section_<sid>" and create a corresponding
	 *   method named "section_<sid>()".
	 *
	 * @return void
	 * @uses wptypo_admin::$sections  to hold the data
	 */
	protected function set_sections() {

		$this->sections = array(
			'contents' => array(
				'title' => __( 'Contents fixing', 'wptypo' ),
				'callback' => 'section_blank',
				),
			'fixes' => array(
				'title' => __( 'Fix to apply', 'wptypo' ),
				'callback' => 'section_blank',
				),
			'misc' => array(
				'title' => __( 'Miscellaneous Policies', 'wptypo' ),
				'callback' => 'section_blank',
				),
			);
	}

	/**
	 * Sets the metadata and labels for each element of the plugin's
	 * options
	 *
	 * The $fields property is a two-dimensional, associative array.  The top
	 * level array is keyed by the field's identifier and contains an array
	 * with the following key value pairs:
	 *
	 * + section:  the section identifier (<sid>) for the section this
	 *   setting should be displayed in
	 * + label:  a very short title for the setting
	 * + text:  the long description about what the setting does.  Note:
	 *   a description of the default value is automatically appended.
	 * + type:  the data type ("int", "string", or "bool").  If type is "bool,"
	 *   the following two elements are also required:
	 * + bool0:  description for the button indicating the option is off
	 * + bool1:  description for the button indicating the option is on
	 *
	 * WARNING:  Make sure to keep this propety and the
	 * wptypo_admin::$options_default
	 * property in sync.
	 *
	 * @return void
	 * @uses wptypo_admin::$fields  to hold the data
	 */
	protected function set_fields() {

		$this->fields = array(
			'deactivate_deletes_data' => array(
				'section' => 'misc',
				'label' => __( 'Deactivation', 'wptypo' ),
				'text' => __( "Remove all of the plugin's data and settings at desactivation", 'wptypo' ),
				'type' => 'bool',
				),
			'debug_in_console' => array(
				'section' => 'misc',
				'label' => __( 'Debugging', 'wptypo' ),
				'text' => __( 'Log information into the browser console (display Developper Tools to show the logs)', 'wptypo' ),
				'type' => 'bool',
				),
			'is_enable_title_fix' => array(
				'section' => 'contents',
				'label' => __( 'Titles', 'wptypo' ),
				'text' => __( "Fix all the posts' titles", 'wptypo' ),
				'type' => 'bool',
				),
			'is_enable_content_fix' => array(
				'section' => 'contents',
				'label' => __( 'Contents', 'wptypo' ),
				'text' => __( "Fix all the posts' contents", 'wptypo' ),
				'type' => 'bool',
				),
			'is_enable_excerpt_fix' => array(
				'section' => 'contents',
				'label' => __( 'Excerpts', 'wptypo' ),
				'text' => __( "Fix all the posts' excerpts", 'wptypo' ),
				'type' => 'bool',
				),
			'is_enable_comment_fix' => array(
				'section' => 'contents',
				'label' => __( 'Comments', 'wptypo' ),
				'text' => __( 'Fix all the comments (content only)', 'wptypo' ),
				'type' => 'bool',
				),
			'is_enable_meta_fix' => array(
				'section' => 'contents',
				'label' => __( 'Meta Datas (Beta)', 'wptypo' ),
				'text' => __( "Fix all the posts' meta datas", 'wptypo' ),
				'type' => 'bool',
				),
			'fix_ellipsis' => array(
				'section' => 'fixes',
				'label' => __( 'Ellipsis', 'wptypo' ),
				'text' => __( 'Replace all occurences of three dots by an ellipsis', 'wptypo' ),
				'type' => 'bool',
				),
			'fix_dimension' => array(
				'section' => 'fixes',
				'label' => __( 'Dimension', 'wptypo' ),
				'text' => __( 'Detect the letter x between number and replace it by the real math symbol', 'wptypo' ),
				'type' => 'bool',
				),
			'fix_dash' => array(
				'section' => 'fixes',
				'label' => __( 'Dashes', 'wptypo' ),
				'text' => __( "Replace '-' by ndash '–' (dates ranges) or double-dash -- by a mdash —", 'wptypo' ),
				'type' => 'bool',
				),
			'fix_smart_quotes' => array(
				'section' => 'fixes',
				'label' => __( 'French quotes', 'wptypo' ),
				'text' => __( 'Replace dumb quotes " " by smart French style quotation marks « » and use a no break space', 'wptypo' ),
				'type' => 'bool',
				),
			'fix_french_no_breakspace' => array(
				'section' => 'fixes',
				'label' => __( 'French No Break Space', 'wptypo' ),
				'text' => __( 'Replace some classic spaces by non breaking spaces following the French typographic code', 'wptypo' ),
				'type' => 'bool',
				),
			'fix_curly_quote' => array(
				'section' => 'fixes',
				'label' => __( 'CurlyQuote', 'wptypo' ),
				'text' => __( "Replace straight quotes ' by curly one's ’", 'wptypo' ),
				'type' => 'bool',
				),
			'fix_hyphen' => array(
				'section' => 'fixes',
				'label' => __( 'Automatic hyphenation', 'wptypo' ),
				'text' => __( 'Enable word hyphenation', 'wptypo' ),
				'type' => 'bool',
				),
			'fix_trademark' => array(
				'section' => 'fixes',
				'label' => __( 'Contents', 'wptypo' ),
				'text' => __( 'Replace (r), (c) and (TM) by the right symbols', 'wptypo' ),
				'type' => 'bool',
				),
			);
	}

	/**
	 * A filter to add a "Settings" link in this plugin's description
	 *
	 * NOTE: This method is automatically called by WordPress for each
	 * plugin being displayed on WordPress' Plugins admin page.
	 *
	 * @param array $links the links generated thus far
	 *
	 * @return array
	 */
	public function plugin_action_links( $links ) {

		// Translation already in WP.
		$links[] = '<a href="' . $this->hsc_utf8( $this->page_options )
		. '?page=' . self::ID . '">'
		. $this->hsc_utf8( __( 'Settings' ) ) . '</a>';

		return $links;
	}

	/**
	 * Declares a menu item and callback for this plugin's settings page
	 *
	 * NOTE: This method is automatically called by WordPress when
	 * any admin page is rendered
	 */
	public function admin_menu() {

		add_submenu_page(
			$this->page_options,
			$this->text_settings,
			self::NAME,
			$this->capability_required,
			self::ID,
			array( &$this, 'page_settings' )
		);
	}

	/**
	 * Declares the callbacks for rendering and validating this plugin's
	 * settings sections and fields
	 *
	 * NOTE: This method is automatically called by WordPress when
	 * any admin page is rendered
	 */
	public function admin_init() {

		register_setting(
			$this->option_name,
			$this->option_name,
			array( &$this, 'validate' )
		);

		// Dynamically declares each section using the info in $sections.
		foreach ( $this->sections as $id => $section ) {
			add_settings_section(
				self::ID . '-' . $id,
				$this->hsc_utf8( $section['title'] ),
				array( &$this, $section['callback'] ),
				self::ID
			);
		}

		// Dynamically declares each field using the info in $fields.
		foreach ( $this->fields as $id => $field ) {
			add_settings_field(
				$id,
				$this->hsc_utf8( $field['label'] ),
				array( &$this, $id ),
				self::ID,
				self::ID . '-' . $field['section']
			);
		}
	}

	/**
	 * The callback for rendering the settings page
	 * @return void
	 */
	public function page_settings() {

		echo '<div class="wrap">';
		echo '<h2>' . $this->hsc_utf8( $this->text_settings ) . '</h2>';
		echo '<form action="' . $this->hsc_utf8( $this->form_action ) . '" method="post">' . "\n";
		settings_fields( $this->option_name );
		do_settings_sections( self::ID );
		submit_button();
		echo '</form></div>';

	}

	/**
	 * The callback for "rendering" the sections that don't have descriptions
	 * @return void
	 */
	public function section_blank() {

	}

	/**
	 * The callback for rendering the "Login Policies" section description
	 * @return void
	 */
	public function section_login() {

		echo '<p>';
		echo $this->hsc_utf8( __( 'An explanation of this section...', 'wptypo' ) );
		echo '</p>';
	}

	/**
	 * The callback for rendering the fields
	 *
	 * @param $name
	 * @param $params
	 *
	 * @return void
	 *
	 * @uses wptypo_admin::input_int()  for rendering
	 *       text input boxes for numbers
	 * @uses wptypo_admin::input_radio()  for rendering
	 *       radio buttons
	 * @uses wptypo_admin::input_string()  for rendering
	 *       text input boxes for strings
	 */
	public function __call( $name, $params ) {

		if ( empty( $this->fields[ $name ]['type'] ) ) {
			return;
		}
		switch ( $this->fields[ $name ]['type'] ) {
			case 'bool':
				$this->input_checkbox( $name );
			break;
			case 'choice':
				$this->input_radio( $name );
			break;
			case 'int':
				$this->input_int( $name );
			break;
			case 'string':
				$this->input_string( $name );
			break;
		}
	}

	/**
	 * Renders the radio button inputs
	 *
	 * @param $name
	 *
	 * @return void
	 */
	protected function input_radio( $name ) {

		echo '<fieldset><legend class="screen-reader-text"><span>' . $this->hsc_utf8( $this->fields[ $name ]['label'] ) . '</span></legend>';
		echo '<p class="description">' . $this->hsc_utf8( $this->fields[ $name ]['text'] ) . '</p>';
		echo '<label title="0"><input type="radio" value="0" name="'
		. $this->hsc_utf8( $this->option_name )
		. '[' . $this->hsc_utf8( $name ) . ']"'
		. ($this->options[ $name ] ? '' : ' checked="checked"') . ' /> ';
		echo '<span>' . $this->hsc_utf8( $this->fields[ $name ]['bool0'] ) . '</span></label>';
		echo '<br>';
		echo '<label title="1"><input type="radio" value="1" name="'
		. $this->hsc_utf8( $this->option_name )
		. '[' . $this->hsc_utf8( $name ) . ']"'
		. ($this->options[ $name ] ? ' checked="checked"' : '') . ' /> ';
		echo '<span>' . $this->hsc_utf8( $this->fields[ $name ]['bool1'] ) . '</span></label>';
		echo '</fieldset>';
	}

	/**
	 * Renders the checkbox inputs
	 *
	 * @param $name
	 *
	 * @return void
	 */
	protected function input_checkbox( $name ) {

		$html = '<input type="checkbox" id="' . $name . '" name="'
		. $this->hsc_utf8( $this->option_name )
		. '[' . $this->hsc_utf8( $name ) . ']" value=1' . checked( 1, $this->options[ $name ], false ) . '/>';
		$html .= '<label for="' . $this->hsc_utf8( $name ) . '">' . $this->hsc_utf8( $this->fields[ $name ]['text'] ) . '</label>';

		echo $html;
	}

	/**
	 * Renders the text input boxes for editing integers
	 *
	 * @param $name
	 *
	 * @return void
	 */
	protected function input_int( $name ) {

		echo '<input type="text" size="3" name="'
		. $this->hsc_utf8( $this->option_name )
		. '[' . $this->hsc_utf8( $name ) . ']"'
		. ' value="' . $this->hsc_utf8( $this->options[ $name ] ) . '" /> ';
		echo $this->hsc_utf8(
			$this->fields[ $name ]['text']
			. ' ' . __( 'Default:', 'wptypo' ) . ' '
			. $this->options_default[ $name ] . '.'
		);
	}

	/**
	 * Renders the text input boxes for editing strings
	 *
	 * @param $name
	 *
	 * @return void
	 */
	protected function input_string( $name ) {

		echo '<input type="text" size="75" name="'
		. $this->hsc_utf8( $this->option_name )
		. '[' . $this->hsc_utf8( $name ) . ']"'
		. ' value="' . $this->hsc_utf8( $this->options[ $name ] ) . '" /> ';
		echo '<br />';
		echo $this->hsc_utf8(
			$this->fields[ $name ]['text']
			. ' ' . __( 'Default:', 'wptypo' ) . ' '
			. $this->options_default[ $name ] . '.'
		);
	}

	/**
	 * Validates the user input
	 *
	 * NOTE: WordPress saves the data even if this method says there are
	 * errors.  So this method sets any inappropriate data to the default
	 * values.
	 *
	 * @param array $in the input submitted by the form
	 *
	 * @return array  the sanitized data to be saved
	 */
	public function validate( $in ) {

		if ( ! is_array( $in ) ) {
			add_settings_error(
				$this->option_name,
				$this->hsc_utf8( $this->option_name ),
				__( 'You need to activate something if you want to use the plugin!', 'wptypo' )
			);

			return $out;
		}

		$gt_format = __( "must be >= '%s',", 'wptypo' );
		$default = __( 'so we used the default value instead.', 'wptypo' );

		// Dynamically validate each field using the info in $fields.
		foreach ( $this->fields as $name => $field ) {
			if ( ! array_key_exists( $name, $in ) ) {
				if ( $field['type'] == 'bool' ) {
					$out[ $name ] = 0;
				}
				continue;
			}

			if ( ! is_scalar( $in[ $name ] ) ) {
				// Not translating this since only hackers will see it.
				add_settings_error(
					$this->option_name,
					$this->hsc_utf8( $name ),
					$this->hsc_utf8( "'" . $field['label'] )
					. "' was not a scalar, $default"
				);
				continue;
			}

			switch ( $field['type'] ) {
				case 'bool':
					if ( isset( $in[ $name ] ) && 1 == $in[ $name ] ) {
						$out[ $name ] = 1;
					} else {
						$out[ $name ] = 0;
					}
				continue 2;
				break;
				case 'int':
					if ( ! ctype_digit( $in[ $name ] ) ) {
						add_settings_error(
							$this->option_name,
							$this->hsc_utf8( $name ),
							$this->hsc_utf8(
								"'" . $field['label'] . "' "
								. __( 'must be an integer,', 'wptypo' )
								. ' ' . $default
							)
						);
						continue 2;
					}
					if ( array_key_exists( 'greater_than', $field ) && $in[ $name ] < $field['greater_than'] ) {
						add_settings_error(
							$this->option_name,
							$this->hsc_utf8( $name ),
							$this->hsc_utf8(
								"'" . $field['label'] . "' "
								. sprintf( $gt_format, $field['greater_than'] )
								. ' ' . $default
							)
						);
						continue 2;
					}
				break;
			}
			$out[ $name ] = $in[ $name ];
		}

		unset( $in );
		return $out;
	}
}
