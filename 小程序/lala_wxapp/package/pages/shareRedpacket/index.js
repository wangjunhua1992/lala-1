var a = getApp();

Page({
    data: {
        invited_info: {
            page: 1,
            psize: 15,
            empty: !1,
            loaded: !1,
            data: []
        },
        rankings: {
            page: 1,
            psize: 15,
            empty: !1,
            loaded: !1,
            data: []
        },
        type: "invite",
        zhezhaoShow: !1,
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function() {
        this.onGetInvited();
    },
    onReachBottom: function() {
        "invite" == this.data.type ? this.onGetInvited() : "ranking" == this.data.type && this.onGetRank();
    },
    onChangeZhezhao: function() {
        this.setData({
            zhezhaoShow: !this.data.zhezhaoShow
        });
    },
    onChangeType: function(a) {
        var t = a.currentTarget.dataset.type;
        this.data.type != t && ("ranking" == t ? (this.data.rankings = {
            page: 1,
            psize: 15,
            empty: !1,
            loaded: !1,
            data: []
        }, this.onGetRank()) : "invite" == t && (this.data.invited_info = {
            page: 1,
            psize: 15,
            empty: !1,
            loaded: !1,
            data: []
        }, this.onGetInvited()), this.setData({
            type: t
        }));
    },
    onGetInvited: function() {
        var t = this;
        if (t.data.invited_info.loaded) return !1;
        a.util.request({
            url: "shareRedpacket/index/invite",
            data: {
                page: t.data.invited_info.page,
                psize: t.data.invited_info.psize
            },
            showLoading: !1,
            success: function(e) {
                a.util.loaded();
                var i = e.data.message;
                if (i.errno) return a.util.toast(i.message), !1;
                i = i.message, t.data.invited_info.data = t.data.invited_info.data.concat(i.invited_info), 
                i.invited_info.length < t.data.invited_info.psize && (t.data.invited_info.loaded = !0), 
                t.data.invited_info.data.length || (t.data.invited_info.empty = !0), t.data.invited_info.page++, 
                i.redPacket.title && wx.setNavigationBarTitle({
                    title: i.redPacket.title
                }), a.WxParse.wxParse("richtext", "html", i.redPacket.agreement, t, 5), t.setData({
                    invited_info: t.data.invited_info,
                    redPacket: i.redPacket,
                    total: i.total,
                    redPacket_num: i.redPacket_num || 0
                });
            }
        });
    },
    onGetRank: function() {
        var t = this;
        if (t.data.rankings.loaded) return !1;
        a.util.request({
            url: "shareRedpacket/index/ranking",
            data: {
                page: t.data.rankings.page,
                psize: t.data.rankings.psize
            },
            showLoading: !1,
            success: function(e) {
                var i = e.data.message;
                if (i.errno) return a.util.toast(i.message), !1;
                i = i.message, t.data.rankings.data = t.data.rankings.data.concat(i.rankings), i.rankings.length < t.data.rankings.psize && (t.data.rankings.loaded = !0), 
                t.data.rankings.data.length || (t.data.rankings.empty = !0), t.data.rankings.page++, 
                t.setData({
                    rankings: t.data.rankings
                });
            }
        });
    },
    onShareAppMessage: function() {
        return this.data.redPacket.share;
    }
});