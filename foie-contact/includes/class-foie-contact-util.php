<?php

/**
 * Add Encryption Utilities to Plugin
 *
 * Loads utilities to be used in other files in the plugin
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Foie_Contact
 * @subpackage Foie_Contact/includes
 */

/**
 * Define the utilties functionality.
 *
 * Defines the encrypt and decrypt functions
 *
 * @since      1.0.0
 * @package    Foie_Contact
 * @subpackage Foie_Contact/includes
 * @author     Tob tob@foiegrame.nu
 */
class Foie_Contact_Util {


		/**
	* Encrypt a message
	*
	* @param string $message - message to encrypt
	* @param string $key - encryption key
	* @return string
	*/
	public static function safeEncrypt($message, $key)
	{
	    $nonce = random_bytes(
	        SODIUM_CRYPTO_SECRETBOX_NONCEBYTES
	    );

	    $cipher = base64_encode(
	        $nonce.
	        sodium_crypto_secretbox(
	            $message,
	            $nonce,
	            $key
	        )
	    );
	    sodium_memzero($message);
	    sodium_memzero($key);
	    return $cipher;
	}

	/**
	* Decrypt a message
	*
	* @param string $encrypted - message encrypted with safeEncrypt()
	* @param string $key - encryption key
	* @return string
	*/
	public static function safeDecrypt($encrypted, $key)
	{
	    $decoded = base64_decode($encrypted);
	    if ($decoded === false) {
	        throw new Exception('Scream bloody murder, the encoding failed');
	    }
	    if (mb_strlen($decoded, '8bit') < (SODIUM_CRYPTO_SECRETBOX_NONCEBYTES + SODIUM_CRYPTO_SECRETBOX_MACBYTES)) {
	        throw new Exception('Scream bloody murder, the message was truncated');
	    }
	    $nonce = mb_substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
	    $ciphertext = mb_substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');

	    $plain = sodium_crypto_secretbox_open(
	        $ciphertext,
	        $nonce,
	        $key
	    );
	    if ($plain === false) {
	         throw new Exception('the message was tampered with in transit');
	    }
	    sodium_memzero($ciphertext);
	    sodium_memzero($key);
	    return $plain;
	}

	/**
     * Get a secret key for encrypt/decrypt
     *
     * Use libsodium to generate a secret key.  This should be kept secure.
     *
     * @return string
     * @see encrypt(), decrypt()
     */
    public static function generateSecretKey()
    {
        return sodium_crypto_secretbox_keygen();
    }



}
