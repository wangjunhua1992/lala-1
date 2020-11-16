var t = getApp();

Page({
    data: {
        template: 1,
        mobile: "",
        Lang: t.Lang,
        wuiLoading: {
            show: !0,
            img: t.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(e) {
        var a = this;
        a.data.options = e, t.util.request({
            url: "superRedpacket/share/index",
            data: {
                order_id: e.order_id
            },
            success: function(e) {
                if (t.util.loaded(), (e = e.data.message).errno) return -1e3 == e.errno ? (a.onGrant(), 
                !1) : (t.util.toast(e.message, "", 1e3), !1);
                a.setData(e.message), t.WxParse.wxParse("agreement", "html", a.data.agreement, a, 5), 
                wx.setNavigationBarTitle({
                    title: a.data.activity_title
                });
            }
        });
    },
    onJsEvent: function(e) {
        t.util.jsEvent(e);
    },
    onInput: function(t) {
        this.data.mobile = t.detail.value;
    },
    onSubmit: function() {
        var e = this;
        if (!e.data.mobile) return t.util.toast("请输入手机号"), !1;
        if (!t.util.isMobile(e.data.mobile)) return t.util.toast("手机号格式错误"), !1;
        t.util.request({
            url: "superRedpacket/share/index",
            data: {
                mobile: e.data.mobile,
                order_id: e.data.options.order_id
            },
            method: "POST",
            success: function(a) {
                if ((a = a.data.message).errno) return t.util.toast(a.message, "", 1e3), !1;
                e.onGrant();
            }
        });
    },
    onGrant: function() {
        var e = this;
        t.util.request({
            url: "superRedpacket/share/grant",
            data: {
                order_id: e.data.options.order_id
            },
            success: function(a) {
                var a = a.data.message;
                if (e.setData({
                    template: 2
                }), a.errno) return t.util.toast(a.message, "", 1e3), e.setData({
                    activity: a.message.activity
                }), t.WxParse.wxParse("agreement", "html", e.data.activity.data.activity.agreement, e, 5), 
                !1;
                e.setData(a.message), t.WxParse.wxParse("agreement", "html", e.data.activity.data.activity.agreement, e, 5), 
                1 == e.data.get_status && t.util.toast("领取红包成功", "", 1e3), 1 == e.data.is_get && t.util.toast("您已领取过这个红包了", "", 1e3), 
                wx.setNavigationBarTitle({
                    title: e.data.activity.name
                });
            }
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});