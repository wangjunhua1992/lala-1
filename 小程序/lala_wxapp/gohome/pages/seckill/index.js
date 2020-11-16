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
            url: "seckill/goods/index",
            data: {
                page: e.data.records.page,
                psize: e.data.records.psize,
                menufooter: 1
            },
            success: function(t) {
                a.util.loaded();
                var o = t.data.message;
                if (o.errno) return 41200 == o.errno ? void e.setData({
                    failedTipsStatus: !0,
                    failedTips: {
                        type: "message",
                        tips: o.message,
                        btnText: "切换地址",
                        link: "/pages/home/location?from=seckill",
                        img: "http://cos.lalawaimai.com/we7_wmall/wxapp/store_no_con.png"
                    }
                }) : (a.util.toast(o.message), !1);
                o = o.message, e.data.records.data = e.data.records.data.concat(o.records), e.data.records.data.length || (e.data.records.empty = !0), 
                o.records && o.records.length < e.data.records.psize && (e.data.records.loaded = !0), 
                e.data.records.page++, e.setData({
                    records: e.data.records,
                    navs: o.navs
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