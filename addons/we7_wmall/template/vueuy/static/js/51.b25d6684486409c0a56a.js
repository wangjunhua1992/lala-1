webpackJsonp([51],{RqKZ:function(t,i){},UC7n:function(t,i,s){"use strict";Object.defineProperty(i,"__esModule",{value:!0});var a=s("Cz8s"),n=s("P8xa"),e=s("Fd2+"),o=s("deIj"),r={components:{PublicHeader:a.a,Dialog:e.a,imessage:n.a},data:function(){return{showPreLoading:!0,isRefresh:!1,zhezhaoShow:!1,message:{type:"",message:"",description:"",redirect:"",btnText:""},pindan:{mine:{}},store:{cn:{}},extra:{},goback:!0}},computed:{cartSendCondition:function(){return(this.store.send_price-this.pindan.total_cart_price).toFixed(2)}},methods:{onPullDownRefresh:function(){this.onLoad()},onChangeZhezhao:function(){this.zhezhaoShow=!this.zhezhaoShow},onLoad:function(){var t=this;Object(o.a)({vue:t,url:"wmall/store/goods/pindan",data:{sid:t.sid,pindan_id:t.pindan_id},success:function(i){t.pindan=i.pindan,t.store=i.store,t.extra=i.extra,t.isRefresh=!1,t.pindan_id=t.pindan.pindan_id,1==t.extra.is_founder&&(window.history.pushState(null,null,document.URL),window.addEventListener("popstate",t.onBrowserBack,!1))},fail:function(i){-1e3==i.errno?(t.util.$toast(i.message,"",1e3),t.pindan_id=0,t.onLoad()):-1001==i.errno?t.message={type:"info",message:i.message,redirect:t.util.getUrl({path:"/pages/home/index"}),btnText:"自己点菜"}:t.util.$toast(i.message,"",1e3)}})},onEditGoods:function(){this.$router.replace(this.util.getUrl({path:"pages/store/goods",query:{sid:this.store.id,pindan_id:this.pindan_id}}))},onGiveUp:function(t){var i=this,s="确定不要继续拼单了吗？";1==i.extra.is_founder&&(s="删除后不可恢复，确认删除吗？"),Object(o.c)({vue:i,confirm:s,url:"wmall/store/goods/giveupPindan",data:{sid:i.sid,cart_id:t},success:function(t){i.pindan=t.pindan,i.extra.not_takepart=t.extra.not_takepart,i.util.$toast("取消拼单成功",i.util.getUrl({path:"pages/store/goods",query:{sid:i.sid}}),1e3,"replace")},fail:function(t){-1e3==t.errno&&i.onGoBack()}})},onContinue:function(t){var i=this;Object(o.c)({vue:i,url:"wmall/store/goods/continuePindan",data:{sid:i.sid},success:function(t){i.onLoad()}})},onTakePindan:function(){var t=this;Object(o.c)({vue:t,url:"wmall/store/goods/takePartPindan",data:{sid:t.sid,pindan_id:t.pindan_id},success:function(i){t.$router.replace(t.util.getUrl({path:"/pages/store/goods",query:{sid:t.sid,pindan_id:t.pindan_id}}))},fail:function(i){t.util.$toast(i.message,"",1e3),t.pindan_id=0,t.onLoad()}})},onSubmit:function(){var t=this;e.a.confirm({title:"ئەسكەرتىش",message:"去结算后其他用户不可加入，确定去结算吗？",confirmButtonText:"جەزىملەش",cancelButtonText:"قالدۇرۇش"}).then(function(){t.$router.push(t.util.getUrl({path:"/pages/order/create",query:{sid:t.store.id,is_pindan:1,pindan_id:t.pindan.pindan_id}}))})},onGoBack:function(){window.history.length<2?this.$router.replace(this.util.getUrl({path:"/pages/store/goods",query:{sid:this.sid}})):(this.goback=!1,this.$router.back())},onBrowserBack:function(){var t=this,i=this;if(!i.goback)return i.onGoBack(),!1;this.goback=!1,e.a.confirm({title:"你确定退出拼单吗",message:"退出拼单不会保留此次拼单内容",confirmButtonText:"جەزىملەش",cancelButtonText:"قالدۇرۇش"}).then(function(){1==i.extra.is_founder&&Object(o.c)({vue:i,url:"wmall/store/goods/giveupPindan",data:{sid:i.sid,cart_id:i.pindan_id},fail:function(t){-1e3==t.errno&&i.onGoBack()}})}).catch(function(){t.goback=!0,window.history.pushState(null,null,document.URL)})}},created:function(){this.$route.query&&this.$route.query.sid>0&&(this.sid=this.$route.query.sid,this.pindan_id=0,this.$route.query.pindan_id>0&&(this.pindan_id=this.$route.query.pindan_id))},mounted:function(){this.onLoad()},beforeDestroy:function(){return window.removeEventListener("popstate",this.onBrowserBack,!1),!1}},d={render:function(){var t=this,i=t.$createElement,s=t._self._c||i;return s("div",{attrs:{id:"store-pindan"}},[s("public-header",{attrs:{title:"拼单"}}),t._v(" "),s("div",{staticClass:"content"},[1==t.extra.is_founder&&2==t.pindan.mine.pindan_status?[s("div",{staticClass:"lock"},[s("div",{staticClass:"lock-inner w-100 text-center"},[s("div",{staticClass:"tip font-15"},[t._v("锁定状态，手动解锁后可继续拼单")]),t._v(" "),s("van-button",{staticClass:"bg-danger font-16 deblock",attrs:{size:"normal",block:""},on:{click:t.onContinue}},[t._v("解锁")])],1)])]:[s("div",{staticClass:"content-inner"},[s("van-pull-refresh",{on:{refresh:t.onPullDownRefresh},model:{value:t.isRefresh,callback:function(i){t.isRefresh=i},expression:"isRefresh"}},[s("div",{staticClass:"store bg-default span-center padding-15"},[s("img",{attrs:{src:t.store.logo}}),t._v(" "),s("div",{staticClass:"name ellipsis"},[t._v(t._s(t.store.title))]),t._v(" "),1==t.pindan.pindan_status?[1==t.extra.is_founder?s("van-button",{staticClass:"bg-danger font-16",attrs:{size:"normal",block:""},on:{click:t.onChangeZhezhao}},[t._v("邀请好友")]):t._e(),t._v(" "),1==t.extra.not_takepart?s("van-button",{staticClass:"bg-danger font-16",attrs:{size:"normal",block:""},on:{click:t.onTakePindan}},[t._v("参与拼单")]):t._e()]:2==t.pindan.pindan_status?[s("div",{staticClass:"tip font-15"},[t._v("正在提交拼单中...")])]:3==t.pindan.pindan_status?[s("div",{staticClass:"tip font-15"},[t._v("拼单订单已提交成功")])]:t._e()],2),t._v(" "),t.pindan.mine?[s("div",{staticClass:"font-14 c-gray margin-10"},[t._v("拼单列表")]),t._v(" "),s("div",{staticClass:"order-info bg-default padding-10-r"},[t.pindan.mine.member?s("div",{staticClass:"user flex-lr padding-10-t"},[s("div",{staticClass:"left flex"},[s("img",{staticClass:"avatar",attrs:{src:t.pindan.mine.member.avatar}})]),t._v(" "),s("div",{staticClass:"right flex-lr van-hairline--bottom"},[s("div",{staticClass:"nickname"},[s("span",{staticClass:"ellipsis"},[t._v(t._s(t.pindan.mine.member.nickname))]),t._v(" "),s("span",{staticClass:"label label-mine"},[t._v("我")]),t._v(" "),t.pindan.mine.id==t.pindan.pindan_id?s("span",{staticClass:"label label-start"},[t._v("发起")]):t._e()]),t._v(" "),1==t.pindan.pindan_status?s("div",{staticClass:"btn-group flex"},[s("div",{staticClass:"btn-item edit-btn",on:{click:t.onEditGoods}},[t._v("编辑商品")]),t._v(" "),s("div",{staticClass:"btn-item cancle-btn",on:{click:function(i){return t.onGiveUp(t.pindan.mine.id)}}},[t._v("不拼了")])]):t._e()])]):t._e(),t._v(" "),t.pindan.mine.data?s("div",{staticClass:"goods flex-lr"},[s("div",{staticClass:"left"}),t._v(" "),s("div",{staticClass:"right"},[t._l(t.pindan.mine.data,function(i){return t._l(i,function(i){return s("div",{key:i.id,staticClass:"goods-item"},[s("div",{staticClass:"goods-title ellipsis"},[t._v(t._s(i.title))]),t._v(" "),s("div",{staticClass:"goods-num"},[t._v("x"+t._s(i.num))]),t._v(" "),s("div",{staticClass:"goods-price c-danger"},[t._v(t._s(t.Lang.dollarSign)+t._s(i.total_discount_price))])])})}),t._v(" "),t.pindan.mine.box_price>0?s("div",{staticClass:"goods-item"},[s("div",{staticClass:"goods-title ellipsis"},[t._v("قاچا ھەققى")]),t._v(" "),s("div",{staticClass:"goods-num"}),t._v(" "),s("div",{staticClass:"goods-price c-danger"},[t._v(t._s(t.Lang.dollarSign)+t._s(t.pindan.mine.box_price))])]):t._e()],2)]):t._e()])]:t._e(),t._v(" "),t.pindan.other&&t.pindan.other.length>0?[s("div",{staticClass:"font-14 c-gray margin-10"},[t._v("其他拼友")]),t._v(" "),t._l(t.pindan.other,function(i){return s("div",{key:i.id,staticClass:"order-info bg-default padding-10-r margin-10-b"},[s("div",{staticClass:"user flex-lr padding-10-t"},[s("div",{staticClass:"left flex"},[s("img",{staticClass:"avatar",attrs:{src:i.member.avatar}})]),t._v(" "),s("div",{staticClass:"right flex-lr van-hairline--bottom"},[s("div",{staticClass:"nickname"},[s("span",{staticClass:"ellipsis"},[t._v(t._s(i.member.nickname))]),t._v(" "),i.id==t.pindan.pindan_id?s("span",{staticClass:"label label-start"},[t._v("发起")]):t._e()]),t._v(" "),t.extra.is_founder?s("i",{staticClass:"icon icon-delete margin-10-r font-18 c-gray",on:{click:function(s){return t.onGiveUp(i.id)}}}):t._e()])]),t._v(" "),s("div",{staticClass:"goods flex-lr"},[s("div",{staticClass:"left"}),t._v(" "),s("div",{staticClass:"right"},[t._l(i.data,function(i){return t._l(i,function(i){return s("div",{key:i.id,staticClass:"goods-item"},[s("div",{staticClass:"goods-title ellipsis"},[t._v(t._s(i.title))]),t._v(" "),s("div",{staticClass:"goods-num"},[t._v("x"+t._s(i.num))]),t._v(" "),s("div",{staticClass:"goods-price c-danger"},[t._v(t._s(t.Lang.dollarSign)+t._s(i.total_discount_price))])])})}),t._v(" "),i.box_price>0?s("div",{staticClass:"goods-item"},[s("div",{staticClass:"goods-title ellipsis"},[t._v(t._s(t.store.cn.box_price))]),t._v(" "),s("div",{staticClass:"goods-num"}),t._v(" "),s("div",{staticClass:"goods-price c-danger"},[t._v(t._s(t.Lang.dollarSign)+t._s(i.box_price))])]):t._e()],2)])])})]:t._e(),t._v(" "),s("van-cell-group",{staticClass:"margin-10-t"},[t.store.pack_price>0?s("van-cell",{attrs:{title:t.store.cn.pack_fee}},[s("span",{attrs:{slot:"right-icon"},slot:"right-icon"},[s("span",{staticClass:"c-danger"},[t._v(t._s(t.Lang.dollarSign)+t._s(t.store.pack_price))])])]):t._e(),t._v(" "),s("van-cell",{attrs:{title:"يەتكۈزۈش ھەققى"}},[s("span",{attrs:{slot:"right-icon"},slot:"right-icon"},[t._v("\n\t\t\t\t\t\t\t另需配送费 "),s("span",{staticClass:"c-danger"},[t._v(t._s(t.Lang.dollarSign)+t._s(t.store.delivery_price))])])]),t._v(" "),s("van-cell",{attrs:{title:"商品费用"}},[s("span",{staticClass:"c-danger",attrs:{slot:"right-icon"},slot:"right-icon"},[t._v("\n\t\t\t\t\t\t\t"+t._s(t.Lang.dollarSign)+t._s(t.pindan.total_cart_price)+"\n\t\t\t\t\t\t")])])],1)],2)],1),t._v(" "),1==t.extra.is_founder?s("div",{staticClass:"cart flex-lr"},[t.pindan.takepart_num>1?s("div",{staticClass:"takepart-tips"},[t._v("\n\t\t\t\t\t您的"),s("span",{staticClass:"c-danger"},[t._v(t._s(t.pindan.takepart_num-1))]),t._v("位拼友已完成拼单\n\t\t\t\t")]):t._e(),t._v(" "),s("div",{staticClass:"cart-tips"},[s("div",{staticClass:"total font-bold"},[t._v(t._s(t.Lang.dollarSign)+t._s(t.pindan.total_cart_price))]),t._v(" "),s("div",{staticClass:"delivery-fee"},[t._v("另需配送费"+t._s(t.Lang.dollarSign)+t._s(t.store.delivery_price))])]),t._v(" "),t.pindan.num>0?[t.cartSendCondition>0?s("div",{staticClass:"cart-btn disabled"},[t._v("差"+t._s(t.Lang.dollarSign)+t._s(t.cartSendCondition)+"起送")]):t._e(),t._v(" "),t.cartSendCondition<=0?s("div",{staticClass:"cart-btn ",class:{disabled:0},on:{click:t.onSubmit}},[t._v("去结算")]):t._e()]:[s("div",{staticClass:"cart-btn disabled"},[t._v("未选择商品")])]],2):t._e()]],2),t._v(" "),t.zhezhaoShow?s("div",{staticClass:"share-zhezhao",on:{click:function(i){return t.onChangeZhezhao()}}},[s("img",{attrs:{src:"static/img/share-layer.png",alt:""}})]):t._e(),t._v(" "),t.message.type?s("imessage",{attrs:{message:t.message}}):t._e(),t._v(" "),s("transition",{attrs:{name:"loading"}},[t.showPreLoading?s("iloading"):t._e()],1)],1)},staticRenderFns:[]};var l=s("VU/8")(r,d,!1,function(t){s("RqKZ")},null,null);i.default=l.exports}});
//# sourceMappingURL=51.b25d6684486409c0a56a.js.map