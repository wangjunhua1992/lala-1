var t = getApp();

Component({
    options: {
        addGlobalClass: !0
    },
    properties: {
        endtime: {
            type: String,
            value: ""
        }
    },
    data: {},
    ready: function() {
        var t = this, e = require("../../static/js/utils/wxTimer.js");
        this.data.endtime = this.data.endtime.replace(/-/g, "/"), new e({
            endTime: this.data.endtime,
            name: "wxTimer1",
            issplit: 1
        }).start(t);
    },
    lifetimes: {
        created: function() {},
        attached: function() {},
        ready: function() {
            var t = this, e = require("../../static/js/utils/wxTimer.js");
            this.data.endtime = this.data.endtime.replace(/-/g, "/"), new e({
                endTime: this.data.endtime,
                name: "wxTimer1",
                issplit: 1
            }).start(t);
        }
    },
    methods: {
        onJsEvent: function(e) {
            t.util.jsEvent(e);
        }
    }
});