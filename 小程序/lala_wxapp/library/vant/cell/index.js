function e(e, i, l) {
    return i in e ? Object.defineProperty(e, i, {
        value: l,
        enumerable: !0,
        configurable: !0,
        writable: !0
    }) : e[i] = l, e;
}

var i = require("../mixins/link");

(0, require("../common/component").VantComponent)({
    classes: [ "title-class", "label-class", "value-class", "right-icon-class" ],
    mixins: [ i.link ],
    props: {
        title: null,
        value: null,
        icon: String,
        size: String,
        label: String,
        center: Boolean,
        isLink: Boolean,
        required: Boolean,
        clickable: Boolean,
        titleWidth: String,
        customStyle: String,
        arrowDirection: String,
        border: {
            type: Boolean,
            value: !0
        }
    },
    computed: {
        cellClass: function() {
            var i = this.data;
            return this.classNames("custom-class", "van-cell", e({
                "van-cell--center": i.center,
                "van-cell--required": i.required,
                "van-cell--borderless": !i.border,
                "van-cell--clickable": i.isLink || i.clickable
            }, "van-cell--" + i.size, i.size));
        },
        titleStyle: function() {
            var e = this.data.titleWidth;
            return e ? "max-width: " + e + ";min-width: " + e : "";
        },
        iconWrapClass: function() {
            var e = "van-cell__right-icon-wrap right-icon-class";
            return this.classNames(e, e + "--" + this.data.arrowDirection);
        }
    },
    methods: {
        onClick: function(e) {
            this.$emit("click", e.detail), this.jumpLink();
        }
    }
});