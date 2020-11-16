var e = getApp();

Page({
    data: {
        showshare: !1,
        Lang: e.Lang,
        wuiLoading: {
            show: !0,
            img: e.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(a) {
        var t = this;
        e.util.request({
            url: "spread/index",
            data: {
                menufooter: 1
            },
            success: function(a) {
                e.util.loaded();
                var s = a.data.message;
                -1e3 == s.errno ? wx.redirectTo({
                    url: "register",
                    fail: function(e) {
                        console.log("=============="), console.info(e);
                    }
                }) : (t.setData(s.message), wx.setNavigationBarTitle({
                    title: s.message.basic.menu_name
                }));
            }
        });
    },
    onJsEvent: function(a) {
        e.util.jsEvent(a);
    },
    onToggleStatus: function() {
        var e = this;
        e.setData({
            showshare: !e.data.showshare
        });
    }
});