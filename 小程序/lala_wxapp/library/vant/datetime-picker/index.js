function e(e, t, n) {
    return t in e ? Object.defineProperty(e, t, {
        value: n,
        enumerable: !0,
        configurable: !0,
        writable: !0
    }) : e[t] = n, e;
}

function t(e, t, n) {
    return Math.min(Math.max(e, t), n);
}

var n = require("../common/component"), a = require("../common/utils"), u = new Date().getFullYear(), i = function(e) {
    return (0, a.isDef)(e) && !isNaN(new Date(e).getTime());
};

(0, n.VantComponent)({
    props: {
        value: null,
        title: String,
        loading: Boolean,
        itemHeight: {
            type: Number,
            value: 44
        },
        visibleItemCount: {
            type: Number,
            value: 5
        },
        confirmButtonText: {
            type: String,
            value: "确认"
        },
        cancelButtonText: {
            type: String,
            value: "取消"
        },
        type: {
            type: String,
            value: "datetime"
        },
        showToolbar: {
            type: Boolean,
            value: !0
        },
        minDate: {
            type: Number,
            value: new Date(u - 10, 0, 1).getTime()
        },
        maxDate: {
            type: Number,
            value: new Date(u + 10, 11, 31).getTime()
        },
        minHour: {
            type: Number,
            value: 0
        },
        maxHour: {
            type: Number,
            value: 23
        },
        minMinute: {
            type: Number,
            value: 0
        },
        maxMinute: {
            type: Number,
            value: 59
        }
    },
    data: {
        pickerValue: [],
        innerValue: Date.now()
    },
    computed: {
        columns: function() {
            var e = this;
            return this.getRanges().map(function(t) {
                var n = t.type, a = t.range;
                return e.times(a[1] - a[0] + 1, function(t) {
                    var u = a[0] + t;
                    return u = "year" === n ? "" + u : e.pad(u);
                });
            });
        }
    },
    watch: {
        value: function(e) {
            var t = this, n = this.data;
            (e = this.correctValue(e)) === n.innerValue || this.setData({
                innerValue: e
            }, function() {
                t.updateColumnValue(e), t.$emit("input", e);
            });
        }
    },
    methods: {
        getRanges: function() {
            var e = this.data;
            if ("time" === e.type) return [ {
                type: "hour",
                range: [ e.minHour, e.maxHour ]
            }, {
                type: "minute",
                range: [ e.minMinute, e.maxMinute ]
            } ];
            var t = this.getBoundary("max", e.innerValue), n = t.maxYear, a = t.maxDate, u = t.maxMonth, i = t.maxHour, r = t.maxMinute, o = this.getBoundary("min", e.innerValue), l = o.minYear, m = o.minDate, s = [ {
                type: "year",
                range: [ l, n ]
            }, {
                type: "month",
                range: [ o.minMonth, u ]
            }, {
                type: "day",
                range: [ m, a ]
            }, {
                type: "hour",
                range: [ o.minHour, i ]
            }, {
                type: "minute",
                range: [ o.minMinute, r ]
            } ];
            return "date" === e.type && s.splice(3, 2), "year-month" === e.type && s.splice(2, 3), 
            s;
        },
        pad: function(e) {
            return ("00" + e).slice(-2);
        },
        correctValue: function(e) {
            var n = this.data, a = this.pad, u = "time" !== n.type;
            if (u && !i(e) ? e = n.minDate : u || e || (e = a(n.minHour) + ":00"), !u) {
                var r = e.split(":"), o = r[0], l = r[1];
                return o = a(t(o, n.minHour, n.maxHour)), l = a(t(l, n.minMinute, n.maxMinute)), 
                o + ":" + l;
            }
            return e = Math.max(e, n.minDate), e = Math.min(e, n.maxDate);
        },
        times: function(e, t) {
            for (var n = -1, a = Array(e); ++n < e; ) a[n] = t(n);
            return a;
        },
        getBoundary: function(t, n) {
            var a, u = new Date(n), i = new Date(this.data[t + "Date"]), r = i.getFullYear(), o = 1, l = 1, m = 0, s = 0;
            return "max" === t && (o = 12, l = this.getMonthEndDay(u.getFullYear(), u.getMonth() + 1), 
            m = 23, s = 59), u.getFullYear() === r && (o = i.getMonth() + 1, u.getMonth() + 1 === o && (l = i.getDate(), 
            u.getDate() === l && (m = i.getHours(), u.getHours() === m && (s = i.getMinutes())))), 
            a = {}, e(a, t + "Year", r), e(a, t + "Month", o), e(a, t + "Date", l), e(a, t + "Hour", m), 
            e(a, t + "Minute", s), a;
        },
        getTrueValue: function(e) {
            if (e) {
                for (;isNaN(parseInt(e, 10)); ) e = e.slice(1);
                return parseInt(e, 10);
            }
        },
        getMonthEndDay: function(e, t) {
            return 32 - new Date(e, t - 1, 32).getDate();
        },
        onCancel: function() {
            this.$emit("cancel");
        },
        onConfirm: function() {
            this.$emit("confirm", this.data.innerValue);
        },
        onChange: function(e) {
            var t, n = this, a = this.data, u = e.detail.value.map(function(e, t) {
                return a.columns[t][e];
            });
            if ("time" === a.type) t = u.join(":"); else {
                var i = this.getTrueValue(u[0]), r = this.getTrueValue(u[1]), o = this.getMonthEndDay(i, r), l = this.getTrueValue(u[2]);
                "year-month" === a.type && (l = 1), l = l > o ? o : l;
                var m = 0, s = 0;
                "datetime" === a.type && (m = this.getTrueValue(u[3]), s = this.getTrueValue(u[4])), 
                t = new Date(i, r - 1, l, m, s);
            }
            t = this.correctValue(t), this.setData({
                innerValue: t
            }, function() {
                n.updateColumnValue(t), n.$emit("input", t), n.$emit("change", n);
            });
        },
        getColumnValue: function(e) {
            return this.getValues()[e];
        },
        setColumnValue: function(e, t) {
            var n = this.data, a = n.pickerValue, u = n.columns;
            a[e] = u[e].indexOf(t), this.setData({
                pickerValue: a
            });
        },
        getColumnValues: function(e) {
            return this.data.columns[e];
        },
        setColumnValues: function(e, t) {
            var n = this.data.columns;
            n[e] = t, this.setData({
                columns: n
            });
        },
        getValues: function() {
            var e = this.data, t = e.pickerValue, n = e.columns;
            return t.map(function(e, t) {
                return n[t][e];
            });
        },
        setValues: function(e) {
            var t = this.data.columns;
            this.setData({
                pickerValue: e.map(function(e, n) {
                    return t[n].indexOf(e);
                })
            });
        },
        updateColumnValue: function(e) {
            var t = [], n = this.pad, a = this.data, u = a.columns;
            if ("time" === a.type) {
                var i = e.split(":");
                t = [ u[0].indexOf(i[0]), u[1].indexOf(i[1]) ];
            } else {
                var r = new Date(e);
                t = [ u[0].indexOf("" + r.getFullYear()), u[1].indexOf(n(r.getMonth() + 1)) ], "date" === a.type && t.push(u[2].indexOf(n(r.getDate()))), 
                "datetime" === a.type && t.push(u[2].indexOf(n(r.getDate())), u[3].indexOf(n(r.getHours())), u[4].indexOf(n(r.getMinutes())));
            }
            this.setData({
                pickerValue: t
            });
        }
    },
    created: function() {
        var e = this, t = this.correctValue(this.data.value);
        this.setData({
            innerValue: t
        }, function() {
            e.updateColumnValue(t), e.$emit("input", t);
        });
    }
});