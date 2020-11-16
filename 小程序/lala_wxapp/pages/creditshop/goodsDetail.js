var a = getApp();

Page({
    data: {
        tab: "detail",
        loaded: 0,
        empty: 0,
        records: {
            id: "",
            page: 2,
            psize: 15,
            data: []
        },
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(e) {
        var t = this;
        t.data.goods_id = e.id, a.util.request({
            url: "creditshop/index/detail",
            data: {
                id: e.id
            },
            success: function(e) {
                a.util.loaded();
                var d = e.data.message.message;
                d.records.length > 0 && t.setData({
                    empty: 1
                }), a.WxParse.wxParse("description", "html", d.good.description, t, 0), t.setData({
                    result: d,
                    allrecords: d.records
                }), t.data.records.data = d.records;
            }
        });
    },
    onReachBottom: function() {},
    onGetRecords: function() {
        var e = this;
        a.util.request({
            url: "creditshop/index/detail",
            data: {
                page: e.data.records.page,
                psize: e.data.records.psize,
                id: e.data.goods_id
            },
            success: function(a) {
                var t = a.data.message.message, d = e.data.records.data.concat(t.records);
                t.records.length < e.data.records.psize && e.setData({
                    loaded: 1
                }), e.data.records.page++, e.data.records.data = d, e.setData({
                    allrecords: d
                }), console.log(allrecords);
            }
        });
    },
    onChangeTab: function(a) {
        var e = this, t = a.currentTarget.dataset.type, d = e.data.records;
        d = t, e.setData({
            tab: d
        });
    }
});