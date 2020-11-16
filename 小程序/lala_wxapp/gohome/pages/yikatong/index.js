getApp();

Page({
    data: {
        tabActive: "privilege"
    },
    onToggleTab: function(t) {
        var a = this;
        a.data.tabActive = t.currentTarget.dataset.tab, a.setData({
            tabActive: a.data.tabActive
        });
    }
});