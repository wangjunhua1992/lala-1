var t = getApp();

Page({
    data: {
        activityStatus: !1,
        hide_telephone: 0,
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
        t.util.request({
            url: "wmall/store/index",
            data: {
                sid: a.sid,
                gconfig: 1
            },
            success: function(a) {
                t.util.loaded();
                var s = a.data.message.message.store, i = a.data.message.message.activity;
                if (i.num || (e.data.activityStatus = !0), e.setData({
                    store: s,
                    hide_telephone: a.data.global.gconfig.hide_telephone,
                    activity: i,
                    activityStatus: e.data.activityStatus
                }), e.data.shareData.title = s.title, e.data.shareData.path = "/pages/store/goods?sid=" + s.id, 
                wx.setNavigationBarTitle({
                    title: e.data.store.title
                }), s.data.wxapp) {
                    var o = s.data.wxapp.extPages.pages_store_goods.navigationBarBackgroundColor;
                    wx.setNavigationBarColor({
                        frontColor: "#ffffff",
                        backgroundColor: o
                    }), e.setData({
                        bgColor: o
                    });
                }
            }
        });
    },
    onJsEvent: function(a) {
        t.util.jsEvent(a);
    },
    onFavor: function(a) {
        var e = this, s = a.currentTarget.dataset.sid, i = e.data.store;
        if (e.data.store.is_favorite) o = "cancal"; else var o = "star";
        var r = {
            id: s,
            type: o
        };
        t.util.request({
            url: "wmall/member/op/favorite",
            data: r,
            success: function(a) {
                0 == a.data.message.errno ? "star" == o ? (t.util.toast("添加收藏成功"), i.is_favorite = !i.is_favorite, 
                e.setData({
                    store: i
                })) : (t.util.toast("取消收藏成功"), i.is_favorite = !i.is_favorite, e.setData({
                    store: i
                })) : t.util.toast(a.data.message.message);
            }
        });
    },
    onShareAppMessage: function() {
        return this.data.shareData;
    },
    onImg: function(t) {
        var a = this, e = t.currentTarget.dataset.type;
        if ("service" == e) s = a.data.store.qualification.service.thumb; else if ("business" == e) s = a.data.store.qualification.business.thumb; else if ("more1" == e) s = a.data.store.qualification.more1.thumb; else if ("more2" == e) s = a.data.store.qualification.more2.thumb; else var s = a.data.store.thumbs[e].image;
        var i = [ s ];
        wx.previewImage({
            urls: i
        });
    }
});