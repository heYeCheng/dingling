<?php
class Weixin
{
	private $wx_appID;
	private $wx_appsecret;

	function __construct($wx_appID , $wx_appsecret){
		$this->wx_appID = $wx_appID;
		$this->wx_appsecret = $wx_appsecret;
	}

	// 获取基础　token
	public function get_base_token(){
		$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->wx_appID.'&secret='.$this->wx_appsecret;
		$data = $this->https_request($url);
		$data = json_decode($data, True);
		// dump($data);
		if (isset($data['access_token'])) {
			return $data;
		}else{
			return '';
		}
	}

	// 验证用户是不是微信用户
	public function vali_weixin_user($code){
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->wx_appID.'&secret='.$this->wx_appsecret.'&code='.$code.'&grant_type=authorization_code';
		$data = $this->https_request($url);
		$data = json_decode($data, True);
		// dump($data);
		if (isset($data['openid'])) {
			return $data;
		}else{
			return '';
		}
	}

	// 生成带 code  的链接
	public function produce_code_link($url){
		$ex_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->wx_appID.'&redirect_uri=';
		$pr_url = '&response_type=code&scope=snsapi_base&state=#wechat_redirect';
		return $ex_url.$url.$pr_url;
	}

	// 获取用户信息， 此access_token为基础支持的access_token，需要读取后台来获取 token 数据
	public function get_user_info($access_token, $openid){
		$url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
		$data = $this->https_request($url);
		$data = json_decode($data, True);
		// dump($data);
		if (isset($data['openid'])) {
			return $data;
		}else{
			return '';
		}
	}

	// 获取用户信息， 此access_token与基础支持的access_token不同 
	public function get_user_info_web($access_token, $openid){
		$url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
		$data = $this->https_request($url);
		$data = json_decode($data, True);
		// dump($data);
		if (isset($data['openid'])) {
			return $data;
		}else{
			return '';
		}
	}

	public function https_request($url, $data = null){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		if (!empty($data)){
		    curl_setopt($curl, CURLOPT_POST, 1);
		    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($curl);
		curl_close($curl);
		return $output;
	}
}

?>