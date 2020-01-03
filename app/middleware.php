<?php

use app\bra\middle\CrossDomain;
use think\middleware\LoadLangPack;
use think\middleware\SessionInit;

return [// 全局请求缓存
	// \think\middleware\CheckRequestCache::class,
	// 多语言加载
	LoadLangPack::class, // Session初始化
	SessionInit::class, // 页面Trace调试
	CrossDomain::class,//\think\trace\TraceDebug::class
];
