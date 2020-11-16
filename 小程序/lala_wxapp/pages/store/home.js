var e = getApp();

Page({
    data: {
        diy: {
            data: {}
        },
        superRedpacket: {
            is_show: !1
        },
        selectedtab: "coupon",
        is_grant: 0,
        Lang: e.Lang,
        wuiLoading: {
            show: !0,
            img: e.util.getStorageSync("theme.loading.img")
        },
        menu: {
            css: {},
            params: {},
            data: {},
            position: {
                right: "15px",
                bottom: "80px",
                left: "inherit"
            }
        },
        storeCouponTop: 0,
        storeOnsaleTop: 0,
        storeEvaluateTop: 0
    },
    onCalculateStoreNavHeight: function() {
        var e = this, a = e.createSelectorQuery(), t = 0, o = 0, s = 0;
        a.select(".diy-store-coupon").boundingClientRect(function(e) {
            e && (t = e.top);
        }), a.select(".diy-store-onsale").boundingClientRect(function(e) {
            e && (o = e.top);
        }), a.select(".diy-store-evaluate").boundingClientRect(function(e) {
            e && (s = e.top);
        }).exec(function() {
            e.setData({
                storeCouponTop: t,
                storeOnsaleTop: o,
                storeEvaluateTop: s
            });
        });
    },
    onLoad: function(a) {
        var t = this;
        if (a && a.scene) {
            var o = decodeURIComponent(a.scene), s = (o = o.split(":"))[1];
            a.sid = s;
        }
        t.data.options = a, e.util.request({
            url: "wmall/store/home/index",
            data: {
                sid: t.data.options.sid || 3,
                menufooter: 1
            },
            success: function(a) {
                e.util.loaded();
                var o = a.data.message;
                o.errno && e.util.toast(o.message);
                var s = a.data.global, n = "/pages/store/home?sid=" + t.data.options.sid;
                1 == o.message.config_mall.version && (n = "/pages/home/index?from=home&sid=" + t.data.options.sid);
                var i = {
                    title: s.share.title,
                    desc: s.share.desc,
                    imageUrl: s.share.imgUrl,
                    path: n
                };
                t.data.diy.data = o.message.homepage;
                var r = o.message.homepage.items;
                for (var d in r) "richtext" == r[d].id && e.WxParse.wxParse("richtext." + d, "html", r[d].params.content, t, 5);
                var l = {}, c = o.message.superRedpacketData;
                c && 0 == c.errno && c.message.page && (l = {
                    is_show: !0,
                    type: c.message.type || "",
                    page: c.message.page,
                    redpackets: c.message.redpackets
                }), t.setData({
                    diy: t.data.diy,
                    shareData: i,
                    superRedpacket: l
                }), wx.setNavigationBarTitle({
                    title: t.data.diy.data.page.title
                }), wx.setNavigationBarColor({
                    frontColor: t.data.diy.data.page.navigationtextcolor,
                    backgroundColor: t.data.diy.data.page.navigationbackground
                }), t.data.store = o.message.store, t.data.store.menu && t.data.store.menu.data && (t.data.menu = Object.assign(t.data.menu, t.data.store.menu.data)), 
                t.data.store.menu && "1" == t.data.store.menu.path.home && e.util.setNavigator(t.data.menu), 
                t.onCalculateStoreNavHeight();
            }
        });
    },
    onJsEvent: function(a) {
        e.util.jsEvent(a);
    },
    onCloseRedpacket: function() {
        this.setData({
            "superRedpacket.is_show": !1
        });
    },
    getCoupon: function(a) {
        var t = this;
        e.util.request({
            url: "wmall/channel/coupon/get",
            data: {
                sid: a.target.dataset.sid
            },
            success: function(a) {
                0 == a.data.message.errno ? (e.util.toast(a.data.message.message), t.setData({
                    is_grant: 1
                })) : e.util.toast("领取失败");
            }
        });
    },
    onScrollTo: function(e) {
        var a = this, t = e.currentTarget.dataset.scrollid;
        if ("coupon" == t) o = Math.ceil(a.data.storeCouponTop - 45); else if ("onsale" == t) o = Math.ceil(a.data.storeOnsaleTop - 45); else if ("evaluate" == t) var o = Math.ceil(a.data.storeEvaluateTop - 45);
        wx.pageScrollTo({
            scrollTop: o,
            duration: 0,
            success: function() {}
        }), a.setData({
            scrollToId: t,
            selectedtab: t
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {
        var e = this;
        return console.log("分享信息", e.data.shareData), e.data.shareData;
    }
});