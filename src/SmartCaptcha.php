<?php

namespace PBorisenko\SpamProtection;

class SmartCaptcha implements Interfaces\CaptchaInterface
{
    const VISIBLE = 1;
    const INVISIBLE = 2;
    const FANCYBOX_VISIBLE = 3;
    const FANCYBOX_INVISIBLE = 4;

    public static function activate(
        string $containerID,
        Interfaces\CaptchaConfigInterface $config,
        ?int $mode = 1
    ): void
    {
        switch ($mode) {
            case self::VISIBLE:
                echo self::visible($containerID, $config); 
                break;

            case self::INVISIBLE:
                echo self::invisible($containerID, $config);
                break;

            case self::FANCYBOX_VISIBLE:
                echo self::fancyboxVisible($containerID, $config); 
                break;

            case self::FANCYBOX_INVISIBLE:
                echo self::fancyboxInvisible($containerID, $config);
                break;
                
            default: throw new \Exception(Exception\Message::UNKNOWN_CAPTCHA_MODE);
        }
    }

    public static function check(string $token, Interfaces\CaptchaConfigInterface $config): bool
    {
        $ch = curl_init();
        $endpoint = $config->getEndpoint();
        $args = http_build_query([
            "secret" => $config->getServerKey(),
            "token" => $token,
            "ip" => self::getIP(),
        ]);
        curl_setopt($ch, CURLOPT_URL, "$endpoint?$args");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    
        $server_output = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    
        if ($httpcode !== 200) {
            return false;
        }
        
        $resp = json_decode($server_output);
        return $resp->status === "ok";
    }

    private static function getIP(): string 
    {
        $keys = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'REMOTE_ADDR'
        ];

        foreach ($keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = trim(end(explode(',', $_SERVER[$key])));
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return "";
    }

    private static function visible(
        string $containerID, 
        Interfaces\CaptchaConfigInterface $config
    ): string
    {
        ob_start();
        ?>
        <div id="<?= $containerID ?>" class="smart-captcha" data-sitekey="<?= $config->getSiteKey() ?>"></div>
        <script>
            if (window.smartCaptcha) {
                smartCaptcha.render(
                    document.querySelector('#<?= $containerID ?>'), 
                    {
                        'sitekey': '<?= $config->getSiteKey() ?>',
                        'test': <?= $config->isDebug() ? 'true' : 'false' ?>
                    }
                );
            }
        </script>
        <?
        return ob_get_clean();
    }

    private static function invisible(
        string $containerID, 
        Interfaces\CaptchaConfigInterface $config
    ): string
    {
        ob_start();
        ?>
        <div id="<?= $containerID ?>" style="display: none;"></div>
        <script>
            let widgetId;
            
            function callback(token) {
                if (typeof token === "string" && token.length > 0) {
                    const eventEmitter = document.querySelector('#<?= $containerID ?>');
                    const customEvent = new CustomEvent('aftercaptcha', {
                        detail: {token: token},
                        bubbles: true,
                        cancelable: true
                    });
                    document.querySelector('#<?= $containerID ?>').dispatchEvent(customEvent);
                    document.querySelector('#<?= $containerID ?> input[name="smart-token"]').value = token;
                }
            }

            if (window.smartCaptcha) {
                widgetId = smartCaptcha.render(
                    document.querySelector('#<?= $containerID ?>'), 
                    {
                        'sitekey': '<?= $config->getSiteKey() ?>',
                        'invisible': true,
                        'hideShield': <?= $config->isHiddenShield() ? 'true' : 'false' ?>,
                        'callback': callback,
                        'test': <?= $config->isDebug() ? 'true' : 'false' ?>
                    }
                );
                document.querySelector('#<?= $containerID ?>').setAttribute('data-wid', widgetId);
                window.smartCaptcha.subscribe(
                    widgetId,
                    'success',
                    () => {
                        const event = new Event("submit", {bubbles : true, cancelable : true});
                        document.querySelector('#<?= $containerID ?>').dispatchEvent(event);
                    }
                );
            }
        </script>
        <?
        return ob_get_clean();
    }

    private static function fancyboxVisible(
        string $containerID, 
        Interfaces\CaptchaConfigInterface $config
    ): string
    {
        ob_start();
        ?>
        <div id="<?= $containerID ?>" class="smart-captcha" data-sitekey="<?= $config->getSiteKey() ?>"></div>
        <script>
            if (window.jQuery.fancybox && window.smartCaptcha) {
                jQuery('[data-fancybox]').each(function(){
                    this.fancybox({'afterShow': function(instance, slide) {
                        slide.$content.find('#<?= $containerID ?>').each(function(){
                            smartCaptcha.render(this, {
                                'sitekey': '<?= $config->getSiteKey() ?>',
                                'test': <?= $config->isDebug() ? 'true' : 'false' ?>
                            });
                        });
                    }});
                });
            }
        </script>
        <?
        return ob_get_clean();
    }

    private static function fancyboxInvisible(
        string $containerID, 
        Interfaces\CaptchaConfigInterface $config
    ): string
    {
        ob_start();
        ?>
        <div id="<?= $containerID ?>" style="display: none;"></div>
        <script>
            let widgetId;
            
            function callback(token) {
                if (typeof token === "string" && token.length > 0) {
                    const eventEmitter = document.querySelector('#<?= $containerID ?>');
                    const customEvent = new CustomEvent('aftercaptcha', {
                        detail: {token: token},
                        bubbles: true,
                        cancelable: true
                    });
                    document.querySelector('#<?= $containerID ?>').dispatchEvent(customEvent);
                    document.querySelector('#<?= $containerID ?> input[name="smart-token"]').value = token;
                }
            }

            if (window.jQuery.fancybox && window.smartCaptcha) {
                jQuery('[data-fancybox]').each(function(){
                    this.fancybox({'afterShow': function(instance, slide) {
                        slide.$content.find('#<?= $containerID ?>').each(function(){
                            widgetId = smartCaptcha.render(this, {
                                'sitekey': '<?= $config->getSiteKey() ?>',
                                'invisible': true,
                                'hideShield': <?= $config->isHiddenShield() ? 'true' : 'false' ?>,
                                'callback': callback,
                                'test': <?= $config->isDebug() ? 'true' : 'false' ?>
                            });
                            this.setAttribute('data-wid', widgetId);
                            window.smartCaptcha.subscribe(
                                widgetId,
                                'success',
                                () => jQuery(this).closest('form').trigger('submit')
                            );
                        });
                    }});
                }):
            }
        </script>
        <?
        return ob_get_clean();
    }
}