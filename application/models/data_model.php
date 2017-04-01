<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Data_model extends CI_Model{

	public function __construct(){
  		parent::__construct();
	}
	
	
	//商品采购明细表
	//作用于 invpu.php下
	public function invpu_info($where='',$order='') {
	    $where = $where ? 'where (1=1) '.$where : '';
	    $sql = 'select a.* , 
					b.number as number, b.spec as spec, b.name as goodsname,b.unitname as unitname,b.unitid as unitid
		        from '.INVPU_INFO.' as a 
				left join '.GOODS.' as b
					on a.goodsid=b.id 
				'.$where.' 
				'.$order.'
				';
		return $this->cache_model->load_sql(INVPU_INFO,$sql,2);		
	}	
	
	
	//商品销售明细表
	//作用于 invsa.php下
	public function invsa_info($where='',$order='') {
	    $where = $where ? 'where (1=1) '.$where : '';
	    $sql = 'select a.* , 
					b.number as number, b.spec as spec, b.name as goodsname,b.unitname as unitname,b.unitid as unitid
		        from '.INVSA_INFO.' as a 
				left join '.GOODS.' as b
					on a.goodsid=b.id 
				'.$where.' 
				'.$order.'
				';
		return $this->cache_model->load_sql(INVSA_INFO,$sql,2);		
	}	
	
	
	//其他入库明细
	//作用于 invoi.php下
	public function invoi_info($where='',$order='') {
	    $where = $where ? 'where (1=1) '.$where : '';
	    $sql = 'select a.* , 
					b.number as number, b.spec as spec, b.name as goodsname,b.unitname as unitname,b.unitid as unitid
		        from '.INVOI_INFO.' as a 
				left join '.GOODS.' as b
					on a.goodsid=b.id 
				'.$where.' 
				'.$order.'
				';
		return $this->cache_model->load_sql(INVOI_INFO,$sql,2);		
	}	
	
    //商品采购明细表
	//作用于报表下
	public function invpu_detail($where='',$order='') {
	    $where = $where ? 'where (1=1) '.$where : '';
	    $sql = 'select a.* , 
					b.number as number, b.spec as spec, b.name as goodsname,b.unitname as unitname
		        from '.INVPU_INFO.' as a 
				left join '.GOODS.' as b
					on a.goodsid=b.id 
				'.$where.' 
				'.$order.'
				';
		return $this->cache_model->load_sql(INVPU_INFO,$sql,2);		
	}	
	
	//采购汇总表（按商品）
	public function invpu_summary($where='',$order='') {
	    $where = $where ? 'where (1=1) '.$where : '';
	    $sql = 'select 
					sum(a.qty) as qty ,
					sum(a.amount) as amount,
					a.goodsno as goodsno,
		            if(ifnull(a.qty,0)=0,"0",ifnull(a.amount,0)/ifnull(a.qty,0)) as price,
					b.number as number, b.spec as spec, b.name as goodsname,b.unitname as unitname
		        from '.INVPU_INFO.' as a 
				left join '.GOODS.' as b
					on a.goodsid=b.id 
				'.$where.' 
				'.$order.'
				';
		return $this->cache_model->load_sql(INVPU_INFO,$sql,2);		
	}	
	
	//采购汇总表（按供应商）
	public function invpu_supply($where='',$order='') {
	    $where = $where ? 'where (1=1) '.$where : '';
	    $sql = 'select a.*,
					b.number as number, b.spec as spec, b.name as goodsname,b.unitname as unitname
		        from '.INVPU_INFO.' as a 
				left join '.GOODS.' as b
					on a.goodsid=b.id 
				'.$where.' 
				'.$order.'
				';
		return $this->cache_model->load_sql(INVPU_INFO,$sql,2);		
	}	
	
	
	//商品销售明细表
	public function invsa_list($where='',$order='') {
	    $where = $where ? 'where (1=1) '.$where : '';
	    $sql = 'select a.* , 
					b.number as number, b.spec as spec, b.name as goodsname,b.unitname as unitname,b.unitid as unitid
		        from '.INVSA_INFO.' as a 
				left join '.GOODS.' as b
					on a.goodsid=b.id 
				'.$where.' 
				'.$order.'
				';
		return $this->cache_model->load_sql(INVSA_INFO,$sql,2);		
	}	
	
	
	//商品销售明细表（按商品）
	public function invsa_summary($where='',$order='') {
	    $where = $where ? 'where (1=1) '.$where : '';
	    $sql = 'select 
		            sum(a.qty) as qty ,
					sum(a.amount) as amount,
					a.goodsno as goodsno,
		            if(ifnull(a.qty,0)=0,"0",ifnull(a.amount,0)/ifnull(a.qty,0)) as price,
					b.number as number, b.spec as spec, 
					b.name as goodsname,b.unitname as unitname,
					b.unitid as unitid
		        from '.INVSA_INFO.' as a 
				left join '.GOODS.' as b
					on a.goodsid=b.id 
				'.$where.' 
				'.$order.'
				';
		return $this->cache_model->load_sql(INVSA_INFO,$sql,2);		
	}	
	
	//商品销售明细表（按客户）
	public function invsa_customer($where='',$order='') {
	    $where = $where ? 'where (1=1) '.$where : '';
	    $sql = 'select a.*,
					b.number as number, b.spec as spec, b.name as goodsname,b.unitname as unitname,b.unitid as unitid
		        from '.INVSA_INFO.' as a 
				left join '.GOODS.' as b
					on a.goodsid=b.id 
				'.$where.' 
				'.$order.'
				';
		return $this->cache_model->load_sql(INVSA_INFO,$sql,2);		
	}	
	
	//商品销售明毛利 (获取商品成本单价)
	public function invsa_rate($where1='',$where2='',$order='') {
	    $where1 = $where1 ? 'where (1=1) '.$where1 : '';
		$where2 = $where2 ? 'where (1=1) '.$where2 : '';
	    $sql = 'select 
		            a.*,
					sum(a.amount) as sa_amount,
					sum(a.qty) as pu_qty,
					if(ifnull(b.pu_qty,0)=0,"0",ifnull(b.pu_amount,0)/ifnull(b.pu_qty,0))  as price
		        from '.INVSA_INFO.' as a 
				left join 
					(select goodsid, sum(amount) as pu_amount ,sum(qty) as pu_qty 
					from '.INVPU_INFO.' 
					'.$where1.' 
					group by goodsid) as b 
				on a.goodsid=b.goodsid  
				'.$where2.' 
				'.$order.'
				';
		return $this->cache_model->load_sql(INVSA_INFO,$sql,2);		
	}	
	
	
	
	//往来单位欠款表
	public function vendor_arrears($where='',$order='') {
	    $where = $where ? 'where (1=1) '.$where : '';
		$sql = 'select a.*, 
		            (ifnull(b.arrear,0) + ifnull(a.amount,0) - ifnull(a.periodmoney,0)) as arrears
		        from '.CONTACT.' as a 
				left join 
				(select contactid, sum(arrears) as arrear from '.INVPU.' '.$where.' group by contactid) as b 
				on a.id=b.contactid
				where a.type=2
				'.$order.'
				';
		return $this->cache_model->load_sql(INVPU,$sql,2);		
	}	
	
	
	//往来单位欠款表
	public function customer_arrears($where='',$order='') {
	    $where = $where ? 'where (1=1) '.$where : '';
		$sql = 'select a.*, 
		            (ifnull(b.arrear,0) + ifnull(a.amount,0) - ifnull(a.periodmoney,0)) as arrears
		        from '.CONTACT.' as a 
				left join 
				(select contactid, sum(arrears) as arrear from '.INVSA.' '.$where.' group by contactid) as b 
				on a.id=b.contactid
				where a.type=1
				'.$order.'
				';
		return $this->cache_model->load_sql(INVSA,$sql,2);		
	}	
	
	
	
	//采购商品总类统计
	public function goodsnum() {
		$sql = 'SELECT COUNT(id) AS goodsnum
				FROM '.INVPU_INFO.'
				WHERE (1=1) and month(billdate)='.date('m').' group by goodsid';
		return $this->cache_model->load_sql(INVPU_INFO,$sql,3);		
	}	
	

	
	//盘点库存 
	public function inventory($where='',$order='') {
	    $where = $where ? 'where (1=1) '.$where : '';
	    $sql = 'select a.*, 
					
					ifnull(a.quantity,0) * a.unitcost + (if(ifnull(b.puqty,0)=0,"0",ifnull(b.amount,0)/ifnull(b.puqty,0)) * (ifnull(b.puqty,0) - ifnull(c.saqty,0) + ifnull(d.oiqty,0))) as puamount,
					
		            (ifnull(a.quantity,0) + ifnull(b.puqty,0) - ifnull(c.saqty,0) + ifnull(d.oiqty,0)) as qty
		        from '.GOODS.' as a 
				left join 
				(select goodsid, sum(qty) as puqty , sum(amount) as amount from '.INVPU_INFO.' group by goodsid) as b
				on a.id=b.goodsid
				left join 
				(select goodsid, sum(qty) as saqty from '.INVSA_INFO.' group by goodsid) as c 
				on a.id=c.goodsid
				left join 
				(select goodsid, sum(qty) as oiqty from '.INVOI_INFO.' group by goodsid) as d
				on a.id=d.goodsid
				'.$where.' 
				'.$order.'
				';
		return $this->cache_model->load_sql(GOODS,$sql,2);		
	}	
	
	//商品收发汇总表
	public function goods_summary($where1='',$where2='',$order='') {
	    $where2 = $where2 ? 'where (1=1) '.$where2 : '';
	    $sql = 'select a.*,
		        ifnull(b1.puqty1,0) as  puqty1,
				ifnull(b2.puqty2,0) as  puqty2,
				ifnull(c1.saqty1,0) as  saqty1,
				ifnull(c2.saqty2,0) as  saqty2,
				ifnull(d1.oiqty1,0) as  oiqty1,
				ifnull(d2.oiqty2,0) as  oiqty2,
				ifnull(d3.oiqty3,0) as  oiqty3,
				ifnull(d4.oiqty4,0) as  oiqty4,
				
				ifnull(b1.puamount1,0) as  puamount1,
				
				if(ifnull(b1.puqty1,0)=0,"0",ifnull(b1.puamount1,0)/ifnull(b1.puqty1,0))  as price,
	
				(ifnull(b1.puqty1,0) + ifnull(d1.oiqty1,0) + ifnull(d2.oiqty2,0)) as puqty,
				(ifnull(d3.oiqty3,0) + ifnull(d4.oiqty4,0) + ifnull(b2.puqty2,0) - ifnull(c1.saqty1,0)) as saqty,
				(ifnull(a.quantity,0)+ifnull(b1.puqty1,0)+ifnull(b2.puqty2,0)-ifnull(c1.saqty1,0)-ifnull(c2.saqty2,0)+ifnull(d1.oiqty1,0)+ifnull(d2.oiqty2,0)+ifnull(d3.oiqty3,0)+ifnull(d4.oiqty4,0)) as qty  
		        from '.GOODS.' as a 
				left join 
				(select goodsid, sum(qty) as puqty1, sum(amount) as puamount1 from '.INVPU_INFO.' where type=1 '.$where1.' group by goodsid) as b1 
				on a.id=b1.goodsid
				left join 
				(select goodsid, sum(qty) as puqty2 from '.INVPU_INFO.' where type=2 '.$where1.' group by goodsid) as b2 
				on a.id=b1.goodsid
				left join 
				(select goodsid, sum(qty) as saqty1 from '.INVSA_INFO.' where type=1 '.$where1.' group by goodsid) as c1
				on a.id=c1.goodsid
				left join 
				(select goodsid, sum(qty) as saqty2 from '.INVSA_INFO.' where type=2 '.$where1.' group by goodsid) as c2 
				on a.id=c2.goodsid
				left join 
				(select goodsid, sum(qty) as oiqty1 from '.INVOI_INFO.' where type=1 '.$where1.' group by goodsid) as d1
				on a.id=d1.goodsid
				left join 
				(select goodsid, sum(qty) as oiqty2 from '.INVOI_INFO.' where type=2 '.$where1.' group by goodsid) as d2
				on a.id=d2.goodsid
				left join 
				(select goodsid, sum(qty) as oiqty3 from '.INVOI_INFO.' where type=3 '.$where1.' group by goodsid) as d3
				on a.id=d3.goodsid
				left join 
				(select goodsid, sum(qty) as oiqty4 from '.INVOI_INFO.' where type=4 '.$where1.' group by goodsid) as d4
				on a.id=d4.goodsid
				'.$where2.' 
				'.$order.'
				';
		return $this->cache_model->load_sql(GOODS,$sql,2);		
	}	
	
	//分类类别
	public function category_type() {
	    $data = array();
	    $list = $this->cache_model->load_data(CATEGORY_TYPE,'(1=1)');	
	    foreach ($list as $arr=>$row) {
		    $data[$row['number']] = $row['name'];
		}
		return $data;		
	}	
	
	
	//写入日志
	public function logs($info) {
	    $time     = date('Y-m-d H:i:s');
		$date     = date('Y-m-d');
		$userid   = $this->session->userdata('uid');
		$name     = $this->session->userdata('name');
		$username = $this->session->userdata('username');
		$data = '';
	    if (is_array($info)) {
		    foreach($info as $row) {
			    $data[] = array(
					'userid'    =>$userid,
					'name'      =>$name,
					'log'       =>$row,
					'username'  =>$username,
					'modifytime'=>$time,
					'adddate'   =>$date
				);
			}
		} else {
			$data['userid']     =  $userid;
			$data['name']       =  $name;
			$data['log']        =  $info;
			$data['username']   =  $username;
			$data['adddate']    =  $date;
			$data['modifytime'] =  $time;
		}
		if (is_array($data)) {
			$this->mysql_model->db_inst(LOG,$data);	
			$this->cache_model->delsome(LOG);	
		}
	}	
	
	
	
}
?>