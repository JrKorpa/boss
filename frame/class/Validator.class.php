<?php
/**
 *  -------------------------------------------------
 *   表单验证类
 *   @file		    : Validator.class.php
 *   @link		    :  www.kela.cn
 *   @copyright    : 2014-2024 kela Inc
 *   @author	    : yangxiaotong
 *   @date		    :
 *   @update	    :
 *  -------------------------------------------------
 */

class Validator
{
    //所有验证函数定义时的前缀
    const PREFIX = 'form_validator_';

    //存储验证函数的出错信息
    public static $error_messages = array();

    //存储验证相关的数据
    protected $valid_rules = array();

    //存储上次验证的出错信息
    protected $errors = array();

    //是否完全检查
    protected $complete_check = true;

    public static function set_error($rule, $error)
    {
        self::$error_messages[$rule] = $error.PHP_EOL;
    }

    /**
     * 遇到第一个错误便返回
     */
    public function first_error_only()
    {
        $this->complete_check = false;
    }

    /**
     * 执行所有验证规则,收集所有错误
     */
    public function show_all_errors()
    {
        $this->complete_check = true;
    }

    /**
     * 设置验证规则
     * @param string $var 验证的变量名,例如 username
     * @param string $var_name 变量名的描述,例如 用户名
     * @param string $func_name 验证规则,可以是多个,并且可以传参,例如 require|length(9,12)
     * @param string $error_message 自定义的出错信息,可选,如果给定将覆盖验证函数的默认出错信息
     */
    public function set_rules($var, $var_name, $func_name, $error_message = NULL)
    {
        $data = array();
        $data['var_name'] = $var_name;
        $data['error'] = $error_message;
        $data['funcs'] = array();
        $funcs_with_arg = explode('|', $func_name);
        $args_match = array();

        //解析验证规则
        foreach( $funcs_with_arg as $func_with_arg )
        {
            preg_match('/\((.*)\)$/', $func_with_arg, $args_match);
            //规则函数是否携带参数
            if ( count($args_match) > 0 )
            {
                //参数列表
                $args = explode(',', $args_match[1]);
                //去掉参数后的验证规则名
                $func = str_replace('(' . $args_match[1] . ')', '', $func_with_arg);
                $data['funcs'][$func] = $args;
            }
            else
            {
                $data['funcs'][$func_with_arg] = NULL;
            }
        }

        $this->valid_rules[$var] = $data;
    }

    /**
     * 检验一组数据是否合法
     * @param array $data 一个关联数组
     * @return boolean
     */

    public function is_valid($data)
    {
        $this->errors = array();
        $valid = true;
        //遍历的顺序为规则添加的顺序
        foreach( $this->valid_rules as $var => $rule )
        {
            //判断数据中是否有此字段,没有的话置一个空的
            if (!isset($data[$var]))
                $data[$var] = '';

            foreach($rule['funcs'] as $func => $args)
            {
                //真实的验证函数的名称,例如 form_validator_require
                $func_run = self::PREFIX . $func;
                //如果函数未定义,循环继续
                if ( !is_callable($func_run) ) continue;

                //为验证函数组装参数
                //顺序为:当前验证字段的值, 所有验证数据, 验证规则参数1, ...
                if ( $args === NULL )
                {
                    $args = array($data[$var], $data);
                }
                else
                {
                    array_unshift($args, $data);
                    array_unshift($args, $data[$var]);
                }

                //调用验证函数
                if ( !call_user_func_array($func_run, $args) )
                {
                    $valid = false;
                    //如果set_rules的时候没有自定义错误信息,则使用默认出错信息
                    if ( $rule['error'] === NULL )
                    {
                        //使用sprintf拼接错误信息
                        //参数顺序: 验证字段的描述, 验证函数的参数1, 验证函数的参数2 ...

                        //去掉刚才添加的字段值
                        array_shift($args);
                        //去掉刚才添加的所有验证数据
                        array_shift($args);
                        //添加字段名的描述
                        array_unshift($args, $rule['var_name']);
                        //添加默认的出错信息
                        array_unshift($args, self::$error_messages[$func]);
                        $this->errors[] = call_user_func_array('sprintf', $args);
                    }
                    else
                    {
                        //使用自定义错误信息
                        $this->errors[] = $rule['error'];
                        continue 2;
                    }

                    //如果设置不进行完全检查,则发现第一个错误后跳出最外层循环
                    if ( !$this->complete_check )
                    {
                        break 2;
                    }
                }
            }
        }

        return $valid;
    }

    /**
     * 获取出错信息
     * @return string array
     */

