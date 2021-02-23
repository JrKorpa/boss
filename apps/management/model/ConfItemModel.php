<?php
/**
 *  -------------------------------------------------
 *   @file		: ConfItemModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-29 18:07:06
 *   @update	:
 *  -------------------------------------------------
 */
class ConfItemModel extends Model
{
	protected $_dataObject;
	protected $_webconfPath;

	function __construct ($id=NULL)
	{
		$this->_webconfPath = KELA_ROOT.'/frame/common/web.config.tmp';
		$this->_ID = $id === null ? null : intval($id);
        $this->_dataObject = array("id"=>"序号",
"item"=>"配置项",
"note"=>"备注",
"db_host"=>"服务器",
"db_port"=>"端口",
"db_name"=>"数据名",
"db_user"=>"用户名",
"db_pwd"=>"密码",
"is_deleted"=>"删除标识",
"addby_id"=>"创建人",
"add_time"=>"创建时间");
	}

	/*获取配置参数*/
	public function getParam(){
		$file = $this->_webconfPath;
		if(!is_file($file)){
			$file = substr($file,0,-4);
		}
		$ini_array = parse_ini_file($file, true);

		$i = 0;
		$data = array();
		foreach ($ini_array as $k => $v) {
			if(is_array($v)){
				$data[$i]['item'] = $k;
				foreach ($v as $m => $n) {
					$data[$i][$m] = $n;
				}
				$i++;
			}else{
				unset($v);
			}
		}
		return $data;
	}

	/**
	 * arraytostr
	 */
	public function arraytostr($arr){
		$str = "";
		foreach ($arr as $k => $v) {
			if(is_array($v)){
				foreach ($v as $x => $y) {
					if($x == 'item'){
						$str .= PHP_EOL."[$y]".PHP_EOL;
					}else{
						$str .= trim($x).'='.trim($y).PHP_EOL;
					}
				}
			}

		}
		return ltrim($str);
	}

	/**
	 * gen_doc ,生成文档
	 */
	public	function gen_doc($arr){

		$str = $this->arraytostr($arr);
		$file_path = $this->_webconfPath;
		$res = file_put_contents($file_path,$str,FILE_USE_INCLUDE_PATH);
		return $res;
	}
}

?>