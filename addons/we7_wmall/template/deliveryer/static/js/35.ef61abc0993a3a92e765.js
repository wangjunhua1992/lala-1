webpackJsonp([35],{FP3a:function(t,s,i){"use strict";Object.defineProperty(s,"__esModule",{value:!0});var e=i("Cz8s"),a=i("75NE"),o=i("deIj"),l=i("nZVv"),n=i("+CBI"),r={data:function(){return{filter:{items:{status:3}},isRefresh:!1,showPreLoading:!0,records:{page:1,psize:15,loading:!1,finished:!1,empty:!1,data:[]},deliveryer:{},can_collect_order:!1,config:{},confirmDialog:!1}},components:{publicHeader:e.a,countDown:a.a,iswitch:n.a},methods:{onToggleStatus:function(t){t=parseInt(t),this.filter.items.status!=t&&(this.filter.items.status=t,this.onLoad(!0))},onLoad:function(t){var s=this;Object(o.b)({vue:s,force:t,url:"delivery/order/takeout/list",recordsName:"orders",success:function(t){s.deliveryer=t.deliveryer,s.can_collect_order=t.can_collect_order,s.config=t.config}})},jsToggleSwitch:function(t){this.util.jsToggleSwitch({vue:this,key:t.keys,value:t.value,url:"delivery/member/mine/setting",data:{which:"work_status"}})},onChangeOrderStatus:function(t,s){Object(l.a)({vue:this,type:s,index:t,from:"list",order:this.records.data[t]})},onPullDownRefresh:function(){this.onLoad(!0)}},mounted:function(){this.onLoad()}},c={render:function(){var t=this,s=t.$createElement,i=t._self._c||s;return i("div",{attrs:{id:"home-index"}},[i("public-header",{attrs:{title:"首页"}},[i("div",{staticClass:"flex",attrs:{slot:"right"},slot:"right"},[i("i",{staticClass:"icon icon-people",on:{click:function(s){t.confirmDialog=!0}}})])]),t._v(" "),i("div",{staticClass:"content"},[i("div",{staticClass:"tabs flex-lr"},[i("div",{staticClass:"tabs-item",class:{active:3==t.filter.items.status},on:{click:function(s){t.onToggleStatus(3)}}},[t._m(0)]),t._v(" "),i("div",{staticClass:"tabs-item",class:{active:7==t.filter.items.status},on:{click:function(s){t.onToggleStatus(7)}}},[i("div",{staticClass:"title"},[t._v("待取货")])]),t._v(" "),i("div",{staticClass:"tabs-item",class:{active:4==t.filter.items.status},on:{click:function(s){t.onToggleStatus(4)}}},[i("div",{staticClass:"title"},[t._v("配送中")])])]),t._v(" "),i("van-pull-refresh",{on:{refresh:function(s){t.onPullDownRefresh()}},model:{value:t.isRefresh,callback:function(s){t.isRefresh=s},expression:"isRefresh"}},[t.records.empty?i("div",{staticClass:"no-data"},[i("img",{attrs:{src:"static/img/order_no_date.png",alt:""}}),t._v(" "),3==t.filter.items.status?[1==t.deliveryer.work_status?[t.deliveryer.perm_takeout>0?[t.can_collect_order?[i("p",[t._v("暂无待抢的订单")])]:[i("p",[t._v("当前调度模式不允许抢单,请等待管理员或系统派单")])]]:[i("p",[t._v("您没有外卖配送权限，请联系管理员")])]]:[i("p",[t._v("您当前处于收工状态,不能抢单")])]]:[i("p",[t._v("暂无符合条件的订单")])]],2):i("van-list",{staticClass:"order-list",attrs:{finished:t.records.finished,offset:100,"immediate-check":!1},on:{load:t.onLoad},model:{value:t.records.loading,callback:function(s){t.$set(t.records,"loading",s)},expression:"records.loading"}},t._l(t.records.data,function(s,e){return i("div",{key:s.id,staticClass:"order-item"},[i("div",{staticClass:"flex-lr padding-10-b"},[i("div",{staticClass:"flex font-14"},[i("span",{staticClass:"icon icon-time font-16 padding-5-r"}),t._v(" "),4==s.status&&3!=s.delivery_status?i("div",{staticClass:"c-danger flex"},[s.delivery_overtime>0?[t._v("\n\t\t\t\t\t\t\t\t距超时"),i("count-down",{staticClass:"c-danger",attrs:{endTime:s.delivery_overtime}})]:s.delivery_overtime_start>0?[t._v("\n\t\t\t\t\t\t\t\t已超时"),i("count-down",{staticClass:"c-danger",attrs:{startTime:s.delivery_overtime_start}})]:t._e()],2):i("div",{staticClass:"c-primary"},[t._v(t._s(s.addtime_cn)+"下单")])]),t._v(" "),i("div",{staticClass:"c-danger font-14"},[i("span",{staticClass:"font-18"},[t._v(t._s(s.plateform_deliveryer_fee))]),t._v(t._s(t.Lang.dollarSignCn)+"\n\t\t\t\t\t")])]),t._v(" "),i("van-row",{staticClass:"padding-10-b"},[i("van-col",{attrs:{span:"4"}},[i("div",{staticClass:"address-left"},[i("div",[t._v(t._s(s.store2deliveryer_distance)+"km")]),t._v(" "),i("div",{staticClass:"padding-5-t"},[t._v("购买")])])]),t._v(" "),i("van-col",{attrs:{span:"20"}},[i("div",{staticClass:"address-right"},[t._v("\n\t\t\t\t\t\t\t"+t._s(s.store.title)+"\n\t\t\t\t\t\t")])])],1),t._v(" "),i("van-row",{staticClass:"padding-10-b"},[i("van-col",{attrs:{span:"4"}},[i("div",{staticClass:"address-left"},[i("div",[t._v(t._s(s.store2user_distance)+"km")]),t._v(" "),i("div",{staticClass:"padding-5-t"},[t._v("送货")])])]),t._v(" "),i("van-col",{attrs:{span:"20"}},[i("div",{staticClass:"address-right"},[t._v("\n\t\t\t\t\t\t\t"+t._s(s.address)+"\n\t\t\t\t\t\t")])])],1),t._v(" "),i("div",{staticClass:"order-info"},[i("div",{staticClass:"itag itag-danger",staticStyle:{"background-color":"#a55ede"}},[t._v("帮买")]),t._v(" "),i("div",{staticClass:"itag"},[t._v("餐饮"),i("span",{staticClass:"padding-5-l"},[t._v("小于5kg")])])]),t._v(" "),s.note?i("div",{staticClass:"remark"},[t._v("\n\t\t\t\t\t备注："+t._s(s.note)+"\n\t\t\t\t")]):t._e(),t._v(" "),i("ul",{staticClass:"padding-15-t padding-5-b flex-lr"},[3==s.delivery_status?[i("li",{staticClass:"btn-item success",on:{click:function(s){t.onChangeOrderStatus(e,"delivery_assign")}}},[t._v("抢单")])]:t._e(),t._v(" "),4==s.delivery_status||7==s.delivery_status||8==s.delivery_status?[1==s.transfer_delivery_status?[i("li",{staticClass:"btn-item bg-danger",on:{click:function(s){t.onChangeOrderStatus(e,"direct_transfer_refuse")}}},[t._v("拒绝转单")]),t._v(" "),i("li",{staticClass:"btn-item success",on:{click:function(s){t.onChangeOrderStatus(e,"direct_transfer_agree")}}},[t._v("接受转单")])]:[4==s.delivery_status?[s.mobile?i("li",{staticClass:"btn-item bg-warning",on:{click:function(i){t.util.jsTel(s.mobile)}}},[t._v("联系顾客")]):t._e(),t._v(" "),i("li",{staticClass:"btn-item success",on:{click:function(s){t.onChangeOrderStatus(e,"delivery_success")}}},[t._v("确认送达")])]:[s.store&&s.store.telephone?i("li",{staticClass:"btn-item",on:{click:function(i){t.util.jsTel(s.store.telephone)}}},[t._v("联系商家")]):t._e(),t._v(" "),i("li",{staticClass:"btn-item success",on:{click:function(s){t.onChangeOrderStatus(e,"delivery_takegoods")}}},[t._v("确认取货")])]]]:t._e()],2)],1)}))],1),t._v(" "),i("div",{staticClass:"refresh-button"},[i("van-button",{staticClass:"font-16",attrs:{size:"normal",block:""},on:{click:function(s){t.onPullDownRefresh()}}},[i("span",{staticClass:"icon icon-refresh"}),t._v("刷新列表")])],1),t._v(" "),i("div",{staticClass:"home-tips",on:{click:function(s){t.util.$toast("同时接单量最大为三单")}}},[t._v("同时接单量受限")]),t._v(" "),i("van-popup",{attrs:{position:"left",overlay:!0},model:{value:t.confirmDialog,callback:function(s){t.confirmDialog=s},expression:"confirmDialog"}},[i("div",{staticClass:"pop-header"},[i("div",{staticClass:"nickname flex"},[t._v("你好，"+t._s(t.deliveryer.title)),i("div",{staticClass:"icon icon-right padding-5-l"})]),t._v(" "),i("div",{staticClass:"itag user-grade",staticStyle:{color:"#5d4837","border-color":"#ffca2e"}},[t._v("青铜3")])]),t._v(" "),i("div",{staticClass:"pop-list"},[i("div",{staticClass:"pop-item flex-lr"},[i("div",{staticClass:"pop-left font-16"},[t._v("上线")]),t._v(" "),i("div",{staticClass:"pop-right"},[i("iswitch",{attrs:{slot:"right-icon",value:t.deliveryer.work_status,"condition-open":"1","condition-close":"0","text-open":" ","text-close":" ",keys:"deliveryer.work_status"},on:{change:t.jsToggleSwitch},slot:"right-icon"})],1)]),t._v(" "),i("div",{staticClass:"pop-item flex-lr"},[i("router-link",{staticClass:"pop-left font-16",attrs:{tag:"div",to:"/pages/finance/current"}},[t._v("明细")])],1),t._v(" "),i("div",{staticClass:"pop-item flex-lr"},[i("div",{staticClass:"pop-left font-16"},[t._v("我的账户")]),t._v(" "),i("div",{staticClass:"pop-right"},[i("div",{staticClass:"account itag"},[t._v(t._s(t.Lang.dollarSign)+t._s(t.deliveryer.credit2))])])]),t._v(" "),i("div",{staticClass:"pop-item flex-lr hide"},[i("router-link",{staticClass:"pop-left font-16",attrs:{tag:"div",to:"/pages/finance/index"}},[t._v("资产")])],1),t._v(" "),i("div",{staticClass:"pop-item flex-lr"},[i("router-link",{staticClass:"pop-left font-16",attrs:{tag:"div",to:"/pages/comment/list"}},[t._v("评价")])],1),t._v(" "),i("div",{staticClass:"pop-item flex-lr"},[i("router-link",{staticClass:"pop-left font-16",attrs:{tag:"div",to:"/pages/statcenter/index"}},[t._v("工作统计")])],1),t._v(" "),i("div",{staticClass:"pop-item flex-lr"},[i("router-link",{staticClass:"pop-left font-16",attrs:{tag:"div",to:"/pages/member/phonic"}},[t._v("语音设置")])],1),t._v(" "),i("div",{staticClass:"pop-item flex-lr"},[i("router-link",{staticClass:"pop-left font-16",attrs:{tag:"div",to:"/pages/finance/getcashList"}},[t._v("帮助中心")])],1),t._v(" "),i("div",{staticClass:"pop-item flex-lr"},[i("router-link",{staticClass:"pop-left font-16 flex",attrs:{tag:"div",to:"/pages/member/setting"}},[t._v("\n\t\t\t\t\t\t设置\n\t\t\t\t\t\t"),i("div",{staticClass:"itag setting",staticStyle:{"background-color":"#ffca2e",color:"#5d4837"}},[t._v("填资料更安心")])])],1)]),t._v(" "),i("div",{staticClass:"icon icon-fold c-gray w-100"}),t._v(" "),i("van-row",{staticClass:"padding-15"},[i("van-col",{attrs:{span:"8"}},[i("router-link",{staticClass:"pop-bottom",attrs:{tag:"div",to:"/pages/order/takeout"}},[i("div",{staticClass:"icon icon-emojifill",staticStyle:{color:"#5d4837","font-size":"30px"}}),t._v(" "),i("div",[t._v("微笑行动")])])],1),t._v(" "),i("van-col",{attrs:{span:"8"}},[i("router-link",{staticClass:"pop-bottom",attrs:{tag:"div",to:"/pages/paotui/index"}},[i("div",{staticClass:"icon icon-community_fill_light",staticStyle:{color:"#5d4837","font-size":"30px"}}),t._v(" "),i("div",[t._v("骑手社区")])])],1)],1)],1)],1),t._v(" "),t.showPreLoading?i("iloading"):t._e()],1)},staticRenderFns:[function(){var t=this.$createElement,s=this._self._c||t;return s("div",{staticClass:"title"},[this._v("新订单 "),s("i",{staticClass:"icon icon-filter"})])}]};var d=i("VU/8")(r,c,!1,function(t){i("Y+r6")},null,null);s.default=d.exports},"Y+r6":function(t,s){}});