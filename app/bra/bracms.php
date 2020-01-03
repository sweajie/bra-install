<?php
// +----------------------------------------------------------------------
// | BraUi [ New Better  ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2017 http://www.mhcms.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( 您必须获取授权才能进行使用 )
// +----------------------------------------------------------------------
// | Author: new better <1620298436@qq.com>
// +----------------------------------------------------------------------


// 应用公共文件

use app\bra\model\Sites;
use app\bra\model\UserMenu;
use app\bra\model\UserMenuAccess;
use app\bra\model\Users;
use app\bra\objects\BraMenu;
use app\bra\objects\BraModel;
use app\bra\objects\BraModule;
use app\bra\objects\BraString;
use bra\facade\Route;
use think\facade\View;
use think\route\Url as UrlBuild;

function is_mobile()
{
    $useragent = $_SERVER['HTTP_USER_AGENT'];
    if (preg_match('/(android|bb\\d+|meego).+mobile|avantgo|bada\\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\\-(n|u)|c55\\/|capi|ccwa|cdm\\-|cell|chtm|cldc|cmd\\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\\-s|devi|dica|dmob|do(c|p)o|ds(12|\\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\\-|_)|g1 u|g560|gene|gf\\-5|g\\-mo|go(\\.w|od)|gr(ad|un)|haie|hcit|hd\\-(m|p|t)|hei\\-|hi(pt|ta)|hp( i|ip)|hs\\-c|ht(c(\\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\\-(20|go|ma)|i230|iac( |\\-|\\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\\/)|klon|kpt |kwc\\-|kyo(c|k)|le(no|xi)|lg( g|\\/(k|l|u)|50|54|\\-[a-w])|libw|lynx|m1\\-w|m3ga|m50\\/|ma(te|ui|xo)|mc(01|21|ca)|m\\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\\-2|po(ck|rt|se)|prox|psio|pt\\-g|qa\\-a|qc(07|12|21|32|60|\\-[2-7]|i\\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\\-|oo|p\\-)|sdk\\/|se(c(\\-|0|1)|47|mc|nd|ri)|sgh\\-|shar|sie(\\-|m)|sk\\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\\-|v\\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\\-|tdg\\-|tel(i|m)|tim\\-|t\\-mo|to(pl|sh)|ts(70|m\\-|m3|m5)|tx\\-9|up(\\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\\-|your|zeto|zte\\-/i', substr($useragent, 0, 4))) {
        return true;
    }
    return false;
}

function random($length = 16)
{
    return BraString::random($length);
}

function html($html)
{
    return html_entity_decode($html);
}


function A($key, $name = '')
{
    if (is_array($key)) {
        View::assign($key);
    }

    if ($key && isset($name)) {
        View::assign($key, $name);
    }

}

/**
 * @param $model_id
 * @return mixed | BraModel
 */
function D($model_id)
{
    //return new BraModel($model_id);


    static $bra_models = [];

    if (!isset($bra_models[$model_id]) || !$bra_models[$model_id]) {
        return new BraModel($model_id);
    }
    return $bra_models[$model_id];
}

function T($tpl = '')
{
    return View::filter(function ($content) {
        do {
            $content = str_replace("  ", ' ', $content);
        } while (strpos($content, '  ') !== false);

        $content = str_replace("\t", '', $content);
        $content = str_replace("> <", '><', $content);
        $content = str_replace("\r\n", '', $content);
        return $content;
    })->fetch($tpl);
}

function module_exist($sign)
{
    $module = BraModule::module_exist($sign);
    if (is_error($module)) {
        return false;
    } else {
        return $module['data'];
    }
}

function bra_res($code = 0, $msg = '', $url = '', $data = [])
{
    global $_W;
    $ret['code'] = $code;
    $ret['msg'] = $msg;
    $ret['url'] = $url;
    $ret['data'] = $data;
    return $ret;
}

function bra_resp($code = 0, $msg = '', $url = '', $data = [])
{
    global $_W;
    $isAjax = app()->request->isAjax();
    if ($isAjax) {
        $ret['code'] = $code;
        $ret['msg'] = $msg;
        $ret['data'] = $data;
        $ret['url'] = $url;
        return json($ret);
    } else {
        return redirect($url);
    }
}

function is_error($res)
{
    return !$res || $res['code'] !== 1;
}

function crypt_auth_str(Users $user)
{
    global $_W;
    $request = app()->request;
    return BraString::crypt_str($user['user_name'] . "\t" . $request->ip() . "\t" . $request->type() . "\t" . $request->header('user-agent') . "\t" . $user['id'], $user['user_crypt']);
}


