var t = getApp();

Page({
    data: {
        theme: t.util.getStorageSync("theme"),
        is_use_diy: 1,
        address: "定位中。。。",
        showNodata: !1,
        config: {
            version: 2
        },
        superRedpacket: {
            is_show: !1
        },
        topSearchBar: !1,
        shareData: {
            title: "",
            path: "/pages/home/index",
            success: function() {},
            fail: function() {}
        },
        store: {
            page: 1,
            psize: 20,
            loaded: 0,
            empty: 0,
            data: [],
            loading: 0,
            filter: {
                cid: 0,
                child_id: 0,
                categorySelectedId: 0
            }
        },
        storeExtra: {
            condition: {
                order: "",
                mode: "",
                dis: ""
            },
            filter_title: "综合排序",
            multiple: !1,
            filter: !1
        },
        danmu: 1,
        dialog: {
            dialogGuide: !0
        },
        diy: {
            data: {}
        },
        has_diy_guide: !1,
        selectedtab: "coupon",
        is_grant: 0,
        Lang: t.Lang,
        wuiLoading: {
            show: !0,
            img: t.util.getStorageSync("theme.loading.img")
        },
        goodsTabActive: 0,
        goodsTabTop: 0,
        goodsTabItemTop: [],
        goodsTabSticky: !1,
        goodsTabStickyTop: 0,
        storesTabActive: 0,
        storesTabTop: 0,
        storesTabItemTop: [],
        storesTabSticky: !1,
        storesTabStickyTop: 0
    },
    onToggleGoodsTab: function(t) {
        var a = this, e = t.detail.index, o = Math.ceil(a.data.goodsTabItemTop[e] - 44);
        a.data.topSearchBar && 1 == a.data.diy.is_has_location && (o -= 44), wx.pageScrollTo({
            scrollTop: o,
            duration: 0,
            success: function() {}
        });
    },
    onCalculateGoodsTabItemHeight: function() {
        var t = this, a = t.createSelectorQuery(), e = 0, o = [];
        a.select(".diy-waimai-goods-tab").boundingClientRect(function(t) {
            e = t.top;
        }), a.selectAll(".goods-tab-item").boundingClientRect(function(t) {
            t.forEach(function(t) {
                o.push(t.top);
            });
        }).exec(function() {
            t.setData({
                goodsTabTop: e,
                goodsTabItemTop: o
            });
        });
    },
    onCalCulateGoodsTabData: function(t) {
        var a = this, e = a.data.goodsTabItemTop, o = e.length, s = a.data.goodsTabActive;
        a.data.goodsTabSticky && (t += 44);
        var i = 0;
        a.data.topSearchBar && 1 == a.data.diy.is_has_location && (t += 44, i = 44);
        for (var r = 0; r < o; r++) {
            if (!e[r + 1]) {
                s = r;
                break;
            }
            if (t < e[r]) {
                s = r;
                break;
            }
            if (t >= e[r] && t < e[r + 1]) {
                s = r;
                break;
            }
        }
        var d = !1;
        t >= a.data.goodsTabTop && t > 44 && (d = !0), d == a.data.goodsTabSticky && i == a.data.goodsTabStickyTop || a.setData({
            goodsTabSticky: d,
            goodsTabStickyTop: i
        }), s != a.data.goodsTabActive && a.setData({
            goodsTabActive: s
        });
    },
    onToggleStoresTab: function(t) {
        var a = this, e = t.detail.index, o = Math.ceil(a.data.storesTabItemTop[e] - 44);
        a.data.topSearchBar && 1 == a.data.diy.is_has_location && (o -= 44), wx.pageScrollTo({
            scrollTop: o,
            duration: 0,
            success: function() {}
        });
    },
    onCalculateStoresTabItemHeight: function() {
        var t = this, a = t.createSelectorQuery(), e = 0, o = [];
        a.select(".diy-waimai-stores-tab").boundingClientRect(function(t) {
            e = t.top;
        }), a.selectAll(".stores-tab-list").boundingClientRect(function(t) {
            t.forEach(function(t) {
                o.push(t.top);
            });
        }).exec(function() {
            t.setData({
                storesTabTop: e,
                storesTabItemTop: o
            });
        });
    },
    onCalCulateStoresTabData: function(t) {
        var a = this, e = a.data.storesTabItemTop, o = e.length, s = a.data.storesTabActive;
        a.data.storesTabSticky && (t += 44);
        var i = 0;
        a.data.topSearchBar && 1 == a.data.diy.is_has_location && (t += 44, i = 44);
        for (var r = 0; r < o; r++) {
            if (!e[r + 1]) {
                s = r;
                break;
            }
            if (t < e[r]) {
                s = r;
                break;
            }
            if (t >= e[r] && t < e[r + 1]) {
                s = r;
                break;
            }
        }
        var d = !1;
        t >= a.data.storesTabTop && t > 44 && (d = !0), d == a.data.storesTabSticky && i == a.data.storesTabStickyTop || a.setData({
            storesTabSticky: d,
            storesTabStickyTop: i
        }), s != a.data.storesTabActive && a.setData({
            storesTabActive: s
        });
    },
    onToggleStoresTabDiscount: function(t) {
        var a = t.currentTarget.dataset;
        this.data.diy.data.items[a.diyindex].data[a.listindex].stores[a.itemindex].activity.is_show_all = !this.data.diy.data.items[a.diyindex].data[a.listindex].stores[a.itemindex].activity.is_show_all, 
        this.setData({
            "diy.data.items": this.data.diy.data.items
        });
    },
    onPageScroll: function(t) {
        var a = this, e = t.scrollTop;
        e > 200 ? a.data.topSearchBar || a.setData({
            topSearchBar: !0
        }) : a.data.topSearchBar && a.setData({
            topSearchBar: !1
        }), 1 == a.data.diy.is_has_goodsTab && a.onCalCulateGoodsTabData(e), 1 == a.data.diy.is_has_storesTab && a.onCalCulateStoresTabData(e);
    },
    onChangeCategory: function(t) {
        var a = this, e = t.currentTarget.dataset.id, o = t.currentTarget.dataset.parentid;
        if (1 != a.data.diy.is_has_allstore || !e || e == a.data.store.filter.categorySelectedId) return !1;
        a.data.store.filter.child_id = o > 0 ? e : 0, a.data.store.filter.categorySelectedId = e, 
        a.setData({
            "store.filter": a.data.store.filter
        }), a.onGetStore(!0);
    },
    onToggleDiscount: function(t) {
        var a = t.currentTarget.dataset;
        "waimai_stores" == a.type ? (this.data.diy.data.items[a.diyindex].data[a.index].activity.is_show_all = !this.data.diy.data.items[a.diyindex].data[a.index].activity.is_show_all, 
        this.setData({
            "diy.data.items": this.data.diy.data.items
        })) : (this.data.store.data[a.index].activity.is_show_all = !this.data.store.data[a.index].activity.is_show_all, 
        this.setData({
            "store.data": this.data.store.data
        }));
    },
    onJsEvent: function(a) {
        t.util.jsEvent(a);
    },
    onGetIndex: function() {
        var a = this, e = t.util.getStorageSync("location");
        if (e) {
            var o = {
                lat: e.x,
                lng: e.y,
                forceOauth: 1,
                menufooter: 1
            }, s = a.data.options;
            if (s && s.scene) {
                var i = decodeURIComponent(s.scene);
                i = i.split(":"), o.code = i[1];
            }
            a.setData({
                "store.loading": 1
            }), t.util.request({
                url: "wmall/home/index/index",
                data: o,
                success: function(e) {
                    t.util.loaded();
                    var s = e.data.message;
                    if (s.errno) return t.util.toast(s.message), !1;
                    if (2 != (s = s.message).config.version) {
                        1 == s.diy.is_has_allstore && (a.data.store.filter.cid = s.diy.cid, a.data.store.filter.categorySelectedId = s.diy.cid), 
                        a.data.store.loading = 0, s.stores.stores = t.util.getStore(s.stores.stores, "array"), 
                        a.data.store.data = s.stores.stores, s.stores.pagetotal <= a.data.store.page && (a.data.store.loaded = 1, 
                        a.data.store.data.length || (a.data.store.empty = 1)), a.data.store.page++, s.store = a.data.store, 
                        s.superRedpacket = {};
                        var i = s.superRedpacketData;
                        if (i && 0 == i.errno && i.message.page && (s.superRedpacket = {
                            is_show: !0,
                            type: i.message.type || "",
                            page: i.message.page,
                            redpackets: i.message.redpackets
                        }), 1 == s.is_use_diy) {
                            var r = s.diy.data.items;
                            for (var d in r) "richtext" == r[d].id && t.WxParse.wxParse("richtext." + d, "html", r[d].params.content, a, 5), 
                            "copyright" == r[d].id && 1 == r[d].params.datafrom && t.WxParse.wxParse("richtext." + d, "html", r[d].params.config, a, 5);
                            a.data.richtext && (s.richtext = a.data.richtext), a.data.shareData.title = s.config.title, 
                            wx.setNavigationBarColor({
                                frontColor: s.diy.data.page.navigationtextcolor,
                                backgroundColor: s.diy.data.page.navigationbackground,
                                complete: function(t) {
                                    console.log("resresresresresresresres"), console.log(t);
                                }
                            }), wx.setNavigationBarTitle({
                                title: s.diy.data.page.title
                            }), setInterval(function() {
                                a.setData({
                                    danmu: !a.data.danmu
                                });
                            }, 2500);
                            var n = s.diy.guide;
                            n && n.params && 1 == n.params.status && (a.setData({
                                has_diy_guide: !0
                            }), n && "everytime" == n.params.show_setting && t.util.getStorageSync("storage") && t.util.setStorageSync("storage", {}, 0), 
                            s.storage = t.util.getStorageSync("storage"), !n || "interval" != n.params.show_setting || s.storage && s.storage.storageGuide || t.util.setStorageSync("storage", {
                                storageGuide: 1
                            }, 60 * n.params.interval_time));
                        }
                        if (!a.data.has_diy_guide) {
                            var l = s.guide;
                            l && "everytime" == l.params.show_setting && t.util.getStorageSync("storage") && t.util.setStorageSync("storage", {}, 0), 
                            s.storage = t.util.getStorageSync("storage"), !l || "interval" != l.params.show_setting || s.storage && s.storage.storageGuide || t.util.setStorageSync("storage", {
                                storageGuide: 1
                            }, 60 * l.params.interval_time);
                        }
                        if (a.setData(s), o.code && s.spread) if (0 == s.spread.errno) {
                            var c = s.spread.message.nickname + "向您推荐了" + a.data.config.title + ",快去下单吧!";
                            t.util.toast(c);
                        } else -1 == s.spread.errno && t.util.toast(s.spread.message);
                        1 == a.data.diy.is_has_goodsTab && a.onCalculateGoodsTabItemHeight(), 1 == a.data.diy.is_has_storesTab && a.onCalculateStoresTabItemHeight();
                    } else {
                        if ("home" == s.config.store_url) {
                            a.data.diy.data = s.homepage;
                            var g = s.homepage.items;
                            for (var d in g) "richtext" == g[d].id && t.WxParse.wxParse("richtext." + d, "html", g[d].params.content, a, 5);
                            return a.setData({
                                diy: a.data.diy
                            }), wx.setNavigationBarTitle({
                                title: a.data.diy.data.page.title
                            }), void wx.setNavigationBarColor({
                                frontColor: a.data.diy.data.page.navigationtextcolor,
                                backgroundColor: a.data.diy.data.page.navigationbackground
                            });
                        }
                        wx.redirectTo({
                            url: "../store/goods?sid=" + s.config.default_sid
                        });
                    }
                }
            });
        } else t.util.toast("获取位置失败,请重新进入小程序");
    },
    onGetStore: function(a) {
        var e = this;
        if (1 == e.data.is_use_diy && 0 == e.data.diy.is_has_allstore) return !1;
        if (a && (e.data.store = {
            page: 1,
            psize: 20,
            loaded: 0,
            empty: 0,
            data: [],
            loading: 0,
            filter: e.data.store.filter
        }), 1 == e.data.store.loaded) return !1;
        if (1 == e.data.store.loading) return !1;
        e.setData({
            "store.loading": 1
        });
        var o = t.util.getStorageSync("location"), s = {
            lat: o.x,
            lng: o.y,
            condition: JSON.stringify(e.data.storeExtra.condition),
            page: e.data.store.page,
            psize: e.data.store.psize,
            cid: e.data.store.filter.cid,
            child_id: e.data.store.filter.child_id,
            forceLocation: 1
        };
        t.util.request({
            url: "wmall/home/index/store",
            data: s,
            success: function(a) {
                e.data.store.loading = 0;
                var o = a.data.message.message;
                o.stores = t.util.getStore(o.stores, "array"), e.data.store.data = e.data.store.data.concat(o.stores), 
                o.pagetotal <= e.data.store.page && (e.data.store.loaded = 1, e.data.store.data.length || (e.data.store.empty = 1)), 
                e.data.store.page++, e.data.store.loaded || o.stores.length ? e.setData({
                    store: e.data.store
                }) : e.onGetStore();
            }
        });
    },
    onLoad: function(a) {
        var e = this;
        if (a && (e.data.options = a), t.util.setStorageSync("onloadHome", 1), a && a.sid > 0) {
            var o = e.data.options.sid, s = "/pages/store/goods?sid=" + o;
            return "home" == e.data.options.from && (s = "/pages/store/home?sid=" + o), void t.util.jump2url(s);
        }
        var i = t.util.getStorageSync("location");
        i && i.x ? (e.setData({
            location: i
        }), e.onGetIndex()) : t.util.getLocation(function(a) {
            var o = a.data.message.message;
            e.setData({
                location: o
            }), o = {
                address: o.address,
                x: o.latitude,
                y: o.longitude
            }, t.util.setStorageSync("location", o, 600), e.onGetIndex();
        });
    },
    onReachBottom: function() {
        this.onGetStore();
    },
    onHide: function() {
        t.util.removeStorageSync("onloadHome");
    },
    onShow: function() {
        var a = this;
        if (1 == t.util.getStorageSync("location").onshow || a.data.options && a.data.options.sid > 0) return a.data.store = {
            page: 1,
            psize: 20,
            loaded: 0,
            empty: 0,
            data: [],
            loading: 0,
            filter: a.data.store.filter
        }, a.onLoad(), !1;
        1 != t.util.getStorageSync("onloadHome") && t.util.request({
            url: "wmall/home/index/cart",
            showLoading: !1,
            success: function(t) {
                var e = t.data.message.message.cart_sum;
                a.setData({
                    cart_sum: e
                });
            }
        });
    },
    onPullDownRefresh: function() {
        var t = this;
        t.data.store = {
            page: 1,
            psize: 20,
            loaded: 0,
            empty: 0,
            data: [],
            loading: 0,
            filter: t.data.store.filter
        }, t.onLoad(), wx.stopPullDownRefresh();
    },
    onMultiple: function() {
        this.setData({
            "storeExtra.multiple": !this.data.storeExtra.multiple,
            topSearchBar: !this.data.topSearchBar
        });
    },
    onFilter: function() {
        this.setData({
            "storeExtra.filter": !this.data.storeExtra.filter,
            topSearchBar: !this.data.topSearchBar
        });
    },
    onOrderby: function(t) {
        var a = this, e = t.currentTarget.dataset.type;
        if ("order" == e) {
            var o = t.currentTarget.dataset.order;
            "svipRedpacket" == o ? a.setData({
                "storeExtra.condition.dis": o
            }) : (a.setData({
                "storeExtra.condition.order": o,
                "storeExtra.multiple": !1,
                showNodata: !1
            }), "sailed" != o && "distance" != o ? a.setData({
                "storeExtra.filter_title": t.currentTarget.dataset.title
            }) : a.setData({
                "storeExtra.filter_title": "综合排序"
            }));
        } else if ("discounts" == e) {
            var s = t.currentTarget.dataset.dis;
            if (a.data.storeExtra.condition.dis == s && (s = ""), a.setData({
                "storeExtra.condition.dis": s
            }), "refresh" != t.currentTarget.dataset.title) return !1;
        } else {
            if ("mode" == e) {
                var i = t.currentTarget.dataset.mode;
                return a.data.storeExtra.condition.mode == i && (i = ""), a.setData({
                    "storeExtra.condition.mode": i
                }), !1;
            }
            "clear" == e ? a.setData({
                "storeExtra.condition.dis": "",
                "storeExtra.condition.order": "",
                "storeExtra.condition.mode": "",
                "storeExtra.filter": !1,
                "storeExtra.filter_title": "综合排序"
            }) : "finish" == e && a.setData({
                "storeExtra.filter": !1,
                showNodata: !1
            });
        }
        a.onGetStore(!0);
    },
    onCloseRedpacket: function() {
        this.setData({
            "superRedpacket.is_show": !1
        });
    },
    getCoupon: function(a) {
        var e = this;
        t.util.request({
            url: "wmall/channel/coupon/get",
            data: {
                sid: a.target.dataset.sid
            },
            success: function(a) {
                0 == a.data.message.errno ? (t.util.toast(a.data.message.message), e.setData({
                    is_grant: 1
                })) : t.util.toast("领取失败");
            }
        });
    },
    onScrollTo: function(t) {
        var a = t.currentTarget.dataset.scrollid;
        this.setData({
            scrollToId: a,
            selectedtab: a
        });
    },
    onShareAppMessage: function() {
        return this.data.shareData;
    }
});