<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OutLinkController
 *
 * @author yangfuyou
 */
class WarehouseListReportController extends Controller
{
    protected $whitelist = array("downthundrpt");
        //put your code here
    public function index ($params)
	{
        $path = '/cron/warehouse_shipping/kucun/';
        $fullPath = KELA_ROOT.$path;
        $myDirectory=opendir($fullPath);

        $cnt=0;
        while ($filename=readdir($myDirectory) )
        {
            $filename =  utf8_encode($filename);
        // drop (exclude) the entry if it includes index or starts with a dot
            if (  preg_match("/xls/",strtolower($filename)) 
            || preg_match("/xlsx/",strtolower($filename))
            || preg_match("/csv/",strtolower($filename))
            
            ) 
            { 
            $DirArray[$cnt]['fname']=$filename;
            $DirArray[$cnt]['type']=strtoupper(filetype($fullPath.$filename));
            $DirArray[$cnt]['size']=$this->formatFileSize(filesize($fullPath.$filename));
            $DirArray[$cnt]['mtime']=date('Y-m-d H:i:s',filemtime($fullPath.$filename));
            $cnt=$cnt+1;
            }
        }
        if(empty($DirArray)){
            echo '没有更新下载文件！';exit;
        }
        $DirArray = $this->multi_array_sort($DirArray,'mtime');
        closedir($myDirectory);
        $this->render('warehouse_list_report_list.html',array('DirArray'=>$DirArray,'path'=>$path));
    }

    function formatFileSize($fileSize)
    {
        $unit = array(' Bytes', ' KB', ' MB', ' GB', ' TB', ' PB', ' EB', ' ZB', ' YB');
        $i = 0;
        $inv = 1 / 1024;

        while($fileSize >= 1024 && $i < 8)
        {
            $fileSize *= $inv;
            ++$i;
        }
        $fileSizeTmp = sprintf("%.2f", $fileSize);
        return ($fileSizeTmp - (int)$fileSizeTmp ? $fileSizeTmp : $fileSize) . $unit[$i];
    }

    function multi_array_sort($multi_array,$sort_key,$sort=SORT_DESC){

        if(is_array($multi_array)){

            foreach ($multi_array as $row_array){

                if(is_array($row_array)){

                    $key_array[] = $row_array[$sort_key]; 
                }else{

                    return false;
                } 
            }
        }else{

            return false; 
        }
        array_multisort($key_array,$sort,$multi_array); 
        return $multi_array;
    }
    /**
     * 文件过滤下载
     * @param unknown $params
     */
    function downfile($params){
        $file = _Request::get('file');
        $file = trim($file,'/');
        if(empty($_SESSION['userId'])){
            echo "No permissions！";exit;
        }else if(preg_match("/\.(php|ini|htaccess|xml)$/is",$file)){
            echo "No permissions read this file！";exit;
        }else if(is_file($file) && file_exists($file)){            
            ob_start();
            $filename = basename($file);
            $date=date("Ymd-H:i:m");
            header( "Content-type:  application/octet-stream ");
            header( "Accept-Ranges:  bytes ");
            header( "Content-Disposition:  attachment;  filename= {$filename}");
            $size=readfile($file);
            header( "Accept-Length: " .$size); 
        }else{
            echo "file not find！";
        }
    }

    function downthundrpt($params) {
        $file = _Request::get('file');
        $file = trim($file,'/');
         if(preg_match("/\.(php|ini|htaccess|xml)$/is",$file)){
            echo "No permissions read this file！";exit;
        }else if(is_file($file) && file_exists($file)){            
            ob_start();
            $filename = basename($file);
            $date=date("Ymd-H:i:m");
            header( "Content-type:  application/octet-stream ");
            header( "Accept-Ranges:  bytes ");
            header( "Content-Disposition:  attachment;  filename= {$filename}");
            $size=readfile($file);
            header( "Accept-Length: " .$size); 
        }else{
            echo "file not find！";
        }
    }
}
