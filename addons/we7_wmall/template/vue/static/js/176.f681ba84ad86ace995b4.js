webpackJsonp([176],{AxGc:function(t,s){},pZWh:function(t,s,a){"use strict";Object.defineProperty(s,"__esModule",{value:!0});var e=a("Gu7T"),i=a.n(e),n=a("Cz8s"),l=a("q0vl"),c={data:function(){return{preLoading:!0,canBuy:!0,mealRedpacketId:0,params:{},style:{},redpackets:[],usefulNum:0,exchanges:{page:2,psize:15,loading:!1,finished:!1,data:[]},islegal:!1,popupShow:!1,selectedRedpacketId:0,selectedSid:0,agreementShow:!1,agreementContent:""}},components:{PublicHeader:n.a,agreement:l.a},methods:{onLoad:function(){var t=this;this.util.request({url:"mealRedpacket/meal/index"}).then(function(s){t.preLoading=!1;var a=s.data.message;if(a.errno)t.util.$toast(a.message);else{if(0==a.message.mealRedpacket)return t.util.$toast("暂无套餐红包活动"),!1;t.canBuy=a.message.canBuy,t.usefulNum=a.message.useful_num,t.redpackets=[].concat(i()(t.redpackets),i()(a.message.redpackets)),t.exchanges.data=[].concat(i()(t.exchanges),i()(a.message.exchanges)),a.message.exchanges.length<t.exchanges.psize&&(t.exchanges.finished=!0);var e=a.message.mealRedpacket;t.agreementContent=e.data.rules,t.mealRedpacketId=e.id,t.params=e.data.params,t.style=e.data.style,t.islegal=!0}})},onGetExchanges:function(){var t=this;if(this.exchanges.finished)return!1;this.util.request({url:"mealRedpacket/meal/exchange",data:{page:this.exchanges.page,psize:this.exchanges.psize,mealRedpacket_id:this.mealRedpacketId}}).then(function(s){var a=s.data.message;if(a.errno)t.util.$toast(a.message);else{t.exchanges.loading=!1;var e=a.message.exchanges;t.exchanges.data=[].concat(i()(t.exchanges.data),i()(e)),e.length<t.exchanges.psize&&(t.exchanges.finished=!0),t.exchanges.page++}})},onSubmit:function(){var t=this;if(!this.islegal)return!1;if(this.islegal=!1,!this.canBuy)return this.uitl.$toast("本月购买次数已用完"),!1;var s={mealRedpacket_id:parseInt(this.mealRedpacketId),final_fee:parseFloat(this.params.price)};this.util.request({url:"mealRedpacket/meal/submit",data:s}).then(function(s){var a=s.data.message;if(a.errno)t.util.$toast(a.message);else{var e=a.message;t.$router.replace(t.util.getUrl({path:"/pages/public/pay?order_id="+e+"&order_type=mealRedpacket"}))}})},onExchangePopupShow:function(t){return 1==this.params.exchangeStatus&&(this.canBuy?(this.util.$toast("请购买套餐红包后再进行兑换操作"),!1):(this.selectedSid=t,void this.onTogglePopup()))},onTogglePopup:function(){this.popupShow=!this.popupShow},onToggleAgreement:function(){this.agreementShow=!this.agreementShow},onSelectRedpacket:function(t){return"paotui"==t.scene?(this.util.$toast("无法使用跑腿红包进行升级"),!1):1!=t.status?(this.util.$toast("请选择有效的红包进行升级"),!1):t.sid>0?(this.util.$toast("无法使用已兑换的红包进行升级"),!1):void(this.selectedRedpacketId=t.id)},onExchange:function(){var t=this,s={redpacket_id:parseInt(this.selectedRedpacketId),sid:parseInt(this.selectedSid),mealRedpacket_id:parseInt(this.mealRedpacketId)};this.util.request({url:"mealRedpacket/meal/do_exchange",data:s}).then(function(s){var a=s.data.message;t.util.$toast(a.message),a.errno||window.location.reload()})}},mounted:function(){this.onLoad()}},o={render:function(){var t=this,s=t.$createElement,a=t._self._c||s;return a("div",{attrs:{id:"mealRedpacket-plus"}},[t.isWeixin?t._e():a("public-header",{attrs:{title:"红包套餐"}}),t._v(" "),t.mealRedpacketId>0?a("div",{staticClass:"content"},[a("div",{staticClass:"info"},[a("div",{staticClass:"city-records flex-lr"},[a("div",{staticClass:"city flex"},[t._e()],2),t._v(" "),a("router-link",{staticClass:"records flex",attrs:{tag:"div",to:t.util.getUrl({path:"/package/pages/mealRedpacket/ordermeal"})}},[a("img",{attrs:{src:"static/img/meal/meal_time.png",alt:""}}),t._v(" "),a("span",[t._v("购买记录")])])],1),t._v(" "),a("div",{staticClass:"meal-info",style:{background:"url("+t.params.backgroundImage+") no-repeat top left/100% 100%"}},[a("div",{staticClass:"tip flex",style:{color:t.style.rulesColor}},[a("span",{staticClass:"icon icon-question1",on:{click:t.onToggleAgreement}}),t._v(" "),a("span",{on:{click:t.onToggleAgreement}},[t._v("特权说明")])]),t._v(" "),a("div",{staticClass:"title",style:{color:t.style.titleColor}},[t._v(t._s(t.params.title))]),t._v(" "),a("div",{staticClass:"contain",style:{color:t.style.placeholderColor}},[t._v(t._s(t.params.placeholder))]),t._v(" "),t.canBuy?a("div",{staticClass:"btn-buy haved ",class:{disabled:!t.islegal},style:{color:t.style.btnColor,background:t.style.btnBackground},on:{click:function(s){return t.onSubmit()}}},[a("span",{staticClass:"renminbi font-12"},[t._v(t._s(t.Lang.dollarSign))]),t._v(" "),a("span",{staticClass:"price font-18 padding-10-r"},[t._v(t._s(t.params.price))]),t._v(" "),a("span",[t._v(t._s(t.params.btnText))])]):a("div",{staticClass:"btn-buy"},[t._v("本月购买次数已用完")])]),t._v(" "),t.params.tips?a("div",{staticClass:"use-limit flex-lr"},t._l(t.params.tips,function(s,e){return a("div",{key:e,staticClass:"limit-item"},[a("div",{staticClass:"img"},[a("img",{attrs:{src:s.imgurl,alt:""}})]),t._v(" "),a("span",{staticClass:"text ellipsis",style:{color:s.color}},[t._v(t._s(s.text))])])}),0):t._e()]),t._v(" "),a("div",{staticClass:"detail"},[t.redpackets&&t.redpackets.length>0?[t.canBuy?t._e():a("div",{staticClass:"title flex-lr padding-10-lr padding-10-t"},[t._m(0),t._v(" "),a("div",{staticClass:"flex"},[a("span",{staticClass:"margin-5-r"},[t._v("剩余")]),t._v(" "),a("span",{staticClass:"font-bold font-20 c-default"},[t._v(t._s(t.usefulNum))]),t._v(" "),a("span",[t._v("/"+t._s(t.redpackets.length)+"张")])])]),t._v(" "),a("div",{staticClass:"redpacket-list"},[a("van-row",{attrs:{gutter:"10"}},t._l(t.redpackets,function(s,e){return a("van-col",{key:e,staticClass:"margin-10-b",attrs:{span:"12"}},[a("div",{staticClass:"redpacket-item",class:{used:s.status>1}},[a("div",{staticClass:"top"},[a("div",{staticClass:"flex-lr"},[a("div",{staticClass:"name"},[t._v(t._s(s.name))]),t._v(" "),a("div",{staticClass:"price color-main"},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t"+t._s(t.Lang.dollarSign)),a("span",{staticClass:"font-20"},[t._v(t._s(s.discount))])])]),t._v(" "),a("div",{staticClass:"flex-lr margin-10-t"},[a("div",{staticClass:"useday-limit"},[t._v("有效期: "+t._s(s.use_days_limit)+"天")]),t._v(" "),s.condition>0?a("p",{staticClass:"color-main"},[t._v("满"+t._s(s.condition)+t._s(t.Lang.dollarSignCn))]):a("p",{staticClass:"color-main"},[t._v("无门槛")])])]),t._v(" "),a("div",{staticClass:"divide"},[a("div",{staticClass:"divide-line"})]),t._v(" "),t.canBuy?a("div",{staticClass:"bottom"},[t._v("购买后即可享受优惠")]):a("div",{staticClass:"bottom"},[t._v(t._s(s.endtime_cn))])])])}),1)],1)]:t._e(),t._v(" "),1==t.params.exchangeStatus&&t.exchanges.data.length>0?[t._m(1),t._v(" "),a("div",{staticClass:"exchange-list padding-10 hide"},[a("van-list",{attrs:{finished:t.exchanges.finished,offset:100,"immediate-check":!1},on:{load:t.onGetExchanges},model:{value:t.exchanges.loading,callback:function(s){t.$set(t.exchanges,"loading",s)},expression:"exchanges.loading"}},t._l(t.exchanges.data,function(s,e){return a("div",{key:e,staticClass:"exchange-item"},[a("div",{staticClass:"exchange-item-wrap "},[a("div",{staticClass:"store"},[a("div",{staticClass:"store-name ellipsis"},[t._v(t._s(s.title))]),t._v(" "),a("div",{staticClass:"flex margin-10-t"},[a("van-rate",{attrs:{size:12,"disabled-color":"#ff2d4b",disabled:""},model:{value:s.score,callback:function(a){t.$set(s,"score",a)},expression:"item.score"}}),t._v(" "),a("p",{staticClass:"star"},[t._v(t._s(s.score))])],1),t._v(" "),s.activity?a("div",{staticClass:"activity flex margin-10-t"},[a("img",{attrs:{src:"static/img/icon-discount.png",alt:""}}),t._v(" "),a("p",{staticClass:"ellipsis"},[t._v(t._s(s.activity))])]):t._e()]),t._v(" "),a("div",{staticClass:"price"},[t._v("\n\t\t\t\t\t\t\t\t\t"+t._s(t.Lang.dollarSign)),a("span",[t._v(t._s(s.discount))]),t._v(" "),s.condition>0?a("p",{staticClass:"ellipsis"},[t._v("满"+t._s(s.condition)+t._s(t.Lang.dollarSignCn))]):a("p",{staticClass:"ellipsis"},[t._v("无门槛")])])]),t._v(" "),a("div",{staticClass:"exchange",on:{click:function(a){return t.onExchangePopupShow(s.store_id)}}},[a("span",[t._v("立")]),a("span",[t._v("即")]),a("span",[t._v("兑")]),a("span",[t._v("换")])])])}),0)],1),t._v(" "),a("van-list",{staticClass:"new-exchange-list padding-10 font-14",attrs:{finished:t.exchanges.finished,offset:100,"immediate-check":!1},on:{load:t.onGetExchanges},model:{value:t.exchanges.loading,callback:function(s){t.$set(t.exchanges,"loading",s)},expression:"exchanges.loading"}},[a("van-row",{attrs:{gutter:"10"}},t._l(t.exchanges.data,function(s,e){return a("van-col",{key:e,staticClass:"margin-10-b ",attrs:{span:"12"}},[a("div",{staticClass:"new-exchange-item padding-15-tb"},[a("div",{staticClass:"redpacket-info"},[a("div",{staticClass:"flex-center"},[a("span",{staticClass:"c-danger"},[t._v(t._s(t.Lang.dollarSign))]),t._v(" "),a("span",{staticClass:"c-danger font-24 font-bold margin-5-r"},[t._v(t._s(s.discount))]),t._v(" "),s.condition>0?a("span",{staticClass:"c-gray font-12"},[t._v("满"+t._s(s.condition)+t._s(t.Lang.dollarSignCn))]):a("span",{staticClass:"c-gray font-12"},[t._v("无门槛")])]),t._v(" "),a("div",{staticClass:"upgrade",on:{click:function(a){return t.onExchangePopupShow(s.store_id)}}},[t._v("升级")]),t._v(" "),a("div",{staticClass:"c-gray font-12"},[t._v("需1张会员红包升级")])]),t._v(" "),a("div",{staticClass:"divide"},[a("div",{staticClass:"divide-line"})]),t._v(" "),a("router-link",{staticClass:"store-info",attrs:{tag:"div",to:t.util.getUrl({path:"/pages/store/goods",query:{sid:s.sid}})}},[a("div",{staticClass:"store-logo"},[a("img",{attrs:{src:s.logo,alt:""}})]),t._v(" "),a("div",{staticClass:"store-title w-100 ellipsis text-center margin-10-t padding-10-lr"},[t._v(t._s(s.title))])])],1)])}),1)],1)]:t._e()],2)]):t._e(),t._v(" "),a("van-popup",{staticClass:"popup-exchange",attrs:{position:"bottom"},model:{value:t.popupShow,callback:function(s){t.popupShow=s},expression:"popupShow"}},[a("div",{staticClass:"popup-title van-hairline--bottom"},[t._v("请选择一个可用红包进行兑换")]),t._v(" "),a("div",{staticClass:"popup-container"},[a("div",{staticClass:"redpacket-list"},[a("van-row",{attrs:{gutter:"10"}},t._l(t.redpackets,function(s,e){return a("van-col",{key:e,staticClass:"margin-10-b",attrs:{span:"12"}},[a("div",{staticClass:"redpacket-item",class:{active:s.id==t.selectedRedpacketId,used:s.status>1||s.sid>0||"paotui"==s.scene},on:{click:function(a){return t.onSelectRedpacket(s)}}},[a("div",{staticClass:"top"},[a("div",{staticClass:"flex-lr"},[a("div",{staticClass:"name"},[t._v(t._s(s.name))]),t._v(" "),a("div",{staticClass:"price color-main"},[t._v("\n\t\t\t\t\t\t\t\t\t\t"+t._s(t.Lang.dollarSign)),a("span",{staticClass:"font-20"},[t._v(t._s(s.discount))])])]),t._v(" "),a("div",{staticClass:"flex-lr margin-10-tb"},[a("div",{staticClass:"useday-limit"},[t._v("有效期: "+t._s(s.use_days_limit)+"天")]),t._v(" "),s.condition>0?a("p",{staticClass:"color-main"},[t._v("满"+t._s(s.condition)+t._s(t.Lang.dollarSignCn))]):a("p",{staticClass:"color-main"},[t._v("无门槛")])])]),t._v(" "),a("div",{staticClass:"divide"},[a("div",{staticClass:"divide-line"})]),t._v(" "),t.canBuy?a("div",{staticClass:"bottom"},[t._v("购买后即可享受优惠")]):a("div",{staticClass:"bottom"},[t._v(t._s(s.endtime_cn))])])])}),1)],1)]),t._v(" "),a("div",{staticClass:"popup-confirm van-hairline--top text-center c-danger",on:{click:function(s){return t.onExchange()}}},[t._v("立即升级")])]),t._v(" "),a("agreement",{attrs:{show:t.agreementShow,title:"特权说明",content:t.agreementContent},on:{agreementHide:t.onToggleAgreement}}),t._v(" "),a("iloading",{directives:[{name:"show",rawName:"v-show",value:t.preLoading,expression:"preLoading"}]})],1)},staticRenderFns:[function(){var t=this.$createElement,s=this._self._c||t;return s("div",{staticClass:"avail-num"},[s("span",[this._v("会员红包")])])},function(){var t=this.$createElement,s=this._self._c||t;return s("div",{staticClass:"title padding-10-lr margin-5-t"},[s("span",{staticClass:"avail-num"},[s("span",[this._v("会员红包金额升级")])]),this._v(" "),s("span",{staticClass:"c-gray font-12 margin-5-l"},[this._v("以下商家支持红包金额升级")])])}]};var d=a("VU/8")(c,o,!1,function(t){a("AxGc")},null,null);s.default=d.exports}});
//# sourceMappingURL=176.f681ba84ad86ace995b4.js.map