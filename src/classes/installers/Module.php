<?php

namespace mmaurice\PaymentLinksWizard\classes\installers;

use mmaurice\PaymentLinksWizard\Wizard;
use mmaurice\PaymentLinksWizard\classes\Installer;
use mmaurice\PaymentLinksWizard\installers\PluginInstaller;

abstract class Module extends Installer
{
    protected $name;
    protected $description;
    protected $editorType = 0;
    protected $disabled = 0;
    protected $category = 0;
    protected $wrap = 0;
    protected $locked = 0;
    protected $icon;
    protected $enableResource = 0;
    protected $resourcefile;
    protected $createdon = 0;
    protected $editedon = 0;
    protected $guid;
    protected $enableSharedparams = 0;
    protected $properties;
    protected $modulecode;

    public function __construct()
    {
        $this->createdon = time();
        $this->editedon = time();
    }

    public function getModuleId()
    {
        return (new Wizard)->getItem([
            'from' => 'site_modules',
            'where' => [
                "`name` = '{$this->name}'",
            ],
        ]);
    }

    public function install()
    {
        $moduleId = (new Wizard)->insert('site_modules', array_filter([
            'name' => $this->name,
            'description' => $this->description,
            'editor_type' => $this->editorType,
            'disabled' => $this->disabled,
            'category' => $this->category,
            'wrap' => $this->wrap,
            'locked' => $this->locked,
            'icon' => $this->icon,
            'enable_resource' => $this->enableResource,
            'resourcefile' => $this->resourcefile,
            'moduleguid' => $this->moduleguid,
            'createdon' => $this->createdon,
            'editedon' => $this->editedon,
            'guid' => $this->guid,
            'enable_sharedparams' => $this->enableSharedparams,
            'properties' => json_encode($this->properties, JSON_UNESCAPED_UNICODE),
            'modulecode' => $this->modulecode,
        ]));

        $plugin = (new PluginInstaller)->getPluginId();

        if (isset($plugin['id'])) {
            (new Wizard)->insert('site_module_depobj', array_filter([
                'module' => $moduleId,
                'resource' => $plugin['id'],
                'type' => 30,
            ]));
        }

        return $moduleId;
    }
}