var t = getApp();

Page({
    data: {
        id: 0,
        comment: {
            dialogShow: !1,
            title: "评论",
            placeholder: "说点什么...",
            content: "",
            type: "",
            id: 0,
            to_uid: 0,
            to_nickname: ""
        },
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
        a && (e.data.id = a.id, e.data.trade_id = a.trade_id), t.util.request({
            url: "tongcheng/index/detail",
            data: {
                id: e.data.id,
                trade_id: e.data.trade_id,
                menufooter: 1,
                _navc: 1
            },
            success: function(a) {
                t.util.loaded();
                var n = a.data.message;
                if (n.errno) return t.util.toast(n.message), !1;
                var i = a.data.global.share;
                i && (e.data.shareData.title = i.title, e.data.shareData.desc = i.desc, e.data.shareData.imageUrl = i.imgUrl, 
                e.data.shareData.path = "/gohome/pages/tongcheng/detail?id=" + e.data.id), n = n.message, 
                t.WxParse.wxParse("content", "html", n.detail.content, e, 5), e.setData({
                    detail: n.detail,
                    comments: n.comments
                });
                var o = a.data.global;
                o.menufooter && t.util.setNavigator(o.menufooter);
            }
        });
    },
    onLike: function() {
        var a = this;
        t.util.request({
            url: "tongcheng/index/like",
            data: {
                id: a.data.id
            },
            success: function(a) {
                var e = a.data.message;
                e.errno ? t.util.toast(e.message) : t.util.toast("点赞成功");
            }
        });
    },
    onComment: function(t) {
        var a = t.currentTarget.dataset;
        this.data.comment.type = a.type, this.data.comment.id = a.id, this.data.comment.to_uid = a.to_uid, 
        this.data.comment.to_nickname = a.to_nickname, "reply" == a.type ? (this.data.comment.title = "回复", 
        this.data.comment.placeholder = "回复" + this.data.comment.to_nickname) : (this.data.comment.title = "评论", 
        this.data.comment.placeholder = "说点什么..."), this.data.comment.dialogShow = !0, this.setData({
            comment: this.data.comment
        });
    },
    onCommentSubmit: function() {
        var a = this, e = a.data.comment;
        if (!e.content) return t.util.toast("你好像什么也没说", "", 1e3), !1;
        var n = "tongcheng/index/comment";
        "reply" == e.type && (n = "tongcheng/index/reply"), t.util.request({
            url: n,
            data: {
                tid: a.data.id,
                id: e.id,
                to_uid: e.to_uid,
                content: e.content
            },
            success: function(e) {
                t.util.toast(e.data.message.message, "", "1000"), a.onLoad({
                    id: a.data.detail.id
                });
            }
        });
    },
    onInput: function(t) {
        this.data.comment.content = t.detail.value;
    },
    onPullDownRefresh: function() {},
    onShareAppMessage: function() {
        return this.data.shareData;
    },
    onJsEvent: function(a) {
        t.util.jsEvent(a);
    }
});