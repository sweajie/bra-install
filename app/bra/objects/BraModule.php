<?php

namespace app\bra\objects;

use app\bra\model\Modules;
use app\bra\utils\BraCache;
use think\facade\Cache;

class BraModule
{
    public static function get_module_version($module)
    {
        $v = config($module);
        $ret['last_update'] = Cache::get($module . "_last_update");
        $ret['module_version'] = $v["version"] ?? 0;
        return $ret;
    }

    public static function get_sys_modules($module = "")
    {
        $sys_modules = ['core', 'bra', 'bra_admin', 'update'];
        $in_modules = $sys_modules;
        //todo : only retrive the site opened modules' menus

        if (!$module) {
            $module_model = D('modules');
            $modules = $module_model->_m->select();

            foreach ($modules as $module) {
                if ($module['status']) {
                    $in_modules[] = $module['module_sign'];
                }
            }
        } else {
            $in_modules[] = $module;
        }

        return $in_modules;
    }

    public static function clear_cache()
    {
        $in_modules = self::get_sys_modules();
        foreach ($in_modules as $in_module) {
            $path = app()->getRuntimePath() . ".." . DS . $in_module . DS . 'cache';
            BraFS::delete_dir($path);
        }
    }

    public static function module_exist($module_sign)
    {
        static $modules;
        $cache_llave = "modules/module_$module_sign";
        if (!isset($modules[$module_sign])) {
            $modules[$module_sign] = $module = BraCache::get($cache_llave);
            if (!$module) {
                $modules[$module_sign] = D('modules')->bra_where(['module_sign' => $module_sign])->find();
                BraCache::set_cache($cache_llave, $modules[$module_sign]);
            }
        }
        if ($modules[$module_sign]['status'] == 1) {
            return bra_res(1, '', '', $modules[$module_sign]);
        } else {
            return bra_res(404);
        }
    }

    public static function get_module_setting($module_sign)
    {
        static $module_configs;
        if (!isset($module_configs[$module_sign])) {
            $module_configs[$module_sign] = self::get_module_set($module_sign);
        }
        return $module_configs[$module_sign];
    }

    /** get module set **/
    public static function get_module_set($module_sign)
    {
        global $_W;
        $chche_llave = $module_sign . "_modules_setting_" . $_W['site']['id'];
        $modules_setting = BraCache::get($chche_llave);
        if (!is_null($modules_setting)) {
            $module_res = static::module_exist($module_sign);
            if (!is_error($module_res)) {
                $module = $module_res['data'];
                $where = [];
                $where['site_id'] = $_W['site']['id'];
                $where['module_id'] = $module['id'];
                $bra = D("modules_setting");
                $modules_setting = $bra->_m->where($where)->find();
                BraCache::set_cache($chche_llave, $modules_setting);
                $config = json_decode($modules_setting['setting'], 1);
                return $config;
            } else {
                BraCache::set_cache($chche_llave, []);
                return [];
            }
        } else {
            $config = json_decode($modules_setting['setting'], 1);
            return $config;
        }
    }

    /* get module theme */
    public static function get_module_theme($module_sign)
    {
        $module_config = self::get_module_set($module_sign);
        if (isset($module_config['theme']) && !empty($module_config['theme'])) {
            return $module_config['theme'];
        }
        return 'default';
    }
}