var e = function(e) {
    return e && e.__esModule ? e : {
        default: e
    };
}(require("./util")), t = {
    ws: null,
    init: function() {
        return t.ws = wx.connectSocket({
            url: "ws://127.0.0.1:2345"
        }), t.ws.onOpen(function(s) {
            console.log("websocket连接成功"), t.heartCheck.start(), e.default.removeStorageSync("wsInitTimes");
        }), t.ws.onError(function(s) {
            var n = e.default.getStorageSync("wsInitTimes");
            n && n.times || (n = {
                times: 0
            }), n.times < 3 && (n.times++, e.default.setStorageSync("wsInitTimes", n, 300), 
            t.init());
        }), t.ws.onClose(function(e) {
            console.log("websocket连接已关闭");
        }), t.ws.onMessage(function(s) {
            t.heartCheck.start();
            var n = JSON.parse(s.data), i = n.type, o = n.data;
            switch (i) {
              case "connect":
                e.default.request({
                    url: "kefu/bind/member",
                    data: {
                        client_id: o.clientId
                    },
                    success: function(e) {}
                });
                break;

              case "message":
                "function" == typeof t.onGetMessage && t.onGetMessage(o);
            }
        }), t;
    },
    onGetMessage: function() {},
    onSend: function(e, s) {
        1 == t.ws.readyState && t.ws.send({
            data: JSON.stringify(e),
            success: function(e) {
                "function" == typeof s && s();
            }
        });
    },
    heartCheck: {
        timeout: 2e4,
        timeoutObj: null,
        serverTimeoutObj: null,
        reset: function() {
            return this.timeoutObj && clearTimeout(this.timeoutObj), this.serverTimeoutObj && clearTimeout(this.serverTimeoutObj), 
            this;
        },
        start: function() {
            var e = this;
            this.reset(), this.timeoutObj = setTimeout(function() {
                var s = {
                    type: "ping",
                    data: {}
                };
                1 == t.ws.readyState && t.onSend(s), e.serverTimeoutObj = setTimeout(function() {
                    t.ws.close();
                }, e.timeout);
            }, this.timeout);
        }
    }
}, s = t.init();

module.exports = s;