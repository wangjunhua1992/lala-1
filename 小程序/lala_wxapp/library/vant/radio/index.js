(0, require("../common/component").VantComponent)({
    field: !0,
    relation: {
        name: "radio-group",
        type: "ancestor"
    },
    classes: [ "icon-class", "label-class" ],
    props: {
        name: null,
        value: null,
        disabled: Boolean,
        labelDisabled: Boolean,
        labelPosition: String,
        checkedColor: String
    },
    computed: {
        iconClass: function() {
            var a = this.data, e = a.disabled, i = a.name, n = a.value;
            return this.classNames("van-radio__icon", {
                "van-radio__icon--disabled": e,
                "van-radio__icon--checked": !e && i === n,
                "van-radio__icon--check": !e && i !== n
            });
        }
    },
    methods: {
        emitChange: function(a) {
            var e = this.getRelationNodes("../radio-group/index")[0] || this;
            e.$emit("input", a), e.$emit("change", a);
        },
        onChange: function(a) {
            this.emitChange(a.detail.value);
        },
        onClickLabel: function() {
            this.data.disabled || this.data.labelDisabled || this.emitChange(this.data.name);
        }
    }
});