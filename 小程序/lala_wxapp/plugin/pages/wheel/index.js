var t = getApp();

Page({
    data: {
        activeNum: -1,
        init: {
            index: 1,
            fast: 4,
            num: 8,
            num_t: 4,
            cycle: 3,
            flag: !1,
            lucky: "",
            cycle_default: 3,
            speed: 100
        },
        prize: {
            note: "",
            type: "",
            id: ""
        },
        navshow: !1,
        Lang: t.Lang,
        wuiLoading: {
            show: !0,
            img: t.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(a) {
        var i = this;
        i.data.options = a, t.util.request({
            url: "wheel/activity/index",
            data: {
                id: a.id || 12,
                menufooter: 1,
                _navc: 1
            },
            success: function(a) {
                t.util.loaded();
                var e = a.data.message;
                if (e.errno) return t.util.toast(e.message), !1;
                i.setData(e.message);
            }
        });
    },
    onStartDraw: function() {
        var a = this;
        if (a.data.init.flag) return !1;
        a.data.init.flag = !0, t.util.request({
            url: "wheel/activity/index",
            data: {
                id: a.options.id || 12,
                order_id: a.options.order_id
            },
            method: "POST",
            showLoading: !1,
            success: function(i) {
                var e = i.data.message;
                if (e.errno) return t.util.toast(e.message), !1;
                a.data.init.lucky = e.luckyNum;
                var n = a.data.init.lucky;
                "1" != n && "4" != n && "7" != n || (a.data.prize.type = e.award.type, a.data.prize.id = e.id), 
                a.data.prize.note = e.message, a.data.activeNum = 1, a.setData({
                    activeNum: a.data.activeNum
                }), a.data.init.fast = a.rand(5, 6), a.data.init.cycle_default = a.data.init.cycle = a.rand(3, 5), 
                a.data.init.speed = 300, a.showLottery();
            }
        });
    },
    rand: function(t, a) {
        var i = a - t, e = Math.random();
        return t + Math.round(e * i);
    },
    showLottery: function() {
        var a = this;
        a.data.init.index > a.data.init.num && (a.data.init.index = 1, a.data.init.cycle--);
        var i = a.data.init.num + 1, e = a.data.init.lucky - a.data.init.num_t, n = e >= 0 ? 0 : 1, d = e >= 0 ? e > 0 ? e : 1 : i + e;
        if (a.setData({
            activeNum: a.data.init.index
        }), a.data.init.index > a.data.init.fast && a.data.init.cycle == a.data.init.cycle_default && (a.data.init.speed = 100), 
        (a.data.init.cycle == n && a.data.init.index >= d || a.data.init.cycle < n) && (a.data.init.speed = a.data.init.speed + 200), 
        a.data.init.cycle <= 0 && a.data.init.index == a.data.init.lucky) clearTimeout(s), 
        setTimeout(function() {
            t.util.toast(a.data.prize.note), a.data.init.flag = !1, a.data.prize.type = 0, a.data.prize.note = "", 
            a.data.init.index = 1, a.setData({
                activeNum: -1
            });
        }, 1e3), a.data.init.flag = !1; else {
            a.data.init.index++;
            var s = setTimeout(a.showLottery, a.data.init.speed);
        }
    },
    onToggleNavsShow: function() {
        this.setData({
            navshow: !this.data.navshow
        });
    },
    onJsEvent: function(a) {
        t.util.jsEvent(a);
    }
});