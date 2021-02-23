$import(function(){
	$('#user_info_tab_5>ul>li>div>div.title').on('click',function(e){
		e.preventDefault();//禁止冒泡
		var obj = $(this).next();
		obj[0].style.display = obj[0].style.display=='block' ? 'none': 'block';
	});
});