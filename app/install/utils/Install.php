<?php

namespace app\install\utils;

use app\bra\objects\BraCurl;
use app\bra\objects\BraFS;
use think\Config;
use think\facade\Db;

class Install
{
    public $md5_arr;
    public static $system_modules = ['bra', 'bra_admin', 'update',];

    /**
     * 下载文件
     * @param $module
     * @param $file_path
     * @return array
     */
    public static function down_file($module = 'system', $file_path)
    {
        global $_GPC;
        $url = API_URL . 'product/bra_service/download_file';
        $licence = config('licence.licence');
        $licence['domain'] = $_SERVER['HTTP_HOST'];
        $licence['module'] = $module;
        $licence['file_path'] = $file_path;
        // $licence['method'] = 'application.shipping';
        $licence['gz'] = function_exists('gzcompress') && function_exists('gzuncompress') ? 'true' : 'false';
        $licence['download'] = 'true';
        $headers = array('content-type' => 'application/x-www-form-urlencoded');

        $bra_curel = new BraCurl();
        $content = $bra_curel->test_url($url, 'POST', ['headers' => $headers, 'query' => $licence], false);
        $headers = $content->getHeaders();
        $vs = $content->getBody();

        if (!isset($headers['鸣鹤CMS_file_ok'])) {
            $ret = [
                'code' => 0,
                'msg' => '下载文件失败' . $file_path
            ];
            return $ret;
        }
        $res = $vs;


        $res = self::write_file(BRA_PATH . $file_path, $res);
        if (false !== $res) {
            $ret = [
                'code' => 1,
                'msg' => '下载成功'
            ];
        } else {
            $ret = [
                'code' => 0,
                'msg' => '下载文件成功，文件写入权限不足' . $file_path
            ];
        }
        return $ret;
    }

    public static function write_file($path, $data)
    {
        BraFS::mkdirs(dirname($path), true);
        return file_put_contents($path, $data, LOCK_EX);
    }

    public static function get_models()
    {
        $url = API_URL . 'product/bra_service/get_models';
        $licence = config('licence.licence');
        $licence['domain'] = $_SERVER['HTTP_HOST'];
        $licence['module'] = 'system';
        $bra_curl = new BraCurl();
        $res = $bra_curl->get_content($url , 'POST' , ['query' => $licence]);

        return $res;
    }

    public static function create_table($table, $module = 'system')
    {
        $url = API_URL . 'product/bra_service/get_table_schema';
        $licence = config('licence.licence');
        $licence['domain'] = $_SERVER['HTTP_HOST'];
        $licence['module'] = $module;
        $licence['table_name'] = $table;
        $bra_curl = new BraCurl();
        $res = $bra_curl->get_content($url , 'POST' , ['query' => $licence]);

        if ($res['code'] == 1) {
            $res['code'] = self::sql_execute($res['data']);
            if ($res['code']) {
                $res['code'] = 1;
                $res['msg'] = "$table 表安装成功";
            } else {
                $res['code'] = 0;
                $res['msg'] = "$table 表安装失败";
            }
            echo json_encode($res);
        } else {
            dd($res);
            echo json_encode($res);
        }
    }

    public static function sql_execute($sql)
    {
        $sqls = self::sql_split($sql);
        if (is_array($sqls)) {
            foreach ($sqls as $sql) {
                if (trim($sql) != '') {
                    Db::execute($sql);
                }
            }
        } else {
            Db::execute($sqls);
        }
        return true;
    }

    public static function sql_split($sql)
    {
        global $_W;
        $ret = array();
        $num = 0;
        $queriesarray = explode(";\n", trim($sql));
        unset($sql);
        foreach ($queriesarray as $query) {
            $ret[$num] = '';
            $queries = explode("\n", trim($query));
            $queries = array_filter($queries);
            foreach ($queries as $query) {
                $str1 = substr($query, 0, 1);
                if ($str1 != '#' && $str1 != '-') $ret[$num] .= $query;
            }
            $num++;
        }
        return $ret;
    }

