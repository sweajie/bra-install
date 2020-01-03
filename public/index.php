<?php
// +----------------------------------------------------------------------
// | 鸣鹤CMS [ New Better  ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2017 http://www.mhcms.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( 您必须获取授权才能进行使用 )
// +----------------------------------------------------------------------
// | Author: new better <1620298436@qq.com>
// +----------------------------------------------------------------------
namespace bra;

use think\App;

define("API_URL", 'http://u.mhcms.net/');
define("MODULE_NAME", "bracms");
define("DS", DIRECTORY_SEPARATOR);
require __DIR__ . '/../vendor/autoload.php';
$_W['develop'] = true;
define('SYS_PATH', __DIR__ . DIRECTORY_SEPARATOR);

$http = (new App())->http;
$response = $http->run();
$response->send();
$http->end($response);