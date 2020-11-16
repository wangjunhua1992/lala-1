var a = getApp();

Page({
    data: {
        id: 0,
        order: {},
        steps: [],
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(t) {
        var e = this;
        if (!(t && t.id > 0)) return a.util.toast("订单不存在或已删除！"), !1;
        e.data.id = t.id, a.util.request({
            url: "wmall/order/index/zhunshibao",
            data: {
                id: e.data.id
            },
            success: function(t) {
                a.util.loaded();
                var s = t.data.message;
                if (s.errno) return a.util.toast(s.message, "/pages/order/detail?id=" + e.data.id, 3e3), 
                !1;
                var i = s.message.order;
                if (5 == i.status) {
                    if (1 == i.zhunshibao_status) d = "订单已送达，本次配送未达到赔付标准"; else if (2 == i.zhunshibao_status) d = "订单已送达，等待赔付" + i.zhunshibao_compensate + e.data.Lang.dollarSignCn; else if (3 == i.zhunshibao_status) d = "订单已送达，已赔付" + i.zhunshibao_compensate + e.data.Lang.dollarSignCn; else if (4 == i.zhunshibao_status) d = "订单已送达，达到赔付标准， 获赔" + i.zhunshibao_compensate + e.data.Lang.dollarSignCn + "，平台设置为已赔付";
                    var n = i.endtime_cn;
                } else if (6 == i.status) d = "已退保"; else var d = "准时宝生效中，订单完成后结算";
                e.data.steps = [ {
                    text: d,
                    desc: n
                }, {
                    text: "预计送达",
                    desc: i.prredict_time_cn
                } ], e.setData({
                    order: s.message.order,
                    steps: e.data.steps
                });
            }
        });
    },
    onJsEvent: function(t) {
        a.util.jsEvent(t);
    }
});