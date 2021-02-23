// JavaScript Document
function tosearch()
{
	$("#show").show();
	$.ajax({
		type: "POST",
		url: "last.php",
		data:$('#searchform').serialize(),// 你的formid
		//data : {'name':'nihao'},
		success: function(data) {
			//$("#commonLayout_appcreshi").parent().html(data);
			$("#show").hide();
			$("#showinfo").html(data);
			$("#showinfo").show();
		}
	});
}

function showpage(page)
{
	var pagenow = page;
	$("#pagenow").val(pagenow);
	tosearch();
}