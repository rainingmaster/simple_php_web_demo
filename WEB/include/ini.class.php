<?PHP
if(!defined('INIDEF')) {//防止重定义
define('INIDEF', 'OK');
	
define('CL', "\r\n");//定义换行符
define('CONF_PATH', dirname(__FILE__).'/conf.ini');

class ini {
	private $Ini_filepath;//配置文件地址
	private $Conf_arr = array();//配置数组
	private $Chang_tar;//配置文件是否改变过

	function __construct($iniFileName = CONF_PATH) {
		if(empty($iniFileName))
			return ($this->Conf_arr = false);
		if(!file_exists($iniFileName))
			return ($this->Conf_arr = false);
		$this->Conf_arr = parse_ini_file($iniFileName,true);
		$this->Ini_filepath = $iniFileName;
	}
	
	/*
	*读取ini键值
	*mode:节点名称，如[mode]，不分节则为空
	*key:键名，如key
	*返回:对应键值
	*/
	public function iniRead($mode = null, $key) {
		if(empty($mode))
			return @$this->Conf_arr[$key]==null ? null : $this->Conf_arr[$key];
		else
		return $this->Conf_arr[$mode][$key]==null ? null : $this->Conf_arr[$mode][$key];
	}
	
	/*
	*写入ini键值
	*mode:节点名称，如[mode]，不分节则为空
	*key:键名，如key
	*$value:对应键值
	*/
	public function iniWrite($mode = null, $key, $value) {
		if(!empty($mode)) {
			$this->Chang_tar = (@$this->Conf_arr[$mode][$key]==$value ? false : true);//若传入的值和原来的一样，则不更改
			@$this->Conf_arr[$mode][$key]=$value;
		}
		else {
			$this->Chang_tar = (@$this->Conf_arr[$key]==$value ? false : true);//若传入的值和原来的一样，则不更改
			@$this->Conf_arr[$key]=$value;
		}
	}
	
	/*
	*将配置写入文件
	*/
	public function iniUpdate() {
		$newini = '';
		foreach ($this->Conf_arr as $key => $val) {//从新已ini格式排版
			if(!is_array($val)) {
				$newini=$newini.$key.'="'.$val.'"'.CL;
			}
			else {
				$newini=$newini.'['.$key."]".CL;//节名
				
				foreach ($val as $k2 => $v2) {
					$newini=$newini.$k2.'="'.$v2.'"'.CL;}
			}		 
		}
		if(empty($this->Ini_filepath))
			return false;
		if ( file_put_contents($this->Ini_filepath, $newini, LOCK_EX ) !== false)
			return true;
		return false;//写文件失败
	}
}
	
}//end INIDEF