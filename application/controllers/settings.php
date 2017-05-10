<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Settings extends CI_Controller {

    public function __construct(){
        parent::__construct();
		$this->load->model('config_model');
		$this->purview_model->checkpurview();
    }
	
	//系统参数
	public function parameter() {
	    $this->purview_model->checkpurview(81);
		$data = str_enhtml($this->input->post(NULL,TRUE));
		if (is_array($data) && count($data)>0) {
		    $dir = './data/config/parameter_config.php';
			$err = $this->config_model->set_config($data,$dir);
			if ($err) {
			    die('{"status":200,"msg":"success"}');
			} else {
			    alert('设置失败'); 
			}
		} else {
		    $this->load->view('settings/parameter',$data);	
		}
	}
	
	//皮肤切换
	public function skins() {
		$skin = $this->input->get_post('skin',TRUE);
		$skin = $skin ? $skin : 'green';
		set_cookie('skin',$skin,120000);
		die('{"status":200,"msg":"success"}');
	}

	
	//供应商分类
	public function vendor_cate_manage() {
		$this->load->view('settings/vendor_cate_manage');	
	}
	
	//客户分类
	public function customer_cate_manage() {
		$this->load->view('settings/customer_cate_manage');	
	}
	
	//批量选择供应商 
	public function vendor_batch() {
		$this->load->view('settings/vendor_batch');	
	}
	
	//批量选择客户
	public function customer_batch() {
		$this->load->view('settings/customer_batch');	
	}
	
	//批量选择商品 
	public function goods_batch() {
		$this->load->view('settings/goods_batch');	
	}
	
	//新增商品
	public function goods_manage() {
	    $res = $this->db->query("SELECT DISTINCT aid,aid_en FROM ci_goods")->result_array();
        $aidArr = array();
        $aidArr_en = array();
        if ($res)
        {
            foreach ($res as $row)
            {
                if ((int)$row['aid'] > 0)
                {
                    $aidArr[] = (int)$row['aid'];
                }

                if ((int)$row['aid_en'] > 0)
                {
                    $aidArr_en[] = (int)$row['aid_en'];
                }
            }
        }
        $data['aidArr'] = json_encode($aidArr);
        $data['aidArr_en'] = json_encode($aidArr_en);
		$this->load->view('settings/goods_manage', $data);
	}
	
	//结算方式选择
	public function settlement_manage() {
		$this->load->view('settings/settlement_manage');	
	}
	
	//供应商选择
	public function vendor_manage() {
		$this->load->view('settings/vendor_manage');	
	}
	
	//客户选择
	public function customer_manage() {
		$this->load->view('settings/customer_manage');	
	}
	
	//单位
	public function unit_manage() {
		$this->load->view('settings/unit_manage');	
	}
	
	//高级查询
	public function other_search() {
		$this->load->view('settings/other_search');	
	}
	
	//单个库存查询
	public function inventory() {
		$this->load->view('settings/inventory');	
	}
	
	//选择客户
	public function select_customer() {
		$this->load->view('settings/select_customer');	
	}
	
	//选择供应商
	public function select_vendor() {
		$this->load->view('settings/select_vendor');	
	}
	
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */