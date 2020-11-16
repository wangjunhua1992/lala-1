var e = getApp();

Page({
    data: {
        Lang: e.Lang,
        wuiLoading: {
            show: !0,
            img: e.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(t) {
        var s = this;
        s.data.sid = t.sid, e.util.request({
            url: "wmall/store/assign/index",
            data: {
                sid: t.sid
            },
            success: function(t) {
                e.util.loaded();
                var a = t.data.message;
                0 == a.errno ? s.setData(a.message) : -1e3 == a.errno ? e.util.toast(a.message, "redirect:/pages/store/index?sid=" + s.data.sid, 1e3) : 1e3 == a.errno ? e.util.toast(a.message, "redirect:/tangshi/pages/assign/assignDetail?sid=" + s.data.sid, 1e3) : -1 == a.errno && e.util.toast(a.message, "switchTab:/pages/home/index", 1e3);
            }
        });
    },
    onSelectQueue: function(e) {
        console.log(e.currentTarget.dataset.index), this.setData({
            queueid_select: e.currentTarget.dataset.index
        });
    },
    onSubmit: function() {
        var t = this;
        e.util.request({
            url: "wmall/store/assign/index",
            data: {
                sid: t.data.store.id,
                queue_id: t.data.queueid_select
            },
            method: "POST",
            success: function(s) {
                var a = s.data.message;
                if (a.errno) return e.util.toast(a.message, "", 1e3), !1;
                e.util.toast(a.message, "redirect:/tangshi/pages/assign/assignDetail?sid=" + t.data.store.id, 1e3);
            }
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {
        this.onLoad({
            sid: this.data.store.id
        }), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});