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

//ip地址
jQuery.validator.addMethod("isIP", function(value, element) {
	if (value)
	{
		var chrnum = /^(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$/;
		return this.optional(element) || (chrnum.test(value));
	}
	else
	{
		return true;
	}
}, "IP地址不合理");

//QQ号码
jQuery.validator.addMethod("isQQ", function(value, element) {
	if (value)
	{
		var chrnum = /^[1-9]\d{4,}$/;
		return this.optional(element) || (chrnum.test(value));
	}
	else
	{
		return true;
	}
}, "QQ号码不合理");

//汉字
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

//中文及全角标点符号(字符)
jQuery.validator.addMethod("isCN", function(value, element) {
	if (value)
	{
		var chrnum = /^([\u3000-\u301e\ufe10-\ufe19\ufe30-\ufe44\ufe50-\ufe6b\uff01-\uffee]+)$/;
		return this.optional(element) || (chrnum.test(value));
	}
	else
	{
		return true;
	}
}, "只能输入中文及全角标点符号(字符)");

//手机号
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

//座机
jQuery.validator.addMethod("isTel", function(value, element) {
	if (value)
	{
		var chrnum = /^(0(10|21|22|23|[1-9][0-9]{2})(-|))?[0-9]{7,8}$/;
		return this.optional(element) || (chrnum.test(value));
	}
	else
	{
		return true;
	}
}, "请输入正确的座机号");

//电话
jQuery.validator.addMethod("isPhone", function(value,element) {
	if (value)
	{
		var mobile = /^1\d{10}$/;
		var tel = /^(0(10|21|22|23|[1-9][0-9]{2})(-|))?[0-9]{7,8}$/;
		return this.optional(element) || (tel.test(value) || mobile.test(value));
	}
	else 
	{
		return true;
	}

}, "请输入正确的电话号码");

//字母
jQuery.validator.addMethod("checkLetter", function(value, element) {
	if (value)
	{
		var chrnum = /^([a-z]+)$/i;
		return this.optional(element) || (chrnum.test(value));
	}
	else
	{
		return true;
	}
}, "只能输入字母");

//字母和数字
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

//字母数字汉字
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


// 验证值必须大于特定值(不能等于)
jQuery.validator.addMethod("gt", function(value, element, param) {
	return value > param;
}, $.validator.format("输入值必须大于{0}!"));

// 验证值必须大于特定值(等于)
jQuery.validator.addMethod("gte", function(value, element, param) {
	return value >= param;
}, $.validator.format("输入值必须不小于{0}!"));

// 验证值必须小于特定值(不能等于)
jQuery.validator.addMethod("lt", function(value, element, param) {
	return value < param;
}, $.validator.format("输入值必须小于{0}!"));

// 验证值必须小于特定值(不能等于)
jQuery.validator.addMethod("lte", function(value, element, param) {
	return value <= param;
}, $.validator.format("输入值必须不大于{0}!"));

// 验证值不允许与特定值等于
jQuery.validator.addMethod("notEqual", function(value, element, param) {
	return value != param;
}, $.validator.format("输入值不允许为{0}!"));

// 验证两次输入值是否不相同
jQuery.validator.addMethod("notEqualTo", function(value, element, param) {
return value != $(param).val();
}, $.validator.format("两次输入不能相同!"));

// 必须以特定字符串开头验证
jQuery.validator.addMethod("begin", function(value, element, param) {
	var begin = new RegExp("^" + param);
	return this.optional(element) || (begin.test(value));
}, $.validator.format("必须以 {0} 开头!"));

// 邮政编码验证
jQuery.validator.addMethod("zipCode", function(value, element) {
	var tel = /^\d{6}$/;
	return this.optional(element) || (tel.test(value));
}, "邮政编码格式错误!");

//身份证号
jQuery.validator.addMethod("isIDCard", function(value, element) {
	var tel = /^\d{15}(\d{2}[\dxX])?$/;
	return this.optional(element) || (tel.test(value));
}, "身份证格式错误!");



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

// 字符验证      
jQuery.validator.addMethod("stringCheck", function(value, element) {
        var chrnum = /^[\u4e00-\u9fa5\w]+$/g;
        return this.optional(element) || chrnum.test(value);      
}, "只能包括中文字、英文字母、数字和下划线"); 