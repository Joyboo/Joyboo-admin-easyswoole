<?php


namespace EasySwoole\Http\GlobalParam;

use EasySwoole\Spl\SplBean;

class SessionConfig extends SplBean
{
    protected $cookieExpire = 0;
    protected $cookiePath = '/';
    protected $cookieDomain = '';
    protected $cookieSecure = false;
    protected $cookieHttponly = false;
    protected $cookieSameSite = '';
    protected $sessionName = 'es_session';

    public function getCookieExpire(): int
    {
        return $this->cookieExpire;
    }

    public function setCookieExpire(int $cookieExpire): SessionConfig
    {
        $this->cookieExpire = $cookieExpire;
        return $this;
    }

    public function getCookiePath(): string
    {
        return $this->cookiePath;
    }

    public function setCookiePath(string $cookiePath): SessionConfig
    {
        $this->cookiePath = $cookiePath;
        return $this;
    }

    public function getCookieDomain(): string
    {
        return $this->cookieDomain;
    }

    public function setCookieDomain(string $cookieDomain): SessionConfig
    {
        $this->cookieDomain = $cookieDomain;
        return $this;
    }

    public function isCookieSecure(): bool
    {
        return $this->cookieSecure;
    }

    public function setCookieSecure(bool $cookieSecure): SessionConfig
    {
        $this->cookieSecure = $cookieSecure;
        return $this;
    }

    public function isCookieHttponly(): bool
    {
        return $this->cookieHttponly;
    }

    public function setCookieHttponly(bool $cookieHttponly): SessionConfig
    {
        $this->cookieHttponly = $cookieHttponly;
        return $this;
    }

    public function getCookieSameSite(): string
    {
        return $this->cookieSameSite;
    }

    public function setCookieSameSite(string $cookieSameSite): SessionConfig
    {
        $this->cookieSameSite = $cookieSameSite;
        return $this;
    }

    /**
     * @return string
     */
    public function getSessionName(): string
    {
        return $this->sessionName;
    }

    /**
     * @param string $sessionName
     */
    public function setSessionName(string $sessionName): void
    {
        $this->sessionName = $sessionName;
    }
}