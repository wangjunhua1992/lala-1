var a = getApp();

Page({
    data: {
        Lang: a.Lang
    },
    onLoad: function(e) {
        var t = this, s = e.id;
        a.util.request({
            url: "delivery/finance/getcash/detail",
            data: {
                id: s
            },
            success: function(e) {
                var s = e.data.message;
                if (s.errno) return a.util.toast(s.message), !1;
                t.setData(s.message);
            }
        });
    }
});