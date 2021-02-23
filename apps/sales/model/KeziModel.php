<?php
/**
 * 刻字图案配置源文件
 *
 */
class KeziModel{
    public $a=array(
		"code"=>1,
		"data"=>array(
				array(
					"phrase"=>"[&符号]","type"=>"face","url"=>"/public/sales/face/1.png","hot"=>false,"common"=>true,"category"=>"","icon"=>"/public/sales/face/1.png","value"=>"[&符号]","picid"=>""),
				array(
					"phrase"=>"[间隔号]","type"=>"face","url"=>"/public/sales/face/2.png","hot"=>false,"common"=>true,"category"=>"","icon"=>"/public/sales/face/2.png","value"=>"[间隔号]","picid"=>""),
				array(
					"phrase"=>"[空心]","type"=>"face","url"=>"/public/sales/face/3.png","hot"=>false,"common"=>true,"category"=>"","icon"=>"/public/sales/face/3.png","value"=>"[空心]","picid"=>""),
				//array(
					//"phrase"=>"[实心]","type"=>"face","url"=>"/public/sales/face/4.png","hot"=>false,"common"=>true,"category"=>"","icon"=>"/public/sales/face/4.png","value"=>"[实心]","picid"=>""),
				array(
					"phrase"=>"[小数点]","type"=>"face","url"=>"/public/sales/face/5.png","hot"=>false,"common"=>true,"category"=>"","icon"=>"/public/sales/face/5.png","value"=>"[小数点]","picid"=>""),
				array(
					"phrase"=>"[心心相印]","type"=>"face","url"=>"/public/sales/face/6.png","hot"=>false,"common"=>true,"category"=>"","icon"=>"/public/sales/face/6.png","value"=>"[心心相印]","picid"=>""),
				array(
					"phrase"=>"[一箭穿心]","type"=>"face","url"=>"/public/sales/face/7.png","hot"=>false,"common"=>true,"category"=>"","icon"=>"/public/sales/face/7.png","value"=>"[一箭穿心]","picid"=>""),
                )
			);
	   
	/**
     *
     *  显示刻字
     *
     */
    public function retWord($word)
    {
        $rep = $this->getKeziData();
        foreach($rep as $key => $val)
        {
            $ret=stripos($word,$key);
            if($ret !== false){
                return str_replace($key,"<img src='".$val."' width='24'/>",$word);
            }
        }
        return $word;
    }

//刻字内容
	public function getKeziData(){
		$rep=array(
            '[&符号]'=>'/public/sales/face/1.png',
            '[间隔号]'=>'/public/sales/face/2.png',
            '[空心]'=>'/public/sales/face/3.png',
            '[实心]'=>'/public/sales/face/4.png',
            '[小数点]'=>'/public/sales/face/5.png',
            '[心心相印]'=>'/public/sales/face/6.png',
            '[一箭穿心]'=>'/public/sales/face/7.png',
            '[红宝石]'=>'/public/sales/face/8.png',
        );

		return $rep;
	}

    //刻字长度
	public function pdKeziData($kezi,$allkezi,$is_oubanjie=0){

        $str = $kezi;
        //[一箭穿心]代表一个字符
        $allkezi_str='';
        foreach ($allkezi as $k=>$val){
            if($val){
                $k=str_replace(array('[',']','&'),array('\[','\]','\&'),$k);
            }
            $allkezi_str.=$k.'|';
        }
        $allkezi_str=rtrim($allkezi_str,'|');
        preg_match_all("/".$allkezi_str."+/u",$str,$allkezi_key);
        if($allkezi_key[0]){
            $allkezi_count=count($allkezi_key[0]);
            foreach($allkezi_key[0] as $k=>$v){
                $str=str_replace($v,'',$str);
            }
        }else{
            $allkezi_count=0;
        }
        //var_dump($str);exit;
        preg_match_all("/[0-9]{1}/i",$str,$arrNum);//数字
        preg_match_all("/[a-zA-Z]{1}/i",$str,$arrAl);//字母
        preg_match_all("/[\x{4e00}-\x{9fa5}]{1}/u",$str,$arrCh); //中文
        preg_match_all("/[^\x{4e00}-\x{9fa5}\w]{1}/u",$str,$punct); //其它字符
        //var_dump($arrCh[0]);exit;
        //1、欧版戒，0、非欧版戒
        $err_bd = '';
        if(!$is_oubanjie){
            //非欧版戒，只能刻以下标点符号
            $is_bdfh = array('`','~','•','！','@','#','$','%','^','&','*','(',')','_','-','+','=','{','}','【','】','|','、','：','；','“','”','‘','’','《','》','，','。','？','\ ','.','<','>',' ','·','!','￥','…','（','）','—','｛','｝','[',']',';',',',':','?','/','"','\'','\\');
            //var_dump($punct[0]);die;
            if(!empty($punct[0])){
                foreach ($punct[0] as $key => $value) {
                    if(!in_array($value,$is_bdfh)){
                        $err_bd .= $value;
                    }
                }
            }
        }
        
        $data = array('err_bd'=>'','str_count'=>'','kezi'=>'');
        $data['err_bd'] = $err_bd;
        $data['str_count'] = count($arrNum[0])+count($arrAl[0])+count($arrCh[0])+count($punct[0])+$allkezi_count;
        //将特殊字符用数字编码代替存入数据库；
        $kezi = str_replace('\\','a01',$kezi);
        $kezi = str_replace('\'','a02',$kezi);
        $kezi = str_replace('"','a03',$kezi);
        $data['kezi'] = $kezi;
		return $data;
	}

	public function getKezi($where=[]){
		if(date('Y-m-d')>'2018-07-20' && date('Y-m-d')<'2018-09-01' && $where['style_type']=='2'){
            $a = $this->a;
            $a['data'][]=array("phrase"=>"[红宝石]","type"=>"face","url"=>"/public/sales/face/8.png","hot"=>false,"common"=>true,"category"=>"","icon"=>"/public/sales/face/8.png","value"=>"[红宝石]","picid"=>"");
                    
            return $a;
        }else{
            return $this->a;
        }

	}
}
?>