var e = getApp();

Page({
    data: {
        partakers: {
            loaded: 0,
            empty: 0,
            data: []
        },
        Lang: e.Lang,
        wuiLoading: {
            show: !0,
            img: e.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(e) {
        var a = this, t = e.type ? e.type : "common";
        a.setData({
            type: t
        }), a.onGetFreelunch();
    },
    onGetFreelunch: function() {
        var a = this, t = a.data.type;
        e.util.request({
            url: "freeLunch/freeLunch",
            data: {
                type: t
            },
            success: function(r) {
                e.util.loaded();
                var n = r.data.message;
                if (n.errno) return e.util.toast(n.message), !1;
                if ("common" == t) s = n.message.freelunch.title; else var s = n.message.freelunch.title + "Plus";
                a.setData({
                    title: s,
                    freelunch: n.message.freelunch,
                    record: n.message.record,
                    partake_status: n.message.partake_status,
                    member_partaker: n.message.member_partaker,
                    luckiers: n.message.luckiers
                }), wx.setNavigationBarTitle({
                    title: s
                }), a.onReachBottom();
            }
        });
    },
    onJsEvent: function(a) {
        e.util.jsEvent(a);
    },
    onSubmit: function() {
        var a = this;
        e.util.request({
            url: "freeLunch/freeLunch/partake",
            data: {
                record_id: a.data.record.id
            },
            success: function(a) {
                var t = a.data.message;
                if (t.errno) return e.util.toast(t.message), !1;
                console.log(t);
                var r = t.message;
                return wx.showToast({
                    success: function() {
                        wx.navigateTo({
                            url: "../../../pages/public/pay?order_id=" + r + "&order_type=freelunch"
                        });
                    }
                }), !1;
            }
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {
        var e = this;
        e.data.min = 0, e.data.partakers = {
            loaded: 0,
            empty: 0,
            data: []
        }, e.onGetFreelunch(), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {
        var a = this;
        if (-1 == a.data.min) return !1;
        e.util.request({
            url: "freeLunch/freeLunch/partakers",
            data: {
                min: a.data.min,
                record_id: a.data.record.id
            },
            success: function(e) {
                var t = a.data.partakers.data.concat(e.data.message.message);
                a.data.partakers.data = t, t.length || (a.data.partakers.empty = 1);
                var r = e.data.message.min;
                !r && t.length > 0 && (a.data.partakers.loaded = 1), r || (r = -1), console.log(a.data.partakers), 
                a.setData({
                    partakers: a.data.partakers,
                    min: r
                });
            }
        });
    },
    onShareAppMessage: function() {
        var e = that.data.freelunch.share;
        return {
            title: e.title,
            desc: e.desc,
            imageUrl: e.imgUrl,
            path: e.link,
            success: function() {},
            fail: function() {}
        };
    }
});