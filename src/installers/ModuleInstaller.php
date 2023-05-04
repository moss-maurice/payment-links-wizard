<?php

namespace mmaurice\PaymentLinksWizard\installers;

use mmaurice\PaymentLinksWizard\classes\installers\Module;

final class ModuleInstaller extends Module
{
    protected $name = 'Генератор ссылок на оплату';
    protected $description = '<strong>0.1</strong> Модуль управления генератором ссылок на оплату';
    protected $icon = 'fa fa-ruble-sign';
    protected $guid = 'b2a6046a707a31b146eeb4c7d5f235f3';
    protected $properties = [
        'pageHandler' => [
            [
                'label' => 'Префикс ссылки',
                'type' => 'string',
                'value' => '',
                'default' => '',
                'desc' => 'Можно указать как строковое значение ссылки, так и ID страницы, чей alias необходимо взять для ссылки',
            ],
        ],
    ];
    protected $modulecode = 'require_once(MODX_BASE_PATH . \\\'/assets/modules/payLinks/module.php\\\');\r\n';

    public function install()
    {
        shell_exec("composer create-project mmaurice/payment-links-module {$_SERVER['DOCUMENT_ROOT']}/assets/modules/payLinks");

        parent::install();
    }
}