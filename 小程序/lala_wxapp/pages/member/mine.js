var t = getApp();

Page({
    data: {
        Lang: t.Lang,
        wuiLoading: {
            show: !0,
            img: t.util.getStorageSync("theme.loading.img")
        },
        Logged: !1
    },
    onShow: function() {},
    onLoad: function() {
        var e = this;
        t.util.request({
            url: "wmall/member/mine",
            data: {
                menufooter: 1
            },
            success: function(a) {
                t.util.loaded();
                var o = a.data.message;
                if (o.errno) t.util.toast(o.message, "", 3e3); else {
                    if (o = o.message, o.credit2 = t.util.getStore(o.credit2, "random"), 1 != o.is_use_diy) t.WxParse.wxParse("copyright", "html", o.config.mall.copyright, e, 0); else {
                        var i = o.diy.data.items;
                        for (var n in i) "richtext" == i[n].id && t.WxParse.wxParse("richtext." + n, "html", i[n].params.content, e, 5);
                        wx.setNavigationBarTitle({
                            title: o.diy.data.page.title
                        }), wx.setNavigationBarColor({
                            frontColor: o.diy.data.page.navigationtextcolor,
                            backgroundColor: o.diy.data.page.navigationbackground
                        });
                    }
                    e.setData(o);
                }
            }
        }), wx.getSetting({
            success: function(t) {
                t.authSetting["scope.userInfo"] && e.setData({
                    Logged: !0
                });
            }
        });
    },
    onToAuth: function() {
        t.util.navigateToAuth();
    },
    onPullDownRefresh: function() {
        this.onLoad(), wx.stopPullDownRefresh();
    },
    onJsEvent: function(e) {
        t.util.jsEvent(e);
    }
});