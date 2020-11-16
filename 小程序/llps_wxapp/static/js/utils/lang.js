var n = {
    "zh-cn": {
        dollarSign: "￥",
        dollarSignCn: "元"
    },
    europe: {
        dollarSign: "€",
        dollarSignCn: "欧元"
    },
    en: {
        dollarSign: "$",
        dollarSignCn: "美元"
    }
}, e = wx.getExtConfigSync();

e && e.siteInfo || (e = getApp().ext);

var l = e.siteInfo.lang || "zh-cn", g = wx.getStorageSync("LangType");

g && (l = g);

var o = n[l];

module.exports = o;