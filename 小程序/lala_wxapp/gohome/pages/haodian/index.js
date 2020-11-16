var a = getApp();

Page({
    data: {
        address: "定位中。。。",
        topSearchBar: !1,
        shareData: {
            title: "",
            path: "/gohome/pages/haodian/index",
            success: function() {},
            fail: function() {}
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
        danmu: 1,
        dialog: {
            dialogGuide: !0
        },
        has_diy_guide: !1,
        showSearchSign: !1,
        getLocationStatus: !0,
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function() {
        var t = this;
        a.util.request({
            url: "haodian/index/index",
            data: {
                psize: t.data.haodian.psize,
                orderby: t.data.haodianExtra.orderby,
                haodian_cid: t.data.haodianExtra.haodian_cid,
                haodian_child_id: t.data.haodianExtra.haodian_child_id,
                menufooter: 1,
                forceLocation: 1
            },
            success: function(i) {
                a.util.loaded();
                var d = i.data.message;
                if (d.errno) return -2 == d.errno ? (t.setData({
                    getLocationStatus: !1,
                    failedTips: {
                        tips: d.message,
                        link: "pages/home/location?from=haodian",
                        btnText: "手动搜索地址",
                        img: "http://cos.lalawaimai.com/we7_wmall/wxapp/store_no_con.png"
                    }
                }), !1) : (a.util.toast(d.message), !1);
                var o = i.data.global.share;
                o && (t.data.shareData.title = o.title, t.data.shareData.imageUrl = o.imgUrl), (d = d.message).diy.haodian && d.diy.haodian.category && (d.diy.haodianCategory = d.diy.haodian.category, 
                t.setData({
                    "haodianExtra.haodian_child_id": d.diy.haodian.haodian_child_id
                })), d.diy.haodian && d.diy.haodian.store && (t.data.haodian.data = d.diy.haodian.store, 
                t.data.haodian.loading = 0, d.diy.haodian.has_get_all && (t.data.haodian.loaded = 1), 
                0 == t.data.haodian.data.length && (t.data.haodian.empty = 1), delete d.diy.haodian, 
                d.diy.haodian = t.data.haodian);
                var e = {};
                d.diy.is_has_location && (e = a.util.getStorageSync("location")), d.location = e;
                var n = d.diy.data.items;
                for (var r in n) "richtext" == n[r].id && a.WxParse.wxParse("richtext." + r, "html", n[r].params.content, t, 5);
                t.data.richtext && (d.richtext = t.data.richtext), wx.setNavigationBarTitle({
                    title: d.diy.data.page.title
                }), wx.setNavigationBarColor({
                    frontColor: d.diy.data.page.navigationtextcolor,
                    backgroundColor: d.diy.data.page.navigationbackground
                }), setInterval(function() {
                    t.setData({
                        danmu: !t.data.danmu
                    });
                }, 2500);
                var h = d.diy.guide;
                h && (h && "everytime" == h.params.show_setting && a.util.getStorageSync("storage") && (t.setData({
                    has_diy_guide: !0
                }), a.util.setStorageSync("storage", {}, 0)), d.storage = a.util.getStorageSync("storage"), 
                !h || "interval" != h.params.show_setting || d.storage && d.storage.storageGuide || a.util.setStorageSync("storage", {
                    storageGuide: 1
                }, 60 * h.params.interval_time)), t.setData(d);
            }
        });
    },
    onPageScroll: function(a) {
        var t = this;
        a.scrollTop > 200 ? t.setData({
            topSearchBar: !0
        }) : t.setData({
            topSearchBar: !1
        });
    },
    onGetHaoDian: function(t) {
        var i = this;
        t && (i.data.haodian = {
            page: 1,
            psize: 10,
            loaded: 0,
            empty: 0,
            data: [],
            loading: 1
        }), i.data.haodian.loaded || a.util.request({
            url: "haodian/index/store",
            data: {
                page: i.data.haodian.page,
                psize: i.data.haodian.psize,
                orderby: i.data.haodianExtra.orderby,
                haodian_cid: i.data.haodianExtra.haodian_cid,
                haodian_child_id: i.data.haodianExtra.haodian_child_id
            },
            success: function(t) {
                var d = t.data.message;
                if (d.errno) return a.util.toast(d.message), !1;
                d = d.message, i.data.haodian.data = i.data.haodian.data.concat(d.store), d.store.length < i.data.haodian.psize && (i.data.haodian.loaded = !0, 
                i.data.haodian.data.length || (i.data.haodian.empty = !0)), i.data.haodian.page++, 
                i.setData({
                    "diy.haodian": i.data.haodian
                });
            }
        });
    },
    onChangeHaodianExtra: function(a) {
        var t = this, i = a.currentTarget.dataset.type;
        if ("filter" == i) t.setData({
            "popup.haodianSearch": !t.data.popup.haodianSearch
        }); else {
            if (i == t.data.haodianExtra.orderby) return;
            t.data.haodianExtra.orderby = i, t.data.haodianExtra.haodian_cid = 0, t.data.diy.haodianCategory && t.data.diy.haodianCategory.length > 0 && t.data.diy.haodianCategory[0].children.length > 0 && (t.data.haodianExtra.haodian_child_id = t.data.diy.haodianCategory[0].children[0].id), 
            t.data.haodianExtra.cIndexActive = 0, t.setData({
                haodianExtra: t.data.haodianExtra,
                showSearchSign: !1
            }), t.onGetHaoDian(!0);
        }
    },
    onClickHaodianParentCategory: function(a) {
        var t = this, i = a.detail.index;
        t.data.haodianExtra.cIndexActive = i, t.data.diy.haodianCategory.hasOwnProperty(i) && (t.data.haodianExtra.haodian_cid = t.data.diy.haodianCategory[i].id, 
        t.data.diy.haodianCategory[i].children.length > 0 && (t.data.haodianExtra.haodian_child_id = t.data.diy.haodianCategory[i].children[0].id)), 
        t.setData({
            haodianExtra: t.data.haodianExtra
        });
    },
    onClickHaodianChildCategory: function(a) {
        var t = this, i = a.detail;
        i && i.id && (t.data.haodianExtra.haodian_child_id = i.id, 0 == t.data.haodianExtra.haodian_cid && t.data.diy.haodianCategory && t.data.diy.haodianCategory.length > 0 && (t.data.haodianExtra.haodian_cid = t.data.diy.haodianCategory[0].id)), 
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
    onReachBottom: function() {
        this.onGetHaoDian();
    },
    onImgPreview: function(a) {
        var t = a.currentTarget.dataset.current, i = a.currentTarget.dataset.urls;
        wx.previewImage({
            current: t,
            urls: i
        });
    },
    onToggleInformationHeight: function(a) {
        var t = this, i = a.currentTarget.dataset.index;
        t.data.diy.tongcheng.data[i].showall = !t.data.diy.tongcheng.data[i].showall, t.setData({
            "diy.tongcheng.data": t.data.diy.tongcheng.data
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
    onPullDownRefresh: function() {
        var a = this;
        a.data.haodian = {
            page: 1,
            psize: 10,
            empty: !1,
            loaded: !1,
            data: []
        }, a.onLoad(), wx.stopPullDownRefresh();
    },
    onJsEvent: function(t) {
        a.util.jsEvent(t);
    },
    onShareAppMessage: function() {
        return this.data.shareData;
    }
});