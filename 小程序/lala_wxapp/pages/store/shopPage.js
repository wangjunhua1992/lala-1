var t = getApp(), a = require("../../static/js/utils/underscore.js");

Page({
    data: {
        goodsAll: [],
        goodsItems: [],
        modelSpecShow: !1,
        showCartDetail: !1,
        showSpec: !1,
        rest: !1,
        is_sail_now: 0,
        goods_id: "",
        cart: {
            num: 0
        },
        shareData: {
            title: "",
            path: "",
            success: function() {},
            fail: function() {}
        },
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
        var o = this, i = a.shopPageKey, e = a.sid;
        a.pindan_id > 0 && (o.data.pindan_id = a.pindan_id), o.setData({
            sid: e,
            shopPageKey: i
        }), t.util.request({
            url: "wmall/store/goods/shopPage",
            data: {
                sid: e,
                shopPageKey: i
            },
            success: function(a) {
                t.util.loaded();
                var i = {
                    page: 2,
                    empty: 0,
                    data: (a = a.data.message.message).goods
                };
                if (i.data.length < 20 && (i.loaded = 1), o.data.goodsAll = i, o.data.store = a.store, 
                a.store.is_in_business_hours || (o.data.rest = !0), o.setData({
                    store: a.store,
                    goodsAll: o.data.goodsAll,
                    goodsItem: i,
                    goodsLoading: 0,
                    cart: a.cart.message.cart,
                    rest: o.data.rest,
                    pindan_id: o.data.pindan_id
                }), t.util.setStorageSync("store", a.store), o.onCalculate(), o.data.shareData.title = a.store.title, 
                o.data.shareData.path = "/pages/store/goods?sid=" + a.store.id, o.data.store.data.wxapp) {
                    var e = o.data.store.data.wxapp.extPages.pages_store_goods.navigationBarBackgroundColor;
                    wx.setNavigationBarColor({
                        frontColor: "#ffffff",
                        backgroundColor: e
                    }), o.setData({
                        bgColor: e
                    });
                }
            }
        }), wx.getSystemInfo({
            success: function(t) {
                o.setData({
                    screenHeight: t.windowHeight
                });
            }
        });
    },
    onSelectOption: function(t) {
        var a = this, o = t.currentTarget.dataset.index;
        this.weToggleActiveGoods(o), a.setData({
            modelSpecShow: !0
        });
    },
    onHideOption: function() {
        this.setData({
            modelSpecShow: !1
        });
    },
    onToggleOption: function(t) {
        var a = this, o = a.data.goodsActive;
        if ("option" == t.currentTarget.dataset.type) o.activeOptions.option = t.currentTarget.dataset.id, 
        o.activeOptions.optionSelected = t.currentTarget.dataset.id; else {
            var i = t.currentTarget.dataset.index;
            o.activeOptions.attrs[i] = t.currentTarget.dataset.id, o.activeOptions.attrsSelected[i] = t.currentTarget.dataset.originid;
        }
        a.data.goodsActive = o, this.weToggleActiveOption(), a.setData({
            goodsActive: o
        });
    },
    weToggleActiveOption: function(t) {
        var a = this, o = a.data.goodsActive;
        t ? o.activeOptionId = t : (o.activeOptionId = o.activeOptions.option, o.activeOptions.attrs.length > 0 && (o.activeOptionId = o.activeOptionId + "_" + o.activeOptions.attrs.join("v"))), 
        o.activeOption = o.options_data[o.activeOptionId], a.data.goodsActive = o;
    },
    weTranfterId2Index: function(t) {
        var o = this;
        t = t.toString();
        var i = a.findWhere(o.data.goodsAll.data, {
            id: t
        });
        if (!i.id) return -1;
        var e = a.indexOf(o.data.goodsAll.data, i);
        return -1 == e ? -1 : {
            gindex: e
        };
    },
    weToggleActiveGoods: function(t, a, o) {
        var i = this, e = t;
        if ("goods_id" == (a = a || "index")) {
            var s = i.weTranfterId2Index(t);
            if (-1 == s) return !1;
            e = s.gindex;
        }
        var d = i.data.goodsAll.data[e];
        if (d.index = e, 1 == d.is_options || 1 == d.is_attrs) {
            if (!o && (d.activeOptions = {
                option: 0,
                attrs: [],
                optionSelected: 0,
                attrsSelected: []
            }, 1 == d.is_options && (d.activeOptions.option = d.options[0].id, d.activeOptions.optionSelected = d.options[0].id), 
            1 == d.is_attrs)) for (var n = 0; n < d.attrs.length; n++) d.activeOptions.attrs.push(n + "s0"), 
            d.activeOptions.attrsSelected[n] = 0;
            i.data.goodsActive = d, this.weToggleActiveOption(o);
        } else d.activeOptionId = 0;
        return i.data.goodsActive = d, i.setData({
            goodsActive: d
        }), e;
    },
    onPlus: function(a) {
        var o = this, i = a.currentTarget.dataset, e = 0;
        if (1 == o.data.cart.is_buysvip && (e = 1), "selectSvip" == a.detail.from) {
            var s = o.data.buySvipInfo.goods, d = s.goods_id, n = s.option_id, r = s.goodsIndex;
            o.setData({
                "buySvipInfo.show": !1
            }), 3 == a.detail.buysvip_status && (e = 1);
        } else {
            var r = i.index, c = i.from, d = i.goodsid;
            if ("list" == c || "detail" == c) var g = o.weToggleActiveGoods(r); else if ("cart" == c) {
                var d = a.currentTarget.dataset.goodsid, n = a.currentTarget.dataset.optionid;
                !1 !== (g = o.weToggleActiveGoods(d, "goods_id", n)) && (r = g);
            }
            if (!1 !== g) {
                var l = o.data.goodsActive;
                if (d = l.id, n = l.activeOptionId, !l.total) return t.util.toast("库存不足"), !1;
            }
            if (1 == o.data.goodsActive.svip_buy_show && 1 != o.data.cart.is_buysvip && o.data.cart.svip_buy_show >= 1) {
                var p = 0, u = 0;
                return o.data.goodsActive.activeOption ? (p = u = o.data.goodsActive.activeOption.price, 
                o.data.goodsActive.activeOption.svip_price > 0 && (p = o.data.goodsActive.activeOption.svip_price), 
                o.data.goodsActive.activeOption.origin_price > 0 && (u = o.data.goodsActive.activeOption.origin_price)) : (p = o.data.goodsActive.svip_price, 
                u = o.data.goodsActive.origin_price), o.data.buySvipInfo.goods = {
                    svip_price: p,
                    price: u,
                    goods_id: d,
                    option_id: n,
                    goodsIndex: r
                }, o.data.buySvipInfo.show = !0, void o.setData({
                    buySvipInfo: o.data.buySvipInfo
                });
            }
        }
        var v = {
            sid: o.data.store.id,
            goods_id: d,
            option_id: n,
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
                var i = {
                    cart: a.message.cart
                };
                if (!1 !== g) {
                    var e = o.data.goodsActive;
                    if (e.options_data[v.option_id].num) e.options_data[v.option_id].num++, e.totalnum++; else {
                        var s = a.message.cart.data1[d][n].num;
                        e.options_data[v.option_id].num = s, e.totalnum += s;
                    }
                    1 != e.is_options && 1 != e.is_attrs || (e.activeOption.num = e.options_data[v.option_id].num), 
                    "detail" == c && (o.data.goodsDetail.totalnum ? o.data.goodsDetail.totalnum++ : o.data.goodsDetail.totalnum = 1), 
                    o.data.goodsAll.data[r] = e, i = {
                        cart: a.message.cart,
                        goodsAll: o.data.goodsAll,
                        goodsItem: o.data.goodsAll,
                        goodsActive: e,
                        goodsDetail: o.data.goodsDetail
                    };
                }
                o.setData(i), o.onCalculate();
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
        var o = this, i = a.currentTarget.dataset, e = 0;
        1 == o.data.cart.is_buysvip && (e = 1);
        var s = i.index, d = i.from, n = i.goodsid;
        if ("list" == d || "detail" == d) var r = o.weToggleActiveGoods(s); else if ("cart" == d) {
            var n = a.currentTarget.dataset.goodsid, c = a.currentTarget.dataset.optionid;
            !1 !== (r = o.weToggleActiveGoods(n, "goods_id", c)) && (s = r);
        }
        if (!1 !== r) {
            var g = o.data.goodsActive;
            n = g.id, c = g.activeOptionId;
        }
        var l = {
            sid: o.data.store.id,
            goods_id: n,
            option_id: c,
            num: 1,
            sign: "-",
            is_buysvip: e
        };
        t.util.request({
            url: "wmall/store/goods/cart",
            data: l,
            success: function(a) {
                if ((a = a.data.message).errno) return t.util.toast(a.message), !1;
                g = {
                    cart: a.message.cart
                };
                if (!1 !== r) {
                    var i = o.data.goodsActive;
                    if (a.message.cart.data1[n]) if (a.message.cart.data1[n][c]) i.totalnum--, i.options_data[l.option_id].num--; else {
                        var e = i.options_data[l.option_id].num;
                        i.options_data[l.option_id].num = 0, i.totalnum -= e;
                    } else i.totalnum = 0, i.options_data[l.option_id].num = 0;
                    1 != i.is_options && 1 != i.is_attrs || (i.activeOption.num = i.options_data[l.option_id].num), 
                    "detail" == d && (o.data.goodsDetail.totalnum ? o.data.goodsDetail.totalnum-- : o.data.goodsDetail.totalnum = 0), 
                    o.data.goodsAll.data[s] = i;
                    var g = {
                        cart: a.message.cart,
                        goodsAll: o.data.goodsAll,
                        goodsItem: o.data.goodsAll,
                        goodsActive: i,
                        goodsDetail: o.data.goodsDetail
                    };
                }
                a.message.cart.num || (g.showCartDetail = !1), o.setData(g), o.onCalculate();
            }
        });
    },
    onReachBottom: function() {},
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
            success: function(o) {
                o.confirm && t.util.request({
                    url: "wmall/store/goods/truncate",
                    data: {
                        sid: a.data.store.id
                    },
                    success: function() {
                        if (a.data.goodsActive) {
                            a.data.goodsActive.totalnum = 0;
                            for (var t in a.data.goodsActive.options_data) a.data.goodsActive.options_data[t].num = 0;
                        }
                        for (var t in a.data.goodsAll.data) {
                            a.data.goodsAll.data[t].totalnum = 0;
                            for (var o in a.data.goodsAll.data[t].options_data) a.data.goodsAll.data[t].options_data[o].num = 0;
                        }
                        a.data.goodsItem = a.data.goodsAll, a.setData({
                            "goodsDetail.totalnum": 0,
                            goodsAll: a.data.goodsAll,
                            goodsActive: a.data.goodsActive,
                            goodsItem: a.data.goodsItem,
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
        var a = this, o = t.currentTarget.dataset.index;
        if (!1 !== a.weToggleActiveGoods(o)) {
            var i = a.data.goodsActive;
            a.setData({
                sail_time: 1,
                week_cn: i.week_cn,
                time_cn: i.time_cn
            });
        }
    },
    onKnow: function() {
        this.setData({
            sail_time: 0
        });
    },
    onSubmit: function() {
        var a = this;
        if (1 == a.data.cart.is_category_limit) return wx.showModal({
            title: "温馨提示",
            content: a.data.cart.category_limit_cn
        }), !1;
        var o = "pages/order/create?sid=" + a.data.store.id + "&is_buysvip=" + a.data.cart.is_buysvip;
        a.data.pindan_id > 0 && (o = "pages/store/pindan?sid=" + a.data.store.id + "&pindan_id=" + a.data.pindan_id), 
        t.util.jump2url(o, "navigateTo");
    },
    onCloseSvip: function() {
        this.setData({
            "buySvipInfo.show": !1
        });
    }
});