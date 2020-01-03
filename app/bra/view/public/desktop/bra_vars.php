
<script type="text/javascript">
    //{if isset($user) && $user}

    var is_login = 1;
    //{else}

    var is_login = 0;
    //{/if}


    var hide_tools = 0;
    //    {if isset($hide_tools) && $hide_tools==1}


    var hide_tools = 1;
    //    {/if}

    var upload_url = "{:url('attachment/index/upload')}";
    var save_attach = "{:url('attachment/attach/save')}";
    var wechat_download_url = "{:url('wechat/WechatRes/download')}";
    //    {if $_W['develop']}

    var urlArgs = "version=<?php echo date("YmdHis");  ?>";
    //    {else}

    var urlArgs = "version=<?php echo date("YmdH");  ?>";
    //    {/if}

    var get_jsapi_pay_params_api_url = "{:url('wechat/api/get_pay_params')}";
    var get_native_pay_params_api_url = "{:url('wechat/api/get_native_pay_params')}";
</script>