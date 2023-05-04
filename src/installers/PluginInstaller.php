<?php

namespace mmaurice\PaymentLinksWizard\installers;

use mmaurice\PaymentLinksWizard\classes\installers\Plugin;

final class PluginInstaller extends Plugin
{
    protected $name = 'Payment Links Handler';
    protected $description = '<strong>0.1</strong> Плагин управления платежными ссылками';
    protected $plugincode = 'require_once(MODX_BASE_PATH . \\\'/assets/plugins/payLinks/plugin.php\\\');\r\n';
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

    protected $events = [
        1000,       // OnPageNotFound
    ];

    public function install()
    {
        shell_exec("composer create-project mmaurice/payment-links-plugin {$_SERVER['DOCUMENT_ROOT']}/assets/plugins/payLinks");

        parent::install();
    }
}