App({
    onLaunch: function() {
        console.log("=================onLaunch==================");
    },
    onShow: function() {
        console.log("=================onShow==================");
    },
    onHide: function() {},
    util: require("static/js/utils/util.js"),
    Lang: require("static/js/utils/lang.js"),
    WxParse: require("./library/wxParse/wxParse.js"),
    ext: {
        siteInfo: {
            uniacid: "2",
            acid: "2",
            siteroot: "https://www.aebug.cn/app/wxapp.php",
            sitebase: "https://www.aebug.cn/app",
            module: "we7_wmall"
        }
    },
    navigator: {
        list: [ {
            link: "pages/order/index",
            icon: "icon-order"
        }, {
            link: "pages/order/tangshi/index",
            icon: "icon-order"
        }, {
            link: "pages/shop/home",
            icon: "icon-mine"
        }, {
            link: "pages/shop/setting",
            icon: "icon-mine"
        } ],
        position: {
            bottom: "80px"
        }
    }
});