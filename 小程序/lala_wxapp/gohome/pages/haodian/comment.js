var a = getApp();

Page({
    data: {
        sid: 0,
        islegal: !1,
        files: [],
        note: "",
        haodianStar: 5,
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(t) {
        var e = this;
        t && t.sid && (e.data.sid = t.sid), a.util.request({
            url: "haodian/comment/index",
            data: {
                sid: e.data.sid
            },
            success: function(t) {
                a.util.loaded();
                var i = t.data.message;
                if (i.errno) return a.util.toast(i.message), !1;
                i = i.message, e.setData({
                    store: i.store,
                    islegal: !0
                });
            }
        });
    },
    onChange: function(a) {
        this.setData({
            haodianStar: a.detail
        });
    },
    onNote: function(a) {
        this.setData({
            note: a.detail.value
        });
    },
    onSubmit: function() {
        var t = this;
        if (t.data.islegal) if (t.data.note) {
            t.setData({
                islegal: !1
            });
            var e = {
                sid: t.data.sid,
                note: t.data.note,
                haodianStar: t.data.haodianStar,
                thumbs: JSON.stringify(t.data.files)
            };
            a.util.request({
                url: "haodian/comment/post",
                data: e,
                success: function(e) {
                    var i = e.data.message;
                    if (i.errno) return a.util.toast(i.message), !1;
                    a.util.toast("评价成功", "/gohome/pages/haodian/detail?sid=" + t.data.sid, 1500);
                }
            });
        } else a.util.toast("评论内容不能为空");
    },
    onJsEvent: function(t) {
        a.util.jsEvent(t);
    }
});