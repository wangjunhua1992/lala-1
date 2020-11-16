var a = getApp();

Page({
    data: {
        extra: {
            dayIndex: 0
        },
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(t) {
        var e = this;
        e.data.sid = t.sid || 3;
        var n = {
            sid: e.data.sid
        };
        a.util.request({
            url: "wmall/store/reserve/index",
            data: n,
            success: function(t) {
                if (a.util.loaded(), 0 != (t = t.data.message).errno) return a.util.toast(t.message), 
                !1;
                e.data.extra.day = t.message.year + "-" + t.message.days[0].day, t.message.extra = e.data.extra, 
                e.setData(t.message);
            }
        });
        var d = wx.getSystemInfoSync().windowWidth;
        e.setData({
            window_Width: d
        });
    },
    onSelectDay: function(a) {
        var t = this, e = a.currentTarget.dataset.value, n = a.currentTarget.dataset.index, d = 33.333333333 * t.data.window_Width / 100;
        if (n >= 1 && n <= 4) var r = (n - 1) * d;
        t.setData({
            "extra.day": t.data.year + "-" + e,
            "extra.dayIndex": n,
            scroll_left: r
        });
    },
    onSelectTime: function(t) {
        var e = this, n = t.currentTarget.dataset.time, d = t.currentTarget.dataset.cid;
        e.setData({
            "extra.time": n,
            "extra.cid": d
        }), e.data.extra.day || (e.data.extra.day = e.data.year + "-" + e.data.days[e.data.extra.dayIndex].day), 
        a.util.setStorageSync("reserve.extra", e.data.extra), wx.navigateTo({
            url: "./create?sid=" + e.data.sid
        });
    },
    onSelectOutTime: function() {
        a.util.toast("该时间不能预定点餐");
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});