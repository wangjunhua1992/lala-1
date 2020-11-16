var a = getApp();

Page({
    data: {
        id: 0,
        helpStatus: !1,
        zhezhaoShow: !1,
        dialogShow: !1,
        comment: {
            page: 1,
            psize: 4,
            empty: !1,
            loaded: !1,
            data: []
        },
        failedTips: {
            type: "message",
            tips: "",
            btnText: "关闭",
            link: "/pages/home/index"
        },
        black_member: {
            status: !1
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
            var i = a.util.parseScene(t.scene);
            e.data.id = i.id;
        }
        a.util.request({
            url: "kanjia/activity/detail",
            data: {
                id: e.data.id,
                page: e.data.comment.page,
                psize: e.data.comment.psize
            },
            success: function(t) {
                a.util.loaded();
                var i = t.data.message;
                if (i.errno) return -1e3 == i.errno ? (e.data.black_member = i.message.black_member, 
                e.data.failedTips.tips = e.data.black_member.tip, void e.setData({
                    black_member: e.data.black_member,
                    failedTips: e.data.failedTips
                })) : (a.util.toast(i.message), !1);
                i = i.message, wx.setNavigationBarTitle({
                    title: i.activity.name
                }), a.WxParse.wxParse("activity_rules", "html", i.activity.activity_rules, e, 5), 
                a.WxParse.wxParse("detail", "html", i.activity.detail, e, 5);
                var o = i.comment;
                e.data.comment.data = e.data.comment.data.concat(o), o && o.length < e.data.comment.psize && (e.data.comment.loaded = !0), 
                e.data.comment.data.length || (e.data.comment.empty = !0), e.data.comment.page++, 
                1 == i.activity.status && 0 != i.activity.total && i.member_takeinfo.price == i.activity.price && (e.data.dialogShow = !0), 
                e.setData({
                    activity: i.activity,
                    member_takeinfo: i.member_takeinfo,
                    store: i.store,
                    take_status: i.take_status,
                    comment: e.data.comment,
                    danmu: i.danmu,
                    dialogShow: e.data.dialogShow,
                    sharedata: i.sharedata
                });
            }
        });
    },
    onToggleHelpStatus: function() {
        var a = this;
        a.data.member_takeinfo.helper.length && a.setData({
            helpStatus: !a.data.helpStatus
        });
    },
    onToggleFavor: function() {
        var t = this;
        a.util.request({
            url: "gohome/favorite/favorite",
            data: {
                goods_id: t.data.activity.id,
                type: "kanjia"
            },
            success: function(e) {
                var i = e.data.message;
                if (a.util.toast(i.message), i.errno) return !1;
                t.setData({
                    "activity.is_favor": !t.data.activity.is_favor
                });
            }
        });
    },
    onParticipate: function() {
        var t = this;
        1 != t.data.take_status && a.util.request({
            url: "kanjia/activity/create",
            data: {
                activityid: t.data.id
            },
            success: function(e) {
                var i = e.data.message;
                if (i.errno) return a.util.toast(i.message), !1;
                var o = i.message;
                a.util.toast("参与成功", "/gohome/pages/kanjia/share?activityid=" + t.data.id + "&uid=" + o, 1500);
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
                type: "kanjia"
            },
            success: function(e) {
                var i = e.data.message;
                if (i.errno) return a.util.toast(i.message), !1;
                var o = (i = i.message).comment;
                t.data.comment.data = t.data.comment.data.concat(o), o && o.length < t.data.comment.psize && (t.data.comment.loaded = !0), 
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
    onReachBottom: function() {},
    onPullDownRefresh: function() {
        var a = this;
        a.setData({
            helpStatus: !1
        }), a.data.comment = {
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
    onToggleDialog: function() {
        this.setData({
            dialogShow: !1
        });
    },
    onShareAppMessage: function(a) {
        return this.data.sharedata;
    }
});