<?php
class commonMod{
	protected $model = NULL; //数据库模型
	protected $layout = NULL; //布局视图
	protected $wx = NULL;  //加载微信类
	protected $config = array();
	private $_data = array();
	
	protected function init(){
		
	}
	
	public function __construct(){
		global $config;
		$this->config = $config;
		$this->model = self::initModel( $this->config );
		// session_start();
		$this->wx = new Weixin($this->config['AppID'], $this->config['AppSecret']);
		// $this->init();	
	}
	
	//初始化模型
	static public function initModel($config){
		static $model = NULL;
		if( empty($model) ){
			$model = new cpModel($config);
		}
		return $model;
	}
	
	public function __get($name){
		return isset( $this->_data[$name] ) ? $this->_data[$name] : NULL;
	}

	public function __set($name, $value){
		$this->_data[$name] = $value;
	}

	//获取模板对象
	public function view(){
		static $view = NULL;
		if( empty($view) ){
			$view = new cpTemplate( $this->config );
		}
		return $view;
	}
	
	//模板赋值
	protected function assign($name, $value){
		return $this->view()->assign($name, $value);
	}
	
	//模板显示
	protected function display($tpl = '', $return = false, $is_tpl = true ){
		if( $is_tpl ){
			$tpl = empty($tpl) ? $_GET['_module'] . '_'. $_GET['_action'] : $tpl;
			if( $is_tpl && $this->layout ){
				$this->__template_file = $tpl;
				$tpl = $this->layout;
			}
		}
		$this->view()->assign( $this->_data );
		return $this->view()->display($tpl, $return, $is_tpl);
	}
	
	//判断是否是数据提交	
	protected function isPost(){
		return $_SERVER['REQUEST_METHOD'] == 'POST';
	}
	
	//直接跳转
	protected function redirect( $url, $code=302) {
		header('location:' . $url, true, $code);
		exit;
	}
	
	//弹出信息
	protected function alert($msg, $url = NULL){
		header("Content-type: text/html; charset=utf-8"); 
		$alert_msg="alert('$msg');";
		if( empty($url) ) {
			$gourl = 'history.go(-1);';
		}
		elseif ($url == "close") {
			$gourl = 'self.opener=null;self.close();';
		}
		else{
			$gourl = "window.location.href = '{$url}'";
		}
		echo "<script>$alert_msg $gourl</script>";
		exit;
	}

	/**
     * 用于接收 get 的参数
     * @param name 要 get 的名字
     * @param default  默认值
     * @param inType  输入的类型，1 = 数据， 2 = 其它类型
     * @param isNeed 此参数是否为必填，若没有填，则返回错误
     */ 
	protected function in_get($name, $default = None, $inType = 1, $isNeed = 'False'){
		if (isset($_GET[$name])) {
			if ($inType == 1) {
				$data = intval($_GET[$name]);
			}else{
				$data = in($_GET[$name]);
			}
			return $data;
		}else{
			if ($isNeed == 'True') {
				echo 'error '. $name . ' 此参数必须填写';
				exit();
			}else{
				return $default;
			}
		}
	}

	/**
     * 用于接收 get 的参数
     * @param name 要 post 的名字
     * @param default  默认值
     * @param inType  输入的类型，1 = 数据， 2 = 其它类型
     * @param isNeed 此参数是否为必填，若没有填，则返回错误
     */ 
	protected function in_post($name, $default = None, $inType = 1, $isNeed = 'False'){
		if (isset($_POST[$name])) {
			if ($inType == 1) {
				$data = intval($_POST[$name]);
			}else{
				$data = in($_POST[$name]);
			}
			return $data;
		}else{
			if ($isNeed == 'True') {
				echo 'error '. $name . ' 此参数必须填写';
				exit();
			}else{
				return $default;
			}
		}
	}

	/**
     * 用于接收 cookie 的参数
     * @param name 要 post 的名字
     * @param default  默认值
     * @param inType  输入的类型，1 = 数据， 2 = 其它类型
     * @param isNeed 此参数是否为必填，若没有填，则返回错误
     */ 
	protected function in_cookie($name, $default = None, $inType = 1, $isNeed = 'False'){
		if (isset($_COOKIE[$name])) {
			if ($inType == 1) {
				$data = intval($_COOKIE[$name]);
			}else{
				$data = in($_COOKIE[$name]);
			}
			return $data;
		}else{
			if ($isNeed == 'True') {
				echo 'error';
				exit();
			}else{
				return $default;
			}
		}
	}

}