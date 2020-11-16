var a = getApp();

Page({
    data: {
        showSearch: !1,
        MapType: "gaode",
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(e) {
        var o = this;
        e && e.from && (o.data.from = e.from), e && !e.getlocationfail && a.util.getLocation(function(a) {
            var e = a.data.message.message;
            o.setData({
                location: e.address,
                location_x: e.location_x,
                location_y: e.location_y,
                pois: e.pois.slice(0, 10)
            });
        }), a.util.request({
            url: "wmall/home/location/index",
            data: {
                forceOauth: 1
            },
            success: function(t) {
                a.util.loaded();
                var t = t.data.message.message;
                o.setData({
                    addresses: t,
                    MapType: a.util.getStorageSync("MapType"),
                    getlocationfail: e.getlocationfail
                });
            }
        });
    },
    onInput: function(e) {
        var o = this, t = e.detail.value;
        if (t) {
            var s = "system/common/map/suggestion";
            "google" == o.data.MapType && (s = "system/common/map/suggestion_google"), a.util.request({
                url: s,
                data: {
                    key: t
                },
                success: function(a) {
                    if (a.data.message.errno) return !1;
                    var e = a.data.message.message;
                    e && e.length > 0 && o.setData({
                        searchAddress: e,
                        showSearch: !0
                    });
                }
            });
        } else o.setData({
            showSearch: !1
        });
    },
    onChooseAddress: function(e) {
        var o = this, t = e.currentTarget.dataset;
        if (!t.x || !t.y) return a.util.toast("该地址无效"), !1;
        t.onshow = 1, a.util.setStorageSync("location", t, 300);
        var s = "./index";
        "paotui" == o.data.from ? s = "../paotui/guide" : "gohome" == o.data.from ? s = "/gohome/pages/home/index" : "tongcheng" == o.data.from ? s = "/gohome/pages/tongcheng/index" : "haodian" == o.data.from ? s = "/gohome/pages/haodian/index" : "kanjia" == o.data.from ? s = "/gohome/pages/kanjia/index" : "seckill" == o.data.from ? s = "/gohome/pages/seckill/index" : "pintuan" == o.data.from ? s = "/gohome/pages/pintuan/index" : "allcategory" == o.data.from && (s = "/pages/home/allcategory"), 
        wx.switchTab({
            url: s,
            fail: function(a) {
                "switchTab:fail can not switch to no-tabBar page" != a.errMsg && "switchTab:fail:can not switch to non-TabBar page" != a.errMsg || wx.redirectTo({
                    url: s
                });
            }
        });
    },
    onPullDownRefresh: function() {
        this.onLoad(), wx.stopPullDownRefresh();
    }
});