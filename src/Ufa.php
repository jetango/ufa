<?php namespace Angejia\Ufa;

use Config;

class Ufa {
    // START: new functions when unifying.
    const SOURCE_EXTERNAL = 'external';
    const SOURCE_INTERNAL = 'internal';

    public $debug = false;
    public $dest_dir = 'dist/';
    public $compatible_ie = false;
    public $data = [];

    private $host = '/';

    private $device_types = ['mobile', 'wechat'];//TODO::Add later.
    private $default = 'mobile';
    private $name = 'page';
    private $params = array();

    function __construct() {
        $this->debug = Config::get('app.debug');
        $this->host = Config::get('page.host', '/');
        $this->dest_dir = $this->host . ($this->debug ? '' : 'dist/');
        $this->compatible_ie = Config::get('page.compatible_ie', false);
    }

    private $external_resources = array(
        'js' => array(),
        'css' => array()
    );
    private $internal_resources = array(
        'js' => array(),
        'css' => array()
    );

    public function init() {echo 'UFA<br>';
//        $this->debug = Config::get('app.debug');
//        $this->host = Config::get('page.host', '/');
//        $this->dest_dir = $this->host . ($this->debug ? '' : 'dist/');
//        $this->compatible_ie = Config::get('page.compatible_ie', false);
    }

    public function realPath($path, $resource_type = 'js', $dest_dir = null) {

        if ($this->debug) {
            $path .= '.' . $resource_type;
        } else {
            $path .= '.min.' . $resource_type;
        }

        $dest_dir = (null != $dest_dir) ? $dest_dir : $this->dest_dir;

        return $dest_dir . $path;

    }

    public function setName($name) {
        if (! $this->name) {
            $this->name = $name;
        }
    }

    public function getCompatible()
    {
        return $this->compatible_ie;
    }

    /**
     * Get suffix of resource.
     * @param $type
     * @return string. e.g.: app, mobile
     */
    private function get_suffix($type) {
        $type = $type ? $type : $this->default;
        $suffix = ($type === $this->default) ? '' : $type;
        return $suffix;
    }

    /**
     * Add Resources.
     *
     * @param $source: external or internal.
     * @param $resource_type: js or css.
     * @param $data
     * @param $client_type: app or mobile.
     */
    public function addResources($source, $resource_type, $data, $client_type) {
        if (! empty($data)) {
            if ($source === self::SOURCE_INTERNAL) {
                $resources = & $this->internal_resources[$resource_type];
            } else {
                $resources = & $this->external_resources[$resource_type];
            }

            $client_type = $client_type ? $client_type : $this->default;
            $suffix = self::get_suffix($client_type);
            if ($suffix) {
                foreach($data as &$val) {
                    $val .= '.' . $suffix;
                }
            }
            $resources[$client_type] = isset($resources[$client_type]) ? $resources[$client_type] : array();
            $resources[$client_type] = array_unique(array_merge(($resources[$client_type]), $data), SORT_REGULAR);
        }
    }

    /**
     * Get resources.
     *
     * @param $source
     * @param $resource_type
     * @param string $client_type
     * @return array
     */
    public function getResources($source, $resource_type, $client_type = '') {

        if ($source === self::SOURCE_INTERNAL) {
            $resources = & $this->internal_resources[$resource_type];
        } else {
            $resources = & $this->external_resources[$resource_type];
        }

        $client_type = $client_type ? $client_type : $this->default;

        return isset($resources[$client_type]) ? $resources[$client_type] : array();
    }

    /**
     * Get all loading resources.
     * @param string $client_type
     * @param bool $is_pure
     * @return array
     */
    public function loadResources($client_type = '', $is_pure = false) {

        $all_resources = [
            'js' => self::loadScripts($client_type, $is_pure),
            'css' => self::loadStyles($client_type, $is_pure)
        ];

        return $all_resources;
    }

    public function loadStyles($client_type = '', $is_pure = false) {
        $resources = [
            'internal' => $this->_loadTool(self::SOURCE_INTERNAL, 'css', $client_type, $is_pure),
            'external' => $this->_loadTool(self::SOURCE_EXTERNAL, 'css', $client_type, $is_pure),
        ];
        $this->_suffixTool($resources['internal'], 'css');
        $this->_suffixTool($resources['external'], 'css');
        return $resources;
    }

