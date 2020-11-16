var t = getApp();

Page({
    data: {
        showCartDetail: !1,
        sail_time: 0,
        Lang: t.Lang,
        wuiLoading: {
            show: !0,
            img: t.util.getStorageSync("theme.loading.img")
        },
        pindan_id: 0
    },
    onLoad: function(a) {
        var o = this, i = a.id, e = a.sid, s = a.order_id > 0 ? a.order_id : 0;
        a.pindan_id > 0 && (o.data.pindan_id = a.pindan_id);
        if (!i) return !1;
        var n = {
            id: i,
            sid: e,
            table_id: a.table_id
        };
        t.util.request({
            url: "wmall/store/table/detail",
            data: n,
            success: function(a) {
                t.util.loaded();
                var i = a.data.message.message;
                i.goodsDetail.index = 0;
                var e = i.goodsDetail;
                if (1 == e.is_options || 1 == e.is_attrs) {
                    if (e.activeOptions = {
                        option: 0,
                        attrs: [],
                        optionSelected: 0,
                        attrsSelected: []
                    }, 1 == e.is_options && (e.activeOptions.option = e.options[0].id, e.activeOptions.optionSelected = e.options[0].id), 
                    1 == e.is_attrs) for (var n = 0; n < e.attrs.length; n++) e.activeOptions.attrs.push(n + "s0"), 
                    e.activeOptions.attrsSelected[n] = 0;
                    e.activeOptionId = e.activeOptions.option, e.activeOptions.attrs.length > 0 && (e.activeOptionId = e.activeOptionId + "_" + e.activeOptions.attrs.join("v")), 
                    e.activeOption = e.options_data[e.activeOptionId];
                } else e.activeOptionId = 0;
                if (t.WxParse.wxParse("description", "html", i.goodsDetail.description, o, 5), o.setData({
                    cart: i.cart.message.cart,
                    goodsDetail: e,
                    store: i.store,
                    goodsActive: e,
                    table: i.table,
                    order_id: s,
                    pindan_id: o.data.pindan_id
                }), o.onCalculate(), o.data.store.data.wxapp) {
                    var d = o.data.store.data.wxapp.extPages.pages_store_goods.navigationBarBackgroundColor;
                    wx.setNavigationBarColor({
                        frontColor: "#ffffff",
                        backgroundColor: d
                    }), o.setData({
                        bgColor: d
                    });
                }
            }
        });
    },
    onJsEvent: function(a) {
        t.util.jsEvent(a);
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
    onPlus: function(a) {
        var o = this, i = a.currentTarget.dataset, e = i.from, s = i.goodsid;
        if ("cart" == e) var s = a.currentTarget.dataset.goodsid, n = a.currentTarget.dataset.optionid; else {
            var d = o.data.goodsActive;
            if (s = d.id, n = d.activeOptionId, !d.total) return t.util.toast("库存不足"), !1;
        }
        var r = {
            sid: o.data.store.id,
            goods_id: s,
            option_id: n,
            num: 1,
            sign: "+"
        };
        t.util.request({
            url: "wmall/store/table/cart",
            data: r,
            success: function(a) {
                if ((a = a.data.message).errno) return t.util.toast(a.message), !1;
                a.message.msg && t.util.toast(a.message.msg);
                var i = o.data.goodsActive;
                if (r.goods_id == i.id) {
                    if (i.options_data[r.option_id].num) i.options_data[r.option_id].num++, i.totalnum++; else {
                        var e = a.message.cart.data1[s][n].num;
                        i.options_data[r.option_id].num = e, i.totalnum += e;
                    }
                    r.option_id != i.activeOptionId || 1 != i.is_options && 1 != i.is_attrs || (i.activeOption.num = i.options_data[r.option_id].num);
                }
                var d = {
                    cart: a.message.cart,
                    goodsActive: i,
                    goodsDetail: i
                };
                o.setData(d), o.onCalculate();
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
        var o = this, i = a.currentTarget.dataset, e = (i.index, i.from), s = i.goodsid;
        if ("cart" == e) var s = a.currentTarget.dataset.goodsid, n = a.currentTarget.dataset.optionid; else {
            var d = o.data.goodsActive;
            s = d.id, n = d.activeOptionId;
        }
        var r = {
            sid: o.data.store.id,
            goods_id: s,
            option_id: n,
            num: 1,
            sign: "-"
        };
        t.util.request({
            url: "wmall/store/table/cart",
            data: r,
            success: function(a) {
                if ((a = a.data.message).errno) return t.util.toast(a.message), !1;
                var i = o.data.goodsActive;
                if (r.goods_id == i.id) {
                    if (a.message.cart.data1[s]) if (a.message.cart.data1[s][n]) i.totalnum--, i.options_data[r.option_id].num--; else {
                        var e = i.options_data[r.option_id].num;
                        i.options_data[r.option_id].num = 0, i.totalnum -= e;
                    } else i.totalnum = 0, i.options_data[r.option_id].num = 0;
                    r.option_id != i.activeOptionId || 1 != i.is_options && 1 != i.is_attrs || (i.activeOption.num = i.options_data[r.option_id].num), 
                    o.data.goodsDetail = i;
                }
                var d = {
                    cart: a.message.cart,
                    goodsActive: i,
                    goodsDetail: i
                };
                a.message.cart.num || (d.showCartDetail = !1), o.setData(d), o.onCalculate();
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
                    url: "wmall/store/table/truncate",
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
    }
});