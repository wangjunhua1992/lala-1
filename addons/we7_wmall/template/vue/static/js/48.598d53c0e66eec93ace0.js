webpackJsonp([48],{Zkyp:function(t,s,a){"use strict";Object.defineProperty(s,"__esModule",{value:!0});var i=a("Gu7T"),e=a.n(i),o=a("Cz8s"),n=a("mzkE"),r={data:function(){return{stores:{min:0,loading:!1,finished:!1,data:[]},menufooter:{},showPreLoading:!0}},components:{PublicHeader:o.a,PublicFooter:n.a},methods:{onLoad:function(){var t=this;if(this.stores.finished)return!1;this.util.request({url:"haodian/index/favorite",data:{min:this.stores.min,menufooter:1}}).then(function(s){t.showPreLoading=!1;var a=s.data.message;a.errno?t.$toast(a.message):(t.stores.data=[].concat(e()(t.stores.data),e()(a.message)),t.stores.loading=!1,t.stores.min=a.min,(a.message.length<10||!a.min)&&(t.stores.finished=!0),t.menufooter=window.menufooter)})},onToggleActivity:function(t){this.stores.data[t].activity.is_show_all=!this.stores.data[t].activity.is_show_all,this.stores.data[t].activity.items=[].concat(e()(this.stores.data[t].activity.items))}},mounted:function(){this.onLoad()}},l={render:function(){var t=this,s=t.$createElement,a=t._self._c||s;return a("div",{attrs:{id:"favorite"}},[a("public-header",{attrs:{title:"我的收藏"}}),t._v(" "),a("public-footer",{attrs:{menufooter:t.menufooter}}),t._v(" "),a("div",{staticClass:"content"},[t.stores.data.length>0?a("van-list",{attrs:{finished:t.stores.finished,offset:100,"immediate-check":!1},on:{load:t.onLoad},model:{value:t.stores.loading,callback:function(s){t.$set(t.stores,"loading",s)},expression:"stores.loading"}},[a("div",{staticClass:"diy-haodian-list"},t._l(t.stores.data,function(s,i){return a("div",{key:i,staticClass:"store-item ",class:{"van-hairline--top":i>0}},[a("router-link",{staticClass:"img-wrap",attrs:{tag:"div",to:t.util.getUrl({path:"/gohome/pages/haodian/detail",query:{sid:s.id}})}},[a("img",{attrs:{src:s.logo,alt:""}})]),t._v(" "),a("div",{staticClass:"store-main"},[a("router-link",{attrs:{tag:"div",to:t.util.getUrl({path:"/gohome/pages/haodian/detail",query:{sid:s.id}})}},[a("div",{staticClass:"store-title"},[t._v(t._s(s.title))]),t._v(" "),a("div",{staticClass:"flex-lr"},[a("div",{staticClass:"flex"},[a("van-rate",{attrs:{size:12,"disabled-color":"#ff2d4b",disabled:""},model:{value:s.haodian_score,callback:function(a){t.$set(s,"haodian_score",a)},expression:"haodianItem.haodian_score"}}),t._v(" "),a("span",{staticClass:"c-gray font-12 margin-5-l"},[t._v(t._s(s.haodian_score)+"分")])],1)]),t._v(" "),a("div",{staticClass:"c-gray font-12 margin-10-t"},[t._v("营业时间: "+t._s(s.business_hours_cn))]),t._v(" "),s.haodian_tags&&s.haodian_tags.length>0?a("ul",{staticClass:"store-tags"},t._l(s.haodian_tags,function(s,i){return a("li",{key:i,staticClass:"tag-item"},[t._v(t._s(s))])}),0):t._e()]),t._v(" "),s.activity&&s.activity.length>0?a("div",{staticClass:"discount-box"},t._l(s.activity,function(s,i){return a("div",{key:i,staticClass:"single-line"},[a("img",{staticClass:"discount-icon",attrs:{src:s.thumb_vue}}),t._v(" "),a("div",{staticClass:"discount-text"},[t._v(t._s(s.text))])])}),0):t._e()],1)],1)}),0)]):a("div",{staticClass:"no-data"},[a("img",{attrs:{src:"static/img/collect_no_bg.png",alt:""}}),t._v(" "),a("p",[t._v("您没有收藏")])])],1),t._v(" "),t.showPreLoading?a("iloading"):t._e()],1)},staticRenderFns:[]};var d=a("VU/8")(r,l,!1,function(t){a("gYrq")},null,null);s.default=d.exports},gYrq:function(t,s){}});
//# sourceMappingURL=48.598d53c0e66eec93ace0.js.map