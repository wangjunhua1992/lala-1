var t = getApp();

Page({
    data: {
        goods_id: 0,
        type: "",
        Lang: t.Lang,
        wuiLoading: {
            show: !0,
            img: t.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(a) {
        var e = this;
        a && a.goods_id && a.type && (e.data.goods_id = a.goods_id, e.data.type = a.type), 
        t.util.request({
            url: "gohome/poster/index",
            data: {
                goods_id: e.data.goods_id,
                type: e.data.type
            },
            success: function(a) {
                t.util.loaded();
                var o = a.data.message;
                if (o.errno) return t.util.toast(o.message), !1;
                e.setData(o.message);
            }
        });
    },
    onJsEvent: function(a) {
        t.util.jsEvent(a);
    }
});