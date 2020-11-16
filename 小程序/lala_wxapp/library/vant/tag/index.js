function a(a, n, r) {
    return n in a ? Object.defineProperty(a, n, {
        value: r,
        enumerable: !0,
        configurable: !0,
        writable: !0
    }) : a[n] = r, a;
}

var n = require("../common/component"), r = require("../common/color"), o = {
    danger: r.RED,
    primary: r.BLUE,
    success: r.GREEN
};

(0, n.VantComponent)({
    props: {
        size: String,
        type: String,
        mark: Boolean,
        color: String,
        plain: Boolean,
        round: Boolean
    },
    computed: {
        classes: function() {
            var n, r = this.data;
            return this.classNames("van-tag", "custom-class", (n = {
                "van-tag--mark": r.mark,
                "van-tag--plain": r.plain,
                "van-tag--round": r.round
            }, a(n, "van-tag--" + r.size, r.size), a(n, "van-hairline--surround", r.plain), 
            n));
        },
        style: function() {
            var a = this.data.color || o[this.data.type] || "#999";
            return (this.data.plain ? "color" : "background-color") + ": " + a;
        }
    }
});