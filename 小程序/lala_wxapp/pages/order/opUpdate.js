var e = getApp();

Page({
    data: {
        id: 0,
        type: "",
        person: [ "1人", "2人", "3人" ],
        person_num: 1,
        islegal: !1,
        Lang: e.Lang,
        wuiLoading: {
            show: !0,
            img: e.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(t) {
        var a = this;
        t && t.id > 0 && (a.data.id = t.id), t && t.type && (a.data.type = t.type), e.util.request({
            url: "wmall/order/index/order_info_update",
            data: {
                id: a.data.id
            },
            success: function(t) {
                e.util.loaded();
                var o = t.data.message;
                if (o.errno) return e.util.toast(o.message), !1;
                a.setData({
                    order: o.message.order,
                    order_note: o.message.store.order_note,
                    type: a.data.type,
                    islegal: !0
                });
            }
        });
    },
    onSubmit: function(t) {
        var a = this;
        if (a.data.islegal) {
            var o = a.data.order, i = a.data.type, r = {
                id: a.data.id,
                type: i
            };
            if ("note" == i) r.note = o.note; else if ("mobile" == i) {
                if (!o.mobile) return void e.util.toast("请输入手机号");
                if (!e.util.isMobile(o.mobile)) return void e.util.toast("手机号格式错误");
                r.mobile = o.mobile;
            } else "person_num" == i && (r.person_num = o.person_num);
            a.setData({
                islegal: !1
            }), e.util.request({
                url: "wmall/order/index/order_info_update",
                data: r,
                method: "POST",
                success: function(t) {
                    var i = t.data.message;
                    if (i.errno) return e.util.toast(i.message), a.setData({
                        islegal: !0
                    }), !1;
                    e.util.toast(i.message, "redirect:/pages/order/op?id=" + o.id, 1500);
                }
            });
        }
    },
    onChooseLabel: function(e) {
        var t = this, a = t.data.order.note;
        a || (a = ""), a = a + " " + e.currentTarget.dataset.note, t.setData({
            "order.note": a
        });
    },
    onNote: function(e) {
        this.setData({
            "order.note": e.detail.value
        });
    },
    bindChange: function(e) {
        this.setData({
            "order.person_num": parseInt(e.detail.value) + 1
        });
    },
    onMobile: function(e) {
        this.setData({
            "order.mobile": e.detail.value
        });
    },
    onJsEvent: function(t) {
        e.util.jsEvent(t);
    }
});