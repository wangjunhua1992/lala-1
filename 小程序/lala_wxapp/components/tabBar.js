var e = getApp();

Component({
    options: {
        addGlobalClass: !0
    },
    properties: {
        menufooter: {
            type: Object,
            value: {
                type: 0,
                currentPageLink: "pages/home/index",
                params: {},
                css: {},
                data: {}
            }
        }
    },
    data: {},
    methods: {
        onJsEvent: function(t) {
            e.util.jsEvent(t);
        }
    }
});