var a = getApp();

Page({
    data: {
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(a) {
        var t = this;
        a && (t.data.sid = a.sid, t.data.table_id = a.table_id, a.pindan_id > 0 && (t.data.pindan_id = a.pindan_id));
    },
    onGetPindan: function() {
        var t = this;
        a.util.request({
            url: "wmall/store/table/pindan",
            data: {
                sid: t.data.sid,
                pindan_id: t.data.pindan_id,
                table_id: t.data.table_id
            },
            success: function(i) {
                a.util.loaded();
                var e = i.data.message;
                if (e.errno) return a.util.toast(e.message, "redirect:/tangshi/pages/table/goods?sid=" + t.data.sid + "&table_id=" + t.data.table_id, 3e3), 
                !1;
                t.setData(e.message);
            }
        });
    },
    onTakePindan: function() {
        a.util.jump2url("/tangshi/pages/table/goods?sid=" + this.data.sid + "&table_id=" + this.data.table_id + "&pindan_id=" + this.data.pindan_id, "redirectTo");
    },
    onGiveUp: function(t) {
        var i = this, e = t.currentTarget.dataset.id, n = "确定不要继续点餐了吗？";
        1 == t.currentTarget.dataset.type && (n = "删除后不可恢复，确认删除吗？"), wx.showModal({
            title: "",
            content: n,
            success: function(t) {
                t.confirm ? a.util.request({
                    url: "wmall/store/table/giveupPindan",
                    data: {
                        sid: i.data.sid,
                        cart_id: e,
                        table_id: i.data.table_id
                    },
                    success: function(t) {
                        var n = t.data.message;
                        return n.errno ? -1e3 == n.errno ? (a.util.toast("取消点餐成功", "redirect:/pages/store/index?sid=" + i.data.sid, 1e3), 
                        !1) : (a.util.toast(n.message), !1) : e == i.data.pindan_id ? (a.util.toast("取消点餐成功", "redirect:/pages/store/index?sid=" + i.data.sid, 1e3), 
                        !1) : (a.util.toast("取消点餐成功", "", 1e3), n = n.message, void i.setData({
                            pindan: n.pindan,
                            "extra.not_takepart": n.extra.not_takepart
                        }));
                    }
                }) : t.cancel;
            }
        });
    },
    onContinue: function() {
        var t = this;
        a.util.request({
            url: "wmall/store/table/continuePindan",
            data: {
                sid: t.data.sid,
                table_id: t.data.table_id
            },
            success: function(i) {
                var e = i.data.message;
                if (e.errno) return a.util.toast(e.message), !1;
                t.onGetPindan();
            }
        });
    },
    onEditGoods: function() {
        var t = this;
        a.util.jump2url("/tangshi/pages/table/goods?sid=" + t.data.store.id + "&table_id=" + t.data.table_id + "&pindan_id=" + t.data.pindan_id, "redirectTo");
    },
    onSubmit: function() {
        var t = this;
        wx.showModal({
            title: "温馨提示",
            content: "有其他桌友正与你一起点餐，确定去结算吗？",
            success: function(i) {
                i.confirm ? a.util.jump2url("/tangshi/pages/table/create?sid=" + t.data.sid + "&is_pindan=1&pindan_id=" + t.data.pindan.pindan_id + "&table_id=" + t.data.table_id) : i.cancel;
            }
        });
    },
    onShow: function() {
        this.onGetPindan();
    },
    onPullDownRefresh: function() {
        this.onGetPindan(), wx.stopPullDownRefresh();
    },
    onShareAppMessage: function() {
        return this.data.sharedata;
    },
    onJsEvent: function(t) {
        a.util.jsEvent(t);
    }
});