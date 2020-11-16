var t = getApp();

Page({
    data: {
        Lang: t.Lang,
        goodsTemp: {}
    },
    onLoad: function(e) {
        var a = this;
        a.data.options = e, t.util.request({
            url: "manage/goods/index/post",
            data: {
                id: e.id || 0
            },
            success: function(e) {
                var o = e.data.message;
                if (o.errno) return t.util.toast(o.message), !1;
                a.data.goodsTemp = o.message.goods, a.data.goodsTemp.unitnum || (a.data.goodsTemp.unitnum = 1), 
                a.setData({
                    goodsTemp: a.data.goodsTemp,
                    categorys: o.message.categorys,
                    type: o.message.type,
                    huangou_types: o.message.huangou_types
                });
            }
        });
    },
    onSelectCategory: function(t) {
        var e = this, a = t.detail.value, o = e.data.categorys[a];
        e.setData({
            "goodsTemp.cid": o.parentid > 0 ? o.parentid : o.id,
            "goodsTemp.child_id": o.parentid > 0 ? o.id : 0,
            "goodsTemp.category_title": o.title
        });
    },
    onSelectType: function(t) {
        var e = this, a = t.detail.value, o = e.data.type[a];
        e.setData({
            "goodsTemp.type": o.id,
            "goodsTemp.type_title": o.title
        });
    },
    onSelectHuangou: function(t) {
        var e = this, a = t.detail.value, o = e.data.huangou_types[a];
        e.setData({
            "goodsTemp.huangou_type": o.id,
            "goodsTemp.huangou_title": o.title
        });
    },
    switch1Change: function(t) {
        var e = this, a = t.currentTarget.dataset.name, o = t.detail.value;
        o = o ? 1 : 0, "status" == a ? e.setData({
            "goodsTemp.status": o
        }) : e.setData({
            "goodsTemp.is_hot": o
        });
    },
    onSelectGoodsImage: function() {
        var e = this;
        t.util.image({
            count: 1,
            success: function(t) {
                e.setData({
                    "goodsTemp.thumb_": t.url,
                    "goodsTemp.thumb": t.filename
                });
            }
        });
    },
    onSubmit: function(e) {
        var a = this, o = e.detail.value;
        return o.title ? o.price ? o.ts_price ? (o.cid = a.data.goodsTemp.cid, o.cid ? (o.type = a.data.goodsTemp.type, 
        o.type ? (o.huangou_type = a.data.goodsTemp.huangou_type, o.thumb = a.data.goodsTemp.thumb, 
        o.child_id = a.data.goodsTemp.child_id, void t.util.request({
            url: "manage/goods/index/post",
            data: {
                id: a.options.id || 0,
                params: JSON.stringify(o),
                formid: e.detail.formId
            },
            method: "POST",
            success: function(e) {
                var a = e.data.message;
                if (a.errno) return t.util.toast(a.message), !1;
                t.util.toast(a.message, "./index", 1e3);
            }
        })) : (t.util.toast("请选择商品所属类型", "", 1e3), !1)) : (t.util.toast("请选择商品所属分类", "", 1e3), 
        !1)) : (t.util.toast("商品店内价格不能为空", "", 1e3), !1) : (t.util.toast("商品外卖价格不能为空", "", 1e3), 
        !1) : (t.util.toast("商品名称不能为空", "", 1e3), !1);
    },
    onJsEvent: function(e) {
        t.util.jsEvent(e);
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});