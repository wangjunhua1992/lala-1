var t = getApp();

Page({
    data: {},
    onLoad: function(t) {
        this.setData({
            sid: t.sid
        });
    },
    onSubmit: function(e) {
        var i = this, a = e.detail.value.title, n = e.detail.value.recognition;
        if (!a) return !1;
        if (!n) return !1;
        var r = {
            title: a,
            recognition: n
        };
        t.util.request({
            url: "wmall/member/invoice",
            data: r,
            success: function(t) {
                var e = t.data.message.message;
                wx.redirectTo({
                    url: "note?invoiceId=" + e + "&sid=" + i.data.sid
                });
            }
        });
    }
});