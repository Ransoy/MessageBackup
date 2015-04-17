<?
require_once 'webMailService.inc';
require_once 'common_proc.inc';
require_once 'common_db_slave127.inc';
require_once 'tmpl2.class_ex.inc';
require_once 'Owner.inc';
require_once 'girl.inc';
require_once 'mailBOXMaintenance.inc';
mailBOXMaintenanceFemale(1);

////////////////////////////////////////////////////////////////////////////////////////////////////

	$sql = "SELECT fm.world_flg,
	               fm.agent_code
	          FROM female_member fm
	         WHERE fm.owner_cd = ?
	           AND fm.user_id = ?";
	$con    = array($ownerCd,$_SESSION['user_id']);
	$sth    = $dbSlave33->prepare($sql);
	$result = $dbSlave33->execute($sth,$con);
	
	if(DB::isError($result)){
		err_proc($result->getMessage());
	}
	
	$row = $result->fetchRow();
	$world_flg = $row[0];
	
if($row[1] != ""){
$agent_code_con = 'style="display:none;"';
}else{
$agent_code_con = '';
}
	$template_name = "";
	$address_btn   = "追加";
	$setting_btn   = "設定";
	$prev_link     = "前の50件";
	$next_link     = "次の50件";
	$template_name = "";
	
	if($world_flg == 1){
		$template_name = "_world";
		$address_btn   = "Addition";
		$setting_btn   = "Set";
		$prev_link     = "Prev";
		$next_link     = "Next";
	}

	$tmpl = new Tmpl22($tmpl_dir . "operator/mail_new/mailbox_receive{$template_name}.html");
	
	//------------------
	//削除時処理
	//------------------
	if(isset($_POST['mode'])){
		if($_POST['mode'] == "del"){
			if(isset($_POST['mail_id'])){
				$sql = "UPDATE female_mailbox
				           SET stat2    = 5,
				               upd_date = now(),
				               upd_id   = '{$_SESSION['user_id']}',
				               upd_ip   = '{$_SERVER['REMOTE_ADDR']}'
				         WHERE owner_cd = '{$ownerCd}'
				           AND mail_id  = ?";

				$sth = $dbMaster->prepare($sql);
				
				//複数削除時
				if(is_array($_POST['mail_id'])){
					foreach($_POST['mail_id'] as $key => $value){
						$result = $dbMaster->execute($sth, $value);
					}
				//1件削除時
				}else{
						$result = $dbMaster->execute($sth, $_POST['mail_id']);
				}
			}
			if(isset($_POST['mailx_id'])){
				if(is_array($_POST['mailx_id'])){
					//複数削除時
					foreach($_POST['mailx_id'] as $key => $value){
						$sql = "SELECT 1
						          FROM female_mailbox_mailmag
						         WHERE owner_cd = ?
						           AND user_id  = ?
						           AND mail_id  = ?
						         LIMIT 1;";
						         
						$data   = array($ownerCd, $_SESSION['user_id'], $value);
						$result = $dbSlave->query($sql,$data);
						
						if($result->numRows() == 0){
						    $tmpl = new Tmpl22($tmpl_dir . "operator/mail_new/receive_end{$template_name}.html");
							$msg = "削除したいメールが存在しませんでした";
							$tmpl->assign("msg" , $msg );
							$tmpl->assign("agent_code", $agent_code_con);
							setDairitenParam($tmpl);
							$tmpl->flush();
							exit;
						}
						
						$sql = "UPDATE female_mailbox_mailmag
						           SET stat2    = 5,
						               upd_date = now(),
						               upd_id   = ?,
						               upd_ip   = ?
						         WHERE owner_cd = ?
						           AND mail_id  = ?
						           AND user_id  = ?
						          LIMIT 1";
						          
						$data = array(
							$_SESSION['user_id'],
							$_SERVER['REMOTE_ADDR'],
							$ownerCd,
							$value,
							$_SESSION['user_id']
						);
						
						$res = $dbMaster->query($sql,$data);
						
						if(DB::isError($res)){
						    $tmpl = new Tmpl22($tmpl_dir . "operator/mail_new/receive_end{$template_name}.html");
							$msg = "削除したいメールが存在しませんでした";
							$tmpl->assign("msg" , $msg );
							$tmpl->assign("agent_code", $agent_code_con);
							setDairitenParam($tmpl);
							$tmpl->flush();
							exit;
						}
					}
				}else{
					//1件削除時
					$sql = "SELECT 1
					          FROM female_mailbox_mailmag
					         WHERE owner_cd = ?
					           AND user_id  = ?
					           AND mail_id  = ?
					         LIMIT 1";
					         
					$data = array($ownerCd, $_SESSION['user_id'], $_POST['mailx_id']);
					$result = $dbSlave->query($sql,$data);
					
					if($result->numRows() == 0){
					    $tmpl = new Tmpl22($tmpl_dir . "operator/mail_new/receive_end{$template_name}.html");
						$msg = "削除したいメールが存在しませんでした";
						$tmpl->assign("msg" , $msg );
						$tmpl->assign("agent_code", $agent_code_con);
						setDairitenParam($tmpl);
						$tmpl->flush();
						exit;
					}
					
					$sql = "UPDATE female_mailbox_mailmag
					           SET stat2 = 5,
					               upd_date = now(),
					               upd_id = ?,
					               upd_ip=?
					         WHERE owner_cd = ?
					           AND mail_id = ?
					           AND user_id = ?
					         LIMIT 1";
					         
					$data = array(
						$_SESSION['user_id'],
						$_SERVER['REMOTE_ADDR'],
						$ownerCd,
						$_POST['mailx_id'],
						$_SESSION['user_id']
					);
					
					$res = $dbMaster->query($sql,$data);
					
					if(DB::isError($res)){
					    $tmpl = new Tmpl22($tmpl_dir . "operator/mail_new/receive_end{$template_name}.html");
						$msg = "削除したいメールが存在しませんでした";
						$tmpl->assign("msg" , $msg );
						$tmpl->assign("agent_code", $agent_code_con);
						setDairitenParam($tmpl);
						$tmpl->flush();
						exit;
					}
				}
			}
		    $tmpl = new Tmpl22($tmpl_dir . "operator/mail_new/receive_end{$template_name}.html");
			$msg  = "削除しました。";
			$tmpl->assign("msg" , $msg );
			$tmpl->assign("agent_code", $agent_code_con);
			setDairitenParam($tmpl);
			$tmpl->flush();
			exit;
		}
	}
	
	//------------------
	//アドレス帳登録
	//------------------
	if(isset($_POST['mode'])){
		if($_POST['mode'] == "insert"){
			//male_user_id取得
			$sql = "SELECT user_id
			          FROM male_member
			         WHERE owner_cd = {$ownerCd}
			           AND hash     = ?";
			           
			$sth = $dbSlave33->prepare($sql);
			$result = $dbSlave33->execute($sth, $_POST['delid']);
			
			if(DB::isError($result)){
				err_proc($result->getMessage());
			}
			
			$row    = $result->fetchRow();
			$user_id = $row[0];
			
			//アドレス登録しているかを確認
			$sql = "SELECT address_type
			          FROM emale_address_book
			         WHERE owner_cd     = ?
			           AND user_id      = ?
			           AND male_user_id = ?
			           AND ( address_type = 0 OR address_type = 1 OR address_type = 9 )";
			
			$con = array($ownerCd,$_SESSION['user_id'],$user_id );
			$sth = $dbSlave33->prepare($sql);
			$result = $dbSlave33->execute($sth,$con);
			
			if(DB::isError($result)){
				err_proc($result->getMessage());
			}
			
			//登録がなければアドレス帳へ登録する
			if($result->numRows() < 1){
				$data = array();
				$data['owner_cd']     = $ownerCd;
				$data['user_id']      = $_SESSION['user_id'];
				$data['male_user_id'] = $user_id;
				$data['address_type'] = 0;
				$data['memo']         = "";
				iTSinsert($data,"female_address_book",$_SESSION['user_id']);
			//登録があり、address_typeが9(削除)の場合は更新する
			}else{
				$row = $result->fetchRow();
				if($row[0] != "0"){
					$sql = "UPDATE female_address_book
					           SET address_type = 0,
					               upd_ip       = ?,
					               upd_id       = ?,
					               upd_date     = now()
					         WHERE owner_cd     = ?
					           AND user_id      = ?
					           AND male_user_id = ?
					           AND (address_type = 1 OR address_type = 9)";
					           
					$con = array(
						$_SERVER['REMOTE_ADDR'],
						$_SESSION['user_id'],
						$ownerCd,
						$_SESSION['user_id'],
						$user_id
					);
					$sth    = $dbMaster->prepare($sql);
					$result = $dbMaster->execute($sth,$con);
					
					if(DB::isError($result)){
						err_proc($result->getMessage());
					}
				}
			}
			
		    $tmpl = new Tmpl22($tmpl_dir . "operator/mail_new/receive_end{$template_name}.html");
			$msg  = "アドレス帳に追加しました。";
			$tmpl->assign("msg" , $msg );
			$tmpl->assign("agent_code", $agent_code_con);
			setDairitenParam($tmpl);
			$tmpl->flush();
			exit;
		}
	}
	
	//------------------
	//拒否設定登録
	//------------------
	if(isset($_POST['mode'])){
		if($_POST['mode'] == "deny"){
			//female_user_id取得
			$sql = "SELECT user_id
			          FROM male_member
			         WHERE owner_cd = {$ownerCd}
			           AND hash     = ?";
			
			$sth    = $dbSlave33->prepare($sql);
			$result = $dbSlave33->execute($sth, $_POST['delid']);
			
			if(DB::isError($result)){
				err_proc($result->getMessage());
			}
			
			$row     = $result->fetchRow();
			$user_id = $row[0];
			
			//アドレス登録しているかを確認
			//削除　AND address_type = 2
			$sql = "SELECT address_type
			          FROM female_address_book
			         WHERE owner_cd     = ?
			           AND user_id      = ?
			           AND male_user_id = ?";
			
			$con    = array($ownerCd,$_SESSION['user_id'],$user_id );
			$sth    = $dbSlave33->prepare($sql);
			$result = $dbSlave33->execute($sth,$con);
			
			if(DB::isError($result)){
				err_proc($result->getMessage());
			}
			

			//登録がなければアドレス帳に登録する。＆あれば拒否にUPDATEする。
			//拒否設定前の address_type を former_type へセットする
			if($result->numRows()){//UPDATE
				$sql = "UPDATE female_address_book
						   SET former_type  = address_type,
						       address_type = 2,
						       upd_ip       = ?,
						       upd_id       = ?,
						       upd_date     = now()
						 WHERE owner_cd     = ?
						   AND user_id      = ?
						   AND male_user_id = ?";
				$con = array(
					$_SERVER['REMOTE_ADDR'],
					$_SESSION['user_id'],
					$ownerCd,
					$_SESSION['user_id'],
					$user_id
				);
				$sth    = $dbMaster->prepare($sql);
				$result = $dbMaster->execute($sth,$con);
				
				if(DB::isError($result)){
					echo "0";exit;
				}
			}else{//INSERT
				$data = array(
						'owner_cd'     => $ownerCd,
						'user_id'      => $_SESSION['user_id'],
						'male_user_id' => $user_id,
						'address_type' => 2,
						'memo'         => ""
				);
			
				iTSinsert($data,"female_address_book",$_SESSION['user_id']);
			}



		    $tmpl = new Tmpl22($tmpl_dir . "operator/mail_new/receive_end{$template_name}.html");
			$msg  = "拒否設定に追加しました。";
			$tmpl->assign("msg" , $msg );
			$tmpl->assign("agent_code", $agent_code_con);
			setDairitenParam($tmpl);
			$tmpl->flush();
			exit;
		}
	}
	
	//-----------------------------------------------------
	//SQL検索条件
	//-----------------------------------------------------
	$search_str = "AND female_mailbox.cre_date <= now() ";
	$search_str_mailmag = "AND female_mailmag.send_time <= now() ";
	if(isset($_GET['midoku'])){
		//未読メールのみを表示
		$search_str .= "AND female_mailbox.stat2 = 1";
		$search_str_mailmag .= "AND female_mailbox_mailmag.stat2 = 1";
		$tmpl->assign("midoku1","&midoku");		//未読のみ表示のGETデータをセット
		
	}else{
		//未読・既読メールを表示
		$search_str .= "AND female_mailbox.stat2 <> 9 ";
		$search_str .= "AND female_mailbox.stat2 <> 5";
		$search_str_mailmag .= "AND female_mailbox_mailmag.stat2 <> 5 AND female_mailbox_mailmag.stat2 <> 9";
		$tmpl->assign("midoku1","");
	}

	//-----------------------------------------------------
	//並び替え処理
	//-----------------------------------------------------
	//並べ替え初期
	$sort = "date desc";
	$tmpl->assign("icon","<img src=\"images/icon_down.gif\">");
	$tmpl->assign("sortc","desc");

	//並び替え変更
	if(isset($_GET['sortc'])){
		if($_GET['sortc'] == "asc"){
			$sort = "date desc";
			$tmpl->assign("icon","<img src=\"images/icon_down.gif\">");
			$tmpl->assign("sortc","desc");
		} else {
			$sort = "date asc";
			$tmpl->assign("icon","<img src=\"images/icon_up.gif\">");
			$tmpl->assign("sortc","asc");
		}
	}

	//-----------------------------------------------------
	//１ページあたりの表示件数と今回表示するページ数を取得
	//-----------------------------------------------------
	$page_num = 50;	//１ページあたりの表示件数
	if(!isset($_POST['pos'])){
		//mail.phpからの場合
		if (isset($_GET['pos'])) {
			$pos = $_GET['pos'];
		} else {
			$pos = 1;
		}
	}else{
		if($_POST['pos'] == ""){
			$pos = 1;
		}else{
			$pos = $_POST['pos'];	//Nページ目を表示
		}
	}

	//-----------------------------------------------------
	//SQLのリミット値とオフセット値をセット
	//-----------------------------------------------------
	$limit = $page_num + 1;		//表示件数+1
	$offset = ($pos - 1) * $page_num;		//飛ばすデータ件数

	//-----------------------------------------------------
	//受信メール一覧取得
	//-----------------------------------------------------
	
	$flg_male_user="";
	
	if(isset($_REQUEST['male_id']) && $_REQUEST['male_id']!="" ){
		if( preg_match("/^[0-9a-zA-Z]+$/i", $_REQUEST['male_id']) ){//hashっぽいやつのみうけつける
			$flg_male_user=$_REQUEST['male_id'];
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

	if(count($rej)){
		$ni = " AND mm.hash NOT IN {$notin}";
	}


	/*** ***/
	
	$SQL2 = "SELECT DISTINCT mm.hash,
	                         mm.nick_name
	                    FROM ((female_mailbox LEFT JOIN male_member mm
	                                                 ON female_mailbox.owner_cd     = mm.owner_cd
	                                                AND female_mailbox.from_user_id = mm.user_id
	                                          LEFT JOIN male_profile2 p2
	                                                 ON female_mailbox.owner_cd     = p2.owner_cd
	                                                AND female_mailbox.from_user_id = p2.user_id)
	                                          LEFT JOIN female_address_book t3
	                                                 ON (t3.address_type = 0 OR t3.address_type = 1)
	                                                AND female_mailbox.owner_cd     = t3.owner_cd
	                                                AND female_mailbox.user_id      = t3.user_id
	                                                AND female_mailbox.from_user_id = t3.male_user_id)
	                                          LEFT JOIN female_address_book t4
	                                                 ON t4.address_type             = 2
	                                                AND female_mailbox.owner_cd     = t4.owner_cd
	                                                AND female_mailbox.user_id      = t4.user_id
	                                                AND female_mailbox.from_user_id = t4.male_user_id
	                   WHERE female_mailbox.owner_cd = ?
	                     AND female_mailbox.user_id  = ?
	                     AND mm.stat                 = 1
	                     {$ni}
	                     {$search_str}
	                ORDER BY mm.nick_name";

	$con2 = array($ownerCd,$_SESSION['user_id']);
	$sth2 = $dbSlave33->prepare($SQL2);
	$res2 = $dbSlave33->execute($sth2,$con2);
	
	if(DB::isError($res2)){
		err_proc($res2->getMessage());
	}
	
	$sel_male_val = '<select id="sel_male_id" name="sel_male_id" onChange="sel_male()"><option value="">---</option>';
	
	if($res2->numRows()>0){
		while($row = $res2->fetchRow()){
			if($row[0]==$flg_male_user){
				$sel_male_val .= '<option value="'.$row[0].'" selected>'.$row[1].'</option>';
			}else{
				$sel_male_val .= '<option value="'.$row[0].'">'.$row[1].'</option>';
			}
		}
	}
	
	$sel_male_val .= '</select>';
	$tmpl->assign("male_sel",$sel_male_val);
	/*** ***/
	
	
	
	
	$sql = "SELECT female_mailbox.mail_id AS mail_id,";		//0 メールID
	$sql .= "	female_mailbox.subject,";			//1 件名
	$sql .= "	female_mailbox.stat2,";				//2 
	$sql .= "	date_format(female_mailbox.cre_date,'%Y-%m-%d %H:%i'),";	//3 日付
	$sql .= "	mm.nick_name,";						//4 男性ニックネーム
	$sql .= "	mm.hash,";							//5 男性ハッシュ
	$sql .= "	female_mailbox.resend_id,";			//6 返信メールの確認（返信済なら返信したメールのmail_idが、未返信ならNULLが入る）
	$sql .= "	female_mailbox.cre_date AS date,";	//7 並べ替えの為
	$sql .= "	t3.owner_cd,";						//8 お気に入り登録確認（登録済なら１が、未登録ならNULLが入る）
	$sql .= "	t4.owner_cd, ";						//9 受信拒否登録確認（登録済なら１が、未登録ならNULLが入る）
	$sql .= "	p2.prof_open_flg, ";				//10
	$sql .= "	0 ";								//11 メルマガフラグ
	$sql .= " FROM ((female_mailbox LEFT JOIN male_member mm
	            ON female_mailbox.owner_cd     = mm.owner_cd
	           AND female_mailbox.from_user_id = mm.user_id ";
	
	
	if($flg_male_user!=""){
		$sql .= " AND mm.hash = ? ";
	}

	$sql .= "LEFT JOIN male_profile2 p2
	                ON female_mailbox.owner_cd     = p2.owner_cd
	               AND female_mailbox.from_user_id = p2.user_id)
	         LEFT JOIN female_address_book t3
	                ON (t3.address_type = 0 OR t3.address_type = 1)
	               AND female_mailbox.owner_cd     = t3.owner_cd
	               AND female_mailbox.user_id      = t3.user_id
	               AND female_mailbox.from_user_id = t3.male_user_id)
	         LEFT JOIN female_address_book t4
	                ON t4.address_type             = 2
	               AND female_mailbox.owner_cd     = t4.owner_cd
	               AND female_mailbox.user_id      = t4.user_id
	               AND female_mailbox.from_user_id = t4.male_user_id
	             WHERE female_mailbox.owner_cd     = ?
	               AND female_mailbox.user_id      = ?
	               AND mm.stat                     = 1
	               {$ni}
	               {$search_str}";
	
	if(!$flg_male_user){
		$sql .= " UNION SELECT `female_mailbox_mailmag`.mail_id AS mail_id,
		                       `female_mailmag`.send_subject,
		                       `female_mailbox_mailmag`.stat2,
		                       DATE_FORMAT(`female_mailmag`.send_time,'%Y-%m-%d %H:%i'),
		                       'マシェリ事務局',
		                       '7f6927fd1d33ea9',
		                       NULL,
		                       `female_mailmag`.send_time AS date,
		                       fa.owner_cd,
		                       deny.owner_cd,
		                       0,
		                       1
		                  FROM `female_mailbox_mailmag` LEFT JOIN `female_mailmag`
		                                                    USING ( owner_cd, mail_id )
		                                                LEFT JOIN female_address_book AS fa
		                                                       ON fa.owner_cd      = female_mailbox_mailmag.owner_cd
		                                                      AND fa.user_id       = female_mailbox_mailmag.user_id
		                                                      AND fa.male_user_id  = 'mmmanager'
		                                                      AND (fa.address_type = 0 OR fa.address_type = 1)
		                                                LEFT JOIN female_address_book AS deny
		                                                       ON deny.owner_cd     = female_mailbox_mailmag.owner_cd
		                                                      AND deny.user_id      = female_mailbox_mailmag.user_id
		                                                      AND deny.male_user_id = 'mmmanager'
		                                                      AND deny.address_type = 2
		                 WHERE `female_mailbox_mailmag`.owner_cd = ?
		                   AND `female_mailbox_mailmag`.user_id  = ?
		                   {$search_str_mailmag}";
	}
			
	$sql .= " ORDER BY {$sort}, mail_id DESC
	            LIMIT {$limit} OFFSET {$offset}";
	
	if($flg_male_user){
		$con = array($flg_male_user,$ownerCd,$_SESSION['user_id']);
	}else{
		$con = array($ownerCd,$_SESSION['user_id'],$ownerCd,$_SESSION['user_id']);
	}
		
	$sth    = $dbSlave33->prepare($sql);
	$result = $dbSlave33->execute($sth,$con);
	
	if(DB::isError($result)){
		err_proc($result->getMessage());
	}

	$dairi_param = getDairitenParam();

	$tmpl->loopset("mail_title");
	
	$cnt = 0;//ループカウンタ
	while($row = $result->fetchRow()){
		$cnt++;
		
		if ($cnt > $page_num) {
			$tmpl->loopset("");
			break;//51件目のデータは次のデータがあるかの確認用なので処理を中断
		}
		
		$tmpl->assign( "mail_id" , $row[0] );
		$tmpl->assign("hash" , $row[5] );
		$tmpl->assign( "cre_date" , $row[3] );
		
		if(!isset($row[1]) || $row[1] == "") {
			$row[1] = "[件名なし]";
		}
		
		//ボタンの表示
		if($row[8] == 1){
			//アドレス帳登録済
			$tmpl->assign( "okiniiri" , "" );
		}else{
			//アドレス帳未登録
			$tmpl->assign("okiniiri","<input type=\"button\" value=\"{$address_btn}\" onClick=\"go_address('$row[5]',{$pos})\">" );
		}
		if($row[9] == 1){
			//受信拒否登録済
			$tmpl->assign("kyohi","");
		}else{
			//受信拒否未登録
			$tmpl->assign("kyohi","<input type=\"button\" value=\"{$setting_btn}\" onclick=\"go_deny('$row[5]',{$pos})\">" ) ;
		}

		if($row[2] == 1 || ($row[11] == "1" && is_null($row[2]))){
			//未読
			$tmpl->assign( "cre_date" , "<b>" . $row[3] ."</b>" );
			
			// メルマガフラグ
			if($row[11] == "1"){
				$tmpl->assign( "subject" ,  "<a href=mail.php?mailx_id=".$row[0]."&pos=".$pos."&".$dairi_param."><b>". htmlspecialchars($row[1]) ."</b></a>");
			}else{
				$tmpl->assign( "subject" ,  "<a href=mail.php?mail_id=".$row[0]."&pos=".$pos."&".$dairi_param."><b>". htmlspecialchars($row[1]) ."</b></a>");
			}
			
			//プロフオープンフラグ
			if($row[10] == "1"){
				$tmpl->assign( "from_nick_name" ,  "<b><a href=\"javascript:func_detail('{$row[5]}')\">".$row[4] ."</a></b>");
			}else{
				$tmpl->assign( "from_nick_name" ,  "<b>" .$row[4] ."</b>");
			}
			
			$tmpl->assign( "img" ,  "<img src=\"images/icon_midoku.gif\" width=\"20\" height=\"15\" alt=\"未読メール\" title=\"未読メール\">");
			$tmpl->assign( "jyoutai" , "midoku");		//色
		}else{
			//既読
			$tmpl->assign( "cre_date" , $row[3] );
			
			// メルマガフラグ
			if($row[11] == "1"){
				$tmpl->assign( "subject" , "<a href=mail.php?mailx_id=".$row[0]."&pos=".$pos."&".$dairi_param.">" .htmlspecialchars($row[1])."</a>");
			}else{
				$tmpl->assign( "subject" , "<a href=mail.php?mail_id=".$row[0]."&pos=".$pos."&".$dairi_param.">" .htmlspecialchars($row[1])."</a>");
			}
			
			//プロフオープンフラグ
			if($row[10] == "1"){
				$tmpl->assign( "from_nick_name" , "<a href=\"javascript:func_detail('{$row[5]}')\">".$row[4] ."</a>") ;
			}else{
				$tmpl->assign( "from_nick_name" , "" .$row[4] ."") ;
			}
			
			// 返信した？
			if(!$row[6]){
				$tmpl->assign("img", "<img src=\"images/icon_kidoku.gif\" width=\"20\" height=\"15\" alt=\"既読メール\" title=\"既読メール\">");
				$tmpl->assign("jyoutai","kidoku");
			}else{
				//返信内容を表示
				$tmpl->assign("img" , "<a href=mail_resend.php?mail_id=".$row[6]."&".$dairi_param."><img src=\"images/icon_return.gif\" width=\"20\" height=\"15\" alt=\"返信メール内容を表示\" title=\"返信メール内容を表示\">");
				$tmpl->assign("jyoutai", "kidoku");
			}
		}
		
		if($row[11] == "1"){
			$tmpl->assign("x","x");
		}else{
			$tmpl->assign("x","");
		}
		
		setDairitenParam($tmpl);
		$tmpl->loopnext();
	}
	$tmpl->loopset("");
	
	//--------------------------------------
	//ページリンク処理
	//--------------------------------------
	//1ページ目を表示する場合
	if ($pos == 1) {
		//1ページを表示なので、戻りページリンクはなし	
		$tmpl->assign("prev_link","");
		
		if ($cnt > $page_num) {
			$tmp = $pos + 1;
			$tmpl->assign("next_link","　<a href=\"javascript:goto_page($tmp,'$flg_male_user')\">{$next_link}</a>　");
		}else{
			//データ件数が51件未満なので、次ページリンクなし
			$tmpl->assign("next_link","");
		}
	}else{//1ページ目以外を表示する場合
		//1ページ目以外なら、必ず戻りはある。
		$tmp = $pos - 1;
		$tmpl->assign("prev_link","　<a href=\"javascript:goto_page($tmp,'$flg_male_user')\">{$prev_link}</a>　");
		
		if ($cnt > $page_num) {
			$tmp = $pos + 1;
			$tmpl->assign("next_link","　<a href=\"javascript:goto_page($tmp,'$flg_male_user')\">{$next_link}</a>　");
		} else {
			//データ件数が51件未満なので、次ページリンクなし
			$tmpl->assign("next_link","");
		}
	}

	$tmpl->assign("agent_code", $agent_code_con);
	setDairitenParam($tmpl);
	$tmpl->flush();

?>