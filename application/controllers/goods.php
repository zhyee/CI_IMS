<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Goods extends CI_Controller {

    public function __construct(){
        parent::__construct();
		$this->purview_model->checkpurview(68);
		$this->load->model('data_model');
    }
	
	public function index(){
		$this->load->view('goods/index');
	}

	//商品添加修改
	public function save() {
	    $id  = intval($this->input->post('id',TRUE));
		$act = str_enhtml($this->input->get('act',TRUE));
		$info['categoryId'] = $data['categoryid'] = intval($this->input->post('categoryId',TRUE));
		$info['unitId']     = $data['unitid'] = intval($this->input->post('baseUnitId',TRUE));
		$info['name']       = $data['name']   = str_enhtml($this->input->post('name',TRUE));
		$info['number']     = $data['number'] = str_enhtml($this->input->post('number',TRUE));
		$info['purPrice']   = $data['purprice'] = (float)$this->input->post('purPrice',TRUE);
		$info['remark']     = $data['remark'] = str_enhtml($this->input->post('remark',TRUE));
		$info['salePrice']  = $data['saleprice'] = (float)$this->input->post('salePrice',TRUE);
		$info['spec']       = $data['spec'] = str_enhtml($this->input->post('spec',TRUE));
		$info['unitCost']   = $data['unitcost'] = (float)$this->input->post('unitcost',TRUE);
		$info['quantity']   = $data['quantity'] = (float)$this->input->post('quantity',TRUE);
		$info['amount']     = $data['amount'] = (float)$this->input->post('amount',TRUE);
		$data['goods']      = $info['number'].' '.$info['name'].'_'.$data['spec'];
		
		strlen($data['name']) < 1 && die('{"status":-1,"msg":"名称不能为空"}'); 
		$data['categoryid'] < 1   && die('{"status":-1,"msg":"请选择商品分类"}'); 
		$data['unitid'] < 1       && die('{"status":-1,"msg":"请选择单位"}'); 
		$info['categoryName']   = $data['categoryname'] = $this->mysql_model->db_one(CATEGORY,'(id='.$data['categoryid'].')','name');
		$info['unitName']   = $data['unitname']     = $this->mysql_model->db_one(UNIT,'(id='.$data['unitid'].')','name');
		!$data['categoryname'] || !$data['unitname']  && die('{"status":-1,"msg":"参数错误"}');
		
		if ($act=='add') {
		    $this->purview_model->checkpurview(69);
			$this->mysql_model->db_count(GOODS,'(number="'.$data['number'].'")') > 0 && die('{"status":-1,"msg":"商品编号重复"}');
		    $sql = $this->mysql_model->db_inst(GOODS,$data);
			if ($sql) {
			    $info['id'] = $sql;
				$this->cache_model->delsome(GOODS);
				$this->data_model->logs('新增商品:'.$data['name']);
				die('{"status":200,"msg":"success","data":'.json_encode($info).'}');
			} else {
			    die('{"status":-1,"msg":"添加失败"}');
			}
		} elseif ($act=='update') {
		    $this->purview_model->checkpurview(70);
			$this->mysql_model->db_count(GOODS,'(id<>'.$id.') and (number="'.$data['number'].'")') > 0 && die('{"status":-1,"msg":"商品编号重复"}');
			$name = $this->mysql_model->db_one(GOODS,'(id='.$id.')','name');
		    $sql = $this->mysql_model->db_upd(GOODS,$data,'(id='.$id.')');
			if ($sql) {
			    $info['id'] = $id;
				$info['propertys'] = array();
			    $this->cache_model->delsome(GOODS);
				$this->data_model->logs('修改商品:'.$name.' 修改为 '.$data['name']);
				die('{"status":200,"msg":"success","data":'.json_encode($info).'}');
			} else {
				die('{"status":-1,"msg":"修改失败"}');
			}
		}
	}

	//商品删除
    public function del() {
	    $this->purview_model->checkpurview(71);
	    $id = str_enhtml($this->input->post('id',TRUE));
		if (strlen($id) > 0) {
		    $this->mysql_model->db_count(INVPU_INFO,'(goodsid in('.$id.'))')>0 && die('{"status":-1,"msg":"其中有商品发生业务不可删除"}'); 
			$this->mysql_model->db_count(INVSA_INFO,'(goodsid in('.$id.'))')>0 && die('{"status":-1,"msg":"其中有商品发生业务不可删除"}'); 
			$this->mysql_model->db_count(INVOI_INFO,'(goodsid in('.$id.'))')>0 && die('{"status":-1,"msg":"其中有商品发生业务不可删除"}'); 
		    $sql = $this->mysql_model->db_del(GOODS,'(id in('.$id.'))');   
		    if ($sql) {
			    $this->cache_model->delsome(GOODS);
				$this->data_model->logs('删除商品:ID='.$id);
				die('{"status":200,"msg":"success","data":{"msg":"","id":['.$id.']}}');
			} else {
			    die('{"status":-1,"msg":"删除失败"}');
			}
		}
	}
	
	//商品导出
	public function export() {
	    $this->purview_model->checkpurview(72);
	    sys_xls('商品明细.xls');
		$skey         = str_enhtml($this->input->get('skey',TRUE));
		$categoryid   = intval($this->input->get('assistId',TRUE));
		$where = '';
		if ($skey) {
			$where .= ' and goods like "%'.$skey.'%"';
		}
		if ($categoryid > 0) {
		    $cid = $this->cache_model->load_data(CATEGORY,'(1=1) and find_in_set('.$categoryid.',path)','id'); 
			if (count($cid)>0) {
			    $cid = join(',',$cid);
			    $where .= ' and categoryid in('.$cid.')';
			} 
		}
		$this->data_model->logs('导出商品');
		$data['list'] = $this->cache_model->load_data(GOODS,'(status=1) '.$where.' order by id desc');  
		$this->load->view('goods/export',$data);
	}	


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */