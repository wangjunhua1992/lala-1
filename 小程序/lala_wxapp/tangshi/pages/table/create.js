var a = getApp(), t = require("../../../static/js/utils/underscore.js");

Page({
    data: {
        showAddress: !1,
        showRedpacket: !1,
        showCoupon: !1,
        extra: {},
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        },
        is_pindan: 0,
        pindan_id: 0
    },
    onLoad: function(e) {
        var i = this;
        i.data.sid = e.sid, i.data.table_id = e.table_id, i.data.is_pindan = e.is_pindan, 
        i.data.pindan_id = e.pindan_id;
        var d = a.util.getStorageSync("order.extra") || {};
        i.data.table_id || (i.data.table_id = d.table_id);
        var n = {
            sid: e.sid,
            extra: d,
            table_id: i.data.table_id,
            is_pindan: i.data.is_pindan,
            pindan_id: i.data.pindan_id
        };
        a.util.request({
            url: "wmall/store/table/create",
            data: n,
            success: function(n) {
                if (a.util.loaded(), (n = n.data.message).errno) return -1e3 == n.errno ? (a.util.toast(n.message.message, "redirect:/tangshi/pages/table/pindan?sid=" + i.data.sid + "&table_id=" + i.data.table_id + "&pindan_id=" + n.message.pindan_id, 1e3), 
                !1) : (a.util.toast(n.message, "redirect:/tangshi/pages/table/goods?sid=" + i.data.sid + "&table_id=" + i.data.table_id, 1e3), 
                !1);
                n = n.message, i.data.extra = t.extend(d, {
                    note: n.order.note,
                    invoice_id: n.order.invoiceId,
                    table_id: i.data.table_id
                }), i.setData({
                    store: n.store,
                    cart: n.cart,
                    activityed: n.activityed,
                    coupons: n.coupons,
                    redPackets: n.redPackets,
                    order: t.extend(n.order, e),
                    islegal: n.islegal,
                    extra: i.data.extra,
                    is_pindan: i.data.is_pindan,
                    pindan_id: i.data.pindan_id
                }), a.util.setStorageSync("order.extra", i.data.extra);
            }
        });
    },
    onOrderSubmit: function() {
        var t = this;
        if (!t.data.islegal) return !1;
        var e = parseInt(t.data.person_num);
        if (isNaN(e) || e < 0) a.util.toast("请输入来客人数"); else {
            t.data.islegal = !1, t.setData({
                islegal: t.data.islegal
            });
            var i = a.util.getStorageSync("order.extra") || {}, d = {
                sid: t.data.sid,
                table_id: t.data.table_id,
                extra: i,
                is_pindan: t.data.is_pindan,
                pindan_id: t.data.pindan_id
            };
            a.util.request({
                url: "wmall/store/table/submit",
                data: d,
                success: function(t) {
                    if ((t = t.data.message).errno) return a.util.toast(t.message, ""), !1;
                    var e = t.message;
                    return wx.showToast({
                        title: "下单成功",
                        success: function() {
                            wx.removeStorageSync("order"), wx.navigateTo({
                                url: "../../../pages/public/pay?order_id=" + e + "&order_type=takeout"
                            });
                        }
                    }), !1;
                }
            });
        }
    },
    onCalculate: function() {
        var t = this, e = {
            sid: t.data.sid,
            extra: t.data.extra,
            table_id: t.data.table_id,
            is_pindan: t.data.is_pindan,
            pindan_id: t.data.pindan_id
        };
        a.util.request({
            url: "wmall/store/table/create",
            data: e,
            success: function(e) {
                var i = {
                    activityed: (e = (e = e.data.message).message).activityed,
                    order: e.order
                };
                t.setData(i), a.util.setStorageSync("order.extra", t.data.extra);
            }
        });
    },
    onChangeInput: function(t) {
        var e = this, i = t.target.dataset.key, d = t.detail.value;
        e.data.extra[i] = d, e.setData(e.data.extra), a.util.setStorageSync("order.extra", e.data.extra), 
        "person_num" == i && e.data.order.box_price_tangshi > 0 && e.onCalculate();
    },
    onSelectCoupon: function(a) {
        var t = this;
        t.data.extra.coupon_id = a.currentTarget.dataset.id, t.setData({
            extra: t.data.extra,
            showCoupon: !1
        }), t.onCalculate();
    },
    onSelectRedpacket: function(a) {
        var t = this;
        t.data.extra.redpacket_id = a.currentTarget.dataset.id, t.setData({
            extra: t.data.extra,
            showRedpacket: !1
        }), t.onCalculate();
    },
    onToggleCoupon: function() {
        if (!this.data.coupons.length) return !1;
        this.setData({
            showCoupon: !this.data.showCoupon
        });
    },
    onToggleRedpacket: function() {
        var a = this;
        if (!a.data.redPackets.length) return !1;
        a.setData({
            showRedpacket: !a.data.showRedpacket
        });
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