var t = getApp(), a = require("../../../static/js/utils/underscore.js");

Page({
    data: {
        categoryAll: [],
        categorySelected: [],
        categorySelectedIndex: 0,
        categorySelectedId: 0,
        childSelectedIndex: 0,
        childSelectedId: 0,
        goodsAll: [],
        goodsItems: [],
        modelSpecShow: !1,
        showCartDetail: !1,
        showSpec: !1,
        couponStatus: 1,
        rest: !1,
        sail_time: 0,
        goods_id: "",
        cart: {
            num: 0
        },
        activityStatus: !1,
        shareData: {
            title: "",
            path: "",
            success: function() {},
            fail: function() {}
        },
        template: 2,
        modelRecommendShow: !0,
        Lang: t.Lang,
        wuiLoading: {
            show: !0,
            img: t.util.getStorageSync("theme.loading.img")
        },
        pindan_id: 0
    },
    onLoad: function(a) {
        var e = this;
        if (a) {
            if (a.scene) {
                var o = t.util.parseScene(a.scene);
                a.sid = o.sid, a.table_id = o.table_id, o.order_id && (a.order_id = o.order_id);
            }
            t.util.setStorageSync("store", a);
        } else a = t.util.getStorageSync("store");
        var d = t.util.getStorageSync("location");
        t.util.request({
            url: "wmall/store/table/index",
            data: {
                table_id: a.table_id,
                cid: a.cid || 0,
                sid: a.sid || 3,
                cart_id: a.cart_id,
                order_id: a.order_id,
                __lat: d.x,
                __lng: d.y,
                forceLocation: 1
            },
            success: function(o) {
                t.util.loaded();
                var d = o.data.message.errno;
                if (o = o.data.message.message, d) return -2 == d ? void t.util.toast(o, "redirect:/pages/home/index", 1500) : void t.util.toast(o);
                var i = {
                    page: 2,
                    psize: 30,
                    empty: 0,
                    data: o.goods,
                    orderby: {
                        type: "",
                        value: ""
                    }
                };
                if (i.data.length < i.psize && (i.loaded = 1, i.data.length || (i.empty = 1)), e.data.categorySelectedId = o.cid, 
                e.data.childSelectedId = o.child_id, e.data.goodsAll[e.data.categorySelectedIndex] = [], 
                e.data.goodsAll[e.data.categorySelectedIndex][e.data.childSelectedIndex] = i, e.data.store = o.store, 
                o.store.is_in_business_hours || (e.data.rest = !0), o.store.activity.num || (e.data.activityStatus = !0), 
                e.data.pindan_id = o.cart.message.cart.pindan_id, e.setData({
                    store: o.store,
                    coupon: o.coupon,
                    categoryAll: o.category,
                    categorySelected: o.category[e.data.categorySelectedIndex],
                    goodsAll: e.data.goodsAll,
                    goodsItem: i,
                    goodsLoading: 0,
                    cart: o.cart.message.cart,
                    rest: e.data.rest,
                    activityStatus: e.data.activityStatus,
                    template: o.template,
                    table: o.table,
                    table_id: a.table_id,
                    order_id: a.order_id,
                    pindan_id: e.data.pindan_id,
                    categorySelectedId: e.data.categorySelectedId
                }), t.util.setStorageSync("store", o.store), e.onCalculate(), e.data.shareData.title = o.store.title, 
                e.data.shareData.path = "/pages/store/goods?sid=" + o.store.id, wx.setNavigationBarTitle({
                    title: e.data.store.title
                }), e.data.store.data.wxapp) {
                    var s = e.data.store.data.wxapp.extPages;
                    if (s && s.pages_store_goods && s.pages_store_goods.navigationBarBackgroundColor) {
                        var r = s.pages_store_goods.navigationBarBackgroundColor;
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
    onReady: function() {},
    onShow: function() {
        var a = this, e = t.util.getStorageSync("store");
        a.setData({
            categorySelectedIndex: a.data.categorySelectedIndex,
            modelSpecShow: a.data.modelSpecShow,
            showCartDetail: a.data.showCartDetail,
            showSpec: a.data.showSpec,
            couponStatus: a.data.couponStatus,
            categoryAll: a.data.categoryAll,
            categorySelected: a.data.categorySelected,
            categorySelectedId: a.data.categorySelectedId,
            goodsAll: a.data.goodsAll,
            goodsItems: a.data.goodsItems,
            childSelectedIndex: a.data.childSelectedIndex,
            childSelectedId: a.data.childSelectedId
        }), e && e.id && (a.data.categoryAll = [], a.data.categorySelected = [], a.data.categorySelectedIndex = 0, 
        a.data.categorySelectedId = 0, a.data.childSelectedId = 0, a.data.childSelectedIndex = 0, 
        a.data.goodsAll = [], a.data.goodsItems = [], a.data.modelSpecShow = !1, a.data.showCartDetail = !1, 
        a.data.showSpec = !1, a.data.couponStatus = 1, a.data.rest = !1, a.setData({
            categorySelectedIndex: a.data.categorySelectedIndex,
            childSelectedIndex: a.data.childSelectedIndex,
            modelSpecShow: a.data.modelSpecShow,
            showCartDetail: a.data.showCartDetail,
            showSpec: a.data.showSpec,
            couponStatus: a.data.couponStatus
        }), a.onLoad({
            sid: e.id,
            table_id: e.table_id
        }));
    },
    onHide: function() {
        console.log("goods/监听页面隐藏");
    },
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onSelectOption: function(t) {
        var a = this, e = t.currentTarget.dataset.index;
        this.weToggleActiveGoods(e), a.setData({
            modelSpecShow: !0
        });
    },
    onHideOption: function() {
        this.setData({
            modelSpecShow: !1
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
    weToggleActiveOption: function(t) {
        var a = this, e = a.data.goodsActive;
        t ? e.activeOptionId = t : (e.activeOptionId = e.activeOptions.option, e.activeOptions.attrs.length > 0 && (e.activeOptionId = e.activeOptionId + "_" + e.activeOptions.attrs.join("v"))), 
        e.activeOption = e.options_data[e.activeOptionId], a.data.goodsActive = e;
    },
    weTranfterId2Index: function(t, e, o) {
        var d = this;
        e = e.toString();
        var i = a.findWhere(d.data.categoryAll, {
            id: e
        });
        if (!i.id) return -1;
        var s = a.indexOf(d.data.categoryAll, i);
        if (-1 == s) return -1;
        if (!d.data.goodsAll[s]) return -1;
        var r = 0;
        if (i.child) {
            o = o.toString();
            var l = a.findWhere(i.child, {
                id: o
            });
            if (!l.id) return -1;
            if (-1 == (r = a.indexOf(i.child, l))) return -1;
        }
        if (!d.data.goodsAll[s][r]) return -1;
        t = t.toString();
        var c = a.findWhere(d.data.goodsAll[s][r].data, {
            id: t
        });
        if (!c.id) return -1;
        var n = a.indexOf(d.data.goodsAll[s][r].data, c);
        return -1 == n ? -1 : {
            cindex: s,
            gindex: n,
            childindex: r
        };
    },
    weToggleActiveGoods: function(t, a, e, o, d) {
        var i = this, s = i.data.categorySelectedIndex, r = t, l = i.data.childSelectedIndex;
        if ("goods_id" == (d = d || "index")) {
            var c = i.weTranfterId2Index(t, e, o);
            if (-1 == c) return !1;
            s = c.cindex, r = c.gindex, l = c.childindex;
        }
        var n = i.data.goodsAll[s][l].data[r];
        if (n.index = r, 1 == n.is_options || 1 == n.is_attrs) {
            if (!a && (n.activeOptions = {
                option: 0,
                attrs: [],
                optionSelected: 0,
                attrsSelected: []
            }, 1 == n.is_options && (n.activeOptions.option = n.options[0].id, n.activeOptions.optionSelected = n.options[0].id), 
            1 == n.is_attrs)) for (var g = 0; g < n.attrs.length; g++) n.activeOptions.attrs.push(g + "s0"), 
            n.activeOptions.attrsSelected[g] = 0;
            i.data.goodsActive = n, this.weToggleActiveOption(a);
        } else n.activeOptionId = 0;
        return i.data.goodsActive = n, i.setData({
            goodsActive: n
        }), r;
    },
    onPlus: function(a) {
        var e = this, o = a.currentTarget.dataset.index, d = a.currentTarget.dataset.from;
        if ("list" == d || "detail" == d) var i = e.weToggleActiveGoods(o); else if ("cart" == d) {
            var s = a.currentTarget.dataset.goodsid, r = a.currentTarget.dataset.cid, l = a.currentTarget.dataset.childid, c = a.currentTarget.dataset.optionid;
            !1 !== (i = e.weToggleActiveGoods(s, c, r, l, "goods_id")) && (o = i);
        }
        if (!1 !== i) {
            var n = e.data.goodsActive;
            if (s = n.id, c = n.activeOptionId, !n.total) return t.util.toast("库存不足"), !1;
        }
        var g = {
            sid: e.data.store.id,
            goods_id: s,
            option_id: c,
            num: 1,
            sign: "+",
            order_id: e.data.order_id || 0
        };
        t.util.request({
            url: "wmall/store/table/cart",
            data: g,
            success: function(a) {
                if ((a = a.data.message).errno) return t.util.toast(a.message), !1;
                a.message.msg && t.util.toast(a.message.msg);
                var r = {
                    cart: a.message.cart
                };
                if (!1 !== i) {
                    var l = e.data.goodsActive;
                    if (l.options_data[g.option_id].num) l.options_data[g.option_id].num++, l.totalnum++; else {
                        var n = a.message.cart.data1[s][c].num;
                        l.options_data[g.option_id].num = n, l.totalnum += n;
                    }
                    1 != l.is_options && 1 != l.is_attrs || (l.activeOption.num = l.options_data[g.option_id].num), 
                    "detail" == d && (e.data.goodsDetail.totalnum ? e.data.goodsDetail.totalnum++ : e.data.goodsDetail.totalnum = 1), 
                    e.data.goodsAll[e.data.categorySelectedIndex][e.data.childSelectedIndex].data[o] = l, 
                    r = {
                        cart: a.message.cart,
                        goodsAll: e.data.goodsAll,
                        goodsItem: e.data.goodsAll[e.data.categorySelectedIndex][e.data.childSelectedIndex],
                        goodsActive: l,
                        goodsDetail: e.data.goodsDetail
                    };
                }
                e.setData(r), e.onCalculate();
            }
        });
    },
    onJsEvent: function(a) {
        t.util.jsEvent(a);
    },
    onCalculate: function() {
        var t = this;
        t.data.cart.num ? t.data.store.send_condition = (t.data.store.send_price - t.data.cart.price - t.data.cart.box_price).toFixed(2) : t.data.store.send_condition = t.data.store.send_price;
        var a = t.data.cart.data;
        if (a) for (var e in t.data.categoryAll) if (t.data.categoryAll[e].total = 0, t.data.categoryAll[e].child) for (var o in t.data.categoryAll[e].child) {
            t.data.categoryAll[e].child[o].total = 0;
            var d = 0, i = 0;
            for (var s in a) for (var r in a[s]) a[s][r].cid == t.data.categoryAll[e].id && (i += a[s][r].num, 
            t.data.categoryAll[e].total = i, a[s][r].child_id == t.data.categoryAll[e].child[o].id && (d += a[s][r].num, 
            t.data.categoryAll[e].child[o].total = d));
        } else {
            i = 0;
            for (var s in a) for (var r in a[s]) a[s][r].cid == t.data.categoryAll[e].id && (i += a[s][r].num, 
            t.data.categoryAll[e].total = i);
        } else for (var e in t.data.categoryAll) if (t.data.categoryAll[e].total = 0, t.data.categoryAll[e].child) for (var o in t.data.categoryAll[e].child) t.data.categoryAll[e].child[o].total = 0;
        t.setData({
            store: t.data.store,
            categoryAll: t.data.categoryAll,
            categorySelected: t.data.categoryAll[t.data.categorySelectedIndex]
        });
    },
    onMinus: function(a) {
        var e = this, o = a.currentTarget.dataset.index, d = a.currentTarget.dataset.from;
        if ("list" == d || "detail" == d) var i = e.weToggleActiveGoods(o); else if ("cart" == d) {
            var s = a.currentTarget.dataset.goodsid, r = a.currentTarget.dataset.cid, l = a.currentTarget.dataset.optionid, c = a.currentTarget.dataset.childid;
            !1 !== (i = e.weToggleActiveGoods(s, l, r, c, "goods_id")) && (o = i);
        }
        if (!1 !== i) {
            var n = e.data.goodsActive;
            s = n.id, l = n.activeOptionId;
        }
        var g = {
            sid: e.data.store.id,
            goods_id: s,
            option_id: l,
            num: 1,
            sign: "-",
            order_id: e.data.order_id || 0
        };
        t.util.request({
            url: "wmall/store/table/cart",
            data: g,
            success: function(a) {
                if ((a = a.data.message).errno) return t.util.toast(a.message), !1;
                n = {
                    cart: a.message.cart
                };
                if (!1 !== i) {
                    var r = e.data.goodsActive;
                    if (a.message.cart.data1[s]) if (a.message.cart.data1[s][l]) r.totalnum--, r.options_data[g.option_id].num--; else {
                        var c = r.options_data[g.option_id].num;
                        r.options_data[g.option_id].num = 0, r.totalnum -= c;
                    } else r.totalnum = 0, r.options_data[g.option_id].num = 0;
                    1 != r.is_options && 1 != r.is_attrs || (r.activeOption.num = r.options_data[g.option_id].num), 
                    "detail" == d && (e.data.goodsDetail.totalnum ? e.data.goodsDetail.totalnum-- : e.data.goodsDetail.totalnum = 0), 
                    e.data.goodsAll[e.data.categorySelectedIndex][e.data.childSelectedIndex].data[o] = r;
                    var n = {
                        cart: a.message.cart,
                        goodsAll: e.data.goodsAll,
                        goodsItem: e.data.goodsAll[e.data.categorySelectedIndex][e.data.childSelectedIndex],
                        goodsActive: r,
                        goodsDetail: e.data.goodsDetail
                    };
                }
                a.message.cart.num || (n.showCartDetail = !1), e.setData(n), e.onCalculate();
            }
        });
    },
    onReachBottom: function() {
        this.onGetGoods();
    },
    onCollectCoupon: function() {
        var a = this;
        t.util.request({
            url: "wmall/channel/coupon/get",
            data: {
                sid: a.data.coupon.sid
            },
            success: function(e) {
                0 == e.data.message.errno ? (t.util.toast(e.data.message.message), a.setData({
                    couponStatus: 0
                })) : t.util.toast("领取失败");
            }
        });
    },
    onToggleCategory: function(t) {
        var a = this, e = 0, o = t.currentTarget.dataset.index, d = t.currentTarget.dataset.cid;
        "child" == t.currentTarget.dataset.from ? (o = a.data.categorySelectedIndex, e = t.currentTarget.dataset.childIndex, 
        d = a.data.categoryAll[a.data.categorySelectedIndex].id) : a.data.categoryAll[o].childSelectedIndex > 0 && (e = a.data.categoryAll[o].childSelectedIndex), 
        a.data.categorySelected = a.data.categoryAll[o], a.data.categoryAll[o].childSelectedIndex = e;
        var i = 0;
        a.data.categorySelected.child && (i = a.data.categorySelected.child[e].id), a.setData({
            categorySelectedIndex: o,
            categorySelectedId: d,
            categorySelected: a.data.categoryAll[o],
            childSelectedIndex: e,
            childSelectedId: i
        }), a.onGetGoods();
    },
    onOrderby: function(t) {
        var a = this, e = {
            page: 1,
            psize: 30,
            empty: 0,
            data: [],
            orderby: {
                type: t.currentTarget.dataset.type,
                value: t.currentTarget.dataset.value
            }
        };
        a.data.goodsAll[a.data.categorySelectedIndex][a.data.childSelectedIndex] = e, a.setData(a.data.orderby), 
        a.onGetGoods();
    },
    onGetGoods: function() {
        var a = this;
        if (1 == a.data.goodsLoading) return console.log("商品正在加载中"), !1;
        a.data.goodsAll[a.data.categorySelectedIndex] || (a.data.goodsAll[a.data.categorySelectedIndex] = []);
        var e = a.data.goodsAll[a.data.categorySelectedIndex][a.data.childSelectedIndex];
        if (e) {
            if (a.setData({
                goodsItem: e
            }), e.empty) return !1;
            if (e.loaded) return console.log("商品全部加载完成"), !1;
        } else e = {
            page: 1,
            psize: 30,
            empty: 0,
            data: [],
            orderby: {
                type: "",
                value: ""
            }
        };
        a.data.goodsLoading = 1, t.util.request({
            url: "wmall/store/table/goods",
            data: {
                sid: a.data.store.id,
                page: e.page,
                psize: e.psize,
                cid: a.data.categorySelectedId,
                child_id: a.data.childSelectedId,
                type: e.orderby.type,
                value: e.orderby.value
            },
            success: function(t) {
                var t = t.data.message.message;
                e.data = e.data.concat(t.goods), e.page++, t.goods.length < a.data.goodsItem.psize && (e.loaded = 1, 
                t.goods.length || (e.empty = 1)), a.data.goodsAll[a.data.categorySelectedIndex][a.data.childSelectedIndex] = e, 
                a.setData({
                    goodsAll: a.data.goodsAll,
                    goodsItem: e,
                    goodsLoading: 0
                });
            }
        });
    },
    onFavor: function(a) {
        var e = this, o = a.currentTarget.dataset.sid, d = e.data.store;
        if (e.data.store.is_favorite) i = "cancal"; else var i = "star";
        var s = {
            id: o,
            type: i
        };
        t.util.request({
            url: "wmall/member/op/favorite",
            data: s,
            success: function(a) {
                0 == a.data.message.errno ? "star" == i ? (t.util.toast("添加收藏成功"), d.is_favorite = !d.is_favorite, 
                e.setData({
                    store: d
                })) : (t.util.toast("取消收藏成功"), d.is_favorite = !d.is_favorite, e.setData({
                    store: d
                })) : t.util.toast(a.data.message.message);
            }
        });
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
            success: function(e) {
                e.confirm && t.util.request({
                    url: "wmall/store/table/truncate",
                    data: {
                        sid: a.data.store.id
                    },
                    success: function() {
                        if (a.data.goodsActive) {
                            a.data.goodsActive.totalnum = 0;
                            for (var t in a.data.goodsActive.options_data) a.data.goodsActive.options_data[t].num = 0;
                        }
                        for (var t in a.data.goodsAll) for (var e in a.data.goodsAll[t]) for (var o in a.data.goodsAll[t][e].data) {
                            a.data.goodsAll[t][e].data[o].totalnum = 0;
                            for (var d in a.data.goodsAll[t][e].data[o].options_data) a.data.goodsAll[t][e].data[o].options_data[d].num = 0;
                        }
                        a.data.goodsItem = a.data.goodsAll[a.data.categorySelectedIndex][a.data.childSelectedIndex], 
                        a.setData({
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
        var a = this, e = t.currentTarget.dataset.index;
        if (!1 !== a.weToggleActiveGoods(e)) {
            var o = a.data.goodsActive;
            a.setData({
                sail_time: 1,
                week_cn: o.week_cn,
                time_cn: o.time_cn
            });
        }
    },
    onKnow: function() {
        this.setData({
            sail_time: 0
        });
    },
    onToggleDiscount: function(t) {
        var a = t.currentTarget.dataset;
        this.data.recommend_stores[a.index].activity.is_show_all = !this.data.recommend_stores[a.index].activity.is_show_all, 
        this.setData({
            recommend_stores: this.data.recommend_stores
        });
    },
    onCallServe: function(a) {
        var e = a.currentTarget.dataset;
        t.util.request({
            url: "wmall/store/call/index",
            data: e,
            success: function(a) {
                t.util.toast(a.data.message.message);
            }
        });
    }
});