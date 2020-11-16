var e = getApp();

Page({
    data: {
        Lang: e.Lang,
        codeModalHide: !0,
        status: 1,
        psize: 15,
        showloading: !1,
        order: {
            status: 1,
            page: 1,
            list: [],
            empty: 0,
            loaded: 0
        },
        yinsihao: {
            status: !1,
            secret_mobile: "",
            extension: ""
        }
    },
    onLoad: function(e) {
        this.onReachBottom();
    },
    onJsEvent: function(t) {
        e.util.jsEvent(t);
    },
    onCallCustomer: function(t) {
        var a = t.currentTarget.dataset.id, s = t.currentTarget.dataset.ordersn, o = this;
        e.util.request({
            url: "yinsihao/yinsihao",
            data: {
                order_id: a,
                type: "errander",
                ordersn: s,
                orderType: "errander"
            },
            success: function(t) {
                var a = t.data.message;
                if (a.errno) return e.util.toast(a.message, "", 1e3), !1;
                a = a.message, o.data.yinsihao.secret_mobile = a.data.secret_mobile, o.data.yinsihao.extension = a.data.extension, 
                o.data.yinsihao.status = !0, o.setData({
                    yinsihao: o.data.yinsihao
                });
            }
        });
    },
    onToggleYinsihaoStatus: function() {
        var e = this;
        e.setData({
            "yinsihao.status": !e.data.yinsihao.status
        });
    },
    onCallSecretMobile: function() {
        var e = this;
        e.onToggleYinsihaoStatus(), wx.makePhoneCall({
            phoneNumber: e.data.yinsihao.secret_mobile
        });
    },
    onReachBottom: function(t) {
        var a = this;
        a.setData({
            showloading: !0
        }), e.util.request({
            url: "delivery/order/errander",
            data: {
                psize: a.data.psize,
                page: a.data.order.page,
                status: a.data.status
            },
            success: function(t) {
                var s = t.data.message;
                s.errno && e.util.toast(s.message, "", 1e3);
                var o = a.data.order.list.concat(s.message.orders);
                a.data.order.list = o, o.length || (a.data.order.empty = 1), s.message.orders.length < a.data.psize && (a.data.order.loaded = 1), 
                a.data.order.page++, a.setData({
                    showloading: !1,
                    can_collect_order: s.message.can_collect_order,
                    activityItem: a.data.order,
                    deliveryer: s.message.deliveryer,
                    order: a.data.order,
                    verification_code: s.message.verification_code
                });
            }
        });
    },
    onChangeOrderStatus: function(t) {
        var a = this, s = t.currentTarget.dataset;
        wx.showModal({
            title: "",
            content: s.confirm,
            success: function(t) {
                if (t.confirm) {
                    if ("delivery_success" == s.type && a.data.verification_code) return a.setData({
                        codeModalHide: !1,
                        orderId: s.id
                    }), !1;
                    e.util.request({
                        url: "delivery/order/errander/status",
                        data: s,
                        success: function(t) {
                            var s = t.data.message;
                            e.util.toast(s.message, "", 1e3), s.errno || a.onPullDownRefresh();
                        }
                    });
                } else t.cancel;
            }
        });
    },
    onChange: function(e) {
        var t = this, a = e.currentTarget.dataset.index;
        if (a == t.data.status) return !1;
        t.data.order = {
            status: a,
            page: 1,
            list: [],
            empty: 0,
            loaded: 0
        }, t.setData({
            status: a
        }), t.onReachBottom();
    },
    onDetail: function(t) {
        var a = t.currentTarget.dataset, s = a.id;
        if (1 == a.status) return e.util.toast("抢单后才能查看订单详情", "", 1e3), !1;
        wx.navigateTo({
            url: "./detail?id=" + s
        });
    },
    onCodeConfirm: function() {
        var t = this;
        return t.setData({
            codeModalHide: !0
        }), t.data.code ? 4 != t.data.code.length ? (e.util.toast("输入收货码有误", "", 1e3), !1) : void e.util.request({
            url: "delivery/order/errander/status",
            data: {
                type: "delivery_success",
                id: t.data.orderId,
                code: t.data.code
            },
            success: function(a) {
                var s = a.data.message;
                e.util.toast(s.message, "", 1e3), s.errno || t.onPullDownRefresh();
            }
        }) : (e.util.toast("请输入收货码", "", 1e3), !1);
    },
    onCodecancel: function() {
        this.setData({
            codeModalHide: !0
        });
    },
    onInput: function(e) {
        this.data.code = e.detail.value;
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {
        var e = this;
        e.data.order = {
            status: e.data.status,
            page: 1,
            list: [],
            empty: 0,
            loaded: 0
        }, e.onReachBottom(), wx.stopPullDownRefresh();
    },
    onShareAppMessage: function() {}
});