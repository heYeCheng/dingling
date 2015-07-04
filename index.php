<?php
//定义CanPHP框架目录
define('CP_PATH',dirname(__FILE__).'/include/');//注意目录后面加“/”

require(dirname(__FILE__).'/config.php');//加载配置
require(CP_PATH.'core/cpApp.class.php');//加载应用控制类
require(CP_PATH.'core/cpModel.class.php');//加载数据库模型类
require(CP_PATH.'core/cpTemplate.class.php');//加载模板类

$app=new cpApp($config);//实例化单一入口应用控制类   哈哈哈
//执行项目
$app->run();

?>