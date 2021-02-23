<?
define('IN_ECS', true);
$zph=str_replace('includes/modules/taobaoOrderApi/callback.php', '', str_replace('\\', '/', __FILE__));
require($zph.'kela/includes/init.php');
 /*
$ori_code = $_GET["top_appkey"].$_GET["top_parameters"].$_GET["top_session"]."046c481f480462907718bd1cce7f2403";
var_dump(base64_encode(md5($ori_code)));
var_dump($_GET["top_sign"]);
var_dump($_GET);
die (__FILE__." Row:".__LINE__);
*/
$_SESSION["taobao_session"] = $_GET["top_session"];
$sql = "UPDATE ecs_shop_config SET value='".trim($_GET["top_session"])."' WHERE id='8817'";
$db -> query($sql);
header("Location: /kela/to.php");
?>