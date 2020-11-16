var a = getApp(), e = require("../../static/js/utils/underscore.js");

Page({
    data: {
        showRedpacket: !1,
        showSvipRedpacket: !1,
        showCoupon: !1,
        buy_mealredpacket: 0,
        extra: {},
        addressUrl: "pages/member/address?",
        person: [ "1人", "2人", "3人", "4人", "5人", "6人", "7人", "8人", "9人", "10人" ],
        person_num: 1,
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        },
        invoiceTitle: "不需要发票"
    },
    onLoad: function(t) {
        var s = this;
        s.data.sid = t.sid, s.data.is_buysvip = t.is_buysvip, s.data.is_pindan = t.is_pindan, 
        s.data.pindan_id = t.pindan_id, s.data.addressUrl = s.onCreateAddressUrl(t);
        var d = a.util.getStorageSync("order.extra") || {}, r = {
            sid: t.sid,
            extra: d,
            is_buysvip: s.data.is_buysvip,
            is_pindan: s.data.is_pindan,
            pindan_id: s.data.pindan_id
        };
        a.util.request({
            url: "wmall/order/create/index",
            data: r,
            success: function(r) {
                if (a.util.loaded(), (r = r.data.message).errno) return a.util.toast(r.message, "", 1e3), 
                !1;
                r = r.message, s.data.extra = e.extend(d, {
                    predict_index: r.order.deliveryTimes.predict_index,
                    predict_time_cn: r.order.deliveryTimes.predict_time_cn,
                    predict_day_cn: r.order.deliveryTimes.predict_day_cn,
                    address_id: r.address.id,
                    order_type: r.order.order_type,
                    person_num: r.order.person_num
                }), 1 == d.buy_mealredpacket && (s.data.buy_mealredpacket = 1), 1 == r.order.yinsihao.status && 0 != d.yinsihao_status && (d.yinsihao_status = !0, 
                s.data.extra.yinsihao_status = !0), s.setData({
                    store: r.store,
                    cart: r.cart,
                    address: r.address,
                    addresses: r.addresses,
                    coupons: r.coupons,
                    redPackets: r.redPackets,
                    order: e.extend(r.order, t),
                    islegal: r.islegal,
                    mobile: r.mobile,
                    buy_mealredpacket: s.data.buy_mealredpacket,
                    config_takeout: r.config_takeout,
                    svip_redpacket: r.svip_redpacket,
                    is_pindan: s.data.is_pindan,
                    pindan_id: s.data.pindan_id,
                    huangou: r.huangou,
                    addressUrl: s.data.addressUrl,
                    buy_zhunshibao: d.buy_zhunshibao,
                    yinsihao_status: d.yinsihao_status,
                    person_num: d.person_num > 0 ? d.person_num : 1,
                    invoiceTitle: d.invoiceTitle
                }), a.util.setStorageSync("order.extra", s.data.extra), s.onCheckSendPrice(r.message, r.address);
            }
        });
    },
    onChangePersonNum: function(e) {
        var t = this, s = parseInt(e.detail.value) + 1;
        t.setData({
            person_num: s
        }), this.data.extra.person_num = s, a.util.setStorageSync("order.extra", t.data.extra);
    },
    onCreateAddressUrl: function(a) {
        var e = this;
        if (!a) return !1;
        var t = e.data.addressUrl;
        for (var s in a) t += s + "=" + a[s] + "&";
        return t += "channel=takeout";
    },
    onJsEvent: function(e) {
        a.util.jsEvent(e);
    },
    onOrderSubmit: function(e) {
        var t = this;
        if (2 == t.data.extra.order_type) {
            var s = e.detail.value.mobile;
            if (!s) return a.util.toast("请输入提货手机号", ""), !1;
            t.data.extra.mobile = s;
        }
        if (!(1 != t.data.extra.order_type && t.data.extra.order_type || t.data.extra.address_id || 2 == t.data.store.delivery_type)) return a.util.toast("请选择收货地址", ""), 
        !1;
        if (!t.data.islegal) return !1;
        t.data.islegal = !1, t.setData({
            islegal: t.data.islegal
        });
        var d = a.util.getStorageSync("order.extra") || {};
        2 == t.data.store.delivery_type && (d.order_type = 2), d.formId = e.detail.formId, 
        d.mobile = s, 1 != d.order_type && d.order_type || 1 != t.data.config_takeout.audit_accept_address ? t.onSubmitPost(d) : wx.showModal({
            title: "请确定您的收货信息",
            content: "收货地址：" + t.data.address.address + "-" + t.data.address.number + "， 手机号：" + t.data.address.mobile + "， 收货人：" + t.data.address.realname,
            success: function(a) {
                if (a.confirm) t.onSubmitPost(d); else if (a.cancel) return t.setData({
                    islegal: !0
                }), !1;
            }
        });
    },
    onSubmitPost: function(e) {
        var t = this, s = {
            sid: t.data.sid,
            extra: e,
            is_pindan: t.data.is_pindan,
            pindan_id: t.data.pindan_id
        };
        a.util.request({
            url: "wmall/order/create/submit",
            data: s,
            success: function(e) {
                if ((e = e.data.message).errno) return 1e3 == e.errno ? (a.util.toast(e.message, "../store/goods?sid=" + s.sid), 
                !1) : (a.util.toast(e.message, ""), !1);
                var t = e.message;
                return wx.showToast({
                    title: "下单成功",
                    success: function() {
                        wx.removeStorageSync("order"), wx.redirectTo({
                            url: "../public/pay?order_id=" + t + "&order_type=takeout"
                        });
                    }
                }), !1;
            }
        });
    },
    onCalculate: function() {
        var e = this, t = {
            sid: e.data.sid,
            extra: e.data.extra,
            is_buysvip: e.data.is_buysvip,
            is_pindan: e.data.is_pindan,
            pindan_id: e.data.pindan_id,
            goods_id: e.data.goods_id,
            sign: e.data.sign
        };
        a.util.request({
            url: "wmall/order/create/index",
            data: t,
            success: function(t) {
                var s = {
                    activityed: (t = (t = t.data.message).message).activityed,
                    address: t.address,
                    order: t.order,
                    islegal: t.islegal,
                    redPackets: t.redPackets,
                    cart: t.cart,
                    svip_redpacket: t.svip_redpacket,
                    store: t.store
                };
                t.huangou && (t.huangou.message.errno && a.util.toast(t.huangou.message.message, "", 2e3), 
                t.huangou.cart_message && a.util.toast(t.huangou.cart_message, "", 3e3)), e.data.goods_id = 0, 
                e.data.sign = "", e.setData(s), a.util.setStorageSync("order.extra", e.data.extra), 
                e.onCheckSendPrice(t.message, t.address);
            }
        });
    },
    onCheckSendPrice: function(e, t) {
        var s = this;
        if ("noReachSendPrice" == e.errno) {
            if (t) {
                var d = {
                    address: t.address,
                    x: t.location_x,
                    y: t.location_y
                };
                a.util.setStorageSync("location", d, 600);
            }
            return a.util.toast(e.message, "../store/goods?sid=" + s.data.sid, 3e3), !1;
        }
        return !0;
    },
    onSelectDeliveryday: function(a) {
        var e = this;
        e.data.order.deliveryTimes.predict_day = a.currentTarget.dataset.id, e.data.order.deliveryTimes.predict_day_cn = a.currentTarget.dataset.id, 
        e.data.extra.predict_day_cn = a.currentTarget.dataset.id, e.setData({
            order: e.data.order
        });
    },
    onSelectDeliverytimes: function(a) {
        var e = this;
        e.data.order.deliveryTimes.predict_index = a.currentTarget.dataset.id, e.data.order.deliveryTimes.predict_time_cn = a.currentTarget.dataset.time, 
        e.data.extra.predict_index = a.currentTarget.dataset.id, e.data.extra.predict_time_cn = a.currentTarget.dataset.time, 
        e.setData({
            order: e.data.order,
            showTimes: !1
        }), e.onCalculate();
    },
    onSelectCoupon: function(a) {
        var e = this;
        e.data.extra.coupon_id = a.currentTarget.dataset.id, e.setData({
            extra: e.data.extra,
            showCoupon: !1
        }), e.onCalculate();
    },
    onSelectRedpacket: function(a) {
        var e = this;
        e.data.extra.redpacket_id = a.currentTarget.dataset.id, e.setData({
            extra: e.data.extra,
            showRedpacket: !1
        }), e.onCalculate();
    },
    onChangeOrderType: function(a) {
        var e = this;
        e.data.extra.order_type = a.currentTarget.dataset.type, e.setData({
            extra: e.data.extra
        }), e.onCalculate();
    },
    onToggleCoupon: function() {
        if (!this.data.coupons.length) return !1;
        this.setData({
            showCoupon: !this.data.showCoupon
        });
    },
    onToggleRedpacket: function() {
        var a = this;
        if (!a.data.redPackets.length && !a.data.svip_redpacket && a.data.svip_redpacket && !a.data.svip_redpacket.id) return !1;
        a.setData({
            showRedpacket: !a.data.showRedpacket
        });
    },
    onToggleTimes: function() {
        this.setData({
            showTimes: !this.data.showTimes
        });
    },
    onBuyMealredpacket: function() {
        var a = this;
        a.setData({
            buy_mealredpacket: !a.data.buy_mealredpacket
        }), a.data.buy_mealredpacket || a.setData({
            "extra.redpacket_id": 0
        }), a.data.extra.buy_mealredpacket = a.data.buy_mealredpacket, a.onCalculate();
    },
    onToggleSvipRedpacket: function() {
        var a = this;
        a.data.showSvipRedpacket = !a.data.showSvipRedpacket, a.setData({
            showSvipRedpacket: a.data.showSvipRedpacket
        });
    },
    onConfirmSvipExchange: function() {
        var e = this, t = e.data.svip_redpacket.id, s = e.data.svip_redpacket.take_status;
        "exchange" == s && (t = e.data.svip_redpacket.store_redpacket.id), a.util.request({
            url: "wmall/order/create/exchange",
            data: {
                id: t,
                sid: e.data.sid,
                oldid: e.data.svip_redpacket.exchange_id,
                discount: e.data.order.total_fee
            },
            success: function(t) {
                if ((t = t.data.message).errno) return a.util.toast(t.message), !1;
                var d = "领取成功";
                "exchange" == s && (d = "兑换成功"), a.util.toast(d), t = t.message, e.setData({
                    redPackets: t.redPackets,
                    svip_redpacket: t.svip_redpacket
                });
                var r = {
                    currentTarget: {
                        dataset: {
                            id: t.redpacket_id
                        }
                    }
                };
                e.onSelectRedpacket(r), "exchange" == s && e.onToggleSvipRedpacket();
            }
        });
    },
    onToggleBuysvip: function() {
        1 == this.data.is_buysvip ? this.data.is_buysvip = 0 : this.data.is_buysvip = 1, 
        this.onCalculate();
    },
    onChangeSwitch: function(a) {
        this.setData({
            buy_zhunshibao: a.detail
        }), this.data.extra.buy_zhunshibao = a.detail, this.onCalculate();
    },
    onChangeYinsihaoSwitch: function(a) {
        this.setData({
            yinsihao_status: a.detail
        }), this.data.extra.yinsihao_status = a.detail, this.onCalculate();
    },
    onHuangouGoods: function(a) {
        this.data.goods_id = a.currentTarget.dataset.goods_id, this.data.sign = a.currentTarget.dataset.sign, 
        this.onCalculate();
    },
    onReady: function() {},
    onShow: function() {
        console.log("监听页面显示");
    },
    onHide: function() {
        console.log("监听页面隐藏");
    },
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {},
    submit: function() {
        wx.navigateTo({
            url: "detail"
        });
    },
    chooseRedpacket: function() {
        this.setData({
            showRedpacket: !this.data.showRedpacket
        }), console.log(this.data.showRedpacket);
    },
    chooseCoupon: function() {
        this.setData({
            showCoupons: !this.data.showCoupons
        }), console.log(this.data.showCoupons);
    }
});