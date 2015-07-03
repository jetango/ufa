<?php namespace Angejia\Ufa;

use Config;

class Ufa {
    // START: new functions when unifying.
    const SOURCE_EXTERNAL = 'external';
    const SOURCE_INTERNAL = 'internal';

    private $debug = false;
    private $host = '/';
    private $dest_dir = 'dist/';
    private $compatible_ie = false;

    private $device_types = ['mobile', 'wechat'];//TODO::Add later.
    private static $default = 'mobile';
    private static $params = array();

    function __construct() {
        echo 'Construct<br>';
        $this->debug = Config::get('app.debug');
        $this->host = Config::get('page.host', '/');
        $this->dest_dir = $this->host . ($this->debug ? '' : 'dist/');
        $this->compatible_ie = Config::get('page.compatible_ie', false);
        echo $this->host, '<br>';
    }

    private $external_resources = array(
        'js' => array(),
        'css' => array()
    );
    private $internal_resources = array(
        'js' => array(),
        'css' => array()
    );

    public static function init() {echo 'UFA<br>';
//        $this->debug = Config::get('app.debug');
//        $this->host = Config::get('page.host', '/');
//        $this->dest_dir = $this->host . ($this->debug ? '' : 'dist/');
//        $this->compatible_ie = Config::get('page.compatible_ie', false);
    }

    public static function path($path, $resource_type) {

        if (self::$debug) {
            $path .= '.' . $resource_type;
        } else {
            $path .= '.min.' . $resource_type;
        }

        return self::$dest_dir . $path;

    }

    public static function getCompatible()
    {
        return self::$compatible_ie;
    }

    /**
     * Get suffix of resource.
     * @param $type
     * @return string. e.g.: app, mobile
     */
    private function get_suffix($type) {
        $type = $type ? $type : $this->$default;
        $suffix = ($type === $this->$default) ? '' : $type;
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
    public static function add_resources($source, $resource_type, $data, $client_type) {
        if (! empty($data)) {
            if ($source === self::SOURCE_INTERNAL) {
                $resources = & $this->$internal_resources[$resource_type];
            } else {
                $resources = & self::$external_resources[$resource_type];
            }

            $client_type = $client_type ? $client_type : self::$default;
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
    public static function get_resources($source, $resource_type, $client_type = '') {

        if ($source === self::SOURCE_INTERNAL) {
            $resources = & self::$internal_resources[$resource_type];
        } else {
            $resources = & self::$external_resources[$resource_type];
        }

        $client_type = $client_type ? $client_type : self::$default;

        return isset($resources[$client_type]) ? $resources[$client_type] : array();
    }

    /**
     * Get all loading resources.
     * @param string $client_type
     * @param bool $is_pure
     * @return array
     */
    public static function load_resources($client_type = '', $is_pure = false) {

        $all_resources = [
            'js' => self::load_scripts($client_type, $is_pure),
            'css' => self::load_styles($client_type, $is_pure)
        ];

        return $all_resources;
    }

    public static function load_styles($client_type = '', $is_pure = false) {
        $resources = [
            'internal' => self::_load_tool(self::SOURCE_INTERNAL, 'css', $client_type, $is_pure),
            'external' => self::_load_tool(self::SOURCE_EXTERNAL, 'css', $client_type, $is_pure),
        ];
        self::_suffix_tool($resources['internal'], 'css');
        self::_suffix_tool($resources['external'], 'css');
        return $resources;
    }

    public static function load_scripts($client_type = '', $is_pure = false) {
        $resources = [
            'internal' => self::_load_tool(self::SOURCE_INTERNAL, 'js', $client_type, $is_pure),
            'external' => self::_load_tool(self::SOURCE_EXTERNAL, 'js', $client_type, $is_pure),
        ];
        self::_suffix_tool($resources['internal'], 'js');
        self::_suffix_tool($resources['external'], 'js');
        return $resources;
    }

    /**
     * Private: add suffix for each loading file.
     * @param $resources
     * @param $resource_type
     * @return mixed
     */
    private function _suffix_tool(& $resources, $resource_type) {
        if ($this->debug) {
            $file_suffix = '.' . $resource_type . '?' . time();
        } else {
            $file_suffix = '.min.' . $resource_type;
        }

        foreach($resources as & $val) {
            $val .= $file_suffix;
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
    private function _load_tool($source, $resource_type, $client_type, $is_pure = false) {
        if ($is_pure) {
            return $this->get_resources($source, $resource_type, $client_type);
        }
        $default = $client_type ? $client_type : $this->$default;
        if ($default != $this->$default) {
            $resources = array_unique(array_merge(
                $this->get_resources($source, $resource_type, ''),
                $this->get_resources($source, $resource_type, $client_type)
            ), SORT_REGULAR);
        } else {
            $resources = $this->get_resources($source, $resource_type, $default);
        }

        return $resources;
    }

    /**
     * @param $resource_type
     * @param array $data
     * @param string $client_type
     */
    public function add_external_resources($resource_type, $data = array(), $client_type = '') {
        self::add_resources(self::SOURCE_EXTERNAL, $resource_type, $data, $client_type);
    }

    /**
     * @param $resource_type string js or css.
     * @param $client_type string mobile or wechat .etc.
     * @return array
     */
    public static function get_external_resources($resource_type, $client_type = '') {
        return self::get_resources(self::SOURCE_EXTERNAL, $resource_type, $client_type);
    }

    /**
     * @param $resource_type
     * @param array $data
     * @param string $client_type
     */
    public static function add_internal_resources($resource_type, $data = array(), $client_type = '') {
        self::add_resources(self::SOURCE_INTERNAL, $resource_type, $data, $client_type);
    }

    /**
     * @param $resource_type string js or css.
     * @param $client_type string mobile or wechat .etc.
     * @return array
     */
    public static function get_internal_resources($resource_type, $client_type = '') {
        return self::get_resources(self::SOURCE_INTERNAL, $resource_type, $client_type);
    }

    /**
     * @param array $data
     * @param string $type client type.
     */
    public static function add_external_js($data = array(), $type = '') {
        self::add_external_resources('js', $data, $type);
    }

    /**
     * @param array $data
     * @param string $type client type.
     */
    public static function add_external_css($data = array(), $type = '') {
        self::add_external_resources('css', $data, $type);
    }

    /**
     * @param array $data
     * @param string $type client type.
     */
    public static function add_internal_css($data = array(), $type = '') {
        self::add_internal_resources('css', $data, $type);
    }

    /**
     * @param array $data
     * @param string $type client type.
     */
    public static function add_internal_js($data = array(), $type = '') {
        self::add_internal_resources('js', $data, $type);
    }

    /**
     * Add parameters.
     * @param $value
     * @param string $key
     */
    public static function add_param($value, $key = '') {
        if ($key) {
            $param = isset(self::$params[$key]) ? self::$params[$key] : array();
            self::$params[$key] = array_merge($param, $value);
        } else {
            $params = array_merge(self::$params, $value);
            self::$params = $params;
        }
    }

    /**
     * Get specified parameter.
     * @param $key
     * @return array
     */
    public static function get_param($key) {
        return isset(self::$params[$key]) ? self::$params[$key] : array();
    }

    /**
     * Get all parameters.
     * @return array
     */
    public static function get_params() {
        return self::$params;
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