var a = getApp();

Page({
    data: {
        records: {
            page: 1,
            psize: 15,
            empty: !1,
            loaded: !1,
            data: []
        },
        navs: [],
        failedTips: {
            type: "message",
            tips: "",
            btnText: "关闭",
            link: "/pages/home/index"
        },
        black_member: {},
        failedTipsStatus: !1,
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function() {
        this.onReachBottom();
    },
    onReachBottom: function() {
        var e = this;
        e.data.records.loaded || a.util.request({
            url: "kanjia/activity/index",
            data: {
                page: e.data.records.page,
                psize: e.data.records.psize,
                menufooter: 1,
                forceLocation: 1
            },
            success: function(t) {
                a.util.loaded();
                var s = t.data.message;
                if (s.errno) return -1e3 == s.errno ? (e.data.black_member = s.message.black_member, 
                e.data.failedTips.tips = e.data.black_member.tip, void e.setData({
                    failedTipsStatus: !0,
                    black_member: e.data.black_member,
                    failedTips: e.data.failedTips
                })) : 41200 == s.errno ? void e.setData({
                    failedTipsStatus: !0,
                    failedTips: {
                        type: "message",
                        tips: s.message,
                        btnText: "切换地址",
                        link: "/pages/home/location?from=kanjia",
                        img: "http://cos.lalawaimai.com/we7_wmall/wxapp/store_no_con.png"
                    }
                }) : (a.util.toast(s.message), !1);
                s = s.message, e.data.records.data = e.data.records.data.concat(s.records), e.data.records.data.length || (e.data.records.empty = !0), 
                s.records && s.records.length < e.data.records.psize && (e.data.records.loaded = !0), 
                e.data.records.page++, e.setData({
                    navs: s.navs,
                    records: e.data.records
                });
            }
        });
    },
    onPullDownRefresh: function() {
        var a = this;
        a.data.records = {
            page: 1,
            psize: 15,
            empty: !1,
            loaded: !1,
            data: []
        }, a.onReachBottom(), wx.stopPullDownRefresh();
    },
    onJsEvent: function(e) {
        a.util.jsEvent(e);
    }
});