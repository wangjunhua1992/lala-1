Page({
    data: {},
    onLoad: function(r) {
        r.url = r.url.replace(/\_a\_/g, "?"), r.url = r.url.replace(/\_b\_/g, "="), r.url = r.url.replace(/\_c\_/g, "&"), 
        this.setData({
            url: r.url
        });
    }
});