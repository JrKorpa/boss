<?php
/**
 * 刻字图案配置源文件
 *
 */
class Kezi{
    public $a=array(
		"code"=>1,
		"data"=>array(
				array(
					"phrase"=>"[&符号]","type"=>"face","url"=>"/public/sales/face/1.png","hot"=>false,"common"=>true,"category"=>"","icon"=>"/public/sales/face/1.png","value"=>"[&符号]","picid"=>""),
				array(
					"phrase"=>"[间隔号]","type"=>"face","url"=>"/public/sales/face/2.png","hot"=>false,"common"=>true,"category"=>"","icon"=>"/public/sales/face/2.png","value"=>"[间隔号]","picid"=>""),
				array(
					"phrase"=>"[空心]","type"=>"face","url"=>"/public/sales/face/3.png","hot"=>false,"common"=>true,"category"=>"","icon"=>"/public/sales/face/3.png","value"=>"[空心]","picid"=>""),
				array(
					"phrase"=>"[实心]","type"=>"face","url"=>"/public/sales/face/4.png","hot"=>false,"common"=>true,"category"=>"","icon"=>"/public/sales/face/4.png","value"=>"[实心]","picid"=>""),
				array(
					"phrase"=>"[小数点]","type"=>"face","url"=>"/public/sales/face/5.png","hot"=>false,"common"=>true,"category"=>"","icon"=>"/public/sales/face/5.png","value"=>"[小数点]","picid"=>""),
				array(
					"phrase"=>"[心心相印]","type"=>"face","url"=>"/public/sales/face/6.png","hot"=>false,"common"=>true,"category"=>"","icon"=>"/public/sales/face/6.png","value"=>"[心心相印]","picid"=>""),
				array(
					"phrase"=>"[一箭穿心]","type"=>"face","url"=>"/public/sales/face/7.png","hot"=>false,"common"=>true,"category"=>"","icon"=>"/public/sales/face/7.png","value"=>"[一箭穿心]","picid"=>"")
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
            if($ret!==false){
                 $word=str_replace($key,"<img src='".$val."' width='24'/>",$word);
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
            '[一箭穿心]'=>'/public/sales/face/7.png'
        );

		return $rep;
	}


	public function getKezi(){
		return $this->a;
	}
}
?>