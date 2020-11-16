var a = getApp();

Page({
    data: {},
    onLoad: function(e) {
        var t = "/tangshi/pages/table/goods";
        e && (e.scene && (t += "?scene=" + e.scene), e.cart_id && (t += "&cart_id=" + e.cart_id)), 
        a.util.jump2url(t);
    }
});