webpackJsonp([45],{"82Nw":function(t,s,a){"use strict";Object.defineProperty(s,"__esModule",{value:!0});var e=a("Gu7T"),i=a.n(e),n={data:function(){return{vipRedpacket:{params:{}},member:{},redpackets:[],preLoading:!0}},components:{PublicHeader:a("Cz8s").a},methods:{onLoad:function(){var t=this;t.util.request({url:"vipRedpacket/index/redpacket"}).then(function(s){t.preLoading=!1;var a=s.data.message;a.errno?t.util.$toast(a.message,t.util.getUrl({path:"/plugin/pages/vipRedpacket/index"}),1500,"replace"):(a=a.message,t.member=t.util.extend(t.member,a.member),t.vipRedpacket=t.util.extend(t.vipRedpacket,a.vipRedpacket),t.redpackets=[].concat(i()(a.redpackets)))})}},mounted:function(){this.onLoad()}},c={render:function(){var t=this,s=t.$createElement,a=t._self._c||s;return a("div",{attrs:{id:"vip-redpacket-month"}},[t.isWeixin?t._e():a("public-header",{attrs:{title:"会员红包"}}),t._v(" "),a("div",{staticClass:"content"},[t._m(0),t._v(" "),a("div",{staticClass:"card"},[a("div",{staticClass:"font-18 font-bold"},[t._v(t._s(t.vipRedpacket.params.title))]),t._v(" "),a("div",{staticClass:"font-12 margin-10-t"},[t._v(t._s(t.member.starttime_day)+"-"+t._s(t.member.endtime_day))])]),t._v(" "),a("div",{staticClass:"redpackets"},[a("div",{staticClass:"flex margin-5-l"},[a("div",{staticClass:"font-bold"},[t._v("可用红包："+t._s(t.member.redpacket_num_avaliable)+"张")]),t._v(" "),a("div",{staticClass:"c-gray font-12 margin-10-l"},[t._v("共"+t._s(t.redpackets.length)+"张")])]),t._v(" "),a("div",{staticClass:"list"},t._l(t.redpackets,function(s,e){return a("div",{key:e,staticClass:"redpacket-item",class:{used:1!=s.status}},[a("div",{staticClass:"inner"},[s.sid>0?a("div",{staticClass:"title"},[a("span",{staticClass:"logo"},[a("img",{staticClass:"img-100",attrs:{src:s.logo,alt:""}})]),t._v(" "),a("div",{staticClass:"store-title ellipsis"},[t._v(t._s(s.title))])]):a("div",{staticClass:"title"},[a("span",[t._v(t._s(s.title))])]),t._v(" "),a("div",{staticClass:"discount"},[a("span",{staticClass:"c-danger"},[t._v("\n\t\t\t\t\t\t\t\t"+t._s(t.Lang.dollarSign)),a("span",{staticClass:"font-20"},[t._v(t._s(s.discount))])]),t._v(" "),a("span",{staticClass:"margin-5-l"},[t._v("无门槛")])]),t._v(" "),1==s.status?a("router-link",{staticClass:"use",attrs:{tag:"div",to:t.util.getUrl({path:"/pages/home/index"})}},[a("span",[t._v("去使用")]),t._v(" "),a("i",{staticClass:"icon icon-right font-12"})]):a("div",{staticClass:"use"},[a("div",{staticClass:"have-used"})])],1)])}),0),t._v(" "),t._m(1)]),t._v(" "),a("router-link",{staticClass:"flex-center padding-15-tb",attrs:{to:t.util.getUrl({path:"pages/member/redPacket/index",query:{status:2}}),tag:"div"}},[a("span",{staticClass:"margin-5-r"},[t._v("查看无效券")]),t._v(" "),a("i",{staticClass:"icon icon-right font-12 c-gray"})])],1),t._v(" "),t.preLoading?a("iloading"):t._e()],1)},staticRenderFns:[function(){var t=this.$createElement,s=this._self._c||t;return s("div",{staticClass:"flex"},[s("span",{staticClass:"status icon icon-roundcheckfill"}),this._v(" "),s("span",[this._v("使用中")])])},function(){var t=this.$createElement,s=this._self._c||t;return s("div",{staticClass:"flex-center hide"},[s("span",{staticClass:"margin-5-r"},[this._v("展开全部")]),this._v(" "),s("i",{staticClass:"icon icon-unfold font-12 c-gray"})])}]};var r=a("VU/8")(n,c,!1,function(t){a("ruNg")},null,null);s.default=r.exports},ruNg:function(t,s){}});
//# sourceMappingURL=45.bb3c71f4eaba102b174d.js.map