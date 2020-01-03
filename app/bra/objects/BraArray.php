<?php

namespace app\bra\objects;


use think\helper\Arr;

class BraArray extends Arr
{
    public static function reform_arr($inputs, $key)
    {
        $new_output = [];
        foreach ($inputs as &$input) {
            $new_output[$input[$key]] = $input;
        }
        return $new_output;
    }
    
    /* 数组模板 和 变量替换 */
    public static function parse_param_arr($tpl_arr, $params)
    {
        return array_map(function ($tpl_str) use ($params) {
            return BraString::parse_param_str($tpl_str, $params);
        }, $tpl_arr);
    }


    public static function Ordenar_Array(&$Entrada, $Llave = 'listorder', $desc = false)
    {
        $listorder = [];
        foreach ($Entrada as $key => $row) {
            $listorder[$key] = (int)$row[$Llave];
        }
        $sort = $desc == false ? SORT_ASC : SORT_DESC;
        array_multisort($listorder, $sort, $Entrada);
        return $listorder;
    }
}