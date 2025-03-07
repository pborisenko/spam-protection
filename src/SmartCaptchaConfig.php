<?php

namespace PBorisenko\SpamProtection;

class SmartCaptchaConfig implements Interfaces\CaptchaConfigInterface
{
    private array $config;

    private function __construct(array $configurationValue)
    {    
        $this->config = $configurationValue;
    }

    public static function apply(array $configurationValue): SmartCaptchaConfig
    {
        return new self($configurationValue);
    }

    public function getSiteKey(): string
    {
        if (!isset($this->config['sitekey']))
            throw new \Exception(Exception\Message::UNDEFINED_SITEKEY_CAPTCHA);

        return $this->config['sitekey']  ?? '';
    }

    public function getServerKey(): string{
        if (!isset($this->config['serverkey']))
            throw new \Exception(Exception\Message::UNDEFINED_SERVERKEY_CAPTCHA);

        return $this->config['serverkey']  ?? '';
    }

    public function getEndpoint(): string{
        if (!isset($this->config['endpoint']))
            throw new \Exception(Exception\Message::UNDEFINED_ENDPOINT_CAPTCHA);

        return $this->config['endpoint']  ?? '';
    }

    public function getShieldPosition(): string{
        if (!isset($this->config['shieldposition']))
            throw new \Exception(Exception\Message::UNDEFINED_SHIELDPOSITION_CAPTCHA);

        return $this->config['shieldposition']  ?? '';
    }

    public function getScriptSource():string{
        if (!isset($this->config['scriptsource']))
            throw new \Exception(Exception\Message::UNDEFINED_SCRIPTSOURCE_CAPTCHA);

        return $this->config['scriptsource']  ?? '';
    }

    public function isDebug(): bool{
        if (!isset($this->config['debug']))
            throw new \Exception(Exception\Message::UNDEFINED_DEBUG_CAPTCHA);

        return $this->config['debug']  ?? false;
    }

    public function isHiddenShield(): bool{
        if (!isset($this->config['hideshield']))
            throw new \Exception(Exception\Message::UNDEFINED_HIDESHIELD_CAPTCHA);
        
        return $this->config['hideshield']  ?? false;
    }
}