<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class DownUserAuthorityController extends Controller
{
        //put your code here
    public function index ($params)
	{
        $this->render('down_user_authority_list.html',array());
    }


    public function down_user_aty()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        $model = new UserModel(1);

        $userInfo = $model->getUserList();
        $userAutInfo = array();
        if(is_array($userInfo) && !empty($userInfo)){
            foreach ($userInfo as $key => $value) {
                $userAutInfo[$value['account']] = $model->getAutList($value['id']);
            }
        }

        header("Content-Type: text/html; charset=gb2312");
        header("Content-type:aplication/vnd.ms-excel");
        header("Content-Disposition:filename=" . iconv('utf-8', 'gb2312', "用户权限".time()) . ".xls");
        $csv_body = '<table border="1"><tr>
                    <td style="text-align: center;">用户姓名</td>
                    <td style="text-align: center;">功能菜单</td>
                    <td style="text-align: center;">权限</td>
                    <td style="text-align: center;">详情页权限</td>
                    </tr>';

        foreach ($userAutInfo as $user_name => $aut) {

            foreach ($aut as $k => $val) {
                $csv_body .= "<tr>";
                $csv_body .="<td>" . $user_name . "</td>";
                $csv_body .="<td>" . $val['功能菜单'] . "</td>";
                $csv_body .="<td>" . $val['权限'] . "</td>";
                $csv_body .="<td>" . $val['详情页权限'] . "</td></tr>";
            } 
        }
        $csv_body .= "</table>";
        echo $csv_body;exit;
    }

}
