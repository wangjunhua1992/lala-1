webpackJsonp([15],{drch:function(e,t){},gSbE:function(e,t,r){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var a=r("Cz8s"),i=r("+CBI"),l=r("deIj"),s={data:function(){return{deliveryer:{kefu_status:1,kefu_status_cn:"",extra:{kefu_busy_reply:""}},showPreLoading:!0}},methods:{onLoad:function(){var e=this;Object(l.a)({vue:e,url:"delivery/kefu/setting/index",data:{},success:function(t){e.deliveryer=e.util.extend(e.deliveryer,t.deliveryer)}})},jsToggleSwitch:function(e){var t=null;0==e.value&&(t='<div class="text-center">顾客消息开启有助于<br>提高用户下单转化率<br>如当前有会话未完成，建议您处理后再关闭<br>关闭后不再接收消息，但历史记录仍然保存</div>'),this.util.jsToggleSwitch({vue:this,key:e.keys,value:e.value,url:"delivery/kefu/setting/kefu_status",confirm:t,confirmButtonText:"关闭",data:{type:e.type,value:e.value}})}},mounted:function(){this.onLoad()},components:{PublicHeader:a.a,iswitch:i.a}},u={render:function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("div",{attrs:{id:"kefu-setting"}},[r("public-header",{attrs:{title:"顾客消息设置"}}),e._v(" "),r("div",{staticClass:"content"},[r("van-cell-group",{staticClass:"margin-10-t border-0px"},[r("van-cell",{attrs:{title:"当前客服状态",label:"此处设置为配送员的客服状态",value:e.deliveryer.kefu_status_cn,"is-link":"",to:e.util.getUrl({path:"/pages/kefu/update",query:{type:"kefu_status"}})}})],1),e._v(" "),r("van-cell-group",{staticClass:"margin-10-t border-0px"},[r("van-cell",{staticClass:"ellipsis",attrs:{title:"忙碌状态自动回复",value:e.deliveryer.extra.kefu_busy_reply?e.deliveryer.extra.kefu_busy_reply:"暂未设置","is-link":"",to:e.util.getUrl({path:"/pages/kefu/update",query:{type:"busy_reply"}})}})],1)],1),e._v(" "),e.showPreLoading?r("iloading"):e._e()],1)},staticRenderFns:[]};var n=r("VU/8")(s,u,!1,function(e){r("drch")},null,null);t.default=n.exports}});