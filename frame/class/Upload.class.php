<?php
/*
 * -------------------------------------------------
 * 文件上传类
 * @file        : upload.class.php
 *
 * @author      : yangxt <yangxiaotong@163.com>
 * @version     : 1.0
 * -------------------------------------------------
*/
class Upload{

    protected $img = ['jpg','jpeg','png','gif'];
    protected $doc = ['doc','docx','pdf','rar','zip','txt'];
    protected $execl = ['xls','csv'];
    protected $ext;
    protected $file_info;                              //上传文件的信息
    protected $save_info;                              //保存文件的信息
    protected $base_path;                              //上传文件根目录
    protected $type_path;                              //文件类型目录
    public $save_path;                              //保存文件路径,不含文件
    public $save_name;                              //保存文件名,含后缀
    protected $maxSize;                                //最大上传大小
    protected $err_msg;                                //提示信息;
    protected $allowExt = [];                          //允许上传文件类型

    public function __construct($size = 2,$ext = null){
        $this->allowExt = array_merge($this->img,$this->doc,$this->execl);
        $this->base_path = KELA_ROOT.'/public/upload/';
        $this->setMaxSize($size);
        if($ext !== null){
			//edit by zhangruiying 修改BUG $this->allowExt($ext);
            $this->allowExt=$ext;
        }
    }

    public function __get($name)
    {
        return (in_array($name,['img','doc','execl']))?$this->$name:false;
    }

    public function __set($name,$val)
    {
//        $this->$name=$val;
    }
    /**
     * @param $file_name
     * @return array|mixed
     */
    public static function getExt($file_name){
        $ext = explode('.', strtolower($file_name));
        $ext = end($ext);
        return $ext;
    }

    /**
     * @param string $allow 不同类型用,隔开
     */
    protected function addExt($allow){
        $arr = explode(',', $allow);
        self::$allowExt = array_merge($arr,self::$allowExt);
    }

    /**
     * @param $size
     */
    protected  function setMaxSize($size){
        $this->maxSize = $size*1024*1024;
    }

    protected function setTypePath(){
        if(in_array($this->ext,$this->img)){
            $this->type_path = 'image/';
        }elseif(in_array($this->ext,$this->doc)){
            $this->type_path = 'doc/';
        }elseif(in_array($this->ext,$this->execl)){
            $this->type_path = 'execl/';
        }else{
            $this->type_path = 'other/';
        }
    }
	//edit by zhangruiying支持自定义上传路径
    protected function setSavePath(){

		if(empty($this->save_path))
		{
			$this->setTypePath();
			$this->save_path = $this->base_path.$this->type_path.date('Ymd').'/';
		}
        is_dir($this->save_path)?$this->save_path:mkdir($this->save_path,0755,true);
    }
	//edit by zhangruiying支持自定义上传名称
    protected function setSaveName(){
		if(empty($this->save_name))
		{
			$chars = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz') ,0,10);
			$this->save_name = $chars.'_'.date('His').'.'.$this->ext;
		}
    }

    /**
     * @param $file_name
     * @return array|string
     */
    public function toUP($file_name)
    {
        if (!isset($file_name) || !is_array($file_name)) {
            $this->err_msg = 'ERROR! Please select a File';
            return $this->err_msg;
        }
        $this->file_info = $file_name;

        /*获取上传文件的后缀*/
        $this->ext = self::getExt($this->file_info['name']);

        if ($this->file_info['error'] != 0) {
            $this->err_msg = $file_name['name'].' Upload falied !!!';
            return $this->err_msg;
        }

        if (!$this->is_POST()) {
            $this->err_msg = 'WARNING !!! Transmission mode is not POST ';
            return $this->err_msg;
        }
      
        if (!$this->checkType()) {
            //$this->err_msg = 'The file type ['.$this->ext.'] is not allowed to upload !!!';
            $this->err_msg = '该文件类型： ['.$this->ext.'] 不允许上传!!!';
            return $this->err_msg;
        }
        if (!$this->checkSize()) {
            $this->err_msg = 'The file is too large to upload !!!';
            return $this->err_msg;
        }
        $this->setSavePath();
        $this->setSaveName();

        /*保存文件*/
        if (move_uploaded_file($this->file_info['tmp_name'],$this->save_path.$this->save_name)) {
            $this->err_msg = '文件上传成功！';
            $this->save_info['name'] = $this->save_name;
            $this->save_info['type'] = $this->ext;
            //$this->save_info['path'] = str_replace($this->base_path,'/upload/', $this->save_path);
            $this->save_info['path'] = str_replace(KELA_ROOT,'', $this->save_path);
            $this->save_info['url'] = $this->save_info['path'].$this->save_info['name'];
            $this->save_info['oldname'] = $this->file_info['name'];
            $this->save_info['msg'] = $this->err_msg;
			$this->save_name='';
            return $this->save_info;
        } else {
            $this->err_msg = $file_name['name'].' upload falied !!!';
            return $this->err_msg;
        }
    }

    /**
     * is_POST()判断是否用POST上传
     * @return boolean
     */
    public function is_POST(){
        return is_uploaded_file($this->file_info['tmp_name']);
    }

    /**
     * 检测文件的后缀是否允许上传
     * @return boolean
     */
    protected function checkType(){
        return in_array($this->ext, $this->allowExt);
    }

    /**
     * 检测文件的大小,是否允许上传
     * @return boolean
     */
    public function checkSize(){
        return ($this->file_info['size'] < $this->maxSize);
    }

    /**
     * 单,多文件上传,【注意】文件字段需为数组样式
     * @param $files
     * @return array|bool
     */
    public function uploadfile($files){
        $infos = [];
        if(isset($files) && is_array($files)){
            if(is_array($files['name'])) {
                foreach($files as $key => $var){
                    foreach($var as $id => $val) {
                        $attachments[$id][$key] = $val;
                    }
                }
            }else{
                $attachments[] = $files;
            }
            foreach ($attachments as $f) {
               $infos[] = $this->toUP($f);
            }
            return $infos;
        }else{
            return 'Upload falied !!!';
        }
    }

    /**
     * 删除文件
     * @param $file 文件路径,含文件名
     * @return bool
     */
    public static function removeFile($file){
        if(isset($file) && is_file($file)){
           return unlink(KELA_ROOT.'/public'.$file);
        }else{
            return false;
        }
    }

    public static function removeAbsoluteFile($file){
        if(isset($file) && is_file($file)){
            return unlink($file);
        }else{
            return true;
        }
    }

    /**
     * 获取CSV 文件内容
     */
    public static function getCSV($file){

        $spl_object = new SplFileObject($file, 'rb');
        $spl_object->seek(filesize($file));
        $start = 0;$len = $spl_object->key();
        $spl_object->seek($start);
        while ($len && !$spl_object->eof()) {
            $data[] = $spl_object->fgetcsv();
            $spl_object->next();
        }
        array_shift($data);if(!end($data)[0]){array_pop($data);}
        $data = eval('return '.iconv('gbk','utf-8',var_export($data,true)).';');
        return $data;

    }


}



