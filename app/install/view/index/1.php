{include file="index/head" /}


<div class="install-box">
    <fieldset class="layui-elem-field layui-field-title">
        <legend>运行环境检测</legend>
    </fieldset>
    <table class="layui-table" lay-skin="line">
        <thead>
        <tr>
            <th>环境名称</th>
            <th>当前配置</th>
            <th>所需配置</th>
        </tr>
        </thead>
        <tbody>
        {volist name="data.env" id="vo"}
        <tr class="{$vo[4]}">
            <td>{$vo[0]}</td>
            <td>{$vo[3]}</td>
            <td>{$vo[2]}</td>
        </tr>
        {/volist}
        </tbody>
    </table>
    <table class="layui-table" lay-skin="line">
        <thead>
        <tr>
            <th>目录/文件</th>
            <th>所需权限</th>
            <th>当前权限</th>
        </tr>
        </thead>
        <tbody>
        {volist name="data.dir" id="filemod"}
        <tr class="<?php echo $filemod['is_writable'] ? "yes" : 'no'?>">
            <td><?php echo $filemod['file']?></td>
            <td><span>可写</td>
            <td ><?php echo $filemod['is_writable'] ? '可写' : '不可写'?></td>
        </tr>
        {/volist}
        </tbody>
    </table>
    <table class="layui-table" lay-skin="line">
        <thead>
        <tr>
            <th>函数/扩展</th>
            <th>类型</th>
            <th>结果</th>
        </tr>
        </thead>
        <tbody>
        {volist name="data.func" id="vo"}
        <tr class="{$vo[2]}">
            <td>{$vo[0]}</td>
            <td>{$vo[3]}</td>
            <td>{$vo[1]}</td>
        </tr>
        {/volist}
        </tbody>
    </table>
    <div class="step-btns">
        <a href="javascript:history.go(-1);" class="layui-btn layui-btn-primary layui-btn-big fl">返回上一步</a>
        <a onclick="check_errors(this)" data-url="{:U('install/index/start_install' ,['step'=> 2])}" class="layui-btn layui-btn-big layui-btn-normal fr">进行下一步</a>
    </div>
</div>


<script>

    function check_errors(obj) {
        layui.use(['layer' , 'jquery'], function(){
            var layer = layui.layer
                ,form = layui.form , $ = layui.$;
            if($('.no').length > 0){
                layer.msg('您好错误数量为' + $('.no').length  +  " ，无法继续");
            }else{
                window.location.href = $(obj).data('url');
            }

        });
    }


</script>

{include file="index/foot" /}
