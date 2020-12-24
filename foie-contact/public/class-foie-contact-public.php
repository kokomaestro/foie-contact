<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Foie_Contact
 * @subpackage Foie_Contact/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Foie_Contact
 * @subpackage Foie_Contact/public
 * @author     Tob tob@foiegrame.nu
 */
class Foie_Contact_Public {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $foie_contact       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $foie_contact, $version ) {

		$this->foie_contact = $foie_contact;
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
		 * defined in Foie_Contact_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Foie_Contact_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->foie_contact, plugin_dir_url( __FILE__ ) . 'css/foie-contact-public.css', array(),
				filemtime( plugin_dir_path( __FILE__ ) . 'css/foie-contact-public.css'), 'all' );

	}

	//Add Foie Contact Shorcode
	public function foie_contact_add_shortcode() {
	    add_shortcode('foie_contact', array($this, 'foie_contact_shortcode'));
	}

		/**
		* The [foie-contact] shortcode.  Accepts a title and will display a contact form.
	  *
	  * @param array  $atts     Shortcode attributes. Default empty.
	  * @param atring $content  Shortcode content. Default null.
	  * @param string $tag      Shortcode tag (name). Default empty.
	  *
	  * @return string
	  */
	 function foie_contact_shortcode( $atts = [], $content = null, $tag = '' ) {

			 $options = get_option( 'foie_contact_option_name' );
			 $errors = get_transient( 'contact_form_errors' );



			 // start box
	     $o = '<div id="outer-container">';

			 if(isset($options) && isset($options['to_address'])) {

				 $o .= '<h2>Email me at ' .  esc_html($options['to_address']) . ' or use the contact form below</h2>';

				 if ( is_array( $errors ) && ! empty( $errors ) ) {
				     $o .= '<ul id="contact-form-errors">
				            	<li>' . implode( '</li><li>', $errors ) . '</li>
										</ul>';
				 } elseif (empty( get_transient( 'contact_form_success' ) )) {
				     $o .= '<form id="container" method="post" action= "' . esc_url( admin_url('admin-post.php') ) . '">
								 		 <label for="subject">Subject: <input class = "contact-form-input" type="text" name="cf_subject" id="subject" maxlength="255" value="' .
										 (isset($_POST["cf_subject"]) ? esc_html($_POST["cf_subject"]) : '') . '"></label>
								 		 <label for="name">Name: <input class = "contact-form-input" type="text" name="cf_name" id="name" maxlength="255" value="' .
										 (isset($_POST["cf_name"]) ? esc_html($_POST["cf_name"]) : '') . '"></label>
								 		 <label for="email">Email address: <input class = "contact-form-input" type="email" name="cf_email" id="email" maxlength="255" value="' .
										 (isset($_POST["cf_email"]) ? esc_html($_POST["cf_email"]) : '') . '" required></label>
								 		 <label for="query">Your question:</label>
								 		 <textarea id= "contact-msg" cols="30" rows="8" name="cf_query" id="query" placeholder="Your question" value="' .
										 (isset($_POST["cf_query"]) ? esc_textarea($_POST["cf_query"]) : '') . '" required></textarea>' .
				 		 		 			wp_nonce_field('contact-form', 'contact_form_nonce', true, false) .
				 		 				 '<input type="hidden" name="action" value="contact_form">
								 		 <input type="submit" value="Submit">
								 		 </form>';
				 } else {
				 		$o .= '<p id="success-msg">Message Sent! Thank you!</p>';
				 		delete_transient( 'my_form_success' );
				 }

		     // enclosing tags
		     if ( ! is_null( $content ) ) {
		         // secure output by executing the_content filter hook on $content
		         $o .= apply_filters( 'the_content', $content );

		         // run shortcode parser recursively
		         $o .= do_shortcode( $content );
		     }

			 }

			 else {

				 $o .= '<h2>First Setup settings on the Admin Page</h2>';

			 }

	     // end box
	     $o .= '</div>';

	     // return output
	     return $o;
	 }

}
