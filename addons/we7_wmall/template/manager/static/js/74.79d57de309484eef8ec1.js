webpackJsonp([74],{AWzI:function(t,i,a){"use strict";Object.defineProperty(i,"__esModule",{value:!0});var n=a("Cz8s"),s=a("deIj"),o={data:function(){return{categorys:[],showPreLoading:!0}},components:{publicHeader:n.a},methods:{onLoad:function(){var t=this;Object(s.a)({vue:this,url:"manage/goods/category/list",success:function(i){t.categorys=i.categorys}})}},mounted:function(){this.onLoad()}},e={render:function(){var t=this,i=t.$createElement,a=t._self._c||i;return a("div",{attrs:{id:"category-list"}},[a("public-header",{attrs:{title:"全部商品"}}),t._v(" "),a("div",{staticClass:"content"},[a("div",{staticClass:"wrap"},[t._l(t.categorys,function(i,n){return[i.child.length>0?a("div",{staticClass:"category-item padding-15-l bg-default font-14 margin-10-b"},[a("div",{staticClass:"title padding-15-r padding-10-tb van-hairline--bottom flex-lr"},[a("router-link",{attrs:{tag:"div",to:t.util.getUrl({path:"/pages/goods/index",query:{cid:i.id}})}},[a("p",[t._v(t._s(i.title))])]),t._v(" "),a("i",{staticClass:"icon icon-edit font-18 c-info",on:{click:function(a){t.util.jsUrl("/pages/category/post",{id:i.id},"replace")}}})],1),t._v(" "),a("div",{staticClass:"child-list padding-15-l"},t._l(i.child,function(n,s){return a("div",{staticClass:"child-item padding-15-r padding-10-tb van-hairline--bottom flex-lr"},[a("router-link",{attrs:{tag:"div",to:t.util.getUrl({path:"/pages/goods/index",query:{child_id:n.id,cid:i.id}})}},[a("p",[t._v(t._s(n.title))]),t._v(" "),a("p",{staticClass:"c-gray font-13 margin-5-t"},[t._v(t._s(n.goods_num?n.goods_num+"个商品":"暂无商品"))])]),t._v(" "),a("i",{staticClass:"icon icon-edit font-18 c-info",on:{click:function(i){t.util.jsUrl("/pages/category/post",{id:n.id},"replace")}}})],1)}))]):a("div",{staticClass:"category-item padding-15-l bg-default font-14 margin-10-b"},[a("div",{staticClass:"title padding-15-r padding-10-tb flex-lr"},[a("router-link",{attrs:{tag:"div",to:t.util.getUrl({path:"/pages/goods/index",query:{cid:i.id}})}},[a("p",[t._v(t._s(i.title))]),t._v(" "),a("p",{staticClass:"c-gray font-13 margin-5-t"},[t._v(t._s(i.goods_num?i.goods_num+"个商品":"暂无商品"))])]),t._v(" "),a("i",{staticClass:"icon icon-edit font-18 c-info",on:{click:function(a){t.util.jsUrl("/pages/category/post",{id:i.id},"replace")}}})],1)])]})],2),t._v(" "),a("div",{staticClass:"action-bottom van-hairline--top"},[a("div",{staticClass:"action-item van-hairline--left c-info",on:{click:function(i){t.util.jsUrl("/pages/category/post",{},"replace")}}},[a("i",{staticClass:"icon icon-roundadd font-16"}),t._v("\n\t\t\t\t新建分类\n\t\t\t")])])]),t._v(" "),t.showPreLoading?a("iloading"):t._e()],1)},staticRenderFns:[]};var c=a("VU/8")(o,e,!1,function(t){a("tuP3")},null,null);i.default=c.exports},tuP3:function(t,i){}});
//# sourceMappingURL=74.79d57de309484eef8ec1.js.map