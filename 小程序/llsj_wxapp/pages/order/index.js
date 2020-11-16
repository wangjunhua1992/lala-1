var a = getApp();

Page(function(a, e, t) {
    return e in a ? Object.defineProperty(a, e, {
        value: t,
        enumerable: !0,
        configurable: !0,
        writable: !0
    }) : a[e] = t, a;
}({
    data: {
        Lang: a.Lang,
        status: 1,
        refresh: 0,
        showLoading: !1,
        orders: {
            page: 1,
            psize: 10,
            loaded: !1,
            data: []
        },
        showGoods: !1,
        yinsihao: {
            status: !1,
            secret_mobile: "",
            extension: ""
        }
    },
    onJsEvent: function(e) {
        a.util.jsEvent(e);
    },
    onLoad: function(e) {
        var t = this;
        e && e.sid && a.util.setSid(e.sid), a.util.uploadOpenid(), t.onReachBottom();
    },
    onCallCustomer: function(e) {
        var t = this, s = e.currentTarget.dataset.order;
        s && s.data && 1 == s.data.yinsihao_status ? a.util.request({
            url: "yinsihao/yinsihao",
            data: {
                order_id: s.id,
                type: "member",
                ordersn: s.ordersn
            },
            success: function(e) {
                var s = e.data.message;
                if (s.errno) return a.util.toast(s.message, "", 1e3), !1;
                s = s.message, t.data.yinsihao.secret_mobile = s.data.secret_mobile, t.data.yinsihao.extension = s.data.extension, 
                t.data.yinsihao.status = !0, t.setData({
                    yinsihao: t.data.yinsihao
                });
            }
        }) : wx.makePhoneCall({
            phoneNumber: s.mobile
        });
    },
    onCallSecretMobile: function() {
        var a = this;
        a.onToggleYinsihaoStatus(), wx.makePhoneCall({
            phoneNumber: a.data.yinsihao.secret_mobile
        });
    },
    onToggleYinsihaoStatus: function() {
        var a = this;
        a.setData({
            "yinsihao.status": !a.data.yinsihao.status
        });
    },
    onChangeOrderStatus: function(e) {
        var t = this, s = e.currentTarget.dataset, o = s.type, r = s.id;
        s.status;
        if ("cancel" == o || "direct_deliveryer" == o) return wx.navigateTo({
            url: "./op?type=" + o + "&id=" + r
        }), !1;
        "handle" == o && 1 == s.is_reserve && (s.confirm = s.reserve_confirm), wx.showModal({
            title: "系统提示",
            content: s.confirm,
            success: function(e) {
                e.confirm ? a.util.request({
                    url: "manage/order/takeout/status",
                    data: s,
                    success: function(e) {
                        var s = e.data.message;
                        a.util.toast(s.message, "", 1e3), s.errno || t.onPullDownRefresh();
                    }
                }) : e.cancel;
            }
        });
    },
    onPushOtherPlateform: function(e) {
        var t = e.currentTarget.dataset.type, s = "manage/order/takeout/push_uupaotui";
        "shansong" == t ? s = "manage/order/takeout/push_shansong" : "dianwoda" == t && (s = "manage/order/takeout/push_dianwoda"), 
        a.util.request({
            url: s,
            data: {
                id: e.currentTarget.dataset.id,
                push: 0
            },
            method: "POST",
            success: function(e) {
                var t = e.data.message;
                if (t.errno) return a.util.toast(t.message), !1;
                wx.showModal({
                    title: "系统提示",
                    content: t.message.tips,
                    success: function(e) {
                        e.confirm ? a.util.request({
                            url: s,
                            data: {
                                id: t.message.id,
                                push: 1
                            },
                            method: "POST",
                            success: function(e) {
                                var t = e.data.message;
                                return a.util.toast(t.message, "", 1e3), !1;
                            }
                        }) : e.cancel;
                    }
                });
            }
        });
    },
    onReady: function() {},
    onShow: function() {
        a.util.getStorageSync("order_refresh") && (a.util.removeStorageSync("order_refresh"), 
        this.setData({
            refresh: 1,
            status: 1
        }), this.onReachBottom());
    },
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {
        this.data.refresh = 1, this.onReachBottom(), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {
        var e = this;
        if (1 == e.data.refresh && (e.data.orders = {
            page: 1,
            psize: 10,
            loaded: !1,
            data: []
        }), e.data.orders.loaded) return !1;
        e.setData({
            showLoading: !0
        }), a.util.request({
            url: "manage/order/takeout/list",
            data: {
                status: e.data.status,
                page: e.data.orders.page,
                psize: e.data.orders.psize
            },
            success: function(t) {
                var s = t.data.message;
                if (s.errno) return a.util.toast(s.message), !1;
                var o = e.data.orders.data.concat(s.message.orders);
                if (e.data.orders.data = o, s.message.orders.length < e.data.orders.psize && (e.data.orders.loaded = !0), 
                e.data.orders.page++, e.data.refresh = 0, e.setData({
                    orders: e.data.orders,
                    showLoading: !1,
                    store: s.message.store
                }), s.message.openid_wxapp_manager) {
                    var r = a.util.getStorageSync("clerkInfo");
                    r.openid_wxapp_manager || (r.openid_wxapp_manager = s.message.openid_wxapp_manager, 
                    a.util.setStorageSync("clerkInfo", r));
                }
            }
        });
    },
    onChangeStatus: function(a) {
        var e = this, t = a.currentTarget.dataset.status;
        e.data.status != t && (e.data.refresh = 1), e.setData({
            status: t
        }), e.onReachBottom();
    },
    onShowGoods: function(a) {
        var e = a.currentTarget.dataset.index;
        this.data.orders.data[e].showGoods = !this.data.orders.data[e].showGoods, this.setData({
            "orders.data": this.data.orders.data
        });
    },
    onShareAppMessage: function() {}
}, "onPullDownRefresh", function() {
    var a = this;
    a.data.refresh = 1, a.onReachBottom(), wx.stopPullDownRefresh();
}));