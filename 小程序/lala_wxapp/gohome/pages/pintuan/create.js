var a = getApp();

Page({
    data: {
        team_id: 0,
        is_team: 0,
        islegal: !1,
        order: {},
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(t) {
        var e = this;
        t && t.id && (e.data.id = t.id, e.data.is_team = t.is_team, t.team_id > 0 && (e.data.team_id = t.team_id, 
        e.data.is_team = 1)), a.util.request({
            url: "pintuan/create/index",
            data: {
                id: e.data.id,
                team_id: e.data.team_id,
                is_team: e.data.is_team,
                extra: {
                    goods_num: e.data.order.goods_num
                }
            },
            success: function(t) {
                a.util.loaded();
                var d = t.data.message;
                if (d.errno) return -1001 == d.errno ? (a.util.toast("您已参加该活动", "/gohome/pages/pintuan/share?id=" + e.data.id + "&team_id=" + e.data.team_id, 1e3), 
                !1) : -1e3 == d.errno ? (a.util.jump2url("/gohome/pages/pintuan/detail?id=" + e.data.id, "navigateTo"), 
                !1) : (a.util.toast(d.message, "/gohome/pages/pintuan/index", 1e3), !1);
                e.setData({
                    goods: d.message.goods,
                    order: d.message.order,
                    islegal: !0
                });
            }
        });
    },
    onNote: function(a) {
        this.setData({
            "order.remark": a.detail.value
        });
    },
    onMinus: function() {
        var t = this;
        t.data.order.goods_num <= 1 ? a.util.toast("数量最小为1") : (t.data.order.goods_num--, 
        t.onLoad());
    },
    onPlus: function() {
        var t = this;
        t.data.goods.total > 0 && t.data.order.goods_num >= t.data.goods.total ? a.util.toast("商品库存不足", "", 1e3) : t.data.order.goods_num >= t.data.goods.buylimit ? a.util.toast("本次活动限购" + t.data.goods.buylimit + t.data.goods.unit, "", 1e3) : (t.data.order.goods_num++, 
        t.onLoad());
    },
    onSubmit: function(t) {
        var e = this;
        if (e.data.islegal) {
            var d = t.detail.value;
            d.mobile ? (e.setData({
                islegal: !1
            }), d.is_team = e.data.is_team, d.remark = e.data.order.remark, a.util.request({
                method: "POST",
                url: "pintuan/create/create",
                data: {
                    id: e.data.id,
                    team_id: e.data.team_id,
                    extra: JSON.stringify(d)
                },
                success: function(t) {
                    var e = t.data.message;
                    if (e.errno) return a.util.toast(e.message), !1;
                    var d = e.message;
                    a.util.toast("下单成功", "/pages/public/pay?order_id=" + d + "&order_type=gohome", 1500);
                }
            })) : a.util.toast("请输入手机号");
        }
    },
    onJsEvent: function(t) {
        a.util.jsEvent(t);
    }
});