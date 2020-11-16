var t = getApp();

Page({
    data: {
        Lang: t.Lang
    },
    onLoad: function(e) {
        var o = this, a = [];
        setInterval(function() {
            wx.getLocation({
                type: "gcj02",
                success: function(e) {
                    console.log(e), a.push(e), o.setData({
                        record: a
                    }), t.util.request({
                        url: "delivery/member/set/location",
                        data: {
                            location_x: e.latitude,
                            location_y: e.longitude
                        },
                        success: function() {}
                    });
                }
            });
        }, 3e3);
    }
});