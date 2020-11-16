var a = getApp();

Component({
    options: {
        addGlobalClass: !0
    },
    properties: {
        danmus: {
            type: Array,
            value: []
        }
    },
    data: {
        danmu: {
            index: 0,
            item: {},
            show: !1
        }
    },
    ready: function() {
        var a = this;
        setTimeout(function() {
            a.data.danmus && (a.onToggleDanmu(), setInterval(function() {
                a.onToggleDanmu();
            }, 5e3));
        }, 5e3);
    },
    lifetimes: {
        created: function() {},
        attached: function() {},
        ready: function() {
            var a = this;
            setTimeout(function() {
                a.data.danmus && (a.onToggleDanmu(), setInterval(function() {
                    a.onToggleDanmu();
                }, 5e3));
            }, 5e3);
        }
    },
    methods: {
        onToggleDanmu: function() {
            var a = this;
            a.data.danmu.item = a.data.danmus[a.data.danmu.index], a.data.danmu.show = !0, a.data.danmu.index == a.data.danmus.length - 1 ? a.data.danmu.index = 0 : a.data.danmu.index++, 
            a.setData({
                danmu: a.data.danmu
            }), setTimeout(function() {
                a.data.danmu.show = !1, a.setData({
                    danmu: a.data.danmu
                });
            }, 2500);
        },
        onJsEvent: function(n) {
            a.util.jsEvent(n);
        }
    }
});