function getUrlParam(e, t) {
    var a = new RegExp("(^|&)" + t + "=([^&]*)(&|$)"), n = e.split("?")[1].match(a);
    return null != n ? unescape(n[2]) : null;
}

var _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(e) {
    return typeof e;
} : function(e) {
    return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e;
}, util = {};

util.imessage = function(e, t, a) {
    var n = {};
    if (t) {
        var i = t.substring(0, 9), r = "", o = "";
        "navigate:" == i ? (o = "navigate", r = t.substring(9)) : "redirect:" == i ? (o = "redirect", 
        r = t.substring(9)) : "switchTab:" == t.substring(0, 10) ? (o = "switchTab", r = t.substring(10)) : (o = "redirect", 
        r = t);
    }
    n = "object" == (void 0 === e ? "undefined" : _typeof(e)) ? {
        show: 1,
        type: a || "info",
        title: e.title,
        message: e.message,
        btn_text: e.btn_text,
        open_type: o,
        url: r
    } : {
        show: 1,
        type: a || "info",
        title: e,
        open_type: o,
        url: r
    }, util.getCurPage().setData({
        wuiMessage: n
    }), wx.setNavigationBarTitle({
        title: "系统提示"
    }), wx.setNavigationBarColor({
        frontColor: "#000000",
        backgroundColor: "#ffffff",
        animation: {
            duration: 400,
            timingFunc: "easeIn"
        }
    });
}, util.jump2url = function(e, t) {
    var t = t || "navigateTo";
    0 != e.indexOf("/") && (e = "/" + e), wx[t]({
        url: e,
        fail: function(t) {
            wx.switchTab({
                url: e,
                fail: function() {
                    wx.redirectTo({
                        url: e
                    });
                }
            });
        }
    });
}, util.toast = function(e, t, a) {
    var n = getCurrentPages(), i = n[n.length - 1], r = i.data.wuiToast || {};
    clearTimeout(r.timer), i.setData({
        wuiToast: {
            show: !0,
            title: e,
            url: t
        }
    });
    var o = setTimeout(function() {
        clearTimeout(r.timer), i.setData({
            "wuiToast.show": !1
        });
        var e = i.data.wuiToast.url;
        if (e) if ("back" == e) wx.navigateBack(); else {
            if ("refresh" != e) {
                var t = e.substring(0, 9), a = "";
                return "navigate:" == t ? (a = "navigateTo", e = e.substring(9)) : "redirect:" == t ? (a = "redirectTo", 
                e = e.substring(9)) : "switchTab:" == e.substring(0, 10) ? (a = "switchTab", e = e.substring(10)) : (e = e, 
                a = "navigateTo"), wx[a]({
                    url: e,
                    fail: function(t) {
                        wx.switchTab({
                            url: e,
                            fail: function() {
                                wx.redirectTo({
                                    url: e
                                });
                            }
                        });
                    }
                }), !1;
            }
            i.onLoad();
        }
    }, a || 3e3);
    i.setData({
        "wuiToast.timer": o
    });
}, util.getCurPage = function() {
    var e = getCurrentPages();
    return e[e.length - 1];
}, util.setNavigator = function(e) {
    util.getCurPage().setData({
        wuiNavigator: {
            show: !1,
            menu: e
        }
    });
}, util.setDiyTabBar = function(e) {
    var t = util.getCurPage();
    e.currentPageLink = t.route, t.setData({
        menufooter: e
    });
}, util.loading = function() {
    util.getCurPage().setData({
        wuiLoading: {
            show: !0,
            img: util.getStorageSync("theme.loading.img")
        }
    });
}, util.loaded = function() {
    util.getCurPage().setData({
        "wuiLoading.show": !1
    });
}, util.showOauth = function() {
    var e = util.getCurPage(), t = util.getStorageSync("mallBasic");
    e.setData({
        wuiOauth: {
            show: !0,
            data: t
        }
    });
}, util.hideOauth = function() {
    util.getCurPage().setData({
        wuiOauth: {
            show: !1
        }
    });
}, util.checkOauth = function() {
    return wx.getSetting({
        success: function(e) {
            if (!e.authSetting["scope.userInfo"]) return util.navigateToAuth(), !1;
        }
    }), !0;
}, util.navigateToAuth = function() {
    return util.setStorageSync("backUrl", util.getCurPageUrl()), wx.redirectTo({
        url: "/pages/public/auth"
    }), !0;
}, util.getCurPageUrl = function() {
    var e = util.getCurPage(), t = "/" + e.route, a = e.options, n = [];
    if (a) for (var i in a) {
        var r = a[i];
        n.push(i + "=" + r);
    }
    return n.length > 0 && (t = t + "?" + n.join("&")), t;
}, util.jsInfinite = function(e) {
    var t = util.getCurPage(), a = e.currentTarget.dataset, n = a.min, i = a.href, r = a.name;
    if (!id || !i || 1 == t.data.loading) return !1;
    t.data.loading = 1, t.setData({
        showloading: !0
    }), util.request({
        url: i,
        data: {
            min: n
        },
        success: function(e) {
            var t = e.data.message;
            {
                if (!t.errno) {
                    var a = that.data.redPackets.concat(t.message);
                    if (!a.length) return that.setData({
                        showNodata: !0,
                        showloading: !1
                    }), !1;
                    var n = {
                        min: t.min,
                        showloading: !1
                    };
                    return n[r] = a, e.data.message.min || (n.min = -1), that.setData(n), !0;
                }
                util.toast(t.message);
            }
        }
    });
}, util.gohome = function() {
    return wx.switchTab({
        url: "/pages/home/index",
        fail: function(e) {
            "switchTab:fail can not switch to no-tabBar page" != e.errMsg && "switchTab:fail:can not switch to non-TabBar page" != e.errMsg || wx.redirectTo({
                url: "/pages/diy/index"
            });
        }
    }), !0;
}, util.jsEvent = function(e) {
    var t = e.currentTarget.dataset.eventType || "jsPost";
    if ("jsPost" == t) util.jsPost(e); else if ("jsPay" == t) util.jsPay(e); else if ("jsUrl" == t) util.jsUrl(e); else if ("jsPhone" == t) util.jsPhone(e); else if ("jsToggle" == t) util.jsToggle(e); else if ("jsLocation" == t) util.jsLocation(e); else if ("jsCopy" == t) util.jsCopy(e); else if ("webview" == t) util.webview(e); else if ("jsInfinite" == t) util.jsInfinite(e); else if ("jsToggleNavigator" == t) {
        var a = util.getCurPage();
        a.setData({
            "wuiNavigator.show": !a.data.wuiNavigator.show
        });
    } else if ("jsSaveImg" == t) util.jsSaveImg(e); else if ("jsUploadImg" == t) util.jsUploadImg(e); else if ("jsDelImg" == t) util.jsDelImg(e); else if ("jsRemoveCache" == t) wx.removeStorageSync("userInfo"), 
    wx.removeStorageSync("location"); else if ("jsOauth" == t) {
        console.log(e);
        var n = e.detail, i = {};
        wx.login({
            success: function(e) {
                util.request({
                    url: "system/common/session/openid",
                    data: {
                        code: e.code
                    },
                    cachetime: 0,
                    success: function(e) {
                        e.data.errno ? wx.showModal({
                            content: e.data.message
                        }) : (i.sessionid = e.data.data.sessionid, wx.setStorageSync("userInfo", i), "getUserInfo:ok" == n.errMsg ? util.request({
                            showLoading: !1,
                            url: "system/common/session/userinfo",
                            data: {
                                signature: n.signature,
                                rawData: n.rawData,
                                iv: n.iv,
                                encryptedData: n.encryptedData
                            },
                            method: "POST",
                            header: {
                                "content-type": "application/x-www-form-urlencoded"
                            },
                            cachetime: 0,
                            success: function(e) {
                                if (e.data.errno) wx.showModal({
                                    content: e.data.message
                                }); else {
                                    i.memberInfo = e.data.data, wx.setStorageSync("userInfo", i);
                                    var t = util.getStorageSync("backUrl");
                                    util.jump2url(t, "redirectTo");
                                }
                            }
                        }) : "getUserInfo:fail auth deny" == n.errMsg && wx.showModal({
                            title: "授权提示",
                            content: "若需登录平台，平台需要获取您的公开信息（昵称、头像等）",
                            confirmText: "授权",
                            showCancel: !1,
                            success: function(e) {
                                e.confirm ? wx.openSetting({
                                    success: function() {}
                                }) : e.cancel;
                            }
                        }));
                    }
                });
            },
            fail: function(e) {
                wx.showModal({
                    title: "获取信息失败",
                    content: "请允许授权以便为您提供给服务",
                    success: function(e) {
                        e.confirm && util.getUserInfo();
                    }
                });
            }
        });
    }
}, util.jsUrl = function(e) {
    var t = e.currentTarget.dataset.url;
    if (!t) return !1;
    if (1 == (t = t.split(":")).length) {
        if ("refresh" == t) return t = util.getCurPageUrl(), void wx.reLaunch({
            url: t
        });
        t = 0 == t[0].indexOf("/") ? t[0] : "/" + t[0];
        var a = e.currentTarget.dataset.openType, n = [ "navigateTo", "switchTab", "redirectTo" ];
        return a && -1 != n.indexOf(a) || (a = "navigateTo"), void wx[a]({
            url: t,
            fail: function(e) {
                console.log(e), wx.switchTab({
                    url: t,
                    fail: function() {
                        wx.redirectTo({
                            url: t
                        });
                    }
                });
            }
        });
    }
    "webview" == t[0] ? util.webview(e) : "tel" == t[0] ? util.jsPhone(e) : "miniProgram" == t[0] ? util.jsMiniProgram(e) : "wx" == t[0] && "scanCode" == t[1] && wx.scanCode({
        success: function(e) {
            console.log(e), e && e.path ? util.jump2url(e.path) : util.toast("小程序码路径错误");
        }
    });
}, util.jsMiniProgram = function(e) {
    var t = e.currentTarget.dataset;
    if (t.url && -1 != t.url.indexOf(":")) {
        for (var a = t.url.split(":")[1].split(","), n = {}, i = 0; i < a.length; i++) {
            var r = a[i].split("_");
            console.log(r);
            var o = r[0], s = r.slice(1).join("_");
            n[o] = s;
        }
        console.log(n);
    } else {
        n = {
            appId: t.appid || t.appId
        };
        t.path && (n.path = path);
    }
    n.fail = function(e) {
        -1 != e.errMsg.indexOf("is not in navigateToMiniProgramAppIdList") && wx.showModal({
            content: "跳转小程序出错了",
            showCancel: !1
        });
    }, wx.navigateToMiniProgram(n);
}, util.tolink = function(e) {
    if (-1 != e.indexOf("http://") || -1 != e.indexOf("https://")) return e;
    var t = util.getExtConfigSync();
    return 0 == e.indexOf("./") ? t.siteInfo.sitebase + "/" + e.replace("./", "") : "";
}, util.webview = function(e) {
    var t = e.currentTarget.dataset;
    if (!t.url) return !1;
    var a = t.url.split(":");
    "webview" == a[0] && (t.url = a[1] + ":" + a[2]);
    var n = util.tolink(t.url), i = t.src || "../public/webview";
    n = (n = (n = n.replace("?", "_a_")).replace(/=/g, "_b_")).replace(/&/g, "_c_"), 
    wx.navigateTo({
        url: i + "?url=" + n
    });
}, util.jsDelImg = function(e) {
    var t = e.currentTarget.dataset, a = t.key, n = t.index, i = util.getCurPage(), r = i.data[a];
    r.splice(n, 1);
    var o = {};
    o[a] = r, i.setData(o);
}, util.jsSaveImg = function(e) {
    var t = e.currentTarget.dataset.url;
    wx.showLoading({
        title: "正在下载中"
    }), wx.downloadFile({
        url: t,
        success: function(e) {
            200 === e.statusCode && wx.saveImageToPhotosAlbum({
                filePath: e.tempFilePath,
                success: function() {
                    wx.hideLoading(), app.util.toast("文件保存成功");
                }
            });
        }
    });
}, util.jsUploadImg = function(e) {
    var t = e.currentTarget.dataset, a = t.key, n = t.count || 9;
    console.log(a);
    var i = util.getCurPage();
    util.image({
        count: n,
        success: function(e) {
            i.data[a].push(e);
            var t = {};
            t[a] = i.data[a], i.setData(t);
        }
    });
}, util.jsPhone = function(e) {
    var t = e.currentTarget.dataset, a = t.phonenumber || t.url, n = a.split(":");
    "tel" == n[0] && (a = n[1]), wx.makePhoneCall({
        phoneNumber: a
    });
}, util.jsLocation = function(e) {
    var t = e.currentTarget.dataset, a = parseFloat(t.lat), n = parseFloat(t.lng);
    if (!a || !n) return !1;
    var i = {
        latitude: a,
        longitude: n
    };
    t.scale && (i.scale = t.scale), t.name && (i.name = t.name), t.address && (i.address = t.address), 
    wx.openLocation(i);
}, util.jsCopy = function(e) {
    var t = e.currentTarget.dataset;
    wx.setClipboardData({
        data: t.text || "",
        success: function(e) {
            wx.getClipboardData({
                success: function(e) {
                    util.toast("复制成功");
                }
            });
        }
    });
}, util.jsToggle = function(e) {
    var t = e.currentTarget.dataset, a = util.getCurPage(), n = t.modal, i = t.modal.split("."), r = {};
    if (1 == i.length) r[n] = !a.data[n], a.setData(r); else {
        var o = !a.data[i[0]][i[1]];
        r[n] = o, a.setData(r);
    }
    return !0;
}, util.jsPay = function(e) {
    var t = e.currentTarget.dataset, a = t.successUrl, n = t.orderId, i = t.orderType;
    return wx.navigateTo({
        url: "../public/pay?order_id=" + n + "&order_type=" + i + "&success_url=" + a
    }), !0;
}, util.jsPost = function(e) {
    var t = e.currentTarget.dataset, a = t.confirm, n = t.href || t.url, i = t.successUrl, r = util.getCurPage(), o = function() {
        r.data.jspost && 1 == r.data.jspost || (r.data.jspost = 1, util.showLoading(), i || (i = "refresh"), 
        util.request({
            url: util.url(n),
            data: {},
            success: function(e) {
                r.data.jspost = 0, wx.hideLoading();
                var t = e.data.message, a = t.errno, n = t.message;
                a ? util.toast(n) : (i || (i = "refresh"), util.toast(n, i));
            }
        }));
    };
    a ? wx.showModal({
        title: "",
        content: a,
        success: function(e) {
            e.confirm ? o() : e.cancel;
        }
    }) : o();
}, util.setData = function(e, t) {
    var a = e.split("."), n = util.getCurPage();
    if (1 == a.length) n.data[e] = t; else if (2 == a.length) (i = n.data[a[0]]) || (i = {}), 
    i[a[1]] = t, n.data[a[0]] = i; else if (3 == a.length) (i = n.data[a[0]]) || (i = {}), 
    i[a[1]] || (i[a[1]] = {}), i[a[1]][a[2]] = t, n.data[a[0]] = i; else if (4 == a.length) {
        var i = n.data[a[0]];
        i || (i = {}), i[a[1]] || (i[a[1]] = {}), i[a[1]][a[2]] || (i[a[1]][a[2]] = {}), 
        i[a[1]][a[2]][a[3]] = t, n.data[a[0]] = i;
    }
    return !0;
}, util.setStorageSync = function(e, t, a) {
    var n = e.split(".");
    if (a > 0) {
        var i = new Date();
        t.expire = parseInt(i.getTime() / 1e3) + a;
    }
    if (1 == n.length) wx.setStorageSync(e, t); else if (2 == n.length) (r = wx.getStorageSync(n[0])) || (r = {}), 
    r[n[1]] = t, wx.setStorageSync(n[0], r); else if (3 == n.length) {
        var r = wx.getStorageSync(n[0]);
        r || (r = {}), r[n[1]] || (r[n[1]] = {}), r[n[1]][n[2]] = t, wx.setStorageSync(n[0], r);
    }
    return !0;
}, util.getStorageSync = function(e) {
    var t = e.split("."), a = "";
    if (1 == t.length) a = wx.getStorageSync(e); else if (2 == t.length) (n = wx.getStorageSync(t[0])) && n[t[1]] && (a = n[t[1]]); else if (3 == t.length) {
        var n = wx.getStorageSync(t[0]);
        n && n[t[1]] && (a = n[t[1]][t[2]]);
    }
    if (a && a.expire) {
        var i = new Date();
        a.expire < i.getTime() / 1e3 && (a = {});
    }
    return a;
}, util.getExtConfigSync = function(e) {
    var t = wx.getExtConfigSync();
    if (t && t.siteInfo || (t = getApp().ext), !e) return t;
    var a = e.split(".");
    return 1 == a.length ? t[a[0]] : 2 == a.length ? t[a[0]] ? t[a[0]][a[1]] : "" : 3 == a.length ? t[a[0]] && t[a[0]][a[1]] ? t[a[0]][a[1]][a[2]] : "" : data;
}, util.removeStorageSync = function(e) {
    var t = e.split(".");
    if (1 == t.length) wx.removeStorageSync(e); else if (2 == t.length) (a = wx.getStorageSync(t[0])) && (delete a[t[1]], 
    wx.setStorageSync(t[0], a)); else if (3 == t.length) {
        var a = wx.getStorageSync(t[0]);
        a && a[t[1]] && (delete a[t[1]][t[2]], wx.setStorageSync(t[0], a));
    }
    return !0;
}, util.merge = function(e, t) {
    var a = require("underscore.js");
    return e || (e = {}), t || (t = {}), a.extend(e, t);
}, util.pay = function(e) {
    if (e.pay_type || (e.pay_type = "wechat"), !e.order_type) return !1;
    if (!e.order_id) return !1;
    var t = {
        pay_type: e.pay_type,
        order_type: e.order_type,
        id: e.order_id
    }, a = util.getCurPage();
    util.request({
        url: "system/paycenter/pay",
        data: t,
        success: function(n) {
            if ((n = n.data.message).errno) return -1e3 == n.errno && a.setData({
                submitDisabled: 0
            }), util.toast(n.message), !1;
            var i = "";
            n.wheel_url ? i = n.wheel_url : n.message.wheel_url && (i = n.message.wheel_url), 
            n.pay_type && (e.pay_type = n.pay_type), n = n.message;
            var r = {
                takeout: {
                    url_detail: "../order/detail?id=" + t.id
                },
                errander: {
                    url_detail: "../paotui/detail?id=" + t.id
                },
                deliveryCard: {
                    url_detail: "../../package/pages/deliveryCard/index"
                },
                recharge: {
                    url_detail: "../member/mine"
                },
                paybill: {
                    url_detail: "../member/mine"
                },
                creditshop: {
                    url_detail: "../creditshop/list"
                },
                freelunch: {
                    url_detail: "../../package/pages/freelunch/partakeSuccess"
                },
                seckill: {
                    url_detail: "../../package/pages/seckill/orderList"
                },
                mealRedpacket_plus: {
                    url_detail: "../../package/pages/mealRedpacket/orderplus"
                },
                mealRedpacket: {
                    url_detail: "../../package/pages/mealRedpacket/meal"
                },
                gohome: {
                    url_detail: "../../gohome/pages/order/detail?id=" + t.id
                },
                tongcheng: {
                    url_detail: "../../gohome/pages/tongcheng/detail?trade_id=" + t.id
                },
                haodian: {
                    url_detail: "../../gohome/pages/haodian/application"
                },
                svip: {
                    url_detail: "../../package/pages/svip/mine"
                }
            }[e.order_type];
            if (i && (i = "/" + i, r.url_detail = i), "wechat" == e.pay_type) return wx.requestPayment({
                timeStamp: n.timeStamp,
                nonceStr: n.nonceStr,
                package: n.package,
                signType: "MD5",
                paySign: n.paySign,
                success: function(t) {
                    return "function" == typeof e.success ? (e.success(t), !1) : r ? (util.toast("支付成功", r.url_detail, 3e3), 
                    !1) : void 0;
                },
                fail: function(t) {
                    if ("function" == typeof e.fail) return e.fail(t), !1;
                    a.setData({
                        submitDisabled: 0
                    });
                }
            }), !0;
            if ("credit" == e.pay_type) {
                if ("function" == typeof e.success) return e.success(res), !1;
            } else if (("delivery" == e.pay_type || "finishMeal" == e.pay_type) && "function" == typeof e.success) return e.success(res), 
            !1;
            return !!r && (util.toast("支付成功", r.url_detail, 3e3), !0);
        }
    });
}, util.url = function(e, t) {
    var a = util.getExtConfigSync(), n = a.siteInfo.siteroot + "?i=" + a.siteInfo.uniacid;
    if (-1 == e.indexOf("/")) return n + "&" + e;
    n = a.siteInfo.siteroot + "?i=" + a.siteInfo.uniacid + "&v=" + a.siteInfo.version + "&m=we7_wmall&c=entry&do=mobile&";
    var i = e.split("?");
    if ((e = i[0].split("/"))[0] && (n += "ctrl=" + e[0] + "&"), e[1] && (n += "ac=" + e[1] + "&"), 
    e[2] && (n += "op=" + e[2] + "&"), e[3] && (n += "ta=" + e[3] + "&"), i[1] && (n += i[1] + "&"), 
    (t = "object" === (void 0 === t ? "undefined" : _typeof(t)) ? t : {}) && "object" === (void 0 === t ? "undefined" : _typeof(t))) for (var r in t) r && t.hasOwnProperty(params) && t[r] && (n += r + "=" + t[r] + "&");
    return n += "&from=wxapp";
}, util.getUrlQuery = function(e) {
    var t = [];
    if (-1 != e.indexOf("?")) for (var a = e.split("?")[1].split("&"), n = 0; n < a.length; n++) a[n].split("=")[0] && unescape(a[n].split("=")[1]) && (t[n] = {
        name: a[n].split("=")[0],
        value: unescape(a[n].split("=")[1])
    });
    return t;
}, util.request = function(e) {
    (e = e || {}).showLoading = void 0 === e.showLoading || e.showLoading;
    var t = e.url;
    -1 == t.indexOf("http://") && -1 == t.indexOf("https://") && (t = util.url(t));
    var a = wx.getStorageSync("userInfo").sessionid;
    if (getUrlParam(t, "state") || e.data && e.data.state || !a || (t = t + "&state=we7sid-" + a), 
    e.data || (e.data = {}), 1 != e.data.forceOauth || a) {
        if (!e.data.lat || !e.data.lng) {
            var n = util.getStorageSync("location");
            n && n.x && (e.data.lat = n.x, e.data.lng = n.y, e.data.__lat = n.x, e.data.__lng = n.y);
        }
        if (1 == e.data.forceLocation && (!e.data.lat || "undefined" == e.data.lat)) return util.setStorageSync("location"), 
        util.getLocation(function(t) {
            var a = t.data.message.message;
            a = {
                address: a.address,
                x: a.latitude,
                y: a.longitude
            }, util.setStorageSync("location", a, 600), util.request(e);
        }), !1;
        wx.showNavigationBarLoading(), e.showLoading, console.log(t), console.log(e.data), 
        wx.request({
            url: t,
            data: e.data ? e.data : {},
            method: e.method ? e.method : "GET",
            header: {
                "content-type": "application/x-www-form-urlencoded"
            },
            success: function(t) {
                wx.hideNavigationBarLoading(), wx.hideLoading(), t.data.message || console.log(t.data.message);
                var a = "";
                if (t.data && t.data.global) {
                    (a = t.data.global).theme && util.setStorageSync("theme", a.theme);
                    var n = {
                        logo: "/static/img/wx-icon.png",
                        title: "平台"
                    };
                    a.configmall && a.configmall.logo && (n.logo = a.configmall.logo), a.configmall && a.configmall.title && (n.title = a.configmall.title), 
                    util.setStorageSync("mallBasic", n), 1 == a.configmall.wxappmenu_type ? (a.menufooter && util.setDiyTabBar(a.menufooter), 
                    a._fnav && a.menufooter && util.setNavigator(a.menufooter)) : a._nav && a.menufooter && util.setNavigator(a.menufooter), 
                    a.theme && util.setStorageSync("theme", a.theme), "" !== a.itime && util.setStorageSync("itime", a.itime), 
                    a.LangType && util.setStorageSync("LangType", a.LangType), a.MapType && util.setStorageSync("MapType", a.MapType);
                }
                if (t.data.message.errno) {
                    if (-3e3 == t.data.message.errno) return void util.imessage(t.data.message.message, "", "info");
                    if ("41009" == t.data.message.errno) return wx.setStorageSync("userInfo", ""), void util.getUserInfo(function() {
                        util.request(e);
                    });
                }
                e.success && "function" == typeof e.success && e.success(t);
            },
            fail: function(t) {
                if (t && "request:ok" != t.errMsg) return wx.hideLoading(), "request:fail url not in domain list" == t.errMsg ? void util.toast("您没有设置小程序服务器域名!注意：每次小程序发布后，都需要重新设置服务器域名，程序每个月可修改5次服务器域名。设置步骤：进入微信公众号平台-登录小程序账号密码进入管理中心-设置-开发设置-服务器域名(服务器域名为模块授权域名)", "", 1e5) : void util.toast(t.errMsg);
                wx.hideNavigationBarLoading(), wx.hideLoading(), e.fail && "function" == typeof e.fail && e.fail(t);
            },
            complete: function(t) {
                if (console.log(t), t && "request:ok" != t.errMsg) return wx.hideLoading(), "request:fail url not in domain list" == t.errMsg ? void util.toast("您没有设置小程序服务器域名!注意：每次小程序发布后，都需要重新设置服务器域名，程序每个月可修改5次服务器域名。设置步骤：进入微信公众号平台-登录小程序账号密码进入管理中心-设置-开发设置-服务器域名(服务器域名为模块授权域名)", "", 1e5) : void util.toast(t.errMsg);
                wx.hideNavigationBarLoading(), wx.hideLoading(), e.complete && "function" == typeof e.complete && e.complete(t);
            }
        });
    } else util.getUserInfo(function() {
        util.request(e);
    });
}, util.getStore = function(e, t) {
    if (1 == util.getStorageSync("itime")) if ("array" == t && e && e.length > 10) {
        var a = Math.floor(5 * Math.random());
        e = e.slice(2, a);
    } else "random" == t && (e = Math.floor(100 * Math.random()));
    return e;
}, util.checkCurrentPageIsInMenufooter = function(e) {
    if (!e || !e.data) return !1;
    var t = util.getCurPage().route, a = !1;
    for (var n in e.data) if (e.data[n].link == t || t == "/" + e.data[n].link) {
        a = !0;
        break;
    }
    return a;
}, util.image = function(e) {
    var t = e.count ? e.count : 9, a = getCurrentPages();
    a[a.length - 1];
    wx.chooseImage({
        count: t,
        sizeType: [ "original", "compressed" ],
        sourceType: [ "album", "camera" ],
        success: function(t) {
            var a = t.tempFilePaths;
            if (a.length && a.length > 0) for (var n = 0; n < a.length; n++) wx.uploadFile({
                url: util.url("c=utility&a=file&do=upload&type=image&thumb=0"),
                filePath: a[n],
                name: "file",
                success: function(t) {
                    t.data = JSON.parse(t.data), "function" == typeof e.success && e.success(t.data);
                }
            });
        },
        fail: function(t) {
            "function" == typeof e.fail && e.fail(t);
        },
        complete: function(t) {
            "function" == typeof e.complete && e.complete(t);
        }
    });
}, util.checkSession = function(e) {
    util.request({
        url: "system/common/session/check",
        method: "POST",
        cachetime: 0,
        showLoading: !1,
        success: function(t) {
            t.data.errno ? "function" == typeof e.fail && e.fail() : "function" == typeof e.success && e.success();
        },
        fail: function() {
            "function" == typeof e.fail && e.fail();
        }
    });
}, util.getUserInfo = function(e) {
    var t = function() {
        var t = {
            sessionid: "",
            wxInfo: "",
            memberInfo: ""
        };
        wx.login({
            success: function(a) {
                util.request({
                    url: "system/common/session/openid",
                    data: {
                        code: a.code
                    },
                    cachetime: 0,
                    success: function(a) {
                        a.data.errno ? wx.showModal({
                            content: a.data.message
                        }) : (t.sessionid = a.data.data.sessionid, wx.setStorageSync("userInfo", t), "function" == typeof e && e(t), 
                        wx.getUserInfo({
                            success: function(e) {
                                console.log("成功成功成功成功成功成功成功成功成功成功"), console.log(e), t.wxInfo = e.userInfo, wx.setStorageSync("userInfo", t), 
                                util.request({
                                    showLoading: !1,
                                    url: "system/common/session/userinfo",
                                    data: {
                                        signature: e.signature,
                                        rawData: e.rawData,
                                        iv: e.iv,
                                        encryptedData: e.encryptedData
                                    },
                                    method: "POST",
                                    header: {
                                        "content-type": "application/x-www-form-urlencoded"
                                    },
                                    cachetime: 0,
                                    success: function(e) {
                                        e.data.errno ? wx.showModal({
                                            content: e.data.message
                                        }) : (t.memberInfo = e.data.data, wx.setStorageSync("userInfo", t));
                                    }
                                });
                            },
                            fail: function(e) {
                                console.log("失败失败失败失败失败失败失败失败失败失败失败失败"), console.log(e), e && ("getUserInfo:fail scope unauthorized" == e.errMsg || e.errMsg);
                            }
                        }));
                    }
                });
            },
            fail: function(e) {
                wx.showModal({
                    title: "获取信息失败",
                    content: "请允许授权以便为您提供给服务",
                    success: function(e) {
                        e.confirm && util.getUserInfo();
                    }
                });
            }
        });
    }, a = wx.getStorageSync("userInfo");
    a.sessionid ? wx.checkSession({
        success: function() {
            "function" == typeof e && e(a);
        },
        fail: function() {
            a.sessionid = "", console.log("relogin"), wx.removeStorageSync("userInfo"), t();
        }
    }) : t();
}, util.getLocation = function(e) {
    wx.getLocation({
        type: "gcj02",
        success: function(t) {
            util.request({
                url: "system/common/map/regeo",
                data: {
                    latitude: t.latitude,
                    longitude: t.longitude,
                    convert: 0
                },
                success: function(t) {
                    "function" == typeof e && e(t);
                },
                fail: function(e) {}
            });
        },
        fail: function(e) {
            console.log("我在测试：" + e.errMsg), console.log("定位失败：" + e.errMsg), console.log(e), 
            e && ("getLocation:fail auth deny" == e.errMsg || "getLocation:fail:auth denied" == e.errMsg ? wx.showModal({
                title: "授权提示",
                content: "若需使用平台，平台需要获取您的位置信息，请在授权使用位置后下拉刷新进入平台",
                confirmText: "授权",
                showCancel: !1,
                success: function(e) {
                    e.confirm ? wx.openSetting({
                        success: function() {}
                    }) : e.cancel;
                }
            }) : "getLocation:fail:system permission denied" == e.errMsg ? wx.showModal({
                content: "亲,你没有给微信APP授权定位权限,请在授权使用位置后下拉刷新进入平台！",
                showCancel: !1
            }) : (wx.showModal({
                content: "获取定位失败,请手动搜索位置",
                showCancel: !1,
                confirmText: "搜索地址"
            }), util.jump2url("/pages/home/location?getlocationfail=1")));
        }
    });
}, util.getLocationPois = function(e) {
    util.request({
        url: "system/common/map/regeo",
        data: {
            latitude: e.latitude,
            longitude: e.longitude
        },
        showLoading: !1 !== e.showLoading || e.showLoading,
        success: function(t) {
            var a = t.data.message.message;
            "function" == typeof e.success && e.success(a);
        },
        fail: function(e) {}
    });
}, util.getLocationAround = function(e) {
    util.request({
        url: "system/common/map/place_around",
        data: {
            latitude: e.latitude,
            longitude: e.longitude,
            keywords: e.keywords,
            radius: e.radius
        },
        success: function(t) {
            var a = t.data.message.message;
            "function" == typeof e.success && e.success(a);
        },
        fail: function(e) {}
    });
}, util.navigateBack = function(e) {
    var t = e.delta ? e.delta : 1;
    if (e.data) {
        var a = getCurrentPages(), n = a[a.length - (t + 1)];
        n.pageForResult ? n.pageForResult(e.data) : n.setData(e.data);
    }
    wx.navigateBack({
        delta: t,
        success: function(t) {
            "function" == typeof e.success && e.success(t);
        },
        fail: function(t) {
            "function" == typeof e.fail && e.function(t);
        },
        complete: function() {
            "function" == typeof e.complete && e.complete();
        }
    });
}, util.footer = function(e) {
    var t = getApp().tabBar;
    for (var a in t.list) t.list[a].pageUrl = t.list[a].pagePath.replace(/(\?|#)[^"]*/g, "");
    t.thisurl = e.__route__, e.setData({
        tabBar: t
    });
}, util.message = function(e, t, a) {
    if (!e) return !0;
    if ("object" == (void 0 === e ? "undefined" : _typeof(e)) && (t = e.redirect, a = e.type, 
    e = e.title), t) {
        var n = t.substring(0, 9), i = "", r = "";
        "navigate:" == n ? (r = "navigateTo", i = t.substring(9)) : "redirect:" == n ? (r = "redirectTo", 
        i = t.substring(9)) : (i = t, r = "redirectTo");
    }
    a || (a = "success"), "success" == a ? wx.showToast({
        title: e,
        icon: "success",
        duration: 2e3,
        mask: !!i,
        complete: function() {
            i && setTimeout(function() {
                wx[r]({
                    url: i
                });
            }, 1800);
        }
    }) : "error" == a && wx.showModal({
        title: "系统信息",
        content: e,
        showCancel: !1,
        complete: function() {
            i && wx[r]({
                url: i
            });
        }
    });
}, util.user = util.getUserInfo, util.showLoading = function() {
    wx.getStorageSync("isShowLoading") && (wx.hideLoading(), wx.setStorageSync("isShowLoading", !1)), 
    wx.showLoading({
        title: "加载中",
        complete: function() {
            wx.setStorageSync("isShowLoading", !0);
        },
        fail: function() {
            wx.setStorageSync("isShowLoading", !1);
        }
    });
}, util.showImage = function(e) {
    var t = e ? e.currentTarget.dataset.preview : "";
    if (!t) return !1;
    wx.previewImage({
        urls: [ t ]
    });
}, util.isMobile = function(e) {
    var t = /^[01][3456789][0-9]{9}$/, a = wx.getStorageSync("LangType");
    return "" != a && "zh-cn" != a && (t = /^\d{5,}$/), !!t.test(e);
}, util.parseContent = function(e) {
    if (!e) return e;
    var t = [ "\ud83c[\udf00-\udfff]", "\ud83d[\udc00-\ude4f]", "\ud83d[\ude80-\udeff]" ], a = e.match(new RegExp(t.join("|"), "g"));
    if (a) for (var n in a) e = e.replace(a[n], "[U+" + a[n].codePointAt(0).toString(16).toUpperCase() + "]");
    return e;
}, util.date = function() {
    this.isLeapYear = function(e) {
        return 0 == e.getYear() % 4 && (e.getYear() % 100 != 0 || e.getYear() % 400 == 0);
    }, this.dateToStr = function(e, t) {
        e = arguments[0] || "yyyy-MM-dd HH:mm:ss", t = arguments[1] || new Date();
        var a = e, n = [ "日", "一", "二", "三", "四", "五", "六" ];
        return a = a.replace(/yyyy|YYYY/, t.getFullYear()), a = a.replace(/yy|YY/, t.getYear() % 100 > 9 ? (t.getYear() % 100).toString() : "0" + t.getYear() % 100), 
        a = a.replace(/MM/, t.getMonth() > 9 ? t.getMonth() + 1 : "0" + (t.getMonth() + 1)), 
        a = a.replace(/M/g, t.getMonth()), a = a.replace(/w|W/g, n[t.getDay()]), a = a.replace(/dd|DD/, t.getDate() > 9 ? t.getDate().toString() : "0" + t.getDate()), 
        a = a.replace(/d|D/g, t.getDate()), a = a.replace(/hh|HH/, t.getHours() > 9 ? t.getHours().toString() : "0" + t.getHours()), 
        a = a.replace(/h|H/g, t.getHours()), a = a.replace(/mm/, t.getMinutes() > 9 ? t.getMinutes().toString() : "0" + t.getMinutes()), 
        a = a.replace(/m/g, t.getMinutes()), a = a.replace(/ss|SS/, t.getSeconds() > 9 ? t.getSeconds().toString() : "0" + t.getSeconds()), 
        a = a.replace(/s|S/g, t.getSeconds());
    }, this.dateAdd = function(e, t, a) {
        switch (a = arguments[2] || new Date(), e) {
          case "s":
            return new Date(a.getTime() + 1e3 * t);

          case "n":
            return new Date(a.getTime() + 6e4 * t);

          case "h":
            return new Date(a.getTime() + 36e5 * t);

          case "d":
            return new Date(a.getTime() + 864e5 * t);

          case "w":
            return new Date(a.getTime() + 6048e5 * t);

          case "m":
            return new Date(a.getFullYear(), a.getMonth() + t, a.getDate(), a.getHours(), a.getMinutes(), a.getSeconds());

          case "y":
            return new Date(a.getFullYear() + t, a.getMonth(), a.getDate(), a.getHours(), a.getMinutes(), a.getSeconds());
        }
    }, this.dateDiff = function(e, t, a) {
        switch (e) {
          case "s":
            return parseInt((a - t) / 1e3);

          case "n":
            return parseInt((a - t) / 6e4);

          case "h":
            return parseInt((a - t) / 36e5);

          case "d":
            return parseInt((a - t) / 864e5);

          case "w":
            return parseInt((a - t) / 6048e5);

          case "m":
            return a.getMonth() + 1 + 12 * (a.getFullYear() - t.getFullYear()) - (t.getMonth() + 1);

          case "y":
            return a.getFullYear() - t.getFullYear();
        }
    }, this.strToDate = function(dateStr) {
        var data = dateStr, reCat = /(\d{1,4})/gm, t = data.match(reCat);
        return t[1] = t[1] - 1, eval("var d = new Date(" + t.join(",") + ");"), d;
    }, this.strFormatToDate = function(e, t) {
        var a = 0, n = -1, i = t.length;
        (n = e.indexOf("yyyy")) > -1 && n < i && (a = t.substr(n, 4));
        var r = 0;
        (n = e.indexOf("MM")) > -1 && n < i && (r = parseInt(t.substr(n, 2)) - 1);
        var o = 0;
        (n = e.indexOf("dd")) > -1 && n < i && (o = parseInt(t.substr(n, 2)));
        var s = 0;
        ((n = e.indexOf("HH")) > -1 || (n = e.indexOf("hh")) > 1) && n < i && (s = parseInt(t.substr(n, 2)));
        var u = 0;
        (n = e.indexOf("mm")) > -1 && n < i && (u = t.substr(n, 2));
        var l = 0;
        return (n = e.indexOf("ss")) > -1 && n < i && (l = t.substr(n, 2)), new Date(a, r, o, s, u, l);
    }, this.dateToLong = function(e) {
        return e.getTime();
    }, this.longToDate = function(e) {
        return new Date(e);
    }, this.isDate = function(e, t) {
        null == t && (t = "yyyyMMdd");
        var a = t.indexOf("yyyy");
        if (-1 == a) return !1;
        var n = e.substring(a, a + 4), i = t.indexOf("MM");
        if (-1 == i) return !1;
        var r = e.substring(i, i + 2), o = t.indexOf("dd");
        if (-1 == o) return !1;
        var s = e.substring(o, o + 2);
        return !(!isNumber(n) || n > "2100" || n < "1900") && (!(!isNumber(r) || r > "12" || r < "01") && !(s > getMaxDay(n, r) || s < "01"));
    }, this.getMaxDay = function(e, t) {
        return 4 == t || 6 == t || 9 == t || 11 == t ? "30" : 2 == t ? e % 4 == 0 && e % 100 != 0 || e % 400 == 0 ? "29" : "28" : "31";
    }, this.isNumber = function(e) {
        return /^\d+$/g.test(e);
    }, this.toArray = function(e) {
        e = arguments[0] || new Date();
        var t = Array();
        return t[0] = e.getFullYear(), t[1] = e.getMonth(), t[2] = e.getDate(), t[3] = e.getHours(), 
        t[4] = e.getMinutes(), t[5] = e.getSeconds(), t;
    }, this.datePart = function(e, t) {
        t = arguments[1] || new Date();
        var a = "", n = [ "日", "一", "二", "三", "四", "五", "六" ];
        switch (e) {
          case "y":
            a = t.getFullYear();
            break;

          case "M":
            a = t.getMonth() + 1;
            break;

          case "d":
            a = t.getDate();
            break;

          case "w":
            a = n[t.getDay()];
            break;

          case "ww":
            a = t.WeekNumOfYear();
            break;

          case "h":
            a = t.getHours();
            break;

          case "m":
            a = t.getMinutes();
            break;

          case "s":
            a = t.getSeconds();
        }
        return a;
    }, this.maxDayOfDate = function(e) {
        (e = arguments[0] || new Date()).setDate(1), e.setMonth(e.getMonth() + 1);
        var t = e.getTime() - 864e5;
        return new Date(t).getDate();
    };
}, util.isNumber = function(e) {
    return !!/^[0-9]*$/.test(e);
}, util.parseScene = function(e) {
    e = (e = decodeURIComponent(e)).split("/");
    for (var t = {}, a = 0; a < e.length; a++) {
        e[a] = e[a].split(":");
        var n = e[a][0], i = e[a][1];
        t[n] = i;
    }
    return t;
}, util.selectPindan = function(e) {
    var t = -1;
    return !(e.pindan_id > 0 || 0 == e.cart_pindan_id) && (wx.showModal({
        title: "温馨提示",
        content: "您正在参与拼单中，单独购买请先放弃拼单",
        confirmText: "放弃拼单",
        cancelText: "继续拼单",
        success: function(a) {
            a.confirm ? util.request({
                url: "wmall/store/goods/giveupPindan",
                data: {
                    sid: e.sid,
                    cart_id: e.cart_id
                },
                success: function(e) {
                    t = 0, util.getCurPage().setData({
                        pindan_id: t
                    });
                }
            }) : a.cancel && (t = e.cart_pindan_id, util.getCurPage().setData({
                pindan_id: t
            }));
        }
    }), t);
}, util.getCaptcha = function(e) {
    if (!e.url) return !1;
    wx.downloadFile({
        url: e.url,
        header: {
            "content-type": "image/png"
        },
        success: function(t) {
            console.log(111, t);
            var a = util.getCurPage(), n = {};
            return e.srcName ? n[e.srcName] = t.tempFilePath : n.captcha = t.tempFilePath, a.setData(n), 
            t.tempFilePath;
        },
        fail: function(e) {
            util.toast("图形验证码下载错误");
        }
    });
}, module.exports = util;