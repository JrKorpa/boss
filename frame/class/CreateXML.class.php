<?php

/**
 *
 * 从数组生成XML文件
 */
final class CreateXML {

        private static $instance;
		private $version  = '1.0';
	    private $encoding  = 'UTF-8';
	    private $root    = 'root';
	    private $xml    = null;

        private function __construct() {
			$this->xml = new XmlWriter();
        }

        private function __clone() {

        }
        /**
         *
         * @return 单例模式
         */
        public static function getInstance() {
                if (!(self::$instance instanceof self)) {
                        self::$instance = new self();
                }
                return self::$instance;
        }
		function toXml($data, $eIsArray=FALSE) {
				if(!$eIsArray) {
				  $this->xml->openMemory();
				  $this->xml->startDocument($this->version, $this->encoding);
				  $this->xml->startElement($this->root);
				}
				foreach($data as $key => $value){
				  if(is_numeric($key)){
						$key = "item";
				  }
				  if(is_array($value)){
					$this->xml->startElement($key);
					$this->toXml($value, TRUE);
					$this->xml->endElement();
					continue;
				  }
				  $this->xml->writeElement($key, $value);
				}
				if(!$eIsArray) {
				  $this->xml->endElement();
				  return $this->xml->outputMemory(true);
				}
		}

        /**
         * 将字串保存到文件
         * @param $fileName 文件名
         * @param $XMLString 已经生成的XML字串
         */
        public function saveToFile($fileName, $XMLString) {
                if (!$handle = fopen($fileName, 'w')) {
                        return FALSE;
                }
                if (!fwrite($handle, $XMLString)) {
                        return FALSE;
                }
                return TRUE;
        }

        /**
         * 直接通过数组生成XML文件
         */
        public function write($fileName, $array) {
                $XMLString = $this->toXml($array);
                $result = $this->saveToFile($fileName, $XMLString);
                return $result;
        }

}
?>
