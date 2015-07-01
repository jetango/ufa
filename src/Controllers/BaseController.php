<?php namespace Angejia\Ufa\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller as LaravelController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Config;
use Angejia\Ufa\Helpers\Resource as Resource;

abstract class BaseController extends LaravelController {

    use DispatchesCommands, ValidatesRequests;

    const CONST_WEILIAO = 'weiliao';
    const CONST_APP = 'app';
    const CONST_TOUCH = 'touch';
    const CONST_WECHAT = 'wechat';
    const CONST_BROWSER = 'browser';
    const CONST_MOBILE = 'mobile';

    protected $title = '';
    protected $file_css = '';
    protected $file_js = '';
    protected $dist_dir = '';
    protected $log_params;

    /**
     * Note: Please Don't make Any changes here.
     * Common view.
     *
     * @param $view
     * @param array $data
     * @return mixed
     */
    protected function view($view, $data = array())
    {
        Resource::init();
        if ($this->file_js) {
            Resource::add_internal_js(array($this->file_js));
        }

        if ($this->file_css) {
            Resource::add_internal_css(array($this->file_css));
        }

        return view($view,
            array_merge(
                array(
                    'debug' => Resource::$debug,
                    'title' => $this->title,
                    'dest_dir' => Resource::$dest_dir,
                ),
                $this->load_more_view_data(),
                $data
            )
        );
    }

    /**
     * Put others data in this function.
     * @return array
     */
    protected function load_more_view_data() {
        return [
            'menu_config' => Config::get('menu'),
        ];
    }

    /**
     * Get client type.
     * @return string: browser/mobile/wechat/app
     */
    private function client_type()
    {
        $user_agent = Request::header('User-Agent');
        $client = self::CONST_BROWSER;

        // App
        if (preg_match('/ClientType\/APP/', $user_agent) || self::CONST_WEILIAO === Request::get('from')) {
            return self::CONST_APP;
        }

        // Mobile
        if (preg_match('/Mobile/', $user_agent)) {
            // wechat
            $client = self::CONST_MOBILE;
            if (preg_match('/MicroMessenger|NetType/', $user_agent)) {
                $client = self::CONST_WECHAT;
            }
        }

        return $client;
    }
}