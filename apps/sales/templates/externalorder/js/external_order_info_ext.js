$import(["public/js/select2/select2.min.js","public/js/jquery-tags-input/jquery.tagsinput.min.js"],function(){
    var info_form_id = 'external_order_info';//form表单id
    var info_form_base_url = 'index.php?mod=sales&con=ExternalOrder&act=';
    var count_id = "<%$order.country|default:''%>";
    var province_id="<%$ids[1]|default:''%>";
    var city_id="<%$ids[2]|default:''%>";
    var regional_id="<%$ids[3]|default:''%>";

    var order_pay_type='<%$order.order_pay_type|default:0%>';
    var shipping_id='<%$order.shipping_id|default:0%>';
    var from_type='<%$order.from_type|default:0%>';
    var cpatt = /([\u4e00-\u9fa5]+)/;
    var cpatts = /([\u4e00-\u9fa5,A-z]+)/;
    var obj = function(){
        var initElements = function(){
            util.setItem('orl',info_form_base_url+'GetGoodsList');
            util.setItem('formID','goods_info_list_searchf');//设定搜索表单id
            util.setItem('listDIV','goods_info_list_s');
            $('#'+info_form_id+' select').select2({
                placeholder: "请选择",
                allowClear: true
            }).live('change',function(e) {
                $(this).valid();
            });
            $('#'+info_form_id+' select[name=shop_type]').select2({
                placeholder: "请选择",
                allowClear: true,

            }).change(function(e){
                 $(this).valid();
                var url =info_form_base_url+"getShopList"
                var shop_type=$(this).val();
                $('#'+info_form_id+' select[name=shop_id]').select2('val','');
                $('#'+info_form_id+' textarea[name=address]').val('');
                $.post(url,{shop_type:shop_type}, function (data) {
                    $('#'+info_form_id+' select[name=shop_id]').empty().html(data);
                   
                });
                
                
            });
            var test = $("#ext_order_info_base input[type='checkbox']:not(.toggle, .make-switch)");
            if (test.size() > 0) {
                test.each(function () {
                if ($(this).parents(".checker").size() == 0) {
                    $(this).show();
                    $(this).uniform();
                }
              });
            }
            $("#"+info_form_id+" input[type='checkbox']").click(function(){
                var check =  $(this).attr('checked');
                var val= $(this).val();
                if(check=="checked"){
                    var url =info_form_base_url+"getGiftList";
                    $('#'+info_form_id+' input[name="gift_num['+val+']"]').css('display','inline');
                    var gift_remark=$('#'+info_form_id+' textarea[name="gift_remark').val();
                    var num=$('#'+info_form_id+' input[name="gift_num['+val+']"]').val();
                    
                    	
                    
                    $.post(url,{val:val}, function (data) {
                    var M =val;
                    var html ='<tr class="list_'+M+'" id='+M+'> <td>单号：<INPUT TYPE="text"  value="<%$order.taobao_order_id|default:""%>" style="margin-bottom:5px;" readonly="readonly"/><br/>'+
                    '名称：<INPUT TYPE="input" class="" NAME="ms[goods_name][]" style="margin-bottom:5px;" readonly="readonly"  value="'+data.name+'"><br/>款号：<INPUT TYPE="text" NAME="ms[goods_sn][]" style="margin-bottom:5px;" readonly="readonly"  value="'+data.goods_number+'"><br/>'+
                    '货号：<INPUT TYPE="text" NAME="ms[goods_id][]" readonly="readonly" id="'+M+'" style="margin-bottom:5px;"  value=""><span class="idc" style="display: none;color: red">货号出现重复请检查</span><br/><a href="javascript:;"   class="delete_goods">删除</a> </td> '+
                    '<td><div align="right">单价：<INPUT class="input-sm danjia" TYPE="text" readonly="readonly" NAME="ms[goods_price][]" style="margin-bottom:5px;"   value="'+data.sell_sprice+'" size="7"></div> <div align="right">优惠：<INPUT class="input-sm" readonly="readonly" TYPE="text" style="margin-bottom:5px;"  NAME="ms[favorable_price][]"  value="'+data.sell_sprice+'" size="7"></div> '+
                    '<div align="right">优惠后：<INPUT class="input-sm" readonly="readonly" TYPE="text" style="margin-bottom:5px;"  NAME="ms[zhenshi][]"  value="0" size="7"></div> <div align="right">数量：<INPUT class="input-sm" TYPE="text" style="margin-bottom:5px;"  NAME="ms[goods_number][]"  value="'+num+'" size="7"></div></td> <td>'+
                        '<div style="margin-bottom:5px;">'+
						'主石单颗重：<INPUT class="input-sm" TYPE="text" NAME="ms[stone][]" value="0"  size="6"></div>'+
						'<div style="margin-left: 5px;">'+
						'主石粒数：&nbsp;<INPUT class="input-sm" TYPE="text" NAME="ms[stone_num][]" value="0"  size="6"></div>'+
                        '<div style="margin-bottom: 5px;">主石颜色:&nbsp;'+
						'<select name="ms[stone_color][]" class="input-sm">'+
						   '<option value=""></option>'+
                            '<%foreach from=$goods_attr.color key=key item=val%>'+
							'<option value="<%$val%>"><%$val%></option>'+
							'<%/foreach%>'+
                        '</select>'+
						'</div>'+
                        '<div style="margin-bottom: 5px;">主石净度:&nbsp;'+
						'<select name="ms[stone_clear][]" class="input-sm">'+
						'<option value=""></option>'+
                        '<%foreach from=$goods_attr.clarity key=key item=val %>'+
							'<option value="<%$val%>"><%$val%></option>'+
						'<%/foreach%>'+
                    '</select>'+
					'</div>'+
                  '</td><td><div align="right"><div style="margin-bottom: 5px;">镶口：<INPUT class="input-sm" TYPE="text" NAME="ms[jietuoxiangkou][]" value="0" readonly  size="7"></div><div style="margin-bottom: 5px;">金重：<INPUT class="input-sm" TYPE="text" NAME="ms[gold_weight][]" value="0" size="7" readonly  ></div><div style="margin-bottom: 5px;">指圈：<INPUT class="input-sm"  TYPE="text" NAME="ms[finger][]" value="0" size="7"></div><div align="right">占用备货名额:<select name="ms[is_occupation][]" class="input-sm" disabled="disabled"><option value=""></option><option value="1">是</option><option value="0" selected>否</option></select></div></td> <td> <div align="right">材质:<select disabled="disabled" name="ms[gold][]" id="gold_type_'+M+'" class="input-sm"> <option label="默认" value="默认">默认</option> </select></div> <div align="right" style="margin-top: 5px;"><label class="label-sm">材质颜色:&nbsp;</label><select disabled="disabled" name="ms[jinse][]" class="input-sm"> <option label="默认" value="默认">默认</option> <option label="无" value="无">无</option> </select></div><div align="right">表面工艺:<select name="ms[biaomiangongyi][]" class="input-sm"><option value=""></option><%foreach from=$goods_attr.face_work key=key item=val %><option value="<%$val%>"><%$val%></option><%/foreach%></select></div> </td><td>'+
					 '<div style="margin-bottom: 5px;">'+
					    '证书类型：<select name="ms[zhengshuleibie][]" style="width:140px">'+
                                '<option value=""></option>'+
                               '<%foreach from=$goods_attr.cert item=val%>'+
								'<option value="<%$val%>"><%$val%></option>'+
							   '<%/foreach%>'+
                            '</select>'+
					'</div>'+
					'<div style="margin-bottom: 5px;">'+
                        '证&nbsp;书&nbsp;号：<INPUT class="input-sm" TYPE="text" NAME="ms[zhengshuhao][]" style="width:140px" value="" />'+
				   '</div>'+
					'<div style="margin-bottom: 5px;">	'+
                        '刻字内容：<INPUT class="input-sm" TYPE="text" NAME="ms[kezi][]" style="width:140px" value="">'+
					'</div>	'+
                    '<div style="margin-bottom: 5px;">'+
					'产品需求：<select name="ms[xiangqian][]"  style="width:140px">'+
                                '<option value=""></option>'+
                               '<%foreach from=$dd->getEnumArray("order.xiangqian") item=value%>'+
								'<option value="<%$value.label%>"><%$value.label%></option>'+
							   '<%/foreach%>'+
                            '</select>'+
                    '</div>'+
                    '</td><td><div align="right"><textarea NAME="ms[remark][]" ROWS="4" COLS="20">'+gift_remark+'</textarea></div></td> <td><input type="hidden" name="ms[is_zp][]" value='+data.is_zp+'></td> <td><input type="hidden" name="ms[is_xz][]" value='+data.is_xz+'></td> </tr>';
			   $('#table_goods').append(html);
               $('#'+info_form_id+' select').select2({
                    placeholder: "请选择",
                    allowClear: true
                })
               });
               
                }else{
                    $('#'+info_form_id+' input[name="gift_num['+val+']"]').css('display','none');
                    $('#'+val).remove(); 
                }
            });
            $('#table_goods').on('click','a.delete_goods',function(e){
                e.preventDefault();
                var l=$('#table_goods tr').length;
                if(l<=3){
                    util.xalert('至少要有一个货品禁止删除');
                }else{
                    $(this).parent().parent().remove();
                    //return false;
                }
            });
            //石重
            //$('#'+info_form_id+" input[name='ms[stone][]']").live('blur',function(){
            //    $(this).val($(this).val().replace(cpatts,''));
            //})
			//根据款号带出主石粒数
			$('#'+info_form_id+" input[name='ms[goods_sn][]']").die('blur');//移除live绑定的blue事件
            $('#'+info_form_id+" input[name='ms[goods_sn][]']").live('blur',function(){																						
				  var f = $('#'+info_form_id+" input[name='ms[goods_sn][]']").index($(this));
				  var style_sn = $.trim($(this).val());
				  if(style_sn!=""){
					  $.ajax({
							type: 'POST',
							url: info_form_base_url+'getStyleAttrsAjax',
							data: {'style_sn':style_sn},
							dataType: 'json',
							success: function (res) {				
								if(res.success ==1){
									var goods = res.data;									
									$('.list_'+f+' input[name="ms[stone_num][]"]').val(goods.zhushi_num).attr('readonly',true).change();

								}else{
									$('.list_'+f+' input[name="ms[stone_num][]"]').val(0).attr('readonly',false).change();
								}
							},
							error:function(res){
								alert('Ajax出错!');
							}
					 });
				  }else{
					  $('.list_'+f+' input[name="ms[stone_num][]"]').val(0).attr('readonly',false).change();
				  }
				 
			});
			//根据证书号带出证书类型
			$('#'+info_form_id+" input[name='ms[zhengshuhao][]']").die('blur');//移除live绑定的blue事件
            $('#'+info_form_id+" input[name='ms[zhengshuhao][]']").live('blur',function(){																						
				  var f = $('#'+info_form_id+" input[name='ms[zhengshuhao][]']").index($(this));
				  var zhengshuhao = $.trim($(this).val());
				  if(zhengshuhao!=""){
					  $.ajax({
							type: 'POST',
							url: 'index.php?mod=sales&con=AppOrderDetails&act=getDiamandInfoAjax',
							data: {'sn':zhengshuhao},
							dataType: 'json',
							success: function (res) {				
								if(res.error ==0){
									var goods = res.data;
									$('.list_'+f+' select[name="ms[zhengshuleibie][]"]').select2('val',goods.cert).attr("readonly",true).change();
									$('.list_'+f+' select[name="ms[stone_clear][]"]').select2('val',goods.clarity).attr("readonly",true).change();
                                    $('.list_'+f+' select[name="ms[stone_color][]"]').select2('val',goods.color).attr("readonly",true).change();
								}else{
									$('.list_'+f+' select[name="ms[zhengshuleibie][]"]').attr("readonly",false).change();
									$('.list_'+f+' select[name="ms[stone_clear][]"]').attr("readonly",false).change();
                                    $('.list_'+f+' select[name="ms[stone_color][]"]').attr("readonly",false).change();
								}
							},
							error:function(res){
								alert('Ajax出错!');
							}
					 });
				  }else{
					  $('.list_'+f+' select[name="ms[zhengshuleibie][]"]').attr("readonly",false).change();
					  $('.list_'+f+' select[name="ms[zhengshuleibie][]"]').attr("readonly",false).change();
					  $('.list_'+f+' select[name="ms[stone_clear][]"]').attr("readonly",false).change();
                      $('.list_'+f+' select[name="ms[stone_color][]"]').attr("readonly",false).change();
				  }
				 
			});
            //重复货号和款号不对应的判定
			$('#'+info_form_id+" input[name='ms[goods_id][]']").die('blur');//移除live绑定的blue事件
            $('#'+info_form_id+" input[name='ms[goods_id][]']").live('blur',function(){
					var f = $('#'+info_form_id+" input[name='ms[goods_id][]']").index($(this));					 
                    var goods_id=$(this).val();
	                var info = jQuery.parseJSON($('#gs_info').html());

                    if(info[goods_id]){
                        var infos = info[goods_id];
                    }else{
						/*
					    $('.list_'+f+' input[name="ms[goods_sn][]"]').val('');	
					    $('.list_'+f+' input[name="ms[stone][]"]').val(0).attr('readonly',false);
					    $('.list_'+f+' input[name="ms[stone_num][]"]').val(0).attr('readonly',false);
					    $('.list_'+f+' select[name="ms[stone_clear][]"]').select2('val','').change();
						$('.list_'+f+' select[name="ms[stone_color][]"]').select2('val','').change();
						$('.list_'+f+' input[name="ms[jietuoxiangkou][]"]').val('0').attr('readonly',false);
						$('.list_'+f+' input[name="ms[gold_weight][]"]').val('0');
						$('.list_'+f+' input[name="ms[finger][]"]').val('0');
						$('.list_'+f+' select[name="ms[zhengshuleibie][]"]').select2('val','').attr("readonly",false).change();
	
						$('.list_'+f+' input[name="ms[zhengshuhao][]"]').val('').attr('readonly',false);
						$('.list_'+f+' select[name="ms[gold][]"]').select2('val','').change();
						$('.list_'+f+' select[name="ms[jinse][]"]').select2('val','').change();
						$('.list_'+f+' select[name="ms[xiangqian][]"]').select2('val','').change();
						*/
						$('.list_'+f+' input[name="ms[goods_sn][]"]').attr('readonly',false);
					    return false;	
					}
	                //自动填充
                    infos.zhushiyanse= infos.zhushiyanse?infos.zhushiyanse:'无';
                    infos.zhushijingdu= infos.zhushijingdu?infos.zhushijingdu:'无';
                   
                    
					$('.list_'+f+' input[name="ms[goods_sn][]"]').val(infos.goods_sn).attr('readonly',true);
                    $('.list_'+f+' input[name="ms[stone][]"]').val(infos.zuanshidaxiao).attr('readonly',true);
					$('.list_'+f+' input[name="ms[stone_num][]"]').val(infos.zhushilishu).attr('readonly',true);
                    $('.list_'+f+' select[name="ms[stone_clear][]"]').select2('val',infos.zhushijingdu).change();
                    $('.list_'+f+' select[name="ms[stone_color][]"]').select2('val',infos.zhushiyanse).change();
					$('.list_'+f+' input[name="ms[jietuoxiangkou][]"]').val(infos.jietuoxiangkou).attr('readonly',true);
                    $('.list_'+f+' input[name="ms[gold_weight][]"]').val(infos.jinzhong);
                    $('.list_'+f+' input[name="ms[finger][]"]').val(infos.shoucun);
					$('.list_'+f+' select[name="ms[zhengshuleibie][]"]').select2('val',infos.zhengshuleibie).attr("readonly",infos.zhengshuleibie?true:false).change();

                    $('.list_'+f+' input[name="ms[zhengshuhao][]"]').val(infos.zhengshuhao).attr('readonly',infos.zhengshuleibie?true:false);
		            $('.list_'+f+' select[name="ms[gold][]"]').select2('val',infos.gold).change();
                    $('.list_'+f+' select[name="ms[jinse][]"]').select2('val',infos.jinse).change();
		            $('.list_'+f+' select[name="ms[xiangqian][]"]').select2('val',infos.tuo_type).change();
                               
					var arrgoodsid=new Array();
					var goods_id = $(this).val();
					$('#'+info_form_id+" .idc").hide();
					$('#'+info_form_id+" input[name='ms[goods_id][]']").each(function(i,e){
						if($(e).val()!=''){
							if($.inArray($(e).val(),arrgoodsid)!=-1){
								$(this).next().show();
							}
							arrgoodsid.push($(e).val());
						}
					});
				
            });

            $('#'+info_form_id+' select[name=shop_id]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                var shop_id=$(this).val();
                var shopurl = info_form_base_url+'getShopInfo';
                if(shop_id==''){
                    return false;
                }
                $.post(shopurl,{shop_id:shop_id},function(data){
                    if(data.success==1){
                        $('#'+info_form_id+' textarea[name=address]').val(data.error);
                    }
                });


            });

            var test = $("#"+info_form_id+" input[name='is_invoice']:not(.toggle, .star, .make-switch)");
            if (test.size() > 0) {
                test.each(function () {
                    if ($(this).parents(".checker").size() == 0) {
                        $(this).show();
                        $(this).uniform();
                    }
                });
            }
            var test = $("#"+info_form_id+" input[name='order_status']:not(.toggle, .star, .make-switch)");
            if (test.size() > 0) {
                test.each(function () {
                    if ($(this).parents(".checker").size() == 0) {
                        $(this).show();
                        $(this).uniform();
                    }
                });
            }


            $('#'+info_form_id+' select[name=distribution_type]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
               var distribution_type =  $(this).val();
                if(distribution_type==''){
                    $('#'+info_form_id+' .shop-div').css('display','none');
                    $('#'+info_form_id+' .area-div').css('display','none');
                    return false;
                }
                if(distribution_type==1){
                    $('#'+info_form_id+' textarea[name=address]').attr('readOnly',true);
                    $('#'+info_form_id+' .area-div').css('display','none');
                    $('#'+info_form_id+' .shop-div').css('display','block');
                }else{
                     $('#'+info_form_id+' textarea[name=address]').attr('readOnly',false);
                    //公司到客户
                    $('#'+info_form_id+' .shop-div').css('display','none');
                    $('#'+info_form_id+' .area-div').css('display','block');
                  
                }
            });
        };

        var handleForm = function(){
            //增加货品行逻辑
            $('#add_goods_tr').click(function(){
                var tr=new Array();
                $('#table_goods tr').each(function(i,e){
                    var a = $(this).attr('class');
                    if(a!==undefined){
                        var l=$(this).attr('class').length;
                        tr[i]=parseInt($(this).attr('class').substr(5,l));
                    }
                });
                var num=[];
                $(tr).each(function(i,e){
                    if(tr[i]!=undefined){
                        num.push(tr[i]);
                    }
                });
                var M = Math.max.apply(null, num)+1;
                var html ='<tr class="list_'+M+'"> <td>单号：<INPUT TYPE="text"  value="<%$order.taobao_order_id|default:""%>" style="margin-bottom:5px;" readonly="readonly"/><br/>名称：<INPUT TYPE="input" class="" NAME="ms[goods_name][]" style="margin-bottom:5px;"  value=""><br/>款号：<INPUT TYPE="text" NAME="ms[goods_sn][]" style="margin-bottom:5px;"  value=""><br/>货号：<INPUT TYPE="text" NAME="ms[goods_id][]" id="'+M+'" style="margin-bottom:5px;"  value=""><span class="idc" style="display: none;color: red">货号出现重复请检查</span><br/><a href="javascript:;"   class="delete_goods">删除</a> </td> <td><div align="right">单价：<INPUT class="input-sm danjia" TYPE="text" NAME="ms[goods_price][]" style="margin-bottom:5px;"   value="0" size="7"></div> <div align="right">优惠：<INPUT class="input-sm " TYPE="text" style="margin-bottom:5px;"  NAME="ms[favorable_price][]"  value="0" size="7"></div> <div align="right">优惠后：<INPUT class="input-sm" TYPE="text" style="margin-bottom:5px;"  NAME="ms[zhenshi][]"  value="0" size="7"></div> <div align="right">数量：<INPUT class="input-sm" TYPE="text" style="margin-bottom:5px;"  NAME="ms[goods_number][]" readonly value="1" size="7"></div></td> <td> '+
				'<div style="margin-bottom:5px;">'+
						'主石单颗重：<INPUT class="input-sm" TYPE="text" NAME="ms[stone][]" value="0"  size="6"></div>'+
						'<div style="margin-left: 5px;">'+
						'主石粒数：&nbsp;<INPUT class="input-sm" TYPE="text" NAME="ms[stone_num][]" value="0"  size="6"></div>'+
                        '<div style="margin-bottom: 5px;">主石颜色:&nbsp;'+
						'<select name="ms[stone_color][]" class="input-sm">'+
						   '<option value=""></option>'+
                            '<%foreach from=$goods_attr.color key=key item=val%>'+
							'<option value="<%$val%>"><%$val%></option>'+
							'<%/foreach%>'+
                        '</select>'+
						'</div>'+
                        '<div style="margin-bottom: 5px;">主石净度:&nbsp;'+
						'<select name="ms[stone_clear][]" class="input-sm">'+
						'<option value=""></option>'+
                        '<%foreach from=$goods_attr.clarity key=key item=val %>'+
							'<option value="<%$val%>"><%$val%></option>'+
						'<%/foreach%>'+
                    '</select>'+
					'</div>'+
				'</td> <td><div style="margin-bottom: 5px;">镶口：<INPUT class="input-sm" TYPE="text" NAME="ms[jietuoxiangkou][]" value="0"  size="7"></div><div style="margin-bottom: 5px;">金重：<INPUT class="input-sm" TYPE="text" NAME="ms[gold_weight][]" value="0" size="7" onBlur="checkGoldWeight(this)"  ></div><div style="margin-bottom: 5px;">指圈：<INPUT class="input-sm"  TYPE="text" NAME="ms[finger][]" value="0" size="7"></div><div align="right">占用备货名额:<select name="ms[is_occupation][]" class="input-sm"><option value=""></option><option value="1">是</option><option value="0" selected>否</option></select></div></td> <td> <div align="right">材质:<select name="ms[gold][]" id="gold_type_'+M+'" class="input-sm"><option value=""></option><%foreach from=$goods_attr.caizhi key=key item=val %><option value="<%$val%>"><%$val%></option><%/foreach%></select></select></div> <div align="left" style="margin-top: 5px;"><label class="label-sm">材质颜色:&nbsp;</label><select name="ms[jinse][]" class="input-sm"> <option value=""></option><%foreach from=$goods_attr.jinse key=key item=val %><option value="<%$val%>"><%$val%></option><%/foreach%></select></div><div align="right">表面工艺:<select name="ms[biaomiangongyi][]" class="input-sm"><option value=""></option><%foreach from=$goods_attr.face_work key=key item=val %><option value="<%$val%>"><%$val%></option><%/foreach%></select></div></td><td>'+'<div style="margin-bottom: 5px;">'+
					    '证书类型：<select name="ms[zhengshuleibie][]" style="width:140px">'+
                                '<option value=""></option>'+
                               '<%foreach from=$goods_attr.cert item=val%>'+
								'<option value="<%$val%>"><%$val%></option>'+
							   '<%/foreach%>'+
                            '</select>'+
					'</div>'+
					'<div style="margin-bottom: 5px;">'+
                        '证&nbsp;书&nbsp;号：<INPUT class="input-sm" TYPE="text" NAME="ms[zhengshuhao][]" style="width:140px" value="" />'+
				   '</div>'+
					'<div style="margin-bottom: 5px;">	'+
                        '刻字内容：<INPUT class="input-sm" TYPE="text" NAME="ms[kezi][]" style="width:140px" value="">'+
					'</div>	'+
                    '<div style="margin-bottom: 5px;">'+
					'产品需求：<select name="ms[xiangqian][]" id="xiangqian_'+M+'"  style="width:140px">'+
                                '<option value=""></option>'+
                               '<%foreach from=$dd->getEnumArray("order.xiangqian") item=value%>'+
								'<option value="<%$value.label%>"><%$value.label%></option>'+
							   '<%/foreach%>'+
                            '</select>'+
                    '</div>'+
'</td> <td><div align="right"><textarea NAME="ms[remark][]" ROWS="4" COLS="20"></textarea></div></td> <td><input type="hidden" name="ms[is_zp][]" value="0"></td> <td><input type="hidden" name="ms[is_xz][]" value="2"></td>  </tr>';
               $('#table_goods').append(html);
                $('#'+info_form_id+' select').select2({
                    placeholder: "请选择",
                    allowClear: true
                })
				if(from_type==71){
                   $('#'+info_form_id+' #xiangqian_'+M).select2('val',"工厂配钻，工厂镶嵌");
               }

            })
        };
        var initData = function(){
            if(order_pay_type){
                $('#'+info_form_id+' select[name=order_pay_type]').select2('val',order_pay_type);
            }
            if(shipping_id){
                $('#'+info_form_id+' select[name=express_id]').select2('val',shipping_id);
            }
            $('#'+info_form_id+' select[name="country_id"]').change(function(e){
                //debugger;
                $(this).valid();
                $('#'+info_form_id+' select[name="province_id"]').attr('readOnly',false).append('<option value=""></option>');
                $('#'+info_form_id+' select[name="city_id"]').empty().append('<option value=""></option>');
                $('#'+info_form_id+' select[name="regional_id"]').empty().append('<option value=""></option>');
                var t_v = $(this).val();
                if(t_v){
                    $.post(info_form_base_url+'getProvince',{count:t_v},function(data){
                        $('#'+info_form_id+' select[name="province_id"]').append(data);
                        if(province_id){
                            $('#'+info_form_id+' select[name="province_id"]').select2('val',province_id).change();
                        }
                        else
                        {
                            $('#'+info_form_id+' select[name="province_id"]').select2('val','').change();
                        }
                    });
                }
                else
                {
                    $('#'+info_form_id+' select[name="province_id"]').select2('val','').attr('readOnly',false).change();
                }
            });

            //点省出现市的列表
            $('#'+info_form_id+' select[name="province_id"]').change(function(e){
                $(this).valid();
                $('#'+info_form_id+' select[name="city_id"]').attr('readOnly',false).html('<option value=""></option>');
                $('#'+info_form_id+' select[name="regional_id"]').html('<option value=""></option>');


                var t_v = $(this).val();
                if(t_v){
                    $.post(info_form_base_url+'getProvince',{count:t_v},function(data){
                        $('#'+info_form_id+' select[name="city_id"]').append(data);
                        if(city_id){
                            $('#'+info_form_id+' select[name="city_id"]').select2('val',city_id).change();
                        }
                        else
                        {
                            $('#'+info_form_id+' select[name="city_id"]').select2('val','').change();
                        }
                    });
                }
                else
                {
                    $('#'+info_form_id+' select[name="city_id"]').select2('val','').attr('readOnly',false).change();
                }
            });
            //点市出现区的列表
            $('#'+info_form_id+' select[name="city_id"]').change(function(e){
                $(this).valid();
                $('#'+info_form_id+' select[name="regional_id"]').attr('readOnly',false).html('<option value=""></option>');
                var t_v = $(this).val();
                if(t_v){
                    $.post(info_form_base_url+'getProvince',{count:t_v},function(data){
                        $('#'+info_form_id+' select[name="regional_id"]').append(data);
                        if(regional_id){
                            $('#'+info_form_id+' select[name="regional_id"]').select2('val',regional_id).change();
                        }
                        else
                        {
                            $('#'+info_form_id+' select[name="regional_id"]').select2('val','').change();
                        }
                    });
                }
                else
                {
                    $('#'+info_form_id+' select[name="regional_id"]').select2('val','').attr('readOnly',false).change();
                }
            });
                if(count_id){
                    $('#'+info_form_id+' select[name="country_id"]').select2('val',count_id).change();
                }
            $('#'+info_form_id+' select[name="distribution_type"]').select2('val',2).change();

            //追加逻辑
            var flag = $('#'+info_form_id+' input[name=flag]').val();
            if(flag==1){
                $('#ext_order_info_base').css('display','none');
            }
            //京东的特殊需求
            //1不默认选中一个白色手提袋
            //2默认选中"工厂配石，和工厂镶嵌"
            if(from_type==71){
                $('#'+info_form_id+' input[name="gift_id[]"][value=5]').click();
                $('#'+info_form_id+' select[name="ms[xiangqian][]"]').select2('val',"工厂配钻，工厂镶嵌");
            }
            //价格计算

            $('#table_goods').on('blur','.danjia',function(e){
                var v = parseInt($(this).val());
                var y =parseInt($(this).parent().next().children('input').val());
                if(v==''){
                    return false;
                }
                if(y==''){
                    return false;
                }

                if((v-y)<0){
                    util.xalert('优惠价格不能比商品大');
                    return false;
                }
                $(this).parent().next().next().children('input').val(v-y);
            })


            $('#s_goods_info').click(function(){
                //搜集表单数据
                var s_style_sn= $('#'+info_form_id+' input[name="s_style_sn"]').val();
                var s_stone=$('#'+info_form_id+' input[name="s_stone"]').val();
                var s_stone_color=$('#'+info_form_id+' select[name="s_stone_color"]').val();
                var s_stone_clear= $('#'+info_form_id+' select[name="s_stone_clear"]').val();
                var s_zhengshuhao=$('#'+info_form_id+' input[name="s_zhengshuhao"]').val();
                var s_finger=$('#'+info_form_id+' input[name="s_finger"]').val();
                var s_jinzhong_begin=$('#'+info_form_id+' input[name="s_jinzhong_begin"]').val();
                var s_jinzhong_end= $('#'+info_form_id+' input[name="s_jinzhong_end"]').val();
                var s_zhushi_begin=$('#'+info_form_id+' input[name="s_zhushi_begin"]').val();
                var s_zhushi_end= $('#'+info_form_id+' input[name="s_zhushi_end"]').val();
                var s_caizhi=$('#'+info_form_id+' select[name="s_caizhi"]').val();
                var s_jinse=$('#'+info_form_id+' select[name="s_jinse"]').val();
				$('body').modalmanager('loading');//进度条和遮罩
                $.ajax({
                    type: "POST",
                    async:true,
                    url: info_form_base_url+'GetGoodsList',
                    data: {s_style_sn:s_style_sn,s_stone:s_stone,s_stone_color:s_stone_color,s_stone_clear:s_stone_clear,s_zhengshuhao:s_zhengshuhao,s_jinzhong_begin:s_jinzhong_begin,s_jinzhong_end:s_jinzhong_end,s_zhushi_begin:s_zhushi_begin,s_zhushi_end:s_zhushi_end,s_caizhi:s_caizhi,s_jinse:s_jinse,s_finger:s_finger},
                    success:function(data){
						$('body').modalmanager('removeLoading');//关闭进度条
						$('#goods_info_list_s').html(data);
						//$('#s_close_f').trigger('click');
                    }
            })
            });
            $('#'+info_form_id+' select[name="s_caizhi"]').change(function(){
                if($(this).val()==''){
                    $('#'+info_form_id+' select[name="s_jinse"]').select2('val','');
                    $('#'+info_form_id+' select[name="s_jinse"]').attr('readonly','readonly');
                    return false;
                }
                $('#'+info_form_id+' select[name="s_jinse"]').removeAttr('readonly');
            });
            $('#'+info_form_id+' select[name="s_jinse"]').attr('readonly','readonly');
        };
        	
        return {
            init:function(){
                initElements();//处理表单元素
                handleForm();//处理表单验证和提交
                initData();//处理表单重置和其他特殊情况
            }
        }
    }();
    obj.init();
});