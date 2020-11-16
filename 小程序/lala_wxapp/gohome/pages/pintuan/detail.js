var a = getApp();

Page({
    data: {
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
        if (t && t.id && (e.data.id = t.id), t.scene) {
            var d = a.util.parseScene(t.scene);
            e.data.id = d.id;
        }
        a.util.request({
            url: "pintuan/index/detail",
            data: {
                id: e.data.id,
                page: e.data.comment.page,
                psize: e.data.comment.psize
            },
            success: function(t) {
                a.util.loaded();
                var d = t.data.message;
                if (d.errno) return a.util.toast(d.message), !1;
                d = d.message, wx.setNavigationBarTitle({
                    title: d.detail.name
                }), a.WxParse.wxParse("goods_detail", "html", d.detail.detail, e, 5);
                var i = d.comment;
                e.data.comment.data = e.data.comment.data.concat(i), i && i.length < e.data.comment.psize && (e.data.comment.loaded = !0, 
                e.data.comment.data.length || (e.data.comment.empty = !0)), e.data.comment.page++, 
                e.setData({
                    detail: d.detail,
                    teams: d.teams,
                    more_activity: d.more_activity,
                    record: d.record,
                    comment: e.data.comment,
                    danmu: d.danmu,
                    shareData: d.shareData
                });
            }
        });
    },
    onJoinTeam: function(t) {
        var e = this, d = t.currentTarget.dataset.team_id;
        if ("start" != t.currentTarget.dataset.type) {
            i = "/gohome/pages/pintuan/create?id=" + this.data.id + "&team_id=" + d;
            e.data.record.id && e.data.record.team_id && (i = "/gohome/pages/pintuan/share?id=" + this.data.id + "&team_id=" + e.data.record.team_id), 
            a.util.jump2url(i, "navigateTo");
        } else {
            if (e.data.record && e.data.record.id) return a.util.toast("您已参与了该团，请等待本次团购结束后在进行开团", "/gohome/pages/pintuan/share?id=" + this.data.id + "&team_id=" + e.data.record.team_id, 1e3), 
            !1;
            var i = "/gohome/pages/pintuan/create?id=" + this.data.id + "&is_team=1";
            a.util.jump2url(i, "navigateTo");
        }
    },
    onToggleFavor: function() {
        var t = this;
        a.util.request({
            url: "gohome/favorite/favorite",
            data: {
                goods_id: t.data.detail.id,
                type: "pintuan"
            },
            success: function(e) {
                var d = e.data.message;
                if (a.util.toast(d.message), d.errno) return !1;
                t.setData({
                    "detail.is_favor": !t.data.detail.is_favor
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
                type: "pintuan"
            },
            success: function(e) {
                var d = e.data.message;
                if (d.errno) return a.util.toast(d.message), !1;
                var i = (d = d.message).comment;
                t.data.comment.data = t.data.comment.data.concat(i), i && i.length < t.data.comment.psize && (t.data.comment.loaded = !0), 
                t.data.comment.data.length || (t.data.comment.empty = !0), t.data.comment.page++, 
                t.setData({
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
        var t = this;
        return t.data.shareData.imageUrl = t.data.shareData.imgUrl, t.data.shareData;
    }
});