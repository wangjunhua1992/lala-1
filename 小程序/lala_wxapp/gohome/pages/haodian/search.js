var t = getApp();

Page({
    data: {
        keyword: "",
        Lang: t.Lang,
        wuiLoading: {
            show: !0,
            img: t.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function() {
        var e = this;
        t.util.request({
            url: "haodian/hunt/index",
            data: {
                menufooter: 1
            },
            success: function(a) {
                t.util.loaded();
                var a = a.data.message.message;
                e.setData({
                    hotStores: a.hotStores.store,
                    searchHistorys: a.searchHistorys
                });
            }
        });
    },
    onReady: function() {},
    onInput: function(t) {
        this.setData({
            keyword: t.detail.value
        });
    },
    onKeyword: function(t) {
        this.setData({
            keyword: t.currentTarget.dataset.keyword
        }), this.onSearch();
    },
    onSearch: function() {
        var e = this;
        if (!e.data.keyword) return !1;
        t.util.jump2url("/gohome/pages/haodian/searchResult?key=" + e.data.keyword);
    },
    onClearHistory: function() {
        var e = this;
        wx.showModal({
            content: "确定清除历史记录吗",
            success: function(a) {
                a.confirm && t.util.request({
                    url: "haodian/hunt/truncate",
                    success: function() {
                        e.setData({
                            searchHistorys: []
                        });
                    }
                });
            }
        });
    },
    onPullDownRefresh: function() {
        this.onLoad(), wx.stopPullDownRefresh();
    }
});