    public function loadScripts($client_type = '', $is_pure = false) {
        $resources = [
            'internal' => $this->_loadTool(self::SOURCE_INTERNAL, 'js', $client_type, $is_pure),
            'external' => $this->_loadTool(self::SOURCE_EXTERNAL, 'js', $client_type, $is_pure),
        ];
        $this->_suffixTool($resources['internal'], 'js');
        $this->_suffixTool($resources['external'], 'js');
        return $resources;
    }

    /**
     * Private: add suffix for each loading file.
     * @param $resources
     * @param $resource_type
     * @return mixed
     */
    private function _suffixTool(& $resources, $resource_type) {
        foreach($resources as & $val) {
            $val = $this->realPath($val, $resource_type, $this->dest_dir . $resource_type . '/');
        }
        return $resources;
    }

    /**
     * Private: get loading resources(without suffix)
     * @param $source
     * @param $resource_type
     * @param $client_type
     * @param bool $is_pure
     * @return array
     */
    private function _loadTool($source, $resource_type, $client_type, $is_pure = false) {
        if ($is_pure) {
            return $this->getResources($source, $resource_type, $client_type);
        }
        $default = $client_type ? $client_type : $this->default;
        if ($default != $this->default) {
            $resources = array_unique(array_merge(
                $this->getResources($source, $resource_type, ''),
                $this->getResources($source, $resource_type, $client_type)
            ), SORT_REGULAR);
        } else {
            $resources = $this->getResources($source, $resource_type, $default);
        }

        return $resources;
    }

    /**
     * @param $resource_type
     * @param array $data
     * @param string $client_type
     */
    public function addExternals($resource_type, $data = array(), $client_type = '') {
        self::addResources(self::SOURCE_EXTERNAL, $resource_type, $data, $client_type);
    }

    /**
     * @param $resource_type string js or css.
     * @param $client_type string mobile or wechat .etc.
     * @return array
     */
    public function getExternalResources($resource_type, $client_type = '') {
        return self::getResources(self::SOURCE_EXTERNAL, $resource_type, $client_type);
    }

    /**
     * @param $resource_type
     * @param array $data
     * @param string $client_type
     */
    public function addInternalResources($resource_type, $data = array(), $client_type = '') {
        self::addResources(self::SOURCE_INTERNAL, $resource_type, $data, $client_type);
    }

    /**
     * @param $resource_type string js or css.
     * @param $client_type string mobile or wechat .etc.
     * @return array
     */
    public function getInternalResources($resource_type, $client_type = '') {
        return self::getResources(self::SOURCE_INTERNAL, $resource_type, $client_type);
    }

    /**
     * @param array $data
     * @param string $type client type.
     */
    public function addExternalJs($data = array(), $type = '') {
        self::addExternals('js', $data, $type);
    }

    /**
     * @param array $data
     * @param string $type client type.
     */
    public function addExternalCss($data = array(), $type = '') {
        self::addExternals('css', $data, $type);
    }

    /**
     * @param array $data
     * @param string $type client type.
     */
    public function addInternalCss($data = array(), $type = '') {
        self::addInternalResources('css', $data, $type);
    }

    /**
     * @param array $data
     * @param string $type client type.
     */
    public function addInternalJs($data = array(), $type = '') {
        self::addInternalResources('js', $data, $type);
    }

    /**
     * Add parameters.
     * @param $value
     * @param string $key
     */
    public function addParam($value, $key = '') {
        if ($key) {
            $param = isset($this->params[$key]) ? $this->params[$key] : array();
            $this->params[$key] = array_merge($param, $value);
        } else {
            $params = array_merge($this->params, $value);
            $this->params = $params;
        }
    }

    /**
     * Get specified parameter.
     * @param $key
     * @return array
     */
    public function getParam($key) {
        return isset($this->params[$key]) ? $this->params[$key] : array();
    }

    /**
     * Get all parameters.
     * @return array
     */
    public function getParams() {
        return $this->params;
    }

    public function getData() {
        return $this->data;
    }

    public function __get($key) {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    public function __set($key, $value) {
        $this->data[$key] = $value;
    }

    /**
     *
     * @param $name
     * @param $args
     */
    public function __call($name, $args) {

    }

    /**
     * >= PHP 5.3.0
     * @param $name
     * @param $args
     */
    public static function __callStatic($name, $args) {

    }

    // END: new functions when unifying.

    // functions below in CRM, Agent, TW, PC, Bureau
}