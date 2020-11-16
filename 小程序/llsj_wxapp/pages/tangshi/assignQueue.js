var t = getApp();

Page({
    data: {
        Lang: t.Lang,
        starttime: "00:00",
        endtime: "23:00"
    },
    onLoad: function(t) {},
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {},
    onSubmit: function(e) {
        var n = this, a = e.detail.value;
        return a.title ? a.guest_num ? a.prefix ? a.notify_num ? (a.starttime = n.data.starttime, 
        a.endtime = n.data.endtime, void t.util.request({
            url: "manage/tangshi/assign/queue_post",
            data: {
                title: a.title,
                guest_num: a.guest_num,
                prefix: a.prefix,
                notify_num: a.notify_num,
                starttime: a.starttime,
                endtime: a.endtime,
                formid: e.detail.formId
            },
            method: "POST",
            success: function(e) {
                var n = e.data.message;
                if (t.util.toast(n.message), n.errno) return !1;
                t.util.toast("添加队列成功", "./assign", 1e3);
            }
        })) : (t.util.toast("提前通知人数必须大于0", "", 1e3), !1) : (t.util.toast("队列编号前缀不能为空", "", 1e3), 
        !1) : (t.util.toast("客人数量少于等于多少人排入此队列必须大于0", "", 1e3), !1) : (t.util.toast("队列名称不能为空", "", 1e3), 
        !1);
    },
    onTimeChange: function(t) {
        var e = this, n = t.currentTarget.dataset.index, a = t.detail.value;
        e.data[n] = a, e.setData(e.data);
    }
});