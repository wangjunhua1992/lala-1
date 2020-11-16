(0, require("../common/component").VantComponent)({
    field: !0,
    relation: {
        name: "checkbox-group",
        type: "ancestor"
    },
    classes: [ "icon-class", "label-class" ],
    props: {
        value: null,
        disabled: Boolean,
        useIconSlot: Boolean,
        checkedColor: String,
        labelPosition: String,
        labelDisabled: Boolean,
        shape: {
            type: String,
            value: "round"
        }
    },
    computed: {
        iconClass: function() {
            var e = this.data, a = e.disabled, t = e.value, i = e.shape;
            return this.classNames("van-checkbox__icon", "van-checkbox__icon--" + i, {
                "van-checkbox__icon--disabled": a,
                "van-checkbox__icon--checked": t
            });
        },
        iconStyle: function() {
            var e = this.data, a = e.value, t = e.disabled, i = e.checkedColor;
            return i && a && !t ? "border-color: " + i + "; background-color: " + i : "";
        }
    },
    methods: {
        emitChange: function(e) {
            var a = this.getRelationNodes("../checkbox-group/index")[0];
            a ? this.setParentValue(a, e) : (this.$emit("input", e), this.$emit("change", e));
        },
        toggle: function() {
            this.data.disabled || this.emitChange(!this.data.value);
        },
        onClickLabel: function() {
            this.data.disabled || this.data.labelDisabled || this.emitChange(!this.data.value);
        },
        setParentValue: function(e, a) {
            var t = e.data.value.slice(), i = this.data.name;
            if (a) {
                if (e.data.max && t.length >= e.data.max) return;
                -1 === t.indexOf(i) && (t.push(i), e.$emit("input", t), e.$emit("change", t));
            } else {
                var n = t.indexOf(i);
                -1 !== n && (t.splice(n, 1), e.$emit("input", t), e.$emit("change", t));
            }
        }
    }
});