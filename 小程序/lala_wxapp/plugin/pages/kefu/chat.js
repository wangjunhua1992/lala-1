function t(t, a, e) {
    return a in t ? Object.defineProperty(t, a, {
        value: e,
        enumerable: !0,
        configurable: !0,
        writable: !0
    }) : t[a] = e, t;
}

var a, e = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(t) {
    return typeof t;
} : function(t) {
    return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : typeof t;
}, o = getApp();

Page((a = {
    data: {
        relation: "member2clerk",
        kefuopenid: 0,
        orderid: 0,
        kefuunionid: 0,
        chatlog: {
            min: 0,
            psize: 100,
            loading: !1,
            finished: !1,
            data: []
        },
        content: "",
        fastReplyMsg: "",
        scrollTop: 0,
        hasSendOrder: !1,
        popup: {
            fastReply: !1,
            order: !1
        },
        status: {
            fastReply: !1,
            others: !1
        },
        chatLogBottom: 96,
        islegal: !1,
        Lang: o.Lang,
        wuiLoading: {
            show: !0,
            img: o.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(t) {
        var a = this;
        t.relation && (a.data.relation = t.relation), t.kefuopenid && (a.data.kefuopenid = t.kefuopenid), 
        t.orderid && (a.data.orderid = t.orderid), t.kefuunionid && (a.data.kefuunionid = t.kefuunionid), 
        o.util.request({
            url: "kefu/member/chat",
            data: {
                relation: a.data.relation,
                kefuopenid: a.data.kefuopenid,
                kefuunionid: a.data.kefuunionid,
                orderid: a.data.orderid,
                min: a.data.chatlog.min,
                psize: a.data.chatlog.psize
            },
            success: function(t) {
                o.util.loaded();
                var e = t.data.message;
                if (0 != e.errno) return o.util.toast(e.message), !1;
                (e = e.message).chatlog.logs && e.chatlog.logs.length > 0 && (a.data.chatlog.data = a.data.chatlog.data.concat(e.chatlog.logs), 
                e.chatlog.logs.length < a.data.chatlog.psize && (a.data.chatlog.finished = !0)), 
                a.data.chatlog.min = e.chatlog.min, a.data.chatlog.min || (a.data.chatlog.finished = !0), 
                a.setData({
                    chat: e.chat,
                    kefu: e.kefu,
                    fans: e.fans,
                    order: e.order,
                    fastReply: e.reply,
                    chatlog: a.data.chatlog,
                    islegal: !0
                }, function() {
                    a.onScrollBottom(), a.onCalculateBottom();
                }), wx.setNavigationBarTitle({
                    title: e.kefu.title
                }), o.iwebsocket.onGetMessage = a.onGetMessage;
            }
        });
    },
    onGetMessage: function(t) {
        var a = this;
        a.data.chat && a.data.chat.id == t.chat.chatid && (a.data.chatlog.data.push(t.chat), 
        a.setData({
            chatlog: a.data.chatlog
        }, function() {
            a.onSetNotreadZero(), a.onScrollBottom();
        }));
    },
    onSetNotreadZero: function() {
        var t = this;
        o.util.request({
            url: "kefu/member/zero",
            data: {
                chatid: t.data.chat.id
            },
            success: function(t) {
                var a = t.data.message;
                if (0 != a.errno) return o.util.toast(a.message), !1;
                console.log("未读消息清零成功");
            }
        });
    },
    onSendMessage: function(t, a) {
        var n = this;
        if (!n.data.islegal) return !1;
        var s = "";
        if (null !== t && "object" == (void 0 === t ? "undefined" : e(t)) ? (s = t.currentTarget.dataset.content, 
        a = t.currentTarget.dataset.type) : s = t, !s || "" == s) return !1;
        a || (a = "text"), n.data.islegal = !1, o.util.request({
            url: "kefu/member/addchat",
            method: "POST",
            data: {
                chatid: n.data.chat.id,
                type: a,
                content: s
            },
            success: function(t) {
                n.data.islegal = !0;
                var e = t.data.message;
                if (0 != e.errno) return o.util.toast(e.message), !1;
                var s = {};
                (e = e.message).log && (n.data.chatlog.data.push(e.log), s.chatlog = n.data.chatlog), 
                "text" == a ? s.content = "" : "orderTakeout" == a && (s.hasSendOrder = !0), n.setData(s, function() {
                    n.onScrollBottom();
                });
            }
        });
    },
    onLoadMore: function() {
        var t = this;
        if (t.data.chatlog.finished) return o.util.toast("没有更多消息了"), !1;
        o.util.request({
            url: "kefu/member/more",
            data: {
                chatid: t.data.chat.id,
                min: t.data.chatlog.min,
                psize: t.data.chatlog.psize
            },
            success: function(a) {
                var e = a.data.message;
                if (0 != e.errno) return o.util.toast(e.message), !1;
                (e = e.message).chatlog.logs && e.chatlog.logs.length > 0 && (t.data.chatlog.data = e.chatlog.logs.concat(t.data.chatlog.data), 
                e.chatlog.logs.length < t.data.chatlog.psize && (t.data.chatlog.finished = !0)), 
                t.data.chatlog.min = e.chatlog.min, t.data.chatlog.min || (t.data.chatlog.finished = !0), 
                t.setData({
                    chatlog: t.data.chatlog
                });
            }
        });
    },
    onConfirmFastReply: function() {
        this.onTogglePopup("fastReply");
    },
    onOrderClick: function(t) {
        var a = this, e = t.currentTarget.dataset.id;
        e > 0 && (a.onSendMessage(e, "orderTakeout"), a.onTogglePopup("order"));
    },
    onUploadImg: function(t) {
        var a = this;
        wx.showLoading({
            title: "上传中..."
        }), o.util.image({
            count: 1,
            success: function(t) {
                t.url && t.filename && a.onSendMessage(t.filename, "image");
            },
            complete: function() {
                wx.hideLoading();
            }
        });
    }
}, t(a, "onConfirmFastReply", function() {
    var t = this, a = t.data.fastReplyMsg;
    if (!a || "" == a) return !1;
    o.util.request({
        url: "kefu/member/addreply",
        method: "POST",
        data: {
            content: a,
            relation: t.data.relation
        },
        success: function(a) {
            var e = a.data.message;
            if (0 != e.errno) return o.util.toast(e.message), !1;
            (e = e.message).reply && e.reply.length > 0 && (t.data.fastReply = e.reply), t.setData({
                fastReply: t.data.fastReply,
                faseReplyMsg: ""
            }), t.onTogglePopup("fastReply");
        }
    });
}), t(a, "onFastReplyInput", function(t) {
    this.setData({
        fastReplyMsg: t.detail
    });
}), t(a, "onShowOrders", function() {
    var t = this;
    o.util.request({
        url: "kefu/member/order",
        data: {
            chatid: t.data.chat.id
        },
        success: function(a) {
            var e = a.data.message;
            if (0 != e.errno) return o.util.toast(e.message), !1;
            var n = e.message.orders;
            if (n && n.length > 0) t.setData({
                orders: n
            }), t.onTogglePopup("order"); else {
                var s = "";
                "member2clerk" == t.data.relation ? s = "您最近未在该门店下过单" : "member2deliveryer" == t.data.relation ? s = "该配送员最近未给您配送过订单" : "member2kefu" == t.data.relation && (s = "您暂未在平台下过单"), 
                o.util.toast(s);
            }
        }
    });
}), t(a, "onCalculateBottom", function() {
    var t = this, a = 96;
    2 == t.data.chat.status ? a = 56 : t.data.status.fastReply ? a = 272 : t.data.status.others && (a = 252), 
    t.setData({
        chatLogBottom: a
    }, function() {
        t.onScrollBottom();
    });
}), t(a, "onToggleStatus", function(t) {
    var a = this, e = t;
    if (t.currentTarget && (e = t.currentTarget.dataset.key), !e) return !1;
    for (var o in a.data.status) a.data.status[o] = o == e && !a.data.status[o];
    a.setData({
        status: a.data.status
    }, function() {
        a.onCalculateBottom();
    });
}), t(a, "onTogglePopup", function(t) {
    var a = this, e = t;
    if (t.currentTarget && (e = t.currentTarget.dataset.key), !e) return !1;
    a.data.popup[e] = !a.data.popup[e], a.setData({
        popup: a.data.popup
    });
}), t(a, "onScrollBottom", function() {
    var t = this;
    t.setData({
        scrollTop: 1e3 * t.data.chatlog.data.length
    });
}), t(a, "onBlur", function() {}), t(a, "onFocus", function(t) {
    var a = this;
    a.setData({
        status: {
            fastReply: !1,
            others: !1
        }
    }, function() {
        a.onCalculateBottom();
    });
}), t(a, "onBindContentInput", function(t) {
    this.setData({
        content: t.detail.value
    });
}), t(a, "onImagePreview", function(t) {
    o.util.showImage(t);
}), t(a, "onJsEvent", function(t) {
    o.util.jsEvent(t);
}), a));