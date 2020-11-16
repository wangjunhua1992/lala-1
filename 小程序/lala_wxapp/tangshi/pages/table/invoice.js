var i = getApp();

Page({
    data: {
        is_pindan: 0,
        pindan_id: 0
    },
    onLoad: function(i) {
        var a = this;
        i.pindan_id > 0 && (a.data.is_pindan = i.is_pindan, a.data.pindan_id = i.pindan_id), 
        a.setData({
            sid: i.sid
        });
    },
    onSubmit: function(a) {
        var n = this, d = a.detail.value.title, t = a.detail.value.recognition;
        if (!d) return !1;
        if (!t) return !1;
        var e = {
            title: d,
            recognition: t
        };
        i.util.request({
            url: "wmall/member/invoice",
            data: e,
            success: function(i) {
                var a = i.data.message.message;
                wx.redirectTo({
                    url: "note?invoiceId=" + a + "&sid=" + n.data.sid + "&pindan_id=" + n.data.pindan_id + "&is_pindan=" + n.data.is_pindan
                });
            }
        });
    }
});