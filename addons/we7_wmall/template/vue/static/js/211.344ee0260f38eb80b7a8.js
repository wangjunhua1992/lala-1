webpackJsonp([211],{O62G:function(t,e,s){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var i=s("Gu7T"),a=s.n(i),o=s("Cz8s"),n=s("deIj"),l={data:function(){return{relation:"member2clerk",kefuopenid:0,orderid:0,chat:{status:1},kefu:{},fans:{},order:{id:0},chatlog:{min:0,psize:100,loading:!1,finished:!1,data:[]},content:"",fastReply:[],faseReplyMsg:"",hasSendOrder:!1,orders:[],websocket:{},heartCheck:{},status:{fastReply:!1,others:!1,voice:!1},popup:{fastReply:!1,order:!1},showPreLoading:!0,islegal:!1}},methods:{onLoad:function(){var t=this;Object(n.a)({vue:t,url:"kefu/member/chat",data:{relation:t.relation,kefuopenid:t.kefuopenid,kefuunionid:t.kefuunionid,orderid:t.orderid,min:t.chatlog.min,psize:t.chatlog.psize},success:function(e){t.chat=t.util.extend(t.chat,e.chat),t.kefu=t.util.extend(t.kefu,e.kefu),t.fans=t.util.extend(t.fans,e.fans),t.order=t.util.extend(t.order,e.order),t.fastReply=[].concat(a()(e.reply)),e.chatlog.logs&&e.chatlog.logs.length>0&&(t.chatlog.data=[].concat(a()(e.chatlog.logs)),e.chatlog.logs.length<t.chatlog.psize&&(t.chatlog.finished=!0)),t.chatlog.min=e.chatlog.min,t.chatlog.min||(t.chatlog.finished=!0),t.util.setWXTitle(t.kefu.title)}})},onLoadMore:function(){var t=this;if(t.chatlog.finished)return t.$nextTick(function(){t.chatlog.loading=!1}),t.util.$toast("没有更多消息了"),!1;Object(n.a)({vue:t,url:"kefu/member/more",data:{chatid:t.chat.id,min:t.chatlog.min,psize:t.chatlog.psize},success:function(e){e.chatlog.logs&&e.chatlog.logs.length>0&&(t.chatlog.data=e.chatlog.logs.concat(t.chatlog.data),e.chatlog.logs.length<t.chatlog.psize&&(t.chatlog.finished=!0)),t.chatlog.min=e.chatlog.min,t.chatlog.min||(t.chatlog.finished=!0),t.chatlog.loading=!1}})},onOrderClick:function(t){t>0&&(this.onSendMessage(t,"orderTakeout"),this.onTogglePopup("order"))},onShowOrders:function(){var t=this;Object(n.a)({vue:t,url:"kefu/member/order",data:{chatid:t.chat.id},success:function(e){var s=e.orders;if(s&&s.length>0)t.orders=[].concat(a()(s)),t.onTogglePopup("order");else{var i="";"member2clerk"==t.relation?i="您最近未在该门店下过单":"member2deliveryer"==t.relation?i="该配送员最近未给您配送过订单":"member2kefu"==t.data.relation&&(i="您暂未在平台下过单"),t.util.$toast(i)}}})},onSendMessage:function(t,e){var s=this;return!!s.islegal&&(s.islegal=!1,t||(t=s.content),e||(e="text"),!(!t||""==t)&&void Object(n.c)({vue:s,url:"kefu/member/addchat",data:{chatid:s.chat.id,type:e,content:t},success:function(t){s.$nextTick(function(){s.chatlog.data.push(t.log),"text"==e?s.content="":"orderTakeout"==e&&(s.hasSendOrder=!0)})}}))},onConfirmFastReply:function(){var t=this;if(!t.faseReplyMsg||""==t.faseReplyMsg)return!1;Object(n.c)({vue:t,url:"kefu/member/addreply",data:{content:t.faseReplyMsg,relation:t.relation},success:function(e){e.reply&&e.reply.length>0&&(t.fastReply=[].concat(a()(e.reply))),t.faseReplyMsg="",t.onTogglePopup("fastReply")}})},onUploadImage:function(t){var e=this;e.$toast.loading({mask:!0,message:"上传中...",duration:0}),e.util.image({obj:t,success:function(t,s){if(s.url&&s.filename){var i=s.filename;e.islegal=!0,e.onSendMessage(i,"image"),e.$toast.clear()}},options:{channel:"h5"}})},onScrollBottom:function(){var t=this;t.islegal=!0,setTimeout(function(){var e=t.$refs.chatlog;e.scrollTop=e.scrollHeight},300)},onToggleStatus:function(t){for(var e in this.status)this.status[e]=e==t&&!this.status[e]},onTogglePopup:function(t){if(!t)return!1;this.popup[t]=!this.popup[t]},onBlur:function(){window.scroll(0,0)},onFocus:function(){this.status.fastReply=!1,this.status.others=!1,document.body.scrollTop=document.body.scrollHeight},onSetNotreadZero:function(){Object(n.c)({vue:this,url:"kefu/member/zero",data:{chatid:this.chat.id},success:function(t){console.log("清零成功")}})},onGetMessage:function(t){var e=this;e.chat&&e.chat.id==t.chat.chatid&&setTimeout(function(){e.chatlog.data.push(t.chat),e.onSetNotreadZero()},200)},initWebSocket:function(){this.iwebsocket.onGetMessage=this.onGetMessage}},mounted:function(){var t=this.util.parseQuery(this.$route.query);this.relation=t.relation,this.kefuopenid=t.kefuopenid,this.kefuunionid=t.kefuunionid,this.orderid=t.orderid,this.onLoad(),this.initWebSocket()},updated:function(){this.onScrollBottom()},destroyed:function(){this.iwebsocket.onGetMessage=null},components:{PublicHeader:o.a},computed:{chatLogBottom:function(){var t=96;return 2==this.chat.status?t=56:this.status.fastReply?t=272:this.status.others&&(t=237),t}}},c={render:function(){var t=this,e=t.$createElement,s=t._self._c||e;return s("div",{attrs:{id:"kefu-chat"}},[s("public-header",{attrs:{title:t.kefu.title}}),t._v(" "),s("div",{staticClass:"content font-14"},[s("div",{staticClass:"connection"},[(t.kefu.isonline,t._e()),t._v(" "),s("div",{staticClass:"call flex c-danger"},[s("div",{staticClass:"flex",on:{click:function(e){return t.util.jsTel(t.kefu.mobile)}}},[s("i",{staticClass:"icon icon-telephone"}),t._v(" "),s("span",[t._v(t._s(t.kefu.mobile_cn))])])])]),t._v(" "),s("div",{ref:"chatlog",staticClass:"chatlog",style:{bottom:t.chatLogBottom+"px"}},[s("van-pull-refresh",{on:{refresh:function(e){return t.onLoadMore()}},model:{value:t.chatlog.loading,callback:function(e){t.$set(t.chatlog,"loading",e)},expression:"chatlog.loading"}},[t.chatlog.data.length>0?[t._l(t.chatlog.data,function(e,i){return["system"==e.type?s("div",{staticClass:"log-item center"},[s("div",{staticClass:"tips"},[t._v(t._s(e.addtime_cn)+" "+t._s(e.content))])]):s("div",{staticClass:"log-item",class:{left:1==e.isleft,right:0==e.isleft}},[e.addtime_cn?s("div",{staticClass:"time flex-center margin-15-b"},[s("span",[t._v(t._s(e.addtime_cn))])]):t._e(),t._v(" "),s("div",{staticClass:"detail"},[s("div",{staticClass:"avatar"},[s("img",{staticClass:"img-100",attrs:{src:e.avatar,alt:""}})]),t._v(" "),"text"==e.type?s("div",{staticClass:"text before"},[t._v(t._s(e.content))]):"image"==e.type?s("div",{staticClass:"image",on:{click:function(s){return t.util.jsPreviewImage(e.content)}}},[s("img",{attrs:{src:e.content,alt:""}})]):"orderTakeout"==e.type?s("div",{staticClass:"order before",on:{click:function(s){return t.util.jsUrl("pages/order/detail",{id:e.orderid})}}},[s("div",{staticClass:"c-gray"},[t._v("订单信息")]),t._v(" "),s("div",{staticClass:"flex-lr margin-10-t"},[s("div",{staticClass:"store-logo"},[s("img",{staticClass:"img-100",attrs:{src:e.content.logo,alt:""}})]),t._v(" "),s("div",{staticClass:"order-info"},[s("div",{staticClass:"flex-lr"},[s("div",{staticClass:"store-title ellipsis"},[t._v(t._s(e.content.title))]),t._v(" "),s("div",{staticClass:"order-status font-12 ellipsis"},[t._v(t._s(e.content.status_cn))])]),t._v(" "),s("div",{staticClass:"flex-lr c-gray"},[s("div",{staticClass:"goods-title ellipsis"},[t._v(t._s(e.content.goods_title))]),t._v(" "),s("div",{staticClass:"order-fee font-12 ellipsis"},[t._v("实付"),s("span",{staticClass:"c-default"},[t._v(t._s(t.Lang.dollarSign)+t._s(e.content.final_fee)+t._s(t.Lang.dollarSignCn))])])])])])]):t._e(),t._v(" "),t._e()])])]})]:t._e(),t._v(" "),"member2clerk"==t.relation&&t.order.id>0&&!t.hasSendOrder?s("div",{staticClass:"order-card margin-15-t"},[s("div",{staticClass:"flex-lr padding-15 van-hairline--bottom"},[s("div",{staticClass:"store-logo"},[s("img",{staticClass:"img-100",attrs:{src:t.order.logo,alt:""}})]),t._v(" "),s("div",{staticClass:"order-info"},[s("div",{staticClass:"flex-lr"},[s("div",{staticClass:"store-title ellipsis"},[t._v(t._s(t.order.title))]),t._v(" "),s("div",{staticClass:"order-status ellipsis"},[t._v(t._s(t.order.status_cn))])]),t._v(" "),s("div",{staticClass:"flex-lr c-gray"},[s("div",{staticClass:"goods-title ellipsis"},[t._v(t._s(t.order.goods_title))]),t._v(" "),s("div",{staticClass:"order-fee ellipsis"},[t._v("\n\t\t\t\t\t\t\t\t\t实付"),s("span",{staticClass:"c-default"},[t._v(t._s(t.Lang.dollarSign)+t._s(t.order.final_fee)+t._s(t.Lang.dollarSignCn))])])])])]),t._v(" "),s("div",{staticClass:"send flex-center",on:{click:function(e){return t.onSendMessage(t.order.id,"orderTakeout")}}},[t._v("\n\t\t\t\t\t\t发送订单\n\t\t\t\t\t")])]):t._e(),t._v(" "),t._e()],2)],1),t._v(" "),1==t.chat.status?s("div",{staticClass:"tools"},[s("div",{staticClass:"guess flex padding-15-lr padding-10-tb van-hairline--bottom"},[s("span",{staticClass:"c-gray margin-10-r"},[t._v("猜你喜欢")]),t._v(" "),s("ul",{staticClass:"guess-list"},[s("li",{staticClass:"guess-item",on:{click:function(e){return t.onShowOrders()}}},[t._v("咨询订单")]),t._v(" "),t._e()])]),t._v(" "),s("div",{staticClass:"main flex-lr"},[t._e(),t._v(" "),s("input",{directives:[{name:"model",rawName:"v-model",value:t.content,expression:"content"}],staticClass:"text",attrs:{type:"text",placeholder:"输入消息..."},domProps:{value:t.content},on:{keyup:function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"enter",13,e.key,"Enter")?null:t.onSendMessage()},blur:t.onBlur,focus:t.onFocus,input:function(e){e.target.composing||(t.content=e.target.value)}}}),t._v(" "),s("i",{staticClass:"icon icon-sort margin-15-lr",on:{click:function(e){return t.onToggleStatus("fastReply")}}}),t._v(" "),t.content?s("div",{staticClass:"btn-send",on:{click:function(e){return t.onSendMessage()}}},[t._v("发送")]):s("i",{staticClass:"icon icon-add",on:{click:function(e){return t.onToggleStatus("others")}}})]),t._v(" "),t.status.fastReply?s("ul",{staticClass:"common-reply van-hairline--top"},[t._l(t.fastReply,function(e,i){return[s("li",{staticClass:"reply-item van-hairline--bottom",on:{click:function(s){return t.onSendMessage(e,"text")}}},[t._v(t._s(e))])]}),t._v(" "),s("li",{staticClass:"reply-item flex-center c-danger"},[s("i",{staticClass:"icon icon-add font-18 margin-5-r"}),t._v(" "),s("span",{on:{click:function(e){return t.onTogglePopup("fastReply")}}},[t._v("添加常用语")])])],2):t._e(),t._v(" "),t.status.others?s("ul",{staticClass:"others"},[s("li",{staticClass:"other-item"},[s("div",{staticClass:"other-item-inner"},[s("input",{staticClass:"weui-uploader__input",attrs:{type:"file",multiple:"multiple",accept:"image/*"},on:{change:function(e){return t.onUploadImage(e)}}}),t._v(" "),s("i",{staticClass:"icon icon-pic-filling"}),t._v(" "),s("span",{staticClass:"margin-10-t"},[t._v("照片")])])])]):t._e()]):s("div",{staticClass:"close-tips ellipsis van-hairline--top"},[t._v(t._s(t.chat.reason))])]),t._v(" "),s("van-popup",{staticClass:"fast-reply-popup",model:{value:t.popup.fastReply,callback:function(e){t.$set(t.popup,"fastReply",e)},expression:"popup.fastReply"}},[s("div",{staticClass:"title"},[t._v("添加快捷短语")]),t._v(" "),s("van-field",{staticClass:"border-0px",attrs:{type:"textarea",placeholder:"例如: 我不能吃辣, 麻烦少放些辣椒",rows:"4"},model:{value:t.faseReplyMsg,callback:function(e){t.faseReplyMsg=e},expression:"faseReplyMsg"}}),t._v(" "),s("div",{staticClass:"flex-center van-hairline--top"},[s("div",{staticClass:"flex-1 flex-center padding-15-tb van-hairline--right",on:{click:function(e){return t.onTogglePopup("fastReply")}}},[t._v("取消")]),t._v(" "),s("div",{staticClass:"flex-1 flex-center padding-15-tb c-gray",on:{click:t.onConfirmFastReply}},[t._v("确认添加")])])],1),t._v(" "),s("van-popup",{staticClass:"order-list-popup",attrs:{position:"bottom"},model:{value:t.popup.order,callback:function(e){t.$set(t.popup,"order",e)},expression:"popup.order"}},[s("div",{staticClass:"title padding-15 flex-lr van-hairline--bottom"},[s("div",{staticClass:"font-15"},[t._v("点击发送订单"),s("span",{staticClass:"c-gray font-12 margin-10-l"},[t._v("展示最近5个订单")])]),t._v(" "),s("i",{staticClass:"icon icon-close font-20",on:{click:function(e){return t.onTogglePopup("order")}}})]),t._v(" "),s("div",{staticClass:"popup-content"},[t._l(t.orders,function(e,i){return s("div",{key:e.id,staticClass:"order-item padding-15-l bg-default font-15",on:{click:function(s){return t.onOrderClick(e.id)}}},[s("div",{staticClass:"item-inner ",class:{"van-hairline--top":i>0}},[s("div",{staticClass:"store-logo"},[s("img",{staticClass:"img-100",attrs:{src:e.logo,alt:""}})]),t._v(" "),s("div",{staticClass:"info"},[s("div",{staticClass:"flex-lr w-100"},[s("div",{staticClass:"store-title ellipsis"},[t._v(t._s(e.title))]),t._v(" "),s("div",{staticClass:"order-status ellipsis"},[t._v(t._s(e.status_cn))])]),t._v(" "),s("div",{staticClass:"flex-lr c-gray padding-10-t"},[s("div",{staticClass:"goods-title ellipsis"},[t._v(t._s(e.goods_title))]),t._v(" "),s("div",{staticClass:"order-fee ellipsis"},[t._v("\n\t\t\t\t\t\t\t\t实付"),s("span",{staticClass:"c-default"},[t._v(t._s(t.Lang.dollarSign)+t._s(e.final_fee))])])])])])])}),t._v(" "),t._e()],2)]),t._v(" "),t.showPreLoading?s("iloading"):t._e()],1)},staticRenderFns:[]};var r=s("VU/8")(l,c,!1,function(t){s("Tn4D")},null,null);e.default=r.exports},Tn4D:function(t,e){}});
//# sourceMappingURL=211.344ee0260f38eb80b7a8.js.map