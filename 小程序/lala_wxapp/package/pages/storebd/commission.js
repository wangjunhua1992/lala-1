var t = getApp();

Page({
    data: {
        Lang: t.Lang,
        wuiLoading: {
            show: !1
        }
    },
    onLoad: function() {
        var a = this;
        t.util.request({
            url: "storebd/index/commission",
            data: {},
            success: function(s) {
                t.util.loaded();
                var e = s.data.message;
                if (e.errno) return t.util.toast(e.message), !1;
                e = e.message, a.setData(e);
            }
        });
    },
    onJsEvent: function(a) {
        t.util.jsEvent(a);
    }
});