var a = getApp();

Page({
    data: {
        Lang: a.Lang,
        showStatus: !1,
        yinsihao: {
            status: !1,
            secret_mobile: "",
            extension: ""
        }
    },
    onLoad: function(t) {
        var e = this;
        a.util.request({
            url: "manage/order/takeout/detail",
            data: {
                id: t.id || 433
            },
            success: function(t) {
                var s = t.data.message;
                s.errno ? a.util.toast(s.message) : e.setData(s.message);
            }
        });
    },
    onCallCustomer: function() {
        var t = this;
        t.data.order.data && 1 == t.data.order.data.yinsihao_status ? a.util.request({
            url: "yinsihao/yinsihao",
            data: {
                order_id: t.data.order.id,
                type: "member",
                ordersn: t.data.order.ordersn
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
            phoneNumber: t.data.order.mobile
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
    chooseStatus: function() {
        this.setData({
            showStatus: !this.data.showStatus
        });
    },
    onJsEvent: function(t) {
        a.util.jsEvent(t);
    },
    onChangeOrderStatus: function(t) {
        var e = this, s = t.currentTarget.dataset, o = s.type, n = s.id;
        s.status;
        if ("cancel" == o || "direct_deliveryer" == o || "reply" == o) return wx.navigateTo({
            url: "./op?type=" + o + "&id=" + n
        }), !1;
        "handle" == o && 1 == s.is_reserve && (s.confirm = s.reserve_confirm), wx.showModal({
            title: "系统提示",
            content: s.confirm,
            success: function(t) {
                t.confirm ? a.util.request({
                    url: "manage/order/takeout/status",
                    data: s,
                    success: function(t) {
                        var s = t.data.message;
                        a.util.toast(s.message, "", 1e3), s.errno || e.onLoad({
                            id: n
                        });
                    }
                }) : t.cancel;
            }
        });
    },
    onPushOtherPlateform: function(t) {
        var e = t.currentTarget.dataset.type, s = "manage/order/takeout/push_uupaotui";
        "shansong" == e ? s = "manage/order/takeout/push_shansong" : "dianwoda" == e && (s = "manage/order/takeout/push_dianwoda"), 
        a.util.request({
            url: s,
            data: {
                id: t.currentTarget.dataset.id,
                push: 0
            },
            method: "POST",
            success: function(t) {
                var e = t.data.message;
                if (e.errno) return a.util.toast(e.message), !1;
                wx.showModal({
                    title: "系统提示",
                    content: e.message.tips,
                    success: function(t) {
                        t.confirm ? a.util.request({
                            url: s,
                            data: {
                                id: e.message.id,
                                push: 1
                            },
                            method: "POST",
                            success: function(t) {
                                var e = t.data.message;
                                return a.util.toast(e.message, "", 1e3), !1;
                            }
                        }) : t.cancel;
                    }
                });
            }
        });
    },
    onPullDownRefresh: function() {
        this.onLoad({
            id: this.data.order.id
        }), wx.stopPullDownRefresh();
    }
});