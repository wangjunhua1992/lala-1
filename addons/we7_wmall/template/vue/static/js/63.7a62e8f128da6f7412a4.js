webpackJsonp([63],{"4V7w":function(t,a,e){"use strict";Object.defineProperty(a,"__esModule",{value:!0});var s,r=e("bOdI"),i=e.n(r),n=e("woOf"),d=e.n(n),o=e("Dd8w"),c=e.n(o),l=e("NYxO"),p=e("Cz8s"),u=e("q0vl"),v=e("WkcG"),g=e("/Vxk"),_=e("NPH5"),y={name:"paotuiScene",components:{PublicHeader:p.a,agreement:u.a,failedTips:v.a,load:_.a,BindMobile:g.a},data:function(){return{title:"跑腿",agreemnetShow:!1,agreementtitle:"《帮买服务协议》",showPreLoading:!0,status:{prefee:!1,deliveryTime:!1,goodsWeight:!1,tip:!1,fee:!1,redpacket:!1,address:!1,yinsihaoAgreement:!1},note:"",goodsWeight:0,tip:0,buyaddressPoint:!0,diy:{data:{}},basicPart:{params:{},tipsGroup:[]},redPackets:[],addresses:[],buyaddress:{},buyAddressType:"nearby",acceptaddress:{},order:{},tipSliderStep:1,weightSliderStep:1,submitDisabled:0,failedTips:{type:"location",tips:"获取定位失败!您可以选择手动搜索地址",btnText:"手动搜索地址",link:"/pages/home/location"},getLocationStatus:!0,bind_mobile:{},tipsInfo:{selectIndex:0,value:"",input:"",inputFocus:!1,dataFrom:"select"}}},computed:c()({},Object(l.c)(["erranderExtra","erranderSceneId"])),methods:c()({},Object(l.b)(["setState","replaceState"]),(s={onToggleYinsihao:function(t){this.setState({type:"erranderExtra",key:"yinsihao_status",val:t})},onGofeeRule:function(){this.$router.push(this.util.getUrl({path:"/pages/paotui/feeRule",query:{id:this.id}}))},onUploadImage:function(t,a,e){var s=this,r=t+"_"+a;s.util.image({obj:e,success:function(t,a){if(a.url&&a.filename){var e={url:a.url,filename:a.filename},i=s.erranderExtra.thumbs;i||(i={}),i[r]||(i[r]=[]),i[r].push(e);var n=d()(s.erranderExtra,{thumbs:i});s.replaceState({key:"erranderExtra",val:n}),s.onCalculate()}},options:{channel:"h5"}})},onDelThumb:function(t,a,e){var s=t+"_"+a,r=this.erranderExtra.thumbs;r[s].splice(e,1);var i=d()(this.erranderExtra,{thumbs:r});this.replaceState({key:"erranderExtra",val:i}),this.onCalculate()},onShowAgreement:function(){this.agreemnetShow=!0},onChangeStatus:function(t){"prefee"==t&&this.status.prefee?this.setState({type:"erranderExtra",key:"goods_price",val:0}):"goodsWeight"==t&&this.status.goodsWeight?(this.setState({type:"erranderExtra",key:"goods_weight",val:0}),this.onCalculate()):"tip"==t&&this.status.tip&&(this.setState({type:"erranderExtra",key:"delivery_tips",val:0}),this.onCalculate()),this.status[t]=!this.status[t]},onBlur:function(){window.scroll(0,0)},onConfirmPrefee:function(){this.setState({type:"erranderExtra",key:"goods_price",val:this.erranderExtra.goods_price}),this.status.prefee=!1,this.onCalculate()},onToggleBuyAddressType:function(t){this.buyAddressType=t,this.setState({type:"erranderExtra",key:"buyAddressType",val:t}),"nearby"==t&&this.setState({type:"erranderExtra",key:"buyaddress",val:{}}),this.onCalculate()},onSelectDay:function(t){this.setState({type:"erranderExtra",key:"delivery_day",val:t}),this.order=d()({},this.order)},onSelectTime:function(t){this.setState({type:"erranderExtra",key:"delivery_time",val:t}),this.erranderExtra.delivery_time=t,this.status.deliveryTime=!1,this.onCalculate()},onChangeWeight:function(t){var a=parseFloat(((100-this.diy.data.fees.weight_data.basic)*t/100).toFixed())+parseFloat(this.diy.data.fees.weight_data.basic);100==t&&(a=100),this.setState({type:"erranderExtra",key:"goods_weight",val:a}),this.onCalculate()},onChangeTip:function(){var t=0;t="input"==this.tipsInfo.dataFrom?parseFloat(this.tipsInfo.input):parseFloat(this.basicPart.tipsGroup[this.tipsInfo.selectIndex]);var a=this.basicPart.params.minfee,e=this.basicPart.params.maxfee;if(isNaN(t)||t<a||t>e)return this.util.$toast("小费最低"+a+this.Lang.dollarSignCn+", 最高"+e+this.Lang.dollarSignCn),!1;this.setState({type:"erranderExtra",key:"delivery_tips",val:t}),this.onCalculate(),this.status.tip=!1},onSelectTips:function(t,a){this.tipsInfo.inputFocus=!1,this.tipsInfo.dataFrom="select",this.tipsInfo.value=t,this.tipsInfo.selectIndex=a},onTipsInputFocus:function(){this.tipsInfo.inputFocus=!0,this.tipsInfo.dataFrom="input",this.tipsInfo.value=this.basicPart.tipsGroup[0],this.tipsInfo.selectIndex=0}},i()(s,"onBlur",function(){window.scroll(0,0)}),i()(s,"onFocus",function(){setTimeout(function(){document.getElementsByClassName("popup-tip")[0].scrollIntoView()},700)}),i()(s,"onSelectRedpacket",function(t){t==this.erranderExtra.redpacket_id&&(t=0),this.setState({type:"erranderExtra",key:"redpacket_id",val:t}),this.status.redpacket=!1,this.onCalculate()}),i()(s,"onGetExtraFee",function(t,a){if(this.erranderExtra.extra_fee&&0!=this.erranderExtra.extra_fee.length||(this.erranderExtra.extra_fee={}),this.erranderExtra.extra_fee[a]){for(var e in this.erranderExtra.extra_fee[a])if(this.erranderExtra.extra_fee[a][e]==t)return delete this.erranderExtra.extra_fee.current,delete this.erranderExtra.extra_fee[a][e],this.onCalculate(),!1}else this.erranderExtra.extra_fee[a]={};this.erranderExtra.extra_fee[a][t]=t,this.erranderExtra.extra_fee.current={pindex:a,cindex:t},this.onCalculate()}),i()(s,"onGetPartData",function(t){var a=t.type,e=t.info,s=e.name,r=e.value,i=e.cindex;if(this.erranderExtra.partData||(this.erranderExtra.partData={}),"text"==a)this.erranderExtra.partData[s]=this.$refs[s][0].$refs.input.value;else if("oneChoice"==a){if(this.erranderExtra.partData[s]&&this.erranderExtra.partData[s]==r)return this.erranderExtra.partData[s]="",this.diy.data.items=d()({},this.diy.data.items),this.replaceState({key:"erranderExtra",val:this.erranderExtra}),!1;this.erranderExtra.partData[s]=r,this.diy.data.items=d()({},this.diy.data.items)}else if("multipleChoices"==a){if(this.erranderExtra.partData[s]){for(var n in this.erranderExtra.partData[s])if(this.erranderExtra.partData[s][n]==r)return delete this.erranderExtra.partData[s][n],this.diy.data.items=d()({},this.diy.data.items),this.replaceState({key:"erranderExtra",val:this.erranderExtra}),!1}else this.erranderExtra.partData[s]={};this.erranderExtra.partData[s][i]=r,this.diy.data.items=d()({},this.diy.data.items),this.replaceState({key:"erranderExtra",val:this.erranderExtra})}this.replaceState({key:"erranderExtra",val:this.erranderExtra})}),i()(s,"onCalculate",function(){var t=this;this.status.redpacket=!1;var a={is_calculate:1,extra:this.erranderExtra,id:this.id};this.util.request({url:"errander/diy/index",data:a}).then(function(a){var e=a.data.message;if(0!=e.errno)return t.util.$toast(e.message,"",1e3),-1e3==e.errno&&(delete t.erranderExtra.extra_fee[t.erranderExtra.extra_fee.current.pindex][t.erranderExtra.extra_fee.current.cindex],t.util.$toast(e.message,"",1e3),t.replaceState({key:"erranderExtra",val:t.erranderExtra}),!1);(e=e.message).buyaddress&&e.buyaddress.errno&&t.util.$toast(e.buyaddress.message,"",2e3);var s=d()(t.erranderExtra,i()({delivery_nowtime:e.order.delivery_nowtime,acceptaddress_id:e.acceptaddress_id,buyaddress_id:e.buyaddress_id,buyaddress:e.buyaddress&&!e.buyaddress.errno?e.buyaddress:{},extra_fee:e.order.extra_fee,redpacket_id:e.order.redpacket_id,goods_price:e.order.goods_price,delivery_day:e.order.delivery_day,delivery_time:e.order.delivery_time,delivery_tips:e.order.delivery_tips},"buyaddress",e.buyaddress));t.basicPart=e.basic,t.diy.data.items=d()({},t.diy.data.items),t.replaceState({key:"erranderExtra",val:s}),t.redPackets=e.redPackets,t.buyaddress=e.buyaddress,t.acceptaddress=e.acceptaddress,t.order=e.order})}),i()(s,"onSubmit",function(){var t=this;if(1==this.submitDisabled)return!1;if(!this.erranderExtra.note)return this.util.$toast("请填写物品信息","",1500),!1;if("buy"!=this.diy.data.page.scene){if(!this.buyaddress)return this.util.$toast("请选择取货地址","",1500),!1;if(!this.util.isValidMobile(this.buyaddress.mobile))return this.$toast("取货联系人手机号格式不正确"),!1}return this.acceptaddress?this.util.isValidMobile(this.acceptaddress.mobile)?1!=this.diy.data.fees.weight_status||this.erranderExtra.goods_weight?(this.submitDisabled=1,void this.util.request({url:"errander/orderdiy/create",data:{id:this.id,extra:this.erranderExtra}}).then(function(a){var e=a.data.message;if(e.errno)return t.submitDisabled=0,t.util.$toast(e.message,"",1e3),!1;var s=e.message;return t.$router.replace(t.util.getUrl({path:"/pages/public/pay?order_id="+s+"&order_type=errander"})),t.replaceState({key:"erranderExtra",val:{partData:{},thumbs:{}}}),!1})):(this.util.$toast("请选择物品重量","",1500),!1):(this.$toast("收货联系人手机号格式不正确"),!1):(this.util.$toast("请选择收货地址",""),!1)}),i()(s,"onLoad",function(){var t=this;this.id!=this.erranderSceneId&&(this.setState({type:"erranderExtra",key:"delivery_tips",val:0}),this.replaceState({key:"erranderSceneId",val:this.id})),this.erranderExtra.notes&&!this.erranderExtra.note&&(this.erranderExtra.note=this.erranderExtra.notes[this.id]),this.util.request({url:"errander/diy/index",data:{id:this.id,extra:this.erranderExtra,forceLocation:1}}).then(function(a){t.showPreLoading=!1;var e=a.data.message;if(0!=e.errno)return t.util.$toast(e.message,"",1e3),!1;(e=e.message).buyaddress&&e.buyaddress.errno&&t.util.$toast(e.buyaddress.message,"",1e3),"nearby"!=t.erranderExtra.buyAddressType&&"store"!=t.erranderExtra.buyAddressType||(t.buyAddressType=t.erranderExtra.buyAddressType);var s=d()(t.erranderExtra,{delivery_day:e.order.delivery_day,delivery_time:e.order.delivery_time,delivery_nowtime:e.order.delivery_nowtime,buyaddress_id:e.buyaddress_id,buyaddress:e.buyaddress&&!e.buyaddress.errno?e.buyaddress:{},acceptaddress_id:e.acceptaddress_id,delivery_tips:e.order.delivery_tips,extra_fee:e.order.extra_fee||{},goods_weight:e.order.goods_weight,goods_price:e.order.goods_price,note:e.order.note,yinsihao_status:e.order.yinsihao_status});t.replaceState({key:"erranderExtra",val:s}),t.redPackets=e.redPackets,t.buyaddress=e.buyaddress,t.acceptaddress=e.acceptaddress,t.diy=e.diy,t.basicPart=t.util.extend(t.basicPart,e.basic),t.order=e.order,t.erranderExtra&&t.erranderExtra.note&&(t.note=t.erranderExtra.note),t.erranderExtra.extra_fee||(t.erranderExtra.extra_fee={}),t.setState({type:"erranderExtra",key:"agentid",val:e.diy.agentid}),t.title="跑腿下单",e.diy.data.page.title&&(t.title=e.diy.data.page.title),t.weightSliderStep=parseInt((100/(100-t.diy.data.fees.weight_data.basic)).toFixed()),t.tipSliderStep=parseInt((100/(t.basicPart.params.maxfee-t.basicPart.params.minfee)).toFixed()),t.util.setWXTitle(t.title),window.bind_mobile&&(t.bind_mobile=window.bind_mobile)}).catch(function(a){"fail"==window.forceGetLocationStatus&&(t.showPreLoading=!1,t.getLocationStatus=!1)})}),i()(s,"onAddLabel",function(t){this.note=this.note+t+" "}),s)),watch:{note:function(){this.setState({type:"erranderExtra",key:"note",val:this.note});var t=this.id,a=this.erranderExtra.notes;a||(a={}),a[t]=this.note,this.setState({type:"erranderExtra",key:"notes",val:a})}},created:function(){this.query=this.$route.query,this.query&&(this.id=this.query.id)},mounted:function(){this.onLoad(),this.util.icloudapi()}},f={render:function(){var t=this,a=t.$createElement,e=t._self._c||a;return e("div",{attrs:{id:"paotui-scene"}},[e("public-header",{attrs:{title:t.title}}),t._v(" "),e("div",{staticClass:"content"},[t._l(t.diy.data.items,function(a,s){return["banner"==a.id?e("div",{staticClass:"diy-banner"},t._l(a.data,function(a,s){return e("div",{key:s,on:{click:function(e){return t.util.jsUrl(a.linkurl)}}},[e("img",{attrs:{src:a.imgurl}})])}),0):"picture"==a.id?e("div",{staticClass:"diy-picture"},[e("van-swipe",{attrs:{autoplay:3e3,"indicator-color":"#ff2d4b"}},t._l(a.data,function(a,s){return e("van-swipe-item",{key:s},[e("div",{on:{click:function(e){return t.util.jsUrl(a.linkurl)}}},[e("img",{attrs:{src:a.imgurl,alt:""}})])])}),1)],1):"line"==a.id?e("div",{staticClass:"diy-line",style:{background:a.style.background,padding:a.style.padding+"px 0px"}},[e("div",{staticClass:"line"})]):"blank"==a.id?e("div",{staticClass:"diy-blank",style:{height:a.style.height+"px",background:a.style.background}}):"uploadImg"==a.id?e("div",{staticClass:"diy-uploadImg",style:{"margin-top":a.style.marginTop+"px"}},t._l(a.data,function(a,r){return e("div",{staticClass:"uploadImg-item"},[e("div",{staticClass:"uploadImg-title"},[t._v(t._s(a.title))]),t._v(" "),t.erranderExtra.thumbs&&t.erranderExtra.thumbs[s+"_"+r]&&t.erranderExtra.thumbs[s+"_"+r].length>0?[e("div",{staticClass:"img-list"},[t._l(t.erranderExtra.thumbs[s+"_"+r],function(a,i){return e("div",{staticClass:"img-group"},[e("div",{staticClass:"upload-img"},[e("img",{attrs:{src:a.url,alt:""}}),t._v(" "),e("span",{staticClass:"del-img",style:{color:t.diy.data.page&&t.diy.data.page.activecolor?t.diy.data.page.activecolor:"#fff","background-color":t.diy.data.page&&t.diy.data.page.activebackground?t.diy.data.page.activebackground:"#ff2d4b"},on:{click:function(a){return t.onDelThumb(s,r,i)}}},[e("i",{staticClass:"icon icon-close"})])])])}),t._v(" "),t.erranderExtra.thumbs[s+"_"+r].length<3?e("div",{staticClass:"img-group"},[e("div",{staticClass:"upload-img"},[e("input",{staticClass:"weui-uploader__input",attrs:{type:"file",multiple:"multiple",accept:"image/*"},on:{change:function(a){return t.onUploadImage(s,r,a)}}}),t._v(" "),e("img",{attrs:{src:"static/img/add_pic.png",alt:""}})])]):t._e()],2)]:[e("div",{staticClass:"uploadImg-right"},[e("div",{staticClass:"uploadImg-tips"},[e("input",{staticClass:"weui-uploader__input",attrs:{type:"file",multiple:"multiple",accept:"image/*"},on:{change:function(a){return t.onUploadImage(s,r,a)}}}),t._v("\n\t\t\t\t\t\t\t\t"+t._s(a.placeholder)+"\n\t\t\t\t\t\t\t")]),t._v(" "),e("div",{staticClass:"icon icon-right"})])]],2)}),0):"basic"==a.id?[e("van-cell-group",{staticClass:"diy-scene-shopinfo margin-10-b"},[e("van-field",{staticClass:"border-0px",attrs:{type:"textarea",placeholder:a.params.placeholder,rows:"4"},model:{value:t.note,callback:function(a){t.note=a},expression:"note"}}),t._v(" "),e("ul",{staticClass:"info-tags"},t._l(a.data,function(a,s){return e("li",{key:s,staticClass:"tag-item",on:{click:function(e){return t.onAddLabel(a.tags)}}},[t._v(t._s(a.tags))])}),0),t._v(" "),1==a.params.estimate?e("van-cell",{staticClass:"van-hairline--top"},[e("div",{staticClass:"flex",attrs:{slot:"title"},slot:"title"},[e("img",{staticClass:"amount-icon",attrs:{src:"static/img/amount_icon.png",alt:""}}),t._v(" "),e("span",{staticClass:"font-12"},[t._v("骑手垫付商品费，收货后与配送员结清")])]),t._v(" "),e("div",{staticClass:"flex",attrs:{slot:"right-icon"},on:{click:function(a){return t.onChangeStatus("prefee")}},slot:"right-icon"},[e("span",{staticClass:"font-12",class:{"c-disabled":!t.erranderExtra.goods_price}},[t._v(t._s(t.erranderExtra.goods_price>0?"预估 "+t.Lang.dollarSign+t.erranderExtra.goods_price:"预估商品费"))]),t._v(" "),e("span",{staticClass:"icon icon-right c-disabled font-12"})])]):t._e()],1),t._v(" "),e("van-cell-group",{staticClass:"diy-scene-address margin-10-b"},["buy"==a.params.scene?[0!=a.params.nearbuy&&a.params.nearbuy?[e("van-cell",{attrs:{to:t.util.getUrl({path:"/pages/paotui/location"})}},[e("div",{staticClass:"flex",attrs:{slot:"title"},slot:"title"},[e("div",{staticClass:"margin-15-r"},[t._v(t._s(a.params.buytitle))]),t._v(" "),t.erranderExtra.buyaddress&&t.erranderExtra.buyaddress.location_x?e("span",[t._v(t._s(t.erranderExtra.buyaddress.address))]):e("span",{staticClass:"c-disabled"},[t._v(t._s(a.params.buytype1placehode))])]),t._v(" "),e("div",{staticClass:"flex",attrs:{slot:"right-icon"},slot:"right-icon"},[e("span",{staticClass:"icon icon-right c-disabled font-12"})])])]:[e("van-cell",{staticClass:"border-0px"},[e("div",{staticClass:"flex",attrs:{slot:"title"},slot:"title"},[e("div",{staticClass:"margin-15-r"},[t._v(t._s(a.params.buytitle))]),t._v(" "),e("div",{staticClass:"address-type flex"},[e("div",{staticClass:"address-type-item font-12 ",class:{active:"store"==t.buyAddressType},style:{"border-color":"store"==t.buyAddressType&&t.diy.data.page&&t.diy.data.page.activebackground?t.diy.data.page.activebackground:"#ccc"},on:{click:function(a){return t.onToggleBuyAddressType("store")}}},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t"+t._s(a.params.buytype1title)+"\n\t\t\t\t\t\t\t\t\t\t")]),t._v(" "),e("div",{staticClass:"address-type-item font-12",class:{active:"nearby"==t.buyAddressType},style:{"border-color":"nearby"==t.buyAddressType&&t.diy.data.page&&t.diy.data.page.activebackground?t.diy.data.page.activebackground:"#ccc"},on:{click:function(a){return t.onToggleBuyAddressType("nearby")}}},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t"+t._s(a.params.buytype2title)+"\n\t\t\t\t\t\t\t\t\t\t")])])])]),t._v(" "),"store"==t.erranderExtra.buyAddressType?e("van-cell",{attrs:{to:t.util.getUrl({path:"/pages/paotui/location"})}},[e("div",{staticClass:"flex",attrs:{slot:"title"},slot:"title"},[e("div",{staticClass:"margin-15-r opacity-1"},[t._v("111")]),t._v(" "),t.buyaddress&&t.buyaddress.location_x?e("span",[t._v(t._s(t.buyaddress.address))]):e("span",{staticClass:"c-disabled"},[t._v(t._s(a.params.buytype1placehode))])]),t._v(" "),e("div",{staticClass:"flex",attrs:{slot:"right-icon"},slot:"right-icon"},[e("span",{staticClass:"icon icon-right c-disabled font-12"})])]):t._e()],t._v(" "),e("van-cell",{on:{click:function(a){return t.util.jsUrl("/pages/member/address",{channel:"errander",input:"accept",erranderId:t.id},"replace")}}},[e("div",{staticClass:"flex ",attrs:{slot:"title"},slot:"title"},[e("div",{staticClass:"margin-15-r"},[t._v(t._s(a.params.accepttitle))]),t._v(" "),t.erranderExtra.acceptaddress_id>0?e("span",[t._v("\n\t\t\t\t\t\t\t\t\t"+t._s(t.acceptaddress.address)+"-"+t._s(t.acceptaddress.number)+"\n\t\t\t\t\t\t\t\t\t"),e("div",{staticClass:"c-disabled"},[t._v(t._s(t.acceptaddress.realname)+" "+t._s(t.acceptaddress.sex)+" "+t._s(t.acceptaddress.mobile))])]):e("span",{staticClass:"c-disabled"},[t._v(t._s(a.params.acceptplacehode))])]),t._v(" "),e("div",{staticClass:"flex",attrs:{slot:"right-icon"},slot:"right-icon"},[e("span",{staticClass:"icon icon-right c-disabled font-12"})])])]:"delivery"==a.params.scene?[e("van-cell",{on:{click:function(a){return t.util.jsUrl("/pages/member/address",{channel:"errander",input:"buy",erranderId:t.id},"replace")}}},[e("div",{staticClass:"flex height-50",attrs:{slot:"title"},slot:"title"},[e("div",{staticClass:"address-icon bg-primary"}),t._v(" "),t.erranderExtra.buyaddress_id>0?e("div",[e("div",[t._v(t._s(t.buyaddress.address)+"-"+t._s(t.buyaddress.number))]),t._v(" "),e("div",{staticClass:"c-disabled"},[t._v(t._s(t.buyaddress.realname)+" "+t._s(t.buyaddress.mobile))])]):e("span",{staticClass:"c-disabled"},[t._v(t._s(a.params.buytype1placehode))])]),t._v(" "),e("div",{staticClass:"flex",attrs:{slot:"right-icon"},slot:"right-icon"},[e("span",{staticClass:"icon icon-right c-disabled font-12"})])]),t._v(" "),e("van-cell",{on:{click:function(a){return t.util.jsUrl("/pages/member/address",{channel:"errander",input:"accept",erranderId:t.id},"replace")}}},[e("div",{staticClass:"flex height-50",attrs:{slot:"title"},slot:"title"},[e("div",{staticClass:"address-icon bg-danger"}),t._v(" "),t.erranderExtra.acceptaddress_id>0?e("div",[t._v("\n\t\t\t\t\t\t\t\t\t"+t._s(t.acceptaddress.address)+" "+t._s(t.acceptaddress.number)+"\n\t\t\t\t\t\t\t\t\t"),e("div",{staticClass:"c-disabled"},[t._v(t._s(t.acceptaddress.realname)+" "+t._s(t.acceptaddress.mobile))])]):e("span",{staticClass:"c-disabled"},[t._v(t._s(a.params.acceptplacehode))])]),t._v(" "),e("div",{staticClass:"flex",attrs:{slot:"right-icon"},slot:"right-icon"},[e("span",{staticClass:"icon icon-right c-disabled font-12"})])])]:t._e(),t._v(" "),e("van-row",[e("van-col",{staticClass:"van-hairline--right",attrs:{span:"12"}},[e("van-cell",{on:{click:function(a){return t.onChangeStatus("deliveryTime")}}},[e("div",{attrs:{slot:"title"},slot:"title"},[t._v(t._s(t.erranderExtra.delivery_day)+" "+t._s(t.erranderExtra.delivery_time==t.order.delivery_nowtime?"立即送出(约"+t.order.delivery_nowtime+")":t.erranderExtra.delivery_time))]),t._v(" "),e("div",{staticClass:"icon icon-right c-disabled font-12",attrs:{slot:"right-icon"},slot:"right-icon"})])],1),t._v(" "),e("van-col",{attrs:{span:"12"}},[e("van-cell",{on:{click:function(a){return t.onChangeStatus("goodsWeight")}}},[t.erranderExtra.goods_weight>t.diy.data.fees.weight_data.basic?e("div",{attrs:{slot:"title"},slot:"title"},[t._v("物品重量："+t._s(t.erranderExtra.goods_weight)+"公斤")]):e("div",{attrs:{slot:"title"},slot:"title"},[t._v("物品重量："+t._s(t.diy.data.fees.weight_data.basic)+"公斤内")]),t._v(" "),e("div",{staticClass:"icon icon-right c-disabled font-12",attrs:{slot:"right-icon"},slot:"right-icon"})])],1)],1)],2)]:"text"==a.id?[e("van-cell-group",{staticClass:"diy-scene-text margin-10-b",style:{"margin-top":a.style.marginTop+"px"}},[t._l(a.data,function(a,r){return[e("van-field",{ref:s+"_"+r,refInFor:!0,attrs:{value:t.erranderExtra.partData[s+"_"+r],label:a.title,placeholder:a.placeholder},on:{blur:function(a){return t.onGetPartData({type:"text",info:{name:s+"_"+r}})}}})]})],2)]:"multipleChoices"==a.id?t._l(a.data,function(r,i){return e("div",{staticClass:"diy-scene-choose flex-lr margin-10-b",style:{"margin-top":a.style.marginTop+"px"}},[e("div",{staticClass:"choose-title ellipsis font-14"},[t._v(t._s(r.title))]),t._v(" "),e("div",{staticClass:"choose-options"},t._l(r.options,function(a,r){return e("span",{key:r,staticClass:"option-item font-14",class:{active:t.erranderExtra.partData[s+"_"+i]&&t.erranderExtra.partData[s+"_"+i][r]==a.name},style:{"background-color":t.erranderExtra.partData[s+"_"+i]&&t.erranderExtra.partData[s+"_"+i][r]==a.name&&t.diy.data.page&&t.diy.data.page.activebackground?t.diy.data.page.activebackground:"#fff","border-color":t.erranderExtra.partData[s+"_"+i]&&t.erranderExtra.partData[s+"_"+i][r]==a.name&&t.diy.data.page&&t.diy.data.page.activebackground?t.diy.data.page.activebackground:"#999",color:t.erranderExtra.partData[s+"_"+i]&&t.erranderExtra.partData[s+"_"+i][r]==a.name&&t.diy.data.page&&t.diy.data.page.activecolor?t.diy.data.page.activecolor:"#333"},on:{click:function(e){return t.onGetPartData({type:"multipleChoices",info:{name:s+"_"+i,value:a.name,cindex:r}})}}},[t._v("\n\t\t\t\t\t\t\t"+t._s(a.name)+"\n\t\t\t\t\t\t")])}),0)])}):"oneChoice"==a.id?t._l(a.data,function(r,i){return e("div",{staticClass:"diy-scene-choose flex-lr margin-10-b",style:{"margin-top":a.style.marginTop+"px"}},[e("div",{staticClass:"choose-title ellipsis font-14"},[t._v(t._s(r.title))]),t._v(" "),e("div",{staticClass:"choose-options"},t._l(r.options,function(a,r){return e("span",{key:r,staticClass:"option-item font-14",class:{active:t.erranderExtra.partData[s+"_"+i]&&t.erranderExtra.partData[s+"_"+i]==a.name},style:{"background-color":t.erranderExtra.partData[s+"_"+i]&&t.erranderExtra.partData[s+"_"+i]==a.name&&t.diy.data.page&&t.diy.data.page.activebackground?t.diy.data.page.activebackground:"#fff","border-color":t.erranderExtra.partData[s+"_"+i]&&t.erranderExtra.partData[s+"_"+i]==a.name&&t.diy.data.page&&t.diy.data.page.activebackground?t.diy.data.page.activebackground:"#999",color:t.erranderExtra.partData[s+"_"+i]&&t.erranderExtra.partData[s+"_"+i]==a.name&&t.diy.data.page&&t.diy.data.page.activecolor?t.diy.data.page.activecolor:"#333"},on:{click:function(e){return t.onGetPartData({type:"oneChoice",info:{name:s+"_"+i,value:a.name,cindex:r}})}}},[t._v("\n\t\t\t\t\t\t\t"+t._s(a.name)+"\n\t\t\t\t\t\t")])}),0)])}):t._e()]}),t._v(" "),t.basicPart.params.yinsihao&&1==t.basicPart.params.yinsihao.status?e("div",{staticClass:"diy-yinsihao bg-default margin-10-tb padding-10-tb padding-15-lr font-14"},[e("div",{staticClass:"flex-lr"},[e("div",{staticClass:"flex"},[e("i",{staticClass:"icon icon-lock font-16"}),t._v(" "),e("div",{staticClass:"padding-5-lr"},[t._v("号码保护")]),t._v(" "),e("i",{staticClass:"icon icon-question",on:{click:function(a){return t.onChangeStatus("yinsihaoAgreement")}}})]),t._v(" "),e("div",{staticClass:"flex"},[e("van-switch",{attrs:{size:"22px","active-color":t.diy.data.page.activebackground?t.diy.data.page.activebackground:"#ff2d4b","inactive-color":"#eee"},on:{change:t.onToggleYinsihao},model:{value:t.erranderExtra.yinsihao_status,callback:function(a){t.$set(t.erranderExtra,"yinsihao_status",a)},expression:"erranderExtra.yinsihao_status"}})],1)]),t._v(" "),e("div",{staticClass:"font-12 c-gray margin-10-t"},[t._v("对骑手隐藏您的真实手机号, 保护您的隐私")])]):t._e(),t._v(" "),t.diy.data.fees&&t.diy.data.fees.extra_fee?t._l(t.diy.data.fees.extra_fee,function(a,s){return 1==a.status?e("div",{key:s,staticClass:"diy-scene-choose flex-lr margin-10-b"},[e("div",{staticClass:"choose-title ellipsis font-14"},[t._v(t._s(a.title))]),t._v(" "),e("div",{staticClass:"choose-options"},t._l(a.data,function(a,r){return e("span",{key:r,staticClass:"option-item font-14",class:{active:t.erranderExtra.extra_fee[s]&&t.erranderExtra.extra_fee[s][r]==r},style:{"background-color":t.erranderExtra.extra_fee[s]&&t.erranderExtra.extra_fee[s][r]==r&&t.diy.data.page&&t.diy.data.page.activebackground?t.diy.data.page.activebackground:"#fff","border-color":t.erranderExtra.extra_fee[s]&&t.erranderExtra.extra_fee[s][r]==r&&t.diy.data.page&&t.diy.data.page.activebackground?t.diy.data.page.activebackground:"#999",color:t.erranderExtra.extra_fee[s]&&t.erranderExtra.extra_fee[s][r]==r&&t.diy.data.page&&t.diy.data.page.activecolor?t.diy.data.page.activecolor:"#333"},on:{click:function(a){return t.onGetExtraFee(r,s)}}},[t._v("\n\t\t\t\t\t\t"+t._s(a.fee_name)+"-"+t._s(t.Lang.dollarSign)+t._s(a.fee)+"\n\t\t\t\t\t")])}),0)]):t._e()}):t._e(),t._v(" "),e("van-cell-group",{staticClass:"diy-scene-extra-fee"},[e("van-cell",[e("div",{attrs:{slot:"title"},slot:"title"},[t._v(t._s(t.basicPart.params.redpacketname))]),t._v(" "),e("div",{attrs:{slot:"right-icon"},slot:"right-icon"},[t.order.redpacket?e("span",{staticClass:"font-12",style:{color:t.diy.data.page&&t.diy.data.page.activebackground?t.diy.data.page.activebackground:"#ff2d4b"},on:{click:function(a){return t.onChangeStatus("redpacket")}}},[t._v("\n\t\t\t\t\t\t-"+t._s(t.Lang.dollarSign)+t._s(t.order.redpacket.discount)+"\n\t\t\t\t\t")]):e("span",{staticClass:"font-12",style:{color:t.redPackets.length>0&&t.diy.data.page&&t.diy.data.page.activebackground?t.diy.data.page.activebackground:"#ccc"},on:{click:function(a){return t.onChangeStatus("redpacket")}}},[t._v("\n\t\t\t\t\t\t"+t._s(t.redPackets.length>0?t.redPackets.length+"个可用红包":t.basicPart.params.noredpacketnote)+"\n\t\t\t\t\t")]),t._v(" "),e("span",{staticClass:"icon icon-right c-disabled font-12"})])]),t._v(" "),1==t.basicPart.params.showtips?e("van-cell",{on:{click:function(a){return t.onChangeStatus("tip")}}},[e("div",{attrs:{slot:"title"},slot:"title"},[t._v(t._s(t.basicPart.params.tipsname))]),t._v(" "),e("div",{attrs:{slot:"right-icon"},slot:"right-icon"},[e("span",{staticClass:"font-14",style:{color:t.erranderExtra.delivery_tips>0&&t.diy.data.page&&t.diy.data.page.activebackground?t.diy.data.page.activebackground:"#333"}},[t._v("\n\t\t\t\t\t\t"+t._s(t.erranderExtra.delivery_tips>0?t.Lang.dollarSign+t.erranderExtra.delivery_tips:t.basicPart.params.tipsnote)+"\n\t\t\t\t\t")]),t._v(" "),e("span",{staticClass:"icon icon-right c-disabled font-12"})])]):t._e()],1),t._v(" "),e("p",{staticClass:"diy-scene-agreement font-12",on:{click:t.onShowAgreement}},[t._v("点击查看 "),e("span",{staticClass:"c-default"},[t._v("《跑腿服务协议》")])])],2),t._v(" "),e("div",{staticClass:"diy-scene-submit van-hairline--top"},[e("div",{staticClass:"order-info flex-lr"},[e("div",{staticClass:"font-12"},[t._v(t._s(t.order.distance>0?t.order.distance+"公里":""))]),t._v(" "),e("div",{staticClass:"font-12 c-default",on:{click:function(a){return t.onChangeStatus("fee")}}},[e("span",[t._v(t._s(t.basicPart.params.feesname))]),t._v(" "),e("span",{style:{color:t.diy.data.page&&t.diy.data.page.activebackground?t.diy.data.page.activebackground:"#ff2d4b"}},[e("span",{staticClass:"font-20"},[t._v(t._s(t.order.final_fee))]),t._v(t._s(t.Lang.dollarSignCn)+"\n\t\t\t\t")]),t._v(" "),e("span",{staticClass:"icon icon-fold font-12 c-disabled"})])]),t._v(" "),1==t.diy.is_rest?e("van-button",{attrs:{disabled:"",size:"normal",block:!0}},[t._v("休息中暂不提供服务")]):e("van-button",{style:{"background-color":t.diy.data.page&&t.diy.data.page.activebackground?t.diy.data.page.activebackground:"#f44","border-color":t.diy.data.page&&t.diy.data.page.activebackground?t.diy.data.page.activebackground:"#f44",color:t.diy.data.page&&t.diy.data.page.activecolor?t.diy.data.page.activecolor:"#fff"},attrs:{size:"normal",block:!0,type:"danger"},on:{click:t.onSubmit}},[t._v("\n\t\t\t"+t._s(t.basicPart.params.submitname)+"\n\t\t")])],1),t._v(" "),e("van-popup",{staticClass:"agreement-popup",style:{height:"100%"},attrs:{position:"bottom"},model:{value:t.status.yinsihaoAgreement,callback:function(a){t.$set(t.status,"yinsihaoAgreement",a)},expression:"status.yinsihaoAgreement"}},[e("van-nav-bar",{staticClass:"border-0px",style:{background:"#ff2d4b",color:"#fff"},attrs:{title:"隐私号服务协议"},on:{"click-left":function(a){return t.onChangeStatus("yinsihaoAgreement")}}},[e("van-icon",{staticClass:"font-20",style:{color:"#fff"},attrs:{slot:"left",name:"left"},slot:"left"})],1),t._v(" "),t.basicPart.params.yinsihao?e("div",{staticClass:"popup-content margin-10",domProps:{innerHTML:t._s(t.basicPart.params.yinsihao.agreement)}}):t._e()],1),t._v(" "),e("van-popup",{staticClass:"popup-prefee",attrs:{position:"bottom"},model:{value:t.status.prefee,callback:function(a){t.$set(t.status,"prefee",a)},expression:"status.prefee"}},[e("div",{staticClass:"popup-title flex-lr border-1px-b"},[e("span",{staticClass:"font-14",on:{click:function(a){return t.onChangeStatus("prefee")}}},[t._v("取消")]),t._v(" "),e("span",[t._v("预估商品费")]),t._v(" "),e("span",{staticClass:"font-14",on:{click:t.onConfirmPrefee}},[t._v("确定")])]),t._v(" "),e("div",{staticClass:"popup-content"},[e("p",{staticClass:"prefee-tips"},[t._v("供骑手代购时参考（可选填）")]),t._v(" "),e("div",{staticClass:"prefee-edit",style:{color:t.diy.data.page&&t.diy.data.page.activebackground?t.diy.data.page.activebackground:"#ff2d4b","border-color":t.diy.data.page&&t.diy.data.page.activebackground?t.diy.data.page.activebackground:"#ff2d4b"}},[t._v("\n\t\t\t\t预估 "+t._s(t.Lang.dollarSign)+"\n\t\t\t\t"),e("input",{directives:[{name:"model",rawName:"v-model",value:t.erranderExtra.goods_price,expression:"erranderExtra.goods_price"}],style:{color:t.diy.data.page&&t.diy.data.page.activebackground?t.diy.data.page.activebackground:"#ff2d4b"},attrs:{type:"text"},domProps:{value:t.erranderExtra.goods_price},on:{blur:t.onBlur,input:function(a){a.target.composing||t.$set(t.erranderExtra,"goods_price",a.target.value)}}})]),t._v(" "),e("p",{staticClass:"prefee-max c-disabled"},[e("span",{staticClass:"icon icon-info"}),t._v("最高500"+t._s(t.Lang.dollarSignCn)+"\n\t\t\t")])])]),t._v(" "),t.order.delivery_info&&t.erranderExtra.delivery_day?e("van-popup",{staticClass:"popup-delivery-time",attrs:{position:"bottom"},model:{value:t.status.deliveryTime,callback:function(a){t.$set(t.status,"deliveryTime",a)},expression:"status.deliveryTime"}},[e("div",{staticClass:"popup-title flex-lr border-1px-b"},[e("span",{staticClass:"font-14",on:{click:function(a){return t.onChangeStatus("deliveryTime")}}},[t._v("取消")]),t._v(" "),e("span",[t._v("取件时间")]),t._v(" "),e("span",{staticClass:"opacity-1"},[t._v("确定")])]),t._v(" "),e("div",{staticClass:"popup-content flex-lr"},[e("div",{staticClass:"date"},t._l(t.order.delivery_info,function(a,s){return e("div",{staticClass:"date-item",class:{active:s==t.erranderExtra.delivery_day},on:{click:function(a){return t.onSelectDay(s)}}},[t._v(t._s(s))])}),0),t._v(" "),e("div",{staticClass:"time"},t._l(t.order.delivery_info[t.erranderExtra.delivery_day].times,function(a,s){return e("div",{key:s,staticClass:"time-item",class:{active:a==t.erranderExtra.delivery_time},style:{color:a==t.erranderExtra.delivery_time&&t.diy.data.page&&t.diy.data.page.activebackground?t.diy.data.page.activebackground:"#333"},on:{click:function(e){return t.onSelectTime(a)}}},[t._v("\n\t\t\t\t\t"+t._s(a==t.order.delivery_nowtime?"立即送出(大约"+a+")":a)+"\n\t\t\t\t\t"),e("span",{staticClass:"icon",class:{"icon-check":a==t.erranderExtra.delivery_time},style:{color:a==t.erranderExtra.delivery_time&&t.diy.data.page&&t.diy.data.page.activebackground?t.diy.data.page.activebackground:"#fff"}})])}),0)])]):t._e(),t._v(" "),""!==t.erranderExtra.goods_weight&&t.diy.data&&t.diy.data.fees?e("van-popup",{staticClass:"popup-goods-weight",attrs:{position:"bottom"},model:{value:t.status.goodsWeight,callback:function(a){t.$set(t.status,"goodsWeight",a)},expression:"status.goodsWeight"}},[e("div",{staticClass:"popup-title flex-lr border-1px-b"},[e("span",{staticClass:"font-14",on:{click:function(a){return t.onChangeStatus("goodsWeight")}}},[t._v("取消")]),t._v(" "),e("span",[t._v("物品重量")]),t._v(" "),e("span",{staticClass:"opacity-1"},[t._v("确定")])]),t._v(" "),e("div",{staticClass:"popup-content"},[e("p",{staticClass:"weight-label"},[t._v("重量")]),t._v(" "),t.erranderExtra.goods_weight>t.diy.data.fees.weight_data.basic?e("div",{staticClass:"weight-value",style:{color:t.diy.data.page&&t.diy.data.page.activebackground?t.diy.data.page.activebackground:"fbb000"}},[t._v("\n\t\t\t\t"+t._s(t.erranderExtra.goods_weight)+"公斤\n\t\t\t")]):e("div",{staticClass:"weight-value",style:{color:t.diy.data.page&&t.diy.data.page.activebackground?t.diy.data.page.activebackground:"fbb000"}},[t._v("小于"+t._s(t.diy.data.fees.weight_data.basic)+"公斤")]),t._v(" "),e("div",{staticClass:"slider"},[e("van-slider",{attrs:{min:0,max:100,step:t.weightSliderStep,"active-color":t.diy.data.page&&t.diy.data.page.activebackground?t.diy.data.page.activebackground:"#ff2d4b"},on:{change:t.onChangeWeight},model:{value:t.goodsWeight,callback:function(a){t.goodsWeight=a},expression:"goodsWeight"}}),t._v(" "),e("div",{staticClass:"slide-line-bottom"},[e("div",{staticClass:"left"},[t._v("小于"+t._s(t.diy.data.fees.weight_data.basic)+"公斤")]),t._v(" "),e("div",{staticClass:"right"},[t._v("100公斤")])])],1)])]):t._e(),t._v(" "),e("van-popup",{staticClass:"popup-tip",attrs:{position:"bottom"},model:{value:t.status.tip,callback:function(a){t.$set(t.status,"tip",a)},expression:"status.tip"}},[e("div",{staticClass:"popup-title flex-lr border-1px-b"},[e("span",{staticClass:"font-14 c-gray",on:{click:function(a){return t.onChangeStatus("tip")}}},[t._v("取消")]),t._v(" "),e("span",[t._v("小费")]),t._v(" "),e("span",{staticClass:"font-14 c-danger",on:{click:t.onChangeTip}},[t._v("确定")])]),t._v(" "),e("div",{staticClass:"popup-content padding-10"},[t.basicPart.tipsGroup.length>0?e("ul",{staticClass:"tip-group"},t._l(t.basicPart.tipsGroup,function(a,s){return e("li",{key:s,staticClass:"tip-item-wrap",on:{click:function(e){return t.onSelectTips(a,s)}}},[e("div",{class:{"tip-item-inner":1,active:t.tipsInfo.selectIndex==s&&"select"==t.tipsInfo.dataFrom}},[t._v(t._s(a>0?t.Lang.dollarSign+a:"不加了"))])])}),0):t._e(),t._v(" "),e("div",{class:{"tip-input":1,active:t.tipsInfo.inputFocus},on:{click:t.onTipsInputFocus}},[e("span",[t._v("其他金额")]),t._v(" "),t.tipsInfo.inputFocus?[e("span",{staticClass:"margin-10-l"},[t._v(t._s(t.Lang.dollarSign))]),t._v(" "),e("input",{directives:[{name:"model",rawName:"v-model",value:t.tipsInfo.input,expression:"tipsInfo.input"}],attrs:{type:"number",autofocus:t.tipsInfo.inputFocus},domProps:{value:t.tipsInfo.input},on:{blur:t.onBlur,focus:t.onFocus,input:function(a){a.target.composing||t.$set(t.tipsInfo,"input",a.target.value)}}})]:t._e()],2),t._v(" "),e("div",{staticClass:"flex-center font-12 margin-10-t margin-5-b c-gray"},[e("i",{staticClass:"icon icon-warn margin-5-r"}),t._v(" "),e("span",[t._v("\n\t\t\t\t\t最低"+t._s(t.basicPart.params.minfee)+t._s(t.Lang.dollarSignCn)+"，\n\t\t\t\t\t最高"+t._s(t.basicPart.params.maxfee)+t._s(t.Lang.dollarSignCn)+"\n\t\t\t\t")])])])]),t._v(" "),e("van-popup",{staticClass:"popup-fee",attrs:{position:"bottom"},model:{value:t.status.fee,callback:function(a){t.$set(t.status,"fee",a)},expression:"status.fee"}},[e("div",{staticClass:"popup-title flex-lr border-1px-b"},[e("span",{staticClass:"font-12 flex opacity-1"},[t._v("\n\t\t\t\t价格规则\n\t\t\t\t"),e("span",{staticClass:"icon icon-right font-12 "})]),t._v(" "),e("span",[t._v("费用明细")]),t._v(" "),e("span",{staticClass:"font-12 flex opacity-0",style:{color:t.diy.data.page&&t.diy.data.page.activebackground?t.diy.data.page.activebackground:"#ff2d4b"},on:{click:t.onGofeeRule}},[t._v("\n\t\t\t\t价格规则\n\t\t\t\t"),e("span",{staticClass:"icon icon-right font-12 "})])]),t._v(" "),e("div",{staticClass:"popup-content"},[e("van-cell-group",{staticClass:"border-0px"},[t._l(t.order.fees,function(a,s){return[""!=a.fee?e("van-cell",{staticClass:"border-0px",style:{color:a.fee<0&&t.diy.data.page&&t.diy.data.page.activebackground?t.diy.data.page.activebackground:"#969799"},attrs:{title:a.title,value:a.fee_cn}}):t._e()]})],2)],1)]),t._v(" "),t.redPackets.length>0?e("van-popup",{attrs:{position:"bottom"},model:{value:t.status.redpacket,callback:function(a){t.$set(t.status,"redpacket",a)},expression:"status.redpacket"}},[e("div",{staticClass:"popup-redpacket"},[e("div",{staticClass:"popup-title van-hairline--bottom text-center"},[t._v("可用红包")]),t._v(" "),e("div",{staticClass:"popup-container"},[e("load",{attrs:{type:"loaded",text:"可用红包("+t.redPackets.length+"个)",bgcolor:"#f5f5f5"}}),t._v(" "),t._l(t.redPackets,function(a){return e("div",{key:a.id,staticClass:"redPacket-list content-padded"},[e("div",{staticClass:"redPacket-list-item",on:{click:function(e){return t.onSelectRedpacket(a.id)}}},[e("div",{staticClass:"redPacket-list-item-container"},[e("div",{staticClass:"redPacket-info row"},[e("div",{staticClass:"col-50"},[e("span",{staticClass:"redPacket-title"},[t._v(t._s(a.title))])]),t._v(" "),e("div",{staticClass:"col-50 text-right"},[e("div",{staticClass:"price"},[t._v(t._s(t.Lang.dollarSign)),e("span",{staticClass:"price-num"},[t._v(t._s(a.discount))])])])]),t._v(" "),e("div",{staticClass:"redPacket-use-limit row"},[e("div",{staticClass:"col-60"},[t._v(t._s(a.day_cn))]),t._v(" "),e("div",{staticClass:"col-40 text-right"},[e("p",{staticClass:"use-condition"},[t._v("满"+t._s(a.condition)+t._s(t.Lang.dollarSignCn)+"可用")])])])]),t._v(" "),e("span",{staticClass:"circle circle-left"}),t._v(" "),e("span",{staticClass:"circle circle-right"}),t._v(" "),a.id==t.order.redpacket_id?e("div",{staticClass:"selected-status"},[e("img",{staticClass:"img-100",attrs:{src:"static/img/success.png",alt:""}})]):t._e()])])})],2),t._v(" "),e("div",{staticClass:"popup-cancle van-hairline--top",on:{click:function(a){return t.onSelectRedpacket(0)}}},[t._v("不使用红包")])])]):t._e(),t._v(" "),e("transition",{attrs:{name:"loading"}},[t.showPreLoading?e("iloading"):t._e()],1),t._v(" "),e("agreement",{attrs:{show:t.agreemnetShow,title:t.agreementtitle,content:t.diy.agreement},on:{agreementHide:function(a){t.agreemnetShow=!1}}}),t._v(" "),t.getLocationStatus?t._e():e("failed-tips",{attrs:{failedTips:t.failedTips}}),t._v(" "),e("bind-mobile",{attrs:{"bind-mobile":t.bind_mobile}})],1)},staticRenderFns:[]};var h=e("VU/8")(y,f,!1,function(t){e("PVlM")},null,null);a.default=h.exports},PVlM:function(t,a){}});
//# sourceMappingURL=63.7a62e8f128da6f7412a4.js.map