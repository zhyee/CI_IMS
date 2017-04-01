<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Backup extends CI_Controller {

    public function __construct(){
        parent::__construct();
		$this->purview_model->checkpurview(84);
		$this->load->model('data_model');
		$this->conf = $this->config->config;
		$this->name = str_no('jxc_').'.sql';
		$this->load->helper(array('number','directory','download')); 
    }
	
	public function index(){
		$this->load->view('backup/index');
	}
	
	//列表
	public function lists(){
	    $v = array();
	    $list = get_dir_file_info($this->conf['db_url']);
		$data['status'] = 200;
		$data['msg'] = 'success';
		$i = 0;
		foreach ($list as $arr=>$row) {
		    $v[$i]['fid'] = $row['name']; 
			$v[$i]['createTime'] = date("Y-m-d H:i:s", $row['date']); 
			$v[$i]['username'] = $row['date']; 
			$v[$i]['filename'] = $row['name']; 
			$v[$i]['dbid'] = 0; 
			$v[$i]['size'] = $row['size']; 
			$i++;
		}
		$data['data']['items'] = $v;
		$data['totalsize']     = 1;
		die(json_encode($data));
	}
	
	//备份
	public function add(){
	    $this->load->dbutil();
		$info = &$this->dbutil->backup(); 
		$path = $this->conf['db_url'].$this->name;
		if (write_file($path, $info)) {
			$this->data_model->logs('备份与恢复,备份文件名:'.$this->name);
			$data['createTime'] = date('Y-m-d H:i:s');
			$data['username'] = $this->name;
			$data['filename'] = $this->name;
			$data['dbid'] = 0;
			$data['fid']  = $this->name;
			$data['size'] = filesize($path);
		    die('{"status":200,"msg":"success","data":'.json_encode($data).'}');
		} else {
		    die('{"status":-1,"msg":"参数错误"}');
		}
	}
	
	//删除
    public function del() {
		$name = str_enhtml($this->input->get_post('name',TRUE));
		$path = $this->conf['db_url'].$name;
		if (@unlink($path)) {
		    $this->data_model->logs('备份与恢复,删除文件名:'.$name);
			die('{"status":200,"msg":"success","data":{"id":"1"}}');
		} else {
		    die('{"status":-1,"msg":"删除失败"}'); 
		}
	}
    
	//下载
	public function down() {
		$name = str_enhtml($this->input->get_post('name',TRUE));
		$path = $this->conf['db_url'].$name;
		$info = read_file($path);
		if ($info) {
		    $this->data_model->logs('备份与恢复,下载文件名:'.$name);
			force_download($name, $info); 
		} else {
		    die('{"status":-1,"msg":"下载失败"}'); 
		}
	}
	
	//恢复
	public function recovery(){
	    $name = str_enhtml($this->input->get_post('name',TRUE));
		$path = $this->conf['db_url'].$name;
	    $info = read_file($path);
		if ($info) {
		    $this->db->trans_begin();
			$list = explode(";\n",$info);
			foreach ($list as $sql) {
				$this->db->query($sql);
			}
			if ($this->db->trans_status() === FALSE) {
			    $this->db->trans_rollback();
				die('{"status":-1,"msg":"恢复失败"}'); 
			} else {
			    $this->db->trans_commit();
				$this->data_model->logs('备份与恢复,恢复文件名:'.$name);
			    die('{"status":200,"msg":"success"}');
			}
		} else {
		    die('{"status":-1,"msg":"恢复失败"}'); 
		}
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */