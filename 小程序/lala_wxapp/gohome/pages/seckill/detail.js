var a = getApp();

Page({
    data: {
        id: 0,
        zhezhaoShow: !1,
        comment: {
            page: 1,
            psize: 4,
            empty: !1,
            loaded: !1,
            data: []
        },
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(t) {
        var e = this;
        if (t && t.id && (e.data.id = t.id), t && t.scene) {
            var o = a.util.parseScene(t.scene);
            e.data.id = o.id;
        }
        a.util.request({
            url: "seckill/goods/detail",
            data: {
                id: e.data.id,
                page: e.data.comment.page,
                psize: e.data.comment.psize
            },
            success: function(t) {
                a.util.loaded();
                var o = t.data.message;
                if (o.errno) return a.util.toast(o.message), !1;
                o = o.message, wx.setNavigationBarTitle({
                    title: o.goods.name
                }), a.WxParse.wxParse("buy_note", "html", o.goods.buy_note, e, 5), a.WxParse.wxParse("description", "html", o.goods.description, e, 5);
                var n = o.comment;
                e.data.comment.data = e.data.comment.data.concat(n), n && n.length < e.data.comment.psize && (e.data.comment.loaded = !0), 
                e.data.comment.data.length || (e.data.comment.empty = !0), e.data.comment.page++, 
                e.setData({
                    goods: o.goods,
                    recommend: o.recommend,
                    comment: e.data.comment,
                    danmu: o.danmu,
                    sharedata: o.sharedata
                });
            }
        });
    },
    onToggleFavor: function() {
        var t = this;
        a.util.request({
            url: "gohome/favorite/favorite",
            data: {
                goods_id: t.data.goods.id,
                type: "seckill"
            },
            success: function(e) {
                var o = e.data.message;
                if (a.util.toast(o.message), o.errno) return !1;
                t.setData({
                    "goods.is_favor": !t.data.goods.is_favor
                });
            }
        });
    },
    onGetComment: function() {
        var t = this;
        t.data.comment.loaded || a.util.request({
            url: "gohome/common/comment",
            data: {
                id: t.data.id,
                page: t.data.comment.page,
                psize: t.data.comment.psize,
                type: "seckill"
            },
            success: function(e) {
                var o = e.data.message;
                if (o.errno) return a.util.toast(o.message), !1;
                var n = (o = o.message).comment;
                t.data.comment.data = t.data.comment.data.concat(n), n && n.length < t.data.comment.psize && (t.data.comment.loaded = !0), 
                t.data.comment.data.length || (t.data.comment.empty = !0), t.data.comment.page++, 
                console.log(111, t.data.comment), t.setData({
                    comment: t.data.comment
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
    onReachBottom: function() {},
    onPullDownRefresh: function() {
        var a = this;
        a.data.comment = {
            page: 1,
            psize: 4,
            empty: !1,
            loaded: !1,
            data: []
        }, a.onLoad(), wx.stopPullDownRefresh();
    },
    onJsEvent: function(t) {
        a.util.jsEvent(t);
    },
    onToggleZhezhao: function() {
        this.setData({
            zhezhaoShow: !this.data.zhezhaoShow
        });
    },
    onShareAppMessage: function(a) {
        return this.data.sharedata;
    }
});