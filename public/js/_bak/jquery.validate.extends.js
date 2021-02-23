/*
二、默认校验规则
(1)required:true               必输字段
(2)remote:"check.php"          使用ajax方法调用check.php验证输入值
(3)email:true                  必须输入正确格式的电子邮件
(4)url:true                    必须输入正确格式的网址
(5)date:true                   必须输入正确格式的日期
(6)dateISO:true                必须输入正确格式的日期(ISO)，例如：2009-06-23，1998/01/22 只验证格式，不验证有效性
(7)number:true                 必须输入合法的数字(负数，小数)
(8)digits:true                 必须输入整数
(9)creditcard:                 必须输入合法的信用卡号
(10)equalTo:"#field"           输入值必须和#field相同
(11)accept:                    输入拥有合法后缀名的字符串（上传文件的后缀）
(12)maxlength:5                输入长度最多是5的字符串(汉字算一个字符)
(13)minlength:10               输入长度最小是10的字符串(汉字算一个字符)
(14)rangelength:[5,10]         输入长度必须介于 5 和 10 之间的字符串")(汉字算一个字符)
(15)range:[5,10]               输入值必须介于 5 和 10 之间
(16)max:5                      输入值不能大于5
(17)min:10                     输入值不能小于10
*/

//增加用户名的验证
jQuery.validator.addMethod("checkName", function(value, element) {
	if (value)
	{
		var chrnum = /^([a-z0-9\u4e00-\u9fa5]+)$/i;
		return this.optional(element) || (chrnum.test(value));
	}
	else
	{
		return true;
	}
}, "只能输入数字、字母和汉字");

jQuery.validator.addMethod("checkLetter", function(value, element) {
	if (value)
	{
		var chrnum = /^([a-zA-Z]+)$/;
		return this.optional(element) || (chrnum.test(value));
	}
	else
	{
		return true;
	}
}, "只能输入字母");

jQuery.validator.addMethod("checkCN", function(value, element) {
	if (value)
	{
		var chrnum = /^([\u4e00-\u9fa5]+)$/;
		return this.optional(element) || (chrnum.test(value));
	}
	else
	{
		return true;
	}
}, "只能输入汉字");

jQuery.validator.addMethod("checkFields", function(value, element) {
	if (value)
	{
		var chrnum = /^([a-zA-Z0-9,_]+)$/;
		return this.optional(element) || (chrnum.test(value));
	}
	else
	{
		return true;
	}
}, "只能输入字母、数字、逗号和下划线");


jQuery.validator.addMethod("checkField", function(value, element) {
	if (value)
	{
		var chrnum = /^([a-zA-Z0-9_]+)$/;
		return this.optional(element) || (chrnum.test(value));
	}
	else
	{
		return true;
	}
}, "只能输入字母、数字和下划线");


jQuery.validator.addMethod("checkDot", function(value, element) {
	if (value)
	{
		var chrnum = /^([a-z\._]+)$/i;
		return this.optional(element) || (chrnum.test(value));
	}
	else
	{
		return true;
	}
}, "只能输入字母和点");

jQuery.validator.addMethod("isMobile", function(value, element) {
	if (value)
	{
		var chrnum = /^1\d{10}$/;
		return this.optional(element) || (chrnum.test(value));
	}
	else
	{
		return true;
	}
}, "请输入正确手机号");

jQuery.validator.addMethod("isFloat", function(value, element) {
	if (value)
	{
		var chrnum = /^\d+(\.\d+)?$/;
		return this.optional(element) || (chrnum.test(value));
	}
	else
	{
		return true;
	}
}, "请输入正数");

jQuery.validator.addMethod("is_Num", function(value, element) {
	if (value)
	{
		var chrnum = /^\d+[-\d]*$/;
		return this.optional(element) || (chrnum.test(value));
	}
	else
	{
		return true;
	}
}, "只能含有数字和-");

jQuery.validator.addMethod("checkCode", function(value, element) {
	if (value)
	{
		var chrnum = /^([A-Z\d]+)$/;
		return this.optional(element) || (chrnum.test(value));
	}
	else
	{
		return true;
	}
}, "只能输入字母和数字");

jQuery.validator.addMethod("isLetter", function(value, element) {
	if (value)
	{
		var chrnum = /[^\w\.\/\-]/g;
		return this.optional(element) || !(chrnum.test(value));
	}
	else
	{
		return true;
	}
}, "只能输入字母、数字、点号和中横线");