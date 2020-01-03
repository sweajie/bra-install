define("braui", ["jquery", 'layui'], function ($, layui) {
    console.log('%c 欢迎使用Bra内容管理系统 ^_^ ', "color:red");
    console.log("%c 官方网站  bra.mhcms.net", "color:orange;font-weight:bold");
    return {
        bra_form: function ($target, mini_url, submit_filter, callback) {
            var $this = this;
            submit_filter = submit_filter || '*';
            layui.use(['form', 'layer'], function () {
                var form = layui.form, layer = layui.layer;
                form.render();
                form.on('submit(' + submit_filter + ')', function (data) {
                    $(".layui-form-item").removeAttr("style");
                    var layer_idx = layer.load(1, {
                        shade: [0.5, '#ccc'], shadeClose: false
                    });
                    $.post($target, data.field, function (ret_data) {
                        layer.close(layer_idx);
                        console.log(ret_data);
                        if (callback && typeof callback === "function") {
                            callback(ret_data);
                        } else {
                            if (ret_data.code === 1) {
                                layer.msg(ret_data.msg, {time : 500 ,icon: 1, shade: [0.8, '#393D49']}, function () {
                                    if (ret_data.javascript) {
                                        eval(ret_data.javascript);
                                    } else {
                                        if (ret_data.url) {
                                            window.location.href = ret_data.url;
                                        } else {
                                            layer.close(layer.index);
                                            layer.close($this.last_frame_idx);
                                            window.location.reload();
                                        }
                                    }
                                });
                            } else {
                                layer.msg(ret_data.msg, {icon: 2}, function () {
                                    //do something
                                    if (ret_data.data && ret_data.data.field_name) {
                                        var tr = $("#row_" + ret_data.data.field_name);
                                        tr.css("color", "red");
                                        $("html,body").animate({scrollTop: tr.offset().top}, 1000)
                                    }
                                });
                            }
                        }
                    }, 'json');
                    return false;
                });
            });
        },

        bra_init: function () {

            var bra_base = this; //鸣鹤CMS核心框架

            bra_base.bra_bottom_action = function (obj) {
                console.log(obj.data('form'));
                var form = $(".list_form").data('form');
                var action = obj.data('href');
                $('.' + form).attr('action', action);
                //$("input[type='checkbox']").attr('value')
                var ids = [];
                $(':checkbox:checked.chain').each(function (i) {
                    ids[i] = $(this).val();
                });

                var action_type = obj.data('action_type');
                if (action_type === 'confirm') {
                    layer.confirm('您确定吗?', {
                        icon: 3,
                        title: '提示'
                    }, function (index) {
                        $.post(action, {ids: ids}, function (data) {
                            console.log(data);
                            layer.msg(data.msg, function () {
                                window.location.reload();
                            });
                        }, 'json');
                    });
                }

                if (action_type === 'iframe') {
                    layui.use(['layer'], function () {

                        var index = layer.open({
                            type: 2,
                            fix: true, //固定位置
                            title: obj.attr('title'),
                            shadeClose: true, //点击背景关闭
                            shade: 0.4,  //透明度
                            content: action + '?ids=' + ids,
                            area: ['98%', '98%'],
                            maxmin: true
                        });

                    });
                }
            };

            bra_base.bra_frame = function (obj) {
                var that = this;
                layui.use(['layer', 'form'], function () {
                    bra_base.loading('open');
                    var $ = layui.$;
                    var form = layui.form;
                    var url = obj.attr('href') || obj.data('href');
                    $.get(url, function (data) { //获取网址内容 把内容放进data
                        var title = obj.attr('title');
                        if (obj.data('hide_title') == 1) {
                            title = false;
                        }
                        bra_base.loading('close'); //关闭加载层

                        if (data.code && data.code === 3) {
                            return window.location.href = data.url
                        }

                        if (data.code && data.code > 1) {
                            layer.msg(data.msg, function () {
                                if (data.callback) {
                                    return eval(data.callback);
                                }
                            });
                            return
                        }
                        if (data.code == 0) {
                            layer.msg(data.msg);
                        } else {
                            console.log(888);
                            that.last_frame_idx = layer.open({
                                type: 1,
                                zIndex: 2000,
                                fix: true, //固定位置
                                title: title,
                                shadeClose: true, //点击背景关闭
                                shade: 0.4,  //透明度
                                content: data,
                                area: [obj.attr('width'), obj.attr('height')],
                                success: function (layero, index) {
                                    form.render();
                                }
                            });
                        }
                    }, 'json');
                });
            };
//Load a iframe
            bra_base.bra_iframe = function (obj) {

                layui.use(['layer'], function () {

                    var index = layer.open({
                        type: 2,
                        fix: true, //固定位置
                        title: obj.attr('title'),
                        shadeClose: true, //点击背景关闭
                        shade: 0.4,  //透明度
                        content: obj.attr('href'),
                        area: ['98%', '98%'],
                        maxmin: true
                    });

                });
            };
//load a url , expecting a json data with status


            bra_base.bra_load = function (obj) {
                bra_base.loading('open');
                var url = obj.attr('href') || obj.data('href');
                layui.use(['layer'], function () {
                    $.get(url, function (data) {
                        bra_base.loading('close');
                        layer.msg(data.msg, {
                            icon : data.code ,
                            time: 500
                        }, function () {
                            if (data.url && !data.callback) {
                                bra_base.bra_goto(data.url)
                            }

                            if (data.callback) {
                                return eval(data.callback);
                            }

                        });

                    }, 'json');
                });
            };

            bra_base.bra_load_url = function (url) {
                bra_base.loading('open');

                layui.use(['layer'], function () {
                    $.get(url, function (data) {
                        bra_base.loading('close');
                        layer.msg(data.msg, {icon: data.code, time: 500}, function () {
                            if (data.url && !data.callback) {
                                bra_base.bra_goto(data.url)
                            }

                            if (data.callback) {
                                return eval(data.callback);
                            }

                        });

                    }, 'json');
                });
            };
//confirm url , expecting a json data with status
            bra_base.bra_confirm = function (obj) {
                var url = obj.attr('href') || obj.data('href');
                var title = obj.data('title') || '提示';
                layui.use(['layer'], function () {
                    var $ = layui.$;
                    layer.confirm('您确定吗?', {
                        icon: 3,
                        title: title
                    }, function (index) {
                        bra_base.loading('open');
                        $.get(url, function (data) {
                            bra_base.loading('close');
                            layer.close(index);
                            layer.msg(data.msg, {icon: data.code}, function () {
                                if (data.javascript) {
                                    eval(data.javascript);
                                }
                                if (data.callback) {
                                    eval(data.callback);
                                }

                                if (data.url) {
                                    window.location.href = data.url;
                                }
                            });

                        }, 'json');
                    });
                });
            };
//confirm url , expecting a json data with status
            bra_base.bra_confirm_frame = function (obj) {
                var url = obj.attr('href');
                layui.use(['layer'], function () {
                    var confirm_idx = layer.confirm('您确定吗?', {
                        icon: 3,
                        title: '提示'
                    }, function (index) {
                        layer.close(confirm_idx);
                        bra_base.bra_frame(obj);
                    });
                });
            };
            bra_base.bra_new_tab = function (obj) {
                var url = obj.attr('href');
                window.open(url, '_blank');
            };
//input 编辑
            bra_base.bra_input_blur = function (obj) {
                layui.use(['layer'], function () {
                    var url = single_url;
                    bra_base.loading('open');
                    $.get(url, {
                        'field': obj.attr('field'),
                        'field_value': obj.val(),
                        'pk': obj.attr('pk'),
                        'pk_value': obj.attr('pk_value'),
                        'model': obj.attr('model'),
                    }, function (data) {
                        bra_base.loading('close');
                        layer.msg(data.msg, {icon: data.code});
                    }, 'json');
                });
            };
//元素编辑
            bra_base.bra_element_blur = function (obj) {
                layui.use(['layer'], function () {
                    var url = single_url;
                    bra_base.loading('open');
                    var value = obj.html();
                    $.get(url, {
                        'field': obj.attr('field'),
                        'field_value': value,
                        'pk': obj.attr('pk'),
                        'pk_value': obj.attr('pk_value'),
                        'model': obj.attr('model'),
                    }, function (data) {
                        bra_base.loading('close');
                        layer.msg(data.msg, {icon: data.code});
                    }, 'json');
                });
            };
//全部选中
            bra_base.bra_check_all = function (obj) {
                layui.use(['layer'], function () {
                    var $ = layui.$;
                    var child = obj.data('rel');
                    $(".child_" + child).prop('checked', obj.prop("checked"));
                });
            };
//弹框选择
            bra_base.bra_form_picker = function (obj) {

                layui.use(['layer'], function () {
                    layer.open({
                        content: 'test'
                        , btn: ['确定', '关闭']
                        , yes: function (index, layero) {
                            //按钮【按钮一】的回调
                            layer.close(index);
                        }
                        , btn2: function (index, layero) {
                            //按钮【按钮二】的回调

                            //return false 开启该代码可禁止点击该按钮关闭
                        },
                        success: function (layero, index) {

                            console.log(layero, index);
                        }
                    });
                });
            };
//icon selector
            bra_base.bra_icon_form_picker = function (obj) {
                var service = obj.data('service');
                layui.use(['layer'], function () {
                    layer.open({
                        content: 'test'
                        , btn: ['确定', '关闭']
                        , yes: function (index, layero) {
                            //按钮【按钮一】的回调
                            layer.close(index);
                        }
                        , btn2: function (index, layero) {
                            //按钮【按钮二】的回调

                            //return false 开启该代码可禁止点击该按钮关闭
                        },
                        success: function (layero, index) {
                            $.get(service, {}, function (data) {

                            });
                            console.log(layero, index);
                        }
                    });
                });
            };

            bra_base.bra_normal = function (obj) {
                this.bra_iframe(obj);
            };

            bra_base.bra_element_tips = function (obj, init) {

                var pos = obj.data('pos') || 1;
                layer.tips(obj.data('title'), obj, {
                    tips: pos
                });
            };
            bra_base.bra_element_linkage_select = function (obj, init) {

                function clear_linkage(linkage_group) {
                    //alert('.sub_linkage_select' + "." + linkage_group);
                    $('.sub_linkage_select' + "." + linkage_group).html("");
                }

                //api service
                var service = obj.data('service');
                //current value
                var id_key_val = obj.val();
                //连动分组
                var linkage_group = obj.data('linkage_group');
                if (id_key_val == "0") {
                    return clear_linkage(linkage_group)
                }

                var model_id = obj.data('current_model_id');
                var target_field = obj.data('target_field');
                var from_field = obj.data('from_field');

                if (!target_field || !from_field) {
                    return false;
                }
                bra_base.loading('open');
                $.get(service, {
                    model_id: model_id,
                    target_field: target_field,
                    from_field: from_field,
                    id_key_val: id_key_val
                }, function (data) {
                    bra_base.loading('close');
                    var rows = data.data;
                    var $str = "";
                    $.each(rows, function (index) {
                        var select = " ";
                        if (rows[index].id == $("#" + target_field).data('default_value')) {
                            select = "selected";
                        }
                        $str += "<option " + select + " value='" + rows[index].id + "'>" + rows[index].name + "</option>";
                    })


                    if (init == 1) {
                        var target = $("#" + target_field).val();
                        if (target == 0 || target == "") {
                            $("#" + target_field).html($str);
                        }
                    } else {
                        $("#" + target_field).html($str);
                    }

                }, 'json');
            };

            bra_base.reload_page = function () {
                layui.use(['layer'], function () {
                    var $ = layui.$;
                    $('#larry_tab_content').children('.layui-show').children('iframe')[0].contentWindow.location.reload(true);
                });
            };
            bra_base.bra_element_view_image = function (obj) {

                var root = this;
                var url = $(obj).data('url') || $(obj).attr('src');
                root.loading('open');
                layui.img(url, function () {

                    root.loading('close');
                    layer.open({
                        type: 1,
                        title: false,
                        closeBtn: 1,
                        area: ['630px'],
                        skin: 'layui-layer-nobg', //没有背景色
                        shadeClose: true,
                        content: "<img style='min-width:630px;min-height:230px; ' src='" + url + "' />"
                    });
                }, function () {

                });

            };
            bra_base.bra_element_view_video = function (obj) {
                var file_id = $(obj).data('file_id');
                layer.open({
                    type: 2,
                    title: false,
                    area: ['98%', '98%'],
                    shade: 0.8,
                    closeBtn: 0,
                    shadeClose: true,
                    content: '/core/frame/view_video?file_id=' + file_id
                });

            };
            bra_base.loading = function (type) {
                require(['layui'], function (layui) {
                    layui.use(['layer'], function () {
                        var $ = layui.$, layer = layui.layer;
                        if (type == "open") {
                            loading_layer = layer.load(1, {
                                shade: [0.5, '#ccc'] //0.1透明度的白色背景shade: 0
                                , shadeClose: false
                            });
                        } else {
                            layer.close(loading_layer);
                        }
                    });
                });
            };

            bra_base.bra_tab = function (obj) {
                parent.open_tab(obj);
            };

            layui.use(['layer'], function () {
                var $ = layui.$, layer = layui.layer;
                $(document).on("click", "a[mini]", function (e) {
                    e.preventDefault();//阻止默认动作
                    eval("bra_base.bra_" + $(this).attr('mini') + "($(this))");
                });


                $(document).on("click", "[bra-mini]", function (e) {
                    e.preventDefault();//阻止默认动作
                    eval("bra_base.bra_" + $(this).attr('bra-mini') + "($(this))");
                });


                $(document).on("blur", "input[mini='blur']", function (e) {
                    e.preventDefault();//阻止默认动作
                    eval("bra_base.bra_input_" + $(this).attr('mini') + "($(this))");
                });

                $(document).on("blur", "[mini='element_blur']", function (e) {
                    e.preventDefault();//阻止默认动作
                    eval("bra_base.bra_element_" + $(this).attr('mini') + "($(this))");
                });

                $(document).on("change", "[mini='linkage_select']", function (e) {
                    e.preventDefault();//阻止默认动作
                    eval("bra_base.bra_element_" + $(this).attr('mini') + "($(this))");
                });

                $(document).on("click", "input[mini='chain']", function (e) {
//    e.preventDefault();
                    var chain = $(this).data('chain');
                    console.log($(this).prop("checked"));
                    $(".chain").prop("checked", $(this).prop("checked"));
                });
                $(document).on("click", "[mini='view_image']", function (e) {
                    e.preventDefault();//阻止默认动作
                    eval("bra_base.bra_element_" + $(this).attr('mini') + "($(this))");
                });

                $(document).on("click", "[mini='view_video']", function (e) {
                    e.preventDefault();//阻止默认动作
                    eval("bra_base.bra_element_" + $(this).attr('mini') + "($(this))");
                });

                $(document).on("mouseover", "[mini='tips']", function (e) {
                    e.preventDefault();//阻止默认动作
                    eval("bra_base.bra_element_" + $(this).attr('mini') + "($(this))");
                });
            });
        }
    }
});