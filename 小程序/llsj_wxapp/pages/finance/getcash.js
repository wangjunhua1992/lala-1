var a = getApp();

Page({
    data: {
        Lang: a.Lang,
        submit: !0,
        channel: "weixin"
    },
    onLoad: function(t) {
        var e = this;
        a.util.request({
            url: "manage/finance/getcash",
            success: function(t) {
                var n = t.data.message;
                n.errno ? a.util.toast(n.message) : e.setData({
                    account: n.message.account,
                    config: n.message.config,
                    submit: !1
                });
            }
        });
    },
    onSubmit: function(t) {
        var e = this, n = parseFloat(t.detail.value.fee), s = e.data.account;
        if (e.data.submit) return !1;
        if (isNaN(n)) return a.util.toast("提现金额有误"), !1;
        if (n > s.amount) return a.util.toast("提现金额不能大于账户可用余额"), !1;
        if (n < s.fee_limit) return a.util.toast("提现金额不能小于" + s.fee_limit + e.data.Lang.dollarSignCn), 
        !1;
        var i = (n * s.fee_rate / 100).toFixed(2);
        i = Math.max(i, s.fee_min), s.fee_max > 0 && (i = Math.min(i, s.fee_max)), i = parseFloat(i);
        var o = (n - i).toFixed(2), r = "提现金额" + n + e.data.Lang.dollarSignCn + ", 手续费" + i + e.data.Lang.dollarSignCn + ",实际到账" + o + e.data.Lang.dollarSignCn + ", 确定提现吗";
        wx.showModal({
            content: r,
            success: function(s) {
                s.confirm ? (e.setData({
                    submit: !0
                }), a.util.request({
                    url: "manage/finance/getcash/getcash",
                    methods: "POST",
                    data: {
                        fee: n,
                        formid: t.detail.formId,
                        channel: e.data.channel
                    },
                    success: function(t) {
                        var n = t.data.message;
                        if (n.errno) return a.util.toast(n.message), void e.setData({
                            submit: !1
                        });
                        a.util.toast(n.message, "/pages/shop/setting", 3e3);
                    }
                })) : s.cancel;
            }
        });
    },
    onChangeType: function(a) {
        var t = a.currentTarget.dataset.channel;
        this.setData({
            channel: t
        });
    }
});