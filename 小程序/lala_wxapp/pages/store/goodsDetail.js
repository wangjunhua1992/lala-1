var t = getApp();

Page({
    data: {
        showCartDetail: !1,
        sail_time: 0,
        buySvipInfo: {
            show: !1,
            goods: {}
        },
        pindan_id: 0,
        Lang: t.Lang,
        wuiLoading: {
            show: !0,
            img: t.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(a) {
        var i = this, o = a.id, e = a.sid;
        if (!o) return !1;
        a && a.pindan_id > 0 && (i.data.pindan_id = a.pindan_id);
        var s = {
            id: o,
            sid: e
        };
        t.util.request({
            url: "wmall/store/goods/detail",
            data: s,
            success: function(a) {
                t.util.loaded();
                var o = a.data.message.message;
                o.goodsDetail.index = 0;
                var e = o.goodsDetail;
                if (1 == e.is_options || 1 == e.is_attrs) {
                    if (e.activeOptions = {
                        option: 0,
                        attrs: [],
                        optionSelected: 0,
                        attrsSelected: []
                    }, 1 == e.is_options && (e.activeOptions.option = e.options[0].id, e.activeOptions.optionSelected = e.options[0].id), 
                    1 == e.is_attrs) for (var s = 0; s < e.attrs.length; s++) e.activeOptions.attrs.push(s + "s0"), 
                    e.activeOptions.attrsSelected[s] = 0;
                    e.activeOptionId = e.activeOptions.option, e.activeOptions.attrs.length > 0 && (e.activeOptionId = e.activeOptionId + "_" + e.activeOptions.attrs.join("v")), 
                    e.activeOption = e.options_data[e.activeOptionId];
                } else e.activeOptionId = 0;
                if (t.WxParse.wxParse("description", "html", o.goodsDetail.description, i, 5), i.setData({
                    cart: o.cart.message.cart,
                    goodsDetail: e,
                    store: o.store,
                    goodsActive: e,
                    pindan_id: i.data.pindan_id
                }), t.util.selectPindan({
                    pindan_id: i.data.pindan_id,
                    cart_pindan_id: o.cart.message.cart.pindan_id,
                    sid: o.store.id,
                    cart_id: o.cart.message.cart.id
                }), i.onCalculate(), i.data.store.data.wxapp) {
                    var n = i.data.store.data.wxapp.extPages.pages_store_goods.navigationBarBackgroundColor;
                    wx.setNavigationBarColor({
                        frontColor: "#ffffff",
                        backgroundColor: n
                    }), i.setData({
                        bgColor: n
                    });
                }
            }
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {
        console.log("goods/监听页面隐藏");
    },
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onSelectOption: function(t) {
        this.setData({
            modelSpecShow: !0
        });
    },
    onHideOption: function() {
        this.setData({
            modelSpecShow: !1
        });
    },
    onToggleOption: function(t) {
        var a = this, i = a.data.goodsActive;
        if ("option" == t.currentTarget.dataset.type) i.activeOptions.option = t.currentTarget.dataset.id, 
        i.activeOptions.optionSelected = t.currentTarget.dataset.id; else {
            var o = t.currentTarget.dataset.index;
            i.activeOptions.attrs[o] = t.currentTarget.dataset.id, i.activeOptions.attrsSelected[o] = t.currentTarget.dataset.originid;
        }
        a.data.goodsActive = i, this.weToggleActiveOption(), a.setData({
            goodsActive: i
        });
    },
    weToggleActiveOption: function(t) {
        var a = this, i = a.data.goodsActive;
        t ? i.activeOptionId = t : (i.activeOptionId = i.activeOptions.option, i.activeOptions.attrs.length > 0 && (i.activeOptionId = i.activeOptionId + "_" + i.activeOptions.attrs.join("v"))), 
        i.activeOption = i.options_data[i.activeOptionId], a.data.goodsActive = i;
    },
    onPlus: function(a) {
        var i = this, o = a.currentTarget.dataset, e = 0;
        if (1 == i.data.cart.is_buysvip && (e = 1), "selectSvip" == a.detail.from) {
            var s = i.data.buySvipInfo.goods, n = s.goods_id, d = s.option_id;
            i.setData({
                "buySvipInfo.show": !1
            }), 3 == a.detail.buysvip_status && (e = 1);
        } else {
            var r = o.from, n = o.goodsid;
            if ("cart" == r) var n = a.currentTarget.dataset.goodsid, d = a.currentTarget.dataset.optionid; else {
                var c = i.data.goodsActive;
                if (n = c.id, d = c.activeOptionId, !c.total) return t.util.toast("库存不足"), !1;
            }
            if (1 == i.data.goodsActive.svip_buy_show && 1 != i.data.cart.is_buysvip && i.data.cart.svip_buy_show >= 1) {
                var p = 0, u = 0;
                return i.data.goodsActive.activeOption ? (p = u = i.data.goodsActive.activeOption.price, 
                i.data.goodsActive.activeOption.svip_price > 0 && (p = i.data.goodsActive.activeOption.svip_price), 
                i.data.goodsActive.activeOption.origin_price > 0 && (u = i.data.goodsActive.activeOption.origin_price)) : (p = i.data.goodsActive.svip_price, 
                u = i.data.goodsActive.origin_price), i.data.buySvipInfo.goods = {
                    svip_price: p,
                    price: u,
                    goods_id: n,
                    option_id: d
                }, i.data.buySvipInfo.show = !0, void i.setData({
                    buySvipInfo: i.data.buySvipInfo
                });
            }
        }
        var v = {
            sid: i.data.store.id,
            goods_id: n,
            option_id: d,
            num: 1,
            sign: "+",
            is_buysvip: e
        };
        t.util.request({
            url: "wmall/store/goods/cart",
            data: v,
            success: function(a) {
                if ((a = a.data.message).errno) return t.util.toast(a.message), !1;
                a.message.msg && t.util.toast(a.message.msg);
                var o = i.data.goodsActive;
                if (v.goods_id == o.id) {
                    if (o.options_data[v.option_id].num) o.options_data[v.option_id].num++, o.totalnum++; else {
                        var e = a.message.cart.data1[n][d].num;
                        o.options_data[v.option_id].num = e, o.totalnum += e;
                    }
                    v.option_id != o.activeOptionId || 1 != o.is_options && 1 != o.is_attrs || (o.activeOption.num = o.options_data[v.option_id].num);
                }
                var s = {
                    cart: a.message.cart,
                    goodsActive: o,
                    goodsDetail: o
                };
                i.setData(s), i.onCalculate();
            }
        });
    },
    onCalculate: function() {
        var t = this;
        t.data.cart.num ? t.data.store.send_condition = (t.data.store.send_price - t.data.cart.price - t.data.cart.box_price).toFixed(2) : t.data.store.send_condition = t.data.store.send_price, 
        t.setData({
            store: t.data.store
        });
    },
    onMinus: function(a) {
        var i = this, o = a.currentTarget.dataset, e = 0;
        1 == i.data.cart.is_buysvip && (e = 1);
        var s = o.from, n = o.goodsid;
        if ("cart" == s) var n = a.currentTarget.dataset.goodsid, d = a.currentTarget.dataset.optionid; else {
            var r = i.data.goodsActive;
            n = r.id, d = r.activeOptionId;
        }
        var c = {
            sid: i.data.store.id,
            goods_id: n,
            option_id: d,
            num: 1,
            sign: "-",
            is_buysvip: e
        };
        t.util.request({
            url: "wmall/store/goods/cart",
            data: c,
            success: function(a) {
                if ((a = a.data.message).errno) return t.util.toast(a.message), !1;
                var o = i.data.goodsActive;
                if (c.goods_id == o.id) {
                    if (a.message.cart.data1[n]) if (a.message.cart.data1[n][d]) o.totalnum--, o.options_data[c.option_id].num--; else {
                        var e = o.options_data[c.option_id].num;
                        o.options_data[c.option_id].num = 0, o.totalnum -= e;
                    } else o.totalnum = 0, o.options_data[c.option_id].num = 0;
                    c.option_id != o.activeOptionId || 1 != o.is_options && 1 != o.is_attrs || (o.activeOption.num = o.options_data[c.option_id].num), 
                    i.data.goodsDetail = o;
                }
                var s = {
                    cart: a.message.cart,
                    goodsActive: o,
                    goodsDetail: o
                };
                a.message.cart.num || (s.showCartDetail = !1), i.setData(s), i.onCalculate();
            }
        });
    },
    onSubmit: function() {
        var a = this;
        if (1 == a.data.cart.is_category_limit) return wx.showModal({
            title: "温馨提示",
            content: a.data.cart.category_limit_cn
        }), !1;
        var i = "pages/order/create?sid=" + a.data.store.id + "&is_buysvip=" + a.data.cart.is_buysvip;
        a.data.pindan_id > 0 && (i = "pages/store/pindan?sid=" + a.data.store.id + "&pindan_id=" + a.data.pindan_id), 
        t.util.jump2url(i, "navigateTo");
    },
    onShowCartDetail: function() {
        if (!this.data.cart.num) return !1;
        this.setData({
            showCartDetail: !this.data.showCartDetail,
            modelSpecShow: !1
        });
    },
    onHideCartDetail: function() {
        this.setData({
            showCartDetail: !1
        });
    },
    showSpec: function() {
        this.setData({
            showSpec: !this.data.showSpec
        });
    },
    hideSpec: function() {
        this.setData({
            showSpec: !1
        });
    },
    onTurncateCart: function() {
        var a = this;
        wx.showModal({
            content: "确定清除购物车吗?",
            success: function(i) {
                i.confirm && t.util.request({
                    url: "wmall/store/goods/truncate",
                    data: {
                        sid: a.data.store.id
                    },
                    success: function() {
                        if (a.data.goodsActive) {
                            a.data.goodsActive.totalnum = 0;
                            for (var t in a.data.goodsActive.options_data) a.data.goodsActive.options_data[t].num = 0;
                        }
                        a.data.goodsItem = a.data.goodsAll, a.setData({
                            goodsActive: a.data.goodsActive,
                            goodsDetail: a.data.goodsActive,
                            cart: {},
                            showCartDetail: !1
                        }), a.onCalculate();
                    }
                });
            }
        });
    },
    onShareAppMessage: function() {
        return this.data.shareData;
    },
    onSailTime: function(t) {
        t.currentTarget.dataset.id;
        this.setData({
            sail_time: 1
        });
    },
    onKnow: function() {
        this.setData({
            sail_time: 0
        });
    },
    onCloseSvip: function() {
        this.setData({
            "buySvipInfo.show": !1
        });
    }
});