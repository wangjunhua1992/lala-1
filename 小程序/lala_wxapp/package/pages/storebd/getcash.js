var t = getApp();

Page({
    data: {
        fee: 0,
        Lang: t.Lang,
        wuiLoading: {
            show: !1
        }
    },
    onLoad: function() {
        var a = this;
        t.util.request({
            url: "storebd/getcash/application",
            data: {},
            success: function(e) {
                t.util.loaded();
                var s = e.data.message;
                if (s.errno) return t.util.toast(s.message), !1;
                s = s.message, a.setData(s);
            }
        });
    },
    getcashInput: function(t) {
        var a = t.detail.value;
        a || (a = 0), this.setData({
            fee: a
        });
    },
    onSubmit: function() {
        var a = this;
        if (a.data.fee <= 0) return t.util.toast("请填写提现金额"), !1;
        t.util.request({
            url: "storebd/getcash/application",
            data: {
                fee: a.data.fee
            },
            method: "POST",
            success: function(a) {
                var e = a.data.message;
                if (e.errno) return t.util.toast(e.message), !1;
                t.util.toast("申请提现成功", "/package/pages/storebd/index", 1500);
            }
        });
    }
});