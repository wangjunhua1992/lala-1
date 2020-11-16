var t = getApp();

Page({
    data: {
        Lang: t.Lang,
        cid: 0
    },
    onLoad: function(a) {
        var n = this;
        n.data.options = a, t.util.request({
            url: "manage/tangshi/table/index",
            data: {
                cid: a.cid || 0
            },
            success: function(a) {
                var s = a.data.message;
                if (s.errno) return t.util.toast(s.message), !1;
                n.setData(s.message), n.data.options.cid && n.setData({
                    cid: n.data.options.cid
                });
            }
        });
    },
    onChangeStatus: function(t) {
        var a = t.currentTarget.dataset.cid;
        this.setData({
            cid: a
        }), this.onLoad({
            cid: a
        });
    },
    onJsEvent: function(a) {
        t.util.jsEvent(a);
    },
    onPullDownRefresh: function() {
        this.onLoad({
            cid: this.data.cid
        }), wx.stopPullDownRefresh();
    }
});