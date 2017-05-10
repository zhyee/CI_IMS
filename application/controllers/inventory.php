<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Inventory extends CI_Controller {

    public function __construct(){
        parent::__construct();
		$this->purview_model->checkpurview(11);
		$this->load->model('data_model');
		$this->uid  = $this->session->userdata('uid');
		$this->name = $this->session->userdata('name');
    }
	
	public function index(){
		$this->load->view('inventory/index');
	}
	
	public function query() {
		$id  = intval($this->input->get_post('invId',TRUE));
	    $v   = '';
		$order = ' order by a.id desc';
		$where = ' and a.id='.$id;
	    $data['status'] = 200;
		$data['msg']    = 'success'; 
		$data['data']['page']        = 1;
		$data['data']['records']     = 1;                                                       
		$data['data']['total']       = 1;                                                       
		$list = $this->data_model->inventory($where,$order);  
		foreach ($list as $arr=>$row) {
		    $v[$arr]['invId']         = intval($row['id']);
			$v[$arr]['locationId']    = 0;
			$v[$arr]['qty']           = $row['qty'];
			$v[$arr]['locationName']  = $row['goods'];
		}
		$data['data']['rows']         = is_array($v) ? $v : '';
		die(json_encode($data)); 
	}

	/* 支持pjax 查询库存 */

	public function pjax_lists()
    {
        $aid = intval($this->input->get('aid'));
        $aid_en = intval($this->input->get('aid_en'));
        $callback = trim($this->input->get('callback'));

        if ($aid)
        {
            $where = " AND a.aid = $aid ";
        }

        if ($aid_en)
        {
            $where = " AND a.aid_en = $aid_en";
        }

        $order = ' order by a.id desc';

        $list = $this->data_model->inventory($where, $order);

        if (!$list)
        {
            $list = array();
        }

        if ($callback)
        {
            echo $callback . '(' . json_encode($list) . ');';
        }
        else
        {
            echo json_encode($list);
        }
    }


	//库存查询
	public function lists() {
		$page        = max(intval($this->input->get_post('page',TRUE)),1);
		$rows        = max(intval($this->input->get_post('rows',TRUE)),100);
		$categoryid  = intval($this->input->get_post('categoryId',TRUE));
		$goods       = str_enhtml($this->input->get_post('goods',TRUE));
		$qty         = intval($this->input->get_post('showZero',TRUE));
	    $v = '';
		$where = '';
		$order = ' order by a.id desc';
	    $data['status'] = 200;
		$data['msg']    = 'success'; 
		if ($categoryid > 0) {
		    $cid = $this->cache_model->load_data(CATEGORY,'(1=1) and find_in_set('.$categoryid.',path)','id'); 
			if (count($cid)>0) {
			    $cid = join(',',$cid);
			    $where .= ' and a.categoryid in('.$cid.')';
			} 
		}
		if ($goods) {
		    $where .= ' and a.goods like "%'.$goods.'%"';
		}
		if ($qty>0) {
		    $order = ' HAVING (qty<=0)';
		}
		
		$offset = $rows * ($page-1);
		$data['data']['page']        = $page;
		$data['data']['records']     = 1000;                                                       //总条数
		$data['data']['total']       = ceil($data['data']['records']/$rows);                       //总分页数
		$list = $this->data_model->inventory($where,$order);  
		foreach ($list as $arr=>$row) {
		    $v[$arr]['dbId']         = intval($row['id']);
			$v[$arr]['assistName']   = $row['categoryname'];
			$v[$arr]['invSpec']      = $row['spec'];
			$v[$arr]['locationId']   = intval($row['id']);
			$v[$arr]['invName']      = $row['name'];
			$v[$arr]['qty']          = number_format($row['qty'],2);
			$v[$arr]['locationName'] = 0;
			$v[$arr]['assistId']     = intval($row['categoryid']);
			$v[$arr]['invCost']      = (float)($row['unitcost']);
			$v[$arr]['invId']        = intval($row['id']);
			$v[$arr]['invNumber']    = $row['number'];
			$v[$arr]['unitId']       = $row['unitid'];
			$v[$arr]['unitName']     = $row['unitname'];
			$v[$arr]['amount']       = $row['qty']*$row['unitcost'];
		}
		$data['data']['rows']        = is_array($v) ? $v : '';
		die(json_encode($data));
	}
	
    //生成盘点单据
	public function generator() {
	    $this->purview_model->checkpurview(12);
		$data = $this->input->post('postData',TRUE);
		if (strlen($data)>0) {
		     $i = 0;
			 $n = 0;
			 $a = 0;
			 $b = 0;
			 $qty1 = 0;
			 $qty2 = 0;
			 $amount1 = 0;
			 $amount2 = 0;
			 $msg = '';
			 $v1 = array();
			 $v2 = array();
		     $data = (array)json_decode($data);
			 $this->db->trans_begin();
			 if (is_array($data['entries'])) {
			     foreach ($data['entries'] as $arr=>$row) {
					 if ($row->checkInventory<0) {
					     die('{"status":400,"msg":"盘点库存要为数字，请输入有效数字！"}');
					 }
				     if ($row->change>0) {
					     $i += 1;
						 $qty1 += (float)$row->change;                     //总数量
						 $amount1 += $row->invCost*(float)$row->change;    //总价
					 } elseif ($row->change<0) {
					     $n += 1;
						 $qty2 += abs($row->change);
						 $amount2 += $row->invCost*abs($row->change);
					 } 
				} 
			 }
			 
			 if ($i==0 && $n==0) {
			     die('{"status":400,"msg":"请先进行盘点！"}');
			 }
			 //盘盈
			 if ($i>0) {
				 $info1['billno']      = str_no('QTRK');
				 $info1['billdate']    = date('Y-m-d');
				 $info1['type']        = 2;
				 $info1['typename']    = '盘盈';
				 $info1['description'] = $data['description'];
				 $info1['totalamount'] = $amount1;
				 $info1['totalqty']    = $qty1;
				 $info1['username']    = $this->name;
				 $info1['billtype']    = 1;
				 $invoiid = $this->mysql_model->db_inst(INVOI,$info1);
				 if (is_array($data['entries'])) {
					 foreach ($data['entries'] as $arr=>$row) {
					     if ($row->change>0) {
							 $v1[$a]['invoiid']       = $invoiid;
							 $v1[$a]['billno']        = $info1['billno'];
							 $v1[$a]['type']          = $info1['type'];
							 $v1[$a]['billtype']      = $info1['billtype'];   
							 $v1[$a]['typename']      = $info1['typename'];
							 $v1[$a]['goodsid']       = $row->invId;
							 $v1[$a]['goodsno']       = $row->invNumber; 
							 $v1[$a]['qty']           = abs($row->change); 
							 $v1[$a]['amount']        = $row->invCost*abs($row->change); 
							 $v1[$a]['price']         = $row->invCost; 
							 $v1[$a]['billdate']      = $info1['billdate']; 
							 $a += 1;
						 }
					} 
				 }
				 $this->mysql_model->db_inst(INVOI_INFO,$v1);
			 }
			 //盘亏
			 if ($n>0) {
				 $info2['billno']      = str_no('QTCK');
				 $info2['billdate']    = date('Y-m-d');
				 $info2['type']        = 4;
				 $info2['typename']    = '盘亏';
				 $info2['description'] = $data['description'];
				 $info2['totalamount'] = $amount2;
				 $info2['totalqty']    = $qty2;
				 $info2['username']    = $this->name;
				 $info2['billtype']    = 2;
				 $invoiid = $this->mysql_model->db_inst(INVOI,$info2);
				 if (is_array($data['entries'])) {
					 foreach ($data['entries'] as $arr=>$row) {
					     if ($row->change>=0) {
						 } else {
							 $v2[$b]['invoiid']       = $invoiid;
							 $v2[$b]['billno']        = $info2['billno'];
							 $v2[$b]['type']          = $info2['type'];
							 $v2[$b]['billtype']      = $info2['billtype'];   
							 $v2[$b]['typename']      = $info2['typename'];
							 $v2[$b]['goodsid']       = $row->invId;
							 $v2[$b]['goodsno']       = $row->invNumber; 
							 $v2[$b]['qty']           = $row->change; 
							 $v2[$b]['amount']        = $row->invCost*abs($row->change); 
							 $v2[$b]['price']         = $row->invCost; 
							 $v2[$b]['billdate']      = $info2['billdate']; 
							 $b += 1;
					     }	 
					} 
				 }
				 $this->mysql_model->db_inst(INVOI_INFO,$v2);
			 }
			 
			 if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				die('{"status":400,"msg":"盘点失败！"}');
			 } else {
				$this->db->trans_commit();
				$this->cache_model->delsome(GOODS);
				$this->cache_model->delsome(INVOI);
				$this->cache_model->delsome(INVOI_INFO); 
				if ($i>0) {
				   $msg .= '成功生成其他入库单 单据编号为'.$info1['billno'].' ';
				}
				if ($n>0) {
				   $msg .= '其他出库单 单据编号为'.$info2['billno'].'';
				}
				$this->data_model->logs('生成盘点记录');
				die('{"status":200,"msg":"'.$msg.'"}');
			 }
		}
		die('{"status":400,"msg":"请先进行盘点！"}');
	}
	
	//导出库存表
	public function export() {
	    $this->purview_model->checkpurview(13);
	    sys_xls('盘点表.xls');
		$categoryid  = intval($this->input->get_post('categoryId',TRUE));
		$goods = str_enhtml($this->input->get_post('goods',TRUE));
		$qty = intval($this->input->get_post('showZero',TRUE));
		$where = '';
		$order = 'order by a.id desc';
		if ($categoryid > 0) {
		    $cid = $this->cache_model->load_data(CATEGORY,'(1=1) and find_in_set('.$categoryid.',path)','id'); 
			if (count($cid)>0) {
			    $cid = join(',',$cid);
			    $where .= ' and a.categoryid in('.$cid.')';
			} 
		}
		if ($qty>0) {
		    $order = ' HAVING (qty<=0)';
		}
		if ($goods)  $where .= ' and a.goods like "%'.$goods.'%"';   
		$this->data_model->logs('导出盘点记录');    
		$data['list'] = $this->data_model->inventory($where,$order);  
		$this->load->view('inventory/export',$data);
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */