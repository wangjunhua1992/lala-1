var e = getApp();

Page({
    data: {
        Lang: e.Lang
    },
    onLoad: function(t) {
        var n = this, a = t.from;
        e.util.request({
            url: "manage/home/index",
            data: {
                nosid: 1
            },
            success: function(t) {
                var s = t.data.message;
                if (s.errno) return e.util.toast(s.message), !1;
                var r = s.message.stores;
                a || 1 != r.length ? r.length > 1 && n.setData(s.message) : (e.util.setStorageSync("__sid", r[0].id), 
                e.util.jump2url("/pages/order/index"));
            }
        });
    },
    onSwitch: function(t) {
        var n = t.currentTarget.dataset.sid;
        e.util.setStorageSync("__sid", n), e.util.setStorageSync("order_refresh", !0), e.util.jump2url("/pages/order/index");
    },
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onJsEvent: function(t) {
        e.util.jsEvent(t);
    }
});