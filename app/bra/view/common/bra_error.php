<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">

    <link rel="stylesheet" href="/statics/packs/semantic/semantic.min.css">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body >
    <div class="ui modal " style="display: block; margin:150px auto;position: static">

        <div class="header" style="font-size: 12px">错误提示</div>
                <div class="content ">
                    <?php
                    switch ($code) {
                        default:
                            ?>
                            <p class="success"><?php echo(strip_tags($msg));?></p>
                            <?php break;?>
                        <?php case 0:?>
                             <span  class="glyphicon glyphicon-info-sign" style="color:#000"></span>
                            <p class="error"><?php echo(strip_tags($msg));?></p>
                            <?php break;?>
                        <?php
                    } ?>
                </div>
        <div class="actions">
            <p class="jump" style="line-height: 35px">页面自动 <a id="href" href="<?php echo($url);?>">跳转</a> 等待时间： <b id="wait"><?php echo($wait);?></b>
            </p>
        </div>
        <!-- /.modal-dialog -->
    </div>
{if !isset($data['auto'])}
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
</body>
</html>