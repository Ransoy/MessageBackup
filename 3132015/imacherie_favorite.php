<?
//require_once 'Owner.inc';
require_once 'common_proc.inc';
//require_once 'db_con.inc';
require_once 'tmpl2.class.inc';
require_once 'imacherie_male.inc';
//require_once 'xxsatoboy.inc';
require_once 'mc_session_routines.inc';
require_once 'boy_head_neo.inc';

//Line mail
require_once 'CommonDb.php';
require_once 'message/MessageHelper.php';

//$dbSlave33 = getDBCon("S","DBS7");

	//DB
	$db = new mcDB(getMaster());
	$dbMaster = $db->get_resource();
	$db = new mcDB(getRandomSlave());
	$dbSlave = $db->get_resource();

	$tmpl = new Tmpl2($tmpl_dir . "imacherie_favorite.html");

	$tab_id = 0;
	if(isset($_POST['tab_id']) && $_POST['tab_id'] != ""){
		$tab_id = $_POST['tab_id'];
	}
	// 選択されているタブが存在するかチェック
	if($tab_id != 0){
		$checTab = selectedTabCheck($tab_id);
		if(!$checTab){
			print "error{$tab_id}";
			exit;
		}
	}
	//------------------------
	// ヘッダ情報取得
	$myInfo   = myInformation();
	$asiato   = myAsiato();// 足跡の有無
	$onairNum = getOnlineNum();// オンライン人数取得
	$tmpl->assign("iHearder_menu",iMachereiHeardMenu());// ヘッダ
	
	//Line message get unread message
	$messageHelper = new MessageHelper();
	$unread = $messageHelper->getAllUnreadMessage($_SESSION['user_id'], 1);

	//------------------------
	// メンバーボックス表示
	$tmpl->assign("nick_name",str_replace(" ", "&nbsp;", $myInfo['nick_name']));
	$tmpl->assign("point",$myInfo['point']);
	//$tmpl->assign("midoku_num",$myInfo['midoku_num']);
	$tmpl->assign('midoku_num', $unread['total']);
	$tmpl->assign("asiato",$asiato);
	$tmpl->assign("onair_num",$onairNum['now']);
	$tmpl->assign("onair_num24",$onairNum['num24']);

	//----------------------------------
	// iMacherieTab取得
	$tmpl->assign("myTabList",iMacherieGetMyTab('dummy',6));

	//----------------------------------
	// テーマ取得
	$theme = getTabTheme(0,0);
	$tmpl->assign("theme_bg",$theme['img']);
	$tmpl->assign("theme_color",$theme['color']);

	//プロフィールページから登録
	if(isset($_GET['sid']) && $_GET['sid'] != ""){
		$_POST['sid']  = $_GET['sid'];
		$_POST['mode'] = "favorite";
	}

	$tmpl->assign("msg","");
	if(isset($_POST['mode'])){
		if($_POST['mode'] == "favorite"){
			$sth = $dbSlave->prepare("select user_id from female_profile where owner_cd = ? and hash = ?");
			$data = array($ownerCd,$_POST['sid']);
			$result = $dbSlave->execute($sth, $data);

			if(DB::isError($result)){
				err_proc($result->getMessage());
			}

			if($row = $result->fetchRow()){
				$f_user_id = $row[0];
			} else {
				print "IDエラー";
				exit;
			}
			//登録チェック
			$sth = $dbSlave->prepare("select count(*) from male_address_book where owner_cd = ? and user_id = ? and female_user_id = ? and (address_type = 0 or address_type = 1 or address_type = 9)");
			$data = array($ownerCd,$_SESSION['user_id'],$f_user_id);
			$result = $dbSlave->execute($sth, $data);

			if(DB::isError($result)){
				err_proc($result->getMessage());
			}

			$row = $result->fetchRow();

			if($row[0] > 0){
				//２重登録チェック
				$sth = $dbSlave->prepare("select count(*) from male_address_book where owner_cd = ? and user_id = ? and female_user_id = ? and address_type = 1");
				$data = array($ownerCd,$_SESSION['user_id'],$f_user_id);
				$result = $dbSlave->execute($sth, $data);

				if(DB::isError($result)){
					err_proc($result->getMessage());
				}

				$row = $result->fetchRow();

				if($row[0] > 0){
					$tmpl->assign("msg","すでに登録されています。");
				}else{
					$data = array();
					$data['address_type'] = 1;
					iTSupdate($data,"male_address_book",$_SESSION['user_id']," owner_cd = " .$ownerCd. " and user_id = '" . $_SESSION['user_id'] . "' and female_user_id = '" .$f_user_id. "' and address_type = 0");
					$tmpl->assign("msg","お気に入りに登録しました。");
				}
			} else {
				$data = array();
				$data['owner_cd'] = 1;
				$data['user_id'] = $_SESSION['user_id'];
				$data['female_user_id'] = $f_user_id;
				$data['address_type'] = 1;
				$data['memo'] = "";
				$data['address_type'] = 1;
				iTSinsert($data,"male_address_book",$_SESSION['user_id']);
				$tmpl->assign("msg","お気に入りに登録しました。");
				
				$contact = new MessageContact();
				$contact->setOwnerId($ownerCd);
				$contact->setUserId($_SESSION['user_id']);
				$contact->setPerformerId($f_user_id);
				$contact->setIsFromChat(0);
				$contact->create();
				
			}
		}// END:favorite ADD

		if($_POST['mode'] == "del_favo"){
			if(isset($_POST['del_check']) && is_array($_POST['del_check']) && !empty($_POST['del_check']) ){
				$sth2 = $dbSlave->prepare("select user_id from female_profile where owner_cd = ? and hash = ?");
				$sth = $dbMaster->prepare("update male_address_book set address_type = 0,upd_id = ?,upd_date = now(),upd_ip = ? where owner_cd = ? and user_id = ? and female_user_id = ? and address_type = 1");
				foreach($_POST['del_check'] as $value){
					$data2 = array($ownerCd,$value);
					$result2 = $dbSlave->execute($sth2, $data2);

					if(DB::isError($result2)){
						err_proc($result2->getMessage());
					}

					if($row2 = $result2->fetchRow()){
						$f_user_id = $row2[0];
					}else{
						$tmpl->assign("msg","このオペレータは退会しているか、もしくは存在していません。");
						break;
					}

					$data = array($_SESSION['user_id'],$_SERVER['REMOTE_ADDR'],$ownerCd,$_SESSION['user_id'],$f_user_id);
					$result = $dbMaster->execute($sth, $data);

					if(DB::isError($result)){
						print_r($result->getMessage());
					}

					$tmpl->assign("msg","お気に入りから削除しました。");
				}
			}
		}
	}




	 //拒否者かどうかの確認
	$sql = "SELECT mm.user_id,
			       fab.address_type,
			       dm.to_hash
			  FROM male_member mm LEFT JOIN female_address_book fab
			                             ON mm.user_id  = fab.male_user_id
			                      LEFT JOIN deny_member dm
			                             ON dm.to_hash  = mm.hash
			 WHERE fab.user_id      = ?
			   AND fab.male_user_id = ?
			   AND dm.from_hash     = ?";

	$arr = array($ownerCd,$_SESSION['user_id'],$row[3]);
	$pre = $dbSlave33->prepare($sql);
	$res = $dbSlave33->execute($pre,$arr);
//print_r($res->fetchRow());
//echo "<br />";
	$fet = $res->fetchRow();




	//お気に入りの人の表示
	$sql = "SELECT fp.nick_name,
	               fp.hash,
	               fp.img,
	               DATE_FORMAT(fm.last_login,'%y/%c/%e'),
	               (SELECT COALESCE(onair.stat,0)
	                  FROM onair
	                 WHERE onair.owner_cd = {$ownerCd}
	                   AND onair.user_id  = mad.female_user_id
	                   AND (onair.start_date IS NULL OR onair.start_date < now())),
	               fab.address_type,
	               dm.to_hash,
				   fp.clip
	          FROM (male_address_book mad INNER JOIN male_member mm
	                                              ON mad.user_id = mm.user_id

	                                      INNER JOIN female_profile fp
	                                              ON mad.owner_cd       = fp.owner_cd
	                                             AND mad.female_user_id = fp.user_id)
	                                      INNER JOIN female_member fm
	                                              ON mad.owner_cd       = fm.owner_cd
	                                             AND mad.female_user_id = fm.user_id
	                                       LEFT JOIN female_address_book fab
			                                      ON mad.user_id        = fab.male_user_id
			                                     AND fp.user_id         = fab.user_id
			                               LEFT JOIN deny_member dm
			                                      ON dm.upd_id          = fm.user_id
			                                     AND dm.to_hash         = mm.hash
	         WHERE mad.owner_cd      = ?
	           AND mad.user_id       = ?
	           AND mad.address_type  = 1
	           AND fm.stat           = 1
	           AND (fab.address_type = 0 OR
	                fab.address_type = 1 OR
	                fab.address_type IS NULL)
	           AND dm.to_hash        IS NULL
	      ORDER BY mad.upd_date";

	$sth    = $dbMaster->prepare($sql);
	$data   = array($ownerCd,$_SESSION['user_id']);
	$result = $dbMaster->execute($sth, $data);

	if(DB::isError($result)){
		err_proc($result->getMessage());
	}

	$i = 2;
	$tmpl->loopset("loop_person");

	while ($row = $result -> fetchRow()){
		$nick_name_txt = $row[0];
		$nick_name_txt = str_replace(" ", "&nbsp;", $nick_name_txt);
		$tmpl->assign("nick_name",$nick_name_txt);
		$tmpl->assign("hash",$row[1]);

		if($row[2] == ""){
			$tmpl->assign("img","/images/common/noimage.gif");
		}else{
			$tmpl->assign("img","/imgs/op/120x90/".$row[2]);
		}

		$tmpl->assign("last_login",$row[3]);

		if($row[4] == "1" || $row[4] == "2" || $row[4] == "3"){
			$tmpl->assign("line_stat","online.gif");
		}else{
			$tmpl->assign("line_stat","offline.gif");
		}

		if($i%2 == 0){
			$tmpl->assign("clear1","");
		}

		if($i%2 == 1){
			$tmpl->assign("clear2","");
		}

if( $row[7] ){
			$tmpl->assign("img_style","width:120px;height:90px;");
}else{
			$tmpl->assign("img_style","");
}


		$i++;
		$tmpl->loopnext();
	}

	$tmpl->loopset("");

	if($i%2 != 0){
		$tmpl->assign("clear2","");
	}

	$tmpl->flush();
?>
