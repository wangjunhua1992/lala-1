webpackJsonp([36],{N7uu:function(t,s,i){"use strict";Object.defineProperty(s,"__esModule",{value:!0});var a=i("Dd8w"),e=i.n(a),o=i("NYxO"),d=i("Cz8s"),n=i("RoZr"),r=i("Vr3d"),c={data:function(){return{preLoading:!0,cart:{},store:{},goodsActive:{}}},components:{PublicHeader:d.a,StoreCart:n.a,GoodsHandle:r.a},methods:e()({},Object(o.b)(["replaceStore","replaceCart"]),{selectPinadan:function(t){this.pindan_id=t},onLoad:function(){var t=this;return this.$route.query.sid?(this.sid=this.$route.query.sid,this.$route.query.id?(this.id=this.$route.query.id,void this.util.request({url:"wmall/store/goods/detail",data:{sid:this.sid,id:this.id}}).then(function(s){t.preLoading=!1;var i=s.data.message;if(!i.errno){var a=(i=i.message).goodsDetail;t.goodsActive=a,t.cart=i.cart.message.cart,t.store=i.store,t.replaceStore(i.store),t.replaceCart(i.cart.message.cart)}})):(this.$toast("参数错误"),!1)):(this.$toast("参数错误"),!1)}}),computed:e()({},Object(o.c)(["istore","icart"])),created:function(){this.query=this.$route.query,this.query&&(this.pindan_id=0,this.query.pindan_id>0&&(this.pindan_id=this.query.pindan_id))},mounted:function(){this.onLoad()}},v={render:function(){var t=this,s=t.$createElement,i=t._self._c||s;return i("div",{attrs:{id:"goods-detail"}},[i("public-header",{attrs:{title:"تاۋار تەپسىلاتى"}}),t._v(" "),i("div",{staticClass:"content"},[i("div",{staticClass:"goods-img"},[t.goodsActive.slides&&!t.goodsActive.slides.length?i("img",{attrs:{src:t.goodsActive.thumb_,alt:""}}):i("van-swipe",{attrs:{autoplay:3e3,"indicator-color":"#ff2d4b"}},t._l(t.goodsActive.slides,function(t,s){return i("van-swipe-item",{key:s,attrs:{ss:""}},[i("img",{attrs:{src:t,alt:""}})])}),1)],1),t._v(" "),i("div",{staticClass:"goods-name"},[t._v(t._s(t.goodsActive.title))]),t._v(" "),i("div",{staticClass:"sell-info"},[t._v("سېتىلغىنى"+t._s(t.goodsActive.sailed)+"  ياخشى باھا"+t._s(t.goodsActive.comment_good))]),t._v(" "),i("div",{staticClass:"goods-num"},[i("van-row",[i("van-col",{staticClass:"price flex",attrs:{span:"18"}},[i("div",[t._v("\n\t\t\t\t\t\t"+t._s(t.Lang.dollarSign)+" "),i("span",{staticClass:"fee"},[t._v(t._s(t.goodsActive.price))])]),t._v(" "),1!=t.goodsActive.kabao_status||t.goodsActive.discount_price?t._e():i("div",{staticClass:"kabao-price-wrap"},[i("span",{staticClass:"kabao-price"},[t._v("\n\t\t\t\t\t\t\t￥"+t._s(t.goodsActive.kabao_price)+"\n\t\t\t\t\t\t")]),t._v(" "),i("span",{staticClass:"kabao-label"},[i("i",{staticClass:"icon icon-vip"}),t._v(" "),i("span",[t._v("会员价")])])]),t._v(" "),t.goodsActive.unitnum>1?i("div",[i("span",{staticClass:"goods-unitnum"},[t._v(t._s(t.goodsActive.unitnum_multi_cn)+"购")])]):t._e(),t._v(" "),1!=t.goodsActive.svip_status||t.goodsActive.discount_price?t._e():i("div",{staticClass:"svip-price-tips margin-10-l"},[i("div",{staticClass:"svip-price"},[i("i",{staticClass:"icon icon-crownfill"}),t._v(" "),i("span",[t._v(t._s(t.goodsActive.discount)+"折")])]),t._v(" "),i("div",{staticClass:"svip-activity"},[t._v("超级会员专享")])])]),t._v(" "),i("goods-handle",{attrs:{goods:t.goodsActive,optionId:0,from:"detail"}})],1)],1),t._v(" "),i("div",{staticClass:"goods-evaluate"},[t._v("تاۋار باھالاش")]),t._v(" "),i("div",{staticClass:"praise text-center"},[t._v("\n\t\t\tياخشى باھا نىسپىتى\n\t\t\t"),i("span",{staticClass:"rate"},[t._v(t._s(t.goodsActive.comment_good_percent))]),t._v(" "),i("span",{staticClass:"num"},[t._v("(جەمئىي"+t._s(t.goodsActive.comment_total)+"ئادەم باھا بەردى)")])]),t._v(" "),i("div",{staticClass:"progress"},[i("div",{staticClass:"progress-bar"},[i("div",{staticClass:"progress-active",style:{width:t.goodsActive.comment_good_percent}})])]),t._v(" "),i("div",{staticClass:"goods-desc"},[t._v("تاۋار چۈشەندۈرلىشى")]),t._v(" "),i("div",{staticClass:"goods-desc-con",domProps:{innerHTML:t._s(t.goodsActive.description)}})]),t._v(" "),i("router-link",{staticClass:"\n\t",attrs:{tag:"div",to:t.util.getUrl({path:"/pages/store/goods",query:{sid:t.store.id}})}},[t._v("\n\t\t进入店铺\n\t")]),t._v(" "),i("store-cart",{attrs:{show:!0,store:t.store,pindan_id:t.pindan_id},on:{selectPinadan:t.selectPinadan}}),t._v(" "),i("transition",{attrs:{name:"loading"}},[t.preLoading?i("iloading"):t._e()],1)],1)},staticRenderFns:[]};var l=i("VU/8")(c,v,!1,function(t){i("UYK2")},null,null);s.default=l.exports},UYK2:function(t,s){}});
//# sourceMappingURL=36.b28f892765242e649c1a.js.map