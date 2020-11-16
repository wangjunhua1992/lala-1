var e = getApp();

Page({
    data: {
        card: [],
        num: 0,
        Lang: e.Lang,
        wuiLoading: {
            show: !0,
            img: e.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(a) {
        var t = this;
        e.util.request({
            url: "deliveryCard/apply/index",
            success: function(a) {
                e.util.loaded();
                var s = a.data.message.message;
                t.setData({
                    cards: s,
                    card: s[0]
                });
            }
        });
    },
    onChooseCard: function(e) {
        var a = this, t = a.data.cards, s = e.currentTarget.dataset.id, r = t[s];
        a.setData({
            card: r,
            num: s
        });
    },
    onSubmit: function() {
        var a = {
            pay_type: "wechat",
            setmeal_id: this.data.card.id
        };
        console.log(a), e.util.request({
            url: "deliveryCard/apply/pay",
            data: a,
            success: function(a) {
                if (console.log(a), 0 == a.data.message.errno) {
                    var t = a.data.message.message;
                    e.util.pay({
                        pay_type: "wechat",
                        order_type: "deliveryCard",
                        order_id: t,
                        success: function() {
                            wx.showToast({
                                title: "支付成功",
                                success: function() {
                                    wx.redirectTo({
                                        url: "/pages/member/mine"
                                    });
                                }
                            });
                        },
                        fail: function() {
                            e.util.toast("支付失败");
                        }
                    });
                } else e.util.toast(a.data.message.message);
            }
        });
    },
    onSubmit1: function() {
        var a = {
            setmeal_id: this.data.card.id
        };
        console.log(a), e.util.request({
            url: "deliveryCard/apply/pay1",
            data: a,
            success: function(a) {
                if (console.log(a), 0 == a.data.message.errno) {
                    var t = a.data.message.message;
                    wx.showToast({
                        title: "下单成功",
                        success: function() {
                            wx.removeStorageSync("order"), wx.redirectTo({
                                url: "../../../pages/public/pay?order_id=" + t + "&order_type=deliveryCard"
                            });
                        }
                    });
                } else e.util.toast(a.data.message.message);
            }
        });
    }
});