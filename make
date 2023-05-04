#!/usr/bin/env php
<?php

final class Builder
{
    protected $source;
    protected $phar;

    public function __construct($source)
    {
        ini_set('phar.readonly', 0);

        $this->source = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, realpath(dirname(__FILE__)) . $source);

        if (!$this->source) {
            throw new Exception('Source path is not found');
        }
    }

    protected function flush()
    {
        if (!realpath(pathinfo($this->phar, PATHINFO_DIRNAME))) {
            mkdir(pathinfo($this->phar, PATHINFO_DIRNAME), 0777, true);
        }

        if (realpath(pathinfo($this->phar, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . 'index.php')) {
            unlink(realpath(pathinfo($this->phar, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . 'index.php'));
        }

        if (realpath($this->phar)) {
            unlink($this->phar);
        }

        $this->cleanUp();
    }

    protected function cleanUp()
    {
        if (realpath("{$this->phar}.gz")) {
            unlink("{$this->phar}.gz");
        }
    }

    public function make($bootstrap, $phar)
    {
        $this->phar = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, realpath(dirname(__FILE__)) . $phar);

        $this->flush();

        $pharObject = new Phar($this->phar);

        $pharObject->buildFromDirectory($this->source);

        $pharObject->setDefaultStub($bootstrap, "/{$bootstrap}");

        $pharObject->compress(Phar::GZ);

        file_put_contents(pathinfo($this->phar, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . 'index.php', '<?php include "phar://".__DIR__."/wizard.phar"; ?>');

        $this->cleanUp();

        return realpath($this->phar);
    }
}

$builder = new Builder('/src');
$phar = $builder->make('bootstrap/index.php', '/dist/wizard.phar');

echo ($phar ? "{$phar} successfully created" : "Creating error") . PHP_EOL;