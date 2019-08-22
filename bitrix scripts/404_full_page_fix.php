//init.php

AddEventHandler('main', 'OnEpilog', 'controller404', 1001);

function controller404() {
    if(defined('ERROR_404') && ERROR_404 == 'Y') {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();
        require(\Bitrix\Main\Application::getDocumentRoot()."/local/templates/bitlate_sport/header.php");
        require(\Bitrix\Main\Application::getDocumentRoot()."/404.php");
        require(\Bitrix\Main\Application::getDocumentRoot()."/local/templates/bitlate_sport/footer.php");
        die();
    }
}