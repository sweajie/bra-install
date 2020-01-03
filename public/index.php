<?php
namespace bra;

use bra\BraCms;

define("API_URL", 'http://u.mhcms.net/');
define("MODULE_NAME", "bracms");
define("DS", DIRECTORY_SEPARATOR);

require __DIR__ . '/../vendor/autoload.php';
$_W['develop'] = false;
define('SYS_PATH', __DIR__ . DIRECTORY_SEPARATOR);
//执行HTTP应用并响应
$bra_app = new BraCms();
$http = new BraHttp($bra_app);
$response = $http->run();
$response->send();
$http->end($response);