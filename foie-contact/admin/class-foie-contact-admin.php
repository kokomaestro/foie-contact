<?php

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-foie-contact-util.php';

require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Foie_Contact
 * @subpackage Foie_Contact/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Foie_Contact
 * @subpackage Foie_Contact/admin
 * @author     Tob tob@foiegrame.nu
 */
class Foie_Contact_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $foie_contact    The ID of this plugin.
	 */
	private $foie_contact;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
   * Holds the values to be used in the fields callbacks
   */
  private $options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $foie_contact       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $foie_contact, $version ) {

		$this->foie_contact = $foie_contact;
		$this->version = $version;


	}

	/**
     * This function introduces the new plugin submenu underSettings menu.
     */
    public function foie_contact_options_page()
    {
        //Add the menu item to the Main menu
        add_options_page(
            'Foie Contact Options',                      // Page title: The title to be displayed in the browser window for this page.
            'Foie Contact',                              // Menu title: The text to be used for the menu.
            'manage_options',                           // Capability: The capability required for this menu to be displayed to the user.
            'foie-contact-setting-admin',                            // Menu slug: The slug name to refer to this menu by. Should be unique for this menu page.
            array($this, 'create_admin_page')  // Callback: The name of the function to call when rendering this menu's page
        );
    }

		/**
     * Renders the Settings page to display for the Settings menu defined above.
     *
     * @since   1.0.0
     * @param   activeTab       The name of the active tab.
     */
    public function create_admin_page()
    {
        // Check user capabilities
        if (!current_user_can('manage_options'))
        {
            return;
        }

				// Set class property
        $this->options = get_option( 'foie_contact_option_name' );

        // Add error/update messages
        // check if the user have submitted the settings. Wordpress will add the "settings-updated" $_GET parameter to the url
        if (isset($_GET['settings-updated']))
        {
            // Add settings saved message with the class of "updated"
            add_settings_error($this->foie_contact, $this->foie_contact . '-message', __('Settings saved.'), 'success');
        }

        // Show error/update messages
        settings_errors($this->foie_contact);

        ?>
        <!-- Create a header in the default WordPress 'wrap' container -->
        <div class="wrap">

            <h2><?php esc_html( get_admin_page_title() ); ?></h2>

            <form method="post" action="options.php">
                <?php
								// This prints out all hidden setting fields
                settings_fields( 'foie_contact_option_group' );
                do_settings_sections( 'foie-contact-setting-admin' );
                submit_button('Save Settings');
                ?>
            </form>

        </div><!-- /.wrap -->
        <?php
    }

		/**
     * Register and add settings
     */
    public function page_init()
    {
        register_setting(
            'foie_contact_option_group', // Option group
            'foie_contact_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'foie_contact_setting_section_id', // ID
            'Foie Contact Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'foie-contact-setting-admin' // Page
        );

        add_settings_field(
            'host_name', // ID
            'SMTP Server Hostname', // Title
            array( $this, 'foie_contact_input_callback' ), // Callback
            'foie-contact-setting-admin', // Page
            'foie_contact_setting_section_id', // Section
						array(
		            'label_for'         => 'host_name',
		            'class'             => 'foie_contact_row',
		        )
        );

        add_settings_field(
            'smtp_user',
            'SMTP Username',
            array( $this, 'foie_contact_input_callback' ),
            'foie-contact-setting-admin',
            'foie_contact_setting_section_id',
						array(
		            'label_for'         => 'smtp_user',
		            'class'             => 'foie_contact_row',
								'input_type'        => 'email',
		        )
        );

				add_settings_field(
            'smtp_password', // ID
            'SMTP Password', // Title
            array( $this, 'foie_contact_input_callback' ), // Callback
            'foie-contact-setting-admin', // Page
            'foie_contact_setting_section_id', // Section
						array(
								'label_for'         => 'smtp_password',
		            'class'             => 'foie_contact_row',
								'input_type'        => 'password',
		        )
        );

        add_settings_field(
            'enc_type',
            'Encryption Type',
            array( $this, 'encryption_type_callback' ),
            'foie-contact-setting-admin',
            'foie_contact_setting_section_id',
						array(
		            'class'             => 'foie_contact_row',
		        )
        );

				add_settings_field(
            'to_address', // ID
            'To Address', // Title
            array( $this, 'foie_contact_input_callback' ), // Callback
            'foie-contact-setting-admin', // Page
            'foie_contact_setting_section_id', // Section
						array(
		            'label_for'         => 'to_address',
		            'class'             => 'foie_contact_row',
								'input_type'        => 'email',
		        )
        );

        add_settings_field(
            'from_address',
            'Domain From Address',
            array( $this, 'foie_contact_input_callback' ),
            'foie-contact-setting-admin',
            'foie_contact_setting_section_id',
						array(
		            'label_for'         => 'from_address',
		            'class'             => 'foie_contact_row',
								'input_type'        => 'email',
		        )
        );
    }

		/**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
				foreach ($input as $key => $val) {
					if( in_array($key, ['host_name', 'enc_type'] ) ) {
							$new_input[$key] = sanitize_text_field( $val );
					}
					elseif( in_array($key, ['to_address', 'from_address', 'smtp_user'] ) ) {
							$new_input[$key] = sanitize_email( $val );
					}
					elseif( $key == 'smtp_password') {
							$new_input[$key] = Foie_Contact_Util::safeEncrypt($val, ENC_KEY);
					}

				}
        return $new_input;
    }

		/**
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

		/**
     * Get the settings option array and print one of its values
     */
    public function foie_contact_input_callback( $args )
    {
        printf(
            '<input type="%s" id="%s" name="foie_contact_option_name[%s]" value="%s" />', isset( $args['input_type'] ) ?
						esc_attr( $args['input_type']) : esc_attr('text'), esc_attr( $args['label_for'] ), esc_attr( $args['label_for'] ),
						isset( $this->options[ $args['label_for'] ] ) ? esc_attr( $this->options[ $args['label_for']]) : ''
        );
    }

		/**
     * Get the settings option array and print one of its values
     */
    public function encryption_type_callback( $args )
    {
        printf(
            '<select id="enc_type" name="foie_contact_option_name[enc_type]">
				        <option value="TLS" %s>
				            %s
				        </option>
				        <option value="SMTPS" %s>
				            %s
				        </option>
				    </select>', isset( $this->options['enc_type']) ? ( selected( $this->options[ 'enc_type' ], 'TLS', false) ) : ( '' ), esc_html( 'TLS' ),
						isset( $this->options['enc_type']) ? ( selected( $this->options[ 'enc_type' ], 'SMTPS', false) ) : ( '' ), esc_html( 'SMTPS')
        );
    }

		// send Email to Admin
		function send_email_to_admin() {

				$this->options = get_option( 'foie_contact_option_name' );

				$errors = array();

				if(!(isset( $this->options['host_name']) && isset( $this->options['smtp_user']) && isset( $this->options['smtp_password']) &&
						isset( $this->options['enc_type']) && isset( $this->options['to_address']) && isset( $this->options['from_address']) ) ) {
				    $errors['incomplete_settings'] = "There was a Settings error sending your email. Please email me directly at " .  $this->options['to_address'];
				} else {
					  if ( !isset($_POST['contact_form_nonce']) || !wp_verify_nonce($_POST['contact_form_nonce'], 'contact-form')) {
							$errors['invalid_nonce'] = "Invalid Authorization.";
					  }
						if (array_key_exists('cf_subject', $_POST) && !empty($_POST['cf_subject'])) {
						    $subject = substr(sanitize_text_field($_POST['cf_subject']), 0, 255);
						} else {
						    $subject = 'No subject given';
						}
						if (array_key_exists('cf_query', $_POST) && !empty($_POST['cf_query'])) {
						    $query = substr(sanitize_textarea_field($_POST['cf_query']), 0, 16384);
						} else {
						    $query = '';
								$errors['no_query'] = 'No query provided!';
						}
						if (array_key_exists('cf_name', $_POST) && !empty($_POST['cf_name'])) {
						    $name = substr(sanitize_text_field($_POST['cf_name']), 0, 255);
						} else {
						    $name = '';
						}
						$to = $this->options['to_address'];
						if (array_key_exists('cf_email', $_POST) && PHPMailer::validateAddress($_POST['cf_email'])) {
						    $email = sanitize_email($_POST['cf_email']);
						} else {
								$errors['invalid_email'] = "Invalid email address provided";
						}
				}

				if ( empty($errors) ) {
				    $mail = new PHPMailer();
				    $mail->isSMTP();
				    $mail->Host = $this->options['host_name'];
						if($this->options['enc_type'] == 'TLS') {
							  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
								$mail->Port = 587;
						} elseif($this->options['enc_type'] == 'SMPTS') {
								$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
								$mail->Port = 465;
						}
				    $mail->CharSet = PHPMailer::CHARSET_UTF8;

						$mail->SMTPAuth   = true;
				    $mail->Username   = $this->options['smtp_user'];
				    $mail->Password   = Foie_Contact_Util::safeDecrypt($this->options['smtp_password'], ENC_KEY ); //define ENC_KEY somewhere outside the plugin
				    $mail->setFrom($this->options['from_address'], (empty($name) ? 'Contact form' : $name));
				    $mail->addAddress($to);
				    $mail->addReplyTo($email, $name);
				    $mail->Subject = 'Contact form: ' . $subject;
				    $mail->Body = "Contact form submission\n\n" . $query;
				    if (!$mail->send()) {
								$errors['mailer_error'] = 'There was a Mailer error sending your email. Please email me directly at ' .  $this->options['to_address'];
								set_transient( 'contact_form_errors', $errors, 15 );
				    } else {
								delete_transient( 'contact_form_errors' );
								set_transient( 'contact_form_success', 'Message Sent!', 15 );
				    }
				} else {
						set_transient( 'contact_form_errors', $errors, 15 );
				}
				wp_redirect( wp_get_referer() );
				exit;
		}

}
