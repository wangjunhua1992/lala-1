var a = getApp(), e = require("../../static/js/utils/underscore.js");

Page({
    data: {
        address: {
            id: 0,
            sid: 0,
            sex: "先生",
            address: "点击选择"
        },
        columns: [],
        submiting: 0,
        orderUrl: "pages/order/create?",
        addressUrl: "pages/member/address?",
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onChooseLocation: function(e) {
        var t = this;
        a.util.setStorageSync("address", t.data.address), wx.chooseLocation({
            success: function(e) {
                t.data.address = a.util.getStorageSync("address"), t.data.address.location_x = e.latitude, 
                t.data.address.location_y = e.longitude, t.data.address.address = e.name, t.setData({
                    address: t.data.address
                });
            },
            cancel: function(e) {
                a.util.toast("取消后不能选择位置");
            }
        });
    },
    onFormChange: function(e) {
        var t = this, d = e.currentTarget.dataset.type;
        t.data.address[d] = e.detail.value, a.util.setStorageSync("address", t.data.address);
    },
    onCreateUrl: function(a) {
        var e = this;
        if (a) {
            var t = [], d = [], s = [ "sid", "is_pindan", "pindan_id", "is_buysvip" ];
            for (var r in a) s.indexOf(r) >= 0 && t.push(r + "=" + a[r]), d.push(r + "=" + a[r]);
            e.setData({
                orderUrl: e.data.orderUrl + t.join("&"),
                addressUrl: e.data.addressUrl + d.join("&")
            });
        }
    },
    onLoad: function(e) {
        var t = this, d = t.data.address.id = e.id ? e.id : 0, s = t.data.address.sid = e.sid;
        t.data.orderId = e.orderId, s > 0 && (e.channel = "takeout"), t.data.options = e, 
        t.onCreateUrl(e);
        var r = {
            id: d,
            sid: e.sid
        };
        a.util.request({
            url: "wmall/member/address/post",
            data: r,
            success: function(e) {
                a.util.loaded();
                var s = e.data.message.message;
                if (s.sid = t.data.address.sid, "1" == s.address_type && s.areas && s.areas.length > 0) {
                    var r = s.areas;
                    t.data.columns[0] = r, r[0].child && (t.data.columns[1] = r[0].child);
                    var i = s.area_parent_index, o = s.area_index;
                    i > 0 && (t.data.columns[1] = r[i].child);
                    var n = [ i, o ];
                    t.setData({
                        columns: t.data.columns,
                        multiIndex: n
                    });
                }
                t.setData({
                    address: s,
                    id: d
                });
            }
        });
    },
    onSelectAddress: function(e) {
        var t = e.detail.value[0], d = e.detail.value[1], s = this.data.columns;
        if ("0" == s[1][d].status) {
            var r = "亲，您的地址【" + s[1][d].title + "】已超出【" + this.data.address.store.title + "】的配送范围了，请更换其他商家下单";
            wx.showModal({
                title: "温馨提示",
                content: r,
                confirmText: "调整地址",
                confirmColor: "#ff2d4b",
                cancelText: "更换商家",
                success: function(e) {
                    if (e.confirm) ; else if (e.cancel) {
                        a.util.jump2url("/pages/home/index", "redirectTo");
                    }
                }
            });
        }
        this.data.address.address = s[0][t].title + s[1][d].title, this.data.address.location_x = s[1][d].location_x, 
        this.data.address.location_y = s[1][d].location_y, this.data.address.area_id = s[1][d].id, 
        this.data.address.area_parentid = s[0][t].id, this.setData({
            "address.address": this.data.address.address
        });
    },
    onChangeColumn: function(a) {
        if (0 == a.detail.column) {
            var e = a.detail.value, t = this.data.columns;
            t[0][e].child ? t[1] = t[0][e].child : t[1] = [], this.setData({
                columns: t
            });
        }
    },
    onSubmit: function(t) {
        var d = this;
        if (1 == d.data.submiting) return !1;
        var s = t.detail.value;
        if (1 == d.data.address.store.auto_get_address && !d.data.address.location_x) return a.util.toast("收货地址不能为空"), 
        !1;
        if (!s.realname) return a.util.toast("联系人不能为空"), !1;
        if (!s.mobile) return a.util.toast("手机号不能为空"), !1;
        if (!a.util.isMobile(s.mobile)) return a.util.toast("手机号格式错误"), !1;
        if ("1" == d.data.address.address_type) {
            if (!s.number) return a.util.toast("门牌号不能为空"), !1;
            if (!a.util.isNumber(s.number)) return a.util.toast("门牌号只能是数字"), !1;
        }
        d.data.submiting = 1;
        var r = {
            id: d.data.address.id,
            sid: d.data.address.sid,
            order_id: d.data.orderId,
            channel: d.data.options.channel,
            flag: 1,
            sex: s.sex,
            number: s.number,
            realname: s.realname,
            mobile: s.mobile,
            address: d.data.address.address,
            location_x: d.data.address.location_x,
            location_y: d.data.address.location_y,
            agentid: d.data.options.agentid || 0,
            area_id: d.data.address.area_id,
            area_parentid: d.data.address.area_parentid,
            tag: d.data.address.tag
        };
        a.util.request({
            method: "POST",
            url: "wmall/member/address/post",
            data: r,
            success: function(t) {
                d.data.submiting = 0;
                var s = t.data.message;
                if (!d.data.options.channel || "" == d.data.options.channel || "undefined" == d.data.options.channel) return s.errno ? (a.util.toast(s.message), 
                !1) : void a.util.jump2url("/pages/member/address", "redirectTo");
                if ("takeout" == d.data.options.channel) if (-1e3 == s.errno) wx.showModal({
                    title: "",
                    content: s.message,
                    confirmText: "调整地址",
                    confirmColor: "#ff2d4b",
                    cancelText: "仍然保存",
                    success: function(e) {
                        e.confirm || e.cancel && (r.force = 1, a.util.request({
                            method: "POST",
                            url: "wmall/member/address/post",
                            data: r,
                            success: function(e) {
                                d.data.orderId > 0 ? a.util.jump2url("/pages/order/address?id=" + d.data.orderId, "redirectTo") : (wx.removeStorageSync("address"), 
                                a.util.jump2url(d.data.orderUrl, "redirectTo"));
                            }
                        }));
                    }
                }); else {
                    if (s.errno) return a.util.toast(s.message), !1;
                    d.data.orderId > 0 ? a.util.jump2url("/pages/order/address?id=" + d.data.orderId, "redirectTo") : (wx.removeStorageSync("address"), 
                    (i = a.util.getStorageSync("order.extra")) && (i = e.extend(i, {
                        address_id: s.message
                    })), a.util.setStorageSync("order.extra", i), a.util.jump2url(d.data.orderUrl, "redirectTo"));
                } else if ("errander" == d.data.options.channel) if (-1e3 == s.errno) wx.showModal({
                    title: "",
                    content: "亲,您的地址已超出跑腿的服务范围了",
                    confirmText: "调整地址",
                    confirmColor: "red",
                    cancelText: "仍然保存",
                    success: function(e) {
                        e.confirm ? wx.hideModal() : e.cancel && (r.force = 1, a.util.request({
                            method: "POST",
                            url: "wmall/member/address/post",
                            data: r,
                            success: function(e) {
                                wx.removeStorageSync("address");
                                var t = "/pages/paotui/diy?id=" + d.data.options.erranderId;
                                a.util.jump2url(t, "redirectTo");
                            }
                        }));
                    }
                }); else {
                    if (s.errno) return a.util.toast(s.message), !1;
                    wx.removeStorageSync("address");
                    var i = a.util.getStorageSync("errander.extra");
                    i && (i = "buy" == d.data.options.input ? e.extend(i, {
                        buyaddress_id: s.message
                    }) : e.extend(i, {
                        acceptaddress_id: s.message
                    })), a.util.setStorageSync("errander.extra", i);
                    var o = "/pages/paotui/diy?id=" + d.data.options.erranderId;
                    a.util.jump2url(o, "redirectTo");
                }
            }
        });
    },
    onDel: function() {
        var e = this;
        wx.showModal({
            title: "提示",
            content: "确定删除该地址么",
            success: function(t) {
                if (t.confirm) {
                    var d = {
                        id: e.data.id
                    };
                    a.util.request({
                        url: "wmall/member/address/del",
                        data: d,
                        success: function(t) {
                            if (0 == t.data.message.errno) {
                                var d = "/pages/member/address";
                                e.data.address.sid > 0 && (d = e.data.addressUrl), a.util.jump2url(d, "redirectTo");
                            }
                        }
                    });
                }
            }
        });
    },
    onChangeAddressTag: function(a) {
        var e = this, t = a.currentTarget.dataset.tag;
        e.setData({
            "address.tag": t
        });
    }
});