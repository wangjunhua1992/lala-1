var a = getApp();

Page({
    data: {
        sid: 0,
        extra: {
            dayIndex: 0,
            day_cn: "今天",
            day: "",
            SearchCategoryItem: "全部桌型",
            SearchTime: "全部时间"
        },
        categorys_new: {},
        reserves_new: {},
        SearchCategoryId: "-1",
        searchtype: "tables",
        showPopupSearch: !1,
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(e) {
        var t = this;
        e && e.sid && (t.data.sid = e.sid), a.util.request({
            url: "wmall/store/reserve/index",
            data: {
                sid: t.data.sid
            },
            success: function(e) {
                a.util.loaded();
                var r = e.data.message;
                if (r.errno) return a.util.toast(r.message), !1;
                r = r.message, t.data.categorys_new = r.categorys, t.data.reserves_new = r.reserves, 
                t.setData(r), t.data.extra.day = r.year + "-" + r.days[0].day, t.setData({
                    "extra.day": t.data.extra.day,
                    "extra.SearchCategoryItem": t.data.extra.SearchCategoryItem,
                    categorys_new: t.data.categorys_new,
                    reserves_new: t.data.reserves_new
                });
            }
        });
    },
    onSelectDay: function(a) {
        var e = this, t = a.currentTarget.dataset.day, r = a.currentTarget.dataset.index;
        e.setData({
            "extra.day": e.data.year + "-" + t,
            "extra.dayIndex": r
        });
    },
    onSelectTime: function(e) {
        var t = this, r = e.currentTarget.dataset.time, s = e.currentTarget.dataset.cid, d = e.currentTarget.dataset.total_num;
        "-1" != r ? t.data.tables_info[t.data.extra.day] && t.data.tables_info[t.data.extra.day][s] && d <= t.data.tables_info[t.data.extra.day][s][r] ? a.util.toast("该桌台类型下没有可以预订的桌位") : (t.setData({
            "extra.time": r,
            "extra.cid": s
        }), t.data.extra.day || (t.data.extra.day = t.data.year + "-" + t.data.days[t.data.extra.dayIndex].day), 
        a.util.setStorageSync("reserve.extra", t.data.extra), wx.navigateTo({
            url: "./create?sid=" + t.data.sid
        })) : a.util.toast("该时间不能预定点餐");
    },
    onChangeSearch: function(a) {
        var e = a.currentTarget.dataset.searchtype;
        this.setData({
            searchtype: e,
            showPopupSearch: !this.data.showPopupSearch
        });
    },
    onMultiple: function() {
        this.setData({
            showPopupSearch: !this.data.showPopupSearch
        });
    },
    onChangeCategory: function(a) {
        var e = a.currentTarget.dataset.id, t = a.currentTarget.dataset.index, r = this;
        "-1" == t ? (r.data.reserves_new = {}, r.data.reserves_new = r.data.reserves, r.data.categorys_new = r.data.categorys, 
        r.data.extra.SearchCategoryItem = "全部桌型") : (r.data.reserves_new = {}, r.data.reserves_new[e] = r.data.reserves[e], 
        r.data.categorys_new = [], r.data.categorys_new.push(r.data.categorys[t]), r.data.extra.SearchCategoryItem = r.data.categorys[t].title), 
        r.setData({
            SearchCategoryId: e,
            categorys_new: r.data.categorys_new,
            reserves_new: r.data.reserves_new,
            "extra.SearchCategoryItem": r.data.extra.SearchCategoryItem,
            "extra.SearchTime": "全部时间",
            showPopupSearch: !this.data.showPopupSearch
        });
    },
    onChangeSearchType: function(a) {
        var e = a.currentTarget.dataset.searchtype;
        this.setData({
            searchtype: e
        });
    },
    onChangeTimes: function(a) {
        var e = a.currentTarget.dataset.index, t = a.currentTarget.dataset.time;
        if ("-1" == e) this.data.reserves_new = this.data.reserves; else {
            if ("-1" == this.data.SearchCategoryId) r = this.data.categorys[0].id; else var r = this.data.SearchCategoryId;
            var s = this.data.reserves[r][e];
            this.data.reserves_new = {}, this.data.reserves_new[r] = [], this.data.reserves_new[r].push(s);
        }
        this.setData({
            reserves_new: this.data.reserves_new,
            "extra.SearchTime": t,
            showPopupSearch: !this.data.showPopupSearch
        });
    }
});