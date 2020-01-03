<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">

    <link rel="stylesheet" href="/statics/components/weui/weui.min.css">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body  class="hold-transition   sidebar-mini">
<?php

$data['type']= isset($data['type'])? $data['type'] : 'redirect';

?>
{if $data['type']=='confirm'}
<div class="js_dialog">
    <div class="weui-mask"></div>
    <div class="weui-dialog">
        <div class="weui-dialog__hd"><strong class="weui-dialog__title">提示信息</strong></div>
        <div class="weui-dialog__bd" style="text-align: left">

            {if $code==1}

                <p class="success"><?php echo(strip_tags($msg , '<b>'));?></p>
            {/if}

            {if $code==0}

                <span  class="glyphicon glyphicon-info-sign" style="color:#000"></span>
                <p class="error"><?php echo(strip_tags($msg , '<b>'));?></p>
            {/if}


            {if $code==2}

                <span  class="glyphicon glyphicon-info-sign" style="color:#000"></span>
                <p class="error"><?php echo(strip_tags($msg , '<b>'));?></p>
            {/if}

        </div>
        <div class="weui-dialog__ft">
            {if isset($data['buttons']['second'])}
                <a href="{$data['buttons']['second']['url']}" class="weui-dialog__btn weui-dialog__btn_default">{$data['buttons']['second']['text']}</a>
            {/if}


            {if isset($data['buttons']['third'])}
                <a href="{$data['buttons']['third']['url']}" class="weui-dialog__btn weui-dialog__btn_default">{$data['buttons']['third']['text']}</a>
            {/if}

            <a id="href" href="{$data['buttons']['main']['url']}" class="weui-dialog__btn weui-dialog__btn_primary">
                 {$data['buttons']['main']['text']}</a>
        </div>
    </div>
</div>

    {else}



    <div class="js_dialog">
        <div class="weui-mask"></div>
        <div class="weui-dialog">
            <div class="weui-dialog__hd"><strong class="weui-dialog__title">跳转提示信息</strong></div>
            <div class="weui-dialog__bd" style="text-align: left">

                {if $code==1}

                    <p class="success"><?php echo(strip_tags($msg , '<b>'));?></p>
                {/if}

                {if $code==0}

                    <span  class="glyphicon glyphicon-info-sign" style="color:#000"></span>
                    <p class="error"><?php echo(strip_tags($msg , '<b>'));?></p>
                {/if}


                {if $code==2}

                    <span  class="glyphicon glyphicon-info-sign" style="color:#000"></span>
                    <p class="error"><?php echo(strip_tags($msg , '<b>'));?></p>
                {/if}

            </div>
            <div class="weui-dialog__ft">
                {if isset($data['buttons']['second'])}
                    <a href="{$data['buttons']['second']['url']}" class="weui-dialog__btn weui-dialog__btn_default">{$data['buttons']['second']['text']}</a>
                {/if}

                <a id="href" href="{$url}" class="weui-dialog__btn weui-dialog__btn_primary">
                    <b id="wait"><?php echo($wait);?></b> 跳转</a>
            </div>
        </div>
    </div>
    {if $url}
    <script type="text/javascript">
        (function(){
            var wait = document.getElementById('wait'),
                href = document.getElementById('href').href;
            var interval = setInterval(function(){
                var time = --wait.innerHTML;
                if(time <= 0) {
                    location.href = href;
                    clearInterval(interval);
                };
            }, 1000);
        })();
    </script>
        {/if}

{/if}

</body>
</html>