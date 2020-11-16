var a = getApp();

Page({
    data: {
        zhezhaoShow: !1,
        team_id: 0,
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(t) {
        var e = this;
        t && t.id && (e.data.id = t.id, t.team_id && (e.data.team_id = t.team_id)), a.util.request({
            url: "pintuan/index/share",
            data: {
                id: e.data.id,
                team_id: e.data.team_id,
                menufooter: 1
            },
            success: function(t) {
                a.util.loaded();
                var i = t.data.message;
                if (i.errno) return a.util.toast(i.message), !1;
                wx.setNavigationBarTitle({
                    title: i.message.detail.name
                }), e.setData(i.message);
            }
        });
    },
    onPullDownRefresh: function() {
        this.onLoad(), wx.stopPullDownRefresh();
    },
    onJsEvent: function(t) {
        a.util.jsEvent(t);
    },
    onToggleZhezhao: function() {
        this.setData({
            zhezhaoShow: !this.data.zhezhaoShow
        });
    },
    onShareAppMessage: function(a) {
        var t = this;
        return t.data.shareData.imageUrl = t.data.shareData.imgUrl, t.data.shareData;
    }
});