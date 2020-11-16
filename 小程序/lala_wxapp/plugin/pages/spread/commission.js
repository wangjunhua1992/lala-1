var t = getApp();

Page({
    data: {
        Lang: t.Lang,
        wuiLoading: {
            show: !0,
            img: t.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function() {
        var a = this;
        t.util.request({
            url: "spread/commission",
            data: {
                menufooter: 1
            },
            success: function(e) {
                t.util.loaded();
                var s = e.data.message;
                a.setData(s.message);
            }
        });
    },
    onJsEvent: function(a) {
        t.util.jsEvent(a);
    }
});