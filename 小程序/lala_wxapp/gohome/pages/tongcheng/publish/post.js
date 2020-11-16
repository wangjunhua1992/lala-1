var t = getApp();

Page({
    data: {
        islegal: !1,
        stickShow: !1,
        publish: {
            id: 0,
            content: "",
            sid: 0,
            days: 0,
            nickname: "",
            mobile: "",
            keyword: []
        },
        calculate: {
            fiinal_fee: 0
        },
        thumbs: [],
        tagsSelect: [],
        Lang: t.Lang,
        wuiLoading: {
            show: !0,
            img: t.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(a) {
        var i = this;
        i.data.options = a, t.util.request({
            url: "tongcheng/publish/post",
            data: {
                parentid: i.data.options.parentid,
                childid: i.data.options.childid,
                publish: JSON.stringify(i.data.publish),
                information_id: i.data.options.information_id
            },
            success: function(a) {
                t.util.loaded();
                var e = a.data.message;
                if (e.errno) return t.util.toast(e.message), !1;
                if (e = e.message, i.data.publish.nickname = e.member.realname, i.data.publish.mobile = e.member.mobile, 
                i.data.options.information_id > 0) {
                    i.data.publish = e.publish, i.data.thumbs = i.data.publish.thumbs;
                    var s = i.data.publish.keyword;
                    if (s.length > 0) for (var o = 0, n = s.length; o < n; o++) {
                        var d = e.category.tags.indexOf(s[o]);
                        i.data.tagsSelect[d] = d;
                    }
                }
                i.setData({
                    category: e.category,
                    calculate: e.calculate,
                    publish: i.data.publish,
                    thumbs: i.data.thumbs,
                    tagsSelect: i.data.tagsSelect,
                    islegal: !0
                });
            }
        });
    },
    onToggleTags: function(t) {
        var a = t.target.dataset.tag, i = t.target.dataset.index, e = this.data.publish.keyword.indexOf(a);
        this.data.tagsSelect[i] == i ? (delete this.data.tagsSelect[i], this.data.publish.keyword.splice(e, 1)) : (this.data.tagsSelect[i] = i, 
        this.data.publish.keyword.push(a)), this.setData({
            "publish.keyword": this.data.publish.keyword,
            tagsSelect: this.data.tagsSelect
        });
    },
    onSelectStick: function(a) {
        if (this.data.calculate && 1 != this.data.calculate.stick_is_available) return t.util.toast("置顶位已售完,暂时不可购买", "", 1e3), 
        !1;
        var i = a.currentTarget.dataset.day;
        this.setData({
            "publish.days": i,
            stickShow: !this.data.stickShow
        }), this.onLoad(this.data.options);
    },
    onSubmit: function(a) {
        var i = this, e = a.detail.value;
        i.data.publish.content = e.content, i.data.publish.nickname = e.nickname, i.data.publish.mobile = e.mobile, 
        i.data.publish.thumbs = i.data.thumbs, i.data.publish.content ? t.util.request({
            url: "tongcheng/publish/post",
            method: "POST",
            data: {
                parentid: i.data.options.parentid,
                childid: i.data.options.childid,
                publish: JSON.stringify(i.data.publish),
                information_id: i.data.options.information_id
            },
            success: function(a) {
                var i = a.data.message;
                if (i.errno) return t.util.toast(i.message), !1;
                1 == (i = i.message).need_pay ? t.util.toast("下单成功", "/pages/public/pay?order_id=" + i.id + "&order_type=tongcheng", 1e3) : t.util.toast(i.message, "/gohome/pages/tongcheng/detail?id=" + i.information_id, 1e3);
            }
        }) : t.util.toast("请输入内容", "", 1e3);
    },
    onReachBottom: function() {},
    onPullDownRefresh: function() {},
    onJsEvent: function(a) {
        t.util.jsEvent(a);
    }
});