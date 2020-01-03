/**
 * MHCMS 上传模块
 * Autor: New Better
 * site: www.mhcms.net
 * Date :2017-10-05
 */
layui.define(['element', 'upload', 'layer', 'jquery'], function (exports) {
    var $ = layui.$,
        device = layui.device(),
        layer = layui.layer,
        upload = layui.upload,
        element = layui.element;

    var bra_upload = {

        init_mutil_wx_upload: function (field_name, number) {
            var wx_choose = {
                elem: $('.layui_mutil_upload_' + field_name),
                sum: 0,
                number: number || 1,
                choose: function () {
                    var that = this;
                    require(['wx', 'mhcms'], function (wx, mhcms) {
                        mhcms.get_sign(location.href, function (jssdk_obj) {
                            wx.config(jssdk_obj);
                            wx.ready(function () {
                                wx.chooseImage({
                                    count: 5, // 默认9
                                    sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
                                    sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
                                    success: function (res) {
                                        that.localIds = res.localIds;
                                        that.upload(0);
                                    }
                                });
                            });
                        });


                    });

                }
                ,
                upload: function (i) {
                    var that = this;
                    var length = that.localIds.length; //本次要上传所有图片的数量

                    require(['wx', 'mhcms'], function (wx, mhcms) {

                        mhcms.get_sign(location.href, function (jssdk_obj) {
                            wx.config(jssdk_obj);

                            wx.ready(function () {
                                wx.uploadImage({
                                    localId: that.localIds[i], //图片在本地的id
                                    isShowProgressTips: 1,
                                    success: function (res) {
                                        var download_api = "/wechat/service/download_media";
                                        // download media
                                        layui.use(['layer'], function () {
                                            var $ = layui.$;
                                            $.get(
                                                download_api, {
                                                    "media_id": res.serverId
                                                }, function (data) {
                                                    // add preview image to the list
                                                    var $real_form_name = that.elem.data('name');
                                                    $('#' + field_name).append('<div id="' + field_name + '_' + i + '_img" class="layui-upload-img  weui-uploader__file"><img src=\"' + data.url + '\" alt=\"' + '\" class=\"layui-upload-img\"><input type="hidden" value="' + data.data.file_id + '" name="' + $real_form_name + '"></div>');
                                                }, 'json'
                                            );
                                        });

                                        i++;
                                        that.sum++;

                                        if (i < length) {
                                            that.upload(i);
                                        }

                                    },
                                    fail: function (res) {
                                        alert(JSON.stringify(res));
                                    }
                                });
                            });
                        });

                    });

                }
            };

            layui.use(['layer'], function () {
                var $ = layui.$;
                $('.layui_mutil_upload_' + field_name).on('click', function () {
                    wx_choose.choose();
                });
            });
        },

        init_mutil_upload: function (field_name, number, accept, ext, data) {
            //执行实例
            if (!accept) {
                accept = 'image/*';
            }

            data = data || {};
            var loading;
            number = number || 1;
            console.log(accept, ext);

            var demoListView = $('#' + field_name);
            var uploadInst = upload.render({
                elem: '.layui_mutil_upload_' + field_name //绑定元素
                , url: upload_url  //上传接口
                , multiple: true
                , accept: accept
                , data: data
                , number: number
                , exts: ext
                , uploaded: 0
                , getJsonLength: function (jsonData) {
                    var jsonLength = 0;
                    for (var item in jsonData) {
                        jsonLength++;
                    }
                    return jsonLength;
                }
                , choose: function (obj) {
                    console.log(obj);
                    var root = this;
                    var len = root.getJsonLength(this.files);
                    console.log(root.uploaded, len, number , root.files);

                    if (parseInt(len) + root.uploaded >= number) {
                        layer.msg("不能超过" + number + "个" , function () {
                            layer.close(loading);
                        });
                        return false;
                    }

                    this.files = obj.pushFile();
                    //todo check md5
                    obj.preview(function (index, file, result) {
                        var bra_annex = $(['<div id="' + field_name + '_' + index + '_img" class="layui-upload-img  weui-uploader__file"><img src=\"' + result + '\"' +
                            ' alt=\"' + file.name + '\" class=\"layui-upload-img\"><i class=\'icon close\'></i></div>'].join(''));
                            bra_annex.find('.close').on('click', function () {
                                delete root.files[index];
                                root.uploaded --;
                                bra_annex.remove();
                                uploadInst.config.elem.next()[0].value = '';
                            });
                            demoListView.append(bra_annex);
                    });

                }
                ,
                before: function (obj) {
                    var root = this;
                    loading = layer.load(1);
                }
                ,
                done: function (res, index, upload) {
                    layer.close(loading);
                    var item = this.item; // 当前元素
                    console.log(this);
                    this.uploaded++;
                    var $real_form_name = item.data('name');
                    $("#" + field_name + '_' + index + '_img').append('<input type="hidden" value="' + res.id + '" name="' + $real_form_name + '">');

                    delete this.files[index];
                    if (JSON.stringify(this.files) == "{}") {
                        layer.msg(res.msg);
                    }
                }
                ,
                error: function (res, index, upload) {
                    /**
                     *  var tr = demoListView.find('tr#upload-'+ index)
                     ,tds = tr.children();
                     tds.eq(2).html('<span style="color: #FF5722;">上传失败</span>');
                     tds.eq(3).find('.demo-reload').removeClass('layui-hide'); //显示重传
                     */
                    layer.close(loading);
                }
            });
        }
    };
    exports('bra_upload', bra_upload);
});