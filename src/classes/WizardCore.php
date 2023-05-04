<?php

namespace mmaurice\PaymentLinksWizard\classes;

abstract class WizardCore
{
    protected static $modx;

    public function __construct($path = "/index.php")
    {
        if (!defined('MODX_API_MODE')) {
            define('MODX_API_MODE', true);
        }

        if (!defined('MODX_BASE_PATH')) {
            define('MODX_BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/');
        }

        if (!defined('MODX_BASE_URL')) {
            define('MODX_BASE_URL', "{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['HTTP_HOST']}/");
        }

        if (!defined('MODX_SITE_URL')) {
            define('MODX_BASE_URL', "{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['HTTP_HOST']}/");
        }

        global $modx;
        global $database_type;
        global $database_server;
        global $database_user;
        global $database_password;
        global $database_connection_charset;
        global $database_connection_method;
        global $dbase;
        global $table_prefix;
        global $base_url;
        global $base_path;

        if (isset($modx) and !empty($modx)) {
            $this->modx = $modx;

            return $this->modx;
        }

        @include_once(realpath($_SERVER['DOCUMENT_ROOT'] . $path));

        $modx->db->connect();

        if (empty($modx->config)) {
            $modx->getSettings();
        }

        $this->modx = $modx;

        return $this->modx;
    }

    public function getList(array $filter = [])
    {
        $resource = $this->search($filter, $log);

        if (!is_null($resource) and $this->modx->db->getRecordCount($resource)) {
            $results = [];

            while ($row = $this->modx->db->getRow($resource)) {
                $results[] = $row;
            }

            return $results;
        }

        return null;
    }

    public function getItem(array $filter = [])
    {
        $resource = $this->search($filter, $log);

        if (!is_null($resource) and $this->modx->db->getRecordCount($resource)) {
            return $this->modx->db->getRow($resource);
        }

        return null;
    }

    public function search(array $filter = [])
    {
        $query = $this->getRawSql($filter);

        if (!is_null($query)) {
            return $this->modx->db->query($query);
        }

        return null;
    }

    public function getRawSql(array $filter = [])
    {
        if (!array_key_exists('alias', $filter)) {
            $filter['alias'] = '';
        }

        if (!array_key_exists('select', $filter) or is_null($filter['select']) or empty($filter['select'])) {
            $filter['select'] = (!empty($filter['alias']) ? "{$filter['alias']}." : '') . '*';
        }

        if (!is_array($filter['select'])) {
            $filter['select'] = [$filter['select']];
        }

        if (!array_key_exists('from', $filter) or empty($filter['from'])) {
            return null;
        } else {
            $filter['from'] = $this->modx->getFullTableName($filter['from']);
        }

        return "SELECT" . PHP_EOL
            . "\t" . implode("," . PHP_EOL . "\t", $filter['select']) . PHP_EOL
            . "FROM " . trim("{$filter['from']} {$filter['alias']}") . PHP_EOL
            . (!empty($filter['join']) ? implode(PHP_EOL, $filter['join']) . PHP_EOL : "")
            . (!empty($filter['where']) ? "WHERE" . PHP_EOL . "\t" . implode(PHP_EOL . "\t", $filter['where']) . PHP_EOL : "")
            . (!empty($filter['group']) ? "GROUP BY" . PHP_EOL . "\t" . implode("," . PHP_EOL . "\t", $filter['group']) . PHP_EOL : "")
            . (!empty($filter['having']) ? "HAVING" . PHP_EOL . "\t" . implode(PHP_EOL . "\t", $filter['having']) . PHP_EOL : "")
            . (!empty($filter['order']) ? "ORDER BY" . PHP_EOL . "\t" . implode("," . PHP_EOL . "\t", $filter['order']) . PHP_EOL : "")
            . (!empty($filter['limit']) ? "LIMIT " . intval($filter['limit']) . PHP_EOL : "")
            . (!empty($filter['offset']) ? "OFFSET " . intval($filter['offset']) . PHP_EOL : "");
    }

    public function insert($table, array $fields)
    {
        $fields = array_filter($fields);

        if (is_array($fields) and !empty($fields)) {
            $sql = "INSERT
                INTO " . $this->modx->getFullTableName($table) . "
                    (`" . implode("`, `", array_keys($fields)) . "`)
                VALUES
                    ('" . implode("', '", array_values($fields)) . "');";

            if ($this->modx->db->query($sql)) {
                return $this->modx->db->getInsertId();
            }
        }

        return null;
    }

    protected static function makeTemplate($__tplName__, $__variables__ = [])
    {
        $__tplName__ = trim($__tplName__);
        $__tplPath__ = static::getTemplateFullPath($__tplName__);

        if (!file_exists($__tplPath__) or !is_file($__tplPath__)) {
            die("Template file \"{$__tplName__}\" is not found!");
        }

        extract($__variables__, EXTR_PREFIX_SAME, 'data');

        ob_start();

        ob_implicit_flush(false);
        include($__tplPath__);

        $content = ob_get_clean();

        return $content;
    }

    protected static function getTemplateFullPath($tplName)
    {
        $path = dirname(__FILE__) . '/../templates/' . $tplName . '.php';

        return $path;
    }

    protected static function render($tplName, $variables = [], $die = true)
    {
        echo static::makeTemplate($tplName, $variables);

        if ($die) {
            die();
        }
    }

    public function run()
    {
        if (isset($_POST['next']) or isset($_POST['prev'])) {
            if (isset($_POST['next'])) {
                $method = $_POST['next_stage'];
            } else if (isset($_POST['prev'])) {
                $method = $_POST['prev_stage'];
            } else {
                return $this->fail();
            }

            if (method_exists($this, $method)) {
                return $this->$method();
            }

            return $this->fail();
        }

        return $this->start();


        if (!isset($_POST['next']) and !isset($_POST['prev'])) {
            return $this->start();
        } else {
            
        }
    }

    abstract public function start();

    abstract public function finish();

    abstract public function fail();
}