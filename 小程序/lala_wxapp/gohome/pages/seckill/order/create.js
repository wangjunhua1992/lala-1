var t = getApp();

Page({
    data: {
        goods_id: 0,
        goodsNum: 1,
        buyremark: "",
        islegal: !1,
        totalPrice: 0,
        Lang: t.Lang,
        wuiLoading: {
            show: !0,
            img: t.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(o) {
        var a = this;
        o && o.goods_id && (a.data.goods_id = o.goods_id), t.util.request({
            url: "seckill/order/create",
            data: {
                goods_id: a.data.goods_id
            },
            success: function(o) {
                t.util.loaded();
                var e = o.data.message;
                if (e.errno) return t.util.toast(e.message), !1;
                e = e.message, a.setData({
                    goods: e.goods,
                    islegal: !0,
                    username: e.member.username,
                    mobile: e.member.mobile
                }, function() {
                    a.onComputeTotalPrice();
                });
            }
        });
    },
    onMinus: function() {
        var o = this;
        0 != o.data.goods.total && (o.data.goodsNum <= 1 ? t.util.toast("数量最小为1") : o.setData({
            goodsNum: o.data.goodsNum - 1
        }, function() {
            o.onComputeTotalPrice();
        }));
    },
    onPlus: function() {
        var o = this;
        0 != o.data.goods.total && (o.data.goods.total > 0 && o.data.goodsNum >= o.data.goods.total ? t.util.toast("数量已达上限") : o.setData({
            goodsNum: o.data.goodsNum + 1
        }, function() {
            o.onComputeTotalPrice();
        }));
    },
    onInput: function(o) {
        var a = this, e = o.detail.value;
        e < 0 && (e = 0), a.data.goods.total > 0 && e - a.data.goods.total > 0 && (t.util.toast("数量已达上限"), 
        e = a.data.goods.total), a.setData({
            goodsNum: e
        }), a.onComputeTotalPrice();
    },
    onNote: function(t) {
        this.setData({
            buyremark: t.detail.value
        });
    },
    onSubmit: function(o) {
        var a = this;
        if (a.data.islegal) {
            var e = o.detail.value;
            a.data.goodsNum <= 0 ? t.util.toast("请选择购买数量") : e.username ? e.mobile ? t.util.isMobile(e.mobile) ? (a.setData({
                islegal: !1
            }), t.util.request({
                method: "POST",
                url: "seckill/order/create",
                data: {
                    goods_id: a.data.goods_id,
                    buyremark: a.data.buyremark,
                    username: e.username,
                    mobile: e.mobile,
                    goods_num: a.data.goodsNum,
                    formid: o.detail.formId
                },
                success: function(o) {
                    var a = o.data.message;
                    if (a.errno) return t.util.toast(a.message), !1;
                    var e = a.message;
                    t.util.toast("下单成功", "/pages/public/pay?order_id=" + e + "&order_type=gohome", 1500);
                }
            })) : t.util.toast("手机号格式错误") : t.util.toast("请输入核销人手机号") : t.util.toast("请输入核销人姓名");
        }
    },
    onJsEvent: function(o) {
        t.util.jsEvent(o);
    },
    onComputeTotalPrice: function() {
        var t = this, o = t.data.goods.price * t.data.goodsNum;
        o = o.toFixed(2), t.setData({
            totalPrice: o
        });
    }
});