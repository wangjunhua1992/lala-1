(0, require("../common/component").VantComponent)({
    field: !0,
    props: {
        title: String,
        border: Boolean,
        checked: Boolean,
        loading: Boolean,
        disabled: Boolean,
        size: {
            type: String,
            value: "26px"
        }
    },
    watch: {
        checked: function(e) {
            this.setData({
                value: e
            });
        }
    },
    created: function() {
        this.setData({
            value: this.data.checked
        });
    },
    methods: {
        onChange: function(e) {
            this.$emit("change", e.detail);
        }
    }
});