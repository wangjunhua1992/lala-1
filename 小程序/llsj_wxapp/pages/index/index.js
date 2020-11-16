var s = getApp();

Page({
    data: {
        Lang: s.Lang,
        motto: "Hello World",
        userInfo: {},
        hasUserInfo: !1,
        canIUse: wx.canIUse("button.open-type.getUserInfo")
    },
    bindViewTap: function() {
        wx.navigateTo({
            url: "../logs/logs"
        });
    },
    onLoad: function() {
        var a = this;
        s.globalData.userInfo ? this.setData({
            userInfo: s.globalData.userInfo,
            hasUserInfo: !0
        }) : this.data.canIUse ? s.userInfoReadyCallback = function(s) {
            a.setData({
                userInfo: s.userInfo,
                hasUserInfo: !0
            });
        } : wx.getUserInfo({
            success: function(e) {
                s.globalData.userInfo = e.userInfo, a.setData({
                    userInfo: e.userInfo,
                    hasUserInfo: !0
                });
            }
        });
    },
    getUserInfo: function(a) {
        console.log(a), s.globalData.userInfo = a.detail.userInfo, this.setData({
            userInfo: a.detail.userInfo,
            hasUserInfo: !0
        });
    }
});