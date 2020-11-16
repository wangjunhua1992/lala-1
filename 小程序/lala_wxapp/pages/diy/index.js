var a = getApp();

Page({
    data: {
        store: {
            page: 1,
            psize: 20,
            loaded: 0,
            empty: 0,
            data: [],
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
            filter: !1,
            activityShowAll: !1
        },
        tongcheng: {
            page: 2,
            psize: 10,
            loaded: 0,
            empty: 0,
            data: [],
            loading: 1
        },
        haodian: {
            page: 2,
            psize: 10,
            loaded: 0,
            empty: 0,
            data: [],
            loading: 1
        },
        haodianExtra: {
            orderby: "distance",
            haodian_cid: 0,
            haodian_child_id: 0,
            cIndexActive: 0
        },
        popup: {
            haodianSearch: !1
        },
        dialog: {
            dialogGuide: !0
        },
        danmu: 1,
        showSearchSign: !1,
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
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
    onToggleGoodsTab: function(a) {
        var t = this, e = a.detail.index, o = Math.ceil(t.data.goodsTabItemTop[e] - 44);
        t.data.topSearchBar && 1 == t.data.diy.is_has_location && (o -= 44), wx.pageScrollTo({
            scrollTop: o,
            duration: 0,
            success: function() {}
        });
    },
    onCalculateGoodsTabItemHeight: function() {
        var a = this, t = a.createSelectorQuery(), e = 0, o = [];
        t.select(".diy-waimai-goods-tab").boundingClientRect(function(a) {
            e = a.top;
        }), t.selectAll(".goods-tab-item").boundingClientRect(function(a) {
            a.forEach(function(a) {
                o.push(a.top);
            });
        }).exec(function() {
            a.setData({
                goodsTabTop: e,
                goodsTabItemTop: o
            });
        });
    },
    onCalCulateGoodsTabData: function(a) {
        var t = this, e = t.data.goodsTabItemTop, o = e.length, i = t.data.goodsTabActive;
        t.data.goodsTabSticky && (a += 44);
        var d = 0;
        t.data.topSearchBar && 1 == t.data.diy.is_has_location && (a += 44, d = 44);
        for (var s = 0; s < o; s++) {
            if (!e[s + 1]) {
                i = s;
                break;
            }
            if (a < e[s]) {
                i = s;
                break;
            }
            if (a >= e[s] && a < e[s + 1]) {
                i = s;
                break;
            }
        }
        var n = !1;
        a >= t.data.goodsTabTop && a > 44 && (n = !0), n == t.data.goodsTabSticky && d == t.data.goodsTabStickyTop || t.setData({
            goodsTabSticky: n,
            goodsTabStickyTop: d
        }), i != t.data.goodsTabActive && t.setData({
            goodsTabActive: i
        });
    },
    onToggleStoresTab: function(a) {
        var t = this, e = a.detail.index, o = Math.ceil(t.data.storesTabItemTop[e] - 44);
        t.data.topSearchBar && 1 == t.data.diy.is_has_location && (o -= 44), wx.pageScrollTo({
            scrollTop: o,
            duration: 0,
            success: function() {}
        });
    },
    onCalculateStoresTabItemHeight: function() {
        var a = this, t = a.createSelectorQuery(), e = 0, o = [];
        t.select(".diy-waimai-stores-tab").boundingClientRect(function(a) {
            e = a.top;
        }), t.selectAll(".stores-tab-list").boundingClientRect(function(a) {
            a.forEach(function(a) {
                o.push(a.top);
            });
        }).exec(function() {
            a.setData({
                storesTabTop: e,
                storesTabItemTop: o
            });
        });
    },
    onCalCulateStoresTabData: function(a) {
        var t = this, e = t.data.storesTabItemTop, o = e.length, i = t.data.storesTabActive;
        t.data.storesTabSticky && (a += 44);
        var d = 0;
        t.data.topSearchBar && 1 == t.data.diy.is_has_location && (a += 44, d = 44);
        for (var s = 0; s < o; s++) {
            if (!e[s + 1]) {
                i = s;
                break;
            }
            if (a < e[s]) {
                i = s;
                break;
            }
            if (a >= e[s] && a < e[s + 1]) {
                i = s;
                break;
            }
        }
        var n = !1;
        a >= t.data.storesTabTop && a > 44 && (n = !0), n == t.data.storesTabSticky && d == t.data.storesTabStickyTop || t.setData({
            storesTabSticky: n,
            storesTabStickyTop: d
        }), i != t.data.storesTabActive && t.setData({
            storesTabActive: i
        });
    },
    onToggleStoresTabDiscount: function(a) {
        var t = a.currentTarget.dataset;
        this.data.diy.data.items[t.diyindex].data[t.listindex].stores[t.itemindex].activity.is_show_all = !this.data.diy.data.items[t.diyindex].data[t.listindex].stores[t.itemindex].activity.is_show_all, 
        this.setData({
            "diy.data.items": this.data.diy.data.items
        });
    },
    onPageScroll: function(a) {
        var t = this, e = a.scrollTop;
        e > 200 ? t.data.topSearchBar || t.setData({
            topSearchBar: !0
        }) : t.data.topSearchBar && t.setData({
            topSearchBar: !1
        }), t.onCalCulateGoodsTabData(e), t.onCalCulateStoresTabData(e);
    },
    onLoad: function(t) {
        var e = this;
        t && (e.data.options = t);
        var o = a.util.getStorageSync("location");
        o && o.x ? (e.setData({
            location: o
        }), e.onGetIndex()) : a.util.getLocation(function(t) {
            var o = t.data.message.message;
            e.setData({
                location: o
            }), o = {
                address: o.address,
                x: o.latitude,
                y: o.longitude
            }, a.util.setStorageSync("location", o, 600), e.onGetIndex();
        });
    },
    onGetIndex: function() {
        var t = this, e = a.util.getStorageSync("location");
        if (e) {
            var o = t.data.options;
            if (o && o.scene) {
                var i = decodeURIComponent(o.scene);
                i = i.split(":"), o.id = i[1];
            }
            a.util.request({
                url: "diypage/diy",
                data: {
                    id: o.id || 2,
                    lat: e.x,
                    lng: e.y
                },
                success: function(e) {
                    a.util.loaded();
                    var o = e.data.message;
                    if (o.errno) return a.util.toast(o.message), !1;
                    if (o.message.config_wxapp && o.message.config_wxapp.basic && 1 == o.message.config_wxapp.basic.audit_status) return wx.redirectTo({
                        url: "../store/goods?sid=" + o.message.config_wxapp.basic.default_sid
                    }), !1;
                    var i = o.message.diy.data.items;
                    for (var d in i) "richtext" == i[d].id && a.WxParse.wxParse("richtext." + d, "html", i[d].params.content, t, 5), 
                    "copyright" == i[d].id && 1 == i[d].params.datafrom && a.WxParse.wxParse("richtext." + d, "html", i[d].params.config, t, 5);
                    t.data.richtext && (o.message.richtext = t.data.richtext), setInterval(function() {
                        t.setData({
                            danmu: !t.data.danmu
                        });
                    }, 2500);
                    var s = o.message.diy.guide;
                    s && s.params && 1 == s.params.status && (t.setData({
                        has_diy_guide: !0
                    }), s && "everytime" == s.params.show_setting && a.util.getStorageSync("storage") && a.util.setStorageSync("storage", {}, 0), 
                    o.message.storage = a.util.getStorageSync("storage"), !s || "interval" != s.params.show_setting || o.message.storage && o.message.storage.storageGuide || a.util.setStorageSync("storage", {
                        storageGuide: 1
                    }, 60 * s.params.interval_time)), o.message.diy.tongcheng && o.message.diy.tongcheng.informationdata && (t.data.tongcheng.data = o.message.diy.tongcheng.informationdata, 
                    t.data.tongcheng.loading = 0, o.message.diy.tongcheng.has_get_all && (t.data.tongcheng.loaded = 1), 
                    0 == t.data.tongcheng.data.length && (t.data.tongcheng.empty = 1), delete o.message.diy.tongcheng, 
                    o.message.diy.tongcheng = t.data.tongcheng), o.message.diy.haodian && (o.message.diy.haodianCategory = o.message.diy.haodian.category, 
                    t.setData({
                        "haodianExtra.haodian_child_id": o.message.diy.haodian.haodian_child_id
                    }), t.data.haodian.data = o.message.diy.haodian.store, t.data.haodian.loading = 0, 
                    o.message.diy.haodian.has_get_all && (t.data.haodian.loaded = 1), 0 == t.data.haodian.data.length && (t.data.haodian.empty = 1), 
                    delete o.message.diy.haodian, o.message.diy.haodian = t.data.haodian), o.message.superRedpacket = {};
                    var n = o.message.superRedpacketData;
                    n && 0 == n.errno && n.message.page && (o.message.superRedpacket = {
                        is_show: !0,
                        type: n.message.type || "",
                        page: n.message.page,
                        redpackets: n.message.redpackets
                    }), 1 == o.message.diy.is_has_allstore && (t.data.store.filter.cid = o.message.diy.cid, 
                    t.data.store.filter.categorySelectedId = o.message.diy.cid, t.data.store.loading = 0, 
                    o.message.stores.stores = a.util.getStore(o.message.stores.stores, "array"), t.data.store.data = o.message.stores.stores, 
                    o.message.stores.pagetotal <= t.data.store.page && (t.data.store.loaded = 1, t.data.store.data.length || (t.data.store.empty = 1)), 
                    t.data.store.page++, o.message.store = t.data.store), t.setData(o.message), wx.setNavigationBarTitle({
                        title: t.data.diy.data.page.title
                    }), wx.setNavigationBarColor({
                        frontColor: t.data.diy.data.page.navigationtextcolor,
                        backgroundColor: t.data.diy.data.page.navigationbackground
                    }), 1 == t.data.diy.is_has_goodsTab && t.onCalculateGoodsTabItemHeight(), 1 == t.data.diy.is_has_storesTab && t.onCalculateStoresTabItemHeight();
                }
            });
        } else a.util.toast("获取位置失败,请重新进入小程序");
    },
    onChangeCategory: function(a) {
        var t = this, e = a.currentTarget.dataset.id, o = a.currentTarget.dataset.parentid;
        if (1 != t.data.diy.is_has_allstore || !e || e == t.data.store.filter.categorySelectedId) return !1;
        t.data.store.filter.child_id = o > 0 ? e : 0, t.data.store.filter.categorySelectedId = e, 
        t.setData({
            "store.filter": t.data.store.filter
        }), t.onGetStore(!0);
    },
    onJsEvent: function(t) {
        a.util.jsEvent(t);
    },
    onGetStore: function(t) {
        var e = this;
        if (0 == e.data.diy.is_has_allstore) return !1;
        if (t && (e.data.store = {
            page: 1,
            psize: 20,
            loaded: 0,
            empty: 0,
            data: [],
            filter: e.data.store.filter
        }), 1 == e.data.store.loaded) return !1;
        var o = a.util.getStorageSync("location"), i = {
            lat: o.x,
            lng: o.y,
            condition: JSON.stringify(e.data.storeExtra.condition),
            page: e.data.store.page,
            psize: e.data.store.psize,
            cid: e.data.store.filter.cid,
            child_id: e.data.store.filter.child_id,
            forceLocation: 1
        };
        a.util.request({
            url: "wmall/home/index/store",
            data: i,
            success: function(a) {
                var t = a.data.message.message;
                e.data.store.data = e.data.store.data.concat(t.stores), t.pagetotal <= e.data.store.page && (e.data.store.loaded = 1, 
                e.data.store.data.length || (e.data.store.empty = 1)), e.data.store.page++, e.setData({
                    store: e.data.store
                });
            }
        });
    },
    onToggleDiscount: function(a) {
        var t = a.currentTarget.dataset;
        "waimai_stores" == t.type ? (this.data.diy.data.items[t.diyindex].data[t.index].activity.is_show_all = !this.data.diy.data.items[t.diyindex].data[t.index].activity.is_show_all, 
        this.setData({
            "diy.data.items": this.data.diy.data.items
        })) : (this.data.store.data[t.index].activity.is_show_all = !this.data.store.data[t.index].activity.is_show_all, 
        this.setData({
            "store.data": this.data.store.data
        }));
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
    onOrderby: function(a) {
        var t = this, e = a.currentTarget.dataset.type;
        if ("order" == e) {
            var o = a.currentTarget.dataset.order;
            "svipRedpacket" == o ? t.setData({
                "storeExtra.condition.dis": o
            }) : (t.setData({
                "storeExtra.condition.order": o,
                "storeExtra.multiple": !1,
                showNodata: !1
            }), "sailed" != o && "distance" != o ? t.setData({
                "storeExtra.filter_title": a.currentTarget.dataset.title
            }) : t.setData({
                "storeExtra.filter_title": "综合排序"
            }));
        } else if ("discounts" == e) {
            var i = a.currentTarget.dataset.dis;
            if (t.data.storeExtra.condition.dis == i && (i = ""), t.setData({
                "storeExtra.condition.dis": i
            }), "refresh" != a.currentTarget.dataset.title) return !1;
        } else {
            if ("mode" == e) {
                var d = a.currentTarget.dataset.mode;
                return t.data.storeExtra.condition.mode == d && (d = ""), t.setData({
                    "storeExtra.condition.mode": d
                }), !1;
            }
            "clear" == e ? t.setData({
                "storeExtra.condition.dis": "",
                "storeExtra.condition.order": "",
                "storeExtra.condition.mode": "",
                "storeExtra.filter": !1
            }) : "finish" == e && t.setData({
                "storeExtra.filter": !1,
                showNodata: !1
            });
        }
        t.onGetStore(!0);
    },
    onCloseRedpacket: function() {
        this.setData({
            superRedpacket: !1
        });
    },
    onGetInformation: function() {
        var t = this;
        t.data.tongcheng.loaded || a.util.request({
            url: "gohome/home/information",
            data: {
                page: t.data.tongcheng.page,
                psize: t.data.tongcheng.psize
            },
            success: function(e) {
                var o = e.data.message;
                if (o.errno) return a.util.toast(o.message), !1;
                o = o.message, t.data.tongcheng.data = t.data.tongcheng.data.concat(o.informations), 
                o.informations.length < t.data.tongcheng.psize && (t.data.tongcheng.loaded = !0, 
                t.data.tongcheng.data.length || (t.data.tongcheng.empty = !0)), t.data.tongcheng.page++, 
                t.setData({
                    "diy.tongcheng": t.data.tongcheng
                });
            }
        });
    },
    onImgPreview: function(a) {
        var t = a.currentTarget.dataset.current, e = a.currentTarget.dataset.urls;
        wx.previewImage({
            current: t,
            urls: e
        });
    },
    onToggleInformationHeight: function(a) {
        var t = this, e = a.currentTarget.dataset.index;
        t.data.diy.tongcheng.data[e].showall = !t.data.diy.tongcheng.data[e].showall, t.setData({
            "diy.tongcheng.data": t.data.diy.tongcheng.data
        });
    },
    onGetHaoDian: function(t) {
        var e = this;
        t && (e.data.haodian = {
            page: 1,
            psize: 10,
            loaded: 0,
            empty: 0,
            data: [],
            loading: 1
        }), e.data.haodian.loaded || a.util.request({
            url: "haodian/index/store",
            data: {
                page: e.data.haodian.page,
                psize: e.data.haodian.psize,
                orderby: e.data.haodianExtra.orderby,
                haodian_cid: e.data.haodianExtra.haodian_cid,
                haodian_child_id: e.data.haodianExtra.haodian_child_id
            },
            success: function(t) {
                var o = t.data.message;
                if (o.errno) return a.util.toast(o.message), !1;
                o = o.message, e.data.haodian.data = e.data.haodian.data.concat(o.store), o.store.length < e.data.haodian.psize && (e.data.haodian.loaded = !0, 
                e.data.haodian.data.length || (e.data.haodian.empty = !0)), e.data.haodian.page++, 
                e.setData({
                    "diy.haodian": e.data.haodian
                });
            }
        });
    },
    onChangeHaodianExtra: function(a) {
        var t = this, e = a.currentTarget.dataset.type;
        if ("filter" == e) t.setData({
            "popup.haodianSearch": !t.data.popup.haodianSearch
        }); else {
            if (e == t.data.haodianExtra.orderby) return;
            t.data.haodianExtra.orderby = e, t.data.haodianExtra.haodian_cid = 0, t.data.diy.haodianCategory && t.data.diy.haodianCategory.length > 0 && t.data.diy.haodianCategory[0].children.length > 0 && (t.data.haodianExtra.haodian_child_id = t.data.diy.haodianCategory[0].children[0].id), 
            t.data.haodianExtra.cIndexActive = 0, t.setData({
                haodianExtra: t.data.haodianExtra,
                showSearchSign: !1
            }), t.onGetHaoDian(!0);
        }
    },
    onClickHaodianParentCategory: function(a) {
        var t = this, e = a.detail.index;
        t.data.haodianExtra.cIndexActive = e, t.data.diy.haodianCategory.hasOwnProperty(e) && (t.data.haodianExtra.haodian_cid = t.data.diy.haodianCategory[e].id, 
        t.data.diy.haodianCategory[e].children.length > 0 && (t.data.haodianExtra.haodian_child_id = t.data.diy.haodianCategory[e].children[0].id)), 
        t.setData({
            haodianExtra: t.data.haodianExtra
        });
    },
    onClickHaodianChildCategory: function(a) {
        var t = this, e = a.detail;
        e && e.id && (t.data.haodianExtra.haodian_child_id = e.id, 0 == t.data.haodianExtra.haodian_cid && t.data.diy.haodianCategory && t.data.diy.haodianCategory.length > 0 && (t.data.haodianExtra.haodian_cid = t.data.diy.haodianCategory[0].id)), 
        t.setData({
            haodianExtra: t.data.haodianExtra
        });
    },
    onHaodianCategoryConfirm: function() {
        var a = this;
        a.data.haodianExtra.haodian_child_id > 0 && 0 == a.data.haodianExtra.haodian_cid && a.data.diy.haodianCategory && a.data.diy.haodianCategory.length > 0 && (a.data.haodianExtra.haodian_cid = a.data.diy.haodianCategory[0].id), 
        a.setData({
            "popup.haodianSearch": !1,
            showSearchSign: !0
        }), a.onGetHaoDian(!0);
    },
    onReady: function() {},
    onShow: function() {
        var t = this;
        t.data.diy && 1 == t.data.diy.is_show_cart && a.util.request({
            url: "wmall/home/index/cart",
            showLoading: !1,
            success: function(a) {
                var e = a.data.message.message.cart_sum;
                t.setData({
                    cart_sum: e
                });
            }
        });
    },
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {
        var a = this;
        this.data.diy.tongcheng ? a.data.tongcheng = {
            page: 1,
            psize: 10,
            empty: !1,
            loaded: !1,
            data: []
        } : this.data.diy.haodian ? (a.data.haodian = {
            page: 1,
            psize: 10,
            empty: !1,
            loaded: !1,
            data: []
        }, a.data.haodianExtra = {
            orderby: "distance",
            haodian_cid: 0,
            haodian_child_id: 0,
            cIndexActive: 0
        }, a.data.popup.haodianSearch = !1, a.data.showSearchSign = !1) : (a.data.store = {
            page: 1,
            psize: 20,
            loaded: 0,
            empty: 0,
            data: [],
            filter: a.data.store.filter
        }, a.data.storeExtra = {
            condition: {
                order: "",
                mode: "",
                dis: ""
            },
            filter_title: "综合排序",
            multiple: !1,
            filter: !1,
            activityShowAll: !1
        }), a.onLoad(a.data.options), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {
        this.data.diy.tongcheng ? this.onGetInformation() : this.data.diy.haodian ? this.onGetHaoDian() : this.onGetStore();
    },
    onShareAppMessage: function() {}
});