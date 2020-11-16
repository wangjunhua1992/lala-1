var e = getApp();

Page({
    data: {
        id: 0,
        order: {},
        addresses: {
            available: [],
            dis_available: []
        },
        selectIndex: -1,
        config: {},
        islegal: !1,
        Lang: e.Lang,
        wuiLoading: {
            show: !0,
            img: e.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(a) {
        var s = this;
        a && a.id > 0 && (s.data.id = a.id), e.util.request({
            url: "wmall/order/index/update_address",
            data: {
                id: s.data.id
            },
            success: function(a) {
                e.util.loaded();
                var t = a.data.message;
                if (t.errno) return e.util.toast(t.message), !1;
                s.setData({
                    order: t.message.order,
                    addresses: t.message.addresses,
                    config: t.message.config,
                    islegal: !0
                });
            }
        });
    },
    onSelectAddress: function(e) {
        var a = this, s = e.currentTarget.dataset.index;
        s != a.data.selectIndex && a.setData({
            selectIndex: s
        });
    },
    onSubmit: function() {
        var a = this, s = a.data.selectIndex;
        s < 0 || !a.data.addresses.available[s] || !a.data.islegal || (a.setData({
            islegal: !1
        }), e.util.request({
            url: "wmall/order/index/update_address",
            method: "POST",
            data: {
                id: a.data.id,
                address_id: a.data.addresses.available[s].id
            },
            success: function(s) {
                var t = s.data.message;
                if (t.errno) return e.util.toast(t.message), a.setData({
                    islegal: !1
                }), !1;
                e.util.toast(t.message, "redirect:/pages/order/detail?id=" + a.data.order.id, 1500);
            }
        }));
    },
    onJsEvent: function(a) {
        e.util.jsEvent(a);
    }
});