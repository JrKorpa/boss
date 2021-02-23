<?php
define('PARENT_ROOT',str_replace('\\','/',realpath(dirname(dirname(dirname(__FILE__))))));

require_once PARENT_ROOT.'/frame/class/Util.class.php';
require_once PARENT_ROOT.'/frame/class/SMTP.class.php';
require_once PARENT_ROOT.'/frame/class/PHPMailer.class.php';
require_once PARENT_ROOT.'/frame/common/define.php';

require_once PARENT_ROOT.'/apps/shipping/modules/express_api/ZtExpress.php';
ZtExpress::getMoneys();