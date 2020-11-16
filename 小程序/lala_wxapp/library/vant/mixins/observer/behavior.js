Object.defineProperty(exports, "__esModule", {
    value: !0
});

exports.behavior = Behavior({
    created: function() {
        var t = this;
        if (this.$options) {
            var e = {}, a = this.setData, i = this.$options().computed, s = Object.keys(i), o = function() {
                var a = {};
                return s.forEach(function(s) {
                    var o = i[s].call(t);
                    e[s] !== o && (e[s] = a[s] = o);
                }), a;
            };
            Object.defineProperty(this, "setData", {
                writable: !0
            }), this.setData = function(e, i) {
                e && a.call(t, e, i), a.call(t, o());
            };
        }
    },
    attached: function() {
        this.setData();
    }
});