<?php

namespace PBorisenko\SpamProtection\Interfaces;

interface CaptchaInterface
{
    public static function activate(
        string $containerID,
        ConfigCaptchaInterface $config,
        ?int $mode = 1
    ): void;

    public static function check(
        string $token,
        ConfigCaptchaInterface $config
    ): bool;
}