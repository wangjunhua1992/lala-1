function t(t, e, a) {
    return e in t ? Object.defineProperty(t, e, {
        value: a,
        enumerable: !0,
        configurable: !0,
        writable: !0
    }) : t[e] = a, t;
}

(0, require("../common/component").VantComponent)({
    relation: {
        name: "row",
        type: "ancestor"
    },
    props: {
        span: Number,
        offset: Number
    },
    data: {
        style: ""
    },
    computed: {
        classes: function() {
            var e, a = this.data, n = a.span, s = a.offset;
            return this.classNames("custom-class", "van-col", (e = {}, t(e, "van-col--" + n, n), 
            t(e, "van-col--offset-" + s, s), e));
        }
    },
    methods: {
        setGutter: function(t) {
            var e = t / 2 + "px", a = t ? "padding-left: " + e + "; padding-right: " + e + ";" : "";
            a !== this.data.style && this.setData({
                style: a
            });
        }
    }
});