<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Logs extends CI_Controller {

    public function __construct(){
        parent::__construct();
		$this->purview_model->checkpurview(83);
    }
	
	public function index(){
		$this->load->view('logs/index');
	}
	
	//导出日志
	public function export() {
	    sys_xls('日志明细.xls');
		$user   = str_enhtml($this->input->get('user',TRUE));
		$where = '';
		if ($user) {
			$where .= ' and username="'.$user.'"';
		}
		$data['list'] = $this->cache_model->load_data(LOG,'(1=1) '.$where.' order by id desc');   
		$this->load->view('logs/export',$data);
	}	
}

