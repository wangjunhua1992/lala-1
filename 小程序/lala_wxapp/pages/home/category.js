var t = getApp();

Page({
    data: {
        theme: t.util.getStorageSync("theme"),
        filter_title: "综合排序",
        activityHeight: !1,
        showNodata: !1,
        shareData: {
            title: "",
            path: "/pages/home/index",
            success: function() {},
            fail: function() {}
        },
        condition: {
            order: "",
            mode: "",
            dis: ""
        },
        store: {
            page: 1,
            psize: 20,
            loaded: 0,
            empty: 0,
            data: [],
            loading: 1
        },
        categorySelectedId: 0,
        Lang: t.Lang,
        wuiLoading: {
            show: !0,
            img: t.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(a) {
        var e = this;
        e.data.options = a, a.cid && (e.data.categorySelectedId = a.cid, e.data.cid = a.cid), 
        a.child_id && (e.data.categorySelectedId = a.child_id, e.data.child_id = a.child_id), 
        a.dis && (e.data.condition.dis = a.dis), a.delivery_type && (e.data.delivery_type = a.delivery_type);
        var i = t.util.getStorageSync("location");
        i && i.x ? e.onGetInfo() : t.util.getLocation(function(a) {
            var i = a.data.message.message;
            i = {
                address: i.address,
                x: i.latitude,
                y: i.longitude
            }, t.util.setStorageSync("location", i, 300), e.onGetInfo();
        });
    },
    onChangeCategory: function(t) {
        var a = this, e = t.currentTarget.dataset.id;
        e != a.data.categorySelectedId && (a.setData({
            categorySelectedId: e
        }), e != a.data.cid ? a.data.child_id = e : a.data.child_id = 0, a.onGetStore(!0));
    },
    onGetStore: function(a) {
        var e = this;
        if (a && (e.data.store = {
            page: 1,
            loaded: 0,
            empty: 0,
            data: [],
            loading: 1
        }), 1 == e.data.store.loaded) return !1;
        e.setData({
            "store.loading": 1
        });
        var i = t.util.getStorageSync("location"), d = {
            lat: i.x,
            lng: i.y,
            condition: JSON.stringify(e.data.condition),
            cid: e.data.cid,
            child_id: e.data.child_id,
            delivery_type: e.data.delivery_type,
            page: e.data.store.page,
            psize: e.data.store.psize,
            forceLocation: 1
        };
        t.util.request({
            url: "wmall/home/index/store",
            data: d,
            success: function(t) {
                var a = t.data.message.message;
                e.data.store.data = e.data.store.data.concat(a.stores), a.pagetotal <= e.data.store.page && (e.data.store.loaded = 1, 
                e.data.store.loading = 0, e.data.store.data.length || (e.data.store.empty = 1)), 
                e.data.store.page++, e.setData({
                    store: e.data.store
                });
            }
        });
    },
    onGetInfo: function() {
        var a = this, e = t.util.getStorageSync("location");
        if (e) {
            var i = {
                lat: e.x,
                lng: e.y,
                cid: a.data.cid,
                child_id: a.data.child_id,
                delivery_type: a.data.delivery_type,
                condition: JSON.stringify(a.data.condition),
                menufooter: 1
            };
            t.util.request({
                url: "wmall/home/search/index",
                data: i,
                success: function(e) {
                    t.util.loaded();
                    var i = e.data.message.message;
                    a.data.store.data = i.stores.stores, i.stores.pagetotal <= a.data.store.page && (a.data.store.loaded = 1, 
                    a.data.store.data.length || (a.data.store.empty = 1)), a.data.store.page++, i.stores = a.data.store, 
                    a.setData({
                        store: i.stores,
                        discounts: i.discounts,
                        orderbys: i.orderbys,
                        carousel: i.carousel,
                        config: i.config,
                        categorySelectedId: a.data.categorySelectedId
                    }), wx.setNavigationBarTitle({
                        title: i.carousel.title ? i.carousel.title : "全部商家"
                    }), a.data.shareData.title = i.config.title;
                }
            });
        } else t.util.toast("获取位置失败,请重新进入小程序");
    },
    onToggleDiscount: function(t) {
        var a = t.currentTarget.dataset, e = this.data.store.data;
        e[a.index].activity.is_show_all = !e[a.index].activity.is_show_all, this.setData({
            "store.data": e
        });
    },
    onReady: function() {},
    onReachBottom: function() {
        this.onGetStore();
    },
    onMultiple: function() {
        this.setData({
            multiple: !this.data.multiple
        });
    },
    onFilter: function() {
        this.setData({
            filter: !this.data.filter
        });
    },
    onJsEvent: function(a) {
        t.util.jsEvent(a);
    },
    onOrderby: function(t) {
        var a = this, e = t.currentTarget.dataset.type;
        if ("order" == e) {
            var i = t.currentTarget.dataset.order;
            "svipRedpacket" == i ? a.setData({
                "condition.dis": i
            }) : (a.setData({
                "condition.order": i,
                multiple: !1,
                showNodata: !1
            }), "sailed" != i && "distance" != i ? a.setData({
                filter_title: t.currentTarget.dataset.title
            }) : a.setData({
                filter_title: "综合排序"
            }));
        } else if ("discounts" == e) {
            var d = t.currentTarget.dataset.dis;
            if (a.data.condition.dis == d && (d = ""), a.setData({
                "condition.dis": d
            }), "refresh" != t.currentTarget.dataset.title) return !1;
        } else {
            if ("mode" == e) {
                var o = t.currentTarget.dataset.mode;
                return a.data.condition.mode == o && (o = ""), a.setData({
                    "condition.mode": o
                }), !1;
            }
            "clear" == e ? a.setData({
                "condition.dis": "",
                "condition.order": "",
                "condition.mode": "",
                filter: !1
            }) : "finish" == e && a.setData({
                filter: !1,
                showNodata: !1
            });
        }
        a.onGetStore(!0);
    },
    onPullDownRefresh: function() {
        var t = this;
        t.data.store = {
            page: 1,
            psize: 20,
            loaded: 0,
            empty: 0,
            data: [],
            loading: 1
        }, t.onLoad(t.data.options), wx.stopPullDownRefresh();
    },
    onShareAppMessage: function() {
        return this.data.shareData;
    }
});