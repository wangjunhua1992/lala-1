webpackJsonp([26],{eK7s:function(t,e,s){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var a=s("Cz8s"),u=s("deIj"),n={data:function(){return{type:"",title:"",user:{kefu_status:"1",busy_reply:{content:""}},islegal:!1,showPreLoading:!0}},methods:{onLoad:function(){var t=this;Object(u.a)({vue:t,url:"plateform/kefu/setting/index",data:{},success:function(e){t.user=t.util.extend(t.user,e.user),t.islegal=!0}})},onSubmit:function(){var t=this;if(t.islegal){t.islegal=!1;var e={type:t.type};"kefu_status"==t.type?e.kefu_status=t.user.kefu_status:"busy_reply"==t.type&&(e.busy_reply=t.user.busy_reply.content),Object(u.c)({vue:t,url:"plateform/kefu/setting/update",data:e,success:function(e){t.util.$toast(e,t.util.getUrl({path:"/pages/plugin/kefu/setting"}),2e3,"replace")}})}}},mounted:function(){var t=this.util.parseQuery(this.$route.query);this.type=t.type,"kefu_status"==this.type?this.title="客服状态":"busy_reply"==this.type&&(this.title="忙碌状态自动回复"),this.util.setWXTitle(this.title),this.onLoad()},components:{PublicHeader:a.a}},i={render:function(){var t=this,e=t.$createElement,s=t._self._c||e;return s("div",{attrs:{id:"kefu-index"}},[s("public-header",{attrs:{title:t.title}}),t._v(" "),s("div",{staticClass:"content"},["kefu_status"==t.type?[s("div",{staticClass:"padding-10-tb padding-15-lr font-14 c-gray"},[t._v("店员客服状态")]),t._v(" "),s("van-radio-group",{model:{value:t.user.kefu_status,callback:function(e){t.$set(t.user,"kefu_status",e)},expression:"user.kefu_status"}},[s("van-cell-group",[s("van-cell",{attrs:{title:"在线",clickable:""},on:{click:function(e){t.user.kefu_status="1"}}},[s("van-radio",{attrs:{name:"1"}})],1),t._v(" "),s("van-cell",{attrs:{title:"忙碌",clickable:""},on:{click:function(e){t.user.kefu_status="2"}}},[s("van-radio",{attrs:{name:"2"}})],1),t._v(" "),s("van-cell",{attrs:{title:"离线",clickable:""},on:{click:function(e){t.user.kefu_status="3"}}},[s("van-radio",{attrs:{name:"3"}})],1)],1)],1)]:"busy_reply"==t.type?[s("div",{staticClass:"padding-10-tb padding-15-lr font-14 c-gray"},[t._v("忙碌状态自动回复")]),t._v(" "),s("van-cell-group",[s("van-field",{attrs:{type:"textarea",placeholder:"当对话为忙碌状态时, 将自动回复此内容给顾客",rows:"4"},model:{value:t.user.busy_reply.content,callback:function(e){t.$set(t.user.busy_reply,"content",e)},expression:"user.busy_reply.content"}})],1)]:t._e(),t._v(" "),s("div",{staticClass:"padding-15"},[s("van-button",{staticClass:"bg-primary",attrs:{size:"normal",disabled:!t.islegal,block:""},on:{click:t.onSubmit}},[t._v("保存修改")])],1)],2),t._v(" "),t.showPreLoading?s("iloading"):t._e()],1)},staticRenderFns:[]};var l=s("VU/8")(n,i,!1,function(t){s("nERY")},null,null);e.default=l.exports},nERY:function(t,e){}});