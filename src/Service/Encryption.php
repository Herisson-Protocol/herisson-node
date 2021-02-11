<?php
/**
 * Herisson\Encryption
 *
 * PHP Version 5.3
 *
 * @category Tools
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      None
 */

namespace Herisson\Service;

use Herisson\Encryption\Exception as EncryptionException;

/**
 * Herisson\Encryption
 * 
 * Handles public/private key asymetric encryption
 * 
 * @category Tools
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      None
 */
class Encryption
{

    /**
     * singleton
     */
    public static $i;

    /**
     * Public key
     */
    public $public;

    /**
     * Private key
     */
    public $private;

    /**
     * Encryption method for long data
     */
    public static $method = "aes256";

    /**
     * Creating singleton
     *
     * @return Encryption instance
     */
    public static function i() : Encryption
    {
        if (is_null(self::$i)) {
            self::$i = new Encryption();
        }
        return self::$i;
    }

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->loadKeys();
    }

    /**
     * Load keys from wordpress options
     * 
     * If not in Wordpress environment, generates a new public/private key pair.
     *
     * @return void
     */
    public function loadKeys()
    {
        global $wp_version;
        if (isset($wp_version)) {
            $options       = get_option('HerissonOptions');
            $this->public  = $options['publicKey'];
            $this->private = $options['privateKey'];
        } else {
            $this->generateKeyPairs();
        }
    }

    /**
     * Generate a public/private keys pair
     *
     * It then set them in $public and $private attributes
     *
     * @return void
     */
    public function generateKeyPairs()
    {
        /*
        error_log("===================================");
        error_log("Generating new key pairs ");
        error_log("===================================");
        */
        // Create the keypair
        $res = openssl_pkey_new();

        // Get private key
        openssl_pkey_export($res, $this->private);

        // Get public key
        $pubkey       = openssl_pkey_get_details($res);
        $this->public = $pubkey["key"];
    }

    /**
     * Hash a variable in sha256
     *
     * @param string $data the data to hash
     *
     * @return string hashed data in sha256
     */
    public function hash(string $data) : string
    {
        return hash("sha256", $data);
    }

    /**
     * Create a random IV string
     *
     * @param integer $length the length of the expected string (default=16)
     *
     * @return string a random IV string of given length
     */
    public function createIV(int $length=16) : string
    {
        if (function_exists('mcrypt_create_iv')) {
            return mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
        }
        $chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        return substr(str_shuffle($chars), 0, $length);
    }

    /**
     * Encrypt data using a public key
     *
     * @param mixed $data the data to encrypt
     * @param mixed $key  optional public key,
     *        if none given, the $this->public key is used
     *
     * @return string the encrypted data
     */
    function publicEncrypt($data, $key=null) : string
    {
        if (is_null($key)) {
            $key = $this->public;
        }

        if (!openssl_public_encrypt($data, $data_crypted, $key)) {
            throw new EncryptionException(
                'Error while encrypting with public key');
        }
        return base64_encode($data_crypted);
    }

    /**
     * Decrypt encrypted data using a public key
     *
     * @param mixed $data_crypted the encrypted data
     * @param mixed $key          optional public key, if none given, the $this->public key is used
     *
     * @return string the clear data
     */
    function publicDecrypt($data_crypted, $key=null) : string
    {
        if (is_null($key)) {
            $key = $this->public;
        }
        if (!openssl_public_decrypt(base64_decode($data_crypted), $data, $key)) {
            throw new EncryptionException('Error while decrypting with public key');
        }
        return $data;
    }

    /**
     * Encrypt data using a private key
     *
     * @param mixed $data the data to encrypt
     * @param mixed $key  optional private key, if none given, the $this->private key is used
     *
     * @return string the encrypted data
     */
    function privateEncrypt($data, $key=null) : string
    {
        if (is_null($key)) {
            $key = $this->private;
        }

        if (!openssl_private_encrypt($data, $data_crypted, $key)) {
            throw new EncryptionException(
                'Error while encrypting with private key');
        }
        return base64_encode($data_crypted);
    }

    /**
     * Decrypt encrypted data using a private key
     *
     * @param mixed $data_crypted the encrypted data
     * @param mixed $key          optional private key, if none given, the $this->private key is used
     *
     * @return string the clear data
     */
    function privateDecrypt($data_crypted, $key=null) : string
    {
        if (is_null($key)) {
            $key = $this->private;
        }
        if (!openssl_private_decrypt(base64_decode($data_crypted), $data, $key)) {
            throw new EncryptionException(
                'Error while decrypting with private key');
        }
        return $data;
    }

    /**
     * Encrypt long data using a public key
     *
     * Since we want to encrypt long data, we encrypt the hash of the data with 
     * the public key, and we encrypt the data with self::method (eg. aes256) 
     * and the password is the encrypted hash
     *
     * @param mixed $data the data to encrypt
     * @param mixed $key  optional public key, if none given, the $this->public key is used
     *
     * @return array an array with 'data' => the encrypted data, 'hash' => the encrypted hash
     */
    function publicEncryptLongData($data, $key=null)
    {
        if (is_null($key)) {
            $key = $this->public;
        }
        $hash = $this->hash($data);

        $iv = $this->createIV();

        if (!openssl_public_encrypt($hash, $hash_crypted, $key)) {
            throw new EncryptionException(
                'Error while encrypting hash with public key');
        }

        $data_crypted = null;
        if (!($data_crypted = openssl_encrypt($data, self::$method, $hash, 0, $iv))) {
            throw new EncryptionException(
                'Error while encrypting long data with encryption method');
        }

        return array(
            'data'  => base64_encode($data_crypted),
            'hash'  => base64_encode($hash_crypted),
            'iv'    => base64_encode($iv),
        );
    }


    /**
     * Decrypt long data using a public key
     *
     * Since we want to decrypt a long encrypted data, we decrypt the crypted hash with the public key,
     * and we decrypt the long data with the decrypted hash using encryption 
     * method
     *
     * @param mixed $data_crypted the crypted data (crypted with the hash)
     * @param mixed $hash_crypted the crypted hash (crypted with the private key)
     * @param mixed $iv           the initialization vector to increase cipher security
     * @param mixed $key          optional public key, if none given, the $this->public key is used
     *
     * @return string the decrypted data
     */
    public function publicDecryptLongData($data_crypted, $hash_crypted, $iv, $key=null)
    {
        if (is_null($key)) {
            $key = $this->public;
        }

        if (!openssl_public_decrypt(base64_decode($hash_crypted), $hash, $key)) {
            throw new EncryptionException(
                'Error while decrypting hash with public key');
        }

        if (!($data = openssl_decrypt(base64_decode($data_crypted), self::$method, $hash, 0, base64_decode($iv)))) {
            throw new EncryptionException(
                'Error while encrypting long data with encryption method');
        }

        // Check the hash
        if ($hash != $this->hash($data)) {
            throw new EncryptionException(
                'Error while comparing checksum of decrypted data');
        }
       
        return $data;
    }

    /**
     * Encrypt long data using a private key
     *
     * Since we want to encrypt long data, we encrypt the hash of the data with 
     * the private key, and we encrypt the data with self::method (eg. aes256) 
     * and the password is the encrypted hash
     *
     * @param mixed $data the data to encrypt
     * @param mixed $key  optional private key, if none given, the $this->private key is used
     *
     * @return array an array with 'data' => the encrypted data, 'hash' => the encrypted hash
     */
    public function privateEncryptLongData($data, $key=null) : array
    {
        if (is_null($key)) {
            $key = $this->private;
        }
        $hash = $this->hash($data);

        $iv = $this->createIV();

        if (!openssl_private_encrypt($hash, $hash_crypted, $key)) {
            throw new EncryptionException(
                'Error while encrypting hash with private key');
        }

        $data_crypted = null;
        if (!($data_crypted = openssl_encrypt($data, self::$method, $hash, 0, $iv))) {
            throw new EncryptionException(
                'Error while encrypting long data with encryption method');
        }

        return array(
            'data'  => base64_encode($data_crypted),
            'hash'  => base64_encode($hash_crypted),
            'iv'    => base64_encode($iv),
        );
    }


    /**
     * Decrypt long data using a private key
     *
     * Since we want to decrypt a long encrypted data, we decrypt the crypted hash with the private key,
     * and we decrypt the long data with the decrypted hash using encryption 
     * method
     *
     * @param mixed $data_crypted the ciphered data (ciphered with the hash)
     * @param mixed $hash_crypted the ciphered hash (ciphered with the public key)
     * @param mixed $iv           the initialization vector to increase cipher security
     * @param mixed $key          optional private key, if none given, the $this->private key is used
     *
     * @return string the decrypted data
     */
    public function privateDecryptLongData($data_crypted, $hash_crypted, $iv, $key=null) : string
    {
        if (is_null($key)) {
            $key = $this->private;
        }

        if (!openssl_private_decrypt(base64_decode($hash_crypted), $hash, $key)) {
            throw new EncryptionException(
                'Error while decrypting hash with private key');
        }

        if (!($data = openssl_decrypt(base64_decode($data_crypted), self::$method, $hash, 0, base64_decode($iv)))) {
            throw new EncryptionException(
                'Error while encrypting long data with encryption method');
        }

        // Check the hash
        if ($hash != $this->hash($data)) {
            throw new EncryptionException(
                'Error while comparing checksum of decrypted data');
        }
       
        return $data;
    }

    /*
    function herisson_encrypt_backup()
    {
     $options = get_option('HerissonOptions');

     $_bookmarks = Doctrine_Query::create()
      ->from('Bookmark')
      ->where("id=$id")
      ->execute();
     $bookmarks = array();
     foreach ($_bookmarks as $bookmark) {
      $bookmarks[] = $bookmark->toArray();
     }
     $data = json_encode($bookmarks);

        $my_public_key  = $options['publicKey'];
        $my_private_key = $options['privateKey'];

     $hash = Herisson\Encryption::i()->hash($data);
        if (!openssl_private_encrypt($hash, $hash_crypted, $my_public_key)) {
         throw new Encryption\Exception(__('Error while encrypting bkacup hash with my public key', HERISSON_TD));
        }
     $data_crypted = null;

     if (!openssl_seal($data, $data_crypted, $seal_key, array($my_public_key))) {
         throw new Encryption\Exception(__('Error while encrypting backup data with my public key<br>', HERISSON_TD));
        }

        return array(
                 'data' => base64_encode($data_crypted),
                    'hash' => base64_encode($hash_crypted),
                    'seal' => base64_encode($seal_key[0]),
                );
    }
    */

}


