webpackJsonp([89],{qZyX:function(t,e){},w5un:function(t,e,s){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var n={data:function(){return{id:0,logs:[],showPreLoading:!0}},components:{publicHeader:s("Cz8s").a},methods:{onLoad:function(){var t=this;this.util.request({url:"plateform/errander/order/logs",data:{id:this.id}}).then(function(e){t.showPreLoading=!1;var s=e.data.message;s.errno?t.$toast(s.message):t.logs=s.message.logs})}},created:function(){this.$route.query&&this.$route.query.id&&(this.id=this.$route.query.id)},mounted:function(){this.onLoad()}},i={render:function(){var t=this,e=t.$createElement,s=t._self._c||e;return s("div",{attrs:{id:"paotui-log"}},[s("public-header",{attrs:{title:"订单日志"}}),t._v(" "),s("div",{staticClass:"content"},[s("van-steps",{attrs:{direction:"vertical",active:t.logs.length-1,"active-color":"#4FAE52"}},t._l(t.logs,function(e,n){return s("van-step",{key:n,staticClass:"border-0px"},[s("h3",[t._v("【"+t._s(e.title)+"】操作人: "+t._s(e.role_cn))]),t._v(" "),s("p",[t._v(t._s(e.addtime_cn))])])}))],1),t._v(" "),t.showPreLoading?s("iloading"):t._e()],1)},staticRenderFns:[]};var o=s("VU/8")(n,i,!1,function(t){s("qZyX")},"data-v-1638999e",null);e.default=o.exports}});