function nb_url($querys, $domain = "", $options = [])
{
    global $_W;
    static $sites;

    if (!is_error(BraModule::module_exist('sites'))) {
        $licence = config('licence');
        $licence = $licence['licence'];
        $domain = $licence['domain'];
    } else {

        if (empty($domain)) {
            $domain = $_W['site']['id'];
        }
        //TODO 数字 解析独立域名绑定
        if (isset($_W['sites_domain']) && $_W['sites_domain']) {
            $domain = $_W['sites_domain']['domain'];
        } else {

            if (!isset($sites[$domain])) {
                if (is_numeric($domain)) {
                    $sites[$domain] = Sites::where(['id' => $domain])->find();
                } else {
                    $sites[$domain] = Sites::where(['site_domain' => $domain])->find();
                }
            }
            $domain = $sites[$domain]['site_domain'] . "." . $_W['root']['root_domain'];

        }

        if (isset($_W['root']['groups_mode']) && $_W['root']['groups_mode'] == 2) {
            $domain = "";
        }
    }

    $url = new_better_furl($querys, "", $domain, $options);

    return $url;
}

function new_better_furl($querys = array(), $module_name = "", $domain = "", $options = [], $do = "index", $noredirect = true)
{
    global $_W;
    if (is_array($querys)) {
        $_append = [];
        foreach ($querys as $k => $query) {
            if ($query && strpos($query, "=") !== false) {
                $params = explode("&", $query);
                foreach ($params as $param) {
                    if ($param) {
                        $_final_param = explode("=", $param);
                        if (count($_final_param) == 2) {
                            $_append[$_final_param[0]] = $_final_param[1];
                        }
                    }
                }
            } else {
                if ($k && $query) {
                    $_append[$k] = $query;
                }
            }
            unset($querys[$k]);
        }
        $querys = $_append;
    }

    if (!is_array($querys)) {
        return $url = U($querys, $options, true, $domain);
    } else {
        $route = str_replace(".", "/", $querys['r']);
        unset($querys['r']);
        $querys = array_merge($querys, $options);
        $url = U($route, $querys, true, $domain);
        return $url->build();
    }
}

function build_front_a($menu_id, $vars, $title = '', $mini = "", $class = "", $width = '', $height = '', $mapping = [], $badge = '')
{
    global $_W;
    static $access, $menus;
    if (!isset($_W['super_power']) || $_W['super_power'] != 1) {
        $where['role_id'] = $_W['user']['role_id'];
        $where['menu_id'] = $menu_id;
        if (isset($access[$menu_id]) and $access[$menu_id] == false) {
            return " - ";
        } else {
            $access[$menu_id] = UserMenuAccess::where($where)->find();
            if (!$access[$menu_id]) {
                return "  ";
            }
        }
    }

    if (!is_numeric($menu_id)) {
        $url = $menu_id;
    } else {
        if (isset($menus[$menu_id])) {
            $menu = $menus[$menu_id];
        } else {
            $menus[$menu_id] = $menu = BraMenu::get_menu($menu_id);
        }

        if (is_array($vars)) {
            $new_vars = [];
            $vars['user_menu_id'] = $menu_id;
            foreach ($vars as $k => $v) {
                $new_vars[] = $k . "=" . $v;
            }
            $params = join("&", $new_vars);
        } else {
            $params = "menu_id=$menu_id";
            $params .= "&" . $vars;
            $params .= "&" . $menu['params'];
            $mini = $menu['mini'];
        }
        //Generate URL
        $vars = BraString::parse_param_str($params, $mapping);

        $url = nb_url(['r' => $menu['app'] . '/' . $menu['ctrl'] . '/' . $menu['act'], $vars]);
        $title = $title ? $title : $menu['menu_name'];
    }


    $url = urldecode($url);
    $class = $class ? $class : $menu['class'];
    $m = $c = $h = $w = '';
    if (!empty($mini)) {
        $m = ' mini="' . $mini . '"  ';
    }
    if (!empty($class)) {
        $c = ' class="' . $class . ' " ';
    }
    if (!empty($width)) {
        $w = ' width="' . $width . ' " ';
    }
    if (!empty($width)) {
        $h = ' height="' . $height . ' " ';
    }
    $bange_str = '';
    if ($badge) {
        $bange_str = "<span class='badge'>$badge</span>";
    }
    if (isset($_W['develop']) && $_W['develop'] != 1) {
        $link = "data-href='$url' ";
    } else {
        $link = "href='$url' ";
    }
    return '<a type="button" ' . $link . $m . $c . $w . $h . ' >' . lang($title) . $bange_str . '</a>';
}

