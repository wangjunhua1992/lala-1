webpackJsonp([41],{"+PI1":function(a,t,s){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var n=s("Dd8w"),r=s.n(n),e=s("NYxO"),v=s("Cz8s"),_=s("deIj"),d={components:{publicHeader:v.a},data:function(){return{showPreLoading:!0,stat:{},detail:[]}},methods:{onLoad:function(){var a=this;Object(_.a)({vue:this,data:{detail:1,filter:a.filter?a.filter.items:{}},url:"plateform/statcenter/takeoutOrderChannel/index",success:function(t){a.detail=t.detail,a.stat=t.stat}})}},computed:r()({},Object(e.c)(["filter"])),mounted:function(){this.onLoad()}},i={render:function(){var a=this,t=a.$createElement,s=a._self._c||t;return s("div",{attrs:{id:"statcenter-current"}},[s("public-header",{attrs:{title:"订单来源详情"}}),a._v(" "),s("div",{staticClass:"content padding-15 font-14 "},[s("van-row",{staticClass:"c-gray"},[s("van-col",{staticClass:"padding-10-tb",attrs:{span:"5"}},[a._v("账期")]),a._v(" "),s("van-col",{staticClass:"padding-10-tb",attrs:{span:"3"}},[a._v("总单数")]),a._v(" "),s("van-col",{staticClass:"padding-10-tb",attrs:{span:"5"}},[a._v("饿了么")]),a._v(" "),s("van-col",{staticClass:"padding-10-tb",attrs:{span:"5"}},[a._v("美团")]),a._v(" "),s("van-col",{staticClass:"padding-10-tb",attrs:{span:"5"}},[a._v("小程序")]),a._v(" "),s("van-col",{staticClass:"padding-10-tb",attrs:{span:"5"}}),a._v(" "),s("van-col",{staticClass:"padding-10-tb",attrs:{span:"3"}}),a._v(" "),s("van-col",{staticClass:"padding-10-b",attrs:{span:"5"}},[a._v("订单/占比")]),a._v(" "),s("van-col",{staticClass:"padding-10-b",attrs:{span:"5"}},[a._v("订单/占比")]),a._v(" "),s("van-col",{staticClass:"padding-10-b",attrs:{span:"5"}},[a._v("订单/占比")]),a._v(" "),s("van-col",{staticClass:"padding-10-tb",attrs:{span:"5"}}),a._v(" "),s("van-col",{staticClass:"padding-10-tb",attrs:{span:"3"}}),a._v(" "),s("van-col",{staticClass:"padding-10-tb",attrs:{span:"5"}},[a._v("H5")]),a._v(" "),s("van-col",{staticClass:"padding-10-tb",attrs:{span:"5"}},[a._v("顾客APP")]),a._v(" "),s("van-col",{staticClass:"padding-10-tb",attrs:{span:"5"}},[a._v("后台创建")]),a._v(" "),s("van-col",{staticClass:"padding-10-tb",attrs:{span:"5"}}),a._v(" "),s("van-col",{staticClass:"padding-10-tb",attrs:{span:"3"}}),a._v(" "),s("van-col",{staticClass:"padding-10-b",attrs:{span:"5"}},[a._v("订单/占比")]),a._v(" "),s("van-col",{staticClass:"padding-10-b",attrs:{span:"5"}},[a._v("订单/占比")]),a._v(" "),s("van-col",{staticClass:"padding-10-b",attrs:{span:"5"}},[a._v("订单/占比")])],1),a._v(" "),a._e(),a._v(" "),a._l(a.detail,function(t,n){return a.detail?s("van-row",{key:n},[s("van-col",{attrs:{span:"5"}},[a._v(a._s(t.stat_day))]),a._v(" "),s("van-col",{attrs:{span:"3"}},[a._v(a._s(t.total_success_order))]),a._v(" "),s("van-col",{attrs:{span:"5"}},[a._v(a._s(t.order_eleme+"/"+t.pre_order_eleme+"%"))]),a._v(" "),s("van-col",{attrs:{span:"5"}},[a._v(a._s(t.order_meituan+"/"+t.pre_order_meituan+"%"))]),a._v(" "),s("van-col",{attrs:{span:"5"}},[a._v(a._s(t.order_wxapp+"/"+t.pre_order_wxapp+"%"))]),a._v(" "),s("van-col",{attrs:{span:"5"}}),a._v(" "),s("van-col",{attrs:{span:"3"}}),a._v(" "),s("van-col",{attrs:{span:"5"}},[a._v(a._s(t.order_wap+"/"+t.pre_order_wap+"%"))]),a._v(" "),s("van-col",{attrs:{span:"5"}},[a._v(a._s(t.order_h5app+"/"+t.pre_order_h5app+"%"))]),a._v(" "),s("van-col",{attrs:{span:"5"}},[a._v(a._s(t.order_plateformCreate+"/"+t.pre_order_plateformCreate+"%"))])],1):a._e()})],2),a._v(" "),a.showPreLoading?s("iloading"):a._e()],1)},staticRenderFns:[]};var c=s("VU/8")(d,i,!1,function(a){s("8VAi")},"data-v-6e26e28c",null);t.default=c.exports},"8VAi":function(a,t){}});