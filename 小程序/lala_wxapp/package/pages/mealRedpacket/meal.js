var a = getApp();

Page({
    data: {
        mealRedpacketId: 0,
        canBuy: !1,
        userfulNum: 0,
        redpackets: [],
        exchanges: {
            page: 2,
            psize: 15,
            loaded: !1,
            empty: !1,
            data: []
        },
        params: {},
        style: {},
        selectedRedpacketId: 0,
        selectedSid: 0,
        islegal: !1,
        dialogStatus: !1,
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(e) {
        var t = this;
        a.util.request({
            url: "mealRedpacket/meal",
            success: function(e) {
                a.util.loaded();
                var s = e.data.message;
                return s.errno ? (a.util.toast(s.message), !1) : 0 == s.message.mealRedpacket ? (a.util.toast("暂无套餐红包活动"), 
                !1) : (s = s.message, t.data.exchanges.data = t.data.exchanges.data.concat(s.exchanges), 
                s.exchanges.length < t.data.exchanges.psize && (t.data.exchanges.loaded = !0), t.data.exchanges.data.length || (t.data.exchanges.empty = !0), 
                void t.setData({
                    canBuy: s.canBuy,
                    usefulNum: s.useful_num,
                    redpackets: s.redpackets,
                    exchanges: t.data.exchanges,
                    mealRedpacketId: s.mealRedpacket.id,
                    params: s.mealRedpacket.data.params,
                    style: s.mealRedpacket.data.style,
                    islegal: !0
                }));
            }
        });
    },
    onSubmit: function() {
        var e = this;
        if (!e.data.islegal) return !1;
        if (e.setData({
            islegal: !1
        }), !e.data.canBuy) return a.util.toast("本月购买次数已用完"), !1;
        var t = {
            mealRedpacket_id: parseInt(e.data.mealRedpacketId),
            final_fee: parseFloat(e.data.params.price)
        };
        a.util.request({
            url: "mealRedpacket/meal/submit",
            data: t,
            success: function(e) {
                var t = e.data.message;
                if (t.errno) return a.util.toast(t.message), !1;
                var s = t.message;
                return wx.showToast({
                    title: "下单成功",
                    success: function() {
                        wx.navigateTo({
                            url: "../../../pages/public/pay?order_id=" + s + "&order_type=mealRedpacket"
                        });
                    }
                }), !1;
            }
        });
    },
    onToggleDialog: function() {
        var a = this;
        a.setData({
            dialogStatus: !a.data.dialogStatus
        });
    },
    onDialogShow: function(e) {
        var t = this;
        return 1 == t.data.params.exchangeStatus && (t.data.canBuy ? (a.util.toast("请购买套餐红包后再进行兑换操作"), 
        !1) : (t.data.selectedSid = e.currentTarget.dataset.sid, void t.onToggleDialog()));
    },
    onSelectRedpacket: function(e) {
        var t = this, s = e.currentTarget.dataset.sid, d = e.currentTarget.dataset.status, n = e.currentTarget.dataset.id;
        return "paotui" == e.currentTarget.dataset.scene ? (a.util.toast("无法使用跑腿红包进行升级"), 
        !1) : 1 != d ? (a.util.toast("请选择有效的红包进行升级"), !1) : s > 0 ? (a.util.toast("无法使用已兑换的红包进行升级"), 
        !1) : void t.setData({
            selectedRedpacketId: n
        });
    },
    onExchange: function() {
        var e = this, t = {
            redpacket_id: parseInt(e.data.selectedRedpacketId),
            sid: parseInt(e.data.selectedSid),
            mealRedpacket_id: parseInt(e.data.mealRedpacketId)
        };
        a.util.request({
            url: "mealRedpacket/meal/do_exchange",
            data: t,
            success: function(t) {
                var s = t.data.message;
                if (a.util.toast(s.message), s.errno) return !1;
                e.onPullDownRefresh();
            }
        });
    },
    onPullDownRefresh: function() {
        var a = this;
        a.setData({
            mealRedpacketId: 0,
            canBuy: !1,
            userfulNum: 0,
            redpackets: [],
            exchanges: {
                page: 2,
                psize: 15,
                loaded: !1,
                empty: !1,
                data: []
            },
            params: {},
            style: {},
            selectedRedpacketId: 0,
            selectedSid: 0,
            islegal: !1,
            dialogStatus: !1
        }), a.onLoad(), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {
        var e = this;
        if (e.data.exchanges.loaded) return !1;
        a.util.request({
            url: "mealRedpacket/meal/exchange",
            data: {
                page: e.data.exchanges.page,
                psize: e.data.exchanges.psize,
                mealRedpacket_id: e.data.mealRedpacketId
            },
            success: function(t) {
                var s = t.data.message;
                if (s.errno) return a.util.toast(s.message), !1;
                var d = s.message.exchanges;
                e.data.exchanges.data = e.data.exchanges.data.concat(d), d.length < e.data.exchanges.psize && (e.data.exchanges.loaded = !0), 
                0 == e.data.exchanges.data.length && (e.data.exchanges.empty = !0), e.data.exchanges.page++, 
                e.setData({
                    exchanges: e.data.exchanges
                });
            }
        });
    },
    onJsEvent: function(e) {
        a.util.jsEvent(e);
    }
});