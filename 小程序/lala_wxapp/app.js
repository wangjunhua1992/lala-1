App({
    onLaunch: function() {
        console.log("=================onLaunch==================");
    },
    onShow: function() {
        console.log("=================onShow==================");
    },
    onHide: function() {},
    iwebsocket: require("static/js/utils/websocket.js"),
    util: require("static/js/utils/util.js"),
    Lang: require("static/js/utils/lang.js"),
    WxParse: require("./library/wxParse/wxParse.js"),
    ext: {
        siteInfo: {
            uniacid: "3",
            acid: "3",
          siteroot: "http://localhost/app/wxapp.php",
          sitebase: "http://localhost/app",
            module: "we7_wmall"
        },
        diy: {
            home: 0
        }
    },
    menu: {
        css: {},
        params: {
            navstyle: 0
        },
        position: {
            bottom: "80px",
            right: "10px",
            left: "inherit"
        },
        data: [ {
            link: "pages/home/index",
            icon: "icon-home",
            text: "首页"
        }, {
            link: "pages/order/index",
            icon: "icon-order",
            text: "订单"
        }, {
            link: "pages/member/mine",
            icon: "icon-mine",
            text: "我的"
        } ]
    }
});