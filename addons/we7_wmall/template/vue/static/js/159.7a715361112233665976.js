webpackJsonp([159],{Ez0X:function(t,a,s){"use strict";Object.defineProperty(a,"__esModule",{value:!0});var e={data:function(){return{paytype:"alipay",card:{},cards:[],num:0,showPreLoading:!0}},components:{PublicHeader:s("Cz8s").a},methods:{onLoad:function(){var t=this;this.util.request({url:"deliveryCard/apply/index"}).then(function(a){t.showPreLoading=!1;var s=a.data.message;if(s.errno)return t.util.$toast(s.message),!1;s=s.message,t.cards=s,t.card=s[0]})},onChooseCard:function(t){this.card=this.cards[t],this.num=t},onSubmit:function(){var t=this;this.util.request({url:"deliveryCard/apply/pay1",data:{setmeal_id:this.card.id}}).then(function(a){var s=a.data.message;if(s.errno)return t.$toast(s.message),!1;var e=s.message;t.$router.replace(t.util.getUrl({path:"/pages/public/pay?order_id="+e+"&order_type=deliveryCard"}))})}},mounted:function(){this.onLoad()}},i={render:function(){var t=this,a=t.$createElement,s=t._self._c||a;return s("div",{attrs:{id:"deliveryCard-apply"}},[s("public-header",{attrs:{title:"购买配送会员卡"}}),t._v(" "),s("div",{staticClass:"content"},[s("div",{staticClass:"block-title van-hairline--bottom"},[t._v("会员选择")]),t._v(" "),s("div",{staticClass:"row setmeal-list"},t._l(t.cards,function(a,e){return s("div",{staticClass:"col-33 setmeal-item ",class:{active:t.num==e},on:{click:function(a){return t.onChooseCard(e)}}},[s("span",{staticClass:"money"},[t._v(t._s(a.price)+t._s(t.Lang.dollarSignCn))]),t._v(" "),s("span",{staticClass:"name"},[t._v(t._s(a.title))])])}),0),t._v(" "),s("div",{staticClass:"block-info"},[s("div",{staticClass:"van-hairline--top"},[t._v("当前选择会员有效期为"),s("span",[t._v(t._s(t.card.starttime))]),t._v("至"),s("span",{attrs:{id:"setmeal-endtime"}},[t._v(t._s(t.card.endtime))])])]),t._v(" "),s("div",{staticClass:"list-block"},[s("van-cell-group",[s("van-cell",[s("template",{slot:"title"},[s("span",{staticClass:"item-icon"}),t._v(" "),s("span",{staticClass:"item-text"},[t._v("仅支持平台配送商户使用")])])],2),t._v(" "),s("van-cell",[s("template",{slot:"title"},[s("span",{staticClass:"item-icon"}),t._v(" "),s("span",{staticClass:"item-text"},[t._v("下单配送费直接扣除")])])],2),t._v(" "),s("van-cell",[s("template",{slot:"title"},[s("span",{staticClass:"item-icon"}),t._v(" "),t.card.day_free_limit>0?s("span",{staticClass:"item-text"},[t._v("每日仅限"+t._s(t.card.day_free_limit)+"单享受特权")]):s("span",{staticClass:"item-text"},[t._v("每日不限次享受特权")])])],2),t._v(" "),s("van-cell",[s("template",{slot:"title"},[s("span",{staticClass:"item-icon"}),t._v(" "),t.card.delivery_fee_free_limit>0?s("span",{staticClass:"item-text"},[t._v("每单最高减免配送费"+t._s(t.card.delivery_fee_free_limit)+t._s(t.Lang.dollarSignCn))]):s("span",{staticClass:"item-text"},[t._v("每单配送费全免")])])],2)],1)],1),t._v(" "),s("div",{staticClass:"list-block"},[s("van-cell-group",[s("van-cell",{attrs:{title:"使用兑换码兑换配送会员卡",to:"/package/pages/deliveryCard/deliveryCardExchange"}},[s("div",{staticClass:"flex",attrs:{slot:"right-icon"},slot:"right-icon"},[s("van-icon",{attrs:{name:"arrow"}})],1)])],1)],1)]),t._v(" "),s("div",{staticClass:"cart"},[s("div",[t._v(t._s(t.Lang.dollarSign)+" "),s("span",{attrs:{id:"cart-money"}},[t._v(t._s(t.card.price))])]),t._v(" "),s("span",{attrs:{id:"cart-submit"},on:{click:t.onSubmit}},[t._v("去支付")])]),t._v(" "),t.showPreLoading?s("iloading"):t._e()],1)},staticRenderFns:[]};var l=s("VU/8")(e,i,!1,function(t){s("UPOf")},null,null);a.default=l.exports},UPOf:function(t,a){}});
//# sourceMappingURL=159.7a715361112233665976.js.map