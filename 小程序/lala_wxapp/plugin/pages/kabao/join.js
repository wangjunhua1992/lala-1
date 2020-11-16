var t = getApp();

Page({
    data: {
        confirmAgreement: !1,
        editStatus: !1
    },
    onLoad: function(t) {},
    onShow: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onConfirmAgreement: function() {
        var t = this;
        t.setData({
            confirmAgreement: !t.data.confirmAgreement
        });
    },
    onToggleEdit: function() {
        var t = this;
        t.setData({
            editStatus: !t.data.editStatus
        });
    },
    onJsEvent: function(n) {
        t.util.jsEvent(n);
    },
    onShareAppMessage: function() {}
});