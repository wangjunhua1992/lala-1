var a = getApp();

Page({
    data: {
        is_use_diy: 1,
        address: "定位中。。。",
        topSearchBar: !1,
        shareData: {
            title: "",
            path: "/gohome/pages/tongcheng/index",
            success: function() {},
            fail: function() {}
        },
        tongcheng: {
            page: 2,
            psize: 10,
            loaded: 0,
            empty: 0,
            data: [],
            loading: 1
        },
        danmu: 1,
        dialog: {
            dialogGuide: !0
        },
        has_diy_guide: !1,
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
            url: "tongcheng/index/index",
            data: {
                menufooter: 1,
                forceLocation: 1
            },
            success: function(e) {
                a.util.loaded();
                var n = e.data.message;
                if (n.errno) return -2 == n.errno ? (t.setData({
                    getLocationStatus: !1,
                    failedTips: {
                        tips: n.message,
                        link: "pages/home/location?from=gohome",
                        btnText: "手动搜索地址",
                        img: "http://cos.lalawaimai.com/we7_wmall/wxapp/store_no_con.png"
                    }
                }), !1) : (a.util.toast(n.message), !1);
                var o = e.data.global.share;
                o && (t.data.shareData.title = o.title, t.data.shareData.imageUrl = o.imgUrl), (n = n.message).diy.tongcheng && n.diy.tongcheng.informationdata && (t.data.tongcheng.data = n.diy.tongcheng.informationdata, 
                t.data.tongcheng.loading = 0, n.diy.tongcheng.has_get_all && (t.data.tongcheng.loaded = 1), 
                0 == t.data.tongcheng.data.length && (t.data.tongcheng.empty = 1), t.data.tongcheng.page++, 
                delete n.diy.tongcheng, n.diy.tongcheng = t.data.tongcheng);
                var i = {};
                n.diy.is_has_location && (i = a.util.getStorageSync("location")), n.location = i;
                var g = n.diy.data.items;
                for (var d in g) "richtext" == g[d].id && a.WxParse.wxParse("richtext." + d, "html", g[d].params.content, t, 5);
                t.data.richtext && (n.richtext = t.data.richtext), wx.setNavigationBarTitle({
                    title: n.diy.data.page.title
                }), wx.setNavigationBarColor({
                    frontColor: n.diy.data.page.navigationtextcolor,
                    backgroundColor: n.diy.data.page.navigationbackground
                }), setInterval(function() {
                    t.setData({
                        danmu: !t.data.danmu
                    });
                }, 2500);
                var s = n.diy.guide;
                s && (s && "everytime" == s.params.show_setting && a.util.getStorageSync("storage") && (t.setData({
                    has_diy_guide: !0
                }), a.util.setStorageSync("storage", {}, 0)), n.storage = a.util.getStorageSync("storage"), 
                !s || "interval" != s.params.show_setting || n.storage && n.storage.storageGuide || a.util.setStorageSync("storage", {
                    storageGuide: 1
                }, 60 * s.params.interval_time)), t.setData(n);
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
    onGetInformation: function() {
        var t = this;
        t.data.tongcheng.loaded || a.util.request({
            url: "tongcheng/index/information",
            data: {
                page: t.data.tongcheng.page,
                psize: t.data.tongcheng.psize
            },
            success: function(e) {
                var n = e.data.message;
                if (n.errno) return a.util.toast(n.message), !1;
                n = n.message, t.data.tongcheng.data = t.data.tongcheng.data.concat(n.informations), 
                n.informations.length < t.data.tongcheng.psize && (t.data.tongcheng.loaded = !0, 
                t.data.tongcheng.data.length || (t.data.tongcheng.empty = !0)), t.data.tongcheng.page++, 
                t.setData({
                    "diy.tongcheng": t.data.tongcheng
                });
            }
        });
    },
    onReachBottom: function() {
        this.onGetInformation();
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
        a.data.tongcheng = {
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