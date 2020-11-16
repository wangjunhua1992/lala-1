(0, require("../common/component").VantComponent)({
    classes: [ "content-class" ],
    relation: {
        name: "collapse",
        type: "ancestor",
        linked: function(t) {
            this.parent = t;
        }
    },
    props: {
        name: [ String, Number ],
        icon: String,
        label: String,
        title: [ String, Number ],
        value: [ String, Number ],
        disabled: Boolean,
        border: {
            type: Boolean,
            value: !0
        },
        isLink: {
            type: Boolean,
            value: !0
        }
    },
    data: {
        contentHeight: 0,
        expanded: !1
    },
    computed: {
        titleClass: function() {
            var t = this.data, e = t.disabled, a = t.expanded;
            return this.classNames("van-collapse-item__title", {
                "van-collapse-item__title--disabled": e,
                "van-collapse-item__title--expanded": a
            });
        }
    },
    methods: {
        updateExpanded: function() {
            if (!this.parent) return null;
            var t = this.parent.data, e = t.value, a = t.accordion, n = t.items, i = this.data.name, s = n.indexOf(this), l = null == i ? s : i, d = a ? e === l : e.some(function(t) {
                return t === l;
            });
            d !== this.data.expanded && this.updateStyle(d), this.setData({
                expanded: d
            });
        },
        updateStyle: function(t) {
            var e = this;
            t ? this.getRect(".van-collapse-item__content").then(function(t) {
                e.setData({
                    contentHeight: t.height ? t.height + "px" : null
                });
            }) : this.setData({
                contentHeight: 0
            });
        },
        onClick: function() {
            if (!this.data.disabled) {
                var t = this.data, e = t.name, a = t.expanded, n = this.parent.data.items.indexOf(this), i = null == e ? n : e;
                this.parent.switch(i, !a);
            }
        }
    }
});