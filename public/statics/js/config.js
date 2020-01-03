(function (require, define) {

    var path_obj = {
        baseUrl: '/statics/js',
        paths: {
            'async': '../packs/requirejs/async',
            'braui': '../packs/braui/braui',
            "qiniu": "../packs/qiniu/qiniu.min",
            'wx': ['../packs/weixin/jweixin-1.3.0'],
            'layui': '../packs/layui/layui',
            'semantic': '../packs/semantic/semantic.min',
            'swiper4': '../packs/swiper/swiper4.min',
            'BMap': ['https://api.map.baidu.com/api?v=2.0&ak=F51571495f717ff1194de02366bb8da9&s=1'],
            'css': './css.min',
            'html2canvas': ['../packs/html2canvas/html2canvas.min'],
            'Vue': ['../packs/vue/vue'],
            'jquery': '../packs/jquery/jquery.min',
            'jquery.qrcode': '../packs/qrcode/jquery.qrcode.min',
            'jquery.jplayer': '../packs/jplayer/jquery.jplayer.min',
            'jquery.zclip': '../packs/zclip/jquery.zclip.min',
            'bootstrap': 'https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min',
            'swiper': '../packs/swiper/swiper.jquery.min',
            'VueLazyload': '../packs/vue_packs/vue-lazyload',
            "plupload": "../packs/plupload/plupload.full.min",
            "echarts": "../packs/echarts/echarts.min",
            "neditor.config": "../packs/neditor/neditor.config",
            "neditor": "../packs/neditor/neditor.all",
            "zeroclipboard": "../packs/neditor/third-party/zeroclipboard/ZeroClipboard.min",

            "ueditor.config": "../packs/ueditor/ueditor.config",
            "ueditor": "../packs/ueditor/ueditor.all",
            'jquery.ztree' : "../packs/ztree/js/jquery.ztree.all.min",
        },
        shim: {

            'BMap': {
                exports: 'BMap'
            },
            'neditor': {
                deps: ['zeroclipboard','neditor.config'],
                exports: 'UE',
                init:function(ZeroClipboard){
                    window.ZeroClipboard = ZeroClipboard;
                }
            },

            'ueditor': {
                deps: ['zeroclipboard','ueditor.config'],
                exports: 'UE',
                init:function(ZeroClipboard){
                    window.ZeroClipboard = ZeroClipboard;
                }
            },
            'qiniu': {
                deps: ['jquery', 'plupload', 'braui'],
            },
            'jquery.ztree': {
                deps: ['jquery'],
                exports: "ztree"
            },

            'plupload': {
                deps: ['jquery'],
                exports: "plupload"
            },
            'braui': {
                deps: ['jquery'],
                exports: "braui"
            },
            'weui': {
                deps: ['jquery'],
                exports: "weui"
            },
            'mhcms_level_picker': {
                deps: ['jquery', 'weui'],
                exports: "level_picker"
            },
            'wx': {
                exports: "wx"
            },
            'VueLazyload': {
                deps: ['jquery'],
                exports: "VueLazyload"
            },
            "Vue": {"exports": "Vue"}
            ,

            'swiper': {
                deps: ['jquery', 'css!../packs/swiper/swiper.min']
            },
            swiper4: {
                deps: ['css!../packs/swiper/swiper4.min']
            },
            'layui': {
                exports: "layui",
                deps: ['jquery', 'css!../packs/layui/css/layui.css']
            },
            'semantic': {
                deps: ['jquery', 'css!../packs/semantic/semantic.min']
            },
            'jquery.jplayer': {
                exports: "$",
                deps: ['jquery']
            },
            'bootstrap': {
                exports: "$",
                deps: ['jquery']
            },
            'map': {
                exports: 'BMap'
            },
            'jquery.qrcode': {
                exports: "$",
                deps: ['jquery']
            },
        },
        waitSeconds: 0,

    };

    if (typeof urlArgs !== "undefined") {
        path_obj.urlArgs = urlArgs;
    }
    require.config(path_obj);

})(require, define);
