var a = getApp();

Page({
    data: {
        activityid: 0,
        buyremark: "",
        islegal: !1,
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(t) {
        var e = this;
        t && t.activityid && (e.data.activityid = t.activityid), a.util.request({
            url: "kanjia/order/create",
            data: {
                activityid: e.data.activityid
            },
            success: function(t) {
                a.util.loaded();
                var i = t.data.message;
                if (i.errno) return a.util.toast(i.message), !1;
                i = i.message, e.setData({
                    activity: i.activity,
                    takeinfo: i.takeinfo,
                    islegal: !0,
                    username: i.member.username,
                    mobile: i.member.mobile
                });
            }
        });
    },
    onNote: function(a) {
        this.setData({
            buyremark: a.detail.value
        });
    },
    onSubmit: function(t) {
        var e = this;
        if (e.data.islegal) {
            var i = t.detail.value;
            console.log(i), i.username ? i.mobile ? (e.setData({
                islegal: !1
            }), a.util.request({
                method: "POST",
                url: "kanjia/order/create",
                data: {
                    activityid: e.data.activityid,
                    buyremark: e.data.buyremark,
                    username: i.username,
                    mobile: i.mobile,
                    formid: t.detail.formId
                },
                success: function(t) {
                    var e = t.data.message;
                    if (e.errno) return a.util.toast(e.message), !1;
                    var i = e.message;
                    a.util.toast("下单成功", "/pages/public/pay?order_id=" + i + "&order_type=gohome", 1500);
                }
            })) : a.util.toast("请输入核销人手机号") : a.util.toast("请输入核销人姓名");
        }
    },
    onJsEvent: function(t) {
        a.util.jsEvent(t);
    }
});