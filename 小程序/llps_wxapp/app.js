App({
    util: require("static/js/utils/util.js"),
    Lang: require("static/js/utils/lang.js"),
    onLaunch: function() {},
    onShow: function() {},
    onHide: function() {},
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
    }
});