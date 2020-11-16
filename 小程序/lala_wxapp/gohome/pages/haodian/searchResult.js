function e(e, t, n) {
    return t in e ? Object.defineProperty(e, t, {
        value: n,
        enumerable: !0,
        configurable: !0,
        writable: !0
    }) : e[t] = n, e;
}

var t, n = getApp();

Page((t = {
    data: {
        keyword: "",
        Lang: n.Lang,
        wuiLoading: {
            show: !0,
            img: n.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(e) {
        var t = e.key ? e.key : "", a = this, o = {
            key: t
        };
        a.setData({
            keyword: t
        }), n.util.request({
            url: "haodian/hunt/search",
            data: o,
            success: function(e) {
                n.util.loaded();
                var t = e.data.message;
                if (t.errno) return n.util.toast(t.message), !1;
                t = t.message, a.setData({
                    stores: t.stores
                });
            }
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {},
    onInput: function(e) {
        this.setData({
            keyword: e.detail.value
        });
    },
    onSearch: function() {
        var e = this;
        if (!e.data.keyword) return !1;
        var t = {
            key: e.data.keyword
        };
        n.util.request({
            url: "haodian/hunt/search",
            data: t,
            success: function(t) {
                var a = t.data.message;
                if (a.errno) return n.util.toast(a.message), !1;
                a = a.message, e.setData({
                    stores: a.stores
                });
            }
        });
    }
}, e(t, "onPullDownRefresh", function() {
    this.onLoad({
        key: ""
    }), wx.stopPullDownRefresh();
}), e(t, "onJsEvent", function(e) {
    n.util.jsEvent(e);
}), t));