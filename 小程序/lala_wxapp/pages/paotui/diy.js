var a = getApp(), e = require("../../static/js/utils/underscore.js");

Page({
    data: {
        modal: {
            modalPrefee: !1,
            modalRedpacket: !1,
            modalDeliveryTime: !1,
            modalGoodsWeight: !1,
            modalFee: !1,
            modalTip: !1
        },
        buyaddressPoint: !0,
        diy: {},
        extra: {},
        extraTemp: {},
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onJsEvent: function(e) {
        a.util.jsEvent(e);
    },
    onLoad: function(t) {
        var r = this;
        r.data.options = t, r.data.options.id = t.id || 10;
        var d = a.util.getStorageSync("errander.extra") || {};
        d.notes && (d.note = d.notes[t.id]), d.hasOwnProperty("yinsihao_status") || (d.yinsihao_status = !0), 
        a.util.request({
            url: "errander/diy/index",
            data: {
                id: t.id || 10,
                extra: d,
                forceLocation: 1
            },
            success: function(t) {
                if (a.util.loaded(), 0 != (t = t.data.message).errno) return a.util.toast(t.message, ""), 
                !1;
                t = t.message, r.data.extra = e.extend(d, {
                    delivery_day: t.order.delivery_day,
                    delivery_time: t.order.delivery_time,
                    delivery_nowtime: t.order.delivery_nowtime,
                    buyaddress_id: t.buyaddress_id,
                    acceptaddress_id: t.acceptaddress_id,
                    delivery_tips: t.order.delivery_tips,
                    extra_fee: t.order.extra_fee,
                    goods_weight: t.order.goods_weight,
                    note: t.order.note
                }), r.data.options.note && "undefined" != r.data.options.note && (r.data.extra.note = r.data.options.note), 
                "undefined" == r.data.extra.note && (r.data.extra.note = ""), r.data.extraTemp.delivery_day = r.data.extra.delivery_day, 
                r.data.extraTemp.delivery_time = r.data.extra.delivery_time, r.setData({
                    redPackets: t.redPackets,
                    addresses: t.addresses,
                    buyaddress: t.buyaddress,
                    acceptaddress: t.acceptaddress,
                    extra: r.data.extra,
                    extraTemp: r.data.extraTemp,
                    diy: t.diy,
                    order: t.order,
                    basicPart: t.basic
                }), r.data.extra.notes || (r.data.extra.notes = {}), r.data.extra.notes[r.data.options.id] = r.data.extra.note, 
                a.util.setStorageSync("errander.extra", r.data.extra), wx.setNavigationBarTitle({
                    title: r.data.diy.data.page.title
                }), wx.setNavigationBarColor({
                    frontColor: r.data.diy.data.page.navigationtextcolor,
                    backgroundColor: r.data.diy.data.page.navigationbackground
                });
            }
        });
    },
    onChangeYinsihaoSwitch: function(a) {
        this.setData({
            "extra.yinsihao_status": a.detail
        }), this.data.extra.yinsihao_status = a.detail;
    },
    onAddLabel: function(a) {
        var e = this, t = e.data.extra.note || "";
        t = t + " " + a.target.dataset.value + " ", e.data.extra.note = t, e.setData({
            "extra.note": t
        });
    },
    onGoodsInfoInput: function(a) {
        this.setData({
            "extra.note": a.detail.value
        });
    },
    onToggleBuyAddressType: function(a) {
        var e = this, t = a.currentTarget.dataset.type;
        e.data.extra.buyAddressType = t, e.setData({
            "extra.buyAddressType": e.data.extra.buyAddressType
        }), "nearby" == t && (e.data.extra.buyaddress = {}, e.setData({
            "extra.buyaddress": {}
        })), e.onCalculate();
    },
    onSelectBuyAddress: function() {
        var a = this;
        wx.chooseLocation({
            success: function(e) {
                console.log("aaaaaaaaaaaaa"), console.log(e);
                var t = {
                    address: e.name + "-" + e.address,
                    name: e.name,
                    location_x: e.latitude,
                    location_y: e.longitude
                };
                a.data.extra.buyaddress = t, a.data.extra.buyaddress_id = 0, a.onCalculate();
            },
            fail: function(a) {
                console.log("bbbbbbbb"), console.log(a);
            }
        });
    },
    onSelectRedpacket: function(a) {
        var e = this, t = a.currentTarget.dataset;
        e.data.extra.redpacket_id == t.id ? e.data.extra.redpacket_id = 0 : e.data.extra.redpacket_id = t.id, 
        e.setData({
            "modal.modalRedpacket": !1
        }), e.onCalculate();
    },
    onGetPrefee: function(a) {
        this.data.extraTemp.goods_price = parseFloat(a.detail.value);
    },
    onConfirmPrefee: function() {
        var a = this;
        a.setData({
            "extra.goods_price": a.data.extraTemp.goods_price || 0,
            "modal.modalPrefee": !1
        }), a.onCalculate();
    },
    onChangeTips: function(a) {
        var e = this;
        e.data.extra.delivery_tips = a.detail.value, e.setData({
            "extra.delivery_tips": e.data.extra.delivery_tips
        }), e.onCalculate();
    },
    onListenerWeightSlider: function(a) {
        var e = this;
        e.setData({
            "extra.goods_weight": a.detail.value
        }), e.onCalculate();
    },
    onSelectDay: function(a) {
        var e = this;
        e.data.extraTemp.delivery_day = a.currentTarget.dataset.value, e.data.extraTemp.delivery_time = "", 
        e.setData({
            "extraTemp.delivery_day": e.data.extraTemp.delivery_day,
            "extraTemp.delivery_time": e.data.extraTemp.delivery_time
        });
    },
    onSelectTime: function(a) {
        var e = this;
        e.data.extra.delivery_day = e.data.extraTemp.delivery_day, e.data.extra.delivery_time = a.currentTarget.dataset.value, 
        e.setData({
            "extra.delivery_day": e.data.extra.delivery_day,
            "extra.delivery_time": e.data.extra.delivery_time,
            "modal.modalDeliveryTime": !1
        }), e.onCalculate();
    },
    onCalculate: function() {
        var e = this;
        e.setData({
            "modal.modalRedpacket": !1
        });
        var t = {
            is_calculate: 1,
            extra: e.data.extra,
            id: e.data.options.id
        };
        console.log("cccccccccccccc"), console.log(t), a.util.request({
            url: "errander/diy/index",
            data: t,
            success: function(t) {
                if (-1e3 == (t = t.data.message).errno) return delete e.data.extra.extra_fee[e.data.extra.extra_fee.current.pindex][e.data.extra.extra_fee.current.cindex], 
                a.util.toast(t.message, "", 1e3), !1;
                (t = t.message).buyaddress && t.buyaddress.errno && a.util.toast(t.buyaddress.message), 
                e.data.extra.delivery_nowtime = t.order.delivery_nowtime, e.data.extra.acceptaddress_id = t.acceptaddress_id, 
                e.data.extra.buyaddress_id = t.buyaddress_id, e.data.extra.extra_fee = t.order.extra_fee, 
                e.data.extra.redpacket_id = t.order.redpacket_id, e.data.extra.goods_price = t.order.goods_price, 
                e.data.extraTemp.delivery_day = e.data.extra.delivery_day, e.data.extraTemp.delivery_time = e.data.extra.delivery_time, 
                e.data.extra.delivery_tips = t.order.delivery_tips, e.setData({
                    redPackets: t.redPackets,
                    addresses: t.addresses,
                    extra: e.data.extra,
                    extraTemp: e.data.extraTemp,
                    order: t.order,
                    acceptaddress: t.acceptaddress,
                    buyaddress: t.buyaddress
                });
            }
        });
    },
    onOrderSubmit: function() {
        var e = this;
        if (1 == e.data.submitDisabled) return !1;
        if (!e.data.extra.note) return a.util.toast("请填写物品信息", "", 1500), !1;
        if ("buy" != e.data.diy.data.page.scene) {
            if (!e.data.buyaddress) return a.util.toast("请选择取货地址", "", 1500), !1;
            if (!a.util.isMobile(e.data.buyaddress.mobile)) return a.util.toast("取货联系人手机号格式不正确", "", 1500), 
            !1;
        }
        if (!e.data.acceptaddress) return a.util.toast("请选择收货地址", ""), !1;
        if (!a.util.isMobile(e.data.acceptaddress.mobile)) return a.util.toast("收货联系人手机号格式不正确", "", 1500), 
        !1;
        if (1 == e.data.diy.data.fees.weight_status && !e.data.extra.goods_weight) return a.util.toast("请选择物品重量", "", 1500), 
        !1;
        var t = {
            id: e.data.options.id || 10,
            extra: e.data.extra
        };
        e.data.submitDisabled = 1, a.util.request({
            url: "errander/orderdiy/create",
            data: t,
            success: function(t) {
                if ((t = t.data.message).errno) return e.data.submitDisabled = 0, a.util.toast(t.message, ""), 
                !1;
                var r = t.message;
                return wx.showToast({
                    title: "下单成功",
                    success: function() {
                        wx.removeStorageSync("errander"), delete e.data.extra, wx.navigateTo({
                            url: "../public/pay?order_id=" + r + "&order_type=errander"
                        });
                    }
                }), !1;
            }
        });
    },
    onGetPartData: function(a) {
        var e = this, t = a.target.dataset, r = t.type, d = t.name, i = t.value;
        if (e.data.extra.partData || (e.data.extra.partData = {}), "text" == r) {
            i = a.detail.value;
            e.data.extra.partData[d] = i;
        } else if ("oneChoice" == r) {
            if (e.data.extra.partData[d] && e.data.extra.partData[d] == i) return e.data.extra.partData[d] = "", 
            e.setData({
                "extra.partData": e.data.extra.partData
            }), !1;
            e.data.extra.partData[d] = i;
        } else if ("multipleChoices" == r) {
            var s = t.cindex;
            if (e.data.extra.partData[d]) {
                for (var o in e.data.extra.partData[d]) if (e.data.extra.partData[d][o] == i) return delete e.data.extra.partData[d][o], 
                e.setData({
                    "extra.partData": e.data.extra.partData
                }), !1;
            } else e.data.extra.partData[d] = {};
            e.data.extra.partData[d][s] = i;
        }
        e.setData({
            "extra.partData": e.data.extra.partData
        });
    },
    onGetExtraFee: function(a) {
        var e = this, t = a.target.dataset.name, r = a.target.dataset.cindex;
        if (e.data.extra.extra_fee && 0 != e.data.extra.extra_fee.length || (e.data.extra.extra_fee = {}), 
        e.data.extra.extra_fee[t]) {
            for (var d in e.data.extra.extra_fee[t]) if (e.data.extra.extra_fee[t][d] == r) return delete e.data.extra.extra_fee.current, 
            delete e.data.extra.extra_fee[t][d], e.onCalculate(), !1;
        } else e.data.extra.extra_fee[t] = {};
        e.data.extra.extra_fee[t][r] = r, e.data.extra.extra_fee.current = {
            pindex: t,
            cindex: r
        }, e.onCalculate();
    },
    onUploadImage: function(e) {
        var t = this, r = e.currentTarget.dataset.diyitem_index + "_" + e.currentTarget.dataset.pindex;
        a.util.image({
            count: 1,
            success: function(a) {
                var e = t.data.extra.thumbs;
                e || (e = {}), e[r] || (e[r] = []);
                var d = {
                    filename: a.filename,
                    url: a.url
                };
                e[r].push(d), t.data.extra.thumbs = e, t.setData({
                    "extra.thumbs": e
                });
            }
        });
    },
    onDelThumb: function(a) {
        var e = a.currentTarget.dataset.diyitem_index + "_" + a.currentTarget.dataset.pindex, t = a.currentTarget.dataset.index;
        this.data.extra.thumbs[e].splice(t, 1), this.setData({
            "extra.thumbs": this.data.extra.thumbs
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {
        var e = this;
        e.data.extra.notes[e.data.options.id] = e.data.extra.note, a.util.setStorageSync("errander.extra", e.data.extra);
    },
    onUnload: function() {
        var e = this;
        e.data.extra.notes[e.data.options.id] = e.data.extra.note, a.util.setStorageSync("errander.extra", e.data.extra);
    },
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});