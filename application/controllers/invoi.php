<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Invoi extends CI_Controller {

    public function __construct(){
        parent::__construct();
		$this->purview_model->checkpurview();
		$this->load->model('data_model');
		$this->uid   = $this->session->userdata('uid');
		$this->name = $this->session->userdata('name');
    }
	
	public function index(){
	    $this->purview_model->checkpurview(14);
		$this->load->view('invoi/index');
	}
	
	public function outindex(){
	    $this->purview_model->checkpurview(18);
		$this->load->view('invoi/outindex');
	}
	
	//入库
	public function in(){
	    $this->purview_model->checkpurview(15);
	    $data = $this->input->post('postData',TRUE);
		if (strlen($data)>0) {
		     $data = (array)json_decode($data);
			 (!isset($data['buId']) && $data['buId']<1) && die('{"status":-1,"msg":"请选择购货单位"}');
			 $contact = $this->mysql_model->db_one(CONTACT,'(id='.intval($data['buId']).')');
			 count($contact)<1 && die('{"status":-1,"msg":"请选择购货单位"}');
			 $info['billno']      = str_no('QTRK');
			 $info['contactid']   = intval($data['buId']);
			 $info['contactname'] = $contact['number'].' '.$contact['name'];
			 $info['billdate']    = $data['date'];
			 $info['type']        = intval($data['transTypeId']);
			 $info['typename']    = $data['transTypeName'];
			 $info['description'] = $data['description'];
			 $info['totalamount'] = (float)$data['totalAmount'];
			 $info['totalqty']    = (float)$data['totalQty'];
			 $info['uid']         = $this->uid;
			 $info['username']    = $this->name;
			 $info['billtype']    = 1;
			 
			 $this->db->trans_begin();
			 $invoiid = $this->mysql_model->db_inst(INVOI,$info);
			 $v = array();
			 if (is_array($data['entries'])) {
			     foreach ($data['entries'] as $arr=>$row) {
				     $v[$arr]['invoiid']       = $invoiid;
				     $v[$arr]['billno']        = $info['billno'];
				     $v[$arr]['contactid']     = $info['contactid'];
					 $v[$arr]['contactname']   = $info['contactname'];
					 $v[$arr]['type']          = $info['type'];
					 $v[$arr]['billtype']      = $info['billtype'];   
					 $v[$arr]['typename']      = $info['typename'];
					 $v[$arr]['goodsid']       = $row->invId;
					 $v[$arr]['goodsno']       = $row->invNumber; 
					 $v[$arr]['qty']           = (float)$row->qty; 
					 $v[$arr]['amount']        = (float)$row->amount; 
					 $v[$arr]['price']         = (float)$row->price; 
					 $v[$arr]['description']   = $row->description; 
                     $v[$arr]['billdate']      = $data['date']; 
				} 
			 }
			 $this->mysql_model->db_inst(INVOI_INFO,$v);
			 if ($this->db->trans_status() === FALSE) {
			    $this->db->trans_rollback();
				die();
			 } else {
			    $this->db->trans_commit();
				$this->cache_model->delsome(GOODS);
				$this->cache_model->delsome(INVOI);
				$this->cache_model->delsome(INVOI_INFO); 
				$this->data_model->logs('新增其他入库 单据编号：'.$info['billno']);
			    die('{"status":200,"msg":"success","data":{"id":'.intval($invoiid).'}}');
			 }
		} else {
		    $data['billno'] = str_no('QTRK');
		    $this->load->view('invoi/in',$data);
		}
	}
	
	//入库修改
	public function inedit() {
	    $this->purview_model->checkpurview(16);
	    $id   = intval($this->input->get('id',TRUE));
	    $data = $this->input->post('postData',TRUE);
		if (strlen($data)>0) {
		     $data = (array)json_decode($data);
			 !isset($data['id']) && die('{"status":-1,"msg":"参数错误"}');
			 (!isset($data['buId']) && $data['buId']<1) && die('{"status":-1,"msg":"请选择购货单位"}');
			 $contact = $this->mysql_model->db_one(CONTACT,'(id='.intval($data['buId']).')');
			 count($contact)<1 && die('{"status":-1,"msg":"请选择购货单位"}');
			 $id                  = intval($data['id']);
			 $info['billno']      = $data['billNo'];
			 $info['billtype']    = 1;
			 $info['type']        = intval($data['transTypeId']);
			 $info['typename']    = $data['transTypeName'];
			 $info['contactid']   = intval($data['buId']);
			 $info['contactname'] = $contact['number'].' '.$contact['name'];
			 $info['description'] = $data['description'];
			 $info['totalamount'] = (float)$data['totalAmount'];
			 $info['totalqty']    = (float)$data['totalQty'];
			 $info['uid']         = $this->uid;
			 $info['username']    = $this->name;
			 $info['billdate']    = $data['date'];
			 $v = array();
			 $this->db->trans_begin();
			 $this->mysql_model->db_upd(INVOI,$info,'(id='.$id.')');
			 $this->mysql_model->db_del(INVOI_INFO,'(invoiid='.$id.')');
			 if (is_array($data['entries'])) {
			     foreach ($data['entries'] as $arr=>$row) {
				     $v[$arr]['invoiid']       = $id;
				     $v[$arr]['billno']        = $info['billno'];
					 $v[$arr]['type']          = $info['type'];
					 $v[$arr]['billtype']      = $info['billtype'];
					 $v[$arr]['typename']      = $info['typename'];
					 $v[$arr]['contactid']     = $info['contactid'];
					 $v[$arr]['contactname']   = $info['contactname'];
					 $v[$arr]['goodsid']       = $row->invId;
					 $v[$arr]['qty']           = (float)$row->qty; 
					 $v[$arr]['amount']        = (float)$row->amount; 
					 $v[$arr]['price']         = (float)$row->price; 
					 $v[$arr]['description']   = $row->description; 
					 $v[$arr]['goodsno']       = $row->invNumber; 
                     $v[$arr]['billdate']      = $data['date']; 
				} 
			 }
			 $this->mysql_model->db_inst(INVOI_INFO,$v);
			 if ($this->db->trans_status() === FALSE) {
			    $this->db->trans_rollback();
				die();
			 } else {
			    $this->db->trans_commit();
				$this->cache_model->delsome(GOODS);
				$this->cache_model->delsome(INVOI);
				$this->cache_model->delsome(INVOI_INFO); 
				$this->data_model->logs('修改其他入库 单据编号：'.$info['billno']);
				die('{"status":200,"msg":"success","data":{"id":'.$id.'}}');
			 }
		} else {
		    $data = $this->mysql_model->db_one(INVOI,'(id='.$id.')');
			if (count($data)>0) {
				$this->load->view('invoi/inedit',$data);
			} else {
			    $data['billno'] = str_no('QTRK');
			    $this->load->view('invoi/in',$data);
			}
		}
	}
	
	
	//出库
	public function out(){
	    $this->purview_model->checkpurview(19);
	    $data = $this->input->post('postData',TRUE);
		if (strlen($data)>0) {
		     $data = (array)json_decode($data);
			 (!isset($data['buId']) && $data['buId']<1) && die('{"status":-1,"msg":"请选择客户"}');
			 $contact = $this->mysql_model->db_one(CONTACT,'(id='.intval($data['buId']).')');
			 count($contact)<1 && die('{"status":-1,"msg":"请选择客户"}');
			 $info['billtype']    = 2;
			 $info['billno']      = str_no('QTCK');
			 $info['contactid']   = intval($data['buId']);
			 $info['contactname'] = $contact['number'].' '.$contact['name'];
			 $info['billdate']    = $data['date'];
			 $info['type']        = $data['transTypeId'];
			 $info['typename']    = $data['transTypeName'];
			 $info['description'] = $data['description'];
			 $info['totalamount'] = -(float)$data['totalAmount'];
			 $info['totalqty']    = (float)$data['totalQty'];
			 $info['uid']         = $this->uid;
			 $info['username']    = $this->name;
			 $this->db->trans_begin();
			 $invoiid = $this->mysql_model->db_inst(INVOI,$info);
			 $v = array();
			 if (is_array($data['entries'])) {
			     foreach ($data['entries'] as $arr=>$row) {
				     $v[$arr]['invoiid']       = intval($invoiid);
					 $v[$arr]['billtype']      = 2;
				     $v[$arr]['billno']        = $info['billno'];
				     $v[$arr]['contactid']     = intval($data['buId']);
					 $v[$arr]['contactname']   = $data['contactName'];
					 $v[$arr]['type']          = $data['transTypeId'];
					 $v[$arr]['typename']      = $data['transTypeName'];
					 $v[$arr]['goodsid']       = intval($row->invId);
					 $v[$arr]['goodsno']       = $row->invNumber; 
					 $v[$arr]['qty']           = -(float)($row->qty); 
					 $v[$arr]['amount']        = -(float)$row->amount; 
					 $v[$arr]['price']         = (float)$row->price; 
					 $v[$arr]['description']   = $row->description; 
                     $v[$arr]['billdate']      = $data['date']; 
				} 
			 }
			 $this->mysql_model->db_inst(INVOI_INFO,$v);
			 if ($this->db->trans_status() === FALSE) {
			    $this->db->trans_rollback();
				die();
			 } else {
			    $this->db->trans_commit();
				$this->cache_model->delsome(GOODS);
				$this->cache_model->delsome(INVOI);
				$this->cache_model->delsome(INVOI_INFO); 
				$this->data_model->logs('新增其他出库 单据编号：'.$info['billno']);
				die('{"status":200,"msg":"success","data":{"id":'.intval($invoiid).'}}');
			 }
		} else {
		    $data['billno'] = str_no('QTCK');
		    $this->load->view('invoi/out',$data);
		}
	}
	
	
	//出库修改
	public function outedit(){
	    $this->purview_model->checkpurview(20);
	    $id   = intval($this->input->get('id',TRUE));
	    $data = $this->input->post('postData',TRUE);
		if (strlen($data)>0) {
		     $data = (array)json_decode($data);
			 !isset($data['id']) && die('{"status":-1,"msg":"参数错误"}');
			 (!isset($data['buId']) && $data['buId']<1) && die('{"status":-1,"msg":"请选择客户"}');
			 $contact = $this->mysql_model->db_one(CONTACT,'(id='.intval($data['buId']).')');
			 count($contact)<1 && die('{"status":-1,"msg":"请选择客户"}');
			 $info['billtype']    = 2;
			 $id                  = intval($data['id']);
			 $info['billno']      = $data['billNo'];
			 $info['type']        = intval($data['transTypeId']);
			 $info['typename']    = $data['transTypeName'];
			 $info['contactid']   = intval($data['buId']);
			 $info['contactname'] = $contact['number'].' '.$contact['name'];
			 $info['description'] = $data['description'];
			 $info['totalamount'] = -(float)$data['totalAmount'];
			 $info['totalqty']    = (float)$data['totalQty'];
			 $info['uid']         = $this->uid;
			 $info['username']    = $this->name;
			 $info['billdate']    = $data['date'];
			 $v = array();
			 $this->db->trans_begin();
			 $this->mysql_model->db_count(INVOI,'(id<>'.$id.') and (billno="'.$info['billno'].'")')>0 && die('{"status":-1,"msg":"其他入库单已存在"}');
			 $this->mysql_model->db_upd(INVOI,$info,'(id='.$id.')');
			 $this->mysql_model->db_del(INVOI_INFO,'(invoiid='.$id.')');
			 if (is_array($data['entries'])) {
			     foreach ($data['entries'] as $arr=>$row) {
				     $v[$arr]['invoiid']       = $id;
				     $v[$arr]['billno']        = $info['billno'];
					 $v[$arr]['type']          = $info['type'];
					 $v[$arr]['billtype']      = $info['billtype'];
					 $v[$arr]['typename']      = $info['typename'];
					 $v[$arr]['contactid']     = $info['contactid'];
					 $v[$arr]['contactname']   = $info['contactname'];
					 $v[$arr]['goodsid']       = $row->invId;
					 $v[$arr]['qty']           = -(float)($row->qty); 
					 $v[$arr]['amount']        = -(float)($row->amount); 
					 $v[$arr]['price']         = (float)$row->price; 
					 $v[$arr]['description']   = $row->description; 
					 $v[$arr]['goodsno']       = $row->invNumber; 
                     $v[$arr]['billdate']      = $data['date']; 
				} 
			 }
			 $this->mysql_model->db_inst(INVOI_INFO,$v);
			 if ($this->db->trans_status() === FALSE) {
			    $this->db->trans_rollback();
				die();
			 } else {
			    $this->db->trans_commit();
				$this->cache_model->delsome(GOODS);
				$this->cache_model->delsome(INVOI);
				$this->cache_model->delsome(INVOI_INFO); 
				$this->data_model->logs('修改其他出库 单据编号：'.$info['billno']);
				die('{"status":200,"msg":"success","data":{"id":'.$id.'}}');
			 }
		} else {
		    $data = $this->mysql_model->db_one(INVOI,'(id='.$id.')');
			if (count($data)>0) {
				$this->load->view('invoi/outedit',$data);
			} else {
			    $data['billno'] = str_no('QTRK');
			    $this->load->view('invoi/out',$data);
			}
		}
	}
	

	//入库列表
	public function inlist() {
	    $this->purview_model->checkpurview(14);
	    $v = '';
	    $data['status'] = 200;
		$data['msg']    = 'success'; 
		$page = max(intval($this->input->get_post('page',TRUE)),1);
		$rows = max(intval($this->input->get_post('rows',TRUE)),100);
		$key  = str_enhtml($this->input->get_post('matchCon',TRUE));
		$stt  = str_enhtml($this->input->get_post('beginDate',TRUE));
		$ett  = str_enhtml($this->input->get_post('endDate',TRUE));
		$where = '';
		if ($key) {
		    $where .= ' and (billno like "%'.$key.'%" or contactname like "%'.$key.'%" or description like "%'.$key.'%")';
		}
		if ($stt) {
		    $where .= ' and billdate>="'.$stt.'"';
		}
		if ($ett) {
		    $where .= ' and billdate<="'.$ett.'"';
		}
		$offset = $rows*($page-1);
		$data['data']['page']      = $page;
		$data['data']['records']   = $this->cache_model->load_total(INVOI,'(billtype=1) '.$where);     //总条数
		$data['data']['total']     = ceil($data['data']['records']/$rows);                             //总分页数
		$list = $this->cache_model->load_data(INVOI,'(1=1) and billtype=1 '.$where.' order by id desc limit '.$offset.','.$rows.'');  
		foreach ($list as $arr=>$row) {
		    $v[$arr]['amount']       = (float)abs($row['totalamount']);
			$v[$arr]['id']           = intval($row['id']);
			$v[$arr]['transType']    = intval($row['type']);;
			$v[$arr]['billtype']     = intval($row['billtype']);;
			$v[$arr]['contactName']  = $row['contactname'];
			$v[$arr]['description']  = $row['description'];
			$v[$arr]['billNo']       = $row['billno'];
			$v[$arr]['billDate']     = $row['billdate'];
			$v[$arr]['userName']     = $row['username'];
			$v[$arr]['transTypeName']= $row['typename'];
		}
		$data['data']['rows']        = is_array($v) ? $v : '';
		die(json_encode($data));
	}
	
	//出库列表
	public function outlist() {
	    $this->purview_model->checkpurview(18);
	    $v = '';
	    $data['status'] = 200;
		$data['msg']    = 'success'; 
		$page = max(intval($this->input->get_post('page',TRUE)),1);
		$rows = max(intval($this->input->get_post('rows',TRUE)),100);
		$key  = str_enhtml($this->input->get_post('matchCon',TRUE));
		$stt  = str_enhtml($this->input->get_post('beginDate',TRUE));
		$ett  = str_enhtml($this->input->get_post('endDate',TRUE));
		$where = '';
		if ($key) {
		    $where .= ' and (billno like "%'.$key.'%" or contactname like "%'.$key.'%" or description like "%'.$key.'%")';
		}
		if ($stt) {
		    $where .= ' and billdate>="'.$stt.'"';
		}
		if ($ett) {
		    $where .= ' and billdate<="'.$ett.'"';
		}
		$offset = $rows*($page-1);
		$data['data']['page']      = $page;
		$data['data']['records']   = $this->cache_model->load_total(INVOI,'(billtype=2) '.$where.'');   //总条数
		$data['data']['total']     = ceil($data['data']['records']/$rows);    //总分页数
		$list = $this->cache_model->load_data(INVOI,'(1=1)  and billtype=2 '.$where.' order by id desc limit '.$offset.','.$rows.'');  
		foreach ($list as $arr=>$row) {
		    $v[$arr]['amount']       = (float)abs($row['totalamount']);
			$v[$arr]['id']           = intval($row['id']);
			$v[$arr]['transType']    = intval($row['type']);;
			$v[$arr]['billtype']     = intval($row['billtype']);;
			$v[$arr]['contactName']  = $row['contactname'];
			$v[$arr]['description']  = $row['description'];
			$v[$arr]['billNo']       = $row['billno'];
			$v[$arr]['billDate']     = $row['billdate'];
			$v[$arr]['userName']     = $row['username'];
			$v[$arr]['transTypeName']= $row['typename'];
		}
		$data['data']['rows']        = is_array($v) ? $v : '';
		die(json_encode($data));
	}
	
	//其他入库、出库类型
	public function type(){
	    $type   = str_enhtml($this->input->get_post('type',TRUE));
		if (strlen($type)>0) {
		    $v = '';
		    $data['status'] = 200;
		    $data['msg']    = 'success';
		    $list = $this->cache_model->load_data(INVOI_TYPE,'(type="'.$type.'") order by id');  
			foreach ($list as $arr=>$row) {
				$v[$arr]['acctId']        = 0;
				$v[$arr]['calCost']       = 1;
				$v[$arr]['commission']    = false;
				$v[$arr]['direction']     = 1;
				$v[$arr]['free']          = false;
				$v[$arr]['id']            = intval($row['id']);
				$v[$arr]['inOut']         = (float)$row['inout'];;
				$v[$arr]['name']          = $row['name']; 
				$v[$arr]['process']       = false;
				$v[$arr]['sysDefault']    = true; 
				$v[$arr]['sysDelete']     = false;
				$v[$arr]['tableName']     = "t_scm_inventryoi";
				$v[$arr]['typeId']        = intval($row['id']);
				$v[$arr]['voucher']       = true; 
			}
			$data['data']['items']        = is_array($v) ? $v : '';
			$data['data']['totalsize']    = $this->cache_model->load_total(INVOI_TYPE,'(type="'.$type.'")');
			die(json_encode($data));
		}	
	}
	
	//修改单据数据回显
	public function info(){
	    $id   = intval($this->input->get_post('id',TRUE));
		$type = intval($this->input->get_post('type',TRUE));
		$data = $this->mysql_model->db_one(INVOI,'(billtype='.$type.') and (id='.$id.')');
		if (count($data)>0) {
			$v = '';
			$info['status'] = 200;
			$info['msg']    = 'success'; 
			$info['data']['id']                 = intval($data['id']);
			$info['data']['buId']               = intval($data['contactid']);
			$info['data']['contactName']        = $data['contactname'];
			$info['data']['date']               = $data['billdate'];
			$info['data']['billNo']             = $data['billno'];
			$info['data']['billType']           = intval($data['billtype']);
			$info['data']['transType']          = intval($data['type']);
			$info['data']['totalQty']           = (float)$data['totalqty'];
			$info['data']['totalAmount']        = (float)abs($data['totalamount']);
			$info['data']['userName']           = $data['username'];
			$info['data']['status']             = 'edit';
			$info['data']['description']        = $data['description'];
			$list = $this->data_model->invoi_info(' and (a.invoiid='.$id.')','order by a.id desc');  
			foreach ($list as $arr=>$row) {
				$v[$arr]['invSpec']           = $row['spec'];
				$v[$arr]['taxRate']           = intval($row['id']);
				$v[$arr]['srcOrderEntryId']   = 0;
				$v[$arr]['srcOrderNo']        = NULL;
				$v[$arr]['locationId']        = 0;
				$v[$arr]['goods']             = $row['goodsno'].' '.$row['goodsname'].' '.$row['spec'];
				$v[$arr]['invName']           = $row['goodsname'];
				$v[$arr]['qty']               = (float)abs($row['qty']);
				$v[$arr]['locationName']      = '';
				$v[$arr]['amount']            = (float)abs($row['amount']);
				$v[$arr]['taxAmount']         = (float)abs($row['amount']);
				$v[$arr]['price']             = (float)abs($row['price']);
				$v[$arr]['tax']               = 0;
				$v[$arr]['mainUnit']          = $row['unitname'];
				$v[$arr]['invId']             = intval($row['goodsid']);
				$v[$arr]['invNumber']         = $row['number'];
				$v[$arr]['unitId']            = intval($row['unitid']);
				$v[$arr]['srcOrderId']        = 0;
			}
			$info['data']['entries']     = is_array($v) ? $v : '';
			$info['data']['accId']       = 0;
			$info['data']['accounts']    = array();
			die(json_encode($info));
		} else { 
			alert('参数错误');
		}
	}
	
	
	//删除
	public function del(){
	    $this->purview_model->checkpurview(17);
	    $id   = intval($this->input->get('id',TRUE));
		$data = $this->mysql_model->db_one(INVOI,'(id='.$id.')');  
		if (count($data)>0) {
		    $this->db->trans_begin();
			$this->mysql_model->db_del(INVOI,'(id='.$id.')');   
			$this->mysql_model->db_del(INVOI_INFO,'(invoiid='.$id.')');   
			if ($this->db->trans_status() === FALSE) {
			    $this->db->trans_rollback();
				die('{"status":-1,"msg":"删除失败"}');
			} else {
			    $this->db->trans_commit();
				$this->cache_model->delsome(GOODS);
				$this->cache_model->delsome(INVOI);
				$this->cache_model->delsome(INVOI_INFO); 
				$this->data_model->logs('删除其他出库 单据编号：'.$data['billno']);
			    die('{"status":200,"msg":"success"}');	 
			} 
		}
		die('{"status":-1,"msg":"删除失败"}');
	}


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */