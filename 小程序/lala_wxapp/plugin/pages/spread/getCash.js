var a = getApp();

Page({
    data: {
        channel: "credit",
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function() {
        var e = this;
        a.util.request({
            url: "spread/getCash/application",
            data: {
                menufooter: 1
            },
            success: function(t) {
                a.util.loaded();
                var s = t.data.message;
                e.setData(s.message);
            }
        });
    },
    onInput: function(a) {
        var e = this, t = a.detail.value;
        t <= 0 && (t = 0), e.setData({
            fee: t
        });
    },
    onRadioChange: function(a) {
        var e = this, t = a.detail.value;
        e.setData({
            channel: t
        });
    },
    onSubmit: function() {
        var e = this, t = e.data.fee;
        if (!t) return a.util.toast("请填写提现金额"), !1;
        var s = e.data.channel;
        if (!s) return a.util.toast("请选择提现渠道"), !1;
        var i = {
            status: 1,
            fee: t,
            channel: s
        };
        a.util.request({
            url: "spread/getCash/application",
            data: i,
            success: function(e) {
                0 == e.data.message.errno ? a.util.toast(e.data.message.message, "redirect:/plugin/pages/spread/getCashLog") : (a.util.toast(e.data.message.message), 
                -1e3 == e.data.message.errno && wx.redirectTo({
                    url: "/pages/member/profile"
                }));
            }
        });
    }
});