<?php

namespace app\install\controller;

use app\BaseController;
use app\bra\objects\BraCurl;
use app\bra\objects\BraFS;
use app\bra\objects\BraString;
use app\install\utils\Install;
use think\exception\ValidateException;
use think\facade\Cache;
use think\facade\Cookie;
use think\facade\Db;
use think\facade\View;

error_reporting(0);

class Index extends BaseController
{
    public $cms_name = '布拉CMS';

    public $error_msg = '';

    public function initialize()
    {
        parent::initialize();
        if (file_exists(CONF_PATH . 'install.lock')) {
            die("请删除文件config目录下面的" . 'install.lock文件');
        }
        $this->view = new View();
        if (phpversion() < '7.1') exit('您的php版本过低，不能安装本软件，请升级到7.1或更高版本再安装，谢谢！');
        if (file_exists(CONF_PATH . 'install.lock')) exit('您已经安装过BRACMS,如果需要重新安装，请删除 ' . CONF_PATH . "/install.lock 文件！");
        //MBstring
        if (!extension_loaded('PDO')) {
            $this->error_msg = "需要安装扩展 PDO";
        }

        if (!extension_loaded('MBstring')) {
            $this->error_msg = "需要安装扩展 MBstring";
        }

        if (!extension_loaded('CURL')) {
            $this->error_msg = "需要安装扩展 CURL";
        }
        if (!empty($this->error_msg)) {
            dd("您好，发生了错误：" . $this->cms_name . $this->error_msg);
        }
        $view_path = APP_PATH . strtolower('install') . DS . 'view' . DS;
        View::config([
            'view_path' => $view_path,
        ]);
    }

    public function index()
    {
        return View::fetch();
    }

    private function checkNnv()
    {
        $items = [
            'os' => ['操作系统', '不限制', '类Unix', PHP_OS, 'ok'],
            'php' => ['PHP版本', '7.1', '7.1及以上', PHP_VERSION, 'ok'],
            'gd' => ['GD库', '2.0', '2.0及以上', '未知', 'ok'],
        ];
        if ($items['php'][3] < $items['php'][1]) {
            $items['php'][4] = 'no';
        }
        $tmp = function_exists('gd_info') ? gd_info() : [];
        if (empty($tmp['GD Version'])) {
            $items['gd'][3] = '未安装';
            $items['gd'][4] = 'no';
        } else {
            $items['gd'][3] = $tmp['GD Version'];
        }
        return $items;
    }

    public static function checkDir()
    {
        $chmod_file = "chmod.txt";
        $files = file(CONF_PATH . $chmod_file);
        foreach ($files as $_k => $file) {
            $file = str_replace('*', '', $file);
            $file = trim($file);
            if (is_dir(BRA_PATH . $file)) {
                $is_dir = '1';
                $cname = '目录';
                //继续检查子目录权限，新加函数
                $write_able = BraFS::writable_check(BRA_PATH . $file);
            } else {
                $is_dir = '0';
                $cname = '文件';
            }
            //新的判断
            if ($is_dir == '0' && is_writable(BRA_PATH . $file)) {
                $is_writable = 1;
            } elseif ($is_dir == '1' && BraFS::dir_writeable(BRA_PATH . $file)) {
                $is_writable = $write_able;
                if ($is_writable == '0') {
                    $no_writablefile = 1;
                }
            } else {
                $is_writable = 0;
                $no_writablefile = 1;
            }
            $filesmod[$_k]['file'] = $file;
            $filesmod[$_k]['is_dir'] = $is_dir;
            $filesmod[$_k]['cname'] = $cname;
            $filesmod[$_k]['is_writable'] = $is_writable;
        }
        return $filesmod;
    }

    private function checkFunc()
    {
        $items = [
            ['pdo', '支持', 'yes', '类'],
            ['pdo_mysql', '支持', 'yes', '模块'],
            ['fileinfo', '支持', 'yes', '模块'],
            ['curl', '支持', 'yes', '模块'],
            ['xml', '支持', 'yes', '函数'],
            ['file_get_contents', '支持', 'yes', '函数'],
            ['mb_strlen', '支持', 'yes', '函数'],
            ['gzopen', '支持', 'yes', '函数'],
        ];
        foreach ($items as &$v) {
            if (('类' == $v[3] && !class_exists($v[0])) || ('模块' == $v[3] && !extension_loaded($v[0])) || ('函数' == $v[3] && !function_exists($v[0]))) {
                $v[1] = '不支持';
                $v[2] = 'no';
                session('install_error', true);
            }
        }
        return $items;
    }