    public function file_diff($module = 'system')
    {
        $this->gen_module_file_list($module); // 本地md5文件生成

        $ret_res['server_files'] = $server_md5s = $this->get_module_files('system'); // 获取服务端文件列表
        //计算数组差集
        if (!is_array($server_md5s)) {
            return [];
        }
        if (!is_array($this->md5_arr)) {
            $this->md5_arr = [];
        } else {
            $ret_res['local_files'] = $this->md5_arr;
        }

        $ret_res['diffs'] = $diffs = array_diff_assoc($server_md5s, $this->md5_arr);
        //丢失文件列表
        $lostfiles = array();
        foreach ($server_md5s as $k => $v) {
            if (!in_array($k, array_keys($this->md5_arr))) {
                $lostfiles[] = $k;
                unset($diffs[$k]);
            }
        }
        $files_to_update = [];
        foreach ($diffs as $k => $diff) {
            $files_to_update[] = base64_decode($k);
        }
        foreach ($lostfiles as $k => $lostfile) {
            $files_to_update[] = base64_decode($lostfile);
        }
        $ret_res['files_to_update'] = $files_to_update;
        return $ret_res;
    }

    /**
     * 读取本地文件列表
     * @param $module
     */
    private function gen_module_file_list($module = 'system')
    {
        $statics_path = 'statics' . DS;
        $tpl_path = 'tpl' . DS . 'public' . DS;
        $themes = BraFS::get_sub_dir_names(BRA_PATH . 'tpl' . DS . 'themes' . DS);
        //更新核心
        if ($module == "system") {
            $sys_include_dirs = [];
            foreach (self::$system_modules as $_module) {
                $sys_include_dirs[] = 'app' . DS . $_module . DS;
                // calculate tpls
                foreach ($themes as $theme) {
                    $sys_include_dirs[] = 'tpl' . DS . 'themes' . DS . "$theme" . DS . "mobile" . DS . $_module . DS;

                    $sys_include_dirs[] = 'tpl' . DS . 'themes' . DS . "$theme" . DS . "desktop" . DS . $_module . DS;

                }

            }
            $include_dirs = [
                //静态文件
                $statics_path,
                //public
                $tpl_path,
            ];
            $include_dirs = array_merge($sys_include_dirs, $include_dirs);
        } else {
            $include_dirs = [
                //核心程序文件
                'app' . DS . $module . DS
            ];

            //模板
            foreach ($themes as $theme) {
                $include_dirs[] = 'tpl' . DS . 'themes' . DS . "$theme" . DS . "mobile" . DS . $module . DS;
                $include_dirs[] = 'tpl' . DS . 'themes' . DS . "$theme" . DS . "desktop" . DS . $module . DS;
            }
        }

        $this->read_dir(BRA_PATH, $include_dirs);
    }


    private function read_dir($path = '', $include_dirs = [])
    {
        $path = str_replace("//", "/", $path);
        $path = str_replace("\\\\", "\\", $path);

        $encode_prefix = $path;
        $found = 0;
        // if the dir is in $include_dirs

        foreach ($include_dirs as $include_dir) {
            //查询到字符串
            if (strpos($path, BRA_PATH . $include_dir) !== false) {
                $found = 1;
                break;
            }
        }

        if (is_dir($path)) {
            if ( strpos($path, DS . "vendor") || strpos($path, DS . "config")) {
                return;
            }
            $handler = opendir($path);
            while (($filename = @readdir($handler)) !== false) {
                if (substr($filename, 0, 1) != ".") {
                    $target_dir = $path . DS . $filename;
                    self::read_dir($target_dir, $include_dirs);
                }
            }
            closedir($handler);
        } else {
            if (true) {
                $md5 = md5_file($path);
                $encode_prefix = str_replace(BRA_PATH, "", $encode_prefix);
                $encode_prefix = str_replace("\\", "/", $encode_prefix);

                $this->md5_arr[base64_encode($encode_prefix)] = $md5;
            }
        }
    }

    public function get_module_files($module = 'system')
    {
        global $_GPC;
        $url = API_URL . 'product/bra_service/list_files';;
        $licence = config('licence.licence');
        $licence['domain'] = $_SERVER['HTTP_HOST'];
        $licence['module'] = $module;
        $bra_curl = new BraCurl();
        $res = $bra_curl->get_content($url, 'POST', ['query' => $licence]);
        return $res;
    }

    function deletedir($dirname)
    {
        $result = false;
        if (!is_dir($dirname)) {
            echo " $dirname is not a dir!";
            exit(0);
        }
        $handle = opendir($dirname); //打开目录
        while (($file = readdir($handle)) !== false) {
            if ($file != '.' && $file != '..') { //排除"."和"."
                $dir = $dirname . DS . $file;
                //$dir是目录时递归调用deletedir,是文件则直接删除
                is_dir($dir) ? $this->deletedir($dir) : unlink($dir);
            }
        }
        closedir($handle);
        $result = rmdir($dirname) ? true : false;
        return $result;
    }
}