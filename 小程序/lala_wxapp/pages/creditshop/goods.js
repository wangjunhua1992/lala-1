var t = getApp();

Page({
    data: {
        goods: {
            page: 1,
            psize: 12,
            type: "",
            title: "",
            category_id: "",
            loaded: 0,
            empty: 0,
            data: []
        },
        Lang: t.Lang,
        wuiLoading: {
            show: !0,
            img: t.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(t) {
        var a = this;
        t && (a.data.goods.category_id = t.category_id), a.onReachBottom();
    },
    onReady: function() {},
    onInput: function(t) {
        this.setData({
            title: t.detail.value
        });
    },
    onSearchGoods: function() {
        var t = this;
        if (!t.data.title) return !1;
        t.data.goods.title = t.data.title, t.onGetGoods(!0);
    },
    onGetGoods: function(a) {
        var o = this;
        a && (o.data.goods = {
            page: 1,
            psize: 12,
            type: o.data.goods.type,
            title: o.data.goods.title,
            category_id: o.data.goods.category_id,
            loaded: 0,
            empty: 0,
            data: []
        }), t.util.request({
            url: "creditshop/index/goods",
            data: {
                page: o.data.goods.page,
                psize: o.data.goods.psize,
                type: o.data.goods.type,
                title: o.data.goods.title,
                category_id: o.data.goods.category_id
            },
            success: function(a) {
                t.util.loaded();
                var d = a.data.message.message;
                o.data.goods.data = o.data.goods.data.concat(d.goods), d.goods.length < o.data.goods.psize && (o.data.goods.loaded = 1, 
                o.data.goods.data.length || (o.data.goods.empty = 1)), o.data.goods.page++, o.setData({
                    goods: o.data.goods
                });
            }
        });
    },
    onReachBottom: function() {
        this.onGetGoods();
    }
});