webpackJsonp([81],{ZVqN:function(t,s){},aUzT:function(t,s,i){"use strict";Object.defineProperty(s,"__esModule",{value:!0});var o=i("Gu7T"),n=i.n(o),a=i("Cz8s"),e=i("NPH5"),l={data:function(){return{store:{},tables:[],table_sn:"",note:"",status:{noDiscountShow:!1,couponShow:!1,tableShow:!1},payment:[],pay_type:"wechat",couponNum:0,coupons:[],couponId:0,condition:0,discount_fee:0,total:0,nodiscount:0,paybill_extra:"",showPreLoading:!0}},components:{PublicHeader:a.a,load:e.a},computed:{final:function(){var t=parseFloat(this.total-this.discount_fee);return(!t||t<0)&&(t=0),t},couponTitle:function(){var t="未使用";return this.couponId&&(t=this.discount_fee+this.Lang.dollarSignCn+"券"),t}},watch:{nodiscount:function(){this.onCalculate(),this.onGetCoupon()},discount_fee:function(){this.onCalculate()},total:function(){this.onGetCoupon(),this.onCalculate()}},methods:{onInput:function(t){var s=t.target.dataset.type;"total"==s?this.total=t.target.value:"nodiscount"==s&&(this.nodiscount=t.target.value)},onToggleStatus:function(t){this.status[t]=!this.status[t],"noDiscountShow"!=t||this.status.noDiscountShow||(this.nodiscount=0)},onLoad:function(){var t=this;this.util.request({url:"wmall/store/paybill/payment",data:{sid:this.sid}}).then(function(s){t.showPreLoading=!1;var i=s.data.message;if(i.errno)return t.util.$toast(i.message),!1;i=i.message,t.store=i.store,t.payment=i.payment,t.tables=[].concat(n()(t.tables),n()(i.tables)),t.paybill_extra=i.paybill_extra,t.util.setWXTitle(t.store.title)})},onCalculate:function(){this.total=parseFloat(this.total),this.discount_fee=parseFloat(this.discount_fee),this.nodiscount=parseFloat(this.nodiscount),this.canDiscount=this.total-this.nodiscount,this.couponId>0&&this.canDiscount<this.condition&&(this.couponId=0,this.condition=0,this.discount_fee=0),this.nodiscount>0&&this.nodiscount>this.total&&this.$toast("超出消费总额,重新输入")},onGetCoupon:function(){var t=this;this.canDiscount=this.total-this.nodiscount,this.util.request({url:"wmall/store/paybill/coupon",data:{sid:this.sid,sum:this.canDiscount}}).then(function(s){var i=s.data.message;t.couponNum=i.num,t.coupons=[].concat(n()(i.message))})},onSelectCoupon:function(t,s){this.canDiscount=this.total-this.nodiscount,this.couponId=s,s>0&&this.coupons[t].id==s?this.canDiscount>=this.coupons[t].condition&&(this.discount_fee=this.coupons[t].discount,this.condition=this.coupons[t].condition):this.discount_fee=0,this.status.couponShow=!1},onTableConfirm:function(t){this.table_sn=t.title,this.onToggleStatus("tableShow")},onSubmit:function(){var t=this;if(console.log(this.pay_type),this.total<=0)return this.$toast("消费总额不能为空"),!1;var s={sid:this.sid,total_fee:this.total,no_discount_part:this.nodiscount,couponId:this.couponId,table_sn:this.table_sn,note:this.note};this.util.request({url:"wmall/store/paybill/index",data:s}).then(function(s){var i=s.data.message;if(i.errno)return t.util.$toast(i.message,"",1e3),!1;var o=i.message;t.$router.replace(t.util.getUrl({path:"/pages/public/pay?order_id="+o+"&order_type=paybill"}))})}},created:function(){this.query=this.$route.query,this.query&&(this.sid=this.query.sid)},mounted:function(){this.onLoad()}},c={render:function(){var t=this,s=t.$createElement,i=t._self._c||s;return i("div",{attrs:{id:"store-paybill"}},[i("public-header",{attrs:{title:t.store.title}}),t._v(" "),i("div",{staticClass:"content"},[i("div",{staticClass:"list-block"},[i("ul",[i("li",[i("div",{staticClass:"item-content"},[i("div",{staticClass:"item-inner"},[i("div",{staticClass:"item-title ltr"},[t._v("("+t._s(t.Lang.dollarSignCn)+")消费总额")]),t._v(" "),i("div",{staticClass:"item-input"},[i("input",{staticClass:"align-right",attrs:{type:"number",placeholder:"询问服务员后输入","data-type":"total"},on:{input:t.onInput}})])])])]),t._v(" "),i("li",[i("div",{staticClass:"check",on:{click:function(s){return t.onToggleStatus("noDiscountShow")}}},[i("div",{staticClass:"checked",class:{checked:t.status.noDiscountShow,active:t.status.noDiscountShow}},[i("van-icon",{attrs:{name:t.status.noDiscountShow?"check":""}})],1),t._v(" "),i("span",{staticClass:"ltr"},[t._v("(如酒水、套餐)输入不参与优惠金额")])])]),t._v(" "),t.status.noDiscountShow?i("li",[i("div",{staticClass:"item-content"},[i("div",{staticClass:"item-inner"},[i("div",{staticClass:"item-title"},[t._v("不参与优惠金额")]),t._v(" "),i("div",{staticClass:"item-input"},[i("input",{staticClass:"align-right",attrs:{type:"text",placeholder:"询问服务员后输入","data-type":"nodiscount"},on:{input:t.onInput}})])])])]):t._e()])]),t._v(" "),"1"==t.paybill_extra?[i("van-cell-group",[i("van-cell",{attrs:{title:"包厢/桌号","arrow-direction":"down"},on:{click:function(s){return t.onToggleStatus("tableShow")}}},[i("div",{staticClass:"flex c-gray",attrs:{slot:"right-icon"},slot:"right-icon"},[t.table_sn?i("span",[t._v(t._s(t.table_sn))]):i("span",[t._v("请选择桌号")]),t._v(" "),i("van-icon",{staticClass:"margin-5-l",attrs:{name:"arrow-down"}})],1)])],1),t._v(" "),i("van-cell-group",{staticClass:"margin-10-t"},[i("van-field",{attrs:{type:"textarea",placeholder:"请输入备注，最多50字哦"},model:{value:t.note,callback:function(s){t.note=s},expression:"note"}})],1)]:t._e(),t._v(" "),i("div",{staticClass:"list-block"},[i("van-cell-group",[i("van-cell",{attrs:{"is-link":""},on:{click:function(s){return t.onToggleStatus("couponShow")}}},[i("div",{staticClass:"van-cell__value"},[i("span",{class:{"c-danger":t.couponId>0}},[t._v(t._s(t.couponTitle))])]),t._v(" "),i("template",{slot:"title"},[i("span",{staticClass:"van-cell-text"},[t._v("优惠券")]),t._v(" "),t.couponNum>0?i("van-tag",{attrs:{type:"danger"}},[t._v(t._s(t.couponNum)+"张可用")]):t._e()],1)],2),t._v(" "),i("van-cell",{attrs:{title:"实付金额",value:t.final}})],1)],1),t._v(" "),t._e(),t._v(" "),i("div",{staticClass:"list-block"},[i("div",{staticClass:"confirm"},[t.total>0&&t.total>t.nodiscount?i("div",{staticClass:"submit",on:{click:t.onSubmit}},[t._v("确认买单")]):i("div",{staticClass:"submit disabled"},[t._v("确认买单")])])])],2),t._v(" "),t.showPreLoading?i("iloading"):t._e(),t._v(" "),t.coupons.length>0?i("van-popup",{attrs:{position:"bottom"},model:{value:t.status.couponShow,callback:function(s){t.$set(t.status,"couponShow",s)},expression:"status.couponShow"}},[i("div",{staticClass:"popup-coupon"},[i("div",{staticClass:"popup-title van-hairline--bottom"},[t._v("ئېتىبار بىلەت")]),t._v(" "),i("div",{staticClass:"popup-container"},[i("load",{attrs:{type:"loaded",text:"可用代金券("+t.coupons.length+"张)",bgcolor:"#f5f5f5"}}),t._v(" "),i("div",{staticClass:"coupon-list"},[i("div",{staticClass:"content-padded"},t._l(t.coupons,function(s,o){return i("div",{staticClass:"coupon-item"},[i("div",{staticClass:"clearfix",on:{click:function(i){return t.onSelectCoupon(o,s.id)}}},[i("span",{staticClass:"circle circle-left"}),t._v(" "),i("span",{staticClass:"circle circle-right"}),t._v(" "),i("div",{staticClass:"left"},[i("div",{staticClass:"store-logo"},[i("img",{attrs:{src:s.logo,alt:""}})]),t._v(" "),i("div",{staticClass:"coupon-detail"},[i("div",{staticClass:"coupon-title"},[t._v(t._s(s.title))]),t._v(" "),i("div",{staticClass:"use-time"},[t._v("有效期至:"+t._s(s.endtime_cn))])])]),t._v(" "),i("div",{staticClass:"right"},[i("div",{staticClass:"price"},[i("span",[t._v(t._s(t.Lang.dollarSign))]),t._v(t._s(s.discount)+"\n\t\t\t\t\t\t\t\t\t")]),t._v(" "),i("div",{staticClass:"condition"},[t._v("满"+t._s(s.condition)+t._s(t.Lang.dollarSignCn)+"可用")])])]),t._v(" "),t.couponId==s.id?i("div",{staticClass:"selected-status"},[i("img",{attrs:{src:"static/img/success.png",alt:""}})]):t._e()])}),0)])],1),t._v(" "),i("div",{staticClass:"popup-cancle van-hairline--top",on:{click:function(s){return t.onSelectCoupon(-1,0)}}},[t._v("不使用代金券")])])]):t._e(),t._v(" "),i("van-popup",{attrs:{position:"bottom"},model:{value:t.status.tableShow,callback:function(s){t.$set(t.status,"tableShow",s)},expression:"status.tableShow"}},[i("van-picker",{attrs:{"show-toolbar":"",title:"请选择桌号",columns:t.tables,"value-key":"title"},on:{cancel:function(s){return t.onToggleStatus("tableShow")},confirm:t.onTableConfirm}})],1)],1)},staticRenderFns:[]};var u=i("VU/8")(l,c,!1,function(t){i("ZVqN")},null,null);s.default=u.exports}});
//# sourceMappingURL=81.240e7466069491f2fcc8.js.map