(0, require("../common/component").VantComponent)({
    props: {
        text: {
            type: String,
            value: ""
        },
        mode: {
            type: String,
            value: ""
        },
        url: {
            type: String,
            value: ""
        },
        openType: {
            type: String,
            value: "navigate"
        },
        delay: {
            type: Number,
            value: 0
        },
        speed: {
            type: Number,
            value: 50
        },
        scrollable: {
            type: Boolean,
            value: !0
        },
        leftIcon: {
            type: String,
            value: ""
        },
        color: {
            type: String,
            value: "#ed6a0c"
        },
        backgroundColor: {
            type: String,
            value: "#fffbe8"
        }
    },
    data: {
        show: !0,
        hasRightIcon: !1,
        width: void 0,
        wrapWidth: void 0,
        elapse: void 0,
        animation: null,
        resetAnimation: null,
        timer: null
    },
    watch: {
        text: function() {
            this.setData({}, this.init);
        }
    },
    created: function() {
        this.data.mode && this.setData({
            hasRightIcon: !0
        });
    },
    destroyed: function() {
        var t = this.data.timer;
        t && clearTimeout(t);
    },
    methods: {
        init: function() {
            var t = this;
            this.getRect(".van-notice-bar__content").then(function(e) {
                e && e.width && (t.setData({
                    width: e.width
                }), t.getRect(".van-notice-bar__content-wrap").then(function(e) {
                    if (e && e.width) {
                        var a = e.width, i = t.data, n = i.width, o = i.speed, r = i.scrollable, s = i.delay;
                        if (r && a < n) {
                            var l = n / o * 1e3, c = wx.createAnimation({
                                duration: l,
                                timeingFunction: "linear",
                                delay: s
                            }), u = wx.createAnimation({
                                duration: 0,
                                timeingFunction: "linear"
                            });
                            t.setData({
                                elapse: l,
                                wrapWidth: a,
                                animation: c,
                                resetAnimation: u
                            }, function() {
                                t.scroll();
                            });
                        }
                    }
                }));
            });
        },
        scroll: function() {
            var t = this, e = this.data, a = e.animation, i = e.resetAnimation, n = e.wrapWidth, o = e.elapse, r = e.speed;
            i.translateX(n).step();
            var s = a.translateX(-o * r / 1e3).step();
            this.setData({
                animationData: i.export()
            }), setTimeout(function() {
                t.setData({
                    animationData: s.export()
                });
            }, 100);
            var l = setTimeout(function() {
                t.scroll();
            }, o);
            this.setData({
                timer: l
            });
        },
        onClickIcon: function() {
            var t = this.data.timer;
            t && clearTimeout(t), this.setData({
                show: !1,
                timer: null
            });
        },
        onClick: function(t) {
            this.$emit("click", t);
        }
    }
});