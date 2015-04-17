<?php 
require_once 'CommonDb.php';
require_once 'message/Message.php';
require_once 'tmpl2.class_ex.inc';
require_once 'Owner.inc';
require_once 'admin.inc';
require_once 'common_proc.inc';
require_once 'common_db.inc';

$db = new CommonDb();

if($_POST['action'] == 'chk_btn'){
	
	if($_POST['dataID']){

		$data = array(
				'is_checked' => 1,
				'chk_ip' => $_SERVER['REMOTE_ADDR'],
				'chk_id' => $_SESSION['id'],
				'chk_date' => date('Y-m-d H:i:s')
		);
		
		$db->where('id = "'.$_POST['dataID'].'"');
		$db->update('message',$data);
		
		echo true;
		
	}else{
		echo  false;
	}
	
}

if($_POST['action'] == 'chk_all'){

	if($_POST['dataID']){
		
		$dataArr = $_POST['dataID'];
		
		$data = array(
				'is_checked' => 1,
				'chk_ip' => $_SERVER['REMOTE_ADDR'],
				'chk_id' => $_SESSION['id'],
				'chk_date' => date('Y-m-d H:i:s')
		);

		$i = 0;
		while($i < count($dataArr)){
			
			$db->where('id = "'.$dataArr[$i].'"');
			$db->update('message',$data);
			
			$i++;
			
		}
		
		echo true;

	}else{
		echo  false;
	}
	
}

if($_POST['action'] == 'delete'){

	if($_POST['dataID']){

			
		$db->from('message');
		$db->where('id',$_POST['dataID']);
		$db->delete();

		echo true;

	}else{
		echo  false;
	}

}

if($_POST['action'] == 'del_all'){

	if($_POST['dataID']){

			
		$dataArr = $_POST['dataID'];
		

		$i = 0;
		while($i < count($dataArr)){
			
			$db->from('message');
			$db->where('id',$dataArr[$i]);
			$db->delete();
			
			
			$i++;
			
		}

		echo true;
	}else{
		echo  false;
	}

}


?>