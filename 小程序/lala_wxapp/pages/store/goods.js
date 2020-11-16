var t = getApp(), a = require("../../static/js/utils/underscore.js");

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
            desc: "",
            imageUrl: "",
            path: "",
            success: function() {},
            fail: function() {}
        },
        template: 2,
        modelRecommendShow: !0,
        tabActive: 0,
        buySvipInfo: {
            show: !1,
            goods: {}
        },
        pindan_id: 0,
        Lang: t.Lang,
        wuiLoading: {
            show: !0,
            img: t.util.getStorageSync("theme.loading.img")
        },
        menu: {
            css: {},
            params: {},
            data: {},
            position: {
                left: "15px",
                bottom: "80px",
                right: "inherit"
            }
        },
        showNotice: !1,
        goods_images: []
    },
    onLoad: function(a) {
        var e = this;
        if (a) {
            if (a.scene) {
                var o = decodeURIComponent(a.scene), d = (o = o.split(":"))[1];
                t.util.setStorageSync("store", d), a.sid = d;
            } else t.util.setStorageSync("store", a.sid);
            a.pindan_id && (e.data.pindan_id = a.pindan_id);
        } else a = t.util.getStorageSync("store");
        var i = t.util.getStorageSync("location");
        t.util.request({
            url: "wmall/store/goods/index",
            data: {
                cid: a.cid || 0,
                sid: a.sid || 3,
                order_id: a.order_id,
                __lat: i.x,
                __lng: i.y,
                forceLocation: 1,
                configmall: 1
            },
            success: function(a) {
                t.util.loaded();
                var o = a.data.global, d = a.data.message.errno;
                if (a = a.data.message.message, d) return -2 == d ? void t.util.toast(a, "redirect:/pages/home/index", 1500) : void t.util.toast(a);
                e.data.store = a.store, a.store.is_in_business_hours || (e.data.rest = !0), a.store.activity.num || (e.data.activityStatus = !0), 
                e.setData({
                    store: a.store,
                    coupon: a.coupon,
                    cart: a.cart.message.cart,
                    rest: e.data.rest,
                    activityStatus: e.data.activityStatus,
                    template: a.template,
                    template_page: a.template_page,
                    recommend_stores: a.recommend_stores,
                    configmall: o.configmall,
                    pindan_id: e.data.pindan_id
                }), t.util.setStorageSync("store", a.store), t.util.selectPindan({
                    pindan_id: e.data.pindan_id,
                    cart_pindan_id: a.cart.message.cart.pindan_id,
                    sid: a.store.id,
                    cart_id: a.cart.message.cart.id
                }), e.data.shareData.title = o.share.title, e.data.shareData.desc = o.share.desc, 
                e.data.shareData.imageUrl = o.share.imgUrl;
                var i = "/pages/store/goods?sid=" + a.store.id;
                if (1 == a.config_mall.version && (i = "/pages/home/index?sid=" + a.store.id), e.data.shareData.path = i, 
                wx.setNavigationBarTitle({
                    title: e.data.store.title
                }), 1 == a.template_page) {
                    var s = {
                        page: 2,
                        psize: 30,
                        empty: 0,
                        data: a.goods,
                        orderby: {
                            type: "",
                            value: ""
                        }
                    };
                    s.data.length < s.psize && (s.loaded = 1, s.data.length || (s.empty = 1)), e.data.categorySelectedId = a.cid, 
                    e.data.childSelectedId = a.child_id, e.data.categorySelectedIndex = a.cindex, e.data.goodsAll[e.data.categorySelectedIndex] = [], 
                    e.data.goodsAll[e.data.categorySelectedIndex][e.data.childSelectedIndex] = s, e.setData({
                        categoryAll: a.category,
                        categorySelected: a.category[e.data.categorySelectedIndex],
                        goodsAll: e.data.goodsAll,
                        goodsItem: s,
                        goodsLoading: 0,
                        categorySelectedId: e.data.categorySelectedId,
                        categorySelectedIndex: e.data.categorySelectedIndex
                    });
                } else e.setData({
                    cateHasGoods: a.cate_has_goods,
                    tabActive: a.tabActive,
                    containerActive: "cateHasGoods-container-" + a.tabActive
                }, function() {
                    e.onGetHeights();
                });
                if (e.onCalculate(), e.data.store.data.wxapp) {
                    var n = e.data.store.data.wxapp.extPages;
                    if (n) {
                        var r = n.pages_store_goods.navigationBarBackgroundColor;
                        wx.setNavigationBarColor({
                            frontColor: "#ffffff",
                            backgroundColor: r
                        }), e.setData({
                            bgColor: r
                        });
                    }
                }
                if (e.data.store.tips) {
                    var c = "storeNotice" + e.data.store.id, l = t.util.getStorageSync(c);
                    (!l || l && !l.notice) && (t.util.setStorageSync(c, {
                        notice: 1
                    }, 300), e.setData({
                        showNotice: !0,
                        "store.tips": e.data.store.tips
                    }));
                }
                e.data.store.menu && e.data.store.menu.data && (e.data.menu = Object.assign(e.data.menu, e.data.store.menu.data)), 
                e.data.store.menu && "1" == e.data.store.menu.path.goods && t.util.setNavigator(e.data.menu);
            }
        });
    },
    onGetHeights: function() {
        var t = this, a = wx.createSelectorQuery(), e = [], o = 0;
        a.select(".coupon-show-container").boundingClientRect(function(t) {
            t && t.height && (o += t.height);
        }), a.select(".banner").boundingClientRect(function(t) {
            t && t.height && (o += t.height);
        }), a.selectAll(".cateHasGoods-container").boundingClientRect(function(a) {
            a.forEach(function(t) {
                o += t.height, e.push(o);
            }), t.setData({
                heightArr: e
            });
        }), a.select(".goods-container").boundingClientRect(function(a) {
            t.setData({
                goodsContainerHeight: a.height
            });
        }).exec();
    },
    onScroll: function(t) {
        var a = this, e = t.detail.scrollTop, o = a.data.heightArr, d = a.data.goodsContainerHeight, i = a.data.tabActive, s = 0;
        if (o.length > 0) {
            if (e >= o[o.length - 1] - d) return;
            for (var n = 0; n < o.length; n++) e >= 0 && e < o[0] ? s = 0 : e >= o[n - 1] && e < o[n] && (s = n);
            s != i && a.setData({
                tabActive: s
            });
        }
    },
    onToggleTab: function(t) {
        var a = this, e = t.currentTarget.dataset.index;
        a.setData({
            containerActive: "cateHasGoods-container-" + e,
            tabActive: e
        });
    },
    onReady: function() {},
    onShow: function() {
        var a = this;
        console.log("onShow", a.data);
        var e = t.util.getStorageSync("store");
        e && e.id && a.onUpdateCartData();
    },
    onUpdateCartData: function() {
        var a = this;
        t.util.request({
            url: "wmall/store/goods/updateCart",
            data: {
                sid: a.data.store.id
            },
            success: function(e) {
                if ((e = e.data.message).errno) return t.util.toast(e.message), !1;
                e = e.message, a.setData({
                    cart: e.cart.message.cart
                }), a.onUpdateGoodsNum(), a.onCalculate();
            }
        });
    },
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onSelectOption: function(t) {
        var a = this, e = t.currentTarget.dataset.index;
        if (1 == a.data.template_page) a.weToggleActiveGoods(e), a.setData({
            modelSpecShow: !0
        }); else {
            var o = t.currentTarget.dataset.cindex;
            a.weToggleActiveGoods1(e, o), a.weToggleActiveOption(), a.setData({
                cIndexActive: o,
                modelSpecShow: !0
            });
        }
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
        if (e = e.toString(), 1 == d.data.template_page) {
            if (!(n = a.findWhere(d.data.categoryAll, {
                id: e
            })).id) return -1;
            if (-1 == (r = a.indexOf(d.data.categoryAll, n))) return -1;
            if (!d.data.goodsAll[r]) return -1;
            var i = 0;
            if (n.child) {
                o = o.toString();
                var s = a.findWhere(n.child, {
                    id: o
                });
                if (!s.id) return -1;
                if (-1 == (i = a.indexOf(n.child, s))) return -1;
            }
            return d.data.goodsAll[r][i] ? (t = t.toString(), (c = a.findWhere(d.data.goodsAll[r][i].data, {
                id: t
            })).id ? -1 == (l = a.indexOf(d.data.goodsAll[r][i].data, c)) ? -1 : {
                cindex: r,
                gindex: l,
                childindex: i
            } : -1) : -1;
        }
        var n = a.findWhere(d.data.cateHasGoods, {
            id: e
        });
        if (!n.id) return -1;
        var r = a.indexOf(d.data.cateHasGoods, n);
        if (-1 == r) return -1;
        if (!d.data.cateHasGoods[r]) return -1;
        t = t.toString();
        var c = a.findWhere(d.data.cateHasGoods[r].goods, {
            id: t
        });
        if (!c.id) return -1;
        var l = a.indexOf(d.data.cateHasGoods[r].goods, c);
        return -1 == l ? -1 : {
            cindex: r,
            gindex: l
        };
    },
    weToggleActiveGoods: function(t, a, e, o, d) {
        var i = this, s = i.data.categorySelectedIndex, n = t, r = i.data.childSelectedIndex;
        if ("goods_id" == (d = d || "index")) {
            var c = i.weTranfterId2Index(t, e, o);
            if (-1 == c) return !1;
            s = c.cindex, n = c.gindex, r = c.childindex;
        }
        var l = i.data.goodsAll[s][r].data[n];
        if (l.index = n, 1 == l.is_options || 1 == l.is_attrs) {
            if (!a && (l.activeOptions = {
                option: 0,
                attrs: [],
                optionSelected: 0,
                attrsSelected: []
            }, 1 == l.is_options && (l.activeOptions.option = l.options[0].id, l.activeOptions.optionSelected = l.options[0].id), 
            1 == l.is_attrs)) for (var g = 0; g < l.attrs.length; g++) l.activeOptions.attrs.push(g + "s0"), 
            l.activeOptions.attrsSelected[g] = 0;
            i.data.goodsActive = l, this.weToggleActiveOption(a);
        } else l.activeOptionId = 0;
        return i.data.goodsActive = l, i.setData({
            goodsActive: l
        }), n;
    },
    weToggleActiveGoods1: function(t, a, e, o) {
        var d = this, i = a, s = t;
        if ("id" == (o = o || "index")) {
            var n = d.weTranfterId2Index(t, a);
            if (-1 == n) return !1;
            i = n.cindex, s = n.gindex;
        }
        var r = d.data.cateHasGoods[i].goods[s];
        if (r.index = s, r.cindex = i, 1 == r.is_options || 1 == r.is_attrs) {
            if (!e) {
                if (r.activeOptions = {
                    option: 0,
                    attrs: [],
                    optionSelected: 0,
                    attrsSelected: []
                }, 1 == r.is_options) {
                    var c = Object.keys(r.options);
                    r.activeOptions.option = r.options[c[0]].id, r.activeOptions.optionSelected = r.options[c[0]].id;
                }
                if (1 == r.is_attrs) for (var l = 0; l < r.attrs.length; l++) r.activeOptions.attrs.push(l + "s0"), 
                r.activeOptions.attrsSelected[l] = 0;
            }
            d.data.goodsActive = r, this.weToggleActiveOption(e);
        } else r.activeOptionId = 0;
        return d.data.goodsActive = r, d.setData({
            goodsActive: r
        }), s;
    },
    onPlus: function(a) {
        var e = this, o = 0;
        if (1 == e.data.cart.is_buysvip && (o = 1), "selectSvip" == a.detail.from) {
            var d = e.data.buySvipInfo.goods, i = d.goods_id, s = d.option_id, n = d.goodsIndex, r = d.cIndex;
            e.setData({
                "buySvipInfo.show": !1
            }), 3 == a.detail.buysvip_status && (o = 1);
        } else {
            var n = a.currentTarget.dataset.index, c = a.currentTarget.dataset.from;
            if (1 == e.data.template_page) {
                if ("list" == c || "detail" == c) u = e.weToggleActiveGoods(n); else if ("cart" == c) {
                    var i = a.currentTarget.dataset.goodsid, l = a.currentTarget.dataset.cid, g = a.currentTarget.dataset.childid, s = a.currentTarget.dataset.optionid;
                    !1 !== (u = e.weToggleActiveGoods(i, s, l, g, "goods_id")) && (n = u);
                }
            } else if ("list" == c || "detail" == c) var r = a.currentTarget.dataset.cindex, u = e.weToggleActiveGoods1(n, r); else if ("cart" == c) {
                var i = a.currentTarget.dataset.goodsid, l = a.currentTarget.dataset.cid, s = a.currentTarget.dataset.optionid, u = e.weToggleActiveGoods1(i, l, s, "id"), r = e.data.goodsActive.cindex;
                !1 !== u && (n = u);
            } else if ("selectOption" == c) r = e.data.cIndexActive;
            if (!1 !== u) {
                var p = e.data.goodsActive;
                if (i = p.id, s = p.activeOptionId, !p.total) return t.util.toast("库存不足"), !1;
            }
            if (1 == e.data.goodsActive.svip_buy_show && 1 != e.data.cart.is_buysvip && e.data.cart.svip_buy_show >= 1) {
                var v = 0, f = 0;
                return e.data.goodsActive.activeOption ? (v = f = e.data.goodsActive.activeOption.price, 
                e.data.goodsActive.activeOption.svip_price > 0 && (v = e.data.goodsActive.activeOption.svip_price), 
                e.data.goodsActive.activeOption.origin_price > 0 && (f = e.data.goodsActive.activeOption.origin_price)) : (v = e.data.goodsActive.svip_price, 
                f = e.data.goodsActive.origin_price), e.data.buySvipInfo.goods = {
                    svip_price: v,
                    price: f,
                    goods_id: i,
                    option_id: s,
                    goodsIndex: n,
                    cIndex: r || 0
                }, e.data.buySvipInfo.show = !0, void e.setData({
                    buySvipInfo: e.data.buySvipInfo
                });
            }
        }
        var m = {
            sid: e.data.store.id,
            goods_id: i,
            option_id: s,
            num: 1,
            sign: "+",
            is_buysvip: o
        };
        t.util.request({
            url: "wmall/store/goods/cart",
            data: m,
            success: function(a) {
                if ((a = a.data.message).errno) return t.util.toast(a.message), !1;
                a.message.msg && t.util.toast(a.message.msg);
                var o = {
                    cart: a.message.cart
                };
                if (!1 !== u) {
                    var d = e.data.goodsActive;
                    if (d.options_data[m.option_id].num) d.options_data[m.option_id].num++, d.totalnum++; else {
                        var l = a.message.cart.data1[i][s].num;
                        d.options_data[m.option_id].num = l, d.totalnum += l;
                    }
                    if (1 != d.is_options && 1 != d.is_attrs || (d.activeOption.num = d.options_data[m.option_id].num), 
                    1 == e.data.template_page) "detail" == c && (e.data.goodsDetail.totalnum ? e.data.goodsDetail.totalnum++ : e.data.goodsDetail.totalnum = 1), 
                    e.data.goodsAll[e.data.categorySelectedIndex][e.data.childSelectedIndex].data[n] = d, 
                    o = {
                        cart: a.message.cart,
                        goodsAll: e.data.goodsAll,
                        goodsItem: e.data.goodsAll[e.data.categorySelectedIndex][e.data.childSelectedIndex],
                        goodsActive: d,
                        goodsDetail: e.data.goodsDetail
                    }; else {
                        var g = e.data.cateHasGoods;
                        o.goodsActive = d, g[r].goods[n] = d, o.cateHasGoods = g;
                    }
                }
                e.setData(o), e.onCalculate();
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
        if (1 == t.data.template_page) {
            if (a) for (var e in t.data.categoryAll) if (t.data.categoryAll[e].total = 0, t.data.categoryAll[e].child) for (var o in t.data.categoryAll[e].child) {
                t.data.categoryAll[e].child[o].total = 0;
                var d = 0, i = 0;
                for (var s in a) for (var n in a[s]) a[s][n].cid == t.data.categoryAll[e].id && (i += parseInt(a[s][n].num), 
                t.data.categoryAll[e].total = i, a[s][n].child_id == t.data.categoryAll[e].child[o].id && (d += parseInt(a[s][n].num), 
                t.data.categoryAll[e].child[o].total = d));
            } else {
                i = 0;
                for (var s in a) for (var n in a[s]) a[s][n].cid == t.data.categoryAll[e].id && (i += parseInt(a[s][n].num), 
                t.data.categoryAll[e].total = i);
            } else for (var e in t.data.categoryAll) if (t.data.categoryAll[e].total = 0, t.data.categoryAll[e].child) for (var o in t.data.categoryAll[e].child) t.data.categoryAll[e].child[o].total = 0;
            t.setData({
                store: t.data.store,
                categoryAll: t.data.categoryAll,
                categorySelected: t.data.categoryAll[t.data.categorySelectedIndex],
                goodsAll: t.data.goodsAll,
                goodsItem: t.data.goodsItem
            });
        } else {
            var r = t.data.cateHasGoods;
            if (a) for (var c in r) {
                r[c].total = 0;
                i = 0;
                for (var s in a) for (var n in a[s]) a[s][n].cid == r[c].id && (i += a[s][n].num, 
                r[c].total = i);
            } else for (var c in r) r[c].total = 0;
            t.setData({
                store: t.data.store,
                cateHasGoods: r
            });
        }
    },
    onUpdateGoodsNum: function() {
        var t = this, a = t.data.cart.data, e = [];
        if (1 == t.data.template_page) {
            if (a) for (var o in a) for (var d in a[o]) {
                v = a[o][d];
                if (-1 == e.indexOf(v.goods_id) && e.push(v.goods_id.toString()), "88888" != v.goods_id) {
                    if (-1 == (f = t.weTranfterId2Index(v.goods_id, v.cid, v.child_id))) return !1;
                    var i = f.cindex, s = f.gindex, n = f.childindex;
                    if (t.data.goodsAll[i] && t.data.goodsAll[i][n].data[s]) {
                        d == (m = t.data.goodsAll[i][n].data[s]).activeOptionId && m.activeOption && (m.activeOption.num = v.num), 
                        m.options_data[d].num = v.num, m.totalnum = 0;
                        for (var r in m.options_data) a[o].hasOwnProperty(r) || (m.options_data[r].num = 0), 
                        m.options_data[r].num && (m.totalnum += m.options_data[r].num);
                        t.data.goodsAll[i][n].data[s] = m, t.data.goodsItem = t.data.goodsAll[t.data.categorySelectedIndex][t.data.childSelectedIndex];
                    }
                }
            }
            var c = t.data.goodsAll;
            for (var l in c) for (var g in c[l]) if (c[l][g].data) for (var u in c[l][g].data) if (-1 == e.indexOf(c[l][g].data[u].id)) {
                c[l][g].data[u].totalnum = 0;
                for (var p in c[l][g].data[u].options_data) c[l][g].data[u].options_data[p].num = 0;
                c[l][g].data[u].activeOption && (c[l][g].data[u].activeOption.num = 0);
            }
            t.data.goodsAll = c;
        } else {
            if (a) for (var o in a) for (var d in a[o]) {
                var v = a[o][d];
                if (-1 == e.indexOf(v.goods_id) && e.push(v.goods_id.toString()), "88888" != v.goods_id) {
                    var f = t.weTranfterId2Index(v.goods_id, v.cid);
                    if (-1 == f) return !1;
                    i = f.cindex, s = f.gindex;
                    var i = f.cindex, s = f.gindex;
                    if (t.data.cateHasGoods[i] && t.data.cateHasGoods[i].goods[s]) {
                        var m = t.data.cateHasGoods[i].goods[s];
                        d == m.activeOptionId && m.activeOption && (m.activeOption.num = v.num), m.options_data[d].num = v.num, 
                        m.totalnum = 0;
                        for (var r in m.options_data) a[o].hasOwnProperty(r) || (m.options_data[r].num = 0), 
                        m.options_data[r].num && (m.totalnum += m.options_data[r].num);
                        t.data.cateHasGoods[i].goods[s] = m;
                    }
                }
            }
            var h = t.data.cateHasGoods;
            for (var _ in h) if (h[_] && h[_].goods) for (var p in h[_].goods) if (-1 == e.indexOf(h[_].goods[p].id)) {
                h[_].goods[p].totalnum = 0;
                for (var u in h[_].goods[p].options_data) h[_].goods[p].options_data[u].num = 0;
                h[_].goods[p].activeOption && (h[_].goods[p].activeOption.num = 0);
            }
        }
    },
    onSubmit: function() {
        var a = this;
        if (1 == a.data.cart.is_category_limit) return wx.showModal({
            title: "温馨提示",
            content: a.data.cart.category_limit_cn
        }), !1;
        var e = "pages/order/create?sid=" + a.data.store.id + "&is_buysvip=" + a.data.cart.is_buysvip, o = "navigateTo";
        a.data.pindan_id > 0 && (o = "redirectTo", e = "pages/store/pindan?sid=" + a.data.store.id + "&pindan_id=" + a.data.pindan_id), 
        t.util.jump2url(e, o);
    },
    onMinus: function(a) {
        var e = this, o = 0;
        1 == e.data.cart.is_buysvip && (o = 1);
        var d = a.currentTarget.dataset.index, i = a.currentTarget.dataset.from;
        if (1 == e.data.template_page) {
            if ("list" == i || "detail" == i) g = e.weToggleActiveGoods(d); else if ("cart" == i) {
                var s = a.currentTarget.dataset.goodsid, n = a.currentTarget.dataset.cid, r = a.currentTarget.dataset.optionid, c = a.currentTarget.dataset.childid;
                !1 !== (g = e.weToggleActiveGoods(s, r, n, c, "goods_id")) && (d = g);
            }
        } else if ("list" == i || "detail" == i) var l = a.currentTarget.dataset.cindex, g = e.weToggleActiveGoods1(d, l); else if ("cart" == i) {
            var s = a.currentTarget.dataset.goodsid, n = a.currentTarget.dataset.cid, r = a.currentTarget.dataset.optionid, g = e.weToggleActiveGoods1(s, n, r, "id"), l = e.data.goodsActive.cindex;
            !1 !== g && (d = g);
        } else if ("selectOption" == i) l = e.data.cIndexActive;
        if (!1 !== g) {
            var u = e.data.goodsActive;
            s = u.id, r = u.activeOptionId;
        }
        var p = {
            sid: e.data.store.id,
            goods_id: s,
            option_id: r,
            num: 1,
            sign: "-",
            is_buysvip: o
        };
        t.util.request({
            url: "wmall/store/goods/cart",
            data: p,
            success: function(a) {
                if ((a = a.data.message).errno) return t.util.toast(a.message), !1;
                var o = {
                    cart: a.message.cart
                };
                if (!1 !== g) {
                    var n = e.data.goodsActive;
                    if (a.message.cart.data1[s]) if (a.message.cart.data1[s][r]) n.totalnum--, n.options_data[p.option_id].num--; else {
                        var c = n.options_data[p.option_id].num;
                        n.options_data[p.option_id].num = 0, n.totalnum -= c;
                    } else n.totalnum = 0, n.options_data[p.option_id].num = 0;
                    if (1 != n.is_options && 1 != n.is_attrs || (n.activeOption.num = n.options_data[p.option_id].num), 
                    1 == e.data.template_page) "detail" == i && (e.data.goodsDetail.totalnum ? e.data.goodsDetail.totalnum-- : e.data.goodsDetail.totalnum = 0), 
                    e.data.goodsAll[e.data.categorySelectedIndex][e.data.childSelectedIndex].data[d] = n, 
                    o = {
                        cart: a.message.cart,
                        goodsAll: e.data.goodsAll,
                        goodsItem: e.data.goodsAll[e.data.categorySelectedIndex][e.data.childSelectedIndex],
                        goodsActive: n,
                        goodsDetail: e.data.goodsDetail
                    }; else {
                        var u = e.data.cateHasGoods;
                        o.goodsActive = n, u[l].goods[d] = n, o.cateHasGoods = u;
                    }
                }
                a.message.cart.num || (o.showCartDetail = !1), e.setData(o), e.onCalculate();
            }
        });
    },
    onReachBottom: function() {
        if (!this.data.template_page) return !1;
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
        if (1 == a.data.goodsLoading) return !1;
        a.data.goodsAll[a.data.categorySelectedIndex] || (a.data.goodsAll[a.data.categorySelectedIndex] = []);
        var e = a.data.goodsAll[a.data.categorySelectedIndex][a.data.childSelectedIndex];
        if (e) {
            if (a.setData({
                goodsItem: e
            }), e.empty) return !1;
            if (e.loaded) return !1;
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
            url: "wmall/store/goods/goods",
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
                    url: "wmall/store/goods/truncate",
                    data: {
                        sid: a.data.store.id
                    },
                    success: function() {
                        if (a.data.goodsActive) {
                            a.data.goodsActive.totalnum = 0;
                            for (var t in a.data.goodsActive.options_data) a.data.goodsActive.options_data[t].num = 0;
                        }
                        if (1 == a.data.template_page) {
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
                            });
                        } else {
                            var i = a.data.cateHasGoods;
                            for (var t in i) for (var o in i[t].goods) {
                                i[t].goods[o].totalnum = 0;
                                for (var s in i[t].goods[o].options_data) i[t].goods[o].options_data[s].num = 0;
                            }
                            a.setData({
                                cateHasGoods: i,
                                cart: {},
                                showCartDetail: !1
                            });
                        }
                        a.onCalculate();
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
        if (1 == a.data.template_page) d = a.weToggleActiveGoods(e); else var o = t.currentTarget.dataset.cindex, d = a.weToggleActiveGoods1(e, o);
        if (!1 !== d) {
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
    onToggleDiscount: function(t) {
        var a = t.currentTarget.dataset;
        this.data.recommend_stores[a.index].activity.is_show_all = !this.data.recommend_stores[a.index].activity.is_show_all, 
        this.setData({
            recommend_stores: this.data.recommend_stores
        });
    },
    onCloseSvip: function() {
        this.setData({
            "buySvipInfo.show": !1
        });
    }
});