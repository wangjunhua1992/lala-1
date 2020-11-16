var t = getApp();

Page({
    data: {
        Lang: t.Lang,
        yinsihao: {
            status: !1,
            secret_mobile: "",
            extension: ""
        }
    },
    onLoad: function(e) {
        var a = this, n = e.id;
        a.data.options = e, t.util.request({
            url: "delivery/order/takeout/detail",
            data: {
                id: n
            },
            showLoading: !1,
            success: function(e) {
                var n = e.data.message;
                if (n.errno) return t.util.toast(n.message, "", 1e3), !1;
                a.setData(n.message);
            }
        });
    },
    onChangeOrderStatus: function(e) {
        var a = this, n = e.currentTarget.dataset, s = n.type;
        if ("delivery_transfer" == s || "direct_transfer" == s || "delivery_cancel" == s) return wx.navigateTo({
            url: "./reason?type=" + s + "&id=" + n.id + "&status=" + n.status
        }), !1;
        wx.showModal({
            title: "系统提示",
            content: n.confirm,
            success: function(e) {
                e.confirm ? t.util.request({
                    url: "delivery/order/takeout/status",
                    data: n,
                    success: function(e) {
                        var n = e.data.message;
                        t.util.toast(n.message, "", 1e3), n.errno || a.onPullDownRefresh();
                    }
                }) : e.cancel;
            }
        });
    },
    onCallCustomer: function() {
        var e = this;
        t.util.request({
            url: "yinsihao/yinsihao",
            data: {
                order_id: e.data.order.id,
                type: "member",
                ordersn: e.data.order.ordersn
            },
            success: function(a) {
                var n = a.data.message;
                if (n.errno) return t.util.toast(n.message, "", 1e3), !1;
                n = n.message, e.data.yinsihao.secret_mobile = n.data.secret_mobile, e.data.yinsihao.extension = n.data.extension, 
                e.data.yinsihao.status = !0, e.setData({
                    yinsihao: e.data.yinsihao
                });
            }
        });
    },
    onCallSecretMobile: function() {
        var t = this;
        t.onToggleYinsihaoStatus(), wx.makePhoneCall({
            phoneNumber: t.data.yinsihao.secret_mobile
        });
    },
    onToggleYinsihaoStatus: function() {
        var t = this;
        t.setData({
            "yinsihao.status": !t.data.yinsihao.status
        });
    },
    onJsEvent: function(e) {
        t.util.jsEvent(e);
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {
        var t = this, e = {
            id: t.data.options.id
        };
        t.onLoad(e), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});