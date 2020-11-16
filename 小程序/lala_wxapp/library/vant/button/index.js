var n = require("../common/component"), t = require("../mixins/button"), a = require("../mixins/open-type");

(0, n.VantComponent)({
    classes: [ "loading-class" ],
    mixins: [ t.button, a.openType ],
    props: {
        plain: Boolean,
        block: Boolean,
        round: Boolean,
        square: Boolean,
        loading: Boolean,
        disabled: Boolean,
        type: {
            type: String,
            value: "default"
        },
        size: {
            type: String,
            value: "normal"
        }
    },
    computed: {
        classes: function() {
            var n = this.data, t = n.type, a = n.size, o = n.block, e = n.plain, i = n.round, s = n.square, l = n.loading, u = n.disabled;
            return this.classNames("custom-class", "van-button", "van-button--" + t, "van-button--" + a, {
                "van-button--block": o,
                "van-button--round": i,
                "van-button--plain": e,
                "van-button--square": s,
                "van-button--loading": l,
                "van-button--disabled": u,
                "van-button--unclickable": u || l
            });
        }
    },
    methods: {
        onClick: function() {
            this.data.disabled || this.data.loading || this.$emit("click");
        }
    }
});