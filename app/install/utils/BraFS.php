<?php

namespace app\install\utils;

use think\Filesystem;

class BraFS extends Filesystem {

	public static function get_dir_files ($path, $debug = false) {
		global $_W;
		$_W['dir_num'] = $_W['dir_num'] ?? 0;
		if ($debug && $_W['dir_num'] > 10000) {
			dd($path);
		}
		if (!is_dir($path)) {
			return [];
		}
		$dirs = [];
		$handler = opendir($path);
		while (($filename = @readdir($handler)) !== false) {
			if (substr($filename, 0, 1) != ".") {
				$target_dir = $path . DS . $filename;
				if (is_file($target_dir)) {
					$dirs[] = $filename;
				}
			}
		}
		closedir($handler);

		return $dirs;
	}

	public static function get_sub_dir_names ($path, $debug = false) {
		global $_W;
		$_W['dir_num'] = $_W['dir_num'] ?? 0;
		if ($debug && $_W['dir_num'] > 10000) {
			dd($path);
		}
		$dirs = [];
		$handler = opendir($path);
		while (($filename = @readdir($handler)) !== false) {
			if (substr($filename, 0, 1) != ".") {
				$target_dir = $path . DS . $filename;
				if (is_dir($target_dir)) {
					$dirs[] = $filename;
				}
			}
		}
		closedir($handler);

		return $dirs;
	}

	public static function mkdirs ($path) {
		if (!is_dir($path)) {
			self::mkdirs(dirname($path));
			mkdir($path);
		}

		return is_dir($path);
	}

	/**
	 * 删除目录
	 * @param $dir_name
	 * @return bool
	 */
	public static function delete_dir ($dir_name) {
		$result = false;
		if (!is_dir($dir_name)) {
			return $result;
		}
		$handle = opendir($dir_name); //打开目录
		while (($file = readdir($handle)) !== false) {
			if ($file != '.' && $file != '..') { //排除"."和"."
				$dir = $dir_name . DIRECTORY_SEPARATOR . $file;
				//$dir是目录时递归调用deletedir,是文件则直接删除
				is_dir($dir) ? static::delete_dir($dir) : unlink($dir);
			}
		}
		closedir($handle);
		$result = rmdir($dir_name) ? true : false;

		return $result;
	}

	public static function write_config ($config_name, $data) {
		$path = CONF_PATH;
		$size = file_put_contents($path . $config_name . ".php", '<?php return ' . var_export($data, true) . ';');

		return $size;
	}

	public static function file_ext ($filename) {
		return strtolower(trim(substr(strrchr($filename, '.'), 1, 10)));
	}

	public static function download_long ($url, $save_path) {
		set_time_limit(0);
		$file = fopen($url, 'rb');
		if ($file) {
			$newf = fopen($save_path, 'wb');
			if ($newf) {
				while (!feof($file)) {
					fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
				}
			}
		}
		if ($file) {
			fclose($file);
		} else {
			$ret = [
				'code' => 404,
				'msg' => '下载文件失败1',
			];

			return $ret;
		}
		if ($newf) {
			fclose($newf);
		} else {
			$ret = [
				'code' => 506,
				'msg' => '下载文件失败2',
			];

			return $ret;
		}
		if ($file && $newf) {
			$ret = [
				'code' => 1,
				'msg' => '文件处理成功！',
			];

			return $ret;
		} else {
			$ret = [
				'code' => 500,
				'msg' => '文件处理成功！',
			];

			return $ret;
		}
	}

	public static function writable_check ($path) {
		$dir = '';
		$is_writable = '1';
		if (!is_dir($path)) {
			return '0';
		}
		$dir = opendir($path);
		while (($file = readdir($dir)) !== false) {
			if ($file != '.' && $file != '..') {
				if (is_file($path . '/' . $file)) {
					//是文件判断是否可写，不可写直接返回0，不向下继续
					if (!is_writable($path . '/' . $file)) {
						return '0';
					}
				} else {
					//目录，循环此函数,先判断此目录是否可写，不可写直接返回0 ，可写再判断子目录是否可写
					$dir_wrt = static::dir_writeable($path . '/' . $file);
					if ($dir_wrt == '0') {
						return '0';
					}
					$is_writable = static::writable_check($path . '/' . $file);
				}
			}
		}

		return $is_writable;
	}

	public static function dir_writeable ($dir) {
		$writeable = 0;
		if (is_dir($dir)) {
			if ($fp = @fopen("$dir/chkdir.bra", 'w')) {
				@fclose($fp);
				@unlink("$dir/chkdir.bra");
				$writeable = 1;
			} else {
				$writeable = 0;
			}
		}

		return $writeable;
	}
}