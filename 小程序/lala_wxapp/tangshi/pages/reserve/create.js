var t = getApp(), e = require("../../../static/js/utils/underscore.js");

Page({
    data: {
        showAddress: !1,
        showRedpacket: !1,
        showCoupon: !1,
        extra: {},
        Lang: t.Lang,
        wuiLoading: {
            show: !0,
            img: t.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(a) {
        var r = this;
        r.data.sid = a.sid || 3;
        var s = t.util.getStorageSync("reserve.extra") || {};
        r.data.extra = s, s.cid || wx.redirectTo({
            url: "./index?sid=" + r.data.sid
        });
        var o = {
            sid: r.data.sid || 3,
            extra: s
        };
        t.util.request({
            url: "wmall/store/reserve/post",
            data: o,
            success: function(s) {
                t.util.loaded(), (s = s.data.message).errno ? t.util.toast(s.message, "/tangshi/pages/reserve/index?sid=" + r.data.sid, 1e3) : (s = s.message, 
                r.data.extra.reserve_type || (r.data.extra.reserve_type = "table"), r.data.extra.username || (r.data.extra.username = s.order.username), 
                r.data.extra.mobile || (r.data.extra.mobile = s.order.mobile), r.setData({
                    store: s.store,
                    cart: s.cart,
                    category: s.category,
                    tables: s.tables,
                    columns: s.columns,
                    activityed: s.activityed,
                    coupons: s.coupons,
                    order: e.extend(s.order, a),
                    islegal: s.islegal,
                    extra: r.data.extra
                }), t.util.setStorageSync("reserve.extra", r.data.extra));
            }
        });
    },
    onOrderSubmit: function() {
        var e = this, a = t.util.getStorageSync("reserve.extra") || {};
        if (!a.username || !a.mobile) return t.util.toast("请完善预订人信息"), !1;
        if (!a.cid) return t.util.toast("请先选择预定桌台"), !1;
        if (!a.time || !a.day) return t.util.toast("请先选择预定时间"), !1;
        if (!e.data.islegal) return !1;
        e.data.islegal = !1, e.setData({
            islegal: e.data.islegal
        });
        var r = {
            sid: e.data.sid,
            table_id: e.data.table_id,
            extra: a
        };
        t.util.request({
            url: "wmall/store/reserve/submit",
            data: r,
            success: function(e) {
                if ((e = e.data.message).errno) return t.util.toast(e.message, ""), !1;
                var a = e.message;
                return wx.showToast({
                    title: "下单成功",
                    success: function() {
                        wx.removeStorageSync("order"), wx.navigateTo({
                            url: "../../../pages/public/pay?order_id=" + a + "&order_type=takeout"
                        });
                    }
                }), !1;
            }
        });
    },
    onCalculate: function() {
        var e = this, a = {
            sid: e.data.sid,
            extra: e.data.extra
        };
        t.util.request({
            url: "wmall/store/reserve/post",
            data: a,
            success: function(a) {
                var r = {
                    activityed: (a = (a = a.data.message).message).activityed,
                    order: a.order
                };
                e.setData(r), t.util.setStorageSync("reserve.extra", e.data.extra);
            }
        });
    },
    onJsEvent: function(e) {
        t.util.jsEvent(e);
    },
    onChangeInput: function(e) {
        var a = this, r = e.target.dataset.key, s = e.detail.value;
        a.data.extra[r] = s, a.setData(a.data.extra), t.util.setStorageSync("reserve.extra", a.data.extra);
    },
    onSelectCoupon: function(t) {
        var e = this;
        e.data.extra.coupon_id = t.currentTarget.dataset.id, e.setData({
            extra: e.data.extra,
            showCoupon: !1
        }), e.onCalculate();
    },
    onSelectRedpacket: function(t) {
        var e = this;
        e.data.extra.redpacket_id = t.currentTarget.dataset.id, e.setData({
            extra: e.data.extra,
            showRedpacket: !1
        }), e.onCalculate();
    },
    onToggleCoupon: function() {
        if (!this.data.coupons.length) return !1;
        this.setData({
            showCoupon: !this.data.showCoupon
        });
    },
    onToggleRedpacket: function() {
        var t = this;
        if (!t.data.redPackets.length) return !1;
        t.setData({
            showRedpacket: !t.data.showRedpacket
        });
    },
    onChangeOrderType: function(e) {
        var a = this, r = e.currentTarget.dataset.type;
        a.setData({
            "extra.reserve_type": r
        }), t.util.setStorageSync("reserve.extra", a.data.extra), "order" == r ? wx.redirectTo({
            url: "./goods?sid=" + a.data.sid + "&table_cid=" + a.data.extra.cid
        }) : a.onCalculate();
    },
    onSelectTable: function(e) {
        var a = e.detail.value, r = this.data.tables[a];
        1 != r.is_reserved ? (this.data.extra.table_id = r.id, this.setData({
            "extra.table_id": r.id,
            "extra.table_title": r.title
        }), t.util.setStorageSync("reserve.extra", this.data.extra)) : t.util.toast("该桌位已被他人提前预定, 请选择其他桌号", "", 1e3);
    },
    onSelectPersonNum: function(e) {
        var a = parseInt(e.detail.value);
        a += 1, this.setData({
            "extra.person_num": a
        }), t.util.setStorageSync("reserve.extra", this.data.extra);
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
    onShareAppMessage: function() {}
});