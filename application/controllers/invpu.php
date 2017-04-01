<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Invpu extends CI_Controller {

    public function __construct(){
        parent::__construct();
		$this->purview_model->checkpurview(1);
		$this->load->model('data_model');
		$this->uid  = $this->session->userdata('uid');
		$this->name = $this->session->userdata('name');
    }
	
	public function index(){
		$this->load->view('invpu/index');
	}
	
	public function add(){
	    $this->purview_model->checkpurview(2);
	    $data = $this->input->post('postData',TRUE);
		if (strlen($data)>0) {
		     $data = (array)json_decode($data);
			 (!isset($data['buId']) && $data['buId']<1) && die('{"status":-1,"msg":"请选择购货单位"}');
			 $contact = $this->mysql_model->db_one(CONTACT,'(id='.intval($data['buId']).')');
			 count($contact)<1 && die('{"status":-1,"msg":"请选择购货单位"}');
			 $info['billno']      = str_no('CG');
			 $info['type']        = intval($data['transType']);
			 $info['contactid']   = intval($data['buId']);
			 $info['contactname'] = $contact['number'].' '.$contact['name'];
			 $info['billdate']    = $data['date'];
			 $info['disamount']   = (float)$data['disAmount'];
			 $info['disrate']     = (float)$data['disRate'];
			 $info['description'] = $data['description'];
			 $info['totalamount'] = (float)$data['totalAmount'];
			 $info['totalqty']    = (float)$data['totalQty'];
			 $info['amount']      = $info['type']==1?(float)$data['amount']:-(float)$data['amount'];  //折扣后金额
			 $info['rpamount']    = $info['type']==1?(float)$data['rpAmount']:-(float)$data['rpAmount'];  //已付款
			 $info['arrears']     = $info['type']==1?(float)$data['arrears']:-(float)$data['arrears'];  //欠款
			 $info['totalarrears'] = (float)$data['totalArrears'];
			 $info['uid']         = $this->uid;
			 $info['username']    = $this->name;
			 $this->db->trans_begin();
			 $invpuid = $this->mysql_model->db_inst(INVPU,$info);
			 $v = array();
			 if (is_array($data['entries'])) {
			     foreach ($data['entries'] as $arr=>$row) {
				     $v[$arr]['invpuid']       = $invpuid;
				     $v[$arr]['billno']        = $info['billno'];
				     $v[$arr]['contactid']     = $info['contactid'];
					 $v[$arr]['contactname']   = $info['contactname'];
					 $v[$arr]['type']          = $info['type'];
					 $v[$arr]['goodsid']       = intval($row->invId);
					 $v[$arr]['qty']           = $info['type']==1?$row->qty:-($row->qty); 
					 $v[$arr]['amount']        = $info['type']==1?$row->amount:-($row->amount);
					 $v[$arr]['price']         = (float)$row->price; 
					 $v[$arr]['discountrate']  = (float)$row->discountRate; 
					 $v[$arr]['description']   = $row->description; 
					 $v[$arr]['deduction']     = (float)$row->deduction; 
					 $v[$arr]['description']   = $row->description; 
					 $v[$arr]['goodsno']       = $row->invNumber; 
                     $v[$arr]['billdate']      = $data['date']; 
				} 
			 }
			 $this->mysql_model->db_inst(INVPU_INFO,$v);
			 if ($this->db->trans_status() === FALSE) {
			    $this->db->trans_rollback();
				die();
			 } else {
			    $this->db->trans_commit();
				$this->cache_model->delsome(GOODS);
				$this->cache_model->delsome(INVPU);
				$this->cache_model->delsome(INVPU_INFO); 
				$this->data_model->logs('新增购货单 单据编号：'.$info['billno']);
				die('{"status":200,"msg":"success","data":{"id":'.intval($invpuid).'}}');
			 }
		} else {
		    $data['billno'] = str_no('CG');
		    $this->load->view('invpu/add',$data);
		}
	}
	
	
	//修改
	public function edit(){
	    $this->purview_model->checkpurview(3);
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
			 $info['type']        = intval($data['transType']);
			 $info['contactid']   = intval($data['buId']);
			 $info['billdate']    = $data['date'];
			 $info['contactname'] = $contact['number'].' '.$contact['name'];
			 $info['disamount']   = (float) $data['disAmount'];
			 $info['disrate']     = (float)$data['disRate'];
			 $info['description'] = $data['description'];
			 $info['totalamount'] = (float)$data['totalAmount'];
			 $info['totalqty']    = (float)$data['totalQty'];
			 $info['amount']      = $info['type']==1?(float)$data['amount']:-(float)$data['amount'];  //折扣后金额
			 $info['rpamount']    = $info['type']==1?(float)$data['rpAmount']:-(float)$data['rpAmount'];  //已付款
			 $info['arrears']     = $info['type']==1?(float)$data['arrears']:-(float)$data['arrears'];  //欠款
			 $info['totalarrears'] = (float)$data['totalArrears'];
			 $info['uid']         = $this->uid;
			 $info['username']    = $this->name;
			 $v = array();
			 $this->db->trans_begin();
			 $this->mysql_model->db_count(INVPU,'(id<>'.$id.') and (billno="'.$info['billno'].'")')>0 && die('{"status":-1,"msg":"购货单已存在"}');
			 $this->mysql_model->db_upd(INVPU,$info,'(id='.$id.')');
			 $this->mysql_model->db_del(INVPU_INFO,'(invpuid='.$id.')');
			 if (is_array($data['entries'])) {
			     foreach ($data['entries'] as $arr=>$row) {
				     $v[$arr]['invpuid']       = $id;
				     $v[$arr]['billno']        = $info['billno'];
				     $v[$arr]['contactid']     = $info['contactid'];
					 $v[$arr]['contactname']   = $info['contactname'];
					 $v[$arr]['type']          = $info['type'];
					 $v[$arr]['goodsid']       = $row->invId;
					 $v[$arr]['qty']           = $info['type']==1?$row->qty:-($row->qty); 
					 $v[$arr]['amount']        = $info['type']==1?$row->amount:-($row->amount);
					 $v[$arr]['price']         = (float)$row->price; 
					 $v[$arr]['discountrate']  = (float)$row->discountRate; 
					 $v[$arr]['description']   = $row->description; 
					 $v[$arr]['deduction']     = (float)$row->deduction; 
					 $v[$arr]['description']   = $row->description; 
					 
					 $v[$arr]['goodsno']       = $row->invNumber; 
                     $v[$arr]['billdate']      = $data['date']; 
				} 
			 }
			 $this->mysql_model->db_inst(INVPU_INFO,$v);
			 if ($this->db->trans_status() === FALSE) {
			    $this->db->trans_rollback();
				die('');
			 } else {
			    $this->db->trans_commit();
				$this->cache_model->delsome(GOODS);
				$this->cache_model->delsome(INVPU);
				$this->cache_model->delsome(INVPU_INFO); 
				$this->data_model->logs('修改购货单 单据编号：'.$info['billno']);
				die('{"status":200,"msg":"success","data":{"id":'.$id.'}}');
			 }
		} else {
		    $data = $this->mysql_model->db_one(INVPU,'(id='.$id.')');
			if (count($data)>0) {
				$this->load->view('invpu/edit',$data);
			} else {
			    $data['billno'] = str_no('CG');
			    $this->load->view('invpu/add',$data);
			}
		}
	}
	
	//修改单据数据回显
	public function info(){
	    $id   = intval($this->input->get_post('id',TRUE));
		$data = $this->mysql_model->db_one(INVPU,'(id='.$id.')');
		if (count($data)>0) {
			$v = '';
			$info['status'] = 200;
			$info['msg']    = 'success'; 
			$info['data']['id']                 = intval($data['id']);
			$info['data']['buId']               = intval($data['contactid']);
			$info['data']['contactName']        = $data['contactname'];
			$info['data']['date']               = $data['billdate'];
			$info['data']['billNo']             = $data['billno'];
			$info['data']['billType']           = 'PUR';
			$info['data']['transType']          = intval($data['type']);
			$info['data']['totalQty']           = (float)$data['totalqty'];
			$info['data']['totalAmount']        = (float)abs($data['totalamount']);
			$info['data']['disRate']            = (float)$data['disrate'];
			$info['data']['disAmount']          = (float)$data['disamount'];
			$info['data']['amount']             = (float)abs($data['amount']);
			$info['data']['rpAmount']           = (float)abs($data['rpamount']);
			$info['data']['arrears']            = (float)abs($data['arrears']);
			$info['data']['userName']           = $data['username'];
			$info['data']['status']             = 'edit';
			$info['data']['description']        = $data['description']; 
			$info['data']['totalDiscount']      = (float)$data['totalarrears'];
			$list = $this->data_model->invpu_info(' and (a.invpuid='.$id.')','order by id desc');  
			foreach ($list as $arr=>$row) {
				$v[$arr]['invSpec']           = $row['spec'];
				$v[$arr]['taxRate']           = intval($row['id']);
				$v[$arr]['srcOrderEntryId']   = 0;
				$v[$arr]['srcOrderNo']        = '';
				$v[$arr]['locationId']        = 0;
				$v[$arr]['goods']             = $row['goodsno'].' '.$row['goodsname'].' '.$row['spec'];
				$v[$arr]['invName']      = $row['goodsname'];
				$v[$arr]['qty']          = (float)abs($row['qty']);
				$v[$arr]['locationName'] = '';
				$v[$arr]['amount']       = (float)abs($row['amount']);
				$v[$arr]['taxAmount']    = 0;
				$v[$arr]['price']        = (float)$row['price'];
				$v[$arr]['tax']          = 0;
				$v[$arr]['mainUnit']     = $row['unitname'];
				$v[$arr]['deduction']    = (float)$row['deduction'];
				$v[$arr]['invId']        = intval($row['goodsid']);
				$v[$arr]['invNumber']    = $row['number'];
				$v[$arr]['discountRate'] = 0;
				$v[$arr]['description']   = $row['description']; 
				$v[$arr]['unitId']       = intval($row['unitid']);
				$v[$arr]['srcOrderId']   = '';
			}
			$info['data']['entries']     = is_array($v) ? $v : '';
			$info['data']['accId']       = 0;
			$info['data']['accounts']    = array();
			die(json_encode($info));
		} else { 
			alert('参数错误');
		}
	}
	
	
	//采购单列表
	public function lists() {
	    $v = '';
	    $data['status'] = 200;
		$data['msg']    = 'success'; 
		$page = max(intval($this->input->get_post('page',TRUE)),1);
		$rows = max(intval($this->input->get_post('rows',TRUE)),100);
		$key  = str_enhtml($this->input->get_post('matchCon',TRUE));
		$stt  = str_enhtml($this->input->get_post('beginDate',TRUE));
		$ett  = str_enhtml($this->input->get_post('endDate',TRUE));
		$where = '';
		if (strlen($key)>0) {
		    $where .= ' and (billno like "%'.$key.'%" or contactname like "%'.$key.'%" or description like "%'.$key.'%")'; 
		}
		if (strlen($stt)>0) {
		    $where .= ' and billdate>="'.$stt.'"'; 
		}
		if (strlen($ett)>0) {
		    $where .= ' and billdate<="'.$ett.'"'; 
		}
		
		$offset = $rows * ($page-1);
		$data['data']['page']      = $page;
		$data['data']['records']   = $this->cache_model->load_total(INVPU,'(1=1) '.$where);   //总条数
		$data['data']['total']     = ceil($data['data']['records']/$rows);    //总分页数
		$list = $this->cache_model->load_data(INVPU,'(1=1) '.$where.' order by id desc limit '.$offset.','.$rows.'');  
		foreach ($list as $arr=>$row) {
		    $v[$arr]['amount']       = (float)abs($row['amount']);
			$v[$arr]['id']           = intval($row['id']);
			$v[$arr]['transType']    = intval($row['type']);;
			$v[$arr]['rpAmount']     = (float)$row['rpamount'];
			$v[$arr]['contactName']  = $row['contactname'];
			$v[$arr]['description']  = $row['description'];
			$v[$arr]['billNo']       = $row['billno'];
			$v[$arr]['billDate']     = $row['billdate'];
			$v[$arr]['totalAmount']  = (float)abs($row['totalamount']);
			$v[$arr]['userName']     = $row['username'];
			$v[$arr]['transTypeName']= $row['type']==1 ? '购货' : '退货';
		}
		$data['data']['rows']      = is_array($v) ? $v : '';
		die(json_encode($data));
	}
	
	//导出购货记录
	public function export() {
	    $this->purview_model->checkpurview(5);
		sys_xls('购货记录.xls');
		$id  = str_enhtml($this->input->get_post('id',TRUE));
		if (strlen($id)>0) {
			$data['list1'] = $this->cache_model->load_data(INVPU,'(id in('.$id.'))');  
			$data['list2'] = $this->data_model->invpu_info(' and (a.invpuid in('.$id.'))');  
			$this->data_model->logs('导出购货记录');
			$this->load->view('invpu/export',$data);
		}	
	}	
	
	//购购单删除
    public function del() {
	    $this->purview_model->checkpurview(4);
	    $id   = intval($this->input->get('id',TRUE));
		$data = $this->mysql_model->db_one(INVPU,'(id='.$id.')');  
		if (count($data)>0) {
		    $this->db->trans_begin();
			$this->mysql_model->db_del(INVPU,'(id='.$id.')');   
			$this->mysql_model->db_del(INVPU_INFO,'(invpuid='.$id.')');   
			if ($this->db->trans_status() === FALSE) {
			    $this->db->trans_rollback();
				die('{"status":-1,"msg":"删除失败"}');
			} else {
			    $this->db->trans_commit();
				$this->cache_model->delsome(GOODS);
				$this->cache_model->delsome(INVPU);
				$this->cache_model->delsome(INVPU_INFO); 
				$this->data_model->logs('删除购货单 单据编号：'.$data['billno']);
			    die('{"status":200,"msg":"success"}');	 
			}
		}
		die('{"status":-1,"msg":"删除失败"}');
	}


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */