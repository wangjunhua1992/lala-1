function r() {
    for (var t = [], n = 0; n < arguments.length; n++) {
        var l = arguments[n];
        if (l) {
            var s = void 0 === l ? "undefined" : e(l);
            if ("string" === s || "number" === s) t.push(l); else if (Array.isArray(l) && l.length) {
                var f = r.apply(null, l);
                f && t.push(f);
            } else if ("object" === s) for (var i in l) o.call(l, i) && l[i] && t.push(i);
        }
    }
    return t.join(" ");
}

Object.defineProperty(exports, "__esModule", {
    value: !0
});

var e = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(r) {
    return typeof r;
} : function(r) {
    return r && "function" == typeof Symbol && r.constructor === Symbol && r !== Symbol.prototype ? "symbol" : typeof r;
};

exports.classNames = r;

var o = {}.hasOwnProperty;