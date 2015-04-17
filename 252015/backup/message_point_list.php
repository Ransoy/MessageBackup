<?php 
require_once 'admin.inc';
require_once 'common_proc.inc';
require_once 'common_db.inc';
require_once 'FormObject.inc';
require_once 'tmpl2.class_ex.inc';
require_once 'Owner.inc';			//本フォームは個別の入力チェックが必要
require_once 'CommonDb.php';

$db = new CommonDb();
$tmpl = new Tmpl22($tmpl_dir . "manage/message/message_point_list.html");

$limit = 10;
$offset = 0;

$strWhere = " owner_cd = " . $_SESSION['ownerCd'] . " AND UPD_MODE = '100' ";

if($_POST['male_id'] != ""){

		$strWhere .= " AND user_id LIKE '%" . trim(getPostData($_POST['male_id'])) . "%'";

}

if($_POST['female_id'] != ""){

		$strWhere .= " AND CRE_ID LIKE '%" . trim(getPostData($_POST['female_id'])) . "%'";


}

if($_POST['start_date'] != "" && $_POST['end_date'] != ""){
	
		$strWhere .= " AND (DATE(CRE_DATE) >= '{$_POST['start_date']}' AND DATE(CRE_DATE) <= '{$_POST['end_date']}') ";

}

if($_POST['action'] == 'submit'){

	$page = ($_POST['page']-1 > 0) ? ($_POST['page']-1) :  $_POST['page'];

	$tmpl->assign("dis_data", "1");

	$db->select('count(*) as total');
	$db->from('male_point_log');
	$db->where($strWhere);
	$offset = $_POST['page'] - 1;
	$result = $db->get_row();
	$total = $result['total'];
	$count = 0;
	
	if($total > 0){

		$totalrow = ceil($total/$limit);
		
		if($_POST['page'] < $totalrow){
			$tmpl->assign("next", $_POST['page']+1);
		}

		if($_POST['page'] > 1){
			$tmpl->assign("prev", $page);
		}

		if($totalrow > 5){
			
			if($page >= 2){
				$tmpl->assign("first_page", "1");
			}
			
			$tmpl->loopset('paginate');
			for($page; $page <= $totalrow; $page++){
				if($count < 5){
					if($page == $_POST['page']){
						$tmpl->assign("page","<span>$page<span>");
					}else{
						$tmpl->assign("page","<a href='javascript:;' class='page_num' data-val='$page'>".$page."</a>");
					}
					$count++;
					$tmpl->loopnext();
				}else{
					$tmpl->assign("page","");
					$tmpl->assign("last_page", $totalrow);
					$tmpl->loopnext();
					break;
				}
				
			}
			$tmpl->loopset('');
		}else{
			$tmpl->loopset('paginate');
			for($page = 1; $page <= $totalrow; $page++){
				if($page == $_POST['page']){
					$tmpl->assign("page","<span>$page<span>");
				}else{
					$tmpl->assign("page","<a href='javascript:;' class='page_num' data-val='$page'>".$page."</a>");
				}
				$tmpl->loopnext();
			}
			$tmpl->loopset('');
		}
	}else{
		 
		$tmpl->assign("no-result","結果なし検索しましたが何も見つかりません。 ");
	}


	$db->select('*');
	$db->from('male_point_log');
	$db->limit($offset, $limit);
	$db->where($strWhere);
	$db->order_by('point_log_id', 'DESC');
	$result = $db->get();
	
	$tmpl->loopset('result_search');
	foreach ($result as $row) {

		$tmpl->assign('user_id', $row->USER_ID);
		$tmpl->assign('current_point', $row->POINT);
		$tmpl->assign('prev_point', $row->POINT_OLD);
		//$tmpl->assign('add_point', $row['USER_ID']);
		$tmpl->assign('date_create', $row->CRE_DATE);
		$tmpl->assign('performer_id', $row->CRE_ID);
		$tmpl->assign('id_addrss', $row->CRE_IP);
		$tmpl->loopnext();
	}
	$tmpl->loopset('');
}
$tmpl->flush();

function getPostData($data) {
	return addcslashes(mb_convert_encoding(urldecode($data), "EUC-JP", "UTF-8"), '!...?');
}

?>