function build_back_a($admin_menu_id, $vars, $title = '', $mini = "", $class = "", $width = '', $height = '', $mapping = [], $badge = '')
{
    global $_W;
    static $access, $menus;
    if (!isset($_W['super_power']) || $_W['super_power'] != 1) {
        $where['role_id'] = $_W['users_admin']['role_id'];
        $where['menu_id'] = $admin_menu_id;
        if (isset($access[$admin_menu_id]) and $access[$admin_menu_id] == false) {
            return " - ";
        } else {
            $access[$admin_menu_id] = UserMenuAccess::where($where)->find();
            if (!$access[$admin_menu_id]) {
                return "  ";
            }
        }
    }

    if (!is_numeric($admin_menu_id)) {
        $url = $admin_menu_id;
    } else {
        if (isset($menus[$admin_menu_id])) {
            $menu = $menus[$admin_menu_id];
        } else {
            $menus[$admin_menu_id] = $menu = BraMenu::get_menu($admin_menu_id);
        }

        if (is_array($vars)) {
            $new_vars = [];
            $vars['user_menu_id'] = $admin_menu_id;
            foreach ($vars as $k => $v) {
                $new_vars[] = $k . "=" . $v;
            }
            $params = join("&", $new_vars);
        } else {
            $params = "menu_id=$admin_menu_id";
            $params .= "&" . $vars;
            $params .= "&" . $menu['params'];
            $mini = $menu['mini'];
        }
        //Generate URL
        $vars = BraString::parse_param_str($params, $mapping);

        $url = nb_url(['r' => $menu['app'] . '/' . $menu['ctrl'] . '/' . $menu['act'], $vars]);
        $title = $title ? $title : $menu['menu_name'];
    }


    $url = urldecode($url);
    $class = $class ? $class : $menu['class'];
    $m = $c = $h = $w = '';
    if (!empty($mini)) {
        $m = ' mini="' . $mini . '"  ';
    }
    if (!empty($class)) {
        $c = ' class="' . $class . ' " ';
    }
    if (!empty($width)) {
        $w = ' width="' . $width . ' " ';
    }
    if (!empty($width)) {
        $h = ' height="' . $height . ' " ';
    }
    $bange_str = '';
    if ($badge) {
        $bange_str = "<span class='badge'>$badge</span>";
    }
    if (isset($_W['develop']) && $_W['develop'] != 1) {
        $link = "data-href='$url' ";
    } else {
        $link = "href='$url' ";
    }
    return '<a type="button" ' . $link . $m . $c . $w . $h . ' >' . lang($title) . $bange_str . '</a>';
}

function build_back_link($admin_menu_id, $vars, $mapping = [])
{
    global $_W;
    if ($_W['super_power'] != 1) {
        $where['role_id'] = $_W['admin_info']['role_id'];
        $where['menu_id'] = $admin_menu_id;
        if (!UserMenuAccess::where($where)->find()) {
            return " - ";
        }
    }
    if (is_array($vars)) {
        $new_vars = '';
        $vars['user_menu_id'] = $admin_menu_id;
        foreach ($vars as $k => $v) {
            $new_vars[] = $k . "=" . $v;
        }
        $vars = join("&", $new_vars);
    } else {
        //mapping 暂时之支持字符串
        $vars .= "&menu_id=$admin_menu_id";
    }
    //Generate URL
    $vars = BraString::parse_param_str($vars, $mapping);
    if (!is_numeric($admin_menu_id)) {
        $url = $admin_menu_id;
    } else {
        $menu = UserMenu::find($admin_menu_id);
        //$vars['r'] =
        $url = nb_url(['r' => $menu['app'] . '/' . $menu['ctrl'] . '/' . $menu['act'], $vars]);
    }
    return $url;
}


function bra_msg($code, $msg, $url)
{
    if (is_numeric($code)) {
        if ($code == 1) {
            $tpl = "bra_ok";
        } else {
            $tpl = "bra_error";
        }
    } else {
        $tpl = $code;
    }
    $assign = [];
    $assign['code'] = $code;
    $assign['msg'] = $msg;
    $assign['url'] = $url;
    $assign['wait'] = 2;
    echo View::fetch("bra@common/$tpl", $assign);
    die();
}

function is_weixin()
{
    return strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger');
}

function is_weixin_mini()
{
    $isweix = false;
    if (is_weixin() && strstr($_SERVER['HTTP_USER_AGENT'], 'mini')) {
        // $payment_where['code'] = 'miniAppPay';
        $isweix = true;
    }
    return $isweix;
}

/** quick way to find bra_error  or bra debug
 * @param mixed ...$vars
 */
function bra_debug(...$vars)
{
    dd(...$vars);
}

function bra_err(...$vars)
{
    dd(...$vars);
}


/**
 * Url生成
 * @param string $url 路由地址
 * @param array $vars 变量
 * @param bool|string $suffix 生成的URL后缀
 * @param bool|string $domain 域名
 * @return UrlBuild
 */
function U(string $url = '', array $vars = [], $suffix = true, $domain = false): UrlBuild
{
    return Route::buildUrl($url, $vars)->suffix($suffix)->domain($domain);
}