    public function get_errors()
    {
        return $this->errors;
    }

    /**
     * 返回第一条出错信息
     * @return string
     */

    public function get_first_error()
    {
        return $this->errors[0];
    }


}


/**
 * -------------------------------------------------
 * 扩展验证规则
 *
 * 设置规则的默认出错信息
 * Validator::set_error('FUNC', '%s+messages');
 *
 * 定义验证函数,以FormValidator::PREFIX打头
 * function form_validator_ + FUNC(param)
 * -------------------------------------------------
 */

/**
 * require,必填
 */
Validator::set_error('require', '%s不能为空');
function form_validator_require($str)
{
    $str = trim($str);
    return (!empty($str)||$str == '0') ? true : false;
}

/**
 * isEmail  验证邮箱
 */
Validator::set_error('isEmail', '%s应是邮箱格式');
function form_validator_isEmail($str){
    return preg_match("/([a-z0-9]*[-_\.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?/i",$str) ? true : false;
}

/**
 * isInt    数字验证[整数]
 */
Validator::set_error('isInt', '%s必须是整数');
function form_validator_isInt($str){
    return ((string)(int)$str === (string)$str) ? true : false;
}

/**
 * isFloat  数字验证[小数]
 */
Validator::set_error('isFloat', '%s必须是小数');
function form_validator_isFloat($str){
    return ((string)(float)$str === (string)$str) ? true : false;
}


/**
 * isNumber     数字验证
 *
 * @param $no
 * @return bool
 */
Validator::set_error('isNumer', '%s必须数字');
function form_validator_isNumer($no){
    return (bool)preg_match("/^[0-9]+$/",$no);
}

/**
 * isMobile     手机验证
 */
Validator::set_error('isMobile', '%s必须是手机号码');
function form_validator_isMobile($str){
    $exp = "/^1\d{10}$/";
    if(preg_match($exp,$str)){
        return true;
    }else{
        return false;
    }
}

/**
 * isTel，检查固话号码
 *
 * @param $no
 * @return bool
 */
Validator::set_error('isTel', '%s必须是固话号码');
function form_validator_isTel ($no)
{
    return (bool)preg_match("/^(0(10|21|22|23|[1-9][0-9]{2})(-|))?[0-9]{7,8}$/",$no);
}

/**
 * isPhone  检查电话号码
 *
 * @param $no
 * @return bool
 */
Validator::set_error('isPhone', '%s必须是电话号码');
function form_validator_isPhone ($no)
{
    return (bool)(preg_match("/^(0(10|21|22|23|[1-9][0-9]{2})(-|))?[0-9]{7,8}$/",$no) || preg_match("/^1\d{10}$/",$no));
}

/**
 * isQQ     检查QQ号码
 *
 * @param $no
 * @return bool
 */
Validator::set_error('isQQ', '%s必须是QQ号码');
function form_validator_isQQ ($no)
{
    return (bool)preg_match("/^\d{5,15}$/",$no);
}

/**
 * isChinese    中文验证
 * 默认utf-8
 */
Validator::set_error('isChinese', '%s必须是中文');
function form_validator_isChinese($str){
    return !(bool)preg_match('/[^\x{4e00}-\x{9fa5}]/u',$str);
}

/**
 * isEnglish    英文验证
 */
Validator::set_error('isEnglish', '%s必须是英文');
function form_validator_isEnglish ($str)
{
    return !(bool)preg_match('/[^a-z]/i',$str);
}

/**
 * isLegal  检查是否合法字符串(字母、汉字、数字)
 *
 * @param $str
 * @return bool
 */
Validator::set_error('isLegal', '请检查%s的合法性');
function form_validator_isLegal($str)
{
    return (bool)preg_match('/^[\x{4E00}-\x{9FA5}a-z0-9]+$/iu',$str);
}

/**
 * isUsername   检查用户名是否符合规定
 *
 * @param $username
 * @return bool
 */
Validator::set_error('isUsername', '检查用户名是否符合规定');
function form_validator_isUsername($username)
{
    $strlen = strlen($username);
    if(empty($username) || preg_match("/[^a-z\d\x{4e00}-\x{9fa5}]/ui", $username) == 1){
        return false;
    } elseif ( 20 < $strlen || $strlen < 2 ) {
        return false;
    }
    return true;
}

/**
 * isEmun   判断枚举类型
 * 默认枚举为0,1
 */
Validator::set_error('isEmun', '请检查【%s】的合法性');
function form_validator_isEmun($str){
    $arr = array('0','1');
    if(in_array($str,$arr)){
        return true;
    }else{
        return false;
    }
}




?>