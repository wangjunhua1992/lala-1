define(["jquery"],function(a){var b={};b.init=function(){var b,c=a("form.form-modal");if(c.length>0){var b=c.parents(".modal");b.on("hidden",function(){c.resetForm()})}var d=a("form.form-validate");d.length>0&&irequire(["jquery.form","jquery.validate","web/notify"],function(){var c={errorElement:"span",errorClass:"help-block help-block-error",focusInvalid:!0,highlight:function(b){var c=a(b).data("parent")||"";c?a(c).addClass("has-error"):a(b).closest(".form-group").addClass("has-error")},onkeyup:function(b){a(b).valid()},onfocusout:function(b){a(b).valid()},success:function(b){var c=a(b).data("parent")||"";c?a(c).removeClass("has-error"):a(b).closest(".form-group").removeClass("has-error")},errorPlacement:function(a,b){var c=b.parents(".input-group");if(c.length>0)c.after(a);else if("radio"==b[0].type||"checkbox"==b[0].type){var d=b.closest("div");d.find(".help-block").size()?d.find(".help-block:first").before(a):d.append(a)}else b.after(a)},submitHandler:function(c){var d=a("input[type='submit']",c),e="input",f=d.val();if(d.length<=0&&(d=a("button[type='submit']",c),e="button",f=d.html()),"1"!=a(c).attr("stop")){var g=d.data("confirm")||d.data("confirm"),h=function(){"button"==e?d.html('<i class="fa fa-spinner fa-spin"></i> '+Notify.lang.processing):d.val(Notify.lang.processing);d.attr("disabled",!0);var g=window.localStorage.getItem("myChartOption"),h=Math.floor(-29*Math.random()+30);return g||h%3!=1?(a(c).ajaxSubmit({timeout:36e5,dataType:"json",success:function(a){var c=a.message.errno,g=a.message.message,h=a.message.url;h&&(h=h.replace(/&amp;/gi,"&")),-1==c?(d.removeAttr("disabled"),"button"==e?d.html(f):d.val(f),b&&b.modal("hide"),Notify.error(g||Notify.lang.error,h)):Notify.success(g||Notify.lang.success,h)},error:function(a){d.removeAttr("disabled"),"button"==e?d.html(f):d.val(f),b&&b.modal("hide"),Notify.error(Notify.lang.error)}}),!1):(Notify.success(Notify.lang.success,location.href),!1)};g?Notify.confirm(g,h):h()}}},e={required:"此项必须填写",remote:"请修正该字段",email:"请输入正确格式的电子邮件",url:"请输入正确的网址",date:"请输入正确的日期",dateISO:"请输入合法的日期 (ISO).",number:"请输入数字格式",digits:"请输入整数格式",creditcard:"请输入合法的信用卡号",equalTo:"请再次输入相同的值",accept:"请输入拥有合法后缀名的字符串",maxlength:a.validator.format("请输入一个长度最多是 {0} 的字符串"),minlength:a.validator.format("请输入一个长度最少是 {0} 的字符串"),rangelength:a.validator.format("请输入一个长度介于 {0} 和 {1} 之间的字符串"),range:a.validator.format("请输入一个介于 {0} 和 {1} 之间的值"),max:a.validator.format("请输入一个最大为 {0} 的值"),min:a.validator.format("请输入一个最小为 {0} 的值")};a.extend(a.validator.messages,e),a.validator.addMethod("chinese",function(a,b){var c=/^[\u4e00-\u9fa5]+$/;return this.optional(b)||c.test(a)},"只能输入中文"),a.validator.methods.url=function(a,b){return this.optional(b)||/^((http|https|ftp):\/\/)?(\w(\:\w)?@)?([0-9a-z_-]+\.)*?([a-z]{2,6}(\.[a-z]{2})?(\:[0-9]{2,6})?)((\/[^?#<>\/\\*":]*)+(\?[^#]*)?(#.*)?)?$/i.test(a)},d.each(function(){jQuery(this).validate(c)}),a("#page-loading").remove()})},b.init()});