    /**
     * 数据库信息 安装
     */
    public function do_step_2()
    {
        global $_GPC;
        $_GPC = input('param.');
        if ($this->request->isPost()) {
            if (file_exists(CONF_PATH . 'database.php') && !is_writable(CONF_PATH . 'database.php')) {
                $ret['code'] = 500;
                $ret['msg'] = CONF_PATH . 'database.php 无读写权限！';
                return $ret;
            }

            $_GPC['type'] = 'mysql';
            $rule = [
                'hostname|服务器地址' => 'require',
                'hostport|数据库端口' => 'require|number',
                'database|数据库名称' => 'require',
                'username|数据库账号' => 'require',
                'password|数据库密码' => 'require',
                'prefix|数据库前缀' => 'require|regex:^[a-z0-9]{1,20}[_]{1}',
                'cover|覆盖数据库' => 'require|in:0,1',
            ];
            try {
                $this->validate($_GPC, $rule);
            } catch (ValidateException $e) {
                $ret['msg'] = $e->getMessage();
                $ret['code'] = 500;
                return $ret;// 验证失败 输出错误信息
            }
            $database = $_GPC['database'];
            // 生成配置文件
            self::write_db_config($_GPC);


            // 创建数据库连接
            $db_connect = Db::connect('mysql');

            try {
                $db_connect->execute("CREATE DATABASE IF NOT EXISTS `{$database}` DEFAULT CHARACTER SET utf8");
            } catch (\Exception $e) {
                $ret['msg'] = $e->getMessage();
                $ret['code'] = 500;
                return $ret;// 验证失败 输出错误信息
            }

            try {
                $test = $db_connect->execute(" show databases like '$database';");
            } catch (\Exception $e) {
                $ret['msg'] = $e->getMessage();
                $ret['code'] = 500;
                return $ret;// 验证失败 输出错误信息
            }
            return ['code' => 1, 'msg' => '数据库连接成功'];
        }
    }

    public function start_install($step = 1)
    {
        global $_GPC;
        $_GPC = input('param.');
        //执行当前安装
        if ($this->isPost()) {
            $action_step = "do_step_" . ($step - 1);
            if ($action_step) {
                $this->$action_step();
            }
        }
        //并展示下一步安装界面
        switch ($step) {
            case 1:
                $data = [];
                $data['env'] = self::checkNnv();
                $data['dir'] = self::checkDir();
                $data['func'] = self::checkFunc();
                View::assign('data', $data);
                break;
            case 2:
                //填写授权码
                $licence = config('licence');
                View::assign('product_licence_code', $licence['licence']['product_licence_code']);
                break;
            case 3:

                break;
            case 4:
                break;
            case 5:
                // 获取系统需要更新的表

                $this->do_step_4();
                break;
            case 6 :


                $res1 = Db::name('users')->where(['id' => 1])->find();
                if (!$res1) {
                    // create admin account
                    $admin_info = Cache::get('admin_install_info');
                    $user_data['id'] = 1;
                    $user_data['user_name'] = $admin_info['user_name'];
                    $user_data['site_id'] = 0;
                    $user_data['nickname'] = "超级管理员";
                    $user_data['user_crypt'] = BraString::random(6);
                    $user_data['pass'] = BraString::crypt_pass($admin_info['password'], $user_data['user_crypt']);
                    $user_data['status'] = 99;
                    $user_data['role_id'] = 1;
                    $user_data['create_at'] = date("Y-m-d H:i:s");
                    $res1 = Db::name('users')->insert($user_data, false, true);
                    Cache::set('admin_install_info', null);
                }


                //创建root
                $res2 = Db::name('roots')->where(['id' => 1])->find();

                $domain_info = explode(".", $_SERVER['HTTP_HOST']);
                if (!$res2) {
                    $root_info = [];
                    $root_info['id'] = 1;
                    $root_info['site_id'] = 1;
                    $root_info['title'] = "默认域名";

                    if (count($domain_info) == 2) {
                        $root_info['root_domain'] = $_SERVER['HTTP_HOST'];
                    }

                    if (count($domain_info) == 3) {
                        $root_info['root_domain'] = $domain_info[1] . "." . $domain_info[2];
                    }
                    $root_id = Db::name('roots')->insert($root_info,  true);
                }else{
                    $root_id = $res2['id'];
                }
                //site root

                $res3 = Db::name('sites')->where(['id' => 1])->find();
                if (!$res3) {
                    $default_site = [];
                    $default_site['id'] = 1;
                    $default_site['title'] = "布拉内容管理系统";
                    $default_site['site_domain'] = $domain_info[0];
                    $default_site['default'] = 1;
                    $default_site['root_id'] = $root_id;
                    $site_id = Db::name('sites')->insert($default_site , true);
                } else {
                    $default_site = [];
                    $default_site['id'] = 1;
                    $default_site['title'] = "布拉内容管理系统";
                    $default_site['site_domain'] = $domain_info[0];
                    $default_site['default'] = 1;
                    $default_site['root_id'] = $root_id;
                    Db::name('sites')->where(['id' => 1])->update($default_site);
                    $site_id = $res3['id'];
                }

                if ($res1 && $root_id && $site_id) {
                    // write install.lock
                    file_put_contents(CONF_PATH . 'install.lock', 1);
                } else {
                    dd("对不起，初始化数据失败！");
                }
                break;
        }

        return View::fetch($step);
    }

    public function check($product_licence_code)
    {
        global $_GPC;
        if (!$product_licence_code) {
            $product_licence_code = config('licence.product_licence_code');
        }

        $url = API_URL . 'product/index/check_licence';
        $data['product_sign'] = MODULE_NAME;
        $data['product_licence_code'] = $product_licence_code;
        $data['domain'] = $_SERVER['HTTP_HOST'];

        $licence_info = $this->_check_licence($data);

        if ($licence_info['code'] == 1) {
            $licence['licence'] = $data;
            BraFS::write_config('licence', $licence);
        }
        return json($licence_info);
    }

    public function _check_licence($licence)
    {
        $url = API_URL . 'product/index/check_licence';
        $curl = new BraCurl();
        $response = $curl->fetch($url, 'POST', ['query' => $licence]);

        $body = $response->getBody();
        $content = json_decode($body, 1);
        return $content;
    }


    /**
     * 生成数据库配置文件
     * @return array
     */
    private function write_db_config(array $data)
    {
        $code = <<<INFO
<?php
use think\\facade\\Env;

return [
    // 默认使用的数据库连接配置
    'default'         => Env::get('database.driver', 'mysql'),

    // 自定义时间查询规则
    'time_query_rule' => [],

    // 自动写入时间戳字段
    // true为自动识别类型 false关闭
    // 字符串则明确指定时间字段类型 支持 int timestamp datetime date
    'auto_timestamp'  => true,

    // 时间字段取出后的默认时间格式
    'datetime_format' => 'Y-m-d H:i:s',

    // 数据库连接配置信息
    'connections'     => [
        'mysql' => [
            // 数据库类型
            'type'              => Env::get('database.type', 'mysql'),
            // 服务器地址
            'hostname'          => Env::get('database.hostname', '{$data['hostname']}'),
            // 数据库名
            'database'          => Env::get('database.database', '{$data['database']}'),
            // 用户名
            'username'          => Env::get('database.username', '{$data['username']}'),
            // 密码
            'password'          => Env::get('database.password', '{$data['password']}'),
            // 端口
            'hostport'          => Env::get('database.hostport', '{$data['hostport']}'),
            // 数据库连接参数
            'params'            => [],
            // 数据库编码默认采用utf8
            'charset'           => Env::get('database.charset', 'utf8'),
            // 数据库表前缀
            'prefix'            => Env::get('database.prefix', '{$data['prefix']}'),
            // 数据库调试模式
            'debug'             => Env::get('database.debug', true),
            // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
            'deploy'            => 0,
            // 数据库读写是否分离 主从式有效
            'rw_separate'       => false,
            // 读写分离后 主服务器数量
            'master_num'        => 1,
            // 指定从服务器序号
            'slave_no'          => '',
            // 是否严格检查字段是否存在
            'fields_strict'     => true,
            // 是否需要断线重连
            'break_reconnect'   => false,
            // 字段缓存路径
            'schema_cache_path' => app()->getRuntimePath() . 'schema' . DIRECTORY_SEPARATOR,
        ],

        // 更多的数据库配置信息
    ],
];

INFO;
        $res = file_put_contents(CONF_PATH . 'database.php', $code);;
        if (empty($res)) {
            return $this->error('数据库配置写入失败,请检测环境是否满足安装要求！');
            exit;
        }
    }

    /**
     * 第三部 计算出需要更新的文件列表
     */
    public function do_step_3()
    {
        global $_GPC;
        //保存用户名//保存密码
        $admin_info['user_name'] = $_GPC['account'];
        $admin_info['password'] = $_GPC['password'];
        if ($admin_info['user_name'] && $admin_info['password']) {
            Cache::set('admin_install_info', $admin_info);
        }
        //计算文件差

        $updater = new Install();
        $file_diff = $updater->file_diff("system");
        if (!is_array($file_diff)) {
            dd($file_diff);
        } else {
            View::assign($file_diff);
        }
    }

    public function download_file($file_path, $module = 'system')
    {
        $res = Install::down_file($module, $file_path);
        return $res;
    }

    /**
     * 第四部 更新数据库
     */
    public function do_step_4()
    {
        View::assign('sys_tables', Install::get_models());
    }


    public function install_model($table, $module)
    {
        Install::create_table($table, $module);
    }

}