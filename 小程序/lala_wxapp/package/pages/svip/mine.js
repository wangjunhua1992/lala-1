var a = getApp();

Page({
    data: {
        member: {},
        goods: [],
        redpackets: [],
        tasks: [],
        islegal: !1,
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(e) {
        var t = this;
        a.util.request({
            url: "svip/mine",
            data: {
                menufooter: 1
            },
            success: function(e) {
                a.util.loaded();
                var s = e.data.message;
                if (s.errno) {
                    if (-2 != s.errno) return a.util.toast(s.message), !1;
                    a.util.toast(s.message, "/package/pages/svip/index", 1500);
                }
                (s = s.message).islegal = !0, t.setData(s);
            }
        });
    },
    onExchange: function(e) {
        var t = this, s = e.currentTarget.dataset.id;
        t.data.islegal && wx.showModal({
            content: "确认领取该红包吗",
            success: function(e) {
                e.confirm ? (t.data.islegal = !1, a.util.request({
                    url: "svip/mine/exchange",
                    data: {
                        id: s,
                        exchange_cost: -1
                    },
                    success: function(e) {
                        var s = e.data.message;
                        if (s.errno) return a.util.toast(s.message), t.setData({
                            islegal: !0
                        }), !1;
                        a.util.toast("领取成功"), s = s.message, t.setData({
                            "member.num_taked": s.num_taked,
                            "member.total_discount": s.total_discount,
                            islegal: !0
                        });
                    }
                })) : e.cancel;
            }
        });
    },
    onTakepartTask: function(e) {
        var t = this, s = e.currentTarget.dataset;
        if (1 != s.link_type) {
            var i = s.id;
            t.data.islegal && (t.data.islegal = !1, a.util.request({
                url: "svip/task/takepart",
                data: {
                    id: i
                },
                success: function(e) {
                    var s = e.data.message;
                    return a.util.toast(s.message), t.setData({
                        islegal: !0
                    }), s.errno || t.onLoad(), !1;
                }
            }));
        } else a.util.jump2url(s.link, "navigateTo");
    },
    onJsEvent: function(e) {
        a.util.jsEvent(e);
    }
});