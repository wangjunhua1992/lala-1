var t = getApp();

Page({
    data: {
        Lang: t.Lang
    },
    onLoad: function(a) {
        var e = this;
        t.util.request({
            url: "manage/statcenter/statistic",
            data: {
                days: a.days || 0
            },
            success: function(a) {
                var s = a.data.message;
                if (s.errno) return t.util.toast(s.message), !1;
                e.setData(s.message);
            }
        });
    },
    onChangeStatus: function(t) {
        var a = t.currentTarget.dataset.days;
        this.setData({
            days: a
        }), this.onLoad({
            days: a
        });
    },
    changeStartTime: function(t) {
        this.setData({
            startTime: t.detail.value
        });
    },
    changeEndTime: function(t) {
        this.setData({
            endTime: t.detail.value
        });
    },
    onSubmit: function(a) {
        var e = this, s = a.detail.value;
        if (!s.start) return t.util.toast("开始时间不能为空", "", 1e3), !1;
        if (!s.end) return t.util.toast("结束时间不能为空", "", 1e3), !1;
        var n = {
            start: s.start,
            end: s.end,
            days: "-1"
        };
        t.util.request({
            url: "manage/statcenter/statistic",
            data: n,
            method: "POST",
            success: function(a) {
                0 == a.data.message.errno ? e.setData(a.data.message.message) : t.util.toast(a.data.message.message);
            }
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});