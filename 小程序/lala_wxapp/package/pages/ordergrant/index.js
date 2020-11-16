var e = getApp();

Page({
    data: {
        agreement: !1,
        Lang: e.Lang,
        wuiLoading: {
            show: !0,
            img: e.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(t) {
        var a = this;
        e.util.request({
            url: "ordergrant/index",
            success: function(t) {
                e.util.loaded(), console.log("======================"), console.log(t.data.message);
                var o = t.data.message;
                e.WxParse.wxParse("richtext", "html", o.message.config_ordergrant.agreement, a, 5), 
                a.setData(o.message);
            }
        });
    },
    onRewardClick: function(t) {
        var a = this;
        e.util.request({
            url: "ordergrant/index",
            success: function(e) {
                console.log(e.data.message);
                var t = e.data.message;
                a.setData(t.message);
            }
        });
    },
    onShowAgreement: function() {
        console.log("++++++++++++++++++++++++");
        var e = this, t = e.data.agreement;
        e.setData({
            agreement: !e.data.agreement
        }), console.log(t);
    },
    onDayClick: function(t) {
        var a = this;
        console.log(t.currentTarget.dataset);
        var o = t.currentTarget.dataset, n = o.day, s = o.grant, r = new Date().getDate(), g = n - r;
        console.log("++++++++++++"), console.log(r);
        var c = {
            grant: s,
            difference: g
        };
        e.util.request({
            url: "ordergrant/index/next",
            data: c,
            success: function(t) {
                e.WxParse.wxParse("toast", "html", t.data.message.message, a, 5), a.setData({
                    toastShow: !0
                });
            }
        });
    },
    onGetReward: function(t) {
        var a = this;
        console.log("*************"), console.log(t);
        var o = t.target.dataset, n = o.days, s = o.type, r = o.status, g = o.index;
        if (console.log(r), !s || !n) return e.util.toast("您还没有达到领取该奖励的条件", "", "1000"), 
        !1;
        var c = {
            days: n,
            type: s
        };
        e.util.request({
            url: "ordergrant/index/get",
            data: c,
            method: "POST",
            success: function(t) {
                e.util.toast(t.data.message.message), console.log("++++++++++------------"), console.log(a.data.config_ordergrant.all[g].status), 
                1 == r && (1 == s ? (a.data.config_ordergrant.continuous[g].status = 2, a.setData({
                    config_ordergrant: a.data.config_ordergrant
                })) : 2 == s && (a.data.config_ordergrant.all[g].status = 2, a.setData({
                    config_ordergrant: a.data.config_ordergrant
                })));
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