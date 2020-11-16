Component({
    options: {
        addGlobalClass: !0
    },
    properties: {
        tips: {
            type: Object
        }
    },
    data: {
        noticeShow: !0
    },
    methods: {
        onClose: function() {
            this.setData({
                noticeShow: !1
            });
        }
    }
});