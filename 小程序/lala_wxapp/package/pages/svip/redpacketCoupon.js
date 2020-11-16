var e = getApp();

Page({
    data: {
        redpackets: {
            page: 1,
            psize: 10,
            empty: !1,
            loaded: !1,
            data: []
        },
        exchange_cost: -1,
        can_exchange: 0,
        exchange_max: 0,
        member: {},
        month: {},
        member_redpackets: [],
        redpacketActive: {},
        showRedpacket: !1,
        islegal: !1,
        Lang: e.Lang,
        wuiLoading: {
            show: !0,
            img: e.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(a) {
        var t = this;
        a && a.exchange_cost && t.setData({
            exchange_cost: a.exchange_cost
        }), e.util.request({
            url: "svip/redpacket/list",
            data: {
                page: t.data.redpackets.page,
                psize: t.data.redpackets.psize,
                exchange_cost: t.data.exchange_cost,
                menufooter: 1
            },
            success: function(a) {
                e.util.loaded();
                var s = a.data.message;
                if (s.errno) return e.util.toast(s.message), !1;
                s = s.message, t.data.redpackets.data = t.data.redpackets.data.concat(s.redpackets), 
                t.data.redpackets.data.length || (t.data.redpackets.empty = !0), s.redpackets && s.redpackets.length < t.data.redpackets.psize && (t.data.redpackets.loaded = !0), 
                t.data.redpackets.page++, s.islegal = !0, s.redpackets = t.data.redpackets, t.setData(s);
            }
        });
    },
    onReachBottom: function(a) {
        var t = this;
        !0 === a && (t.data.redpackets = {
            page: 1,
            psize: 10,
            empty: !1,
            loaded: !1,
            data: []
        }), 1 != t.data.redpackets.loaded && e.util.request({
            url: "svip/redpacket/more",
            data: {
                page: t.data.redpackets.page,
                psize: t.data.redpackets.psize,
                exchange_cost: t.data.exchange_cost
            },
            success: function(a) {
                var s = a.data.message;
                if (s.errno) return e.util.toast(s.message), !1;
                s = s.message, t.data.redpackets.data = t.data.redpackets.data.concat(s.redpackets), 
                t.data.redpackets.data.length || (t.data.redpackets.empty = !0), s.redpackets && s.redpackets.length < t.data.redpackets.psize && (t.data.redpackets.loaded = !0), 
                t.data.redpackets.page++, t.setData({
                    redpackets: t.data.redpackets
                });
            }
        });
    },
    onShowRedpacket: function(a) {
        var t = this;
        e.util.request({
            url: "svip/redpacket/redpacket",
            data: {
                id: a.currentTarget.dataset.id
            },
            success: function(a) {
                var s = a.data.message;
                if (s.errno) return e.util.toast(s.message), !1;
                t.setData({
                    redpacketActive: s.message.redpacket
                });
            }
        }), t.onTogglePopup();
    },
    onTogglePopup: function() {
        var e = this;
        e.setData({
            showRedpacket: !e.data.showRedpacket
        });
    },
    onToggleExchangeCost: function(e) {
        var a = this, t = e.currentTarget.dataset.exchangeCost;
        a.data.exchange_cost != t && (a.data.exchange_cost = e.currentTarget.dataset.exchangeCost, 
        a.setData({
            exchange_cost: a.data.exchange_cost
        }), a.onReachBottom(!0));
    },
    onExchange: function(a) {
        var t = this, s = a.currentTarget.dataset.redpacket;
        !t.data.islegal || 1 == t.data.exchange_cost && t.data.redpacketActive.exchange_cost > t.data.member.svip_credit1 || (t.data.islegal = !1, 
        e.util.request({
            url: "svip/redpacket/exchange",
            data: {
                id: s.id,
                exchange_cost: t.data.exchange_cost
            },
            success: function(a) {
                var s = a.data.message;
                if (s.errno) return e.util.toast(s.message), t.onTogglePopup(), t.setData({
                    islegal: !0
                }), !1;
                var c = "领取成功";
                1 == t.data.exchange_cost && (c = "兑换成功"), e.util.toast(c), s = s.message, t.setData({
                    member_redpackets: s.member_redpackets,
                    "member.svip_credit1": s.svip_credit1,
                    can_exchange: s.can_exchange,
                    islegal: !0
                }), t.onTogglePopup();
            }
        }));
    },
    onJsEvent: function(a) {
        e.util.jsEvent(a);
    }
});