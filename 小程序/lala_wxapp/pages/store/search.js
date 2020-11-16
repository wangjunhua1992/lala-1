var t = getApp();

Page({
    data: {
        keyword: "",
        searchHistory: [],
        hotGoods: [],
        records: {
            page: 1,
            psize: 10,
            empty: !1,
            loaded: !1,
            data: []
        },
        model: {
            cartDetail: !1,
            specShow: !1,
            sail_time: !1
        },
        buySvipInfo: {
            show: !1,
            goods: {}
        },
        pindan_id: 0,
        table_id: 0,
        Lang: t.Lang,
        wuiLoading: {
            show: !0,
            img: t.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(a) {
        var e = this;
        e.data.sid = a.sid, a.pindan_id > 0 && (e.data.pindan_id = a.pindan_id), a.table_id > 0 && (e.data.table_id = a.table_id), 
        t.util.request({
            url: "wmall/store/goods/search",
            data: {
                sid: e.data.sid,
                table_id: e.data.table_id,
                is_search: 0
            },
            success: function(a) {
                t.util.loaded();
                var o = a.data.message;
                if (o.errno) return t.util.toast(o.message), !1;
                var i = t.util.getStorageSync("isearchHistory.goods" + e.data.sid);
                i && (e.data.searchHistory = i);
                var s = "pages/store/goodsDetail?sid=" + o.message.store.id + "&pindan_id=" + e.data.pindan_id;
                if (e.data.table_id > 0 && (s = "tangshi/pages/table/goodsDetail?sid=" + o.message.store.id + "&table_id=" + e.data.table_id), 
                e.setData({
                    searchHistory: e.data.searchHistory,
                    hotGoods: o.message.hotGoods,
                    store: o.message.store,
                    pindan_id: e.data.pindan_id,
                    table_id: e.data.table_id,
                    table: o.message.table,
                    goodsDetailUrl: s
                }), e.data.store.data.wxapp) {
                    var d = e.data.store.data.wxapp.extPages;
                    if (d) {
                        var r = d.pages_store_goods.navigationBarBackgroundColor;
                        wx.setNavigationBarColor({
                            frontColor: "#ffffff",
                            backgroundColor: r
                        }), e.setData({
                            bgColor: r
                        });
                    }
                }
            }
        });
    },
    onInput: function(t) {
        this.data.keyword = t.detail.value;
    },
    onSearch: function(a) {
        var e = this, o = a.currentTarget.dataset.keyword;
        o ? e.data.keyword = o : e.data.keyword && -1 == e.data.searchHistory.indexOf(e.data.keyword) && (e.data.searchHistory.push(e.data.keyword), 
        t.util.setStorageSync("isearchHistory.goods" + e.data.sid, e.data.searchHistory)), 
        e.data.keyword ? e.onReachBottom(!0) : t.util.toast("请输入搜索条件");
    },
    onDeleteHistory: function() {
        var a = this;
        wx.showModal({
            content: "确定清空搜索历史吗",
            success: function(e) {
                e.confirm ? (t.util.removeStorageSync("isearchHistory.goods." + a.data.sid), a.setData({
                    searchHistory: []
                })) : e.cancel;
            }
        });
    },
    onReachBottom: function(a) {
        var e = this;
        1 == a && (e.data.records = {
            page: 1,
            psize: 10,
            empty: !1,
            loaded: !1,
            data: []
        }), e.data.records.loaded || t.util.request({
            url: "wmall/store/goods/search",
            data: {
                keyword: e.data.keyword,
                page: e.data.records.page,
                psize: e.data.records.psize,
                sid: e.data.sid,
                table_id: e.data.table_id,
                is_search: 1
            },
            success: function(a) {
                var o = a.data.message;
                if (o.errno) return t.util.toast(o.message), !1;
                o = o.message, e.data.records.data = e.data.records.data.concat(o.goods), o.goods.length < e.data.records.psize && (e.data.records.loaded = !0, 
                e.data.records.data.length || (e.data.records.empty = !0)), e.data.records.page++;
                var i = {
                    records: e.data.records
                };
                e.data.records.empty || (i.store = o.store, i.cart = o.cart.message.cart), e.setData(i), 
                e.data.records.empty || e.onCalculate();
            }
        });
    },
    weTranfterId2Index: function(t) {
        for (var a = this.data.records.data, e = 0, o = a.length; e < o; e++) if (a[e].id == t) return e;
        return -1;
    },
    weToggleActiveOption: function(t) {
        var a = this, e = a.data.goodsActive;
        t ? e.activeOptionId = t : (e.activeOptionId = e.activeOptions.option, e.activeOptions.attrs.length > 0 && (e.activeOptionId = e.activeOptionId + "_" + e.activeOptions.attrs.join("v"))), 
        e.activeOption = e.options_data[e.activeOptionId], a.data.goodsActive = e;
    },
    weToggleActiveGoods: function(t, a, e) {
        var o = this, i = t;
        if ("goods_id" == (e = e || "index") && -1 == (i = o.weTranfterId2Index(i))) return !1;
        var s = o.data.records.data[i];
        if (s.index = i, 1 == s.is_options || 1 == s.is_attrs) {
            if (!a && (s.activeOptions = {
                option: 0,
                attrs: [],
                optionSelected: 0,
                attrsSelected: []
            }, 1 == s.is_options && (s.activeOptions.option = s.options[0].id, s.activeOptions.optionSelected = s.options[0].id), 
            1 == s.is_attrs)) for (var d = 0; d < s.attrs.length; d++) s.activeOptions.attrs.push(d + "s0"), 
            s.activeOptions.attrsSelected[d] = 0;
            this.data.goodsActive = s, this.weToggleActiveOption(a);
        } else s.activeOptionId = 0;
        return o.setData({
            goodsActive: s
        }), i;
    },
    onChangeCart: function(a) {
        var e = this, o = 0;
        if (1 == e.data.cart.is_buysvip && (o = 1), "selectSvip" == a.detail.from) {
            var i = "plus", s = e.data.buySvipInfo.goods, d = s.goods_id, r = s.option_id, n = s.goodsIndex;
            e.setData({
                "buySvipInfo.show": !1
            }), 3 == a.detail.buysvip_status && (o = 1);
        } else {
            var i = a.currentTarget.dataset.changeType, n = a.currentTarget.dataset.index, c = a.currentTarget.dataset.from;
            if ("cart" == c) {
                var d = a.currentTarget.dataset.goodsid, r = a.currentTarget.dataset.optionid;
                !1 !== (p = e.weToggleActiveGoods(d, r, "goods_id")) && (n = p);
            } else if ("list" == c) var p = e.weToggleActiveGoods(n);
            if (!1 !== p) {
                var g = e.data.goodsActive;
                if (d = g.id, r = g.activeOptionId, !g.total && "plus" == i) return t.util.toast("库存不足"), 
                !1;
            }
            if ("plus" == i && 1 == e.data.goodsActive.svip_buy_show && 1 != e.data.cart.is_buysvip && e.data.cart.svip_buy_show >= 1) {
                var l = 0, u = 0;
                return e.data.goodsActive.activeOption ? (l = u = e.data.goodsActive.activeOption.price, 
                e.data.goodsActive.activeOption.svip_price > 0 && (l = e.data.goodsActive.activeOption.svip_price), 
                e.data.goodsActive.activeOption.origin_price > 0 && (u = e.data.goodsActive.activeOption.origin_price)) : (l = e.data.goodsActive.svip_price, 
                u = e.data.goodsActive.origin_price), e.data.buySvipInfo.goods = {
                    svip_price: l,
                    price: u,
                    goods_id: d,
                    option_id: r,
                    goodsIndex: n
                }, e.data.buySvipInfo.show = !0, void e.setData({
                    buySvipInfo: e.data.buySvipInfo
                });
            }
        }
        var v = {
            sid: e.data.store.id,
            goods_id: d,
            option_id: r,
            num: 1,
            sign: "plus" == i ? "+" : "-",
            is_buysvip: o
        }, _ = "wmall/store/goods/cart";
        e.data.table_id > 0 && (_ = "wmall/store/table/cart"), t.util.request({
            url: _,
            data: v,
            success: function(a) {
                if ((a = a.data.message).errno) return t.util.toast(a.message), !1;
                a.message.msg && t.util.toast(a.message.msg);
                var o = {
                    cart: a.message.cart
                };
                if (!1 !== p) {
                    var s = e.data.goodsActive;
                    if ("plus" == i) if (s.options_data[v.option_id].num) s.options_data[v.option_id].num++, 
                    s.totalnum++; else {
                        c = a.message.cart.data1[d][r].num;
                        s.options_data[v.option_id].num = c, s.totalnum += c;
                    } else if (a.message.cart.data1[d]) if (a.message.cart.data1[d][r]) s.totalnum--, 
                    s.options_data[v.option_id].num--; else {
                        var c = s.options_data[v.option_id].num;
                        s.options_data[v.option_id].num = 0, s.totalnum -= c;
                    } else s.totalnum = 0, s.options_data[v.option_id].num = 0;
                    1 != s.is_options && 1 != s.is_attrs || (s.activeOption.num = s.options_data[v.option_id].num), 
                    e.data.records.data[n] = s, o = {
                        cart: a.message.cart,
                        goodsActive: s,
                        "records.data": e.data.records.data
                    };
                }
                e.setData(o), e.onCalculate();
            }
        });
    },
    onToggleOption: function(t) {
        var a = this, e = a.data.goodsActive;
        if ("option" == t.currentTarget.dataset.type) e.activeOptions.option = t.currentTarget.dataset.id, 
        e.activeOptions.optionSelected = t.currentTarget.dataset.id; else {
            var o = t.currentTarget.dataset.index;
            e.activeOptions.attrs[o] = t.currentTarget.dataset.id, e.activeOptions.attrsSelected[o] = t.currentTarget.dataset.originid;
        }
        a.data.goodsActive = e, this.weToggleActiveOption(), a.setData({
            goodsActive: e
        });
    },
    onCalculate: function() {
        var t = this;
        t.data.cart.num ? t.data.store.send_condition = (t.data.store.send_price - t.data.cart.price - t.data.cart.box_price).toFixed(2) : t.data.store.send_condition = t.data.store.send_price, 
        t.setData({
            store: t.data.store
        });
    },
    onTurncateCart: function() {
        var a = this;
        wx.showModal({
            content: "确定清除购物车吗?",
            success: function(e) {
                e.confirm && t.util.request({
                    url: "wmall/store/goods/truncate",
                    data: {
                        sid: a.data.store.id
                    },
                    success: function() {
                        var t = a.data.records.data;
                        if (t.length > 0) for (var e in t) {
                            t[e].totalnum = 0;
                            for (var o in t[e].options_data) t[e].options_data[o].num = 0;
                        }
                        a.setData({
                            "records.data": t,
                            cart: {},
                            "model.cartDetail": !1
                        }), a.onCalculate();
                    }
                });
            }
        });
    },
    onSubmit: function() {
        var a = this;
        if (1 == a.data.cart.is_category_limit) return wx.showModal({
            title: "温馨提示",
            content: a.data.cart.category_limit_cn
        }), !1;
        var e = "pages/order/create?sid=" + a.data.store.id + "&is_buysvip=" + a.data.cart.is_buysvip;
        a.data.pindan_id > 0 && (e = "pages/store/pindan?sid=" + a.data.store.id + "&pindan_id=" + a.data.pindan_id), 
        t.util.jump2url(e, "navigateTo");
    },
    onSailTime: function(t) {
        var a = this, e = t.currentTarget.dataset.index;
        !1 !== a.weToggleActiveGoods(e) && a.setData({
            "model.sailTime": !0
        });
    },
    onSelectOption: function(t) {
        var a = this, e = t.currentTarget.dataset.index;
        a.weToggleActiveGoods(e), a.setData({
            "model.specShow": !0
        });
    },
    onToggleModel: function(t) {
        var a = t.currentTarget.dataset.type;
        if ("cartDetail" == a) {
            if (!this.data.cart.num) return !1;
            this.data.model.specShow = !1;
        }
        this.data.model[a] = !this.data.model[a], this.setData({
            model: this.data.model
        });
    },
    onJsEvent: function(a) {
        t.util.jsEvent(a);
    },
    onCloseSvip: function() {
        this.setData({
            "buySvipInfo.show": !1
        });
    }
});