<?php

namespace App\Service;


use App\Entity\User;

class HashService
{
    /**
     * simple method to encrypt or decrypt a plain text string
     * initialization vector(IV) has to be the same when encrypting and decrypting
     *
     * @param string $action: can be 'encrypt' or 'decrypt'
     * @param string $string: string to encrypt or decrypt
     *
     * @return string
     */
    function encrypt_decrypt($action, $string) {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = '123456';
        $secret_iv = '123456';
        $key = hash('sha256', $secret_key);

        /** iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning **/
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if( $action == 'decrypt' ) {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }
    /**
     * @param User $user
     * @param      $string
     * @return string
     */
    public function hashValue(User $user, $string)
    {
        return crypt($string, $user->getSalt());
    }

    /**
     * @param User   $user
     * @param string $verificationCode
     *
     * @return bool
     */
    public function checkUserVerificationCode(User $user, $verificationCode)
    {
        return $this->hashValue($user, $verificationCode) === $user->getExpert()->getVerificationCode();
    }
}