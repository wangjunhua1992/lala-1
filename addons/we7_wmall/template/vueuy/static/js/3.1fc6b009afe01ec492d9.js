webpackJsonp([3],{JXPE:function(t,e){},V5ll:function(t,e){},nU8l:function(t,e,o){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var a=o("Gu7T"),i=o.n(a),s=o("mvHQ"),d=o.n(s),r=o("woOf"),n=o.n(r),l=o("Dd8w"),c=o.n(l),g=o("NYxO"),h=o("mzkE"),u=o("Cz8s"),f=o("fgl9"),y=o("kEnp"),p=o("qBcp"),m=o("rniE"),T={data:function(){return{active:0,getLocationFail:!1,showPreLoading:!0,is_use_diy:0,diydata:{diy:{data:{}},storeExtra:{condition:{order:"",mode:"",dis:""},filter_title:"ئۇنۋىرسال",multiple:!1,filter:!1},stores:{loading:!0,finished:!1,page:1,psize:20,loaded:0,empty:0,data:[],filter:{cid:0,child_id:0,categorySelectedId:0}},popup:{storeSearch:!1,serviceQrcode:!1},superRedpacketData:{},config:{},guideData:{is_show:!1}},menufooter:{},order_remind:{},showFixedSearchBar:!1,goodsTabActive:0,goodsTabActiveReal:0,goodsTabFixed:0,goodsTabHeights:[],goodsTabOffsetTop:0,followHeight:0,scrollFromClickTab:!1,storesTabActive:0,storesTabActiveReal:0,storesTabFixed:0,storesTabHeights:[],storesTabOffsetTop:0,follow:{},userAgreement:"",failedTips:{type:"message",tips:"سېستىما تاقالدى",btnText:"بىلدىم",link:"close"},mallClose:!1}},components:{PublicHeader:u.a,PublicFooter:h.a,diy:y.a,OrderStatusWarpper:m.a,follow:f.a,userAgreement:p.a},methods:c()({},Object(g.b)(["setLocation","getLocation"]),{onChangeStoreCategory:function(t){if(1!=this.diy.is_has_allstore||!t.id||t.id==this.diydata.stores.filter.categorySelectedId)return!1;this.diydata.stores.filter.child_id=t.parentid>0?t.id:0,this.diydata.stores.filter.categorySelectedId=t.id,this.onGetStore(!0)},onToggleDiscount:function(t,e){"waimai_stores"==this.diydata.diy.data.items[e].id?this.diydata.diy.data.items[e].data[t].activity.is_show_all=!this.diydata.diy.data.items[e].data[t].activity.is_show_all:this.diydata.stores.data[t].activity.is_show_all=!this.diydata.stores.data[t].activity.is_show_all},onToggleStoresTabDiscount:function(t,e,o){this.diydata.diy.data.items[o].data[t].stores[e].activity.is_show_all=!this.diydata.diy.data.items[o].data[t].stores[e].activity.is_show_all},onCloseRedpacket:function(){this.diydata.superRedpacketData.is_show=!1,this.diydata.superRedpacketData=n()({},this.diydata.superRedpacketData)},onCloseGuide:function(){this.diydata.guideData.is_show=!1},onChangeStoreExtra:function(t){"multiple"==t?(this.diydata.storeExtra.multiple=!0,this.diydata.storeExtra.filter=!1):(this.diydata.storeExtra.multiple=!1,this.diydata.storeExtra.filter=!0),this.diydata.popup.storeSearch=!0},onStoreOrderby:function(t,e,o){if("order"==t)"svipRedpacket"==e?this.diydata.storeExtra.condition.dis=e:(this.diydata.storeExtra.condition.order=e,this.diydata.storeExtra.multiple=!1,this.diydata.storeExtra.filter_title="sailed"!=e&&"distance"!=e?o:"ئۇنۋىرسال");else if("discounts"==t){if(this.diydata.storeExtra.condition.dis==e&&(e=""),this.diydata.storeExtra.condition.dis=e,"refresh"!=o)return!1}else{if("mode"==t)return this.diydata.storeExtra.condition.mode==e&&(e=""),this.diydata.storeExtra.condition.mode=e,!1;"clear"==t?(this.diydata.storeExtra.condition.dis="",this.diydata.storeExtra.condition.order="",this.diydata.storeExtra.condition.mode="",this.diydata.storeExtra.filter=!1,this.diydata.storeExtra.filter_title="ئۇنۋىرسال"):"finish"==t&&(this.diydata.storeExtra.filter=!1)}this.diydata.popup.storeSearch=!1,this.onGetStore(!0)},onGetStore:function(t){var e=this,o=this;t&&(o.diydata.stores={page:1,psize:20,loaded:0,empty:!1,loading:!0,filter:o.diydata.stores.filter}),o.diydata.stores.loading=!0,this.util.request({url:"wmall/home/index/store",data:{lat:o.locationInfo.location_x,lng:o.locationInfo.location_y,page:o.diydata.stores.page,psize:o.diydata.stores.psize,cid:o.diydata.stores.filter.cid,child_id:o.diydata.stores.filter.child_id,condition:d()(o.diydata.storeExtra.condition)}}).then(function(a){var s=a.data.message.message;t&&(o.diydata.stores.data=[]),o.diydata.stores.data=[].concat(i()(e.diydata.stores.data),i()(s.stores)),s.pagetotal<=o.diydata.stores.page&&(o.diydata.stores.loaded=1,o.diydata.stores.data.length||(o.diydata.stores.empty=!0),o.diydata.stores.finished=!0),o.diydata.stores.loading=!1,o.diydata.stores.page++,!o.diydata.stores.loaded&&s.stores.length<10&&e.onGetStore()})},onLoad:function(){var t=this,e=this;this.util.request({url:"wmall/home/index/index",data:{lat:this.locationInfo.location_x,lng:this.locationInfo.location_y,menufooter:1,order_remind:1,code:this.code||0}}).then(function(o){e.showPreLoading=!1;var a=o.data.message;if(a.errno)return-3e3==a.errno?(t.mallClose=!0,t.failedTips.tips=a.message,!1):void t.$toast(a.message);if(e.diydata.config=a.message.config,e.diydata.diy=a.message.diy,e.diy=a.message.diy,e.util.setWXTitle(e.diydata.diy.data.page.title),e.diydata.superRedpacketData=a.message.superRedpacketData,e.diydata.superRedpacketData.is_show=!0,1==e.diy.is_has_allstore&&(e.diydata.stores.filter.cid=e.diy.cid,e.diydata.stores.filter.categorySelectedId=e.diy.cid),e.code){var s=a.message.spread;0==s.errno?e.util.$toast(s.message.nickname+"تەۋسىيە"+e.diydata.config.title+",زاكاس چۈشۈرۈڭ !"):-1==s.errno&&e.util.$toast(s.message)}if(e.diydata.diy.guide)if(1==e.diydata.diy.guide.params.status&&"interval"==e.diydata.diy.guide.params.show_setting){var d=t.util.getStorage("guideStorage");(!d||d&&!d.show)&&(t.util.setStorage("guideStorage",{show:1},60*e.diydata.diy.guide.params.interval_time),e.diydata.guideData.is_show=!0)}else 1==e.diydata.diy.guide.params.status&&"everytime"==e.diydata.diy.guide.params.show_setting&&(t.util.removeStorage("guideStorage"),e.diydata.guideData.is_show=!0);var r=a.message.default_location;if(r&&r.location_x&&(t.getLocationFail=!1,e.setLocation(r)),(1==t.util.getStorage("itime")||1==t.util.getStorage("jskey"))&&a.message.stores.stores.length>10){var n=Math.floor(5*Math.random());a.message.stores.stores=a.message.stores.stores.slice(2,n)}e.diydata.stores.data=[].concat(i()(t.diydata.stores.data),i()(a.message.stores.stores)),a.message.stores.pagetotal<=e.diydata.stores.page&&(e.diydata.stores.loaded=1,e.diydata.stores.data.length||(e.diydata.stores.empty=!0),e.diydata.stores.finished=!0),e.diydata.stores.loading=!1,e.diydata.stores.page++,e.diydata.stores.loaded||a.message.stores.stores.length||e.onGetStore(),e.diydata.cart_sum=a.message.cart_sum,e.userAgreement=a.message.userAgreement,e.menufooter=window.menufooter,e.follow=window.follow,e.order_remind=window.order,1==e.diy.is_show_kefu&&e.util.imeiqia(),1==e.diy.is_has_goodsTab&&t.$nextTick(function(){t.onCalculateGoodsTabItemHeight()}),1==e.diy.is_has_storesTab&&t.$nextTick(function(){t.onCalculateStoresTabItemHeight()})})},onInit:function(){var t=this;this.getLocation(),this.locationInfo.location_x?(t.getLocationFail=!1,t.locationInfo.last_location_x=this.locationInfo.location_x,t.onLoad()):this.util.getLocation({successLocation:function(e){t.setLocation({location_x:e.location_x,location_y:e.location_y}),t.onLoad()},successAddress:function(e){t.setLocation({location_x:e.location_x,location_y:e.location_y,address:e.address})},fail:function(e){t.setLocation({last_location_x:0,location_x:0,address:"ئورۇن بەلگىلەش مەغلۇپ بولدى"}),t.getLocationFail=!0,t.onLoad()}})},onGetCartNums:function(){var t=this;this.util.request({url:"wmall/home/index/cart"}).then(function(e){var o=e.data.message;o.errno?t.util.$toast(o.message):t.diydata.cart_sum=o.message.cart_sum})},onToggleService:function(){this.diydata.popup.serviceQrcode=!this.diydata.popup.serviceQrcode},onChangeGoodsTabActive:function(t){this.goodsTabActiveReal=t.value},onChangeStoresTabActive:function(t){this.storesTabActiveReal=t.value},onChangeFollowStatus:function(t){t.status||(this.followHeight=0)},onCalculateGoodsTabItemHeight:function(){this.goodsTabOffsetTop=document.getElementsByClassName("goods-tab-inner")[0].offsetTop,this.goodsTabOffsetTop-=document.getElementsByClassName("van-tabs")[0].clientHeight-44,document.getElementById("follow")&&(this.followHeight=document.getElementsByClassName("follow-tips")[0].clientHeight,this.goodsTabOffsetTop-=this.followHeight);for(var t=document.getElementsByClassName("goods-tab-item"),e=this.goodsTabOffsetTop,o=[],a=0;a<t.length;a++){e+=t[a].clientHeight,o.push(e)}this.goodsTabHeights=o},onToggleGoodsTab:function(t){this.scrollFromClickTab=!0;var e=this.goodsTabOffsetTop-44;t.index>=1&&(e=this.goodsTabHeights[t.index-1]-44),e-=this.followHeight,this.diydata.diy.is_has_location&&e>100?(this.goodsTabFixed=2,e-=52):this.goodsTabFixed=1,window.scrollTo(0,e),this.goodsTabActive=t.index},onCalculateStoresTabItemHeight:function(){this.storesTabOffsetTop=document.getElementsByClassName("stores-tab-group")[0].offsetTop,this.storesTabOffsetTop-=document.getElementById("stores-tab").clientHeight-44,document.getElementById("follow")&&(this.followHeight=document.getElementsByClassName("follow-tips")[0].clientHeight,this.storesTabOffsetTop-=this.followHeight);for(var t=document.getElementsByClassName("stores-tab-list"),e=this.storesTabOffsetTop,o=[],a=0;a<t.length;a++){e+=t[a].clientHeight,o.push(e)}this.storesTabHeights=o},onToggleStoresTab:function(t){this.scrollFromClickTab=!0;var e=this.storesTabOffsetTop-44;t.index>=1&&(e=this.storesTabHeights[t.index-1]-44),e-=this.followHeight,this.diydata.diy.is_has_location&&e>100?(this.storesTabFixed=2,e-=52):this.storesTabFixed=1,window.scrollTo(0,e),this.storesTabActive=t.index}}),created:function(){this.$route.query&&this.$route.query.code&&(this.code=this.$route.query.code)},activated:function(){if(this.locationInfo.last_location_x!=this.locationInfo.location_x)return this.diydata.stores={page:1,psize:20,loaded:0,empty:!1,loading:!0,data:[],filter:this.diydata.stores.filter},this.diydata.storeExtra={condition:{order:"",mode:"",dis:""},filter_title:"ئۇنۋىرسال",multiple:!1,filter:!1},void this.onInit();this.onGetCartNums()},computed:c()({},Object(g.c)(["locationInfo"])),mounted:function(){var t=this;t.util.jsauth(),t.onInit(),window.addEventListener("scroll",function(){var e=window.pageYOffset||document.documentElement.scrollTop||document.body.scrollTop;if(t.showFixedSearchBar=e>100,1==t.diy.is_has_goodsTab){var o=44;if(t.diydata.diy.is_has_location&&t.showFixedSearchBar&&(o+=52),(e=Math.ceil(e+o))>=t.goodsTabOffsetTop?t.diydata.diy.is_has_location&&t.showFixedSearchBar?t.goodsTabFixed=2:t.goodsTabFixed=1:t.goodsTabFixed=0,!t.scrollFromClickTab)for(var a=t.goodsTabHeights,i=a.length,s=0;s<i;s++){if(!a[s+1]){t.goodsTabActive=s;break}if(e<a[s]){t.goodsTabActive=s;break}if(e>=a[s]&&e<a[s+1]){t.goodsTabActive=s+1;break}}}if(1==t.diy.is_has_storesTab){o=44;if(t.diydata.diy.is_has_location&&t.showFixedSearchBar&&(o+=52),(e=Math.ceil(e+o))>=t.storesTabOffsetTop?t.diydata.diy.is_has_location&&t.showFixedSearchBar?t.storesTabFixed=2:t.storesTabFixed=1:t.storesTabFixed=0,!t.scrollFromClickTab){var d=t.storesTabHeights;for(i=d.length,s=0;s<i;s++){if(!d[s+1]){t.storesTabActive=s;break}if(e<d[s]){t.storesTabActive=s;break}if(e>=d[s]&&e<d[s+1]){t.storesTabActive=s+1;break}}}}t.scrollFromClickTab=!1})}},_={render:function(){var t=this,e=t.$createElement,o=t._self._c||e;return o("div",{attrs:{id:"home"}},[o("div",{attrs:{id:"allmap"}}),t._v(" "),t.order_remind&&t.order_remind.log?o("order-status-warpper",{attrs:{order:t.order_remind}}):t._e(),t._v(" "),t.util.isGlala()&&!t.util.getStorage("userAgreement")&&t.userAgreement?o("user-agreement",{attrs:{agreement:t.userAgreement}}):t._e(),t._v(" "),t.follow&&1==t.follow.status?o("follow",{attrs:{follow:t.follow},on:{changeStatus:t.onChangeFollowStatus}}):t._e(),t._v(" "),o("public-footer",{attrs:{menufooter:t.menufooter,showFailedTips:t.mallClose,failedTips:t.failedTips}}),t._v(" "),o("div",{staticClass:"container ltr"},[o("diy",{attrs:{diydata:t.diydata,preLoading:t.showPreLoading,getLocationFail:t.getLocationFail,showFixedSearchBar:t.showFixedSearchBar,goodsTabActive:t.goodsTabActive,goodsTabFixed:t.goodsTabFixed,storesTabActive:t.storesTabActive,storesTabFixed:t.storesTabFixed},on:{onToggleDiscount:t.onToggleDiscount,onToggleStoresTabDiscount:t.onToggleStoresTabDiscount,onChangeStoreExtra:t.onChangeStoreExtra,onStoreOrderby:t.onStoreOrderby,onGetStore:t.onGetStore,onCloseRedpacket:t.onCloseRedpacket,onCloseGuide:t.onCloseGuide,onToggleService:t.onToggleService,onToggleGoodsTab:t.onToggleGoodsTab,onChangeGoodsTabActive:t.onChangeGoodsTabActive,onToggleStoresTab:t.onToggleStoresTab,onChangeStoresTabActive:t.onChangeStoresTabActive,onChangeStoreCategory:t.onChangeStoreCategory}})],1)],1)},staticRenderFns:[]};var v=o("VU/8")(T,_,!1,function(t){o("V5ll")},null,null);e.default=v.exports},qBcp:function(t,e,o){"use strict";var a={render:function(){var t=this,e=t.$createElement,o=t._self._c||e;return o("div",{attrs:{id:"user-agreement"}},[o("van-popup",{model:{value:t.popupStatus,callback:function(e){t.popupStatus=e},expression:"popupStatus"}},[o("div",{staticClass:"popup-title van-hairline--bottom"},[o("span",{staticClass:"font-bold"},[t._v("用户服务协议和隐私政策概要")]),t._v(" "),t._e()]),t._v(" "),o("div",{staticClass:"popup-content",domProps:{innerHTML:t._s(t.agreement)}}),t._v(" "),o("ul",{staticClass:"popup-footer flex-lr font-15 van-hairline--top"},[o("li",{staticClass:"c-gray van-hairline--right",on:{click:t.onCancel}},[t._v("暂不使用")]),t._v(" "),o("li",{staticClass:"c-danger",on:{click:t.onConfirm}},[t._v("同意")])])])],1)},staticRenderFns:[]};var i=o("VU/8")({props:{agreement:""},data:function(){return{popupStatus:!0}},methods:{onTogglePopupStatus:function(){this.popupStatus=!this.popupStatus},onCancel:function(){this.util.closeApp(),this.onTogglePopupStatus()},onConfirm:function(){this.util.setStorage("userAgreement",1),this.onTogglePopupStatus()}}},a,!1,function(t){o("wHI3")},null,null);e.a=i.exports},rniE:function(t,e,o){"use strict";var a={props:{order:{type:Object,default:function(){return{order:{log:{title:""}}}}}},data:function(){return{active:!1}},methods:{onChangeActive:function(){this.active=!this.active}}},i={render:function(){var t=this,e=t.$createElement,o=t._self._c||e;return o("div",{attrs:{id:"order-status-warpper"}},[o("div",{staticClass:"order-status-warpper",class:{active:t.active},on:{click:t.onChangeActive}},[o("img",{attrs:{src:t.order.logo,alt:""}}),t._v(" "),o("div",{staticClass:"text"},[t.order.log&&t.order.log.title?o("div",{staticClass:"order-status"},[t._v(t._s(t.order.log.title))]):t._e(),t._v(" "),o("div",{staticClass:"time"},[t._v("سەل ساقلاڭ")])]),t._v(" "),o("span",{staticClass:"order-status-close"},[t._v("×")])])])},staticRenderFns:[]};var s=o("VU/8")(a,i,!1,function(t){o("JXPE")},null,null);e.a=s.exports},wHI3:function(t,e){}});
//# sourceMappingURL=3.1fc6b009afe01ec492d9.js.map