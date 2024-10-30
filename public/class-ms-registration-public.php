<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    MS_Registration
 * @subpackage MS_Registration/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    MS_Registration
 * @subpackage MS_Registration/public
 * @author     Your Name <email@example.com>
 */
class MS_Registration_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ms-registration-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ms-registration-public.js', array( 'jquery' ), $this->version, false );

	}

    public function ms_registration_form() {
	    $registration = get_option('ms_registration_form', array());

		if (is_array($registration)) {
			foreach($registration as $rego) {
				${$rego['name']} = !empty($_POST[$rego['name']]) ? trim($_POST[$rego['name']]) : $rego['default_value'];
				?>
				<p>
					<label for="<?php echo esc_attr($rego['name']) ?>"><?php esc_html_e($rego['label'], 'ms-registration') ?><br/>
						<input type="<?php echo esc_attr($rego['type']) ?>"
							   id="<?php echo esc_attr($rego['name']) ?>"
							   name="<?php echo esc_attr($rego['name']) ?>"
							   placeholder="<?php echo esc_attr($rego['placeholder']) ?>"
							   value="<?php echo esc_attr(${$rego['name']}) ?>"
							   class="input"
						/>
					</label>
				</p>
				<?php
			}
		}
    }

    public function ms_registration_errors($errors, $sanitized_user_login, $user_email) {
        $registration = get_option('ms_registration_form', array());

		if (is_array($registration)) {
			foreach($registration as $rego) {
				if ($rego['type'] == 'tel') {
					if (!empty($_POST[$rego['name']])) {
						if (strlen($_POST[$rego['name']]) < 6)
							$errors->add($rego['name'] . '_error', __('<strong>ERROR</strong>: Enter a valid phone number.', 'ms-registration'));
					} else {
						if ($rego['required'])
							$errors->add($rego['name'] . '_error', __('<strong>ERROR</strong>: ' . $rego['label'] . ' is required', 'ms-registration'));
					}
				}

				if ($rego['type'] == 'url') {
					if (!empty($_POST[$rego['name']])) {
						if (!filter_var($_POST[$rego['name']], FILTER_VALIDATE_URL))
							$errors->add($rego['name'] . '_error', __('<strong>ERROR</strong>: Enter a valid URL.', 'ms-registration'));
					} else {
						if ($rego['required'])
							$errors->add($rego['name'] . '_error', __('<strong>ERROR</strong>: ' . $rego['label'] . ' is required', 'ms-registration'));
					}
				}

				if ($rego['type'] == 'email') {
					if (!empty($_POST[$rego['name']])) {
						if (filter_var($_POST[$rego['name']], FILTER_VALIDATE_EMAIL))
							$errors->add($rego['name'] . '_error', __('<strong>ERROR</strong>: Enter a valid Email.', 'ms-registration'));
					} else {
						if ($rego['required'])
							$errors->add($rego['name'] . '_error', __('<strong>ERROR</strong>: ' . $rego['label'] . ' is required', 'ms-registration'));
					}
				}

				if ($rego['type'] == 'password') {
					if (!empty($_POST[$rego['name']])) {
						if (strlen($_POST[$rego['name']]) < $rego['min_length'])
							$errors->add($rego['name'] . '_error', __('<strong>ERROR</strong>: Password must be at least ' . $rego['min_length'] . ' characters long.', 'ms-registration'));

						if ($rego['numbers']) {
							if (!preg_match('/\d/', $_POST[$rego['name']]))
								$errors->add($rego['name'] . '_error', __('<strong>ERROR</strong>: Password must contain a combination of letters and numbers', 'ms-registration'));
						}

						if ($rego['symbols']) {
							if (!preg_match('/[^a-zA-Z\d]/', $_POST[$rego['name']]))
								$errors->add($rego['name'] . '_error', __('<strong>ERROR</strong>: Password must contain a combination of letters and symbols', 'ms-registration'));
						}
					} else {
						$errors->add($rego['name'] . '_error', __('<strong>ERROR</strong>: Password is required', 'ms-registration'));
					}
				}

				if ($rego['type'] == 'text') {
					if (!empty($_POST[$rego['name']])) {
						if (strlen($_POST[$rego['name']]) < $rego['min_length'])
							$errors->add($rego['name'] . '_error', __('<strong>ERROR</strong>: ' . $rego['label'] . ' must be at least ' . $rego['min_length'] . ' characters long.', 'ms-registration'));

						if (strlen($_POST[$rego['name']]) > $rego['max_length'])
							$errors->add($rego['name'] . '_error', __('<strong>ERROR</strong>: ' . $rego['label'] . ' cannot be mora than ' . $rego['max_length'] . ' characters long.', 'ms-registration'));
					} else {
						if ($rego['required'])
							$errors->add($rego['name'] . '_error', __('<strong>ERROR</strong>: ' . $rego['label'] . ' is required', 'ms-registration'));
					}
				}
			}
		}

        return $errors;
    }

    public function ms_user_register($user_id) {
        $registration = get_option('ms_registration_form', array());

		if (is_array($registration)) {
			foreach($registration as $rego) {
				if ($rego['type'] == 'password')
					wp_set_password($_POST[$rego['name']], $user_id);
				elseif ($rego['type'] == 'text') {
					$value = $_POST[$rego['name']];

					if ($rego['cap_first'])
						$value = ucfirst(strtolower($value));

					if ($rego['uppercase'])
						$value = strtoupper($value);

					if ($rego['lowercase'])
						$value = strtolower($value);

					update_user_meta($user_id, $rego['name'], $value);
				} elseif (!empty($_POST[$rego['name']])) {
					update_user_meta($user_id, $rego['name'], $_POST[$rego['name']]);
				}
			}
		}
    }
	
	public function ms_custom_login_design() {
		$registration_design = get_option('ms_registration_form_design', array());
		if (isset($registration_design['logo']))
			echo '<style type="text/css">.login h1 a {background-image: url(' . $registration_design['logo'] . ') !important; }</style>';
		if (isset($registration_design['bg']))
			echo '<style type="text/css">body.login {background-image: url(' . $registration_design['bg'] . ') !important; }</style>';
	}
}
