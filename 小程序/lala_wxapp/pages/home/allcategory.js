var t = getApp();

Page({
    data: {
        tabActive: 0,
        getLocationStatus: !0,
        Lang: t.Lang,
        wuiLoading: {
            show: !0,
            img: t.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function() {
        var e = this;
        t.util.request({
            url: "wmall/home/search/allcategory",
            data: {
                forceLocation: 1
            },
            success: function(a) {
                t.util.loaded();
                var i = a.data.message;
                if (i.errno) return -2 == i.errno ? (e.setData({
                    getLocationStatus: !1,
                    failedTips: {
                        type: "message",
                        tips: i.message,
                        btnText: "手动搜索地址",
                        link: "/pages/home/location?from=allcategory",
                        img: "http://cos.lalawaimai.com/we7_wmall/wxapp/store_no_con.png"
                    }
                }), !1) : void t.util.toast(i.message, "", 1e3);
                e.setData(i.message, function() {
                    e.onGetHeights();
                });
            }
        });
    },
    onGetHeights: function() {
        var t = this, e = wx.createSelectorQuery(), a = [], i = 0;
        e.selectAll(".goods-info").boundingClientRect(function(e) {
            e.forEach(function(t) {
                i += t.height, a.push(i);
            }), t.setData({
                heightArr: a
            });
        }), e.select(".goods-list").boundingClientRect(function(e) {
            t.setData({
                categoryContainerHeight: e.height
            });
        }).exec();
    },
    onScroll: function(t) {
        var e = this, a = t.detail.scrollTop, i = e.data.heightArr, n = e.data.categoryContainerHeight, o = e.data.tabActive, s = 0;
        if (i.length > 0) {
            if (a >= i[i.length - 1] - n) return;
            for (var c = 0; c < i.length; c++) a >= 0 && a < i[0] ? s = 0 : a >= i[c - 1] && a < i[c] && (s = c);
            s != o && e.setData({
                tabActive: s
            });
        }
    },
    onToggleTab: function(t) {
        var e = this, a = t.currentTarget.dataset.index;
        e.setData({
            containerActive: "childcategory-container-" + a,
            tabActive: a
        });
    },
    onJsEvent: function(e) {
        t.util.jsEvent(e);
    }
});