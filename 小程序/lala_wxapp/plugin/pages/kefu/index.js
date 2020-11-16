var a = getApp();

Page({
    data: {
        chats: {
            page: 1,
            psize: 50,
            loaded: 0,
            empty: 0,
            data: []
        },
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(a) {
        this.onReachBottom();
    },
    onReachBottom: function() {
        var t = this;
        if (t.data.chats.loaded) return !1;
        a.util.request({
            url: "kefu/member/index",
            data: {
                page: t.data.chats.page,
                psize: t.data.chats.psize
            },
            success: function(e) {
                a.util.loaded();
                var s = e.data.message;
                if (0 != s.errno) return a.util.toast(s.message), !1;
                t.data.chats.page++, t.data.chats.data = t.data.chats.data.concat(s.message.chats.chats), 
                t.data.chats.data.length || (t.data.chats.empty = 1), s.message.chats.chats && s.message.chats.chats.length < t.data.chats.psize && (t.data.chats.loaded = 1), 
                t.setData({
                    chats: t.data.chats
                });
            }
        });
    },
    onJsEvent: function(t) {
        a.util.jsEvent(t);
    }
});