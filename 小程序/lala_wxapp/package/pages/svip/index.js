var e = getApp();

Page({
    data: {
        Lang: e.Lang,
        wuiLoading: {
            show: !0,
            img: e.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function() {
        var t = this;
        e.util.request({
            url: "svip/index/index",
            success: function(a) {
                e.util.loaded();
                var i = a.data.message;
                if (i.errno) return -2 == i.errno ? e.util.jump2url("/package/pages/svip/mine", "redirectTo") : e.util.toast(i.message), 
                !1;
                t.setData(i.message);
            }
        });
    },
    onJsEvent: function(t) {
        e.util.jsEvent(t);
    }
});