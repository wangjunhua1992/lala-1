webpackJsonp([201],{"9eGM":function(e,t){},IeHp:function(e,t,a){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var s=a("woOf"),r=a.n(s),i=a("Gu7T"),o=a.n(i),l=a("Cz8s"),n=a("mzkE"),d=a("5ryX"),c=a("Fd2+"),v=a("MJLE"),_=a.n(v),p=a("Yo4o"),m=a("nIsk"),u={components:{PublicHeader:l.a,PublicFooter:n.a,navigator:d.a,Dialog:c.a},data:function(){return{showPreLoading:!0,active_tab:2,goods:[],store:{cn:{}},order:{},activityed:[],deliveryer:{},logs:{},log:{},maxid:"",slides:[],refundmaxid:"",share:{info:{}},refund:{},refund_logs:{},can_delete:0,menufooter:{},sendRedpacket:!1,zhezhaoShow:!1,orderShare:!1,show_location:0,showSelectCall:!1,config_mall:{call_deliveryer_need_select:0,mobile:""},member:{},scrollTop:0,showMap:!0,showYinsihao:!1,yinsihao:{secret_mobile:"",extension:""},servicePopupStatus:!1,deliveryerMarkers:[],memberMarkers:[],storeMarkers:[],MapType:"gaode"}},methods:{onRefresh:function(){window.location.reload()},onChangeStatus:function(e,t,a){if("cancel"==t)var s={url:"wmall/order/index/cancel?id="+e,confirm:"确定取消订单吗?"};else if("end_1"==t||"end_2"==t){s={url:"wmall/order/index/end?id="+e,confirm:"你确定收到该商家的外卖?",successUrl:"/pages/order/comment?id="+e};"end_2"==t&&(s.confirm="你确定收到该商家的外卖?")}else if("remind"==t)s={url:"wmall/order/index/remind?id="+e};this.util.jspost(s)},onFinishMealPay:function(){c.a.confirm({title:"温馨提示",message:"您的支付方式为餐后支付，请到商家收银台付款"}).then(function(){})},onCallStoreOrDeliveryer:function(e){var t=this,a=this;if(1==a.order.data.yinsihao_status)a.util.request({url:"yinsihao/yinsihao",data:{order_id:a.order.id,type:e,ordersn:a.order.ordersn}}).then(function(s){var r=s.data.message;if(r.errno)if(-1e3==r.errno){var i="";"store"==e?i=a.store.telephone:"deliveryer"==e&&(i=a.deliveryer.mobile),a.util.jsTel(i)}else-2==r.errno?a.servicePopupStatus=!0:t.$toast(r.message);else r=r.message,a.yinsihao.secret_mobile=r.data.secret_mobile,a.yinsihao.extension=r.data.extension,a.showYinsihao=!0});else{var s="";"store"==e?s=a.store.telephone:"deliveryer"==e&&(s=a.deliveryer.mobile),a.util.jsTel(s)}},onLoad:function(){var e=this;this.$route.query.id?this.id=this.$route.query.id:this.$toast("订单不存在或已删除！"),this.util.request({url:"wmall/order/index/new_detail",data:{id:this.id,menufooter:1}}).then(function(t){e.showPreLoading=!1;var a=t.data.message;if(a.errno)e.$toast(a.message);else{if(e.goods=[].concat(o()(e.goods),o()(a.message.goods)),e.store=a.message.store,1==e.util.getStorage("istamp")){var s=Math.floor(100*Math.random());a.message.order.final_fee=s}if(e.order=a.message.order,e.activityed=[].concat(o()(e.activityed),o()(a.message.activityed)),e.deliveryer=a.message.deliveryer,e.logs=a.message.order_log.logs,e.log=a.message.order_log.log,e.maxid=a.message.order_log.maxid,e.slides=0==a.message.slides?a.message.slides:[].concat(o()(e.slides),o()(a.message.slides)),e.share=a.message.share,e.refund=a.message.refund_data.refund,e.refund_logs=a.message.refund_data.refund_logs,e.refundmaxid=a.message.refund_data.refundmaxid,e.can_delete=a.message.can_delete,e.show_location=a.message.show_location,e.config_mall=a.message.config_mall,e.MapType=e.util.getStorage("MapType"),e.menufooter=window.menufooter,2==e.order.order_type&&a.message.qrcode&&e.$nextTick(function(){this.newQrcode(a.message.qrcode)}),e.member=a.message.member,1==e.show_location&&(e.loadmap(),setInterval(function(){e.onRefreshMap()},1e4),e.checkContentDivScroll()),e.share.info&&(e.share.info.share_button>0||1==e.share.info.superRedpacket_share_status)){var r=window._share,i=e;2==i.share.info.share_button&&(r.success=function(){i.util.request({url:"ordergrant/share/grant",data:{id:i.order.id}}).then(function(e){i.util.$toast(e.data.message.message.message,i.util.getUrl({path:"pages/order/detail",query:{id:i.order.id}}),1e3)})}),i.wx.share(r)}}})},onChangeZhezhao:function(e){e&&(this.orderShare=!1,this.sendRedpacket=!1),this.zhezhaoShow=!this.zhezhaoShow},onDeleteOrder:function(){var e=this,t=this;t.$dialog.confirm({message:"确订删除该订单吗？"}).then(function(){t.util.request({url:"wmall/order/index/delete",data:{id:t.order.id}}).then(function(a){var s=a.data.message;s.errno?e.$toast(s.message):t.util.$toast(s.message,t.util.getUrl({path:"/pages/order/index"}),1500,"replace")})}).catch(function(){})},onSelectCall:function(e){e&&this.util.jsTel(e),this.showSelectCall=!this.showSelectCall},newQrcode:function(e){new _.a("qrcode",{width:150,height:150,text:e,image:""})},loadmap:function(){var e=this;"google"==e.MapType?Object(m.a)().then(function(t){e.map=new t.maps.Map(document.getElementById("map-container"),{center:{lat:parseFloat(e.order.location_x),lng:parseFloat(e.order.location_y)},zoom:10,disableDefaultUI:!0}),e.onSetMarkers()}):Object(p.a)().then(function(t){e.map=new t.Map("map-container",{resizeEnable:!0,center:[e.order.location_y,e.order.location_x],zoom:10}),e.onSetMarkers()})},onRefreshMap:function(){var e=this,t=this;t.util.request({url:"wmall/order/index/refresh_map",data:{id:t.order.id}}).then(function(a){var s=a.data.message;s.errno?e.$toast(s.message):(s=s.message,t.order=r()(t.order,s.order),s.deliveryer&&(t.deliveryer=r()(t.deliveryer,s.deliveryer)),t.show_location=s.show_location,t.onSetMarkers())})},onSetMarkers:function(){var e=this;if("google"==e.MapType){var t;if(e.order.location_y&&e.order.location_x){var a=function(e,t){this.bounds_=e,this.map_=t,this.div_=null,this.setMap(t)};e.memberOverlay&&e.memberOverlay.setMap(null),(a.prototype=new google.maps.OverlayView).onRemove=function(){this.div_.parentNode.removeChild(this.div_),this.div_=null},a.prototype.onAdd=function(){var t=document.createElement("div");if(0==e.order.is_pay){t.className="marker-common marker-common-gmap marker-mine-nopay";var a=document.createElement("div");a.className="img-wrap";var s=document.createElement("img");s.src=e.member.avatar,s.className="img-100",a.appendChild(s),t.appendChild(a);var r=document.createElement("div"),i=document.createElement("div");i.className="font-12",i.innerHTML="等待支付",r.appendChild(i),t.appendChild(r)}else t.className="marker-mine-route";this.div_=t,this.getPanes().overlayLayer.appendChild(t)},a.prototype.draw=function(){var t=this.getProjection(),a=t.fromLatLngToDivPixel(this.bounds_.getSouthWest()),s=t.fromLatLngToDivPixel(this.bounds_.getNorthEast()),r=this.div_;0==e.order.is_pay?(r.style.left=a.x-50+"px",r.style.top=s.y-50+"px"):(r.style.left=a.x-35+"px",r.style.top=s.y-35+"px")},t=new google.maps.LatLngBounds(new google.maps.LatLng(parseFloat(e.order.location_x),parseFloat(e.order.location_y))),e.memberOverlay=new a(t,e.map)}if(1==e.order.is_pay&&e.store.location_y&&e.store.location_x){var s=function(e,t){this.bounds_=e,this.map_=t,this.div_=null,this.setMap(t)};e.storeOverlay&&e.storeOverlay.setMap(null),(s.prototype=new google.maps.OverlayView).onRemove=function(){this.div_.parentNode.removeChild(this.div_),this.div_=null},s.prototype.onAdd=function(){var t=document.createElement("div");if(1==e.order.status){t.className="marker-common marker-common-gmap marker-store-waiting";var a=document.createElement("div");a.className="img-wrap",(i=document.createElement("img")).src=e.store.logo,i.className="img-100",a.appendChild(i),t.appendChild(a);var s=document.createElement("div"),r=document.createElement("div");r.className="font-12",r.innerHTML="等待商家接单",s.appendChild(r),t.appendChild(s),o=-62.5,l=-44}else{var i;t.className="marker-start-head-route",(i=document.createElement("img")).src=e.store.logo,t.appendChild(i)}this.div_=t,this.getPanes().overlayLayer.appendChild(t)},s.prototype.draw=function(){var t=this.getProjection(),a=t.fromLatLngToDivPixel(this.bounds_.getSouthWest()),s=t.fromLatLngToDivPixel(this.bounds_.getNorthEast()),r=this.div_;1==e.order.status?(r.style.left=a.x-62.5+"px",r.style.top=s.y-115+"px"):(r.style.left=a.x-35+"px",r.style.top=s.y-140+"px")},t=new google.maps.LatLngBounds(new google.maps.LatLng(parseFloat(e.store.location_x),parseFloat(e.store.location_y))),e.storeOverlay=new s(t,e.map)}if(e.order.deliveryer_id>0&&e.deliveryer&&e.deliveryer.location_x&&e.deliveryer.location_y){var r=function(e,t){this.bounds_=e,this.map_=t,this.div_=null,this.setMap(t)};e.deliveryerOverlay&&e.deliveryerOverlay.setMap(null),(r.prototype=new google.maps.OverlayView).onRemove=function(){this.div_.parentNode.removeChild(this.div_),this.div_=null},r.prototype.onAdd=function(){var t=document.createElement("div");t.className="marker-common marker-common-gmap marker-deliveryer-take";var a=document.createElement("div");a.className="img-wrap";var s=document.createElement("img");s.src=e.deliveryer.avatar,s.className="img-100",a.appendChild(s),t.appendChild(a);var r=document.createElement("div"),i=document.createElement("div");i.className="font-12",i.innerHTML=7==e.order.delivery_status?"骑手正在赶往商家":"骑手正在送餐中",r.appendChild(i),t.appendChild(r),this.div_=t,this.getPanes().overlayLayer.appendChild(t)},r.prototype.draw=function(){var e=this.getProjection(),t=e.fromLatLngToDivPixel(this.bounds_.getSouthWest()),a=e.fromLatLngToDivPixel(this.bounds_.getNorthEast()),s=this.div_;s.style.left=t.x-85+"px",s.style.top=a.y-190+"px"},t=new google.maps.LatLngBounds(new google.maps.LatLng(parseFloat(e.deliveryer.location_x),parseFloat(e.deliveryer.location_y))),e.deliveryerOverlay=new r(t,e.map)}}else{var i="",o="",l="";if(e.order.location_y&&e.order.location_x){e.map.remove(e.memberMarkers),i='<div class="marker-mine-route"></div>',o=-35,l=-35,0==e.order.is_pay&&(i='<div class="marker-common marker-mine-nopay"><div class="img-wrap"><img class="img-100" src="'+e.member.avatar+'" alt=""></div><div class=""><div class="font-12 ">等待支付</div></div>',o=-50,l=-44);var n=new AMap.Marker({position:[e.order.location_y,e.order.location_x],offset:new AMap.Pixel(o,l),content:i});e.memberMarkers.push(n),n.setMap(e.map)}if(1==e.order.is_pay&&e.store.location_y&&e.store.location_x){e.map.remove(e.storeMarkers),i='<div class="marker-start-head-route"><img src="'+e.store.logo+'" alt=""/></div>',o=-33,l=-80,1==e.order.status&&(i='<div class="marker-common marker-store-waiting"><div class="img-wrap"><img class="img-100" src="'+e.store.logo+'" alt=""></div><div class=""><div class="font-12 ">等待商家接单</div></div>',o=-62.5,l=-44);n=new AMap.Marker({position:[e.store.location_y,e.store.location_x],offset:new AMap.Pixel(o,l),content:i});e.storeMarkers.push(n),n.setMap(e.map)}if(e.order.deliveryer_id>0&&e.deliveryer&&e.deliveryer.location_x&&e.deliveryer.location_y){e.map.remove(e.deliveryerMarkers),7==e.order.delivery_status?(i='<div class="marker-common marker-deliveryer-take"><div class="img-wrap"><img class="img-100" src="'+e.deliveryer.avatar+'" alt=""></div><div class=""><div class="font-12 ">骑手正在赶往商家</div><div class="font-12 hide">距商家 <span class="c-danger">1.6km 18分钟</span></div> </div>',o=-85,l=-44):4==e.order.delivery_status&&(i='<div class="marker-common marker-deliveryer-take"><div class="img-wrap"><img class="img-100" src="'+e.deliveryer.avatar+'" alt=""></div><div class=""><div class="font-12 ">骑手正在送餐中</div><div class="font-12 hide">预计<span class="c-danger">18</span>分钟送达</span></div> </div>',o=-85,l=-44);n=new AMap.Marker({position:[e.deliveryer.location_y,e.deliveryer.location_x],offset:new AMap.Pixel(o,l),content:i});e.deliveryerMarkers.push(n),n.setMap(e.map)}e.map.setFitView()}},checkContentDivScroll:function(){var e=this,t=document.getElementById("scrollDiv");t.addEventListener("scroll",function(){var a=t.scrollTop-e.scrollTop;e.scrollTop=t.scrollTop,a<0?e.scrollTop<=0&&!e.showMap&&(e.showMap=!0):e.scrollTop>20&&e.showMap&&(e.showMap=!1,e.scrollTop=0)})}},mounted:function(){this.onLoad()}},h={render:function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("div",{attrs:{id:"orderDetail"}},[a("public-header",{attrs:{title:"订单详情"}}),e._v(" "),a("public-footer",{attrs:{preLoading:e.showPreLoading,menufooter:e.menufooter}}),e._v(" "),a("div",{staticClass:"content",attrs:{id:"scrollDiv"}},[a("van-tabs",{model:{value:e.active_tab,callback:function(t){e.active_tab=t},expression:"active_tab"}},[a("van-tab",{staticClass:"order-detail",attrs:{title:"订单详情"}},[a("div",{staticClass:"order-info-wrap "},[1==e.show_location?a("div",{staticClass:"map-wrap",class:{"map-wrap-hide":!e.showMap}},[a("div",{attrs:{id:"map-container"}}),e._v(" "),a("div",{staticClass:"map-refresh",on:{click:e.onRefreshMap}},[a("i",{staticClass:"icon icon-refresh c-white"})])]):e._e(),e._v(" "),a("div",{staticClass:"order-state border-1px-tb"},[a("div",{staticClass:"order-state-con"},[a("div",{staticClass:"guide"},[a("img",{attrs:{src:"static/img/order_status_service.png",alt:""}})]),e._v(" "),a("div",{staticClass:"order-state-detail"},[a("div",{staticClass:"flex-lr"},[e._v("订单"+e._s(e.order.status_cn)),a("span",{staticClass:"pull-right date"},[e._v(e._s(e.order.addtime_cn))])]),e._v(" "),e.log&&7!=e.order.delivery_status?a("div",{staticClass:"tips clearfix"},[e._v(e._s(e.log.note))]):e._e()]),e._v(" "),e.order.data&&1==e.order.data.yinsihao_status?a("div",{staticClass:"yinsihao-baohu-img"},[a("img",{attrs:{src:"static/img/yinsihao_baohu.png",alt:""}})]):e._e()]),e._v(" "),a("div",{staticClass:"order-btn"},[0==e.order.is_pay&&e.order.status<5?[3==e.order.order_type&&"finishMeal"==e.order.pay_type?a("div",{staticClass:"table-cell van-hairline--top van-hairline--right",on:{click:e.onFinishMealPay}},[e._v("立即支付")]):a("router-link",{staticClass:"table-cell van-hairline--top van-hairline--right",attrs:{to:e.util.getUrl({path:"/pages/public/pay",query:{order_id:e.order.id,order_type:"takeout"}})}},[e._v("立即支付")]),e._v(" "),3==e.order.order_type?a("router-link",{staticClass:"table-cell van-hairline--top van-hairline--right",attrs:{to:e.util.getUrl({path:"/tangshi/pages/table/goods",query:{order_id:e.order.id,table_id:e.order.table_id,sid:e.order.sid}})}},[e._v("加菜")]):e._e(),e._v(" "),1==e.order.order_type&&1==e.order.update_order_info_status?a("router-link",{staticClass:"table-cell  van-hairline--top van-hairline--left",attrs:{to:e.util.getUrl({path:"/pages/order/op",query:{id:e.order.id}})}},[e._v("修改备注等信息")]):e._e()]:e._e(),e._v(" "),1==e.order.status?[a("div",{staticClass:"table-cell van-hairline--top",on:{click:function(t){return e.onChangeStatus(e.order.id,"cancel")}}},[e._v("取消订单")]),e._v(" "),1==e.order.is_pay?a("div",{staticClass:"table-cell van-hairline--left van-hairline--top",on:{click:function(t){return e.onChangeStatus(e.order.id,"remind")}}},[e._v("催单")]):e._e()]:e.order.status>1&&e.order.status<5?[e.order.customer_cancel_status?a("div",{staticClass:"table-cell  van-hairline--top",on:{click:function(t){return e.onChangeStatus(e.order.id,"cancel")}}},[e._v("取消订单")]):e._e(),e._v(" "),1==e.order.order_type&&4==e.order.status?a("div",{staticClass:"table-cell  van-hairline--top",on:{click:function(t){return e.onChangeStatus(e.order.id,"end_1")}}},[e._v("确认送达")]):2==e.order.order_type?a("div",{staticClass:"table-cell van-hairline--top",on:{click:function(t){return e.onChangeStatus(e.order.id,"end_2")}}},[e._v("#"+e._s(e.order.serial_sn)+"-我已提货")]):e._e(),e._v(" "),1==e.order.is_pay?a("div",{staticClass:"table-cell van-hairline--left van-hairline--top",on:{click:function(t){return e.onChangeStatus(e.order.id,"remind")}}},[e._v("催单")]):e._e(),e._v(" "),3==e.order.order_type&&1==e.order.is_pay&&"finishMeal"==e.order.pay_type?a("router-link",{staticClass:"table-cell van-hairline--left van-hairline--top",attrs:{to:e.util.getUrl({path:"/tangshi/pages/table/goods",query:{order_id:e.order.id,table_id:e.order.table_id,sid:e.order.sid}})}},[e._v("加菜")]):e._e()]:5==e.order.status?[a("router-link",{staticClass:"table-cell  van-hairline--top",attrs:{to:e.util.getUrl({path:"/pages/store/goods",query:{sid:e.order.sid,order_id:e.order.id}})}},[e._v("再来一单")]),e._v(" "),0==e.order.is_comment?a("router-link",{staticClass:"table-cell  van-hairline--top van-hairline--left",attrs:{to:e.util.getUrl({path:"/pages/order/comment",query:{id:e.order.id}})}},[e._v(e._s(e.order.comment_cn))]):e._e(),e._v(" "),e._e()]:6==e.order.status?[a("router-link",{staticClass:"table-cell  van-hairline--top",attrs:{to:e.util.getUrl({path:"/pages/store/goods",query:{sid:e.order.sid,order_id:e.order.id}})}},[e._v("再来一单")])]:e._e(),e._v(" "),1==e.order.update_address_status?[a("router-link",{staticClass:"table-cell  van-hairline--top van-hairline--left",attrs:{to:e.util.getUrl({path:"/pages/order/address",query:{id:e.order.id}})}},[e._v("修改收货地址")])]:e._e(),e._v(" "),e.order.data&&e.order.data.pindan?[a("router-link",{staticClass:"table-cell  van-hairline--top van-hairline--left",attrs:{to:e.util.getUrl({path:"/pages/order/pindanbill",query:{id:e.order.id}})}},[e._v("拼单分账")])]:e._e(),e._v(" "),1==e.can_delete?a("div",{staticClass:"table-cell van-hairline--top van-hairline--left",on:{click:e.onDeleteOrder}},[e._v("删除订单")]):e._e(),e._v(" "),e.order.refund_status>0?a("router-link",{staticClass:"table-cell  van-hairline--top van-hairline--left",attrs:{to:e.util.getUrl({path:"/pages/order/refund",query:{id:e.order.id}})}},[e._v("退款详情")]):e._e()],2)]),e._v(" "),e.slides?a("div",{staticClass:"swiper-container"},[a("van-swipe",{attrs:{autoplay:3e3,"indicator-color":"#ff2d4b"}},e._l(e.slides,function(t,s){return a("van-swipe-item",{key:s},[a("div",{on:{click:function(a){return e.util.jsUrl(t.wxapp_link)}}},[a("img",{attrs:{src:t.thumb}})])])}),1)],1):e._e(),e._v(" "),(e.order.deliveryer_id,e._e()),e._v(" "),a("div",{staticClass:"content-block-title"},[e._v("订单明细")]),e._v(" "),a("div",{staticClass:"order-details"},[a("div",{staticClass:"order-details-con border-1px-t "},[a("div",{staticClass:"store-info"},[a("router-link",{staticClass:"external",attrs:{tag:"div",to:e.util.getUrl({path:"/pages/store/goods",query:{sid:this.store.id}})}},[a("img",{attrs:{src:e.store.logo,alt:""}}),e._v(" "),a("span",{staticClass:"store-title"},[e._v(e._s(e.store.title))]),e._v(" "),a("span",{staticClass:"icon icon-arrow-right"})]),e._v(" "),a("div",{staticClass:"store-tel",on:{click:function(t){return e.onCallStoreOrDeliveryer("store")}}},[a("span",{staticClass:"icon icon-telephone icon-telephone-circle"})])],1),e._v(" "),a("div",{staticClass:"inner-con border-1px-t"},e._l(e.goods,function(t,s){return a("van-row",{key:s,staticClass:"no-gutter"},[a("van-col",{attrs:{span:"12"}},[e._v(e._s(t.goods_title))]),e._v(" "),a("van-col",{staticClass:"color-muted text-right ",attrs:{span:"4"}},[e._v("x"+e._s(t.goods_num))]),e._v(" "),a("van-col",{staticClass:"text-right",attrs:{span:"8"}},[e._v(e._s(e.Lang.dollarSign)+e._s(t.goods_price))])],1)}),1),e._v(" "),a("div",{staticClass:"inner-con border-1px-t"},[e.order.box_price>0?a("van-row",{staticClass:"no-gutter"},[a("van-col",{attrs:{span:"20"}},[e._v(e._s(3==e.order.order_type?"餐具费":e.store.cn.box_price))]),e._v(" "),a("van-col",{staticClass:"text-right",attrs:{span:"4"}},[e._v(e._s(e.Lang.dollarSign)+e._s(e.order.box_price))])],1):e._e(),e._v(" "),a("van-row",{staticClass:"no-gutter"},[a("van-col",{attrs:{span:"20"}},[e._v(e._s(e.store.cn.pack_fee))]),e._v(" "),a("van-col",{staticClass:"text-right",attrs:{span:"4"}},[e._v(e._s(e.Lang.dollarSign)+e._s(e.order.pack_fee))])],1),e._v(" "),e.order.person_num>0?a("van-row",{staticClass:"no-gutter"},[a("van-col",{attrs:{span:"20"}},[e._v("餐具数量")]),e._v(" "),a("van-col",{staticClass:"text-right",attrs:{span:"4"}},[e._v(e._s(e.order.person_num)+"套")])],1):e._e(),e._v(" "),a("van-row",{staticClass:"no-gutter"},[a("van-col",{attrs:{span:"20"}},[e._v("配送费")]),e._v(" "),a("van-col",{staticClass:"text-right",attrs:{span:"4"}},[e._v(e._s(e.Lang.dollarSign)+e._s(e.order.delivery_fee))])],1),e._v(" "),3==e.order.order_type?a("van-row",{staticClass:"no-gutter"},[a("van-col",{attrs:{span:"20"}},[e._v("服务费")]),e._v(" "),a("van-col",{staticClass:"text-right",attrs:{span:"4"}},[e._v(e._s(e.Lang.dollarSign)+e._s(e.order.serve_fee))])],1):e._e(),e._v(" "),e.order.data?e._l(e.order.data.extra_fee,function(t,s){return e.order.data.extra_fee.length>0?a("van-row",{key:s,staticClass:"no-gutter"},[a("van-col",{attrs:{span:"16"}},[e._v(e._s(t.name))]),e._v(" "),a("van-col",{staticClass:" text-right ",attrs:{span:"8"}},[e._v(e._s(e.Lang.dollarSign)+e._s(t.fee))])],1):e._e()}):e._e()],2),e._v(" "),e.activityed.length>0?a("div",{staticClass:"inner-con border-1px-t"},e._l(e.activityed,function(t,s){return a("van-row",{key:s,staticClass:"no-gutter"},[a("van-col",{staticClass:"icon-before",attrs:{span:"12"}},[a("img",{attrs:{src:"static/img/"+t.type+"_b.png",alt:""}}),e._v("\n\t\t\t\t\t\t\t\t\t\t"+e._s(t.name)+"\n\t\t\t\t\t\t\t\t\t")]),e._v(" "),a("van-col",{staticClass:"text-right discount-note",attrs:{span:"12"}},[e._v(e._s(t.note))])],1)}),1):e._e(),e._v(" "),a("div",{staticClass:"inner-con"},[a("van-row",{staticClass:"no-gutter"},[a("van-col",{attrs:{span:"14"}},[a("span",{staticClass:"color-muted"},[e._v("订单")]),e._v(" "+e._s(e.Lang.dollarSign)+e._s(e.order.total_fee)+" "),a("span",{staticClass:"color-muted"},[e._v(" - 优惠")]),e._v(" "+e._s(e.Lang.dollarSign)+e._s(e.order.discount_fee)+"\n\t\t\t\t\t\t\t\t\t")]),e._v(" "),a("van-col",{staticClass:"text-right color-muted",attrs:{span:"5"}},[e._v("总计")]),e._v(" "),a("van-col",{staticClass:"text-right",attrs:{span:"5"}},[e._v(e._s(e.Lang.dollarSign)+e._s(e.order.final_fee))])],1)],1),e._v(" "),(e.order.order_type,e._e())]),e._v(" "),a("div",{staticClass:"flex-lr font-14 bg-default border-1px-t"},[e.order.kefu&&1==e.order.kefu.store_status?a("router-link",{staticClass:"border-1px-r padding-15-tb flex-center flex-1",attrs:{tag:"div",to:e.util.getUrl({path:"/plugin/pages/kefu/chat",query:{relation:"member2clerk",kefuopenid:e.order.sid,kefuunionid:e.order.sid,orderid:e.order.id}})}},[a("i",{staticClass:"icon icon-wang margin-5-r"}),e._v(" "),a("span",[e._v("联系商家")])]):e._e(),e._v(" "),a("div",{staticClass:"padding-15-tb flex-center flex-1",on:{click:function(t){return e.onCallStoreOrDeliveryer("store")}}},[a("i",{staticClass:"icon icon-telephone margin-5-r"}),e._v(" "),a("span",[e._v("致电商家")])])],1)]),e._v(" "),2==e.order.order_type?[a("div",{staticClass:"content-block-title"},[e._v("订单核销")]),e._v(" "),a("div",{staticClass:"cancel-code border-1px-t"},[a("div",[a("div",{staticClass:"qrcode",attrs:{id:"qrcode"}}),e._v(" "),a("p",{staticClass:"text-center font-12 margin-10-t"},[e._v("核销码: "+e._s(e.order.code))])]),e._v(" "),a("p",{staticClass:"code-text"},[e._v("请商家扫描二维码或者填写核销码即可消费")])])]:e._e(),e._v(" "),e.order.order_type<=2?[a("div",{staticClass:"content-block-title"},[e._v("配送信息")]),e._v(" "),a("div",{staticClass:"other-info"},[a("ul",{staticClass:"border-1px-tb"},[1==e.order.order_type&&e.order.deliveryer_id>0?[a("li",{staticClass:"item-content flex"},[a("div",{staticClass:"item-inner border-1px-b flex-lr"},[a("div",{staticClass:"item-title"},[e._v("配送服务")]),e._v(" "),a("div",{staticClass:"item-after"},[e._v(e._s(e.order.delivery_title))])])]),e._v(" "),a("li",{staticClass:"item-content flex"},[a("div",{staticClass:"item-inner border-1px-b flex-lr"},[a("div",{staticClass:"item-title"},[e._v("配送骑手")]),e._v(" "),a("div",{staticClass:"item-after"},[e._v(e._s(e.deliveryer.title))])])])]:e._e(),e._v(" "),a("li",{staticClass:"item-content flex"},[a("div",{staticClass:"item-inner border-1px-b flex-lr"},[a("div",{staticClass:"item-title"},[e._v(e._s(1==e.order.order_type?"期望时间":"自提时间"))]),e._v(" "),a("div",{staticClass:"item-after"},[e._v(e._s(e.order.delivery_day)+"~"+e._s(e.order.delivery_time))])])]),e._v(" "),1==e.order.order_type?[a("li",{staticClass:"item-content flex"},[a("div",{staticClass:"item-inner border-1px-b flex-lr"},[a("div",{staticClass:"item-title"},[e._v("配送地址")]),e._v(" "),a("div",{staticClass:"item-after",staticStyle:{"line-height":"22px"}},[a("div",[e._v(e._s(e.order.username)+" ("+e._s(e.order.sex)+") "+e._s(e.order.mobile))]),e._v("  "+e._s(e.order.address))])])])]:2==e.order.order_type?[a("li",{staticClass:"item-content flex"},[a("div",{staticClass:"item-inner border-1px-b flex-lr"},[a("div",{staticClass:"item-title"},[e._v("自提地址")]),e._v(" "),a("div",{staticClass:"item-after"},[e._v(e._s(e.store.address))])])])]:e._e(),e._v(" "),e.order.zhunshibao_status>0?a("li",{staticClass:"item-content flex"},[a("div",{staticClass:"item-inner flex-lr"},[a("div",{staticClass:"item-title"},[e._v("准时宝")]),e._v(" "),a("router-link",{staticClass:"item-after",attrs:{tag:"div",to:e.util.getUrl({path:"pages/order/zhunshibao",query:{id:e.order.id}})}},[e._v("\n\t\t\t\t\t\t\t\t\t\t\t准时宝详情\n\t\t\t\t\t\t\t\t\t\t\t"),a("div",{staticClass:"icon icon-right c-gray"})])],1)]):e._e()],2),e._v(" "),e.order.deliveryer_id>0?a("div",{staticClass:"flex-lr font-14 bg-default"},[e.order.kefu&&1==e.order.kefu.deliveryer_status&&3!=e.deliveryer.kefu_status?a("router-link",{staticClass:"border-1px-r padding-15-tb flex-center flex-1",attrs:{tag:"div",to:e.util.getUrl({path:"/plugin/pages/kefu/chat",query:{relation:"member2deliveryer",kefuopenid:e.deliveryer.token,orderid:e.order.id}})}},[a("i",{staticClass:"icon icon-wang margin-5-r"}),e._v(" "),a("span",[e._v("联系骑手")])]):e._e(),e._v(" "),a("div",{staticClass:"padding-15-tb flex-center flex-1",on:{click:function(t){return e.onCallStoreOrDeliveryer("deliveryer")}}},[a("i",{staticClass:"icon icon-telephone margin-5-r"}),e._v(" "),a("span",[e._v("致电骑手")])])],1):e._e()])]:e._e(),e._v(" "),a("div",{staticClass:"content-block-title"},[e._v("其他信息")]),e._v(" "),a("div",{staticClass:"other-info"},[a("ul",{staticClass:"border-1px-tb"},[a("li",{staticClass:"item-content flex"},[a("div",{staticClass:"item-inner border-1px-b flex-lr"},[a("div",{staticClass:"item-title"},[e._v("商户订单号")]),e._v(" "),a("div",{staticClass:"item-after"},[e._v("#"+e._s(e.order.serial_sn))])])]),e._v(" "),a("li",{staticClass:"item-content flex"},[a("div",{staticClass:"item-inner border-1px-b flex-lr"},[a("div",{staticClass:"item-title"},[e._v("平台订单号")]),e._v(" "),a("div",{staticClass:"item-after"},[e._v(e._s(e.order.ordersn))])])]),e._v(" "),a("li",{staticClass:"item-content flex"},[a("div",{staticClass:"item-inner border-1px-b flex-lr"},[a("div",{staticClass:"item-title"},[e._v("订单类型")]),e._v(" "),a("div",{staticClass:"item-after"},[1==e.order.order_type?[e._v("外卖")]:2==e.order.order_type?[e._v("自提")]:3==e.order.order_type?[e._v("店内")]:4==e.order.order_type?[e._v("预定")]:e._e()],2)])]),e._v(" "),3==e.order.order_type?[a("li",{staticClass:"item-content flex"},[a("div",{staticClass:"item-inner border-1px-b flex-lr"},[a("div",{staticClass:"item-title"},[e._v("桌台号")]),e._v(" "),a("div",{staticClass:"item-after"},[e._v(e._s(e.order.table.title))])])]),e._v(" "),a("li",{staticClass:"item-content flex"},[a("div",{staticClass:"item-inner border-1px-b flex-lr"},[a("div",{staticClass:"item-title"},[e._v("来客人数")]),e._v(" "),a("div",{staticClass:"item-after"},[e._v(e._s(e.order.person_num>0?e.order.person_num:"未知"))])])])]:e._e(),e._v(" "),4==e.order.order_type?[a("li",{staticClass:"item-content flex"},[a("div",{staticClass:"item-inner border-1px-b flex-lr"},[a("div",{staticClass:"item-title"},[e._v("预定时间")]),e._v(" "),a("div",{staticClass:"item-after"},[e._v(e._s(e.order.reserve_time))])])]),e._v(" "),a("li",{staticClass:"item-content flex"},[a("div",{staticClass:"item-inner border-1px-b flex-lr"},[a("div",{staticClass:"item-title"},[e._v("桌台类型")]),e._v(" "),a("div",{staticClass:"item-after"},[e._v(e._s(e.order.table_category.title))])])]),e._v(" "),e.order.table_id>0?a("li",{staticClass:"item-content flex"},[a("div",{staticClass:"item-inner border-1px-b flex-lr"},[a("div",{staticClass:"item-title"},[e._v("预定桌号")]),e._v(" "),a("div",{staticClass:"item-after"},[e._v(e._s(e.order.table.title))])])]):e._e(),e._v(" "),a("li",{staticClass:"item-content flex"},[a("div",{staticClass:"item-inner border-1px-b flex-lr"},[a("div",{staticClass:"item-title"},[e._v("预定类型")]),e._v(" "),a("div",{staticClass:"item-after"},[e._v(e._s(e.order.reserve_type_cn))])])]),e._v(" "),a("li",{staticClass:"item-content flex"},[a("div",{staticClass:"item-inner border-1px-b flex-lr"},[a("div",{staticClass:"item-title"},[e._v("来客人数")]),e._v(" "),a("div",{staticClass:"item-after"},[e._v(e._s(e.order.person_num>0?e.order.person_num:"未知"))])])])]:e._e(),e._v(" "),a("li",{staticClass:"item-content flex"},[a("div",{staticClass:"item-inner border-1px-b flex-lr"},[a("div",{staticClass:"item-title"},[e._v("支付方式")]),e._v(" "),a("div",{staticClass:"item-after"},[e._v(e._s(e.order.pay_type_cn))])])]),e._v(" "),a("li",{staticClass:"item-content flex"},[a("div",{staticClass:"item-inner border-1px-b flex-lr"},[a("div",{staticClass:"item-title"},[e._v("备注信息")]),e._v(" "),a("div",{staticClass:"item-after"},[e._v(e._s(e.order.note?e.order.note:"无"))])])]),e._v(" "),e.order.invoice?[e.order.invoice.title?a("li",{staticClass:"item-content flex"},[a("div",{staticClass:"item-inner border-1px-b flex-lr"},[a("div",{staticClass:"item-title"},[e._v("发票抬头")]),e._v(" "),a("div",{staticClass:"item-after"},[e._v(e._s(e.order.invoice.title))])])]):e._e(),e._v(" "),e.order.invoice.recognition?a("li",{staticClass:"item-content flex"},[a("div",{staticClass:"item-inner border-1px-b flex-lr"},[a("div",{staticClass:"item-title"},[e._v("纳税人识别号")]),e._v(" "),a("div",{staticClass:"item-after"},[e._v(e._s(e.order.invoice.recognition))])])]):e._e()]:e._e()],2)])],2)]),e._v(" "),a("van-tab",{staticClass:"order-status",attrs:{title:"订单状态"}},e._l(e.logs,function(t,s){return a("div",{key:s,staticClass:"order-status-item"},[a("div",{staticClass:"guide"},[e.maxid!=s?a("img",{attrs:{src:"static/img/order_status_service_grey.png",alt:""}}):a("img",{attrs:{src:"static/img/order_status_service.png",alt:""}})]),e._v(" "),a("div",{staticClass:"order-status-info"},[a("div",{staticClass:"arrow-left"}),e._v(" "),a("div",{staticClass:"flex-lr"},[e._v(e._s(t.title)),a("span",{staticClass:"time pull-right"},[e._v(e._s(t.addtime))])]),e._v(" "),t.note?a("div",{staticClass:"tips",domProps:{innerHTML:e._s(t.note)}}):e._e()])])}),0)],1)],1),e._v(" "),e.share.info.share_button>0?[a("div",{staticClass:"send-redpacket",on:{click:function(t){e.orderShare=!0}}},[e._m(0)]),e._v(" "),a("van-popup",{staticClass:"popup-send-redpacket padding-15-b",attrs:{overlay:!0},model:{value:e.orderShare,callback:function(t){e.orderShare=t},expression:"orderShare"}},[a("div",{staticClass:"popup-content grant-share"},[a("p",{staticClass:"font-20 grant-share-title margin-10-b"},[e._v("分享规则")]),e._v(" "),a("p",{staticClass:"font-15"},[e._v("1. 顾客在订单完成后"+e._s(e.share.share_grant_days_limit)+"天之内，对订单进行评价并分享到朋友圈即可获取"+e._s(e.share.share_grant)+e._s(e.share.grantType_cn))]),e._v(" "),a("p",{staticClass:"font-15 margin-10-t"},[e._v("2. 每人通过分享订单最多可获取"+e._s(e.share.share_grant_max)+e._s(e.share.grantType_cn)+"，超出后将不再奖励")])]),e._v(" "),a("div",{staticClass:"popup-footer"},[a("div",{staticClass:"grant-share-btn"},[1==e.share.info.share_button?a("router-link",{staticClass:"btn bg-danger",attrs:{tag:"div",to:e.util.getUrl({path:"pages/order/comment",query:{id:e.order.id}})}},[e._v("现在去评价")]):a("div",{staticClass:"btn bg-danger",on:{click:function(t){return e.onChangeZhezhao(!0)}}},[e._v("您已评价,去分享")])],1)])])]:e._e(),e._v(" "),1==e.share.info.superRedpacket_share_status?[a("div",{staticClass:"send-redpacket",on:{click:function(t){e.sendRedpacket=!0}}},[a("img",{attrs:{src:"static/img/send-redpacket.png",alt:""}})]),e._v(" "),a("van-popup",{staticClass:"popup-send-redpacket",attrs:{overlay:!0},model:{value:e.sendRedpacket,callback:function(t){e.sendRedpacket=t},expression:"sendRedpacket"}},[a("div",{staticClass:"popup-content border-1px-b"},[a("img",{attrs:{src:"static/img/wv.png",alt:""}}),e._v(" "),a("p",{staticClass:"font-15 font-bold"},[e._v("恭喜获得"+e._s(e.share.info.superRedpacket.packet_total)+"个红包")]),e._v(" "),a("p",{staticClass:"font-14 margin-10-t"},[e._v("分享给小伙伴，大家一起抢。")])]),e._v(" "),a("div",{staticClass:"popup-footer flex-lr"},[a("div",{staticClass:"cancle border-1px-r",on:{click:function(t){e.sendRedpacket=!1}}},[e._v("取消")]),e._v(" "),a("div",{staticClass:"grant",on:{click:function(t){return e.onChangeZhezhao(!0)}}},[e._v("发红包")])])])]:e._e(),e._v(" "),e.zhezhaoShow?a("div",{staticClass:"share-zhezhao",on:{click:function(t){return e.onChangeZhezhao()}}},[a("img",{attrs:{src:"static/img/share-layer.png",alt:""}})]):e._e(),e._v(" "),a("van-popup",{staticClass:"call-phone",model:{value:e.showSelectCall,callback:function(t){e.showSelectCall=t},expression:"showSelectCall"}},[a("div",{staticClass:"call-phone-header"},[e._v("请选择联系人")]),e._v(" "),a("div",{staticClass:"call-plateform",on:{click:function(t){return e.onSelectCall(e.config_mall.mobile)}}},[a("div",[e._v("联系平台(推荐)")])]),e._v(" "),a("div",{staticClass:"call-deliveryer van-hairline--top",on:{click:function(t){return e.onSelectCall(e.deliveryer.mobile)}}},[a("div",[e._v("联系配送员")])])]),e._v(" "),a("van-popup",{staticClass:"yinsihao-popup",model:{value:e.showYinsihao,callback:function(t){e.showYinsihao=t},expression:"showYinsihao"}},[a("div",{staticClass:"popup-title"},[a("img",{staticClass:"yinsihao-img",attrs:{src:"static/img/yinsihao_bg.png",alt:""}})]),e._v(" "),a("div",{staticClass:"popup-container van-hairline--bottom"},[a("div",{staticClass:"font-14"},[e._v("将通过转播的方式保护您的隐私")]),e._v(" "),a("div",{staticClass:"yinsihao-mobile"},[e._v(e._s(e.yinsihao.secret_mobile)+" 转 "),a("span",{staticClass:"c-danger"},[e._v(e._s(e.yinsihao.extension))])]),e._v(" "),a("div",{staticClass:"yinsihao-tips"},[e._v("拨打"+e._s(e.yinsihao.secret_mobile)),a("br"),e._v("接通后输入分机号"+e._s(e.yinsihao.extension)+"#即可")])]),e._v(" "),a("div",{staticClass:"popup-footer"},[a("div",{staticClass:"yinsihao-cancel",on:{click:function(t){e.showYinsihao=!1}}},[e._v("取消")]),e._v(" "),a("div",{staticClass:"yinsihao-confirm van-hairline--left",on:{click:function(t){e.util.jsTel(e.yinsihao.secret_mobile),e.showYinsihao=!1}}},[e._v("立即呼叫")])])]),e._v(" "),a("van-popup",{staticClass:"yinsihao-popup",model:{value:e.servicePopupStatus,callback:function(t){e.servicePopupStatus=t},expression:"servicePopupStatus"}},[a("div",{staticClass:"popup-title font-500"},[e._v("\n\t\t\t订单已不能使用隐私号联系\n\t\t")]),e._v(" "),a("div",{staticClass:"popup-container padding-15-b van-hairline--bottom"},[e._v("\n\t\t\t为您联系客服\n\t\t")]),e._v(" "),a("div",{staticClass:"popup-footer"},[a("div",{staticClass:"yinsihao-cancel",on:{click:function(t){e.servicePopupStatus=!1}}},[e._v("取消")]),e._v(" "),a("div",{staticClass:"yinsihao-confirm van-hairline--left",on:{click:function(t){e.util.jsTel(e.config_mall.mobile),e.servicePopupStatus=!1}}},[e._v("继续呼叫")])])])],2)},staticRenderFns:[function(){var e=this.$createElement,t=this._self._c||e;return t("div",{staticClass:"share-inner"},[t("div",{staticClass:"icon icon-share"})])}]};var g=a("VU/8")(u,h,!1,function(e){a("9eGM")},null,null);t.default=g.exports}});
//# sourceMappingURL=201.14f6e1f144e7c90f2df8.js.map