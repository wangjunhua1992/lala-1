webpackJsonp([38],{YnGz:function(t,e){},ea5B:function(t,e,i){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var a=i("Cz8s"),n=i("deIj"),s={components:{publicHeader:a.a},data:function(){return{timePickerShow:!1,currentDate:"",TimeType:"",data:{title:"",guest_num:"",starttime:"0:00",endtime:"0:00",prefix:"",notify_num:""},showPreLoading:!0}},methods:{onToggleTimePicker:function(t){this.timePickerShow=!this.timePickerShow,"s"!=t&&"e"!=t||(this.TimeType=t)},onConfirmTime:function(t){t&&("s"==this.TimeType?this.data.starttime=t:this.data.endtime=t),this.onToggleTimePicker()},onSubmit:function(){return this.data.title?this.data.guest_num?this.data.notify_num?void Object(n.c)({vue:this,url:"manage/tangshi/assign/queue_post",data:this.data,redirect:this.util.getUrl({path:"/pages/tangshi/assign"}),message:"添加队列成功"}):(this.util.$toast("提前通知人数必须大于0","",1e3),!1):(this.util.$toast("客人数量少于多少人必须大于0","",1e3),!1):(this.util.$toast("请输入队列名称","",1e3),!1)}},mounted:function(){this.showPreLoading=!1}},l={render:function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{attrs:{id:"assign-queue"}},[i("public-header",{attrs:{title:"添加队列"}}),t._v(" "),i("div",{staticClass:"content"},[i("van-cell-group",[i("van-field",{attrs:{label:"队列名称",placeholder:"请填写队列名称","input-align":"right"},model:{value:t.data.title,callback:function(e){t.$set(t.data,"title",e)},expression:"data.title"}}),t._v(" "),i("van-field",{attrs:{label:"排入队列",placeholder:"客人数量少于多少人","input-align":"right"},model:{value:t.data.guest_num,callback:function(e){t.$set(t.data,"guest_num",e)},expression:"data.guest_num"}})],1),t._v(" "),i("van-cell-group",{staticClass:"margin-10-t"},[i("van-cell",{staticClass:"flex-lr",attrs:{title:"排队时间"}},[i("div",{staticClass:"flex",attrs:{slot:"right-icon"},slot:"right-icon"},[i("span",{on:{click:function(e){t.onToggleTimePicker("s")}}},[t._v(t._s(t.data.starttime))]),t._v(" "),i("span",{staticClass:"padding-10-lr"},[t._v("至")]),t._v(" "),i("span",{on:{click:function(e){t.onToggleTimePicker("e")}}},[t._v(t._s(t.data.endtime))])])])],1),t._v(" "),i("van-cell-group",{staticClass:"margin-10-t"},[i("van-field",{attrs:{label:"队列前缀",placeholder:"请填写编号前缀","input-align":"right"},model:{value:t.data.prefix,callback:function(e){t.$set(t.data,"prefix",e)},expression:"data.prefix"}}),t._v(" "),i("van-field",{attrs:{label:"提前通知",placeholder:"请填写提前通知人数","input-align":"right"},model:{value:t.data.notify_num,callback:function(e){t.$set(t.data,"notify_num",e)},expression:"data.notify_num"}})],1),t._v(" "),i("div",{staticClass:"assign-but"},[i("van-button",{staticClass:"bg-info font-16",attrs:{size:"normal",block:""},on:{click:t.onSubmit}},[t._v("点击添加")])],1)],1),t._v(" "),t.showPreLoading?i("iloading"):t._e(),t._v(" "),i("van-popup",{attrs:{position:"bottom"},model:{value:t.timePickerShow,callback:function(e){t.timePickerShow=e},expression:"timePickerShow"}},[i("van-datetime-picker",{attrs:{type:"time","min-hour":0,"max-hour":23},on:{confirm:t.onConfirmTime,cancel:t.onToggleTimePicker},model:{value:t.currentDate,callback:function(e){t.currentDate=e},expression:"currentDate"}})],1)],1)},staticRenderFns:[]};var o=i("VU/8")(s,l,!1,function(t){i("YnGz")},null,null);e.default=o.exports}});
//# sourceMappingURL=38.0b730a0e4cb76f310bff.js.map