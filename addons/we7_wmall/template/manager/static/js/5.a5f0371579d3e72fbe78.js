webpackJsonp([5],{"52iP":function(n,t,a){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var i=a("Cz8s"),o=a("deIj"),s=a("XbGN"),c={components:{publicHeader:i.a},data:function(){return{}},methods:{onaliPayPlus2:function(){s.a.aliPayPlus2()},onWxPay:function(){Object(o.c)({vue:this,url:"",data:{},success:function(n){}})},onWxPay2:function(){s.a.wxPay2()},onWxLogin:function(){s.a.wxLogin()},onLoad:function(){}},mounted:function(){this.onLoad()}},e={render:function(){var n=this,t=n.$createElement,a=n._self._c||t;return a("div",{attrs:{id:"auth-login"}},[a("public-header",{attrs:{title:"APICloud微信模块测试"}}),n._v(" "),a("div",{staticClass:"content"},[a("div",{staticClass:"padding-15"},[a("van-button",{staticClass:"bg-primary margin-10-t",attrs:{size:"normal",block:""},on:{click:n.onWxLogin}},[n._v("微信登录")]),n._v(" "),a("van-button",{staticClass:"bg-danger margin-10-t margin-15-t",attrs:{size:"normal",block:""},on:{click:n.onWxPay2}},[n._v("微信支付方案二")]),n._v(" "),a("van-button",{staticClass:"bg-info margin-10-t margin-15-t",attrs:{size:"normal",block:""},on:{click:n.onWxPay}},[n._v("微信支付方案一")]),n._v(" "),a("van-button",{staticClass:"bg-danger margin-10-t margin-15-t",attrs:{size:"normal",block:""},on:{click:n.onaliPayPlus2}},[n._v("支付宝支付方案二")])],1)])],1)},staticRenderFns:[]};var l=a("VU/8")(c,e,!1,function(n){a("VdyP")},null,null);t.default=l.exports},VdyP:function(n,t){}});
//# sourceMappingURL=5.a5f0371579d3e72fbe78.js.map