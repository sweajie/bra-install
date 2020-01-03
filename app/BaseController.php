<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace app;

use app\bra\objects\BraModule;
use app\bra\objects\BraString;
use bra\BraCms;
use think\App;
use think\exception\ValidateException;
use think\Validate;

/**
 * 控制器基础类
 */
abstract class BaseController
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var App
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    /**
     * 构造方法
     * @access public
     * @param BraCms $app 应用对象
     */
    public function __construct(App $app)
    {
        global $_W;
        $app->get_bra_app();

        $bra_common = APP_PATH . 'bra' . DS . 'bracms.php';
        if (is_file($bra_common)) {
            include_once $bra_common;
        }

        if (strpos(ROUTE_A, 't__') !== false) {
            die('bra error:passable hack action!');
        }
        // get device
        $_W['DEVICE_TYPE'] = BraString::is_mobile() ? "mobile" : "desktop";
        $_W['siteroot'] = $app->request->domain() . "/";
        //完整URL
        $_W['current_url'] = $app->request->url(true);
        self::bra_input();
        $this->request = $app->request;
        A('system', BraModule::get_module_version('system'));
        $this->app = $app;
        $this->initialize();
    }

    /**
     * 输入数据处理
     */
    public static function bra_input()
    {
        global $_W, $_GPC;
        $_GPC = input('param.', null, 'trim');
        if (isset($_GPC['bra_q']) && !is_array($_GPC['bra_q'])) {
            $_GPC['bra_q'] = json_decode($_GPC['bra_q'], 1);
        }
        return $_GPC;
    }

    protected function initialize()
    {
    }

    // 初始化

    public function get_app()
    {
        dd();
    }

    public function is_bra_post()
    {
        global $_GPC, $_W;
        // collect form id
        if ($_W['mini_app']) {
            $form_id = $_GPC['query']['formId'] ?? ' ';
            if (!empty($form_id) && strpos($form_id, ' ') === false) {
                $data = [];
                $data['user_id'] = $_W['user']['id'];
                $data['media_id'] = $_W['mini_app']['id'];
                $data['form_id'] = $form_id;
                $data['create'] = time();
                D('wechat_fans_ticket')->insert($data);
            }
        }
        return $_GPC['bra_action'] == 'post';
    }

    public function isPost($check_token = false)
    {
        global $_GPC;
        $this->check_token = isset($this->check_token) ? $this->check_token : $check_token;
        $is_post = $this->request->isPost();
        if ($is_post) {
            if (isset($this->check_token) && $this->check_token == true) {
                $res = $_GPC['__token__'] ? $this->request->checkToken('__token__', $this->request->param()) : false;
                if (!$res) {
                    return bra_res(4000, "对不起，您的会话不合法 ，请刷新页面重试！");
                }
            }
        }
        return $is_post;
    }

    /**
     * 验证数据
     * @access protected
     * @param array $data 数据
     * @param string|array $validate 验证器名或者验证规则数组
     * @param array $message 提示信息
     * @param bool $batch 是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, $validate, array $message = [], bool $batch = false, $module = 'bra')
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                list($validate, $scene) = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $class = '\app\\' . $module . '\verify\\' . $validate;
            $v = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
    }
}
