var e = getApp();

Page({
    data: {
        showText: !0,
        home: {},
        getLocationStatus: !0,
        Lang: e.Lang,
        wuiLoading: {
            show: !0,
            img: e.util.getStorageSync("theme.loading.img")
        }
    },
    onChangeShowText: function() {
        var e = this;
        e.setData({
            showText: !e.data.showText
        });
    },
    onJsEvent: function(t) {
        e.util.jsEvent(t);
    },
    onLoad: function(t) {
        var a = this;
        e.util.request({
            url: "errander/guide/index",
            data: {
                forceLocation: 1,
                menufooter: 1
            },
            success: function(t) {
                if (e.util.loaded(), -1 == (t = t.data.message).errno) return e.util.toast(t.message, ""), 
                !1;
                if (-2 == t.errno) return a.setData({
                    getLocationStatus: !1
                }), !1;
                var o = t.message, s = o.superRedpacketData;
                s && 0 == s.errno && s.message.page && (s = {
                    is_show: !!(s.message.redpackets && s.message.redpackets.length > 0),
                    type: s.message.type || "",
                    page: s.message.page,
                    redpackets: s.message.redpackets
                }), a.setData({
                    home: o.data,
                    superRedpacket: s
                }), wx.setNavigationBarTitle({
                    title: o.data.page.title
                }), wx.setNavigationBarColor({
                    frontColor: o.data.page.navigationtextcolor,
                    backgroundColor: o.data.page.navigationbackground
                });
            }
        });
    },
    onGoodsInfo: function(e) {
        this.setData({
            "extra.note": e.detail.value
        });
    },
    onCloseRedpacket: function() {
        this.setData({
            "superRedpacket.is_show": !1
        });
    }
});