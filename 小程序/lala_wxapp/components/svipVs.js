var t = getApp();

Component({
    options: {
        addGlobalClass: !0
    },
    properties: {
        goods: {
            type: Object,
            value: {
                price: 0,
                svip_price: 0
            }
        }
    },
    data: {
        Lang: t.Lang
    },
    methods: {
        onJsEvent: function(e) {
            t.util.jsEvent(e);
        },
        onParentPlus: function(t) {
            var e = t.currentTarget.dataset.buysvip_status;
            this.triggerEvent("onParentPlus", {
                from: "selectSvip",
                buysvip_status: e
            });
        }
    }
});