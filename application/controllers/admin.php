<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {
    
    public function __construct(){
        parent::__construct();
		$this->purview_model->checkpurview(82);
		$this->uid   = $this->session->userdata('uid');
    }
	
	public function index(){
		$this->load->view('admin/index');
	}
	
	//用户接口
	public function lists() {
	    $v = array();
	    $data['status'] = 200;
		$data['msg']    = 'success'; 
		$list = $this->cache_model->load_data(ADMIN,'(1=1) order by roleid');  
		foreach ($list as $arr=>$row) {
		    $v[$arr]['share']       = true;
			$v[$arr]['admin']       = $row['roleid'] > 0 ? false : true;
		    $v[$arr]['userId']      = intval($row['uid']);
			$v[$arr]['isCom']       = intval($row['status']);
			$v[$arr]['role']        = intval($row['roleid']);
			$v[$arr]['userName']    = $row['username'];
			$v[$arr]['realName']    = $row['name'];
			$v[$arr]['shareType']   = 0;
			$v[$arr]['mobile']      = $row['mobile'];
		}
		$data['data']['items']      = $v;
		$data['data']['shareTotal'] = $this->cache_model->load_total(ADMIN);
		$data['data']['totalsize']  = 3;
		$data['data']['corpID']     = 0;
		die(json_encode($data));
	}
	
	//用户添加
	public function add(){
		$data = str_enhtml($this->input->post(NULL,TRUE));
		if (is_array($data)&&count($data)>0) {
			!isset($data['username']) || strlen($data['username'])<1 && die('{"status":-1,"msg":"用户名不能为空"}'); 
			!isset($data['userpwd']) || strlen($data['userpwd'])<1 && die('{"status":-1,"msg":"密码不能为空"}'); 
			$this->mysql_model->db_count(ADMIN,'(username="'.$data['username'].'")')>0 && die('{"status":-1,"msg":"用户名已经存在"}');
			$data['userpwd'] = md6($data['userpwd']);
		    $sql = $this->mysql_model->db_inst(ADMIN,$data);
			if ($sql) {
			    $this->cache_model->delsome(ADMIN);
				die('{"status":200,"msg":"注册成功","userNumber":"'.$data['username'].'"}');
			} else {
			    die('{"status":-1,"msg":"添加失败"}');  
			}
		} else {
		    $this->load->view('admin/add');
		}
	}
	
	//密码修改
	public function edit(){
		$data = str_enhtml($this->input->post(NULL,TRUE));
		if (is_array($data)&&count($data)>0) {
			!isset($data['userpwd']) || strlen($data['userpwd'])<1 && die('{"status":-1,"msg":"密码不能为空"}'); 
			$data['userpwd'] = md6($data['userpwd']);
		    $sql = $this->mysql_model->db_upd(ADMIN,$data,'(uid='.$this->uid.')');
			if ($sql) {
			    $this->cache_model->delsome(ADMIN);
				die('{"status":200,"msg":"密码修改成功","userNumber":""}');
			} else {
			    die('{"status":-1,"msg":"修改失败"}');  
			}
		} else {
		    $this->load->view('admin/edit');
		}
	}
	
	//权限分配
	public function authority(){
		$username = str_enhtml($this->input->get_post('username',TRUE));
		$lever    = str_enhtml($this->input->get_post('rightid',TRUE));
		$act = str_enhtml($this->input->get_post('act',TRUE));
		if ($act == 'ok') {
		    $sql = $this->mysql_model->db_upd(ADMIN,array('lever'=>$lever),'(username="'.$username.'")');  
			if ($sql) {
			    $this->cache_model->delsome(ADMIN);
			    die('{"status":200,"msg":"success"}');
			} else {
			    die('{"status":400,"msg":"操作失败"}');
			}
		} else {
		    $data['username'] = $username;
		    $this->load->view('admin/authority',$data);
		}
	}
	
	
	//权限树
	public function tree(){
		$username = str_enhtml($this->input->get_post('username',TRUE));
		if (strlen($username)>0) {
		    $lever = $this->cache_model->load_one(ADMIN,'(username="'.$username.'")','lever');  
			$lever = strlen($lever)>0 ? explode(',',$lever) : array();
		} else {
		    $lever = array();	
		}
		$v = '';
		$data['status'] = 200;
		$data['msg']    = 'success'; 
		$data['data']['totalsize']   = $this->cache_model->load_total(MENU,'(status=1)');   //总条数
		$list = $this->cache_model->load_data(MENU,'(status=1) order by path');  
		foreach ($list as $arr=>$row) {
		    $v[$arr]['fobjectid']  = intval($row['pid']);
			$v[$arr]['fobject']    = $row['title'];
			$v[$arr]['faction']    = $row['pid']==$row['path']? '' : $row['title'];
			$v[$arr]['fright']     = in_array($row['id'],$lever) ? 1 : 0;
			$v[$arr]['frightid']   = intval($row['id']);
		}
		$data['data']['items']      = is_array($v) ? $v : '';
		die(json_encode($data));
	}
	
	//启用停用
	public function doset(){
	    $act = $this->input->get('act',TRUE);
	    $username = str_enhtml($this->input->get('username',TRUE));
		$username == 'admin' && die('{"status":-1,"msg":"管理员不可操作"}');  
		switch ($act) { 
			case 'isstatus': $data['status'] = 1; break;   
			case 'nostatus': $data['status'] = 0; break; 
			default:die('{"status":-1,"msg":"操作失败"}');  
		} 
		$sql = $this->mysql_model->db_upd(ADMIN,$data,'(username="'.$username.'")');		
		if ($sql) {
			$this->cache_model->delsome(ADMIN);
			die('{"status":200,"data":{"userName":"'.$username.'"},"msg":"success"}');  
		} else {
			die('{"status":-1,"msg":"操作失败"}');  
		}
	}
	
	

	
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */