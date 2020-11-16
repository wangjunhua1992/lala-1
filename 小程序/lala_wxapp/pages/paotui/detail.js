var a = getApp();

Page({
    data: {
        showStatus: !1,
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        },
        yinsihao: {
            status: !1,
            secret_mobile: "",
            extension: ""
        },
        servicePopupStatus: !1
    },
    onLoad: function(e) {
        var t = this;
        a.util.request({
            url: "errander/order/detail",
            data: {
                id: e.id,
                menufooter: 1
            },
            success: function(e) {
                a.util.loaded(), t.data.config_mall = e.data.message.message.config_mall, t.setData({
                    order: e.data.message.message.order,
                    deliveryer: e.data.message.message.deliveryer,
                    logs: e.data.message.message.logs,
                    minid: e.data.message.message.minid,
                    maxid: e.data.message.message.maxid,
                    show_location: e.data.message.message.show_location
                });
            }
        });
    },
    chooseStatus: function() {
        this.setData({
            showStatus: !this.data.showStatus
        });
    },
    onJsEvent: function(e) {
        a.util.jsEvent(e);
    },
    onShowImage: function(e) {
        a.util.showImage(e);
    },
    onCallDeliveryer: function(e) {
        var t = this, s = e.currentTarget.dataset.type;
        t.data.order && "1" == t.data.order.data.yinsihao_status ? a.util.request({
            url: "yinsihao/yinsihao",
            data: {
                order_id: t.data.order.id,
                type: s,
                ordersn: t.data.order.order_sn,
                orderType: "errander"
            },
            success: function(e) {
                if (e.data.message.errno) return -1e3 == e.data.message.errno ? t.onMakePhoneCall() : -2 == e.data.message.errno ? t.onToggleServicePopupStatus() : a.util.toast(e.data.message.message), 
                !1;
                var s = e.data.message.message;
                t.data.yinsihao.secret_mobile = s.data.secret_mobile, t.data.yinsihao.extension = s.data.extension, 
                t.data.yinsihao.status = !0, t.setData({
                    yinsihao: t.data.yinsihao
                });
            }
        }) : t.onMakePhoneCall();
    },
    onMakePhoneCall: function() {
        var a = this.data.deliveryer.mobile;
        wx.makePhoneCall({
            phoneNumber: a
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
            phoneNumber: a.data.config_mall.mobile
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
    onReachBottom: function() {}
});