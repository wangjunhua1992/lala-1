var t = getApp();

Page({
    data: {
        islegal: !1,
        tagsSelected: {
            title: "",
            tags: []
        },
        goods_quality: 0,
        note: "",
        rate: [ 1, 2, 3, 4, 5 ],
        files: [],
        Lang: t.Lang,
        wuiLoading: {
            show: !0,
            img: t.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(a) {
        var e = this;
        a && a.order_id && (e.data.order_id = a.order_id), t.util.request({
            url: "gohome/comment/comment",
            data: {
                order_id: e.data.order_id
            },
            success: function(a) {
                t.util.loaded();
                var s = a.data.message;
                if (s.errno) return t.util.toast(s.message), !1;
                s = s.message, e.setData({
                    order: s.order,
                    islegal: !0,
                    goods_tags: s.goods_tags
                });
            }
        });
    },
    onRateChange: function(t) {
        var a = this, e = t.currentTarget.dataset.value;
        if (e != a.data.goods_quality) {
            var s = {
                title: a.data.goods_tags[e].title,
                tags: a.data.goods_tags[e].tags
            };
            a.setData({
                goods_quality: e,
                tagsSelected: s
            });
        }
    },
    onNote: function(t) {
        this.setData({
            note: t.detail.value
        });
    },
    onToggleTag: function(t) {
        var a = this, e = t.currentTarget.dataset.index;
        a.data.tagsSelected.tags[e].active = !a.data.tagsSelected.tags[e].active + 0, a.setData({
            tagsSelected: a.data.tagsSelected
        });
    },
    onSubmit: function(a) {
        var e = this;
        if (e.data.islegal) if (e.data.goods_quality) {
            e.setData({
                islegal: !1
            });
            var s = [], o = e.data.files;
            if (o.length > 0) for (var i in o) s.push(o[i].filename);
            var d = {
                order_id: e.data.order_id,
                note: e.data.note,
                goods_quality: e.data.goods_quality,
                thumbs: JSON.stringify(e.data.files),
                tags: JSON.stringify(e.data.tagsSelected.tags)
            };
            console.log(d), t.util.request({
                method: "POST",
                url: "gohome/comment/comment",
                data: d,
                success: function(a) {
                    var e = a.data.message;
                    if (e.errno) return t.util.toast(e.message), !1;
                    t.util.toast("评价成功", "/gohome/pages/order/index", 1500);
                }
            });
        } else t.util.toast("请对商品质量进行评分");
    },
    onJsEvent: function(a) {
        t.util.jsEvent(a);
    }
});