webpackJsonp([90],{FrRT:function(t,e){},q8pq:function(t,e,s){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var a=s("Gu7T"),i=s.n(a),n=s("Cz8s"),o=s("mzkE"),r={components:{PublicHeader:n.a,PublicFooter:o.a},data:function(){return{status:0,refresh:0,active:0,now_active:0,records:{min:0,loaded:!1,loading:!1,empty:!1,data:[]},title:{1:"所有",2:"申请中",3:"提现成功",4:"已撤销"},showPreLoading:!0,menufooter:{}}},computed:{},methods:{onLoad:function(){var t=this;if(1==this.refresh&&(this.records={min:0,loaded:!1,loading:!1,empty:!1,data:[]}),this.records.loaded)return!1;this.records.loading=!0,this.util.request({url:"spread/getCash/index",data:{menufooter:1,min:this.records.min,status:this.status}}).then(function(e){var s=e.data.message;t.records.data=[].concat(i()(t.records.data),i()(s.message)),t.records.data.length||(t.records.empty=!0),t.records.loading=!1,t.records.min=s.min,s.min||(t.records.loaded=!0),t.now_active=t.active,t.refresh=0,t.showPreLoading=!1,t.menufooter=window.menufooter})},onChangeStatus:function(t,e){this.now_active!=t&&(this.refresh=1),0==t?this.status=0:1==t?this.status=2:2==t?this.status=1:3==t&&(this.status=3),this.onLoad()},onGetRecords:function(){}},mounted:function(){this.onLoad()}},d={render:function(){var t=this,e=t.$createElement,s=t._self._c||e;return s("div",{attrs:{id:"getcash-application-index"}},[s("public-header",{attrs:{title:"提现明细"}}),t._v(" "),s("public-footer",{attrs:{preLoading:t.showPreLoading,menufooter:t.menufooter}}),t._v(" "),s("div",{staticClass:"content"},[s("van-tabs",{attrs:{swipeable:""},on:{click:t.onChangeStatus},model:{value:t.active,callback:function(e){t.active=e},expression:"active"}},t._l(4,function(e,a){return s("van-tab",{key:a,attrs:{title:t.title[e]}},[t.records.empty?s("div",{staticClass:"current-list"},[s("div",{staticClass:"no-data"},[s("div",{staticClass:"bg"}),t._v(" "),s("p",[t._v("暂时没有提现明细哦～")])])]):s("van-list",{attrs:{finished:t.records.loaded,offset:100,"immediate-check":!1},on:{load:t.onLoad},model:{value:t.records.loading,callback:function(e){t.$set(t.records,"loading",e)},expression:"records.loading"}},[s("div",{staticClass:"current-list"},[s("div",{staticClass:"list-block"},[s("ul",{staticClass:"list"},t._l(t.records.data,function(e,a){return s("li",{key:a,staticClass:"item-content"},[s("router-link",{attrs:{to:""}},[s("div",{staticClass:"item-inner van-hairline--bottom"},[s("div",{staticClass:"item-title"},[s("div",{staticClass:"code"},[t._v("编号: "+t._s(e.trade_no))]),t._v(" "),s("div",{staticClass:"commission"},[t._v("申请佣金:"+t._s(e.get_fee)+"实际金额:"+t._s(e.final_fee)+",个人所得税:"+t._s(e.take_fee)+t._s(t.Lang.dollarSignCn))]),t._v(" "),s("div",{staticClass:"time"},[t._v("申请时间:"+t._s(e.addtime))])]),t._v(" "),s("div",{staticClass:"item-after"},[s("div",[t._v("+"+t._s(3==e.status?e.get_fee:e.final_fee))]),t._v(" "),1==e.status?s("div",[t._v("已成功")]):t._e(),t._v(" "),2==e.status?s("div",[t._v("申请中")]):t._e(),t._v(" "),3==e.status?s("div",[t._v("已撤销")]):t._e()])])])],1)}),0)])])])],1)}),1)],1)],1)},staticRenderFns:[]};var c=s("VU/8")(r,d,!1,function(t){s("FrRT")},null,null);e.default=c.exports}});
//# sourceMappingURL=90.ef3a665ac5b0c6232c87.js.map