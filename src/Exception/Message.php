<?php

namespace PBorisenko\SpamProtection\Exception;

class Message
{
    const UNKNOWN_CAPTCHA_MODE = "Передан неизвестный режим отображения капчи";

    const UNDEFINED_SITEKEY_CAPTCHA = "Не определён конфигурационный параметр 'sitekey' для капчи";
    
    const UNDEFINED_SERVERKEY_CAPTCHA = "Не определён конфигурационный параметр 'serverkey' для капчи";
    
    const UNDEFINED_ENDPOINT_CAPTCHA = "В конфигурации не определён URL для проверки капчи";
    
    const UNDEFINED_SHIELDPOSITION_CAPTCHA = "В конфигурации на определён параметр расположения шилдика для невидимой капчи";
    
    const UNDEFINED_SCRIPTSOURCE_CAPTCHA = "В конфигурации не определён URL для загрузки скрипта капчи";
    
    const UNDEFINED_DEBUG_CAPTCHA = "В конфигурации не определён параметр запуска капчи в режиме отладки";

    const UNDEFINED_HIDESHIELD_CAPTCHA = "В конфигурации не определён параметр отображения шилдика для невидимой капчи";    
}