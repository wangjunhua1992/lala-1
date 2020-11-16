(0, require("../common/component").VantComponent)({
    props: {
        items: Array,
        mainActiveIndex: {
            type: Number,
            value: 0
        },
        activeId: {
            type: Number,
            value: 0
        },
        maxHeight: {
            type: Number,
            value: 300
        }
    },
    data: {
        subItems: [],
        mainHeight: 0,
        itemHeight: 0
    },
    watch: {
        items: function() {
            this.updateSubItems(), this.updateMainHeight();
        },
        maxHeight: function() {
            this.updateItemHeight(), this.updateMainHeight();
        },
        mainActiveIndex: "updateSubItems"
    },
    methods: {
        onSelectItem: function(t) {
            var e = t.currentTarget.dataset.item;
            e.disabled || this.$emit("click-item", e);
        },
        onClickNav: function(t) {
            var e = t.currentTarget.dataset.index;
            this.$emit("click-nav", {
                index: e
            });
        },
        updateSubItems: function() {
            var t = this.data.items[this.data.mainActiveIndex] || {};
            this.setData({
                subItems: t.children || []
            }), this.updateItemHeight();
        },
        updateMainHeight: function() {
            var t = Math.max(44 * this.data.items.length, 44 * this.data.subItems.length);
            this.setData({
                mainHeight: Math.min(t, this.data.maxHeight)
            });
        },
        updateItemHeight: function() {
            this.setData({
                itemHeight: Math.min(44 * this.data.subItems.length, this.data.maxHeight)
            });
        }
    }
});