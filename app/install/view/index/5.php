{include file="index/head" /}


<div class="install-box">
    <fieldset class="layui-elem-field layui-field-title">
        <legend>正在安装程序</legend>
    </fieldset>

    <table class="layui-table">

        <thead>
        <tr>
            <td>表名字</td>
            <td>状态</td>
        </tr>
        </thead>
        <tbody>

        <tr> <td>温馨提示:</td><td><span id="">如果下载过程中卡住,请修改mysql配置文件,将sql-mode配置为:  sql-mode=NO_ENGINE_SUBSTITUTION</span></td></tr>
        {foreach $sys_tables.data as $table}
        <tr id="{$table.table_name}">
            <td>{$table.table_name}</td>
            <td id="{$table.table_name}_status">等待操作</td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    <div class="step-btns">
        <a href="javascript:history.go(-1);" class="layui-btn layui-btn-primary layui-btn-big fl">返回上一步</a>
        <button type="submit" onclick="check_status()" class="layui-btn layui-btn-big layui-btn-normal fr" lay-submit="" lay-filter="formSubmit">下一步</button>
    </div>
</div>

<script>
    var next_step_url = "{:url('install/index/start_install' , ['step'=>6])}";
    var tables = {:json_encode($sys_tables)};
    var tables = tables.data;
    var install_model_api = "{:url('install/index/install_model')}";

    function install_tables() {
        layui.use(['layer'] , function () {
            var $ = layui.$ , layer = layui.layer;
            $("#" +tables[0].table_name + "_status" ).html("正在安装");
            $.get(install_model_api , {

                table: tables[0].table_name , module : 'system'

            } , function (data) {
                console.log(data);
                if(data.code!=1){
                    $("#" +tables[0].table_name + "_status" ).html("安装失败");
                    layer.msg(data.msg);
                }else{
                    $("#" +tables[0].table_name + "_status" ).html("安装完成");
                    layer.msg(data.msg);
                }
                tables.splice(0 ,1);
                if(tables.length> 0){
                    setTimeout(function () {
                        install_tables();
                    } , 100)
                }
            } , 'json').fail(function() {
                layer.msg('安装失败,尝试从安装该表!', {
                    icon: 2,
                    time: 1000 //2秒关闭（如果不配置，默认是3秒）
                }, function(){
                    //do something
                    if(tables.length> 0){
                        setTimeout(function () {
                            install_tables();
                        } , 100)
                    }
                });


            });
        });
    }
    layui.use(['layer'] , function () {

        var $ = layui.$ , layer = layui.layer;

        $(document).ready(function () {
            if(tables.length> 0){
                install_tables();
            }else{
                $("#tips").html("更新完成");
            }
        });
    });

    function check_status() {
        if(tables.length == 0){
            window.location.href=next_step_url;
        }else{
            layer.msg("请等待数据表更新完成，再进入下一步");
        }
    }

</script>

{include file="index/foot" /}
