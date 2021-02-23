<?php
/**
 *  -------------------------------------------------
 *   @file		: TableView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: zhangruiying
 *   @date		:2015/5/22
 *   @update	:
 *	 @description:生成列表页面，可支持复选。单选，表头字段排序
 *  -------------------------------------------------
 */
 class TableView
 {
	 private $tableId='';
	 private $tableStr='';
	 private $radio=false;//默认不显示
	 private $checkBox=false;//默认不显示
	 private $sortfld='';
	 private $desc='';
	 private $title='';
	 private $pk='';
	 function __construct($table_id='',$pk='')
	 {
		global $_REQUEST;
		$this->tableId=$table_id;
		$this->sortfld=isset($_REQUEST['orderby'])?$_REQUEST['orderby']:'';
		$this->desc=isset($_REQUEST['desc_or_asc'])?$_REQUEST['desc_or_asc']:'';
		$this->pk=$pk;
		$this->title=$pk;
	 }
	 /*在列表显示复选框
	 *name 复选框NAME
	 *value 复选框
	 */
	 function CheckBox($name,$value)
	 {
		$this->checkBox=array(
			'name'=>$name,
			'value'=>$value
			);
	 }
	 /*在列表显示复选框
	 *name 单接按钮组名称
	 *value 单选按钮值的列名field
	 */
	 function Radio($name,$value)
	 {
		$this->radio=array(
			'name'=>$name,
			'value'=>$value
			);
	 }
	 /**
	 *---------
	 *设置要显示的字段
	 *排序字段
	 *显示字段是否允许排序
	 * ---------
	 */
	 function SetFieldConf($arr=array())
	 {
		$this->fileds=$arr;
	 }
	 function ShowList($data=array(),$page,$count='')
	 {
		 if(empty($data))
		 {
			return $data;
		 }
		 else
		 {
			$pa='';
		    if($page)
			{
				$pa=Util::page($page);
			}
			$str=file_get_contents('./frame/template/list.html');
			$title=$this->GetTableTitle();
			$list=$this->GetTableList($data);
			$js=$this->checkBoxJS();
			if($count !=''){
				$count = "<div style='font-weight:bold;font-height:20px;font-size:15px;'>货品总数量：".$count."</div>";
			}
			$str=str_replace(array('{TITLE}','{PA}','{LIST}','{SCRIPT}','{COUNT}'),array($title,$pa,$list,$js,$count),$str);
			$this->tableStr=$str;

		 }
		 return $this->tableStr;
	 }
	 function GetTableList($data)
	 {
		 $str='';

		 if(!empty($this->fileds))
		 {
			 $arr=array_column($this->fileds,'field');
			 foreach($data['data'] as $key=>$v)
			{
				$str.="<tr data-id=\"".$this->tableId."_".$v[$this->pk]."\" data-title=\"".$v[$this->title]."\">";
				if($this->checkBox!=false)
				{
					$checkbox_v=isset($v[$this->checkBox['value']])?$v[$this->checkBox['value']]:'';
					$str.='<td>
					<input class="checkboxes" type="checkbox" name="'.$this->checkBox['name'].'" value="'.$checkbox_v.'" />
					</td>';
				}
				foreach($arr as $val)
				{
					$value=isset($v[$val])?$v[$val]:'';
					$str.="<td>".$value."</td>";
				}
				$str.="</tr>";
			}

		 }
		 return $str;

	 }
	 function GetTableTitle()
	 {
		$title='';
		if($this->checkBox!=false)
		{
				$title="<th>
				<input type=\"checkbox\" class=\"group-checkable\" data-set=\"#".$this->tableId." .checkboxes\" />
				</th>";
		}
		 if(!empty($this->fileds))
		 {
			 foreach($this->fileds as $key=>$v)
			 {
				$t='';
				$temp=$v['title'];
				if(isset($v['is_sort']) and 1==$v['is_sort'])
				{
					$t="onclick=\"util.fieldSort(this,'".$v['sort']."','')\" onmouseover=\"this.style.cursor='pointer'\"";
					if(!empty($this->sortfld) and !empty($this->desc))
					{
						if($this->sortfld==$v['sort'] and $this->desc=='ASC')
						{
							$img="/public/img/down.png";
						}
						elseif($this->sortfld==$v['sort'] and $this->desc=='DESC')
						{
							$img="/public/img/up.png";;
						}
						else
						{
							$img="/public/img/order.png";
						}
					}
					else
					{
						$img="/public/img/order.png";
					}
					$temp=$v['title']."<img src=\"".$img."\" />";
				}
				$title.="<th ".$t.">".$temp."</th>";
			 }
		}
		return $title;
	 }
	 function setTitle($title)
	 {
		$this->title=$title;
	 }
	 //复选JS
	 function checkBoxJS()
	 {
		$str='<script type="text/javascript">';
		if($this->checkBox!=false)
		{
			$str.="
			  var test = $(\"#".$this->tableId." input[type='checkbox']:not(.toggle, .make-switch)\");
				if (test.size() > 0) {
					test.each(function () {
					if ($(this).parents(\".checker\").size() == 0) {
						$(this).show();
						$(this).uniform();
					}
				  });
				}
			  $('#".$this->tableId." .group-checkable').change(function () {
				var set = $(this).attr(\"data-set\");
					var checked = $(this).is(\":checked\");
					$(set).each(function () {
						if (checked) {
							$(this).attr(\"checked\", true);
							$(this).parents('tr').addClass(\"active\");
						} else {
							$(this).attr(\"checked\", false);
							$(this).parents('tr').removeClass(\"active\");
						}
					});
					$.uniform.update(set);
				});
				$('#".$this->tableId."').on('change', 'tbody tr .checkboxes', function(){
				$(this).parents('tr').toggleClass(\"active\");
			  });";
		}
		$str.="util.hover();</script>";
		return $str;
	 }
	 function SetSort($filed='',$order='ASC')
	 {
		 if($filed)
		 {
			 $this->sortfld=$filed;
		 }
		 if($order)
		 {
			$this->desc=$order;
		 }
	 }



 }

?>