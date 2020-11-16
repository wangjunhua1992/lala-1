var a = getApp();

Page({
    data: {
        sid: 0,
        detailOrComment: "detail",
        zhezhaoShow: !1,
        can_comment: !1,
        comment: {
            page: 1,
            psize: 5,
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
        t && t.sid && (e.data.sid = t.sid), a.util.request({
            url: "haodian/index/detail",
            data: {
                sid: e.data.sid,
                page: e.data.comment.page,
                psize: e.data.comment.psize
            },
            success: function(t) {
                a.util.loaded();
                var o = t.data.message;
                if (o.errno) return a.util.toast(o.message), !1;
                o = o.message, e.data.can_comment = o.can_comment;
                var n = o.store.description;
                a.WxParse.wxParse("content", "html", n, e, 5), e.data.comment.data = o.comment, 
                e.data.comment.data.length || (e.data.comment.empty = !0), o.comment && o.comment.length < e.data.comment.psize && (e.data.comment.loaded = !0), 
                e.data.comment.page++, e.setData({
                    store: o.store,
                    coupon: o.coupon,
                    kanjia: o.kanjia,
                    seckill: o.seckill,
                    pintuan: o.pintuan,
                    comment: {
                        page: e.data.comment.page,
                        psize: 5,
                        empty: e.data.comment.empty,
                        loaded: e.data.comment.loaded,
                        data: e.data.comment.data
                    },
                    sharedata: o.sharedata
                });
            }
        });
    },
    onFavor: function(t) {
        var e = this, o = t.currentTarget.dataset.sid, n = e.data.store;
        if (e.data.store.is_favorite) m = "cancal"; else var m = "star";
        var s = {
            id: o,
            type: m
        };
        a.util.request({
            url: "wmall/member/op/favorite",
            data: s,
            success: function(t) {
                0 == t.data.message.errno ? "star" == m ? (a.util.toast("添加收藏成功"), n.is_favorite = !n.is_favorite, 
                e.setData({
                    store: n
                })) : (a.util.toast("取消收藏成功"), n.is_favorite = !n.is_favorite, e.setData({
                    store: n
                })) : a.util.toast(t.data.message.message);
            }
        });
    },
    onJsEvent: function(t) {
        a.util.jsEvent(t);
    },
    onToggleZhezhao: function() {
        this.setData({
            zhezhaoShow: !this.data.zhezhaoShow
        });
    },
    onComment: function(t) {
        this.data.can_comment ? a.util.jsEvent(t) : a.util.toast("您已评论过，请勿重复评论");
    },
    onToggleDetailOrComment: function(a) {
        var t = this, e = a.currentTarget.dataset.type;
        e != t.data.detailOrComment && t.setData({
            detailOrComment: e
        });
    },
    onReachBottom: function() {
        var t = this;
        if ("detail" == t.data.detailOrComment) return !1;
        t.data.comment.loaded || a.util.request({
            url: "haodian/index/comment",
            data: {
                sid: t.data.sid,
                page: t.data.comment.page,
                psize: t.data.comment.psize
            },
            success: function(e) {
                var o = e.data.message;
                if (o.errno) return a.util.toast(o.message), !1;
                o = o.message, t.data.comment.data = t.data.comment.data.concat(o.comment), t.data.comment.data.length || (t.data.comment.empty = !0), 
                o.comment && o.comment.length < t.data.comment.psize && (t.data.comment.loaded = !0), 
                t.data.comment.page++, t.setData({
                    comment: {
                        page: t.data.comment.page,
                        psize: 5,
                        empty: t.data.comment.empty,
                        loaded: t.data.comment.loaded,
                        data: t.data.comment.data
                    }
                });
            }
        });
    },
    getCoupon: function(t) {
        var e = this;
        a.util.request({
            url: "wmall/channel/coupon/get",
            data: {
                sid: t.target.dataset.sid
            },
            success: function(t) {
                0 == t.data.message.errno ? (a.util.toast(t.data.message.message), e.setData({
                    is_grant: 1
                })) : a.util.toast("领取失败");
            }
        });
    },
    onShowImage: function(t) {
        a.util.showImage(t);
    },
    onShareAppMessage: function(a) {
        return this.data.sharedata;
    }
});