webpackJsonp([203],{GXhf:function(a,t,s){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var e=s("Gu7T"),i=s.n(e),r=s("Dd8w"),d=s.n(r),n=s("NYxO"),l={data:function(){return{query:{},preLoading:!0,list:[],available:[],dis_available:[]}},components:{PublicHeader:s("Cz8s").a},methods:d()({},Object(n.b)(["setOrderExtra","setState"]),{onLoad:function(){var a=this,t={sid:this.sid,erranderId:this.erranderId};this.erranderExtra&&this.erranderExtra.agentid&&(t.agentid=this.erranderExtra.agentid),this.util.request({url:"wmall/member/address",data:t}).then(function(t){a.preLoading=!1;var s=t.data.message;s.errno||(a.sid>0||a.erranderId>0?(a.available=[].concat(i()(a.available),i()(s.message.available)),a.dis_available=[].concat(i()(a.dis_available),i()(s.message.dis_available))):a.list=[].concat(i()(a.list),i()(s.message)))})},onSelectAddress:function(a){return!(!this.sid&&!this.erranderId)&&(a.available?"errander"==this.channel?("accept"==this.input?this.setState({type:"erranderExtra",key:"acceptaddress_id",val:a.id}):"buy"==this.input&&this.setState({type:"erranderExtra",key:"buyaddress_id",val:a.id}),this.$router.replace(this.util.getUrl({path:"/pages/paotui/diy",query:{id:this.erranderId}})),!1):(this.setOrderExtra({key:"address_id",val:a.id}),void this.$router.replace(this.util.getUrl({path:"/pages/order/create",query:this.query}))):(this.$toast("该地址不在商家配送范围内"),!1))}}),computed:d()({},Object(n.c)(["orderExtra","erranderExtra"])),created:function(){this.query=this.util.parseQuery(this.$route.query),this.query&&(this.sid=this.query.sid,this.channel=this.query.channel,this.input=this.query.input,this.erranderId=this.query.erranderId)},mounted:function(){this.onLoad()}},c={render:function(){var a=this,t=a.$createElement,s=a._self._c||t;return s("div",{attrs:{id:"address"}},[s("public-header",{attrs:{title:"我的地址"}}),a._v(" "),s("div",{staticClass:"content"},[(a.sid>0||a.erranderId>0)&&(a.available.length>0||a.dis_available.length>0)||!a.sid&&!a.erranderId&&a.list.length>0?[a.sid>0||a.erranderId>0?[s("div",{staticClass:"list"},[a.available.length>0?[s("div",{staticClass:"block-title"},[a._v("可选地址")]),a._v(" "),s("div",{staticClass:"list-container van-hairline--bottom"},a._l(a.available,function(t,e){return s("div",{staticClass:"item  flex-lr",class:{"van-hairline--bottom":e<a.available.length-1}},[s("div",{staticClass:"item-content",on:{click:function(s){return s.preventDefault(),s.stopPropagation(),a.onSelectAddress(t)}}},[s("div",{staticClass:"user"},["1"==t.tag?[s("span",{staticClass:"tag-address tag-address-yellow"},[a._v("家")])]:"2"==t.tag?[s("span",{staticClass:"tag-address tag-address-blue"},[a._v("公司")])]:"3"==t.tag?[s("span",{staticClass:"tag-address tag-address-green"},[a._v("学校")])]:a._e(),a._v(" "),s("span",[a._v(a._s(t.realname))]),a._v(" "),s("span",[a._v(a._s(t.sex))]),a._v(" "),s("span",[a._v(a._s(t.mobile))])],2),a._v(" "),s("div",{staticClass:"address"},[a._v(a._s(t.address))])]),a._v(" "),s("div",{on:{click:function(s){a.util.jsUrl("/pages/member/addressPost",a.util.extend({id:t.id},a.query),"replace")}}},[s("van-icon",{attrs:{name:"edit"}})],1)])}),0)]:a._e(),a._v(" "),a.dis_available.length>0?[s("div",{staticClass:"block-title"},[a._v("不在配送范围内或地址不完善")]),a._v(" "),s("div",{staticClass:"list-container van-hairline--bottom"},a._l(a.dis_available,function(t,e){return s("div",{staticClass:"item  flex-lr c-disabled",class:{"van-hairline--bottom":e<a.dis_available.length-1}},[s("div",{staticClass:"item-content ",on:{click:function(s){return s.preventDefault(),s.stopPropagation(),a.onSelectAddress(t)}}},[s("div",{staticClass:"user"},["1"==t.tag?[s("span",{staticClass:"tag-address tag-address-yellow"},[a._v("家")])]:"2"==t.tag?[s("span",{staticClass:"tag-address tag-address-blue"},[a._v("公司")])]:"3"==t.tag?[s("span",{staticClass:"tag-address tag-address-green"},[a._v("学校")])]:a._e(),a._v(" "),s("span",[a._v(a._s(t.realname))]),a._v(" "),s("span",[a._v(a._s(t.sex))]),a._v(" "),s("span",[a._v(a._s(t.mobile))])],2),a._v(" "),s("div",{staticClass:"address c-disabled"},[a._v(a._s(t.address))])]),a._v(" "),s("div",{on:{click:function(s){a.util.jsUrl("/pages/member/addressPost",a.util.extend({id:t.id},a.query),"replace")}}},[s("van-icon",{attrs:{name:"edit"}})],1)])}),0)]:a._e()],2)]:[s("div",{staticClass:"list"},[s("div",{staticClass:"list-container van-hairline--bottom"},a._l(a.list,function(t,e){return s("div",{staticClass:"item  flex-lr",class:{"van-hairline--bottom":e<a.list.length-1}},[s("div",{staticClass:"item-content",on:{click:function(s){return s.preventDefault(),s.stopPropagation(),a.onSelectAddress(t)}}},[s("div",{staticClass:"user"},["1"==t.tag?[s("span",{staticClass:"tag-address tag-address-yellow"},[a._v("家")])]:"2"==t.tag?[s("span",{staticClass:"tag-address tag-address-blue"},[a._v("公司")])]:"3"==t.tag?[s("span",{staticClass:"tag-address tag-address-green"},[a._v("学校")])]:a._e(),a._v(" "),s("span",[a._v(a._s(t.realname))]),a._v(" "),s("span",[a._v(a._s(t.sex))]),a._v(" "),s("span",[a._v(a._s(t.mobile))])],2),a._v(" "),s("div",{staticClass:"address"},[a._v(a._s(t.address))])]),a._v(" "),s("div",{on:{click:function(s){a.util.jsUrl("/pages/member/addressPost",a.util.extend({id:t.id},a.query))}}},[s("van-icon",{attrs:{name:"edit"}})],1)])}),0)])]]:s("div",{staticClass:"no-data"},[s("img",{attrs:{src:"static/img/store_no_con.png",alt:""}}),a._v(" "),s("p",[a._v("您还没有收货地址")])]),a._v(" "),s("div",{staticClass:"add",on:{click:function(t){a.util.jsUrl("/pages/member/addressPost",a.util.extend({id:0},a.query),"replace")}}},[s("van-cell",{staticClass:"border-1px-t",attrs:{title:"新增收货地址",icon:"add","is-link":""}})],1)],2),a._v(" "),s("transition",{attrs:{name:"loading"}},[a.preLoading?s("iloading"):a._e()],1)],1)},staticRenderFns:[]};var v=s("VU/8")(l,c,!1,function(a){s("bI1a")},null,null);t.default=v.exports},bI1a:function(a,t){}});
//# sourceMappingURL=203.af4dfbaa70b070c75ff5.js.map