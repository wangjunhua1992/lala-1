var e = getApp();

Page({
    data: {
        Lang: e.Lang
    },
    onLoad: function() {
        var t = this;
        e.util.request({
            url: "manage/shop/index/info",
            success: function(n) {
                var s = n.data.message;
                if (s.errno) return e.util.toast(s.message), !1;
                t.setData(s.message);
            }
        });
    },
    onShowTip: function() {
        wx.showModal({
            content: "您无法修改此项，如需更换门店分类，请联系平台管理员",
            showCancel: !1
        });
    },
    onUploadLogo: function() {
        e.util.image({
            count: 1,
            success: function(t) {
                var n = t.filename;
                e.util.request({
                    url: "manage/shop/index/logo",
                    methods: "POST",
                    data: {
                        logo: n
                    },
                    success: function(t) {
                        var n = t.data.message;
                        if (n.errno) return e.util.toast(n.message), !1;
                        e.util.toast(n.message, "refresh");
                    }
                });
            }
        });
    },
    onJsEvent: function(t) {
        e.util.jsEvent(t);
    },
    onPullDownRefresh: function() {
        this.onLoad(), wx.stopPullDownRefresh();
    }
});