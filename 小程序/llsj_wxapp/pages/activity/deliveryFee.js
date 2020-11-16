var t = getApp(), e = require("../../static/js/utils/dateTimePicker.js");

Page({
    data: {
        Lang: t.Lang,
        options: {},
        startYear: 2018,
        endYear: 2030
    },
    onLoad: function(a) {
        var i = this;
        t.util.request({
            url: "manage/activity/index/activity_other",
            data: {
                type: a.type
            },
            success: function(a) {
                var n = a.data.message;
                if (n.errno) return -1e3 == n.errno ? (t.util.toast(n.message, "redirect:/pages/activity/index", 1e3), 
                !1) : (t.util.toast(n.message), !1);
                var s = e.dateTimePicker(i.data.startYear, i.data.endYear), r = s.dateTimeArray, o = s.dateTime;
                r.pop(), o.pop(), i.data.starttime = r[0][o[0]] + "-" + r[1][o[1]] + "-" + r[2][o[2]] + " " + r[3][o[3]] + ":" + r[4][o[4]], 
                i.setData({
                    starttime: i.data.starttime,
                    endtime: i.data.starttime,
                    dateTime: o,
                    dateTimeArray: r,
                    type: n.message.type,
                    page_title: n.message.page_title,
                    discount_title: n.message.discount_title,
                    discount_cn: n.message.discount_cn
                }), wx.setNavigationBarTitle({
                    title: i.data.page_title
                });
            }
        });
    },
    changeDateTime: function(t) {
        this.setData({
            dateTime: t.detail.value
        });
    },
    changeDateTimeColumn: function(t) {
        var a = this, i = a.data.dateTime, n = a.data.dateTimeArray;
        i[t.detail.column] = t.detail.value, n[2] = e.getMonthDay(n[0][i[0]], n[1][i[1]]);
        var s = t.currentTarget.dataset.type, r = n[0][i[0]] + "-" + n[1][i[1]] + "-" + n[2][i[2]] + " " + n[3][i[3]] + ":" + n[4][i[4]];
        "starttime" == s ? a.setData({
            starttime: r
        }) : "endtime" == s && a.setData({
            endtime: r
        });
    },
    onInput: function(t) {
        var e = this, a = t.currentTarget.dataset.index, i = t.currentTarget.dataset.type, n = t.detail.value;
        e.data.options[a] || (e.data.options[a] = {}), e.data.options[a][i] = n;
    },
    onSubmit: function(e) {
        var a = this;
        if (!a.data.type) return t.util.toast("请选择活动类型", "", 1e3), !1;
        var i = {
            options: a.data.options,
            starttime: a.data.starttime,
            endtime: a.data.endtime
        };
        t.util.request({
            url: "manage/activity/index/activity_other",
            data: {
                type: a.data.type,
                params: JSON.stringify(i)
            },
            method: "POST",
            success: function(e) {
                var a = e.data.message;
                if (t.util.toast(a.message), a.errno) return !1;
                wx.navigateTo({
                    url: "./index"
                });
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