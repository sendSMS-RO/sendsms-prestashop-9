<?php
class Csrf
{
    private $cookie;
    public function __construct()
    {
        $this->cookie = Context::getContext()->cookie;
    }
    
    public function getTokenId()
    {
        if ($this->cookie->__isset('token_id')) {
            return $this->cookie->__get('token_id');
        } else {
            $token_id = $this->random(10);
            $this->cookie->__set('token_id', $token_id);
            return $token_id;
        }
    }

    public function getToken()
    {
        if ($this->cookie->__isset('token_value')) {
            return $this->cookie->__get('token_value');
        } else {
            $token = hash('sha256', $this->random(500));
            $this->cookie->__set('token_value', $token);
            return $token;
        }
    }

    public function checkValid($ajax = false)
    {
        if ($ajax && Tools::getValue('sendsms_security') == $this->getToken()) {
            //sendsms_security predefined value in ajax
            return true;
        }
        if (Tools::getIsset($this->getTokenId()) && Tools::getValue($this->getTokenId()) == $this->getToken()) {
            return true;
        }
        return false;
    }

    public function random($len)
    {
        try {
            // Use cryptographically secure random_bytes (PHP 7+)
            return Tools::substr(bin2hex(random_bytes((int) ceil($len / 2))), 0, $len);
        } catch (\Exception $e) {
            // Fallback to openssl if random_bytes fails
            if (function_exists('openssl_random_pseudo_bytes')) {
                $byteLen = (int) ceil($len / 2);
                return Tools::substr(bin2hex(openssl_random_pseudo_bytes($byteLen)), 0, $len);
            }
            // Last resort fallback using PrestaShop's Tools
            return Tools::passwdGen($len, 'ALPHANUMERIC');
        }
    }
}
