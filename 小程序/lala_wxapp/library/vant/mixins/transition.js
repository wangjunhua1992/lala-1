Object.defineProperty(exports, "__esModule", {
    value: !0
});

exports.transition = function(t) {
    return Behavior({
        properties: {
            customStyle: String,
            show: {
                type: Boolean,
                value: t,
                observer: "observeShow"
            },
            duration: {
                type: Number,
                value: 300
            }
        },
        data: {
            type: "",
            inited: !1,
            display: !1
        },
        attached: function() {
            this.data.show && this.show();
        },
        methods: {
            observeShow: function(t) {
                t ? this.show() : this.setData({
                    type: "leave"
                });
            },
            show: function() {
                this.setData({
                    inited: !0,
                    display: !0,
                    type: "enter"
                });
            },
            onAnimationEnd: function() {
                this.data.show || this.setData({
                    display: !1
                });
            }
        }
    });
};