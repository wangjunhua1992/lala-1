var a = function(a) {
    return a && a.__esModule ? a : {
        default: a
    };
}(require("../../static/js/utils/qrcode.js")), e = getApp();

Page({
    data: {
        showStatus: !1,
        onShowSuperredpacket: !1,
        zhezhaoShow: !1,
        shareData: {
            title: "",
            path: "",
            desc: "",
            imageUrl: "",
            success: function() {},
            fail: function() {}
        },
        showYinsihao: !1,
        Lang: e.Lang,
        wuiLoading: {
            show: !0,
            img: e.util.getStorageSync("theme.loading.img")
        },
        yinsihao: {
            status: !1,
            secret_mobile: "",
            extension: ""
        },
        servicePopupStatus: !1
    },
    onJsEvent: function(a) {
        e.util.jsEvent(a);
    },
    onLoad: function(a) {
        var t = this, o = a.id;
        t.data.options = a, t.setData({
            id: o
        }), e.util.request({
            url: "wmall/order/index/detail",
            data: {
                id: o,
                forceOauth: 1,
                menufooter: 1
            },
            success: function(a) {
                if (e.util.loaded(), a.data.message.errno) return e.util.toast(a.data.message.message), 
                !1;
                var o = a.data.message.message;
                0 == o.activityed.length && (o.activityed = !1);
                var s = o.order_status[o.order.status].text;
                if (t.setData({
                    detail: o,
                    status: s
                }, function() {
                    t.newQrcode(t.data.detail.qrcode);
                }), t.data.detail.share.sharedata) {
                    var i = t.data.detail.share.sharedata;
                    t.data.shareData = {
                        title: i.title,
                        desc: i.desc,
                        imageUrl: i.imgUrl,
                        path: i.link,
                        success: function() {},
                        fail: function() {}
                    };
                }
            }
        });
    },
    onCallStoreOrDeliveryer: function(a) {
        var t = this, o = a.currentTarget.dataset.type;
        t.data.detail.order && "1" == t.data.detail.order.data.yinsihao_status ? e.util.request({
            url: "yinsihao/yinsihao",
            data: {
                order_id: t.data.detail.order.id,
                type: o,
                ordersn: t.data.detail.order.ordersn
            },
            success: function(a) {
                if (a.data.message.errno) return -1e3 == a.data.message.errno ? t.onMakePhoneCall(o) : -2 == a.data.message.errno ? t.onToggleServicePopupStatus() : e.util.toast(a.data.message.message), 
                !1;
                var s = a.data.message.message;
                t.data.yinsihao.secret_mobile = s.data.secret_mobile, t.data.yinsihao.extension = s.data.extension, 
                t.data.yinsihao.status = !0, t.setData({
                    yinsihao: t.data.yinsihao
                });
            }
        }) : t.onMakePhoneCall(o);
    },
    onMakePhoneCall: function(a) {
        var e = this, t = "";
        "store" == a ? t = e.data.detail.store.telephone : "deliveryer" == a && (t = e.data.detail.deliveryer.mobile), 
        wx.makePhoneCall({
            phoneNumber: t
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
    },
    onToggleServicePopupStatus: function() {
        var a = this;
        a.setData({
            servicePopupStatus: !a.data.servicePopupStatus
        });
    },
    onCallService: function() {
        var a = this;
        a.onToggleServicePopupStatus(), wx.makePhoneCall({
            phoneNumber: a.data.detail.config_mall.mobile
        });
    },
    chooseStatus: function() {
        this.setData({
            showStatus: !this.data.showStatus
        });
    },
    onToggleSuperredpacket: function(a) {
        this.setData({
            onShowSuperredpacket: !this.data.onShowSuperredpacket
        }), a.currentTarget.dataset.force && this.onToggleZhezhao();
    },
    onToggleZhezhao: function() {
        this.setData({
            zhezhaoShow: !this.data.zhezhaoShow
        });
    },
    onPullDownRefresh: function() {
        var a = this;
        a.onLoad(a.data.options), wx.stopPullDownRefresh();
    },
    onShareAppMessage: function() {
        return this.data.shareData;
    },
    onFinishMealPay: function() {
        wx.showModal({
            title: "",
            content: "您的支付方式为餐后支付，请到商家收银台付款",
            success: function(a) {}
        });
    },
    newQrcode: function(e) {
        new a.default("canvas", {
            text: e,
            width: 150,
            height: 150,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: a.default.correctLevel.H
        });
    }
});