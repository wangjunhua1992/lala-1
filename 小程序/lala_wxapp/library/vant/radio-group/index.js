(0, require("../common/component").VantComponent)({
    field: !0,
    relation: {
        name: "radio",
        type: "descendant",
        linked: function(a) {
            var e = this.data, t = e.value, d = e.disabled;
            a.setData({
                value: t,
                disabled: d || a.data.disabled
            });
        }
    },
    props: {
        value: null,
        disabled: Boolean
    },
    watch: {
        value: function(a) {
            this.getRelationNodes("../radio/index").forEach(function(e) {
                e.setData({
                    value: a
                });
            });
        },
        disabled: function(a) {
            this.getRelationNodes("../radio/index").forEach(function(e) {
                e.setData({
                    disabled: a || e.data.disabled
                });
            });
        }
    }
});