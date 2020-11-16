var e = getApp(), a = require("../../static/js/utils/underscore.js");

Page({
    data: {
        address: [],
        nodata: !1,
        orderUrl: "pages/order/create?",
        addressPostUrl: "pages/member/addressPost?",
        Lang: e.Lang,
        wuiLoading: {
            show: !0,
            img: e.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(e) {
        var t = this;
        if (e) {
            var r = {
                sid: 0,
                channel: "",
                erranderId: 0,
                input: "",
                agentid: 0
            };
            r = a.extend(r, e), t.onCreateUrl(r), t.setData(r);
        }
        t.onReachBottom();
    },
    onCreateUrl: function(e) {
        var a = this;
        if (e) {
            var t = [], r = [], d = [ "sid", "is_pindan", "pindan_id", "is_buysvip" ];
            for (var n in e) d.indexOf(n) >= 0 && t.push(n + "=" + e[n]), r.push(n + "=" + e[n]);
            a.setData({
                orderUrl: a.data.orderUrl + t.join("&"),
                addressPostUrl: a.data.addressPostUrl + r.join("&")
            });
        }
    },
    onJsEvent: function(a) {
        e.util.jsEvent(a);
    },
    onReady: function() {},
    onReachBottom: function() {
        var a = this, t = {
            sid: a.data.sid,
            erranderId: a.data.erranderId,
            agentid: a.data.agentid
        };
        e.util.request({
            url: "wmall/member/address",
            data: t,
            success: function(t) {
                e.util.loaded();
                var r = t.data.message.message;
                (a.data.sid > 0 || a.data.erranderId > 0) && (r.available.length > 0 || r.dis_available.length > 0) || !a.data.sid && !a.data.erranderId && r.length > 0 ? a.data.nodata = !1 : a.data.nodata = !0, 
                a.setData({
                    address: r,
                    showNodata: a.data.nodata,
                    config: t.data.message.config
                });
            }
        });
    },
    onSelectAddress: function(t) {
        var r = t.currentTarget.dataset.id, d = t.currentTarget.dataset.available, n = this;
        if (!n.data.sid && !n.data.erranderId) return !1;
        if (!d) return e.util.toast("该地址不在商家配送范围内"), !1;
        if ("errander" == n.data.channel) {
            (i = e.util.getStorageSync("errander.extra")) && (i = "buy" == n.data.input ? a.extend(i, {
                buyaddress_id: r
            }) : a.extend(i, {
                acceptaddress_id: r
            })), e.util.setStorageSync("errander.extra", i);
            var s = "/pages/paotui/diy?id=" + n.data.erranderId;
            return e.util.jump2url(s, "redirectTo"), !1;
        }
        var i = e.util.getStorageSync("order.extra");
        i && (i = a.extend(i, {
            address_id: r
        })), e.util.setStorageSync("order.extra", i), e.util.jump2url(n.data.orderUrl, "redirectTo");
    },
    onUseWxAddress: function() {
        var t = this;
        wx.chooseAddress({
            success: function(r) {
                var d = {
                    sid: t.data.sid,
                    channel: t.data.channel,
                    erranderId: t.data.erranderId,
                    realname: r.userName,
                    mobile: r.telNumber,
                    provinceName: r.provinceName,
                    cityName: r.cityName,
                    countyName: r.countyName,
                    detailInfo: r.detailInfo,
                    address: r.provinceName + r.cityName + r.countyName + r.detailInfo,
                    agentid: t.data.agentid
                };
                e.util.request({
                    method: "POST",
                    url: "wmall/member/address/wxaddress_add",
                    data: d,
                    success: function(r) {
                        var n = r.data.message;
                        if (!t.data.channel || "" == t.data.channel || "undefined" == t.data.channel) return n.errno ? (e.util.toast(n.message), 
                        !1) : void t.onPullDownRefresh();
                        if ("takeout" == t.data.channel) if (-1e3 == n.errno) wx.showModal({
                            title: "",
                            content: "亲,您的地址已超出商家的配送范围了",
                            confirmText: "调整地址",
                            confirmColor: "#ff2d4b",
                            cancelText: "仍然保存",
                            success: function(a) {
                                a.confirm ? wx.hideModal() : a.cancel && (d.force = 1, e.util.request({
                                    method: "POST",
                                    url: "wmall/member/address/wxaddress_add",
                                    data: d,
                                    success: function(a) {
                                        e.util.jump2url(t.data.orderUrl, "redirectTo");
                                    }
                                }));
                            }
                        }); else {
                            if (n.errno) return e.util.toast(n.message), !1;
                            (s = e.util.getStorageSync("order.extra")) && (s = a.extend(s, {
                                address_id: n.message
                            })), e.util.setStorageSync("order.extra", s), e.util.jump2url(t.data.orderUrl, "redirectTo");
                        } else if ("errander" == t.data.channel) if (-1e3 == n.errno) wx.showModal({
                            title: "",
                            content: "亲,您的地址已超出跑腿的服务范围了",
                            confirmText: "调整地址",
                            confirmColor: "red",
                            cancelText: "仍然保存",
                            success: function(a) {
                                a.confirm ? wx.hideModal() : a.cancel && (d.force = 1, e.util.request({
                                    method: "POST",
                                    url: "wmall/member/address/wxaddress_add",
                                    data: d,
                                    success: function(a) {
                                        var r = "/pages/paotui/diy?id=" + t.data.erranderId;
                                        e.util.jump2url(r, "redirectTo");
                                    }
                                }));
                            }
                        }); else {
                            if (n.errno) return e.util.toast(n.message), !1;
                            var s = e.util.getStorageSync("errander.extra");
                            s && (s = "buy" == t.data.input ? a.extend(s, {
                                buyaddress_id: n.message
                            }) : a.extend(s, {
                                acceptaddress_id: n.message
                            })), e.util.setStorageSync("errander.extra", s);
                            var i = "/pages/paotui/diy?id=" + t.data.erranderId;
                            e.util.jump2url(i, "redirectTo");
                        }
                    }
                });
            },
            fail: function() {
                wx.showModal({
                    title: "系统信息",
                    content: "使用微信通讯地址失败，请重新授权后使用",
                    showCancel: !1,
                    complete: function() {
                        wx.getSetting({
                            success: function(e) {
                                e.authSetting["scope.address"] || wx.openSetting();
                            }
                        });
                    }
                });
            }
        });
    },
    onShareAppMessage: function() {},
    onPullDownRefresh: function() {
        var e = this;
        e.data.min = 0, e.data.address = [], e.onReachBottom(), wx.stopPullDownRefresh();
    }
});