<?php
/*
 * @Title：お客様メモ
 * @Description：とりあずソースはそのまま流用　テンプレのパス変更等
 * @Author：Satodate
 */
require_once 'common_proc.inc';
require_once 'common_db_slave127.inc';
require_once 'Owner.inc';
require_once 'FormObject.inc';

// 新しい画面用のファイル読み込み　@satodate
require_once 'operator/tmpl2.class_operator.inc';
require_once 'operator/operator.inc';

///////////////////////////////////////////////////////////////////////////////////////////////////

	$sth    = $dbSlave33->prepare("SELECT world_flg,agent_code FROM female_member WHERE owner_cd = ? AND user_id = ?");
	$data   = array($ownerCd,$_SESSION['user_id']);
	$result = $dbSlave33->execute($sth, $data);
	
	if(DB::isError($result)){
		print_r($result->getMessage());
	}
	
	$row           = $result->fetchRow();
	$world_flg     = $row[0];
	$agent_code    = $row[1];
	$template_name = "";
	$prev_link     = "前の10件";
	$next_link     = "次の10件";
	
	if($world_flg == "1"){
		$template_name = "_world";
		$prev_link     = "Prev";
		$next_link     = "Next";
	}

	$tmpl = new Tmpl23(OP_PATH.'template/memo/call_wait'.$template_name.'.html');

	//１ページあたりの表示件数
	$page_num = 10;
	
	if(!isset($_POST['pos'])){
		$pos = 0;
	}else{
		if(!$_POST['pos']){
			$pos = 0;
		}else{
			$pos = $_POST['pos'];
		}
	}


	//拒否者のハッシュを取得する
	$sql = "SELECT mm.user_id AS mid,
                   mm.hash    AS hash,
                   fab.address_type,
                   dm.to_hash
              FROM female_address_book fab
         LEFT JOIN male_member         mm
                ON mm.user_id  = fab.male_user_id
         LEFT JOIN deny_member         dm
                ON dm.upd_id   = fab.user_id
               AND dm.to_hash  = fab.male_hash
             WHERE fab.user_id = ?
               AND (fab.address_type = 2 OR dm.to_hash IS NOT NULL)";

	$arr = array($_SESSION['user_id']);
	$sth = $dbSlave33->prepare($sql);
	$res = $dbSlave33->execute($sth,$arr);

	$rej   = array();
	$notin = "('";
	
	while($gyo = $res->fetchRow()){
		$rej[$gyo[0]] = $gyo[1];
		$notin .= "{$gyo[1]}',";
	}
	
	$notin = substr($notin,0,-1);
	$notin .= ")";

	$forCall = "";
	if(count($rej)){
		$ni  = "mm.hash NOT IN {$notin}";
		$ni2 = " AND {$ni}";
		//$sql .= $ni;
		$forCall = "WHERE {$ni}";
	}

	
	$sql = "SELECT COUNT(*)
	          FROM (SELECT user_id,
	                       MAX(cre_date)
	                  FROM `call`
	                 WHERE female_user_id = ?
	                   AND cre_date >= now() - interval 3 day
	              GROUP BY user_id,disp_nick_name_flg) `call`
	    INNER JOIN male_member mm
	            ON mm.user_id = call.user_id
	           AND mm.stat    = 1
	           {$ni2}";
	
	$sth    = $dbSlave33->prepare($sql);
	$data   = array($_SESSION['user_id']);
	$result = $dbSlave33->execute($sth, $data);

	if(DB::isError($result)){
		err_proc($result->getMessage());
	}
	
	$num = 0;
	if($row = $result->fetchRow()){
		$num = $row[0];
		$tmpl->assign("non_zero","1");
	}
	
	$nbsp = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	
	if($pos < $page_num){
		$tmpl->assign("prev_link",$nbsp);
	}else{
		$tmp = $pos - $page_num;
		$tmpl->assign("prev_link","<a href=\"javascript:goto_page($tmp)\">{$prev_link}</a>");
	}
	
	if($pos + $page_num >= $num){
		$tmpl->assign("next_link",$nbsp);
	}else{
		$tmp = $pos + $page_num;
		$tmpl->assign("next_link","<a href=\"javascript:goto_page($tmp)\">{$next_link}</a>");
	}

	//最新チャットのシステムコールデータを取得
	$sql = "SELECT mm.user_id,
	               mm.nick_name,
	               mm.hash,
	               call.cre_date,
	               mab.address_type,
	               call.disp_nick_name_flg
	          FROM `call` INNER JOIN (SELECT user_id,
	                                       MAX(cre_date) AS cre_date,
	                                       female_user_id
	                                  FROM `call`
	                                 WHERE female_user_id = ?
	                                   AND cre_date >= now() - interval 3 day
	                              GROUP BY user_id,disp_nick_name_flg) sub
	                             ON sub.user_id        = call.user_id
	                            AND sub.cre_date       = call.cre_date
	                            AND sub.female_user_id = call.female_user_id
	                     INNER JOIN male_member mm
	                             ON mm.user_id         = call.user_id
	                            AND mm.stat            = 1
	                      LEFT JOIN male_address_book mab
	                             ON mab.address_type   = 1
	                            AND mab.female_user_id = ?
	                            AND mm.user_id         = mab.user_id
	         {$forCall}
	      ORDER BY call.cre_date DESC
	         LIMIT {$page_num} OFFSET {$pos}";
//echo "SQL : {$sql}<br /><br />";
	$sth    = $dbSlave33->prepare($sql);
	$data   = array($_SESSION['user_id'],$_SESSION['user_id']);
	$result = $dbSlave33->execute($sth, $data);
//echo "DATA : ";
//print_r($data);
//echo "<br />";
	if(DB::isError($result)){
		err_proc($result->getMessage());
	}
	
	$tmpl->loopset("list_loop");
	while ($row = $result -> fetchRow()){

		$tagStr = "<a href=\"javascript:MM_openBrWindow('../mail/res.php?sid={$row[2]}','mail',";
		$tagStr .= " 'scrollbars=yes, toolbar=yes');\">{$row[1]}</a>";
		
		$tmpl->assign("nick_name",$tagStr);
		$tmpl->assign("hash","$row[2]");
		
		if(!$row[3]){
			$tmpl->assign("last_connect","未チャット");
		}else{
			$tmpl->assign("last_connect","{$row[3]}");
		}
		
		if($row[4] == 1){
			$tmpl->assign("favorite","☆");
		}else{
			$tmpl->assign("favorite","&nbsp;");
		}
		
		if($row[5] == 1){
			$tmpl->assign("disp_nick_name_flg1","1");
		}else{
			$tmpl->assign("disp_nick_name_flg0","1");
		}
		
		$data = array();
		$data['female_show_flg'] = 1;
		
		$tagStr = " owner_cd = {$ownerCd} AND female_user_id = ".$dbSlave33->quote($_SESSION['user_id']);
		$tagStr .= " AND user_id = ".$dbSlave33->quote($row[0])." AND female_show_flg = 0";
		
		iTSupdate($data,"`call`",$_SESSION['user_id'],$tagStr);
		
		$tmpl->loopnext();
	}
	
	$tmpl->loopset("");

	if(!$agent_code){
		$tmpl->assign("disp_when_not_dairiten",1);
	}

	$tmpl->flush();

?>