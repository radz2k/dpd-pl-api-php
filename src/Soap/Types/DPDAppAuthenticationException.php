<?php

namespace radz2k\Dpd\Soap\Types;

class DPDAppAuthenticationException
{

    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $message;

    /**
     * @return string
     */
    public function getLogin() : string
    {
        return $this->login;
    }

    /**
     * @param string $login
     * @return $this
     */
    public function setLogin(string $login) : \radz2k\Dpd\Soap\Types\DPDAppAuthenticationException
    {
        $this->login = $login;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage() : string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message) : \radz2k\Dpd\Soap\Types\DPDAppAuthenticationException
    {
        $this->message = $message;
        return $this;
    }


}

