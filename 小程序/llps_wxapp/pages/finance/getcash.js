var e = getApp();

Page({
    data: {
        Lang: e.Lang,
        submit: !0,
        channel: "weixin"
    },
    onLoad: function(t) {
        var a = this;
        e.util.request({
            url: "delivery/finance/getcash/index",
            success: function(t) {
                var s = t.data.message;
                if (s.errno) return e.util.toast(s.message), !1;
                var n = s.message.deliveryer, i = {
                    get_cash_fee_limit: 0,
                    get_cash_fee_rate: 0,
                    get_cash_fee_min: 0,
                    get_cash_fee_max: 0
                };
                n.fee_getcash = Object.assign(i, n.fee_getcash), a.setData({
                    deliveryer: n,
                    config: s.message.config,
                    submit: !1
                });
            }
        });
    },
    onGetCash: function(t) {
        var a = this, s = parseFloat(t.detail.value.get_fee), n = a.data.deliveryer;
        return !a.data.submit && (s <= 0 ? (e.util.toast("提现金额有误"), !1) : s > n.credit2 ? (e.util.toast("提现金额大于可用余额"), 
        !1) : s < n.fee_getcash.get_cash_fee_limit ? (e.util.toast("提现金额不能小于" + n.fee_getcash.get_cash_fee_limit + a.data.Lang.dollarSignCn), 
        !1) : (a.setData({
            submit: !0
        }), void e.util.request({
            url: "delivery/finance/getcash/submit",
            data: {
                formId: t.detail.formId,
                get_fee: s,
                channel: a.data.channel
            },
            success: function(t) {
                var s = t.data.message;
                if (s.errno ? (s.message.message ? e.util.toast(s.message.message) : e.util.toast(s.message), 
                a.setData({
                    submit: !1
                })) : e.util.toast(s.message.message), !s.errno || s.errno && s.message.id) {
                    var n = s.message.id;
                    wx.redirectTo({
                        url: "./getcashDetail?id=" + n
                    });
                }
            }
        })));
    },
    onChangeType: function(e) {
        var t = e.currentTarget.dataset.channel;
        this.setData({
            channel: t
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});