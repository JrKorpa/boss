<?php
/**
 *  -------------------------------------------------
 *   @file		: ApiProModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	:
 *   @date		: 2015年1月19日
 *   @update	:
 *  -------------------------------------------------
 */
class UpdatePiliangModel extends Model {
    function __construct ($id,$strConn="")
    {
        parent::__construct($id,$strConn);
    }
    function uploadExcel() {
        
       $pdo = $this->db()->db();
        //判断是否上传
        
        if (!empty($_FILES))
        {
            //var_dump($_FILES);exit;
            $f = $_FILES['file']['tmp_name'];
            $file_array = explode(".",$_FILES['file']['name']);
            $file_extension = strtolower(array_pop($file_array)); 
            //判断格式是否正确
            if($file_extension != 'csv'){
               Util::jsonExit("上传的附件格式不正确！必须为csv格式！");
                
            }

                $handle = @fopen($f, "r");
                $n = 0;
                if ($handle) 
                {
                   while (!feof($handle))
                   {	
                           $buffer = fgets($handle, 4096);	
                            if($n == 0)
                            {
                                    $n++;
                                    continue;
                            }
                            $n++;
                          
                           if(!empty($buffer)) {
                               $a = explode(',',$buffer);
                               $goods_id = trim($a[0]);
                               $mo_sn = trim($a[1]);
                           }
                           
                           if ($goods_id != '' && $mo_sn != '')
                           {
                               $sql = "select count(*) from warehouse_goods where goods_id='{$goods_id}'";
                               $numrow = $this->db()->getOne($sql);
                               if ($numrow >= 1) {
                                   $sql = "update `warehouse_goods` set `mo_sn` = '$mo_sn' where goods_id = '$goods_id' limit 1";
                                    $rs = $this->db()->query($sql);
                                    
                                    if($rs == false){
                                        echo "该货号：".$goods_id."修改失败！<br/>";
                                    }
                               }else{
                                   Util::jsonExit("$goods_id 货号不正确或者不存在,请修改");
                                   
                               }
                                
                                
                           }
                   }
                   fclose($handle);
                   if ($n <3)
                   {
                           Util::jsonExit('上传的附为空！');
                           
                   }
                }
            }
            else
            {
                Util::jsonExit("请上传附件");
                   
            }
        exit('修改成功');
           
    }

    function uploadWGDExcel() {
        
       $pdo = $this->db()->db();
        //判断是否上传
        
        if (!empty($_FILES))
        {
            //var_dump($_FILES);exit;
            $f = $_FILES['file']['tmp_name'];
            $file_array = explode(".",$_FILES['file']['name']);
            $file_extension = strtolower(array_pop($file_array)); 
            //判断格式是否正确
            if($file_extension != 'csv'){
               Util::jsonExit("上传的附件格式不正确！必须为csv格式！");
            }

                $handle = @fopen($f, "r");
                $n = 0;
                if ($handle) 
                {
                   while (!feof($handle))
                   {	
                           $buffer = fgets($handle, 4096);	
                            if($n == 0)
                            {
                                    $n++;
                                    continue;
                            }
                            $n++;
                          
                           if(!empty($buffer)) {
                               $a = explode(',',$buffer);
                               $goods_id = trim($a[0]);
                               $goods_sn = trim($a[1]);
                               // gemx证书号：
                               $goods_gemx = trim($a[2]);
                           }
                           
                           if ($goods_id != '' && ($goods_sn != '' || $goods_gemx != ''))
                           {
                               $sql = "select count(*) from `warehouse_goods` where `cat_type`='裸石' and  `goods_id`='{$goods_id}'";
                               $numrow = $this->db()->getOne($sql);
                               if ($numrow >= 1) {
                                    $sql = "update `warehouse_goods` set ";
                                    if ($goods_sn != '') {
                                        $sql .= " `goods_sn` = '$goods_sn' ";
                                    }
                                    if ($goods_gemx != '') {
                                        if ($goods_sn != '') $sql .= ', ';
                                        $sql .= " `gemx_zhengshu` = '$goods_gemx' ";
                                    }                                    
                                    $sql .= " where `goods_id` = '$goods_id' limit 1;";

                                    $rs = $this->db()->query($sql);
                                    if($rs == false){
                                        Util::jsonExit("第".$n."行"."货号出错或非裸石！请修改");
                                    }
                               }else{
                                   Util::jsonExit("第".$n."行"."货号不正确，请修改");
                               }     
                           }else{
                                   Util::jsonExit("第".$n."行"."货号/款号/GEMX证书为空，请修改");
                           }
                   }
                   fclose($handle);
                }
            }
            else
            {
                Util::jsonExit("请上传附件");
                   
            }
        exit('修改款号/GEMX证书号成功！');
           
    }

}
?>