<?php

namespace mmaurice\PaymentLinksWizard;

use mmaurice\PaymentLinksWizard\classes\WizardCore;
use mmaurice\PaymentLinksWizard\installers\ModuleInstaller;
use mmaurice\PaymentLinksWizard\installers\PluginInstaller;

final class Wizard extends WizardCore
{
    public function start()
    {
        $this->render('start');
    }

    public function finish()
    {
        (new PluginInstaller)->install();
        (new ModuleInstaller)->install();

        $this->render('finish');
    }

    public function fail()
    {
        $this->render('fail');
    }

    public function setup()
    {
        $this->render('setup');
    }

    public function remove()
    {
        array_map(function ($value) {
            unlink($value);
        }, [
            realpath(ROOT),
            realpath($_SERVER['SCRIPT_FILENAME']),
        ]);

        $main = "{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['HTTP_HOST']}/";

        header("Location: {$main}");

        echo "<script>window.location.replace('{$main}');</script>";
    }
}