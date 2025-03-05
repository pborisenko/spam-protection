<?php

namespace PBorisenko\SpamProtection\Interfaces;

interface CaptchaConfigInterface
{
    public function getSiteKey(): string;

    public function getServerKey(): string;

    public function getEndpoint(): string;

    public function getShieldPosition(): string;

    public function getScriptSource():string;

    public function isDebug(): bool;

    public function isHiddenShield(): bool;
}