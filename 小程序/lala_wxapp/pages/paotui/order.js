var a = getApp();

Page({
    data: {
        orders: {
            page: 1,
            psize: 15,
            empty: !1,
            loaded: !1,
            data: []
        },
        showloading: !1,
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        },
        yinsihao: {
            status: !1,
            secret_mobile: "",
            extension: ""
        }
    },
    onLoad: function() {
        this.onReachBottom();
    },
    onJsEvent: function(e) {
        a.util.jsEvent(e);
    },
    onReachBottom: function() {
        var e = this;
        e.data.orders.loaded || (e.setData({
            showloading: !0
        }), a.util.request({
            url: "errander/order/vi_list",
            data: {
                page: e.data.orders.page,
                psize: e.data.orders.psize,
                menufooter: 1
            },
            success: function(t) {
                a.util.loaded();
                var s = t.data.message;
                if (s.errno) return a.util.toast(s.message), !1;
                s = s.message, e.data.orders.data = e.data.orders.data.concat(s.orders), e.data.orders.data.length || (e.data.orders.empty = !0), 
                s.orders && s.orders.length < e.data.orders.psize && (e.data.orders.loaded = !0), 
                e.data.orders.page++, e.setData({
                    orders: e.data.orders,
                    showloading: !1
                });
            }
        }));
    },
    onCallDeliveryer: function(e) {
        var t = e.currentTarget.dataset.id, s = e.currentTarget.dataset.ordersn, o = e.currentTarget.dataset.mobile, r = this;
        a.util.request({
            url: "yinsihao/yinsihao",
            data: {
                order_id: t,
                type: "deliveryer",
                ordersn: s,
                orderType: "errander"
            },
            success: function(e) {
                if (e.data.message.errno) return -1e3 == e.data.message.errno ? wx.makePhoneCall({
                    phoneNumber: o
                }) : a.util.toast(e.data.message.message), !1;
                var t = e.data.message.message;
                r.data.yinsihao.secret_mobile = t.data.secret_mobile, r.data.yinsihao.extension = t.data.extension, 
                r.data.yinsihao.status = !0, r.setData({
                    yinsihao: r.data.yinsihao
                });
            }
        });
    },
    onToggleYinsihaoStatus: function() {
        var a = this;
        a.setData({
            "yinsihao.status": !a.data.yinsihao.status
        });
    },
    onCallSecretMobile: function() {
        var a = this;
        a.onToggleYinsihaoStatus(), wx.makePhoneCall({
            phoneNumber: a.data.yinsihao.secret_mobile
        });
    }
});