webpackJsonp([69],{"+PAG":function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var s={data:function(){return{preLoading:!0,mobile:"",isverifymobile:0,yzm:"",password:"",repassword:"",getCode:!0,code:{text:"获取验证码",downcount:60},captcha:"",captcha_img:""}},components:{PublicHeader:a("Cz8s").a},methods:{onLoad:function(){var t=this;t.util.request({url:"wmall/member/profile/info"}).then(function(e){t.preLoading=!1;var a=e.data.message;if(a.errno)return t.$toast(a.message),!1;t.mobile=a.message.mobile,t.isverifymobile=a.message.isverifymobile}),t.onRefreshCaptcha()},onGetCode:function(){var t=this;return!!this.getCode&&(this.mobile?this.util.isValidMobile(this.mobile)?this.captcha?void this.util.request({url:"system/common/code",method:"POST",data:{mobile:this.mobile,captcha:this.captcha}}).then(function(e){var a=e.data.message;if(a.errno)return t.$toast(a.message),!1;t.code.text=t.code.downcount+"秒后重新获取";var s=setInterval(function(){t.code.downcount--,t.code.downcount<=0?(clearInterval(s),t.code.text="获取验证码",t.code.downcount=60,t.getCode=!0):t.code.text=t.code.downcount+"秒后重新获取"},1e3);t.$toast("验证码发送成功, 请注意查收")}):(this.$toast("请输入图形验证码"),!1):(this.$toast("手机号格式错误"),!1):(this.$toast("手机号不能为空"),!1))},onSubmit:function(){var t=this;if(!this.mobile)return this.$toast("手机号不能为空"),!1;if(!this.util.isValidMobile(this.mobile))return this.$toast("手机号格式错误"),!1;if(1==this.isverifymobile&&!this.yzm)return this.$toast("请输入短信验证码"),!1;if(!this.password)return this.$toast("密码不能为空"),!1;var e=this.password.length;if(e<8||e>20)return this.$toast("请输入8-20位密码"),!1;if(!/[0-9]+[a-zA-Z]+[0-9a-zA-Z]*|[a-zA-Z]+[0-9]+[0-9a-zA-Z]*/.test(this.password))return this.$toast("密码必须由数字和字母组合"),!1;if(!this.repassword)return this.$toast("请重复输入密码"),!1;if(this.password!=this.repassword)return this.$toast("两次密码输入不一致"),!1;var a={mobile:this.mobile,code:this.yzm,password:this.password,repassword:this.repassword,force:0};this.util.request({url:"wmall/member/profile/bind",data:a}).then(function(e){var s=e.data.message;if(s.errno){if(-2!=s.errno)return t.util.$toast(s.message),!1;t.$dialog.confirm({message:"该手机号已绑定其他账号，确定进行合并吗",confirmButtonColor:"#ff2d4b"}).then(function(e){a.force=1,t.util.request({url:"wmall/member/profile/bind",data:a}).then(function(e){var a=e.data.message;if(a.errno)return t.util.$toast(a.message),!1;t.util.$toast(a.message,t.util.getUrl({path:"/pages/member/profile"}),1500,"replace")})}).catch(function(t){console.log("cancel")})}else t.util.$toast(s.message,t.util.getUrl({path:"/pages/member/profile"}),1500,"replace")})},onRefreshCaptcha:function(){var t=this;this.util.request({url:"wmall/member/profile/captcha"}).then(function(e){t.captcha_img=e.data.message.message.captcha})}},mounted:function(){this.onLoad()}},o={render:function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{attrs:{id:"memberBind"}},[a("public-header",{attrs:{title:"更改绑定手机号"}}),t._v(" "),a("div",{staticClass:"content"},[a("van-cell-group",[a("van-field",{attrs:{label:"手机号",placeholder:"请输入手机号"},model:{value:t.mobile,callback:function(e){t.mobile=e},expression:"mobile"}}),t._v(" "),1==t.isverifymobile?[a("van-field",{attrs:{type:"number",label:"图形验证码",placeholder:"请输入图形验证码"},model:{value:t.captcha,callback:function(e){t.captcha=e},expression:"captcha"}}),t._v(" "),a("van-field",{attrs:{label:"短信验证码",placeholder:"请输入短信验证码"},model:{value:t.yzm,callback:function(e){t.yzm=e},expression:"yzm"}},[60==t.code.downcount?a("van-button",{attrs:{slot:"button",size:"small",type:"primary"},on:{click:function(e){return t.onGetCode()}},slot:"button"},[t._v(t._s(t.code.text))]):a("van-button",{attrs:{slot:"button",size:"small",type:"primary",disabled:""},slot:"button"},[t._v(t._s(t.code.text))])],1)]:t._e(),t._v(" "),a("van-field",{attrs:{type:"password",label:"登录密码",placeholder:"请输入您的登录密码"},model:{value:t.password,callback:function(e){t.password=e},expression:"password"}}),t._v(" "),a("van-field",{attrs:{type:"password",label:"确认密码",placeholder:"请输入确认登录密码"},model:{value:t.repassword,callback:function(e){t.repassword=e},expression:"repassword"}})],2),t._v(" "),1==t.isverifymobile?[a("div",{staticClass:"email-img",on:{click:t.onRefreshCaptcha}},[a("img",{attrs:{src:t.captcha_img,alt:""}})])]:t._e(),t._v(" "),a("div",{staticClass:"submit"},[a("van-button",{attrs:{size:"large",type:"danger"},on:{click:function(e){return t.onSubmit()}}},[t._v("立即绑定")])],1)],2),t._v(" "),a("transition",{attrs:{name:"loading"}},[t.preLoading?a("iloading"):t._e()],1)],1)},staticRenderFns:[]};var i=a("VU/8")(s,o,!1,function(t){a("i7t3")},null,null);e.default=i.exports},i7t3:function(t,e){}});
//# sourceMappingURL=69.8047f694d376387ac309.js.map