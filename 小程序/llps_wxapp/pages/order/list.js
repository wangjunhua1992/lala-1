var e = getApp();

Page({
    data: {
        Lang: e.Lang,
        status: 3,
        order: {
            page: 1,
            psize: 15,
            list: [],
            empty: 0,
            loaded: 0,
            loading: !1
        },
        yinsihao: {
            status: !1,
            secret_mobile: "",
            extension: ""
        }
    },
    onLoad: function(e) {
        console.log("onLoadonLoadonLoadonLoadonLoadonLoadonLoadonLoadonLoad");
    },
    onJsEvent: function(a) {
        e.util.jsEvent(a);
    },
    onReachBottom: function() {
        var a = this;
        a.data.order.loaded || a.data.order.loading || (a.setData({
            "order.loading": !0
        }), e.util.request({
            url: "delivery/order/takeout",
            data: {
                psize: a.data.order.psize,
                page: a.data.order.page,
                status: a.data.status
            },
            success: function(t) {
                var o = t.data.message;
                if (o.errno) e.util.toast(o.message, "", 1e3); else {
                    var r = a.data.order.list.concat(o.message.orders);
                    if (a.data.order.list = r, o.message.orders.length < a.data.order.psize && (a.data.order.loaded = 1, 
                    r.length || (a.data.order.empty = 1)), a.data.order.page++, a.data.order.loading = !1, 
                    a.setData({
                        can_collect_order: o.message.can_collect_order,
                        deliveryer: o.message.deliveryer,
                        order: a.data.order
                    }), a.data.deliveryer.openid_wxapp_deliveryer) {
                        var n = e.util.getStorageSync("deliveryerInfo");
                        n.openid_wxapp_deliveryer || (n.openid_wxapp_deliveryer = a.data.deliveryer.openid_wxapp_deliveryer, 
                        e.util.setStorageSync("deliveryerInfo", n));
                    }
                }
            }
        }));
    },
    onChangeOrderStatus: function(a) {
        var t = this, o = a.currentTarget.dataset;
        wx.showModal({
            title: "系统提示",
            content: o.confirm,
            success: function(a) {
                a.confirm ? e.util.request({
                    url: "delivery/order/takeout/status",
                    data: o,
                    success: function(a) {
                        var o = a.data.message;
                        e.util.toast(o.message, "", 1e3), o.errno || t.onPullDownRefresh();
                    }
                }) : a.cancel;
            }
        });
    },
    onCallCustomer: function(a) {
        var t = this, o = a.currentTarget.dataset.order;
        o && o.data && 1 == o.data.yinsihao_status ? e.util.request({
            url: "yinsihao/yinsihao",
            data: {
                order_id: o.id,
                type: "member",
                ordersn: o.ordersn
            },
            success: function(a) {
                var o = a.data.message;
                if (o.errno) return e.util.toast(o.message, "", 1e3), !1;
                o = o.message, t.data.yinsihao.secret_mobile = o.data.secret_mobile, t.data.yinsihao.extension = o.data.extension, 
                t.data.yinsihao.status = !0, t.setData({
                    yinsihao: t.data.yinsihao
                });
            }
        }) : wx.makePhoneCall({
            phoneNumber: o.mobile
        });
    },
    onCallSecretMobile: function() {
        var e = this;
        e.onToggleYinsihaoStatus(), wx.makePhoneCall({
            phoneNumber: e.data.yinsihao.secret_mobile
        });
    },
    onToggleYinsihaoStatus: function() {
        var e = this;
        e.setData({
            "yinsihao.status": !e.data.yinsihao.status
        });
    },
    onChange: function(e) {
        var a = this, t = e.currentTarget.dataset.index;
        a.data.order = {
            page: 1,
            psize: 15,
            list: [],
            empty: 0,
            loaded: 0
        }, a.setData({
            status: t
        }), a.onReachBottom();
    },
    onDetail: function(a) {
        var t = a.currentTarget.dataset, o = t.id;
        if (3 == t.status) return e.util.toast("抢单后才能查看订单详情", "", 1e3), !1;
        wx.navigateTo({
            url: "./detail?id=" + o
        });
    },
    onReady: function() {},
    onShow: function() {
        var a = this;
        a.data.order = {
            page: 1,
            psize: 15,
            list: [],
            empty: 0,
            loaded: 0
        }, e.util.uploadOpenid(), a.onReachBottom(), e.util.followLocation(!1, !0);
    },
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {
        var e = this;
        e.data.order = {
            page: 1,
            psize: 15,
            list: [],
            empty: 0,
            loaded: 0
        }, e.onReachBottom(), wx.stopPullDownRefresh();
    },
    onShareAppMessage: function() {}
});