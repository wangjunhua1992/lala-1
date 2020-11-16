Object.defineProperty(exports, "__esModule", {
    value: !0
}), exports.basic = void 0;

var e = require("../common/class-names");

exports.basic = Behavior({
    methods: {
        classNames: e.classNames,
        $emit: function() {
            this.triggerEvent.apply(this, arguments);
        },
        getRect: function(e, t) {
            var s = this;
            return new Promise(function(r) {
                wx.createSelectorQuery().in(s)[t ? "selectAll" : "select"](e).boundingClientRect(function(e) {
                    t && Array.isArray(e) && e.length && r(e), !t && e && r(e);
                }).exec();
            });
        }
    }
});