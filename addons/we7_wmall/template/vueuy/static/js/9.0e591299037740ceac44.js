webpackJsonp([9],{"+Y3u":function(t,e,r){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var i=r("Cz8s"),o=r("MJLE"),s=r.n(o),n={data:function(){return{order_id:Number,islegal:!1,order:{goods_info:{}},showPreLoading:!0}},components:{PublicHeader:i.a},methods:{onLoad:function(){var t=this;this.util.request({url:"creditshop/order/detail",data:{order_id:this.order_id}}).then(function(e){t.showPreLoading=!1;var r=e.data.message;if(r.errno)return t.util.$toast(r.message),!1;t.islegal=!0,t.order=r.message,t.$nextTick(function(){t.newQrcode(t.order.qrcode)}),console.log(t.order)})},newQrcode:function(t){new s.a("qrcode",{width:150,height:150,text:t,image:""})}},created:function(){if(!this.$route.query.id)return this.util.$toast("参数错误"),!1;this.order_id=this.$route.query.id},mounted:function(){this.onLoad()}},a={render:function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("div",{attrs:{id:"creditshop-detail"}},[r("public-header",{attrs:{title:"兑换详情"}}),t._v(" "),r("div",{staticClass:"content"},[r("div",{staticClass:"detail"},[r("div",{staticClass:"shop-content"},[t._m(0),t._v(" "),r("div",{staticClass:"food-list"},[r("div",{staticClass:"food-item-img"},[r("img",{attrs:{src:t.order.goods_info.thumb,alt:""}})]),t._v(" "),r("div",{staticClass:"food-item-price"},[r("div",{staticClass:"now-price"},[t._v("\n\t\t\t\t\t\t\t"+t._s(t.order.goods_info.use_credit1)+"积分\n\t\t\t\t\t\t\t"),t.order.goods_info.use_credit2>0?[t._v("\n\t\t\t\t\t\t\t\t+ "+t._s(t.Lang.dollarSign)+t._s(t.order.goods_info.use_credit2)+"\n\t\t\t\t\t\t\t")]:t._e()],2),t._v(" "),r("div",{staticClass:"old-price"},[t._v(t._s(t.Lang.dollarSign)+t._s(t.order.goods_info.old_price))])]),t._v(" "),r("div",{staticClass:"food-item-info"},[r("div",{staticClass:"food-item-name"},[t._v(t._s(t.order.goods_info.title))]),t._v(" "),r("div",{staticClass:"food-item-num"},[t._v("x1")])])])]),t._v(" "),"goods"==t.order.goods_type?r("div",{staticClass:"dispatching"},[r("div",{staticClass:"time van-hairline--bottom"},[r("div",{staticClass:"text"},[t._v("收件人姓名")]),t._v(" "),r("div",{staticClass:"main"},[t._v(t._s(t.order.username))])]),t._v(" "),r("div",{staticClass:"server"},[r("div",{staticClass:"text"},[t._v("联系方式")]),t._v(" "),r("div",{staticClass:"main"},[t._v(t._s(t.order.mobile))])])]):t._e(),t._v(" "),"goods"==t.order.goods_type?r("div",{staticClass:"code-qrcode"},[r("div",{attrs:{id:"qrcode"}}),t._v(" "),r("span",{staticClass:"font-14 margin-10-t"},[t._v("兑换码："+t._s(t.order.code))]),t._v(" "),r("div",{staticClass:"code-text"},[t._v("请平台管理员扫描二维码或者填写兑换码即可兑换")])]):t._e(),t._v(" "),r("div",{staticClass:"order"},[r("div",{staticClass:"code"},[r("div",{staticClass:"text"},[t._v("订单号码")]),t._v(" "),r("div",{staticClass:"main"},[t._v(t._s(t.order.order_sn))])]),t._v(" "),r("div",{staticClass:"divide-line"}),t._v(" "),"credit2"==t.order.goods_type||"redpacket"==t.order.goods_type?[r("div",{staticClass:"code"},[r("div",{staticClass:"text"},[t._v("商品类型")]),t._v(" "),r("div",{staticClass:"main ltr"},["credit2"==t.order.goods_type?[t._v("\n\t\t\t\t\t\t\t\t余额\n\t\t\t\t\t\t\t")]:"redpacket"==t.order.goods_type?[t._v("\n\t\t\t\t\t\t\t\t红包\n\t\t\t\t\t\t\t")]:t._e(),t._v(" "),r("span",{staticClass:"c-danger"},[t._v("（已发送到您的账户中）")])],2)]),t._v(" "),r("div",{staticClass:"divide-line"})]:t._e(),t._v(" "),r("div",{staticClass:"time"},[r("div",{staticClass:"text"},[t._v("\n\t\t\t\t\t\t消耗积分"),t.order.use_credit2>0?[t._v("+消耗余额")]:t._e()],2),t._v(" "),r("div",{staticClass:"main"},[t._v("\n\t\t\t\t\t\t"+t._s(t.order.use_credit1)+"积分 "),t.order.use_credit2>0?[t._v("+ "+t._s(t.Lang.dollarSign)+t._s(t.order.use_credit2))]:t._e()],2)]),t._v(" "),r("div",{staticClass:"divide-line"}),t._v(" "),r("div",{staticClass:"code"},[r("div",{staticClass:"text"},[t._v("兑换时间")]),t._v(" "),r("div",{staticClass:"main"},[t._v(t._s(t.order.addtime))])])],2),t._v(" "),t.order.use_credit2>0&&0==t.order.is_pay&&1==t.order.status?r("router-link",{staticClass:"now-pay",attrs:{to:t.util.getUrl({path:"/pages/public/pay",query:{order_id:t.order.id,order_type:"creditshop"}})}},[t._v("\n\t\t\t\t立即支付\n\t\t\t")]):t._e()],1)]),t._v(" "),t.showPreLoading?r("iloading"):t._e()],1)},staticRenderFns:[function(){var t=this.$createElement,e=this._self._c||t;return e("div",{staticClass:"store-name"},[e("div",{staticClass:"name"},[this._v("兑换商品信息")])])}]};var h=r("VU/8")(n,a,!1,function(t){r("Guwy")},null,null);e.default=h.exports},Guwy:function(t,e){},MJLE:function(t,e,r){var i,o;o=function(){function t(t){this.mode=r.MODE_8BIT_BYTE,this.data=t,this.parsedData=[];for(var e=0,i=this.data.length;e<i;e++){var o=[],s=this.data.charCodeAt(e);s>65536?(o[0]=240|(1835008&s)>>>18,o[1]=128|(258048&s)>>>12,o[2]=128|(4032&s)>>>6,o[3]=128|63&s):s>2048?(o[0]=224|(61440&s)>>>12,o[1]=128|(4032&s)>>>6,o[2]=128|63&s):s>128?(o[0]=192|(1984&s)>>>6,o[1]=128|63&s):o[0]=s,this.parsedData.push(o)}this.parsedData=Array.prototype.concat.apply([],this.parsedData),this.parsedData.length!=this.data.length&&(this.parsedData.unshift(191),this.parsedData.unshift(187),this.parsedData.unshift(239))}function e(t,e){this.typeNumber=t,this.errorCorrectLevel=e,this.modules=null,this.moduleCount=0,this.dataCache=null,this.dataList=[]}t.prototype={getLength:function(t){return this.parsedData.length},write:function(t){for(var e=0,r=this.parsedData.length;e<r;e++)t.put(this.parsedData[e],8)}},e.prototype={addData:function(e){var r=new t(e);this.dataList.push(r),this.dataCache=null},isDark:function(t,e){if(t<0||this.moduleCount<=t||e<0||this.moduleCount<=e)throw new Error(t+","+e);return this.modules[t][e]},getModuleCount:function(){return this.moduleCount},make:function(){this.makeImpl(!1,this.getBestMaskPattern())},makeImpl:function(t,r){this.moduleCount=4*this.typeNumber+17,this.modules=new Array(this.moduleCount);for(var i=0;i<this.moduleCount;i++){this.modules[i]=new Array(this.moduleCount);for(var o=0;o<this.moduleCount;o++)this.modules[i][o]=null}this.setupPositionProbePattern(0,0),this.setupPositionProbePattern(this.moduleCount-7,0),this.setupPositionProbePattern(0,this.moduleCount-7),this.setupPositionAdjustPattern(),this.setupTimingPattern(),this.setupTypeInfo(t,r),this.typeNumber>=7&&this.setupTypeNumber(t),null==this.dataCache&&(this.dataCache=e.createData(this.typeNumber,this.errorCorrectLevel,this.dataList)),this.mapData(this.dataCache,r)},setupPositionProbePattern:function(t,e){for(var r=-1;r<=7;r++)if(!(t+r<=-1||this.moduleCount<=t+r))for(var i=-1;i<=7;i++)e+i<=-1||this.moduleCount<=e+i||(this.modules[t+r][e+i]=0<=r&&r<=6&&(0==i||6==i)||0<=i&&i<=6&&(0==r||6==r)||2<=r&&r<=4&&2<=i&&i<=4)},getBestMaskPattern:function(){for(var t=0,e=0,r=0;r<8;r++){this.makeImpl(!0,r);var i=g.getLostPoint(this);(0==r||t>i)&&(t=i,e=r)}return e},createMovieClip:function(t,e,r){var i=t.createEmptyMovieClip(e,r);this.make();for(var o=0;o<this.modules.length;o++)for(var s=1*o,n=0;n<this.modules[o].length;n++){var a=1*n;this.modules[o][n]&&(i.beginFill(0,100),i.moveTo(a,s),i.lineTo(a+1,s),i.lineTo(a+1,s+1),i.lineTo(a,s+1),i.endFill())}return i},setupTimingPattern:function(){for(var t=8;t<this.moduleCount-8;t++)null==this.modules[t][6]&&(this.modules[t][6]=t%2==0);for(var e=8;e<this.moduleCount-8;e++)null==this.modules[6][e]&&(this.modules[6][e]=e%2==0)},setupPositionAdjustPattern:function(){for(var t=g.getPatternPosition(this.typeNumber),e=0;e<t.length;e++)for(var r=0;r<t.length;r++){var i=t[e],o=t[r];if(null==this.modules[i][o])for(var s=-2;s<=2;s++)for(var n=-2;n<=2;n++)this.modules[i+s][o+n]=-2==s||2==s||-2==n||2==n||0==s&&0==n}},setupTypeNumber:function(t){for(var e=g.getBCHTypeNumber(this.typeNumber),r=0;r<18;r++){var i=!t&&1==(e>>r&1);this.modules[Math.floor(r/3)][r%3+this.moduleCount-8-3]=i}for(r=0;r<18;r++){i=!t&&1==(e>>r&1);this.modules[r%3+this.moduleCount-8-3][Math.floor(r/3)]=i}},setupTypeInfo:function(t,e){for(var r=this.errorCorrectLevel<<3|e,i=g.getBCHTypeInfo(r),o=0;o<15;o++){var s=!t&&1==(i>>o&1);o<6?this.modules[o][8]=s:o<8?this.modules[o+1][8]=s:this.modules[this.moduleCount-15+o][8]=s}for(o=0;o<15;o++){s=!t&&1==(i>>o&1);o<8?this.modules[8][this.moduleCount-o-1]=s:o<9?this.modules[8][15-o-1+1]=s:this.modules[8][15-o-1]=s}this.modules[this.moduleCount-8][8]=!t},mapData:function(t,e){for(var r=-1,i=this.moduleCount-1,o=7,s=0,n=this.moduleCount-1;n>0;n-=2)for(6==n&&n--;;){for(var a=0;a<2;a++)if(null==this.modules[i][n-a]){var h=!1;s<t.length&&(h=1==(t[s]>>>o&1)),g.getMask(e,i,n-a)&&(h=!h),this.modules[i][n-a]=h,-1==--o&&(s++,o=7)}if((i+=r)<0||this.moduleCount<=i){i-=r,r=-r;break}}}},e.PAD0=236,e.PAD1=17,e.createData=function(t,r,i){for(var o=p.getRSBlocks(t,r),s=new m,n=0;n<i.length;n++){var a=i[n];s.put(a.mode,4),s.put(a.getLength(),g.getLengthInBits(a.mode,t)),a.write(s)}var h=0;for(n=0;n<o.length;n++)h+=o[n].dataCount;if(s.getLengthInBits()>8*h)throw new Error("code length overflow. ("+s.getLengthInBits()+">"+8*h+")");for(s.getLengthInBits()+4<=8*h&&s.put(0,4);s.getLengthInBits()%8!=0;)s.putBit(!1);for(;!(s.getLengthInBits()>=8*h||(s.put(e.PAD0,8),s.getLengthInBits()>=8*h));)s.put(e.PAD1,8);return e.createBytes(s,o)},e.createBytes=function(t,e){for(var r=0,i=0,o=0,s=new Array(e.length),n=new Array(e.length),a=0;a<e.length;a++){var h=e[a].dataCount,l=e[a].totalCount-h;i=Math.max(i,h),o=Math.max(o,l),s[a]=new Array(h);for(var d=0;d<s[a].length;d++)s[a][d]=255&t.buffer[d+r];r+=h;var u=g.getErrorCorrectPolynomial(l),c=new v(s[a],u.getLength()-1).mod(u);n[a]=new Array(u.getLength()-1);for(d=0;d<n[a].length;d++){var f=d+c.getLength()-n[a].length;n[a][d]=f>=0?c.get(f):0}}var _=0;for(d=0;d<e.length;d++)_+=e[d].totalCount;var p=new Array(_),m=0;for(d=0;d<i;d++)for(a=0;a<e.length;a++)d<s[a].length&&(p[m++]=s[a][d]);for(d=0;d<o;d++)for(a=0;a<e.length;a++)d<n[a].length&&(p[m++]=n[a][d]);return p};for(var r={MODE_NUMBER:1,MODE_ALPHA_NUM:2,MODE_8BIT_BYTE:4,MODE_KANJI:8},o={L:1,M:0,Q:3,H:2},s=0,n=1,a=2,h=3,l=4,d=5,u=6,c=7,g={PATTERN_POSITION_TABLE:[[],[6,18],[6,22],[6,26],[6,30],[6,34],[6,22,38],[6,24,42],[6,26,46],[6,28,50],[6,30,54],[6,32,58],[6,34,62],[6,26,46,66],[6,26,48,70],[6,26,50,74],[6,30,54,78],[6,30,56,82],[6,30,58,86],[6,34,62,90],[6,28,50,72,94],[6,26,50,74,98],[6,30,54,78,102],[6,28,54,80,106],[6,32,58,84,110],[6,30,58,86,114],[6,34,62,90,118],[6,26,50,74,98,122],[6,30,54,78,102,126],[6,26,52,78,104,130],[6,30,56,82,108,134],[6,34,60,86,112,138],[6,30,58,86,114,142],[6,34,62,90,118,146],[6,30,54,78,102,126,150],[6,24,50,76,102,128,154],[6,28,54,80,106,132,158],[6,32,58,84,110,136,162],[6,26,54,82,110,138,166],[6,30,58,86,114,142,170]],G15:1335,G18:7973,G15_MASK:21522,getBCHTypeInfo:function(t){for(var e=t<<10;g.getBCHDigit(e)-g.getBCHDigit(g.G15)>=0;)e^=g.G15<<g.getBCHDigit(e)-g.getBCHDigit(g.G15);return(t<<10|e)^g.G15_MASK},getBCHTypeNumber:function(t){for(var e=t<<12;g.getBCHDigit(e)-g.getBCHDigit(g.G18)>=0;)e^=g.G18<<g.getBCHDigit(e)-g.getBCHDigit(g.G18);return t<<12|e},getBCHDigit:function(t){for(var e=0;0!=t;)e++,t>>>=1;return e},getPatternPosition:function(t){return g.PATTERN_POSITION_TABLE[t-1]},getMask:function(t,e,r){switch(t){case s:return(e+r)%2==0;case n:return e%2==0;case a:return r%3==0;case h:return(e+r)%3==0;case l:return(Math.floor(e/2)+Math.floor(r/3))%2==0;case d:return e*r%2+e*r%3==0;case u:return(e*r%2+e*r%3)%2==0;case c:return(e*r%3+(e+r)%2)%2==0;default:throw new Error("bad maskPattern:"+t)}},getErrorCorrectPolynomial:function(t){for(var e=new v([1],0),r=0;r<t;r++)e=e.multiply(new v([1,f.gexp(r)],0));return e},getLengthInBits:function(t,e){if(1<=e&&e<10)switch(t){case r.MODE_NUMBER:return 10;case r.MODE_ALPHA_NUM:return 9;case r.MODE_8BIT_BYTE:case r.MODE_KANJI:return 8;default:throw new Error("mode:"+t)}else if(e<27)switch(t){case r.MODE_NUMBER:return 12;case r.MODE_ALPHA_NUM:return 11;case r.MODE_8BIT_BYTE:return 16;case r.MODE_KANJI:return 10;default:throw new Error("mode:"+t)}else{if(!(e<41))throw new Error("type:"+e);switch(t){case r.MODE_NUMBER:return 14;case r.MODE_ALPHA_NUM:return 13;case r.MODE_8BIT_BYTE:return 16;case r.MODE_KANJI:return 12;default:throw new Error("mode:"+t)}}},getLostPoint:function(t){for(var e=t.getModuleCount(),r=0,i=0;i<e;i++)for(var o=0;o<e;o++){for(var s=0,n=t.isDark(i,o),a=-1;a<=1;a++)if(!(i+a<0||e<=i+a))for(var h=-1;h<=1;h++)o+h<0||e<=o+h||0==a&&0==h||n==t.isDark(i+a,o+h)&&s++;s>5&&(r+=3+s-5)}for(i=0;i<e-1;i++)for(o=0;o<e-1;o++){var l=0;t.isDark(i,o)&&l++,t.isDark(i+1,o)&&l++,t.isDark(i,o+1)&&l++,t.isDark(i+1,o+1)&&l++,0!=l&&4!=l||(r+=3)}for(i=0;i<e;i++)for(o=0;o<e-6;o++)t.isDark(i,o)&&!t.isDark(i,o+1)&&t.isDark(i,o+2)&&t.isDark(i,o+3)&&t.isDark(i,o+4)&&!t.isDark(i,o+5)&&t.isDark(i,o+6)&&(r+=40);for(o=0;o<e;o++)for(i=0;i<e-6;i++)t.isDark(i,o)&&!t.isDark(i+1,o)&&t.isDark(i+2,o)&&t.isDark(i+3,o)&&t.isDark(i+4,o)&&!t.isDark(i+5,o)&&t.isDark(i+6,o)&&(r+=40);var d=0;for(o=0;o<e;o++)for(i=0;i<e;i++)t.isDark(i,o)&&d++;return r+=10*(Math.abs(100*d/e/e-50)/5)}},f={glog:function(t){if(t<1)throw new Error("glog("+t+")");return f.LOG_TABLE[t]},gexp:function(t){for(;t<0;)t+=255;for(;t>=256;)t-=255;return f.EXP_TABLE[t]},EXP_TABLE:new Array(256),LOG_TABLE:new Array(256)},_=0;_<8;_++)f.EXP_TABLE[_]=1<<_;for(_=8;_<256;_++)f.EXP_TABLE[_]=f.EXP_TABLE[_-4]^f.EXP_TABLE[_-5]^f.EXP_TABLE[_-6]^f.EXP_TABLE[_-8];for(_=0;_<255;_++)f.LOG_TABLE[f.EXP_TABLE[_]]=_;function v(t,e){if(void 0==t.length)throw new Error(t.length+"/"+e);for(var r=0;r<t.length&&0==t[r];)r++;this.num=new Array(t.length-r+e);for(var i=0;i<t.length-r;i++)this.num[i]=t[i+r]}function p(t,e){this.totalCount=t,this.dataCount=e}function m(){this.buffer=[],this.length=0}v.prototype={get:function(t){return this.num[t]},getLength:function(){return this.num.length},multiply:function(t){for(var e=new Array(this.getLength()+t.getLength()-1),r=0;r<this.getLength();r++)for(var i=0;i<t.getLength();i++)e[r+i]^=f.gexp(f.glog(this.get(r))+f.glog(t.get(i)));return new v(e,0)},mod:function(t){if(this.getLength()-t.getLength()<0)return this;for(var e=f.glog(this.get(0))-f.glog(t.get(0)),r=new Array(this.getLength()),i=0;i<this.getLength();i++)r[i]=this.get(i);for(i=0;i<t.getLength();i++)r[i]^=f.gexp(f.glog(t.get(i))+e);return new v(r,0).mod(t)}},p.RS_BLOCK_TABLE=[[1,26,19],[1,26,16],[1,26,13],[1,26,9],[1,44,34],[1,44,28],[1,44,22],[1,44,16],[1,70,55],[1,70,44],[2,35,17],[2,35,13],[1,100,80],[2,50,32],[2,50,24],[4,25,9],[1,134,108],[2,67,43],[2,33,15,2,34,16],[2,33,11,2,34,12],[2,86,68],[4,43,27],[4,43,19],[4,43,15],[2,98,78],[4,49,31],[2,32,14,4,33,15],[4,39,13,1,40,14],[2,121,97],[2,60,38,2,61,39],[4,40,18,2,41,19],[4,40,14,2,41,15],[2,146,116],[3,58,36,2,59,37],[4,36,16,4,37,17],[4,36,12,4,37,13],[2,86,68,2,87,69],[4,69,43,1,70,44],[6,43,19,2,44,20],[6,43,15,2,44,16],[4,101,81],[1,80,50,4,81,51],[4,50,22,4,51,23],[3,36,12,8,37,13],[2,116,92,2,117,93],[6,58,36,2,59,37],[4,46,20,6,47,21],[7,42,14,4,43,15],[4,133,107],[8,59,37,1,60,38],[8,44,20,4,45,21],[12,33,11,4,34,12],[3,145,115,1,146,116],[4,64,40,5,65,41],[11,36,16,5,37,17],[11,36,12,5,37,13],[5,109,87,1,110,88],[5,65,41,5,66,42],[5,54,24,7,55,25],[11,36,12],[5,122,98,1,123,99],[7,73,45,3,74,46],[15,43,19,2,44,20],[3,45,15,13,46,16],[1,135,107,5,136,108],[10,74,46,1,75,47],[1,50,22,15,51,23],[2,42,14,17,43,15],[5,150,120,1,151,121],[9,69,43,4,70,44],[17,50,22,1,51,23],[2,42,14,19,43,15],[3,141,113,4,142,114],[3,70,44,11,71,45],[17,47,21,4,48,22],[9,39,13,16,40,14],[3,135,107,5,136,108],[3,67,41,13,68,42],[15,54,24,5,55,25],[15,43,15,10,44,16],[4,144,116,4,145,117],[17,68,42],[17,50,22,6,51,23],[19,46,16,6,47,17],[2,139,111,7,140,112],[17,74,46],[7,54,24,16,55,25],[34,37,13],[4,151,121,5,152,122],[4,75,47,14,76,48],[11,54,24,14,55,25],[16,45,15,14,46,16],[6,147,117,4,148,118],[6,73,45,14,74,46],[11,54,24,16,55,25],[30,46,16,2,47,17],[8,132,106,4,133,107],[8,75,47,13,76,48],[7,54,24,22,55,25],[22,45,15,13,46,16],[10,142,114,2,143,115],[19,74,46,4,75,47],[28,50,22,6,51,23],[33,46,16,4,47,17],[8,152,122,4,153,123],[22,73,45,3,74,46],[8,53,23,26,54,24],[12,45,15,28,46,16],[3,147,117,10,148,118],[3,73,45,23,74,46],[4,54,24,31,55,25],[11,45,15,31,46,16],[7,146,116,7,147,117],[21,73,45,7,74,46],[1,53,23,37,54,24],[19,45,15,26,46,16],[5,145,115,10,146,116],[19,75,47,10,76,48],[15,54,24,25,55,25],[23,45,15,25,46,16],[13,145,115,3,146,116],[2,74,46,29,75,47],[42,54,24,1,55,25],[23,45,15,28,46,16],[17,145,115],[10,74,46,23,75,47],[10,54,24,35,55,25],[19,45,15,35,46,16],[17,145,115,1,146,116],[14,74,46,21,75,47],[29,54,24,19,55,25],[11,45,15,46,46,16],[13,145,115,6,146,116],[14,74,46,23,75,47],[44,54,24,7,55,25],[59,46,16,1,47,17],[12,151,121,7,152,122],[12,75,47,26,76,48],[39,54,24,14,55,25],[22,45,15,41,46,16],[6,151,121,14,152,122],[6,75,47,34,76,48],[46,54,24,10,55,25],[2,45,15,64,46,16],[17,152,122,4,153,123],[29,74,46,14,75,47],[49,54,24,10,55,25],[24,45,15,46,46,16],[4,152,122,18,153,123],[13,74,46,32,75,47],[48,54,24,14,55,25],[42,45,15,32,46,16],[20,147,117,4,148,118],[40,75,47,7,76,48],[43,54,24,22,55,25],[10,45,15,67,46,16],[19,148,118,6,149,119],[18,75,47,31,76,48],[34,54,24,34,55,25],[20,45,15,61,46,16]],p.getRSBlocks=function(t,e){var r=p.getRsBlockTable(t,e);if(void 0==r)throw new Error("bad rs block @ typeNumber:"+t+"/errorCorrectLevel:"+e);for(var i=r.length/3,o=[],s=0;s<i;s++)for(var n=r[3*s+0],a=r[3*s+1],h=r[3*s+2],l=0;l<n;l++)o.push(new p(a,h));return o},p.getRsBlockTable=function(t,e){switch(e){case o.L:return p.RS_BLOCK_TABLE[4*(t-1)+0];case o.M:return p.RS_BLOCK_TABLE[4*(t-1)+1];case o.Q:return p.RS_BLOCK_TABLE[4*(t-1)+2];case o.H:return p.RS_BLOCK_TABLE[4*(t-1)+3];default:return}},m.prototype={get:function(t){var e=Math.floor(t/8);return 1==(this.buffer[e]>>>7-t%8&1)},put:function(t,e){for(var r=0;r<e;r++)this.putBit(1==(t>>>e-r-1&1))},getLengthInBits:function(){return this.length},putBit:function(t){var e=Math.floor(this.length/8);this.buffer.length<=e&&this.buffer.push(0),t&&(this.buffer[e]|=128>>>this.length%8),this.length++}};var C=[[17,14,11,7],[32,26,20,14],[53,42,32,24],[78,62,46,34],[106,84,60,44],[134,106,74,58],[154,122,86,64],[192,152,108,84],[230,180,130,98],[271,213,151,119],[321,251,177,137],[367,287,203,155],[425,331,241,177],[458,362,258,194],[520,412,292,220],[586,450,322,250],[644,504,364,280],[718,560,394,310],[792,624,442,338],[858,666,482,382],[929,711,509,403],[1003,779,565,439],[1091,857,611,461],[1171,911,661,511],[1273,997,715,535],[1367,1059,751,593],[1465,1125,805,625],[1528,1190,868,658],[1628,1264,908,698],[1732,1370,982,742],[1840,1452,1030,790],[1952,1538,1112,842],[2068,1628,1168,898],[2188,1722,1228,958],[2303,1809,1283,983],[2431,1911,1351,1051],[2563,1989,1423,1093],[2699,2099,1499,1139],[2809,2213,1579,1219],[2953,2331,1663,1273]];function w(){var t=!1,e=navigator.userAgent;if(/android/i.test(e)){t=!0;var r=e.toString().match(/android ([0-9]\.[0-9])/i);r&&r[1]&&(t=parseFloat(r[1]))}return t}var y=function(){var t=function(t,e){this._el=t,this._htOption=e};return t.prototype.draw=function(t){var e=this._htOption,r=this._el,i=t.getModuleCount();Math.floor(e.width/i),Math.floor(e.height/i);function o(t,e){var r=document.createElementNS("http://www.w3.org/2000/svg",t);for(var i in e)e.hasOwnProperty(i)&&r.setAttribute(i,e[i]);return r}this.clear();var s=o("svg",{viewBox:"0 0 "+String(i)+" "+String(i),width:"100%",height:"100%",fill:e.colorLight});s.setAttributeNS("http://www.w3.org/2000/xmlns/","xmlns:xlink","http://www.w3.org/1999/xlink"),r.appendChild(s),s.appendChild(o("rect",{fill:e.colorLight,width:"100%",height:"100%"})),s.appendChild(o("rect",{fill:e.colorDark,width:"1",height:"1",id:"template"}));for(var n=0;n<i;n++)for(var a=0;a<i;a++)if(t.isDark(n,a)){var h=o("use",{x:String(a),y:String(n)});h.setAttributeNS("http://www.w3.org/1999/xlink","href","#template"),s.appendChild(h)}},t.prototype.clear=function(){for(;this._el.hasChildNodes();)this._el.removeChild(this._el.lastChild)},t}(),D="svg"===document.documentElement.tagName.toLowerCase()?y:"undefined"==typeof CanvasRenderingContext2D?function(){var t=function(t,e){this._el=t,this._htOption=e};return t.prototype.draw=function(t){for(var e=this._htOption,r=this._el,i=t.getModuleCount(),o=Math.floor(e.width/i),s=Math.floor(e.height/i),n=['<table style="border:0;border-collapse:collapse;">'],a=0;a<i;a++){n.push("<tr>");for(var h=0;h<i;h++)n.push('<td style="border:0;border-collapse:collapse;padding:0;margin:0;width:'+o+"px;height:"+s+"px;background-color:"+(t.isDark(a,h)?e.colorDark:e.colorLight)+';"></td>');n.push("</tr>")}n.push("</table>"),r.innerHTML=n.join("");var l=r.childNodes[0],d=(e.width-l.offsetWidth)/2,u=(e.height-l.offsetHeight)/2;d>0&&u>0&&(l.style.margin=u+"px "+d+"px")},t.prototype.clear=function(){this._el.innerHTML=""},t}():function(){function t(){this._elImage.src=this._elCanvas.toDataURL("image/png"),this._elImage.style.display="block",this._elCanvas.style.display="none"}if(this._android&&this._android<=2.1){var e=1/window.devicePixelRatio,r=CanvasRenderingContext2D.prototype.drawImage;CanvasRenderingContext2D.prototype.drawImage=function(t,i,o,s,n,a,h,l,d){if("nodeName"in t&&/img/i.test(t.nodeName))for(var u=arguments.length-1;u>=1;u--)arguments[u]=arguments[u]*e;else void 0===l&&(arguments[1]*=e,arguments[2]*=e,arguments[3]*=e,arguments[4]*=e);r.apply(this,arguments)}}var i=function(t,e){this._bIsPainted=!1,this._android=w(),this._htOption=e,this._elCanvas=document.createElement("canvas"),this._elCanvas.width=e.width,this._elCanvas.height=e.height,t.appendChild(this._elCanvas),this._el=t,this._oContext=this._elCanvas.getContext("2d"),this._bIsPainted=!1,this._elImage=document.createElement("img"),this._elImage.alt="Scan me!",this._elImage.style.display="none",this._el.appendChild(this._elImage),this._bSupportDataURI=null};return i.prototype.draw=function(t){var e=this._elImage,r=this._oContext,i=this._htOption,o=t.getModuleCount(),s=i.width/o,n=i.height/o,a=Math.round(s),h=Math.round(n);e.style.display="none",this.clear();for(var l=0;l<o;l++)for(var d=0;d<o;d++){var u=t.isDark(l,d),c=d*s,g=l*n;r.strokeStyle=u?i.colorDark:i.colorLight,r.lineWidth=1,r.fillStyle=u?i.colorDark:i.colorLight,r.fillRect(c,g,s,n),r.strokeRect(Math.floor(c)+.5,Math.floor(g)+.5,a,h),r.strokeRect(Math.ceil(c)-.5,Math.ceil(g)-.5,a,h)}this._bIsPainted=!0},i.prototype.makeImage=function(){this._bIsPainted&&function(t,e){var r=this;if(r._fFail=e,r._fSuccess=t,null===r._bSupportDataURI){var i=document.createElement("img"),o=function(){r._bSupportDataURI=!1,r._fFail&&r._fFail.call(r)};return i.onabort=o,i.onerror=o,i.onload=function(){r._bSupportDataURI=!0,r._fSuccess&&r._fSuccess.call(r)},void(i.src="data:image/gif;base64,iVBORw0KGgoAAAANSUhEUgAAAAUAAAAFCAYAAACNbyblAAAAHElEQVQI12P4//8/w38GIAXDIBKE0DHxgljNBAAO9TXL0Y4OHwAAAABJRU5ErkJggg==")}!0===r._bSupportDataURI&&r._fSuccess?r._fSuccess.call(r):!1===r._bSupportDataURI&&r._fFail&&r._fFail.call(r)}.call(this,t)},i.prototype.isPainted=function(){return this._bIsPainted},i.prototype.clear=function(){this._oContext.clearRect(0,0,this._elCanvas.width,this._elCanvas.height),this._bIsPainted=!1},i.prototype.round=function(t){return t?Math.floor(1e3*t)/1e3:t},i}();function L(t,e){for(var r=1,i=function(t){var e=encodeURI(t).toString().replace(/\%[0-9a-fA-F]{2}/g,"a");return e.length+(e.length!=t?3:0)}(t),s=0,n=C.length;s<=n;s++){var a=0;switch(e){case o.L:a=C[s][0];break;case o.M:a=C[s][1];break;case o.Q:a=C[s][2];break;case o.H:a=C[s][3]}if(i<=a)break;r++}if(r>C.length)throw new Error("Too long data");return r}return(i=function(t,e){if(this._htOption={width:256,height:256,typeNumber:4,colorDark:"#000000",colorLight:"#ffffff",correctLevel:o.H},"string"==typeof e&&(e={text:e}),e)for(var r in e)this._htOption[r]=e[r];"string"==typeof t&&(t=document.getElementById(t)),this._htOption.useSVG&&(D=y),this._android=w(),this._el=t,this._oQRCode=null,this._oDrawing=new D(this._el,this._htOption),this._htOption.text&&this.makeCode(this._htOption.text)}).prototype.makeCode=function(t){this._oQRCode=new e(L(t,this._htOption.correctLevel),this._htOption.correctLevel),this._oQRCode.addData(t),this._oQRCode.make(),this._el.title=t,this._oDrawing.draw(this._oQRCode),this.makeImage()},i.prototype.makeImage=function(){"function"==typeof this._oDrawing.makeImage&&(!this._android||this._android>=3)&&this._oDrawing.makeImage()},i.prototype.clear=function(){this._oDrawing.clear()},i.CorrectLevel=o,i},t.exports=o()}});
//# sourceMappingURL=9.0e591299037740ceac44.js.map