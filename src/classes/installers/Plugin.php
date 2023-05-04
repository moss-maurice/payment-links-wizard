<?php

namespace mmaurice\PaymentLinksWizard\classes\installers;

use mmaurice\PaymentLinksWizard\Wizard;
use mmaurice\PaymentLinksWizard\classes\Installer;

abstract class Plugin extends Installer
{
    protected $name;
    protected $description;
    protected $editorType = 0;
    protected $category = 0;
    protected $cacheType = 0;
    protected $plugincode;
    protected $locked = 0;
    protected $properties;
    protected $disabled = 0;
    protected $moduleguid;
    protected $createdon = 0;
    protected $editedon = 0;

    protected $events = [];

    public function __construct()
    {
        $this->createdon = time();
        $this->editedon = time();
    }

    public function getPluginId()
    {
        return (new Wizard)->getItem([
            'from' => 'site_plugins',
            'where' => [
                "`name` = '{$this->name}'",
            ],
        ]);
    }

    public function install()
    {
        $pluginId = (new Wizard)->insert('site_plugins', array_filter([
            'name' => $this->name,
            'description' => $this->description,
            'editor_type' => $this->editorType,
            'category' => $this->category,
            'cache_type' => $this->cacheType,
            'plugincode' => $this->plugincode,
            'locked' => $this->locked,
            'properties' => json_encode($this->properties),
            'disabled' => $this->disabled,
            'moduleguid' => $this->moduleguid,
            'createdon' => $this->createdon,
            'editedon' => $this->editedon,
        ]));

        if (is_array($this->events) and !empty($this->events)) {
            foreach ($this->events as $eventId) {
                (new Wizard)->insert('site_plugin_events', array_filter([
                    'pluginid' => $pluginId,
                    'evtid' => $eventId,
                    'priority' => 0,
                ]));
            }
        }

        return $pluginId;
    }
}