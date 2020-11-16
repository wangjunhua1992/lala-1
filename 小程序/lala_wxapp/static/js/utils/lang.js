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
    },
    HKD: {
        dollarSign: "HKD",
        dollarSignCn: "港元"
    },
    RUB: {
        dollarSign: "₽",
        dollarSignCn: "卢布"
    }
}, l = wx.getExtConfigSync();

l && l.siteInfo || (l = getApp().ext);

var g = l.siteInfo.lang || "zh-cn", o = wx.getStorageSync("LangType");

o && (g = o);

var r = n[g];

module.exports = r;