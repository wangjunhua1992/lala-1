var t = getApp();

Page({
    data: {
        isAgree: !1,
        isSelect: !1,
        showCoupon: !1,
        total_fee: 0,
        no_discount_part: 0,
        sum: 0,
        interim: 0,
        final_fee: 0,
        couponNum: 0,
        legal: 0,
        showCouponPrice: !1,
        showNum: !1,
        coupons: [],
        activityCoupon: [],
        submit: !1,
        tables: [],
        table_num: 0,
        note: "",
        paybill_extra: "",
        Lang: t.Lang,
        wuiLoading: {
            show: !0,
            img: t.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(a) {
        var e = this;
        a && a.sid && (e.data.sid = a.sid), t.util.request({
            url: "wmall/store/paybill/payment",
            data: {
                sid: e.data.sid
            },
            success: function(o) {
                t.util.loaded();
                var s = o.data.message.message;
                e.data.tables = e.data.tables.concat(s.tables), e.data.paybill_extra = s.paybill_extra, 
                e.setData({
                    sid: a.sid,
                    tables: e.data.tables,
                    paybill_extra: e.data.paybill_extra
                });
            }
        });
    },
    bindChange: function(t) {
        this.setData({
            table_num: t.detail.value
        });
    },
    onNote: function(t) {
        this.setData({
            note: t.detail.value
        });
    },
    onUnload: function() {},
    bindAgreeChange: function(t) {
        this.setData({
            isAgree: !this.data.isAgree
        });
    },
    onTotalfee: function(t) {
        var a = t.detail.value, e = this;
        a || (a = 0), e.setData({
            total_fee: a,
            isSelect: !1,
            activityCoupon: []
        }), e.calculate(), e.requestCoupons();
    },
    noDiscountPart: function(t) {
        var a = t.detail.value, e = this;
        a || (a = 0), e.setData({
            no_discount_part: a,
            isSelect: !1,
            activityCoupon: []
        }), e.calculate(), e.requestCoupons();
    },
    showCoupon: function() {
        if (!this.data.showNum) return !1;
        this.setData({
            showCoupon: !this.data.showCoupon
        });
    },
    selectCoupon: function(t) {
        var a = this, e = a.data.coupons, o = t.currentTarget.dataset.id;
        e[o].selected = !e[o].selected, a.setData({
            activityCoupon: e[o],
            showCoupon: !1,
            coupons: e,
            isSelect: !0
        }), a.calculate();
    },
    noUse: function() {
        var t = this;
        t.setData({
            activityCoupon: [],
            showCoupon: !1,
            isSelect: !0
        }), t.calculate(), t.requestCoupons();
    },
    calculate: function() {
        var t = this, a = parseFloat(t.data.total_fee), e = parseFloat(t.data.no_discount_part);
        if (t.setData({
            submit: !1,
            legal: 0,
            showCouponPrice: !1,
            interim: a,
            final_fee: a
        }), t.data.isSelect || t.setData({
            couponNum: 0
        }), isNaN(e) && (e = 0), e > a) return wx.showToast({
            title: "超出消费总额",
            icon: "loading"
        }), !1;
        var o = (a - e).toFixed(2);
        if (t.setData({
            sum: o
        }), a) {
            var s = t.data.activityCoupon, i = parseFloat(s.condition);
            if (s.id > 0 && i <= o) {
                var n = (a - parseFloat(s.discount)).toFixed(2);
                t.setData({
                    showNum: !0,
                    showCouponPrice: !0,
                    interim: n,
                    final_fee: n
                });
            }
            t.setData({
                submit: !0
            });
        }
        t.setData({
            legal: 1
        });
    },
    requestCoupons: function() {
        var a = this, e = {
            sid: a.data.sid,
            sum: a.data.sum
        };
        t.util.request({
            url: "wmall/store/paybill/coupon",
            showLoading: !1,
            data: e,
            success: function(t) {
                var e = t.data.message.message, o = t.data.message.num;
                e && o > 0 && a.setData({
                    coupons: e,
                    couponNum: o,
                    showNum: !0
                });
            }
        });
    },
    onSubmit: function() {
        var a = this;
        if (!a.data.submit) return wx.showToast({
            title: "请输入金额",
            icon: "loading"
        }), !1;
        var e = {
            sid: a.data.sid || 3,
            total_fee: a.data.total_fee,
            no_discount_part: a.data.no_discount_part,
            couponId: a.data.activityCoupon.id,
            note: a.data.note,
            table_sn: a.data.tables[a.data.table_num].title
        };
        t.util.request({
            url: "wmall/store/paybill/index",
            showLoading: !1,
            data: e,
            success: function(a) {
                var e = a.data.message;
                if (e.errno) return t.util.toast(e.message, ""), !1;
                var o = e.message;
                wx.showToast({
                    title: "下单成功",
                    success: function() {
                        wx.navigateTo({
                            url: "../public/pay?order_id=" + o + "&order_type=paybill"
                        });
                    }
                });
            }
        });
    },
    onReady: function() {}
});