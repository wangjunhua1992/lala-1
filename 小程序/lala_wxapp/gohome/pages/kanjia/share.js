var a = getApp();

Page({
    data: {
        activityid: 0,
        uid: 0,
        rankType: "helper",
        bargainprice: 0,
        bargainSuccessPopup: !1,
        zhezhaoShow: !1,
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(t) {
        var i = this;
        t && t.activityid && (i.data.activityid = t.activityid), t && t.uid && (i.data.uid = t.uid), 
        a.util.request({
            url: "kanjia/activity/share",
            data: {
                activityid: i.data.activityid,
                uid: i.data.uid,
                page: 1,
                psize: 6
            },
            success: function(t) {
                a.util.loaded();
                var e = t.data.message;
                if (e.errno) return a.util.toast(e.message), !1;
                e = e.message, i.setData(e);
            }
        });
    },
    onToggleRank: function(a) {
        var t = this, i = a.currentTarget.dataset.type;
        i != t.data.rankType && t.setData({
            rankType: i
        });
    },
    onBargain: function() {
        var t = this;
        a.util.request({
            url: "kanjia/activity/bargain",
            data: {
                activityid: t.data.activityid,
                uid: t.data.uid
            },
            success: function(i) {
                var e = i.data.message;
                if (e.errno) return a.util.toast(e.message), !1;
                var n = e.message;
                t.data.takeinfo.price = n.afterprice, t.data.takeinfo.per_price = (t.data.activity.oldprice - t.data.takeinfo.price) / (t.data.activity.oldprice - t.data.activity.price) * 100, 
                t.data.takeinfo.helper.push(n), t.setData({
                    takeinfo: t.data.takeinfo,
                    bargainprice: n.bargainprice
                }), t.onTogglePopup();
            }
        });
    },
    onTogglePopup: function() {
        var a = this;
        a.setData({
            bargainSuccessPopup: !a.data.bargainSuccessPopup
        });
    },
    onPullDownRefresh: function() {
        this.onLoad(), wx.stopPullDownRefresh();
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
        return this.data.sharedata;
    }
});