var e = getApp();

Page({
    data: {
        Lang: e.Lang
    },
    onLoad: function(t) {
        wx.removeStorageSync("timer");
        var n = this;
        e.util.request({
            url: "delivery/member/mine/index",
            success: function(t) {
                var o = t.data.message;
                if (o.errno) return e.util.toast(o.message), !1;
                o.message.wxappversion = e.util.wxappversion(), n.setData(o.message);
            }
        });
    },
    onJsEvent: function(t) {
        e.util.jsEvent(t);
    },
    onPullDownRefresh: function() {
        this.onLoad(), wx.stopPullDownRefresh();
    },
    onClearStorage: function(t) {
        wx.showModal({
            title: "",
            content: "确定清除缓存吗？",
            success: function(t) {
                t.confirm && (e.util.followLocation(!0, !0), e.util.toast("清除缓存成功"));
            }
        });
    }
});