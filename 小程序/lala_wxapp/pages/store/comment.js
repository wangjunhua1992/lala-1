var t = getApp();

Page({
    data: {
        type: 0,
        note: 1,
        comments: [],
        showloading: !1,
        showNodata: !1,
        activityStatus: !1,
        shareData: {
            title: "",
            path: "",
            success: function() {},
            fail: function() {}
        },
        Lang: t.Lang,
        wuiLoading: {
            show: !0,
            img: t.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(a) {
        var e = this;
        a && a.sid && (e.data.sid = a.sid), t.util.request({
            url: "wmall/store/goods/index",
            data: {
                sid: e.data.sid
            },
            success: function(a) {
                t.util.loaded();
                var a = a.data.message.message;
                if (e.data.store = a.store, a.store.activity.num || (e.data.activityStatus = !0), 
                e.setData({
                    store: a.store,
                    activityStatus: e.data.activityStatus
                }), e.data.shareData.title = a.store.title, e.data.shareData.path = "/pages/store/goods?sid=" + a.store.id, 
                wx.setNavigationBarTitle({
                    title: e.data.store.title
                }), a.store.data.wxapp) {
                    var s = a.store.data.wxapp.extPages.pages_store_goods.navigationBarBackgroundColor;
                    wx.setNavigationBarColor({
                        frontColor: "#ffffff",
                        backgroundColor: s
                    }), e.setData({
                        bgColor: s
                    });
                }
            }
        }), e.onReachBottom();
    },
    onReachBottom: function(a) {
        var e = this;
        if (!0 === a && (e.data.min = 0, e.data.comments = [], e.data.showloading = !1, 
        e.data.showNodata = !1), -1 == e.data.min) return !1;
        e.setData({
            showloading: !0
        }), t.util.request({
            url: "wmall/store/comment",
            data: {
                sid: e.data.sid,
                min: e.data.min,
                type: e.data.type,
                note: e.data.note
            },
            success: function(t) {
                var a = e.data.comments.concat(t.data.message.message.comments), s = t.data.message.message.stat;
                a.length || e.setData({
                    showNodata: !0
                }), e.setData({
                    comments: a,
                    min: t.data.message.min,
                    stat: s
                }), t.data.message.min || (e.data.min = -1), e.setData({
                    showNodata: e.data.showNodata,
                    showloading: !1
                });
            }
        });
    },
    onImg: function(t) {
        var a = this.data.comments[t.currentTarget.dataset.idx].thumbs, e = a[t.currentTarget.dataset.id];
        wx.previewImage({
            current: e,
            urls: a
        });
    },
    onFavor: function(a) {
        var e = this, s = a.currentTarget.dataset.sid, o = e.data.store;
        if (e.data.store.is_favorite) i = "cancal"; else var i = "star";
        var n = {
            id: s,
            type: i
        };
        t.util.request({
            url: "wmall/member/op/favorite",
            data: n,
            success: function(a) {
                0 == a.data.message.errno ? "star" == i ? (t.util.toast("添加收藏成功"), o.is_favorite = !o.is_favorite, 
                e.setData({
                    store: o
                })) : (t.util.toast("取消收藏成功"), o.is_favorite = !o.is_favorite, e.setData({
                    store: o
                })) : t.util.toast(a.data.message.message);
            }
        });
    },
    onChangeType: function(t) {
        var a = t.currentTarget.dataset.type;
        this.setData({
            type: a
        }), this.onReachBottom(!0);
    },
    onCheckedReadComment: function() {
        var t = this;
        1 == t.data.note ? t.data.note = 0 : t.data.note = 1, t.setData({
            note: t.data.note
        }), this.onReachBottom(!0);
    },
    onShareAppMessage: function() {
        return this.data.shareData;
    }
});