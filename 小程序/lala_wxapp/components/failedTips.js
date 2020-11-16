var t = getApp();

Component({
    properties: {
        failedTips: {
            type: Object,
            value: {
                img: "http://cos.lalawaimai.com/we7_wmall/wxapp/store_no_con.png",
                tips: "您所在的区域暂未开启跑腿功能,建议您手动搜索地址或切换到此前常用的地址再试试",
                btnText: "手动搜索地址",
                link: "pages/home/location?from=paotui"
            }
        }
    },
    data: {},
    methods: {
        onJsEvent: function(e) {
            t.util.jsEvent(e);
        }
    }
});