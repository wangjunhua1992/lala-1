var a = getApp();

Page({
    data: {
        islegal: !1,
        days: 0,
        calculate: {
            final_fee: 0
        },
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(t) {
        var e = this;
        e.data.options = t, a.util.request({
            url: "tongcheng/publish/stick",
            data: {
                information_id: e.data.options.information_id
            },
            success: function(t) {
                a.util.loaded();
                var s = t.data.message;
                if (s.errno) return a.util.toast(s.message), !1;
                s = s.message, e.data.days = s.calculate.default_days, e.setData({
                    category: s.category,
                    calculate: s.calculate,
                    days: e.data.days,
                    islegal: !0
                });
            }
        });
    },
    onSelectStick: function(t) {
        if (this.data.calculate && 1 != this.data.calculate.stick_is_available) return a.util.toast("置顶位已售完,暂时不可购买", "", 1e3), 
        !1;
        var e = t.currentTarget.dataset.day;
        this.setData({
            days: e
        });
    },
    onSubmit: function(t) {
        var e = this;
        e.data.days ? a.util.request({
            url: "tongcheng/publish/stick",
            method: "POST",
            data: {
                days: e.data.days,
                information_id: e.data.options.information_id
            },
            success: function(t) {
                e.setData({
                    islegal: !1
                });
                var s = t.data.message;
                if (s.errno) return e.setData({
                    islegal: !0
                }), a.util.toast(s.message), !1;
                a.util.toast("下单成功", "/pages/public/pay?order_id=" + s.message + "&order_type=tongcheng", 1e3);
            }
        }) : a.util.toast("请选择置顶天数", "", 1e3);
    },
    onJsEvent: function(t) {
        a.util.jsEvent(t);
    }
});