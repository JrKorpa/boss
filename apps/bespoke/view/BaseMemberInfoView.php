<?php
/**
 *  -------------------------------------------------
 *   @file		: BaseMemberInfoView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-07 17:49:23
 *   @update	:
 *  -------------------------------------------------
 */
class BaseMemberInfoView extends View
{
	protected $_member_id;
	protected $_country_id;
	protected $_province_id;
	protected $_city_id;
	protected $_region_id;
	protected $_source_id;
	protected $_member_name;
	protected $_mem_card_sn;
	protected $_member_phone;
	protected $_member_age;
	protected $_member_qq;
	protected $_member_email;
	protected $_member_aliww;
	protected $_member_dudget;
	protected $_member_maristatus;
	protected $_member_address;
	protected $_member_peference;
	protected $_member_type;
	protected $_department_id;
	protected $_customer_source_id;
    protected $_member_truename;
    protected $_member_tel;
    protected $_member_msn;
    protected $_member_sex;
    protected $_member_birthday;
    protected $_member_wedding;
    protected $_member_member_dudget;
    protected $_member_member_aliww;
    protected $_member_password;
    protected $_member_question;
    protected $_member_answer;


	public function get_member_id(){return $this->_member_id;}
	public function get_country_id(){return $this->_country_id;}
	public function get_province_id(){return $this->_province_id;}
	public function get_city_id(){return $this->_city_id;}
	public function get_region_id(){return $this->_region_id;}
	public function get_source_id(){return $this->_source_id;}
	public function get_member_name(){return $this->_member_name;}
	public function get_department_id(){return $this->_department_id;}
	public function get_customer_source_id(){return $this->_customer_source_id;}
	public function get_mem_card_sn(){return $this->_mem_card_sn;}
	public function get_member_phone(){return $this->_member_phone;}
	public function get_member_age(){return $this->_member_age;}
	public function get_member_qq(){return $this->_member_qq;}
	public function get_member_email(){return $this->_member_email;}
	public function get_member_aliww(){return $this->_member_aliww;}
	public function get_member_dudget(){return $this->_member_dudget;}
	public function get_member_maristatus(){return $this->_member_maristatus;}
	public function get_member_address(){return $this->_member_address;}
	public function get_member_peference(){return $this->_member_peference;}
	public function get_member_type(){return $this->_member_type;}
    public function get_member_truename(){return $this->_member_truename;}
    public function get_member_tel(){return $this->_member_tel;}
    public function get_member_msn(){return $this->_member_msn;}
    public function get_member_sex(){return $this->_member_sex;}
    public function get_member_birthday(){return $this->_member_birthday;}
    public function get_member_wedding(){return $this->_member_wedding;}
    public function get_member_password(){return $this->_member_password;}
    public function get_member_answer(){return $this->_member_answer;}
    public function get_member_question(){return $this->_member_question;}
    //public function get_member_member_dudget(){return $this->_member_member_dudget;}
    //public function get_member_member_aliww(){return $this->_member_member_aliww;}


	public function get_source_name($source_id){
		$sql = 'SELECT `source_name` FROM `customer_sources` WHERE `id` ='.$source_id;
		$res = DB::cn(1)->getOne($sql);
		return	$res;
	}

}
?>