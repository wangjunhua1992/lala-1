var t = getApp();

Page({
    data: {
        activeDelivery: [],
        goodsComment: [ "很差", "一般", "满意", "非常满意", "无可挑剔" ],
        tasteComment: [ "很差", "一般", "满意", "非常满意", "无可挑剔" ],
        packageComment: [ "很差", "一般", "满意", "非常满意", "无可挑剔" ],
        goodsStar: 0,
        tasteStar: 0,
        packageStar: 0,
        files: [],
        uploadImg: !1,
        comment: "",
        submit: !1,
        Lang: t.Lang,
        wuiLoading: {
            show: !0,
            img: t.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(a) {
        var e = this, r = a.id;
        t.util.request({
            url: "wmall/order/comment/index",
            data: {
                id: r,
                menufooter: 1
            },
            success: function(a) {
                t.util.loaded();
                var s = a.data.message.message;
                e.setData({
                    goodsList: s.goods,
                    deliveryer: s.deliveryer,
                    order: s.order,
                    store: s.store,
                    delivery: s.delivery
                }), e.setData({
                    id: r
                });
            }
        });
    },
    onReady: function() {},
    chooseTags: function(t) {
        var a = this, e = a.data.activeDelivery, r = t.currentTarget.dataset.id;
        1 == e.tags[r].selected ? e.tags[r].selected = 0 : e.tags[r].selected = 1, a.setData({
            activeDelivery: e
        });
    },
    onStarChange: function(t) {
        var a = this, e = t.currentTarget.dataset.id, r = t.currentTarget.dataset.type;
        if ("deliveryStar" == r) {
            var s = a.data.delivery[e];
            a.setData({
                activeDelivery: s
            });
        } else if ("goodsStar" == r) {
            var i = t.currentTarget.dataset.style;
            if ("goods" == i) d = a.data.goodsComment; else if ("taste" == i) d = a.data.tasteComment; else var d = a.data.packageComment;
            var o = d[e], c = e + 1;
            "goods" == i ? a.setData({
                goodsStar: c,
                goodsTitle: o
            }) : "taste" == i ? a.setData({
                tasteStar: c,
                tasteTitle: o
            }) : a.setData({
                packageStar: c,
                packageTitle: o
            });
        }
        (1 == a.data.order.order_type && a.data.activeDelivery.value && a.data.goodsStar && a.data.tasteStar && a.data.packageStar || a.data.order.order_type > 1 && a.data.goodsStar && a.data.tasteStar && a.data.packageStar) && a.setData({
            submit: !0
        }), console.log(a.data.submit);
    },
    onComment: function(t) {
        this.setData({
            comment: t.detail.value
        });
    },
    onJsEvent: function(a) {
        t.util.jsEvent(a);
    },
    favorOppose: function(t) {
        var a = this, e = a.data.goodsList, r = t.currentTarget.dataset.id;
        "favor" == t.currentTarget.dataset.type ? 0 == e[r].activity || 2 == e[r].activity ? e[r].activity = 1 : e[r].activity = 0 : 0 == e[r].activity || 1 == e[r].activity ? e[r].activity = 2 : e[r].activity = 0, 
        a.setData({
            goodsList: e
        });
    },
    onSubmit: function() {
        var a = this;
        if (!a.data.submit) return t.util.toast("请填写完整信息"), !1;
        var e = [];
        if (1 == a.data.order.order_type) for (var r = 0; r < a.data.activeDelivery.tags.length; r++) 1 == a.data.activeDelivery.tags[r].selected && e.push(a.data.activeDelivery.tags[r].name);
        var s = {
            id: a.data.id,
            note: a.data.comment,
            deliverScore: a.data.activeDelivery.value,
            goods_quality: a.data.goodsStar,
            tasteScore: a.data.tasteStar,
            packageScore: a.data.packageStar,
            thumbs: a.data.files,
            goods: a.data.goodsList,
            delivery_tags: e
        };
        t.util.request({
            url: "wmall/order/comment/post",
            data: s,
            success: function(e) {
                var r = e.data.message.message;
                t.util.toast(r, "/pages/order/detail?id=" + a.data.id);
            }
        });
    }
});