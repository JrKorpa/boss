/**
 * 驳回
 * @returns {Boolean}
 */
function reCon(obj)
{
    var status = '<%$view->get_status()%>';
    if(status != 2){
        util.xalert('待审核状态才能进行驳回操作。');
    }
    else if ($("#rule_th").css("display") == 'none')
    {
        $("#rule_th").css("display", "");
        $('#pay_hexiao_show_list tr').each(function() {
            $(this).find("td").eq(5).html("<input type='text' class='form-control' name='overrule_reason[]' id='" + $(this).find("td").eq(0).html() + "'/>");
            $(this).find("td").eq(5).css("display", "")
        });
        util.xalert('请填写驳回原因');
    } else {
        $i = 0;
        $j = 0;
        var ids = new Array();
        var reasons = new Array();
        $("input[name='overrule_reason[]']").each(function(i, o) {
            if ($(o).val() != '')
            {
                if ($(o).val() != '' && no_tsStr($(o).val()))
                {
                    $j = 1;
                    return false;
                }
                $i++;
                ids.push($(o).attr('id'))
                reasons.push($(o).val())
            }
        });
        if ($j)
        {
            util.xalert("驳回原因不能包括特殊字符");
            return false;
        }
        ids = ids.join();
        reasons = reasons.join("#");
        if ($i == 0)
        {
            util.xalert('驳回原因至少填写一项');
        } else {
            var url = 'index.php?mod=finance&con=PayHexiao&act=reCon';
            var id = '<%$view->get_id()%>';
            $.post(url, {hx_id: id , ids:ids, reasons:reasons}, function(res) {
                if (!res.success)
                {
                    util.xalert(res.error);
                } else {
                    util.xalert("已经驳回");
                    util.retrieveReload(obj);
                }
            });
        }
    }
}

//不包含*,$,#特殊字符 还有什么特殊字符需要加进去就行
function no_tsStr(str)
{
	if(str.replace(/^[^*$#]+$/,''))	
	{
		return true;
	}
	return false;
}

function downloadCon(obj){
    var id = '<%$view->get_id()%>';
    location.href = "index.php?mod=finance&con=PayHexiao&act=downloadCon&id="+id;
}