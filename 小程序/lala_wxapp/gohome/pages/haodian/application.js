var a = getApp();

Page({
    data: {
        store: {},
        logo: [],
        qualification: [],
        thumbs: [],
        meal: {
            selectIndex: 0,
            price: 0,
            days: 0
        },
        columns: [],
        failedTips: {
            show: !1,
            type: "message",
            tips: "",
            btnText: "关闭",
            link: ""
        },
        submitting: !1,
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function() {
        var t = this;
        a.util.request({
            url: "haodian/settle/store",
            success: function(e) {
                if (a.util.loaded(), -1e3 == (e = e.data.message).errno) a.util.toast(e.message, "/gohome/pages/haodian/settle", 1e3); else {
                    if (-1005 == e.errno || 0 == e.errno || -1004 == e.errno) return t.data.failedTips = {
                        show: !0,
                        type: "info",
                        tips: e.message.message,
                        link: "/gohome/pages/haodian/index",
                        btnText: "确定"
                    }, void t.setData({
                        failedTips: t.data.failedTips
                    });
                    if (-1 == e.errno) return t.data.failedTips = {
                        show: !0,
                        type: "error",
                        tips: e.message,
                        link: "/gohome/pages/haodian/index",
                        btnText: "返回"
                    }, void t.setData({
                        failedTips: t.data.failedTips
                    });
                    if (-1006 == e.errno) return t.data.failedTips = {
                        show: !0,
                        type: "info",
                        tips: e.message.message,
                        link: "/pages/public/pay?order_type=haodian&order_id=" + e.message.order_id,
                        btnText: "前去支付"
                    }, void t.setData({
                        failedTips: t.data.failedTips
                    });
                    var i = e.message.config.meal;
                    i && i.length > 0 && (t.data.meal.price = i[0].price, t.data.meal.days = i[0].time);
                    var s = e.message.categorys;
                    t.data.columns[0] = s, s[0].child && (t.data.columns[1] = s[0].child), t.setData({
                        failedTips: t.data.failedTips,
                        columns: t.data.columns,
                        config: e.message.config,
                        meal: t.data.meal
                    });
                }
            }
        });
    },
    onInput: function(a) {
        var t = a.target.dataset.type;
        this.data.store[t] = a.detail;
    },
    onSelectCategory: function(a) {
        var t = a.detail.value[0], e = a.detail.value[1], i = this.data.columns;
        this.data.store.haodian_cid = i[0][t].id, this.data.store.haodian_child_id = 0;
        var s = i[0][t].title;
        i[1].length > 0 && (this.data.store.haodian_child_id = i[1][e].id, s = s + "-" + i[1][e].title), 
        this.setData({
            "store.category_title": s
        });
    },
    onChangeColumn: function(a) {
        if (0 == a.detail.column) {
            var t = a.detail.value, e = this.data.columns;
            e[0][t].child ? e[1] = e[0][t].child : e[1] = [], this.setData({
                columns: e
            });
        }
    },
    onSelectMeal: function(a) {
        var t = a.currentTarget.dataset.index;
        this.data.meal.price = this.data.config.meal[t].price, this.data.meal.days = this.data.config.meal[t].time, 
        this.data.meal.selectIndex = t, this.setData({
            meal: this.data.meal
        });
    },
    onSubmit: function(t) {
        var e = this;
        return !e.data.submitting && (e.data.store.telephone ? a.util.isMobile(e.data.store.telephone) ? e.data.store.address ? e.data.store.title ? e.data.store.haodian_cid ? 1 != e.data.config.qualification_verify_status || e.data.qualification[0] && e.data.qualification[0].filename ? (e.data.submitting = !0, 
        e.data.store.logo = e.data.logo[0] ? e.data.logo[0].filename : "", e.data.store.meal = e.data.meal, 
        e.data.store.thumbs = e.data.thumbs, e.data.store.qualification = e.data.qualification, 
        void a.util.request({
            url: "haodian/settle/store",
            data: {
                store: JSON.stringify(e.data.store)
            },
            method: "POST",
            success: function(t) {
                if (-1 == (t = t.data.message).errno) return e.data.submitting = !1, void a.util.toast(t.message);
                -1006 != t.errno ? e.onLoad() : a.util.toast("请支付", "/pages/public/pay?order_type=haodian&order_id=" + t.message, 1e3);
            }
        })) : (a.util.toast("请上传营业执照照片"), !1) : (a.util.toast("请选择商户分类"), !1) : (a.util.toast("商户名称不能为空"), 
        !1) : (a.util.toast("商户地址不能为空"), !1) : (a.util.toast("手机号格式错误"), !1) : (a.util.toast("手机号不能为空"), 
        !1));
    },
    onJsEvent: function(t) {
        a.util.jsEvent(t);
    }
});