var t = getApp();

Component({
    options: {
        addGlobalClass: !0
    },
    properties: {
        informations: {
            type: Array,
            value: []
        },
        from: {
            type: String,
            value: ""
        }
    },
    data: {},
    ready: function() {},
    lifetimes: {
        created: function() {},
        attached: function() {},
        ready: function() {}
    },
    methods: {
        onJsEvent: function(a) {
            t.util.jsEvent(a);
        },
        onImgPreview: function(t) {
            var a = t.currentTarget.dataset.current, e = t.currentTarget.dataset.urls;
            wx.previewImage({
                current: a,
                urls: e
            });
        },
        onToggleTextHeight: function(t) {
            var a = this, e = t.currentTarget.dataset.index;
            a.data.informations[e].showall = !a.data.informations[e].showall, a.setData({
                informations: a.data.informations
            });
        }
    }
});