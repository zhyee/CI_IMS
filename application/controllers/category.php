<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Category extends CI_Controller {

    public function __construct(){
        parent::__construct();
		$this->purview_model->checkpurview(73);
		$this->load->model('data_model');
    }
	
	public function index(){
		$this->load->view('category/index');
	}
	
	//新增
	private function add(){
	    $this->purview_model->checkpurview(74);
	    $data['name'] = str_enhtml($this->input->post('name',TRUE));
		$data['pid']  = intval($this->input->post('parentId',TRUE));
		$data['type'] = str_enhtml($this->input->get_post('typeNumber',TRUE));
		$this->mysql_model->db_count(CATEGORY,'(name="'.$data['name'].'") and type="'.$data['type'].'"') > 0 && die('{"status":-1,"msg":"辅助资料名称重复"}');
		if ($data['pid']==0) {    
			$datas['path'] = $this->mysql_model->db_inst(CATEGORY,$data);
			$sql = $this->mysql_model->db_upd(CATEGORY,$datas,'(id='.$datas['path'].')');
		} else {   	
			$info = $this->mysql_model->db_one(CATEGORY,'(id='.$data['pid'].')');   
			count($info)<1 && die('{"status":-1,"msg":"参数错误"}');  
			$data['depth'] = $info['depth'] + 1;                           
			$lastid = $this->mysql_model->db_inst(CATEGORY,$data);        
			$datas['path'] = $info['path'].','.$lastid;
			$sql = $this->mysql_model->db_upd(CATEGORY,$datas,'(id='.$lastid.')');    
		}
		if ($sql) {
		    $cate = $this->data_model->category_type();
		    $this->data_model->logs('新增'.$cate[$data['type']].':'.$data['name']);
			$this->cache_model->delsome(CATEGORY);
			die('{"status":200,"msg":"success","data":{"id":'.$sql.',"name":"'.$data['name'].'","parentId":'.$data['pid'].'}}');
		} else {
			die('{"status":-1,"msg":"添加失败"}');
		}
	}
	
	//修改
	private function edit() {
	    $this->purview_model->checkpurview(75);
		$id   = intval($this->input->post('id',TRUE));
		$name = str_enhtml($this->input->post('name',TRUE));
		$pid  = intval($this->input->post('parentId',TRUE));
		$type = str_enhtml($this->input->post('typeNumber',TRUE));
		if ($id>0) {
		    strlen($name) < 1 && die('{"status":-1,"msg":"类别不能为空"}');
			$this->mysql_model->db_count(CATEGORY,'(id<>'.$id.') and (name="'.$name.'") and type="'.$type.'"') > 0 && die('{"status":-1,"msg":"辅助资料名称重复"}');
		    $data = $this->mysql_model->db_one(CATEGORY,'(id='.$id.')');                                          //获取原ID数据
			count($data)<1 && die('{"status":-1,"msg":"参数错误"}');  
			$old_pid  = $data['pid'];
			$old_path = $data['path'];
			$pid_list = $this->mysql_model->db_select(CATEGORY,'(id<>'.$id.') and find_in_set('.$id.',path)');    //是否有子栏目
		    $old_pid_num = count($pid_list);    //是否有子栏目
		    //$pid == $old_pid && alert('没有移动'); 
			$pid == $id && die('{"status":-1,"msg":"当前分类和上级分类不能相同"}'); 
			if ($pid==0) {                     //多级转顶级 
			    $pare_depth = 1; 
			    if ($old_pid_num==0) {         //ID不存在子栏目
				    $this->mysql_model->db_upd(CATEGORY,array('pid'=>0,'path'=>$id,'depth'=>1,'name'=>$name),'(id='.$id.')');
				} else {                       //ID存在子栏目
				    $this->mysql_model->db_upd(CATEGORY,array('pid'=>0,'path'=>$id,'depth'=>1,'name'=>$name),'(id='.$id.')');
					foreach($pid_list as $arr=>$row) {
					    $path = str_replace($id,'',$old_path);
					    $path = str_replace(''.$path.'','',''.$row['path'].'');  
						$pare_depth = substr_count($path,',')+1;
						$datas[] =  array('id'=>$row['id'],'path'=>$path,'depth'=>$pare_depth);
					}
				    $this->mysql_model->db_upd(CATEGORY,$datas,'id');
				}
			} else {                       //pid<>0时，顶级转多级  多级转多级
			    $data = $this->mysql_model->db_one(CATEGORY,'(id='.$pid.')');     //获取原PID数据
				count($data)<1 && die('{"status":-1,"msg":"参数错误"}');  
			    $pare_pid   = $data['pid'];
				$pare_path  = $data['path'];
				$pare_depth = $data['depth'];
				if ($old_pid==0) {            //顶级转多级  
					if ($old_pid_num==0) {    //ID不存在子栏目
						$this->mysql_model->db_upd(CATEGORY,array('name'=>$name,'pid'=>$pid,'path'=>$pare_path.','.$id,'depth'=>$pare_depth+1),'(id='.$id.')');
					} else {                  //ID存在子栏目 
						$this->mysql_model->db_upd(CATEGORY,array('name'=>$name,'pid'=>$pid,'path'=>$pare_path.','.$id,'depth'=>$pare_depth+1),'(id='.$id.')');
						foreach ($pid_list as $arr=>$row) {
							$path = $pare_path.','.$row['path'];
							$pare_depth = substr_count($path,',')+1;
							$datas[] = array('id'=>$row['id'],'path'=>$path,'depth'=>$pare_depth);
						}
						$this->mysql_model->db_upd(CATEGORY,$datas,'id');
					}
					    
				} else {                      //多级转多级
					if ($old_pid_num==0) {    //ID不存在子栏目
						$this->mysql_model->db_upd(CATEGORY,array('name'=>$name,'pid'=>$pid,'path'=>$pare_path.','.$id,'depth'=>$pare_depth+1),'(id='.$id.')');
					} else {                  //ID存在子栏目 
						$this->mysql_model->db_upd(CATEGORY,array('name'=>$name,'pid'=>$pid,'path'=>$pare_path.','.$id,'depth'=>$pare_depth+1),'(id='.$id.')');
						foreach ($pid_list as $arr=>$row) {
							$path = str_replace($id,'',$old_path);
					        $path = str_replace($path,'',$row['path']);   
							$path = $pare_path.','.$path;
							$pare_depth = substr_count($path,',')+1;
							$datas[] = array('id'=>$row['id'],'path'=>$path,'depth'=>$pare_depth+1);
						}
						$this->mysql_model->db_upd(CATEGORY,$datas,'id');
					}
				}
			}
			$cate = $this->data_model->category_type();
		    $this->data_model->logs('修改'.$cate[$type].':'.$name);
			$this->cache_model->delsome(CATEGORY);
			$info['id']         = intval($id);
			$info['level']      = intval($pare_depth);
			$info['name']       = $name;
			$info['parentId']   = intval($pid);
			die('{"status":200,"msg":"success","data":'.json_encode($info).'}');
		} else {
			die('{"status":-1,"msg":"参数错误"}'); 
		}  
	}
    
	//分类新增修改
	public function save(){
		$act = str_enhtml($this->input->get('act',TRUE));
		if ($act=='add') {          //新增
		    $this->add();
		} elseif ($act=='update') { //修改
			$this->edit();
		}
	}
	
	//分类删除
	public function del(){
	    $this->purview_model->checkpurview(76);
	    $id = intval($this->input->post('id',TRUE));
		$type = str_enhtml($this->input->post('typeNumber',TRUE));
		$data = $this->mysql_model->db_one(CATEGORY,'(id='.$id.')');   
		if (count($data) > 0) {
			$this->mysql_model->db_count(CATEGORY,'(1=1) and (find_in_set('.$id.',path))')>1 && die('{"status":500,"msg":"操作的对象包含了下级类别，请先删除下级类别"}'); 
			$this->mysql_model->db_count(GOODS,'(categoryid='.$id.')')>0 && die('{"status":500,"msg":"发生业务不可删除"}'); 
			$this->mysql_model->db_count(CONTACT,'(categoryid='.$id.')')>0 && die('{"status":500,"msg":"发生业务不可删除"}'); 
			$sql = $this->mysql_model->db_del(CATEGORY,'(id='.$id.')');   
			if ($sql) {
			    $cate = $this->data_model->category_type();
		        $this->data_model->logs('删除'.$cate[$data['type']].':ID='.$id.' 名称：'.$data['name']);
				$this->cache_model->delsome(CATEGORY);	
				die('{"status":200,"msg":"success"}');
			} else {
			    die('{"status":-1,"msg":"删除失败"}');
			}
		}
	}
	
	
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */