<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Basedata extends CI_Controller {

    public function __construct(){
        parent::__construct();
		$this->purview_model->checkpurview();
		$this->load->model('data_model');
    }
	
	//main 统计
	public function main_data() {
	    $list = $this->data_model->inventory('','order by a.id desc');  
	    $qty = 0;  //库存数量
		$sum = 0;  //库存成本
		foreach($list as $arr=>$row) {
			$qty += $row['qty'];
			$sum += $row['puamount'];
		}
		
		$list1 = $this->data_model->customer_arrears('and month(billdate)='.date('m').'',' order by a.id');
		$list2 = $this->data_model->vendor_arrears('and month(billdate)='.date('m').'',' order by a.id');
		$arrears1  = 0;  //客户欠款
		$arrears2  = 0;  //供应商欠款
		foreach($list1 as $arr=>$row){
		    $arrears1   += $row['arrears'];
		}
		foreach($list2 as $arr=>$row){
		    $arrears2   += $row['arrears'];
		}
		
		$cost = 0;     //购货成本总额
		$list3 = $this->data_model->invsa_rate('and month(billdate)='.date('m').'','and month(a.billdate)='.date('m').'');
		foreach($list3 as $arr=>$row) {
			$cost += $row['pu_qty']*$row['price'];   //销售数量*采购单价=成购成本
		}
		
		
		$goodsnum = $this->data_model->goodsnum();   //采购商品种类数量
		$invpu    = $this->cache_model->load_sum(INVPU,'(1=1) and month(billdate)='.date('m').'',array('amount','arrears'));    
	    $invsa    = $this->cache_model->load_sum(INVSA,'(1=1) and month(billdate)='.date('m').'',array('amount','arrears'));   
		$puamount = $invpu ? $invpu['amount'] : 0;
		$saamount = $invsa ? $invsa['amount'] : 0;
		$pusarate = $saamount - $cost;
		
	    $data['status'] = 200;
		$data['msg']    = 'success';
		$data['data']['items']   = array(
										array('mod'=>'inventory','total1'=>str_money($qty),'total2'=>str_money($sum)),
										array('mod'=>'fund','total1'=>0,'total2'=>100),
										array('mod'=>'contact','total1'=>str_money($arrears1),'total2'=>str_money($arrears2)),
										array('mod'=>'sales','total1'=>str_money($saamount),'total2'=>str_money($pusarate)),
										array('mod'=>'purchase','total1'=>str_money($puamount),'total2'=>$goodsnum)
								);
		$data['data']['totalsize']     = 4;                       
		die(json_encode($data));
	}
	

	
	
	//商品接口
	public function goods() {
	    $v = array();
	    $data['status'] = 200;
		$data['msg']    = 'success'; 
		$page = max(intval($this->input->get_post('page',TRUE)),1);
		$rows = max(intval($this->input->get_post('rows',TRUE)),100);
		$skey = str_enhtml($this->input->get('skey',TRUE));
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
		$offset = $rows*($page-1);
		$data['data']['page']      = $page;                                                      //当前页
		$data['data']['records']   = $this->cache_model->load_total(GOODS,'(status=1) '.$where.'');   //总条数
		$data['data']['total']     = ceil($data['data']['records']/$rows);                       //总分页数
		$list = $this->cache_model->load_data(GOODS,'(status=1) '.$where.' order by id desc limit '.$offset.','.$rows.'');   
		foreach ($list as $arr=>$row) {
		    $v[$arr]['amount']        = (float)$row['amount'];
			$v[$arr]['barCode']       = '';
			$v[$arr]['number']        = $row['number'];
			$v[$arr]['categoryName']  = $row['categoryname'];
			$v[$arr]['id']       = intval($row['id']);
			$v[$arr]['name']     = $row['name'];
			$v[$arr]['josl']     = '';
			$v[$arr]['purPrice'] = $row['purprice'];
			$v[$arr]['quantity'] = $row['quantity'];
			$v[$arr]['salePrice']= $row['saleprice'];
			$v[$arr]['spec']     = $row['spec'];
			$v[$arr]['unitCost'] = $row['unitcost'];
			$v[$arr]['unitId']   = $row['unitid'];
			$v[$arr]['unitName'] = $row['unitname'];
			$v[$arr]['localtionName'] = 'a';
			$v[$arr]['locationId'] = 1;
		}
		$data['data']['rows']   = $v;
		die(json_encode($data));
	}
	
	
	//商品名称重复检验接口
	public function goods_checkname() {
	    $name = str_enhtml($this->input->post('name',TRUE));
	    $this->cache_model->load_total(GOODS,'(name="'.$name.'")') > 0 && die('{"status":-1,"msg":"商品名称重复"}'); 
	    die('{"status":200,"msg":"success"}');
	}
	
	//商品编号检验接口
	public function goods_getnextno() {
	    $skey = str_enhtml($this->input->post('skey',TRUE));
	    $this->cache_model->load_total(GOODS,'(number="'.$skey.'")') > 0 && die('{"status":-1,"msg":"商品编号重复"}'); 
		die('{"status":200,"msg":"success","data":{"number":""}}');
	}
	
	//商品ID查询接口
	public function goods_query() {
	    $id = intval($this->input->post('id',TRUE));
	    $data = $this->cache_model->load_one(GOODS,'(id='.$id.')');
		if (count($data)>0) {
			$info['id']          = intval($data['id']);
			$info['count']       = 0;
			$info['name']        = $data['name'];
			$info['spec']        = $data['spec'];
			$info['number']      = $data['number'];
			$info['salePrice']   = $data['saleprice'];
			$info['purPrice']    = $data['purprice'];
			$info['unitTypeId']  = 0;
			$info['baseUnitId']     = intval($data['unitid']);
			$info['assistIds']      = 0;
			$info['assistName']     = 0;
			$info['assistUnit']     = 0;
			$info['remark']         =  $data['remark'];
			$info['categoryName']   =  $data['categoryname'];
			$info['categoryId']     = intval($data['categoryid']);
			$info['unitId']       = intval($data['unitid']);
			$info['quantity']     = (float)$data['quantity'];
			$info['unitCost']     = (float)$data['unitcost'];
			$info['amount']       = (float)$data['amount'];
            $info['aid']          = (int)$data['aid'];
            $info['aid_en']       =  (int)$data['aid_en'];
			die('{"status":200,"msg":"success","data":'.json_encode($info).'}');
		}
	}
	
	//分类接口
	public function category() {
	    $v = '';
	    $data['status'] = 200;
		$data['msg']    = 'success'; 
	    $type   = str_enhtml($this->input->get('typeNumber',TRUE));
		$skey   = str_enhtml($this->input->get('skey',TRUE));
		
		$where = '';
		if ($type) {
			$where .= ' and type="'.$type.'"';
		}
		if ($skey) {
			$where .= ' and name like "%'.$skey.'%"';
		}
		$pid  = $this->cache_model->load_data(CATEGORY,'(status=1) '.$where.' order by id','pid');  
		$list = $this->cache_model->load_data(CATEGORY,'(status=1) '.$where.' order by path');  
		foreach ($list as $arr=>$row) {
		    $v[$arr]['coId']     = 0;
			$v[$arr]['detail']   = in_array($row['id'],$pid) ? false : true;
			$v[$arr]['id']       = intval($row['id']);
			$v[$arr]['level']    = $row['depth'];
			$v[$arr]['name']     = $row['name'];
			$v[$arr]['parentId'] = intval($row['pid']);
			$v[$arr]['remark']   = '';
			$v[$arr]['sortIndex'] = 0;
			$v[$arr]['status'] = 0;
			$v[$arr]['typeNumber'] = $row['type']; 
			$v[$arr]['uuid'] = '';
		}
		$data['data']['items']      = is_array($v) ? $v : '';
		$data['data']['totalsize']  = $this->cache_model->load_total(CATEGORY,'(1=1) '.$where.'');
		die(json_encode($data));
	}
	
	//类别3种接口
	public function category_type() {
	    $list = $this->cache_model->load_data(CATEGORY_TYPE,'(1=1) order by id'); 
		$v = array(); 
		$data['status'] = 200;
		$data['msg']    = 'success'; 
		foreach ($list as $arr=>$row) {
		    $v[$arr]['id']      = intval($row['id']);
			$v[$arr]['name']    = $row['name'];
			$v[$arr]['number']  = $row['number'];
		}
		$data['data']['items']      = is_array($v) ? $v : '';
		$data['data']['totalsize']  = $this->cache_model->load_total(CATEGORY_TYPE);
	    die(json_encode($data));
	}
	
    //单位接口
	public function unit() {
	    $v = '';
	    $data['status'] = 200;
		$data['msg']    = 'success'; 
		$list = $this->cache_model->load_data(UNIT,'(status=1) order by id desc');  
		foreach ($list as $arr=>$row) {
		    $v[$arr]['default'] = false;
			$v[$arr]['guid']    = false;
			$v[$arr]['id']      = intval($row['id']);
			$v[$arr]['name']    = $row['name'];
			$v[$arr]['rate']    = 0;
			$v[$arr]['isdelete']   = 0;
			$v[$arr]['unitTypeId'] = 0;
		}
		$data['data']['items']   = is_array($v) ? $v : '';
		$data['data']['totalsize']  = $this->cache_model->load_total(UNIT);
		die(json_encode($data));
	}
	
	
	//客户、供应商接口
	public function contact() {
	    $v = '';
	    $data['status'] = 200;
		$data['msg']    = 'success'; 
		$type   = intval($this->input->get('type',TRUE));
		$skey   = str_enhtml($this->input->get('skey',TRUE));
		$page = max(intval($this->input->get_post('page',TRUE)),1);
		$rows = max(intval($this->input->get_post('rows',TRUE)),100);
		$where  = '';
		if ($skey) {
			$where .= ' and contact like "%'.$skey.'%"';
		}
		if ($type) {
			$where .= ' and type='.$type.'';
		}
		$offset = $rows * ($page-1);
		$data['data']['page']      = $page;                                                      //当前页
		$data['data']['records']   = $this->cache_model->load_total(CONTACT,'(1=1) '.$where.'');     //总条数
		$data['data']['total']     = ceil($data['data']['records']/$rows);                       //总分页数
		$list = $this->cache_model->load_data(CONTACT,'(status=1) '.$where.' order by id desc limit '.$offset.','.$rows.'');   
		foreach ($list as $arr=>$row) {
		    $v[$arr]['id']           = intval($row['id']);
			$v[$arr]['cCategory']    = intval($row['categoryid']);
			$v[$arr]['number']       = $row['number'];
			$v[$arr]['name']         = $row['name'];
			$v[$arr]['beginDate']    = 1409500800000;
			$v[$arr]['amount']       = (float)$row['amount'];
			$v[$arr]['periodMoney']  = (float)$row['periodmoney'];
			$v[$arr]['remark']       = $row['remark'];
			$v[$arr]['taxRate']      = (float)$row['taxrate'];
			$v[$arr]['customerType'] = $row['categoryname'];
			$v[$arr]['links'] = '';
			if (strlen($row['linkmans'])>0) {                             //获取首个联系人
				$list = (array)json_decode($row['linkmans']);
				foreach ($list as $arr1=>$row1) {
					if ($row1->linkFirst==1) {
						$v[$arr]['contacter']        = $row1->linkName;
						$v[$arr]['mobile']           = $row1->linkMobile; 
						$v[$arr]['telephone']        = $row1->linkPhone; 
						$v[$arr]['linkIm']           = $row1->linkIm; 
						$v[$arr]['firstLink']['first']   = $row1->linkFirst; 
						if ($type==1) {
							$v[$arr]['deliveryAddress']  = $row1->linkAddress; 
						}
					}
				} 
		    }
		}
		$data['data']['rows']   = is_array($v) ? $v : '';
		$data['data']['totalsize']  = $this->cache_model->load_total(CONTACT,'(status=1) '.$where.' order by id desc');  
		die(json_encode($data));
	}
	
	
	//客户、供应商ID查询接口
	public function contact_query() {
	    $id   = intval($this->input->post('id',TRUE));
		$type = intval($this->input->get('type',TRUE));
	    $data = $this->cache_model->load_one(CONTACT,'(type='.$type.') and (id='.$id.')');
		 
		if (count($data)>0) {
			$info['id']          = intval($data['id']);
			$info['cCategory']   = intval($data['categoryid']);
			$info['number']      = $data['number'];
			$info['name']        = $data['name'];
			//$info['beginDate']   = $data['beginDate'];
			$info['amount']      = (float)$data['amount'];
			$info['periodMoney'] = (float)$data['periodmoney'];
			$info['remark']      = $data['remark'];
			$info['taxRate']     = (float)$data['taxrate'];
			$info['links'] = array();	
		    if (strlen($data['linkmans'])>0) {                               //获取首个联系人
				$list = (array)json_decode($data['linkmans']);
				foreach ($list as $arr=>$row) {
					$info['links'][$arr]['name']        = $row->linkName;
					$info['links'][$arr]['mobile']      = $row->linkMobile; 
					$info['links'][$arr]['phone']       = $row->linkPhone; 
					$info['links'][$arr]['im']          = $row->linkIm; 
					$info['links'][$arr]['first']       = $row->linkFirst==1 ? true : false; 
					if ($type==1) {
						$info['links'][$arr]['address'] = $row->linkAddress; 
					}
				}  
		    }
		    unset($data['linkmans']);
			die('{"status":200,"msg":"success","data":'.json_encode($info).'}');
		}
	}
	
    //客户、供应商编号验证接口
	public function contact_getnextno() {
	    $type = intval($this->input->get('type',TRUE));
	    $skey = str_enhtml($this->input->post('skey',TRUE));
		!in_array($type,array(1,2)) && die('{"status":-1,"msg":"参数错误"}'); 
	    $this->cache_model->load_total(CONTACT,'(type='.$type.') and (number="'.$skey.'")') > 0 && die('{"status":-1,"msg":"客户名称重复"}'); 
		die('{"status":200,"msg":"success","data":{"number":""}}');
	}
	
	//客户、供应商名称验证接口
	public function contact_checkname() {
	    $id   = intval($this->input->post('id',TRUE));
		$type = intval($this->input->get('type',TRUE));
	    $name = str_enhtml($this->input->post('name',TRUE));
		!in_array($type,array(1,2)) && die('{"status":-1,"msg":"参数错误"}'); 
		if ($id > 0) {
		    $this->cache_model->load_total(CONTACT,'(type='.$type.') and (id<>'.$id.') and (name="'.$name.'")') > 0 && die('{"status":-1,"msg":"客户名称重复"}'); 
		} else {
		    $this->cache_model->load_total(CONTACT,'(type='.$type.') and (name="'.$name.'")') > 0 && die('{"status":-1,"msg":"客户名称重复"}'); 
		} 
	    die('{"status":200,"msg":"success"}');
	}
	

    //操作日志接口
	public function logs() {
	    $v = array();
	    $data['status'] = 200;
		$data['msg']    = 'success'; 
		$stt  = str_enhtml($this->input->get('fromDate',TRUE));
		$ett  = str_enhtml($this->input->get('toDate',TRUE));
		$page = max(intval($this->input->get_post('page',TRUE)),1);
		$rows = max(intval($this->input->get_post('rows',TRUE)),100);
		$skey   = str_enhtml($this->input->get('skey',TRUE));
		$user   = str_enhtml($this->input->get('user',TRUE));
		$where = '';
		if ($user) {
			$where .= ' and username="'.$user.'"';
		}
		if ($stt) {
			$where .= ' and adddate>="'.$stt.'"';
		}
		if ($ett) {
			$where .= ' and adddate<="'.$ett.'"';
		}
		$offset = $rows*($page-1);
		$data['data']['page']      = $page;                                                      //当前页
		$data['data']['records']   = $this->cache_model->load_total(LOG,'(1=1) '.$where.'');     //总条数
		$data['data']['total']     = ceil($data['data']['records']/$rows);                       //总分页数
		$list = $this->cache_model->load_data(LOG,'(1=1) '.$where.' order by id desc');  
		foreach ($list as $arr=>$row) {
		    $v[$arr]['id']              = intval($row['id']);
			$v[$arr]['name']            = $row['name'];
			$v[$arr]['loginName']       = $row['username'];
			$v[$arr]['operateTypeName'] = $row['name'];
			$v[$arr]['operateType']     = 255;
			$v[$arr]['userId']          = $row['userid'];
			$v[$arr]['log']             = $row['log'];
			$v[$arr]['modifyTime']      = $row['modifytime'];
		}
		$data['data']['rows']   = $v;
		die(json_encode($data));
	}
	
	
	
	//日志用户接口
	public function admin() {
	    $v = array();
	    $data['status'] = 200;
		$data['msg']    = 'success'; 
		$list = $this->cache_model->load_data(ADMIN,'(1=1) order by roleid');  
		foreach ($list as $arr=>$row) {
			$v[$arr]['name']        = $row['username'];
		    $v[$arr]['userid']      = intval($row['uid']);
		}
		$data['data']['items']      = $v;
		$data['data']['totalsize']  = $this->cache_model->load_total(ADMIN);
		die(json_encode($data));
	}
	
	//用户名检测接口
	public function admin_checkname() {
	    $username = str_enhtml($this->input->get('userName',TRUE));
	    $this->cache_model->load_total(ADMIN,'(username="'.$username.'")') > 0 && die('{"status":200,"msg":"success"}');
		die('{"status":502,"msg":"用户名不存在"}');
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */