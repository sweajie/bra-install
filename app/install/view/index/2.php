{include file="index/head" /}
<div class="install-box">
    <fieldset class="layui-elem-field layui-field-title">
        <legend>填写授权码 请加QQ群:496729831 或者QQ:1620298436 获取授权码</legend>
    </fieldset>

    <form class="layui-form" action="{:url('install/index/start_install' ,['step'=>2])}" method="post">

        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">授权码</label>
            <div class="layui-input-block">
                <textarea name="product_licence_code" id="product_licence_code" placeholder="请输入授权码"
                          class="layui-textarea">{$product_licence_code}</textarea>
            </div>
        </div>

    </form>

    <div class="step-btns">
        <a href="javascript:history.go(-1);" class="layui-btn layui-btn-primary layui-btn-big fl">返回上一步</a>
        <a onclick="check_errors(this)" data-url="{:url('install/index/check')}"
           class="layui-btn layui-btn-big layui-btn-normal fr">进行下一步</a>
    </div>
</div>

<script>
    function check_errors(obj) {
        layui.use(['layer', 'form', 'jquery'], function () {
            var layer = layui.layer
                , form = layui.form, $ = layui.$;


            var next_url = "{:url('install/index/start_install' ,[ 'step'=>3])}";
            var api_url = $(obj).data('url');

            var $product_licence_code = $("#product_licence_code").val();

            $.post(api_url, {
                    product_licence_code: $product_licence_code
                },
                function (data) {
                    console.log(data);
                    if (data.code == "1") {

                        window.location.href = next_url;
                    } else {
                        layer.msg(data.msg);
                    }
                }
            );
        });
    }
</script>
{include file="index/foot" /}
