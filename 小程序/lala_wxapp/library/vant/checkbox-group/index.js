(0, require("../common/component").VantComponent)({
    field: !0,
    relation: {
        name: "checkbox",
        type: "descendant",
        linked: function(e) {
            var a = this.data, t = a.value, n = a.disabled;
            e.setData({
                value: -1 !== t.indexOf(e.data.name),
                disabled: n || e.data.disabled
            });
        }
    },
    props: {
        value: Array,
        disabled: Boolean,
        max: Number
    },
    watch: {
        value: function(e) {
            this.getRelationNodes("../checkbox/index").forEach(function(a) {
                a.setData({
                    value: -1 !== e.indexOf(a.data.name)
                });
            });
        },
        disabled: function(e) {
            this.getRelationNodes("../checkbox/index").forEach(function(a) {
                a.setData({
                    disabled: e || a.data.disabled
                });
            });
        }
    }
});