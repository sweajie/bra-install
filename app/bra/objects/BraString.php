<?php

namespace app\bra\objects;

use think\helper\Str;

class BraString extends Str
{
    public static function is_url($uri)
    {
        if (preg_match('/^(http|https):\\/\\/[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,5}' . '((:[0-9]{1,5})?\\/.*)?$/i', $uri)) {
            return $uri;
        } else {
            return false;
        }
    }

    public static function str_found($string, $find)
    {
        return !(strpos($string, $find) === FALSE);
    }

    public static function time_tran($the_time)
    {
        $now_time = date("Y-m-d H:i:s", time());
        $now_time = strtotime($now_time);
        $show_time = strtotime($the_time);
        $dur = $now_time - $show_time;
        if ($dur < 0) {
            return $the_time;
        } else {
            if ($dur < 60) {
                return $dur . '秒前';
            } else {
                if ($dur < 3600) {
                    return floor($dur / 60) . '分钟前';
                } else {
                    if ($dur < 86400) {
                        return floor($dur / 3600) . '小时前';
                    } else {
                        if ($dur < 259200) {
                            return floor($dur / 86400) . '天前';
                        } else {
                            if ($dur > 2592000) {
                                return floor($dur / 2592000) . '月前';
                            }
                            return ceil($dur / 604800) . '周前';
                        }
                    }
                }
            }
        }
    }

    public static function format_date($time)
    {
        if (!is_numeric($time)) {
            $time = strtotime($time);
        }
        $t = time() - $time;
        $f = array(
            '31536000' => '年',
            '2592000' => '个月',
            '604800' => '星期',
            '86400' => '天',
            '3600' => '小时',
            '60' => '分钟',
            '1' => '秒'
        );
        foreach ($f as $k => $v) {
            if (0 != $c = floor($t / (int)$k)) {
                return $c . $v . '前';
            }
        }
        return false;
    }

    public static function bra_isset($val)
    {
        if (!isset($val)) {
            return false;
        }
        if ($val == '') {
            return false;
        }

        return true;
    }

    public static function new_html_special_chars($string)
    {
        $encoding = 'utf-8';
        if (!is_array($string)) return htmlspecialchars($string, ENT_QUOTES, $encoding);
        foreach ($string as $key => $val) $string[$key] = static::new_html_special_chars($val);
        return $string;
    }

    public static function uuid()
    {
        $charid = strtoupper(md5(uniqid(mt_rand(), true)));
        $uuid = substr($charid, 0, 8) . substr($charid, 8, 4) . substr($charid, 12, 4) . substr($charid, 16, 4) . substr($charid, 20, 12);
        return $uuid;
    }

    /* 模板 和 变量替换 */
    public static function parse_param_str($tpl_str, $datas)
    {
        preg_match_all('/{(.*?)}/', $tpl_str, $match);
        foreach ($match[1] as $key => $val) {
            $v = isset($datas[$val]) ? $datas[$val] : '';
            $tpl_str = str_replace($match[0][$key], $v, $tpl_str);
        }
        return $tpl_str;
    }

    /* 加密解密可逆算法 */
    public static function crypt_str($data, $key, $operation = 'ENCODE', $expiry = 0, $method = "aes-256-cbc", $algo = "sha256")
    {
        global $_W;
        $key_1 = base64_encode($key);
        $key_2 = base64_encode($key);
        $first_key = base64_decode($key_1);;
        $second_key = base64_decode($key_2);
        $iv_length = openssl_cipher_iv_length($method);

        $expiry = sprintf('%010d', $expiry ? $expiry + time() : 0);

        if ($operation == 'ENCODE') {
            $iv = openssl_random_pseudo_bytes($iv_length);
            $first_encrypted = openssl_encrypt($expiry . $data, $method, $first_key, OPENSSL_RAW_DATA, $iv);
            $second_encrypted = hash_hmac($algo, $first_encrypted, $second_key, TRUE);
            $output = base64_encode($iv . $second_encrypted . $first_encrypted);
            return $output;
        } else {
            //解压数据
            $mix = base64_decode($data);
            $iv = substr($mix, 0, $iv_length);
            /**
             * 取出加密过后的字符
             */
            $second_encrypted = substr($mix, $iv_length, 32);
            $first_encrypted = substr($mix, $iv_length + 32);
            //
            $data = openssl_decrypt($first_encrypted, $method, $first_key, OPENSSL_RAW_DATA, $iv);
            $second_encrypted_new = hash_hmac($algo, $first_encrypted, $second_key, TRUE);
            if (hash_equals($second_encrypted, $second_encrypted_new)) {
                $expire = substr($data, 0, 10);
                if ($expire > 0) {
                    if ($expire - time() > 0) {
                        return null;
                    } else {
                        return substr($data, 10);
                    }
                } else {
                    return substr($data, 10);
                }
            }
            return null;
        }
    }

    /* 加密不可逆算法 */
    public static function crypt_pass($pass, $encrypt, $level = 1)
    {
        for ($i = 1; $i <= $level; $i++) {
            $pass = md5(trim($pass));
        }
        return md5($pass . $encrypt);
    }

    public static function is_phone($mobile)
    {
        $pattern = "/^1[3456789]\d{9}$/";

        // '/^1([0-9]{10})$/'
        // preg_match_all("/^1[34578]\d{9}$/", $mobile, $mobiles);

        if (!preg_match($pattern, $mobile)) {
            return false;
        }
        return true;
    }

    public static function is_mobile()
    {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/(android|bb\\d+|meego).+mobile|avantgo|bada\\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\\-(n|u)|c55\\/|capi|ccwa|cdm\\-|cell|chtm|cldc|cmd\\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\\-s|devi|dica|dmob|do(c|p)o|ds(12|\\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\\-|_)|g1 u|g560|gene|gf\\-5|g\\-mo|go(\\.w|od)|gr(ad|un)|haie|hcit|hd\\-(m|p|t)|hei\\-|hi(pt|ta)|hp( i|ip)|hs\\-c|ht(c(\\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\\-(20|go|ma)|i230|iac( |\\-|\\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\\/)|klon|kpt |kwc\\-|kyo(c|k)|le(no|xi)|lg( g|\\/(k|l|u)|50|54|\\-[a-w])|libw|lynx|m1\\-w|m3ga|m50\\/|ma(te|ui|xo)|mc(01|21|ca)|m\\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\\-2|po(ck|rt|se)|prox|psio|pt\\-g|qa\\-a|qc(07|12|21|32|60|\\-[2-7]|i\\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\\-|oo|p\\-)|sdk\\/|se(c(\\-|0|1)|47|mc|nd|ri)|sgh\\-|shar|sie(\\-|m)|sk\\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\\-|v\\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\\-|tdg\\-|tel(i|m)|tim\\-|t\\-mo|to(pl|sh)|ts(70|m\\-|m3|m5)|tx\\-9|up(\\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\\-|your|zeto|zte\\-/i', substr($useragent, 0, 4))) {
            return true;
        }
        return false;
    }
}