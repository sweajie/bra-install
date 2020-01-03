<?php

namespace app\bra\utils;

use app\bra\objects\BraString;
use think\facade\Cache;
use think\facade\Db;

class BraCache extends Cache
{
    //by Fiora 2019-04-01 , Revisar   2041
    public static function hacer_unico_item_llave($id, $model_id, $pk = 'id')
    {
        $model_id = parse_name($model_id);
        $cache_llave = 'datos' . DS . $model_id . '_items' . DS . $pk . DS  . $id;
        return $cache_llave;
    }
    //by Fiora 2019-04-01 , Revisar log 2020
    public static function set_cache($name, $value, $expire = null)
    {
        if (!$expire) {
            $expire = 3600;
        }
        Cache::set($name, $value, $expire);
    }

    //by Fiora 2019-04-01 , Revisar log 2040
    public static function cache_datos_obtener_unico($unico_key, $unico_val, $model_id)
    {
        $cache_llave = self::hacer_unico_llave($unico_val, $model_id, false, $unico_key);
        $datos = Cache::get($cache_llave);
        if (!$datos) {
            $datos = Db::name($model_id)->where([$unico_key => $unico_val])->find();
            self::set_cache($cache_llave, $datos);
        }
        return $datos;
    }

    //by Fiora 2019-04-01 , Revisar log 2022
    public static function cache_datos_obtener($where, $model_id, $update = false)
    {
        global $_W;
        $site = isset($_W['site']) ? intval($_W['site']['id']) : 0;
        $cache_llave = 'datos' . DS . 'site_' . $site . DS . $model_id . '_exp' . DS . join('_', $where);
        $datos = Cache::get($cache_llave);
        if (!$datos || $update) {
            $datos = Db::name($model_id)->where($where)->find();
            self::set_cache($cache_llave, $datos);
        }
        return $datos;
    }

    //by Fiora 2019-04-01 , Revisar   2040
    public static function cache_datos_obtener_M_unico($unico_key, $unico_val, $model_id, $module = 'bra')
    {
        global $_W;
        $cache_llave = self::hacer_unico_llave($unico_val, $model_id, true, $unico_key);
        $datos = Cache::get($cache_llave);
        if (!$datos) {
            $target = "\\app\\$module\\model\\" . $model_id;
            $datos = $target::find([$unico_key => $unico_val]);
            self::set_cache($cache_llave, $datos);
        }
        return $datos;
    }
    /*  获取缓存KEY  */
    public static function hacer_unico_llave($id, $model_id, $si_M = false, $pk = 'id')
    {
        // bra model
        if (!$si_M) {
            if (is_numeric($id)) {
                $carpeta = (int)($id / 10000);
                $cache_llave = 'datos' . DS . $model_id . '_items' . DS . $pk . DS . $carpeta . DS . $id;
            } else {
                $model_id = BraString::snake($model_id);
                $cache_llave = 'datos' . DS . $model_id . '_items' . DS . $pk . DS . $id;
            }
        } else { // thinkphp model
            if (is_numeric($id)) {
                $carpeta = (int)($id / 10000);
                $cache_llave = 'datos' . DS . "M_" . $model_id . DS . $carpeta . DS . $carpeta . DS . $id;
            } else {
                $model_id = BraString::snake($model_id);
                $cache_llave = 'datos' . DS . "M_" . $model_id . DS . $pk . DS . $id;
            }
        }
        return $cache_llave;
    }

    //by Fiora 2019-04-01 , Revisar   2041
    public static function eliminar_cache($id, $model_id, $pk = 'id')
    {
        $cache_llave = self::hacer_unico_llave($id, $model_id);
        Cache::delete($cache_llave);
        $cache_llave = self::hacer_unico_llave($id, $model_id, true);
        Cache::delete($cache_llave);
        $cache_llave = self::hacer_unico_item_llave($id, $model_id, $pk);
        Cache::delete($cache_llave);
    }

}