{include file="index/head" /}

<div class="install-box">
    <fieldset class="layui-elem-field layui-field-title">
        <legend>数据库配置</legend>
    </fieldset>
    <form class="layui-form layui-form-pane" action="" method="post">
        <div class="layui-form-item">
            <label class="layui-form-label">服务器地址</label>
            <div class="layui-input-inline w200">
                <input type="text" class="layui-input" name="hostname" lay-verify="title" value="127.0.0.1">
            </div>
            <div class="layui-form-mid layui-word-aux">数据库服务器地址，一般为127.0.0.1</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">数据库端口</label>
            <div class="layui-input-inline w200">
                <input type="text" class="layui-input" name="hostport" lay-verify="title" value="3306">
            </div>
            <div class="layui-form-mid layui-word-aux">系统数据库端口，一般为3306</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">数据库名称</label>
            <div class="layui-input-inline w200">
                <input type="text" class="layui-input" name="database" value="{:config('database.database')}"  lay-verify="title">
            </div>
            <div class="layui-form-mid layui-word-aux">系统数据库名,必须包含字母</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">数据库账号</label>
            <div class="layui-input-inline w200">
                <input type="text" class="layui-input" name="username" value="{:config('database.username')}" lay-verify="title">
            </div>
            <div class="layui-form-mid layui-word-aux">连接数据库的用户名</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">数据库密码</label>
            <div class="layui-input-inline w200">
                <input type="password" class="layui-input" value="{:config('database.password')}" name="password"  lay-verify="title">
            </div>
            <div class="layui-form-mid layui-word-aux">连接数据库的密码</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">数据库前缀</label>
            <div class="layui-input-inline w200">
                <input type="text" class="layui-input" name="prefix" lay-verify="title" readonly value="bra_">
            </div>
            <div class="layui-form-mid layui-word-aux">建议使用默认,数据库前缀必须带 '_'</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">覆盖数据库</label>
            <div class="layui-input-inline w200">
                <input type="radio" name="cover" value="1" title="覆盖" checked>
                <input type="radio" name="cover" value="0" title="不覆盖" disabled>
            </div>
            <div class="layui-form-mid layui-word-aux">如果数据库存在将会被覆盖掉，安装之前请先备份旧数据</div>
        </div>
        <div class="layui-form-item">
            <button class="layui-btn fl" style="margin-left:120px;" lay-submit="" lay-filter="formSubmit_database">测试数据库连接</button>
        </div>
    </form>
    <form class="layui-form layui-form-pane" action="{:U('install/index/start_install' , ['step'=>4])}" method="post">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>管理账号设置</legend>
        </fieldset>
        <div class="layui-form-item">
            <label class="layui-form-label">管理员账号</label>
            <div class="layui-input-inline w200">
                <input type="text" class="layui-input" name="account" lay-verify="required">
            </div>
            <div class="layui-form-mid layui-word-aux">管理员账号最少4位</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">管理员密码</label>
            <div class="layui-input-inline w200">
                <input type="password" class="layui-input" name="password" lay-verify="required">
            </div>
            <div class="layui-form-mid layui-word-aux">保证密码最少6位</div>
        </div>
        <div class="step-btns">
            <a href="javascript:history.go(-1);" class="layui-btn layui-btn-primary layui-btn-big fl">返回上一步</a>
            <button type="submit" class="layui-btn layui-btn-big layui-btn-normal fr" lay-submit="" lay-filter="formSubmit">立即执行安装</button>
        </div>
    </form>
</div>


<script>
    var connected = 0;
    layui.use(['layer' , 'form'] , function () {
        var $ = layui.$;
        var form = layui.form;

        form.on('submit(formSubmit_database)', function (data) {
            console.log(data.elem) //被执行事件的元素DOM对象，一般为button对象
            console.log(data.form) //被执行提交的form对象，一般在存在form标签时才会返回
            console.log(data.field) //当前容器的全部表单字段，名值对形式：{name: value}

            var api_url = "{:U('install/index/do_step_2')}";

            $.post(api_url, data.field, function (data) {
                layer.msg(data.msg);

                if (data.code == 0) {
                    connected = 0;
                }
                if (data.code == 1) {
                    connected = 0;
                }
                if (data.msg == "数据库连接成功") {
                    connected = 1;
                }
                console.log(data);
            } , 'json');

            return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
        });


        form.on('submit(formSubmit)', function (data) {

            if (connected == 0) {
                layer.msg("请先测试数据库连接是否成功！");
                return false;
            }


        });
    });

    function check_errors() {


    }
</script>
{include file="index/foot" /}
