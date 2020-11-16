var t = getApp();

Page({
    data: {
        selectIndex: 0,
        submitting: !0,
        Lang: t.Lang,
        wuiLoading: {
            show: !0,
            img: t.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function() {
        var e = this;
        t.util.request({
            url: "svip/index/meal",
            success: function(a) {
                t.util.loaded();
                var s = a.data.message;
                if (s.errno) return t.util.toast(s.message), !1;
                s.message.submitting = !1, e.setData(s.message);
            }
        });
    },
    onSelectMeal: function(t) {
        this.setData({
            selectIndex: t.currentTarget.dataset.index
        });
    },
    onSubmit: function() {
        var e = this;
        if (e.data.submitting) return !1;
        e.setData({
            submitting: !0
        }), t.util.request({
            url: "svip/index/buy",
            data: {
                id: e.data.meals[e.data.selectIndex].id
            },
            method: "POST",
            success: function(a) {
                e.setData({
                    submitting: !1
                });
                var s = a.data.message;
                if (s.errno) return t.util.toast(s.message), !1;
                var i = "/pages/public/pay?order_id=" + (s = s.message).id + "&order_type=svip";
                t.util.jump2url(i, "navigateTo");
            }
        });
    },
    onJsEvent: function(e) {
        t.util.jsEvent(e);
    }
});