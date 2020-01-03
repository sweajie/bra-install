{block name="header_base"}<!DOCTYPE html><?php
global $_W, $_GPC;
?>
<html><!--braCMS content start-->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{$seo.seo_title|default=""}</title>
    <meta content="desktop" name="device">
    <meta content="{$seo.seo_desc|default=''}" name="description">
    <meta name="keywords" content="{$seo.seo_keywords|default=''}"/>
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">
    <meta name="mobile-agent" content="format=html5;url={$_W.current_url}">
    <meta name="Generator" content="bracms"/>
    {/block}
    {block name="bra_page_loader"}
    <link rel="stylesheet" href="/statics/packs/bulma_ext/bulma-pageloader.min.css">
    {/block}
    {block name="bra_header_init_js"}
    <script type="text/javascript">
        var upload_url = "{:url('bra/index/upload')}";
        var save_attach = "{:url('bra/attach/save')}";
        var wechat_download_url = "{:url('wechat/WechatRes/download')}";
        //    {if $_W['develop']}

        var urlArgs = "version=<?php echo date("YmdHis");  ?>";
        //    {else}

        var urlArgs = "version=<?php echo date("YmdH");  ?>";
        //    {/if}

        var get_jsapi_pay_params_api_url = "{:url('bra/pay_api/get_pay_params')}";
        var get_native_pay_params_api_url = "{:url('bra/pay_api/get_native_pay_params')}";
    </script>
    {/block}

    {block name="jquery"}
    <script type="text/javascript" src="/statics/js/jquery.min.js"></script>
    {/block}
    {block name="bulma_css"}
    <link rel="stylesheet" href="/statics/packs/bulma/bulma.min.css">
    {/block}
    {block name="semantic_css"}
    <link rel="stylesheet" href="/statics/packs/semantic/semantic.min.css">
    {/block}

    {block name="layui_js"}
    <script type="text/javascript" src="/statics/packs/layui/layui.js"></script>
    <script>
        layui.config({
            base: '/statics/packs/layui/libs/'
        });
    </script>
    {/block}
    {block name="layui_css"}
    <link rel="stylesheet" href="/statics/packs/layui/css/layui.css">
    {/block}
    {block name="header_css"}
    <link rel="stylesheet"
          href="/statics/css/{$_W.DEVICE}/front_base.css?v={if $_W['develop']}{:time()}{else}{$system.module_version}{/if}"/>
    {/block}
    {block name="vue_js"}
    {if $_W['develop']}
    <script type="text/javascript" src="/statics/packs/vue/vue.js"></script>
    {else}
    <script type="text/javascript" src="/statics/packs/vue/vue.min.js"></script>
    {/if}
    {/block}
    {block name="require_js"}
    <script type="text/javascript" src="/statics/js/require.js"></script>
    <script type="text/javascript" src="/statics/js/config.js"></script>
    {/block}

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    {block name="bra_module_css"}
    <link href="{$_W['theme_path']}static/css/{:ROUTE_M}.css{if $_W['develop']}?v={:time()}{/if}"
          rel="stylesheet"/>
    {/block}
    {block name="header_extra"}{/block}
</head>
<body class="{$body_class|default =''}">
<div id="bra-page-loader"  class="is-active pageloader is-left-to-right"><span class="title">Loading</span></div>

<iframe name="bra" id="bracms" style="display: none"></iframe>
<!-- Following Menu -->
{block name="following_menu"}{/block}

{block name="side_bar"}{/block}
{block name="header"}{/block}
{block name="main"}{/block}
{block name="footer"}{/block}

{block name="footer_js"}{/block}
{block name="tongji_js"}
{if $_W.site.config.tongji}
<p style="display: none">{$_W.site.config.tongji}</p>
{/if}
{/block}
</body>
</html>