var a = getApp();

Page({
    data: {
        category: {
            id: 0,
            title: "好店分类"
        },
        haodian: {
            page: 2,
            psize: 10,
            loaded: !1,
            empty: !1,
            data: []
        },
        popup: {
            haodianSearch: !1
        },
        haodianExtra: {
            orderby: "distance",
            haodian_cid: 0,
            haodian_child_id: 0
        },
        haodianCategory: [],
        haodianTemp: {
            haodian_cid: 0,
            haodian_child_id: 0,
            cIndexActive: 0
        },
        getLocationStatus: !0,
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(d) {
        var t = this;
        d && d.cid && (t.data.haodianExtra.haodian_cid = d.cid), d && d.force && (t.data.haodian = {
            page: 1,
            psize: 10,
            loaded: 0,
            empty: 0,
            data: []
        }), a.util.request({
            url: "haodian/index/category",
            data: {
                psize: t.data.haodian.psize,
                orderby: t.data.haodianExtra.orderby,
                haodian_cid: t.data.haodianExtra.haodian_cid,
                haodian_child_id: t.data.haodianExtra.haodian_child_id,
                menufooter: 1,
                forceLocation: 1
            },
            success: function(d) {
                a.util.loaded();
                var i = d.data.message;
                if (i.errno) return -2 == i.errno ? (t.setData({
                    getLocationStatus: !1,
                    failedTips: {
                        tips: i.message,
                        link: "pages/home/location?from=haodian",
                        btnText: "手动搜索地址",
                        img: "http://cos.lalawaimai.com/we7_wmall/wxapp/store_no_con.png"
                    }
                }), !1) : (a.util.toast(i.message), !1);
                (i = i.message).category && wx.setNavigationBarTitle({
                    title: i.category.title
                }), t.data.haodian.data = t.data.haodian.data.concat(i.store), (!i.store || i.store.length < t.data.haodian.psize) && (t.data.haodian.loaded = !0), 
                t.data.haodian.data.length || (t.data.haodian.empty = !0), t.setData({
                    category: i.category,
                    haodianCategory: i.categorys,
                    haodian: t.data.haodian
                });
            }
        });
    },
    onGetHaoDian: function(d) {
        var t = this;
        d && (t.data.haodian = {
            page: 1,
            psize: 10,
            loaded: 0,
            empty: 0,
            data: []
        }), t.data.haodian.loaded || a.util.request({
            url: "haodian/index/store",
            data: {
                page: t.data.haodian.page,
                psize: t.data.haodian.psize,
                orderby: t.data.haodianExtra.orderby,
                haodian_cid: t.data.haodianExtra.haodian_cid,
                haodian_child_id: t.data.haodianExtra.haodian_child_id
            },
            success: function(d) {
                var i = d.data.message;
                if (i.errno) return a.util.toast(i.message), !1;
                i = i.message, t.data.haodian.data = t.data.haodian.data.concat(i.store), i.store.length < t.data.haodian.psize && (t.data.haodian.loaded = !0), 
                t.data.haodian.data.length || (t.data.haodian.empty = !0), t.data.haodian.page++, 
                t.setData({
                    haodian: t.data.haodian
                });
            }
        });
    },
    onChangeHaodianExtra: function(a) {
        var d = this, t = a.currentTarget.dataset.type;
        if ("filter" == t) d.setData({
            "popup.haodianSearch": !d.data.popup.haodianSearch
        }); else {
            if (t == d.data.haodianExtra.orderby) return;
            d.data.haodianExtra.orderby = t, d.setData({
                haodianExtra: d.data.haodianExtra
            }), d.onGetHaoDian(!0);
        }
    },
    onClickHaodianParentCategory: function(a) {
        var d = this, t = a.detail.index;
        console.log("parent"), d.data.haodianTemp.cIndexActive = t, d.data.haodianCategory.hasOwnProperty(t) && (d.data.haodianTemp.haodian_cid = d.data.haodianCategory[t].id, 
        d.data.haodianCategory[t].children.length > 0 && (d.data.haodianTemp.haodian_child_id = d.data.haodianCategory[t].children[0].id)), 
        d.setData({
            haodianTemp: d.data.haodianTemp
        });
    },
    onClickHaodianChildCategory: function(a) {
        var d = this, t = a.detail;
        t && t.id && (d.data.haodianTemp.haodian_child_id = t.id, 0 == d.data.haodianTemp.haodian_cid && d.data.haodianCategory && d.data.haodianCategory.length > 0 && (d.data.haodianTemp.haodian_cid = d.data.haodianCategory[0].id)), 
        d.setData({
            haodianTemp: d.data.haodianTemp
        });
    },
    onHaodianCategoryConfirm: function() {
        var a = this;
        a.data.haodianExtra.haodian_cid = a.data.haodianTemp.haodian_cid, a.data.haodianExtra.haodian_child_id = a.data.haodianTemp.haodian_child_id, 
        a.setData({
            haodianExtra: a.data.haodianExtra,
            "popup.haodianSearch": !1
        }), a.onLoad({
            force: !0
        });
    },
    onReachBottom: function() {
        this.onGetHaoDian();
    },
    onJsEvent: function(d) {
        a.util.jsEvent(d);
    }
});