var a = getApp();

Page({
    data: {
        type: "bank",
        islegal: !1,
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(t) {
        var e = this;
        a.util.request({
            url: "wmall/member/account",
            data: {},
            success: function(t) {
                a.util.loaded();
                var n = t.data.message;
                n.errno ? a.util.toast(n.message) : ((n = n.message).islegal = !0, 1 != n.status.bank && (n.type = "alipay"), 
                e.setData(n));
            }
        });
    },
    onSubmit: function(t) {
        var e = this;
        if (e.data.islegal) {
            var n = e.data.type, i = e.data.bank, s = e.data.alipay;
            if ("bank" == n) {
                if (!i.title) return void a.util.toast("请选择开户银行");
                if (!i.account) return void a.util.toast("银行卡号不能为空");
                if (!i.realname) return void a.util.toast("开户人姓名不能为空");
            } else {
                if (!s.account) return void a.util.toast("支付宝账号不能为空");
                if (!s.realname) return void a.util.toast("支付宝姓名不能为空");
            }
            e.setData({
                islegal: !1
            });
            var r = {
                bank: i,
                alipay: s
            };
            a.util.request({
                method: "POST",
                url: "wmall/member/account",
                data: {
                    params: JSON.stringify(r)
                },
                success: function(t) {
                    var e = t.data.message;
                    e.errno ? a.util.toast(e.message) : a.util.toast("提现账户设置成功", "redirect:/plugin/pages/spread/commission", 1e3);
                }
            });
        }
    },
    onChangeValue: function(a) {
        var t = this, e = a.detail, n = {};
        n[a.currentTarget.dataset.type + "." + a.currentTarget.dataset.key] = e, t.setData(n);
    },
    onSelectBank: function(a) {
        var t = this, e = a.detail.value;
        e != t.data.bankIndex && t.setData({
            bankIndex: e,
            bank: {
                id: t.data.bank_list[e].id,
                title: t.data.bank_list[e].title,
                account: t.data.bank.account,
                realname: t.data.bank.realname
            }
        });
    },
    onToggleType: function(a) {
        var t = this, e = a.currentTarget.dataset.type;
        e != t.data.type && t.setData({
            type: e
        });
    },
    onJsEvent: function(t) {
        a.util.jsEvent(t);
    }
});