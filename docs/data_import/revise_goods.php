<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
<head>
<title>商品库存修正--by yxt</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="http://www.kela.cn" />
<style>
    #content{margin:0px auto;width:1000px;padding-top:10px;}
    .left{width:300px;background:#999111;padding:5px;float:left;}
    .reight{width: 400px;float:left;}
    .submit{width:80px;height:50px;margin:3px;float: left;}
    .note{float:right;background:gray;padding:5px;width:300px;height:480px;}
    .right{width:240px;height:480px;background:#999111;padding:5px;float:right;}}
    .border{border:1px solid red;width:200px;height:180px;}
</style>
</head>
    <body>
        <div id='content'>
            <form action="./03_bill_goods_status.php" method="post">
            <fieldset>
            <div class="left">
                <p>请输入商品货号(一行一个商品)</p>
                <textarea name="goods" id="goods" style="width:290px;height:420px;"></textarea>
            </div>
            <div class='reight'>
                <!-- <select name="use_house">
                    <option value="0">请选择</option>
                    <option value="new">新系统</option>
                    <option value="old">老系统</option>
                </select> -->
                <div id='submit'>
                    <input type="submit" value="提交" class="submit">
                </div>
                <div class="note">
                    <p style="text-">使用说明</p>
                    <p>输入需要调整的商品：(例)</p>
                    <div class="border">
                        150432912519<br/>
                        150432263052<br/>
                        150432212137<br/>
                    </div>
                    <p>提交后:<br/>
                        查询[新/老]系统货品单据的状态,<br/>
                        取最后操作时间商品所在位置!</p>
                     <p>&nbsp;</p>   
                     <p>&nbsp;</p>
                     <p>注：维修单 不改变商品状态,如遇状态不准确的请联系开发人员！</p>  
                     <p>新系统的单据最好是审核后,再使用本操作</p>  
                </div>
            </div>
            <div class="right">
                <p>
                   <b>old_ => 老系统</b><br/><br/>
                    L=收货,F=转仓,S=销售,W=盘点,<br/>
                    B=销售退货,M=退货返厂,C=拆货,<br/>
                    E=损益，Z=组合返厂,X=组合收货,<br/>
                    P=批发销售,H=批发退货,<br/>
                    T=其他收货<br/>
                    <br/><br/><br/>
                </p><p>
                    <b>new_ => 新系统</b><br/><br/>
                    L=收货,M=调拨,S=销售,W=盘点,<br/>
                    B=退货返厂,E=损益，D=销售退货,<br/>
                    C=其他出库，T=其他收货<br/>
                    P=批发销售<br/>
                </p>
            </div>
            </fieldset>
            </form>
        </div>
    </body>
</html>