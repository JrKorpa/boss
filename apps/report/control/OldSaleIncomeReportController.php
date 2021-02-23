<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OutLinkController
 *
 * @author yangfuyou
 */
class OldSaleIncomeReportController extends Controller
{
	protected $smartyDebugEnabled = false;
        //put your code here
    public function index ($params)
	{
        header('Location:http://203.130.44.139:8080/reportmis/gezEntry.url?patternID=SR&resID=33411');
        exit;
    }
}
