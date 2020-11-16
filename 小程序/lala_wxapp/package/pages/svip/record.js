function a(a) {
    if (Array.isArray(a)) {
        for (var t = 0, e = Array(a.length); t < a.length; t++) e[t] = a[t];
        return e;
    }
    return Array.from(a);
}

var t = getApp();

Page({
    data: {
        type: "redpacket",
        next: "",
        exchange_max: 0,
        redpacket: {
            page: 1,
            psize: 11,
            loaded: !1,
            empty: !1,
            data: []
        },
        credit: {
            page: 1,
            psize: 11,
            loaded: !1,
            empty: !1,
            data: []
        },
        Lang: t.Lang,
        wuiLoading: {
            show: !1
        }
    },
    onLoad: function(a) {
        this.onReachBottom();
    },
    onReachBottom: function() {
        var e = this, d = e.data.type;
        e.data[d].loaded || t.util.request({
            url: "svip/records/list",
            data: {
                type: d,
                page: e.data[d].page,
                psize: e.data[d].psize
            },
            success: function(r) {
                t.util.loaded();
                var n = r.data.message;
                if (n.errno) return t.util.toast(n.message), !1;
                var o = (n = n.message).records;
                e.data[d].data = [].concat(a(e.data[d].data), a(o)), e.data[d].data.length || (e.data[d].empty = !0), 
                o && o.length < e.data[d].psize && (e.data[d].loaded = !0), e.data[d].page++, delete n.records, 
                n[d] = e.data[d], e.setData(n);
            }
        });
    },
    onToggleType: function(a) {
        var t = this, e = a.currentTarget.dataset.type;
        e != t.data.type && (t.data.type = e, t.setData({
            type: e
        }), t.data[e].data.length || t.data[e].loaded || t.onReachBottom());
    },
    onJsEvent: function(a) {
        t.util.jsEvent(a);
    }
});