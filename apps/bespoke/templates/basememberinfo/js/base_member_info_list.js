//分页
function base_member_info_search_page(url){
	util.page(url);
}

//匿名回调
$import("public/js/select2/select2.min.js",function(){
	util.setItem('orl','index.php?mod=bespoke&con=BaseMemberInfo&act=search');
	util.setItem('formID','base_member_info_search_form');//设定搜索表单id
	util.setItem('listDIV','base_member_info_search_list');//设定列表数据容器id

	var baseMemberInfoObj = function(){
		var initElements = function(){
			$('#base_member_info_search_form select[name="member_name"]').select2({
				placeholder: "请选择",
				allowClear: true
			});
			
			$('#base_member_info_search_form select[name="member_type"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});

			$('#base_member_info_search_form select[name="cause_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
				
				var cause_id =$("#cause_id").val();
				//$('#diamond_info_info select[name="department_id"]').select2('val','');
				$('#department_id').select2('val','');
					
				$.post('index.php?mod=bespoke&con=BaseMemberInfo&act=getDepartmentInfo',{cause_id:cause_id},function(data){
					//alert(data.content);
					$('#base_member_info_search_form select[name="department_id"]').html(data.content);
					//$("#department_id").html(data.content);
				})
			});

			$('#base_member_info_search_form select[name="department_id"]').select2({
				placeholder: "请选择销售渠道",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
        };
		var handleForm = function(){
			util.search();
        };
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			base_member_info_search_page(util.getItem('orl'));

			$('#base_member_info_search_form :reset').on('click',function(){
					$('#base_member_info_search_form select[name="member_type"]').select2("val",'');
					$('#base_member_info_search_form select[name="cause_id"]').select2("val",'');
					$('#base_member_info_search_form select[name="department_id"]').select2("val",'');
					
			})
		};
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();
	baseMemberInfoObj.init();
});