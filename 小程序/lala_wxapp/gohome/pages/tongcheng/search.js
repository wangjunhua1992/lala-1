var t = getApp();

Page({
    data: {
        keyword: "",
        searchHistory: [],
        Lang: t.Lang,
        wuiLoading: {
            show: !0,
            img: t.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(a) {
        var e = this;
        e.data.options = a, t.util.request({
            url: "tongcheng/index/get_search",
            data: {},
            success: function(a) {
                t.util.loaded();
                var s = a.data.message;
                if (s.errno) return t.util.toast(s.message), !1;
                var i = t.util.getStorageSync("isearchHistory.tongcheng");
                i && (e.data.searchHistory = i), e.setData({
                    searchHistory: e.data.searchHistory,
                    categorys: s.message.categorys
                });
            }
        });
    },
    onInput: function(t) {
        this.data.keyword = t.detail.value;
    },
    onSubmit: function() {
        if (this.data.keyword) {
            this.data.searchHistory.push(this.data.keyword), t.util.setStorageSync("isearchHistory.tongcheng", this.data.searchHistory);
            var a = "/gohome/pages/tongcheng/searchResult?id=" + this.data.options.id + "&childid=" + this.data.options.childid + "&keyword=" + this.data.keyword;
            t.util.jump2url(a, "navigateTo");
        } else t.util.toast("请输入搜索条件");
    },
    onDeleteHistory: function() {
        var a = this;
        wx.showModal({
            content: "确定删除吗",
            success: function(e) {
                e.confirm ? (t.util.removeStorageSync("isearchHistory"), a.setData({
                    searchHistory: []
                })) : e.cancel;
            }
        });
    },
    onJsEvent: function(a) {
        t.util.jsEvent(a);
    }
});