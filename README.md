# Spam Protection
Свой опыт по защите веб-форм от спам атак оформляю в PHP-пакет для оперативного развёртывания решений на различных проектах и PHP фреймворках.
## Установка пакета
```shell
composer require pborisenko/spam-protection
```
## SmartCaptcha
Одним из самых простых и эффективных решений по защите веб-форм представлено технологией SmartCaptcha от Yandex. Данный пакет содержит простое решение для использования технологии в вашем проекте. Подробнее об использовании технологии в официальной документации [Yandex SmartCaptcha](https://yandex.cloud/ru/docs/smartcaptcha/).
### Конфигурация
__Bitrix Framework__

Необходимо разместить в файле `bitrix/.settings_extra.php` следующую структуру.
```php
'smart_captcha' => [
    'value' => [
        'sitekey' => '<ключ клиентской части>',
        'serverkey' => '<ключ серверной части>',
        'endpoint' => 'https://smartcaptcha.yandexcloud.net/validate',
        'scriptsource' => 'https://smartcaptcha.yandexcloud.net/captcha.js',
        'shieldposition' => 'bottom-right',
        'hideshield' => false,
        'debug' => false
    ],
    'readonly' => true,
]
```
### Использование
Для начала необходимо подключить JS скрипт капчи на страницу. Используйте средства вашего фреймворка и метод пакета для получения URL адреса для загрузки JS скрипта.

__Bitrix Framework__
```php
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Config\Configuration;
use PBorisenko\SpamProtection\SmartCaptchaConfig;

$captchaConfig = SmartCaptchaConfig::apply(Configuration::getValue('smart_captcha'));
Asset::getInstance()->addJs($captchaConfig->getScriptSource());
```
Отредактируйте код веб-формы, добавив к ней вывод контейнера капчи.
```php
use Bitrix\Main\Config\Configuration;
use PBorisenko\SpamProtection\SmartCaptcha;
use PBorisenko\SpamProtection\SmartCaptchaConfig;

SmartCaptcha::activate(
    '<идентификатор контейнера>',
    SmartCaptchaConfig::apply(Configuration::getValue('smart_captcha'))
);
```
Отредактируйте обработчик запроса, добавив проверку токена на валидность.
```php
use Bitrix\Main\Config\Configuration;
use PBorisenko\SpamProtection\SmartCaptcha;
use PBorisenko\SpamProtection\SmartCaptchaConfig;

$isValidToken = SmartCaptcha::check(
    '<ваш токен>',
    SmartCaptchaConfig::apply(Configuration::getValue('smart_captcha'))
);

if ($isValidToken) {
    // код обработчика в случае валидного токена
} else {
    // код обработчика во всех остальных случаях
}
```
### Использование невидимой капчи
При выводе контейнера в веб-форме необходимо явно указать модификатор режима `INVISIBLE`.
```php
use Bitrix\Main\Config\Configuration;
use PBorisenko\SpamProtection\SmartCaptcha;
use PBorisenko\SpamProtection\SmartCaptchaConfig;

SmartCaptcha::activate(
    '<идентификатор контейнера>',
    SmartCaptchaConfig::apply(Configuration::getValue('smart_captcha')),
    SmartCaptcha::INVISIBLE
);
```
Обязательно измените ваш обработчик формы (событие submit), таким образом, чтобы перед отправкой запроса была выполнена проверка на наличие токена и запуск проверки пользователя, если токен отсутствует.

__jQuery__
```JavaScript
const form //объект формы в DOM-дереве

$.ajax({
    type: "POST",
    url: '<адрес запроса>',
    data: '<тело запроса>',
    beforeSend: function (xhr, settings) {
        if (window.smartCaptcha && form.find('input[name="smart-token"]').val().length == 0) {
            window.smartCaptcha.execute(form.find('[data-wid]').attr('data-wid'));
            return false;
        }					
    },
    success: function (data) {/*обработчик ответа*/}
});
```
### Поддержка jQuery плагина FancyBox
Если веб-форма открывается в всплывающем окне, используйте режимы `FANCYBOX_VISIBLE` или `FANCYBOX_INVISIBLE` для капчи с виджетом или невидимой капчи соответственно.
```php
use PBorisenko\SpamProtection\SmartCaptcha;

SmartCaptcha::FACYBOX_VISIBLE;
SmartCaptcha::FANCYBOX_INVISIBLE;
```