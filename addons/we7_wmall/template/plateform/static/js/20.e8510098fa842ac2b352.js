webpackJsonp([20],{f3Lz:function(t,e,s){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var a=s("Cz8s"),i=s("deIj"),n={data:function(){return{records:{page:1,psize:15,loading:!1,finished:!1,empty:!1,data:[]},isRefresh:!1,showPreLoading:!0,filter:{items:{trade_type:"0"}}}},components:{publicHeader:a.a},methods:{onLoad:function(t){Object(i.b)({vue:this,force:t,url:"plateform/agent/current/list"})},onToggleStatus:function(t){t=parseInt(t),this.filter.items.trade_type!=t&&(this.filter.items.trade_type=t)},onPullDownRefresh:function(){this.onLoad(!0)}},mounted:function(){this.onLoad()},watch:{filter:{handler:function(t,e){this.onLoad(!0)},deep:!0}}},r={render:function(){var t=this,e=t.$createElement,s=t._self._c||e;return s("div",{attrs:{id:"agent-current"}},[s("public-header",{attrs:{title:"账户明细"}}),t._v(" "),s("div",{staticClass:"content"},[s("div",{staticClass:"wrap-search wrap-search-input"},[s("div",{staticClass:"tab-group flex-lr border-1px-b"},[s("div",{staticClass:"tab-item",class:{active:0==t.filter.items.trade_type},on:{click:function(e){t.onToggleStatus(0)}}},[t._v("全部")]),t._v(" "),s("div",{staticClass:"tab-item",class:{active:1==t.filter.items.trade_type},on:{click:function(e){t.onToggleStatus(1)}}},[t._v("订单入账")]),t._v(" "),s("div",{staticClass:"tab-item",class:{active:2==t.filter.items.trade_type},on:{click:function(e){t.onToggleStatus(2)}}},[t._v("申请提现")]),t._v(" "),s("div",{staticClass:"tab-item",class:{active:3==t.filter.items.trade_type},on:{click:function(e){t.onToggleStatus(3)}}},[t._v("其他变动")])]),t._v(" "),s("van-search",{attrs:{placeholder:"请输入代理名称或代理区域"},model:{value:t.filter.items.keyword,callback:function(e){t.$set(t.filter.items,"keyword",e)},expression:"filter.items.keyword"}})],1),t._v(" "),s("van-pull-refresh",{on:{refresh:function(e){t.onPullDownRefresh()}},model:{value:t.isRefresh,callback:function(e){t.isRefresh=e},expression:"isRefresh"}},[t.records.empty?s("div",{staticClass:"no-data"},[s("img",{attrs:{src:"static/img/order_no_con.png",alt:""}}),t._v(" "),s("p",[t._v("没有符合条件的数据!")])]):s("van-list",{staticClass:"current-list",attrs:{finished:t.records.finished,offset:100,"immediate-check":!1},on:{load:t.onLoad},model:{value:t.records.loading,callback:function(e){t.$set(t.records,"loading",e)},expression:"records.loading"}},[t._l(t.records.data,function(e,a){return s("div",{staticClass:"current-item margin-10-b"},[s("div",{staticClass:"current-title"},[s("div",{staticClass:"c-default font-bold font-16"},[t._v(t._s(e.title))])]),t._v(" "),s("div",{staticClass:"border-1px-t padding-10"},[s("div",{staticClass:"current-detail font-14 padding-10-b"},[1==e.trade_type?s("span",{staticClass:"font-bold c-default"},[t._v("订单入账")]):2==e.trade_type?s("span",{staticClass:"font-bold c-default"},[t._v("申请提现")]):3==e.trade_type?s("span",{staticClass:"font-bold c-default"},[t._v("其他变动")]):t._e(),t._v(" "),e.fee>0?s("span",{staticClass:"font-bold c-danger"},[t._v("+"+t._s(e.fee))]):s("span",{staticClass:"font-bold c-gray"},[t._v(t._s(e.fee))])]),t._v(" "),s("div",{staticClass:"current-detail font-14"},[s("span",{staticClass:"c-gray"},[t._v(t._s(e.addtime_cn))]),t._v(" "),s("span",{staticClass:"c-gray"},[t._v("余额￥"+t._s(e.amount))])])])])}),t._v(" "),t.records.finished?s("div",{staticClass:"loaded"},[s("div",{staticClass:"loaded-tips"},[t._v("没有更多了")])]):t._e()],2)],1)],1),t._v(" "),t.showPreLoading?s("iloading"):t._e()],1)},staticRenderFns:[]};var o=s("VU/8")(n,r,!1,function(t){s("iLfg")},null,null);e.default=o.exports},iLfg:function(t,e){}});