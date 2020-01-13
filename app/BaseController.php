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
use think\helper\Str;
use think\Validate;

/**
 * 控制器基础类
 */
abstract class BaseController {
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
	public function __construct (App $app) {
		global $_W;
		$_W['bra_scripts'] = [];
		$this->get_bra_app($app);

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
		$_W['share_url'] = $_W['current_url'] = $app->request->url(true);


		self::bra_input();
		$this->request = $app->request;
		A('system', BraModule::get_module_version('system'));
		$this->app = $app;
		$this->initialize();
	}

	public function get_bra_app (App $app) {
		$app_name = ltrim($app->request->root(), '/');
		$app_name = empty($app_name) ? config('app.default_app') : $app_name;
		//common used static
		define("ROUTE_M", $app_name);
		define("ROUTE_C", Str::snake($app->request->controller()));
		define("ROUTE_A", $app->request->action());
		if (strpos(ROUTE_A, '__') !== false) {
			dd();
		}
		define('BRA_PATH', $app->getRootPath());
		define('APP_PATH', $app->getBasePath());
		define('CONF_PATH', $app->getConfigPath());
	}

	/**
	 * 输入数据处理
	 */
	public static function bra_input () {
		global $_W, $_GPC;
		$_GPC = input('param.', null, 'trim');
		if (isset($_GPC['bra_q']) && !is_array($_GPC['bra_q'])) {
			$_GPC['bra_q'] = json_decode($_GPC['bra_q'], 1);
		}

		return $_GPC;
	}

	protected function initialize () {
	}

	// 初始化

	public function get_app () {
		dd();
	}

	public function is_bra_post () {
		global $_GPC, $_W;
		//todo collect form id
		if (!empty($_W['mini_app'])) {
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

	public function isPost ($check_token = false) {
		if ($this->request->isPost()) {
			if (!$check_token) {
				return $this->request->isPost();
			} else {
				if (false === check_token()) {
					$msg = "对不起，您的请勿重复提交，请刷新页面重试！";
					bra_end_resp(4000, $msg);
				} else {
					return true;
				}
			}
		} else {
			return false;
		}
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
	protected function validate (array $data, $validate, array $message = [], bool $batch = false, $module = 'bra') {
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
