<?
require_once 'common_proc.inc';
require_once 'common_db.inc';
require_once 'CommonSelect.inc';
require_once 'FormObject.inc';
require_once 'tmpl2.class_ex.inc';
require_once 'Owner.inc';

///////////////////////////////////////////////////////////////////////////////////
	//メンテナンス状態の取得
	$sql = "SELECT value FROM point_setting WHERE owner_cd = 1 AND id = '85'";
	$result = $dbSlave->query($sql);
	if(DB::isError($result)){
		var_dump($result);
		err_proc($result->getMessage());
		}
	$row = $result->fetchRow();
	$mainte_flg = floor($row[0]);

	if($mainte_flg == "1"){
		header("Location: http://www.macherie.tv/biglobe/");
		exit;
	}
///////////////////////////////////////////////////////////////////////////////////

	session_start();
	if(isset($_GET['logout'])){
		session_destroy();
		if(isset($_SESSION['stat'])){
			unset($_SESSION['stat']);
		}
	}
	$fobj = new FormObject("女性プロフィール");
	$tmpl_dir = $tmpl_dir . "biglobe/";

	//招待オンオフ
	$sql = "select value from point_setting where id = 18";
	$result = $dbMaster->query($sql);
	$row = $result->fetchRow();
	(int) $st_flg = floor($row[0]);
	$sql = "select value from point_setting where id = 44";
	$result = $dbMaster->query($sql);
	$row = $result->fetchRow();
	(int) $st_type = floor($row[0]);

	// ランダム表示(次へ)
	if(isset($_GET['rand_id'])){
		if(isset($_GET['world']) && $_GET['world'] == "1"){
			//ワールドマシェリ
			$world_where = "and female_member.world_flg = 1 ";
		}else{
			$world_where = "and female_member.world_flg = 0 ";
			$world_where = "";
		}

		$sql  = "select female_profile.hash ";
		$sql .= "from";
		$sql .= " (onair INNER JOIN female_profile ON onair.owner_cd = female_profile.owner_cd and onair.user_id = female_profile.user_id and (onair.start_date is null or onair.start_date < now()) and female_profile.hash > ? )";
		$sql .= " INNER JOIN female_member ON onair.owner_cd = female_member.owner_cd and onair.user_id = female_member.user_id ";
		$sql .= "where onair.owner_cd = ".$ownerCd." and female_profile.owner_cd = ".$ownerCd." and female_member.owner_cd = ".$ownerCd." and female_member.stat = 1 and onair.stat = 1 {$world_where}";
		$sql .= "order by female_profile.hash limit 1";
		$sth = $dbSlave->prepare($sql);
		$result = $dbSlave->execute($sth, $_GET['id']);
		if(DB::isError($result)){
			print $sql;
			err_proc($result->getMessage());
		}
		if($row = $result->fetchRow()){
			$_GET['id'] = $row[0];
		}else{
			// なかったので初めから
			$sql  = "select female_profile.hash ";
			$sql .= "from";
			$sql .= " (onair INNER JOIN female_profile ON onair.owner_cd = female_profile.owner_cd and  onair.user_id = female_profile.user_id and (onair.start_date is null or onair.start_date < now()) )";
			$sql .= " INNER JOIN female_member ON onair.owner_cd = female_profile.owner_cd and onair.user_id = female_member.user_id ";
			$sql .= "where onair.owner_cd = ".$ownerCd." and female_profile.owner_cd = ".$ownerCd." and female_member.owner_cd = ".$ownerCd." and female_member.stat = 1 and onair.stat = 1 {$world_where}";
			$sql .= "order by female_profile.hash limit 1";
			$result = $dbSlave->query($sql);
			if(DB::isError($result)){
				print $sql;
				err_proc($result->getMessage());
			}
			if($row = $result->fetchRow()){
				$_GET['id'] = $row[0];
			}
		}
	}

	$cookie_cnt = 0;
	if(!isset($_GET['rand_id']) && !isset($_GET['back']) && !isset($_GET['small']) && !isset($_GET['normal']) && !isset($_GET['big']) && !isset($_GET['logout'])){
		// クッキーの初期化
		setcookie("Mshicho_girls", "0:".$_GET['id']);
	}
	else if(isset($_GET['rand_id'])){
		// 保存する処理
		if(isset($_COOKIE['Mshicho_girls']) && $_COOKIE['Mshicho_girls'] != ""){
			$cookie_array = explode(",",$_COOKIE['Mshicho_girls']);
			foreach($cookie_array as $key => $value){
				$cookie_cnt = $key;
				$cookie_val = explode(":",$value);
				$last_id = $cookie_val[1];
			}
			if($last_id != $_GET['id']){
				$cookie_cnt++;
				setcookie("Mshicho_girls",$_COOKIE['Mshicho_girls'].",".$cookie_cnt.":".$_GET['id']);
			}
		}else{
			setcookie("Mshicho_girls", "0:".$_GET['id']);
		}
	}
	else if(isset($_GET['back'])){
		// 戻るボタンの処理
		if(isset($_COOKIE['Mshicho_girls'])){
			$cookie_array = explode(",",$_COOKIE['Mshicho_girls']);
			foreach($cookie_array as $key => $value){
				if($_GET['back'] == $key){
					$cookie_cnt = $key;
					$cookie_del = $key + 1;
					$back_id = explode(":",$value);
					$_GET['id'] = $back_id[1];
				}
			}
		}
		if($_GET['back'] == 0){
			setcookie("Mshicho_girls", "0:".$_GET['id']);
		}else{
			$del_id = explode(",".$cookie_del.":",$_COOKIE['Mshicho_girls']);
			setcookie("Mshicho_girls", $del_id[0]);
		}
	}
	else{
		if(isset($_COOKIE['Mshicho_girls'])){
			$cookie_array = explode(",",$_COOKIE['Mshicho_girls']);
			foreach($cookie_array as $key => $value){
				$cookie_cnt = $key;
			}
		}
	}

	//女性情報の表示
	$sql  = "select ";
	$sql .= "female_profile.user_id, ";			//0
	$sql .= "female_profile.nick_name, ";		//1
	$sql .= "onair.chat_mode, ";				//2
	$sql .= "onair.stat, "	;					//3
	$sql .= "onair.fcs_no, ";					//4
	$sql .= "onair.hash, ";						//5
	$sql .= "female_profile.charm_point, ";		//6
	$sql .= "female_profile.job, ";				//7
	$sql .= "female_profile.category, ";		//8
	$sql .= "female_profile.favorite, ";		//9
	$sql .= "female_profile.area, ";			//10
	$sql .= "female_profile.age, ";				//11
	$sql .= "female_profile.appear_time, ";		//12
	$sql .= "female_profile.mic, ";				//13
	$sql .= "female_profile.personality, ";		//14
	$sql .= "female_profile.height, ";			//15
	$sql .= "female_profile.waist, ";			//16
	$sql .= "female_profile.bust, ";			//17
	$sql .= "female_profile.hip, ";				//18
	$sql .= "female_profile.cup, ";				//19
	$sql .= "female_profile.blood_type, ";		//20
	$sql .= "female_profile.type, ";			//21
	$sql .= "female_profile.img, ";				//22
	$sql .= "female_member.world_flg, ";		//23
	$sql .= "onair.mizugi_flg, ";				//24
	$sql .= "female_profile.charm_point_other, ";//25
	$sql .= "onair.machiawase_flg ";			//26
	$sql .= " from female_profile LEFT JOIN onair ON female_profile.user_id = onair.user_id and (onair.start_date is null or onair.start_date < now()) ";
	$sql .= " LEFT JOIN female_member ON female_profile.user_id = female_member.user_id and female_profile.owner_cd = female_member.owner_cd ";
	$sql .= "where female_profile.owner_cd = ".$ownerCd." and female_profile.hash = ? and female_member.stat <> 9";
	$sth = $dbMaster->prepare($sql);
	$result = $dbMaster->execute($sth, $_GET['id']);
	if(DB::isError($result)){
		print $sql;
		err_proc($result->getMessage());
	}
	if(!$row = $result->fetchRow()){
		$row[0] = "";
#		print "不正なアクセスです";
header("Location: /biglobe/404.html\n\n");
		exit();
	}
	//すでに居ないときのため
	if(!isset($row[3])){
		$row[4] = "A";
	}
	if($row[4] == "Z"){
		$row[4] = "A";
	}
	$female_user_id = $row[0];
	$chat_mode = $row[2];
	$onair_stat = $row[3];
	$fcs_no = $row[4];
	$sid = $row[5];
	$world_flg = $row[23];
	$eventLoginFlg = $row[24];

	// FCS判定
	$fcs_no1 = $row[4];
	$fcs_no2 = strtolower($row[4]);

	//error
	if(isset($_GET['emsg']) && $_GET['emsg'] != ""){
		//print mb_convert_encoding(urldecode($_GET['emsg']), "SJIS", "EUC-JP");
	}
	// テンプレート読み込み
	$screenSize = "normal";
	if(isset($_GET['small'])){
		$screenSize = "small";
	}
	else if(isset($_GET['normal'])){
		$screenSize = "normal";
	}
	else if(isset($_GET['big'])){
		$screenSize = "big";
	}
	else if(isset($_COOKIE['Mshicho'])){
		$screenSize = $_COOKIE['Mshicho'];
	}
	$tmpl = new Tmpl22($tmpl_dir . "chat/shicho_{$screenSize}.html");

	$fobj->setValue('charm_point',$row[6]);
	$fobj->setValue('job',$row[7]);
	$fobj->setValue('category',$row[8]);
	$fobj->setValue("favorite",$row[9]);
	$fobj->setValue('area',$row[10]);
	$fobj->setValue('age',$row[11]);
	$fobj->setValue('appear_time',$row[12]);
	$fobj->setValue('mic',$row[13]);
	$fobj->setValue('personality',$row[14]);
	$fobj->setValue('height',$row[15]);
	$fobj->setValue('waist',$row[16]);
	$fobj->setValue('bust',$row[17]);
	$fobj->setValue('hip',$row[18]);
	$fobj->setValue('cup',$row[19]);
	$fobj->setValue("blood_type",$row[20]);
	$fobj->setValue('type',$row[21]);
	//一般女性以外の場合　外人用項目取得
	if($world_flg != 0){
		$sql = "select ";
		$sql .= "face_type, ";     //0
		$sql .= "Japanese, ";      //1
		$sql .= "english, ";       //2
		$sql .= "rainiti_num, ";   //3
		$sql .= "rainiti_area, ";  //4
		$sql .= "nationality ";    //5
		$sql .= "from female_profile_world ";
		$sql .= "where owner_cd = ? and user_id = ? ";
		$sth = $dbMaster->prepare($sql);
		$con = array($ownerCd,$female_user_id);
		$result = $dbMaster->execute($sth, $con);
		if(DB::isError($result)){
			err_proc($result->getMessage());
		}
		if($row_w = $result->fetchRow()){
			$fobj->setValue('face_type',$row_w[0]);
			$fobj->setValue('Japanese',$row_w[1]);
			$fobj->setValue('english',$row_w[2]);
			$fobj->setValue('rainiti_num',$row_w[3]);
			$fobj->setValue('rainiti_area',$row_w[4]);
			$fobj->setValue('nationality',$row_w[5]);
		}
	}

	// 2009-10-15 wakasugi@innet get job_other
	// 職業その他を選択した場合入力を female_job_other に保存し、
	if($row[7] == "18"){
		$sql = 'SELECT job_other FROM female_job_other WHERE owner_cd = ? AND user_id = ? LIMIT 1;';
		$result = $dbSlave->query($sql,array($ownerCd, $female_user_id));
		if(DB::isError($result)){
			//err_proc($result->getMessage());
		}else{
			if($row2 = $result->fetchRow()){
				$fobj->setValue('job',$row2[0]);
			}
		}
	}
    //chatzone
	if($GL_CHATZONE_PROFILE == 1){
	    $result = $dbSlave->query("SELECT dummy FROM onair WHERE owner_cd = ? AND user_id = ? AND start_date <= now()",array($ownerCd, $female_user_id));
	    if($row_dummy = $result->fetchRow()){
	        if($row_dummy[0] == "1" ){
	            $fobj->setValue('job', getEncryptionIP($_SERVER['HTTP_REMOTE_ADDR']));
	        }
	    }
	}
	if(strcmp($row[6], "8") == 0){
		$fobj->setValue('charm_point',$row[25]);
	}

	$tmpl->setFormInstance($fobj);
	$tmpl->setFormObjectValues();
	if($world_flg != 0){
		$tmpl->assign("world_disp","");
	}else{
		$tmpl->assign("normal_disp","");
	}
	$tmpl->assign("nick_name",$row[1]);
	$tmpl->assign("girl_img",$row[22]);

	// 視聴サイズ保存
	setcookie ("Mshicho", "$screenSize",time()+30*24*60*60, "/");

	$hash = md5("sscawdvcxz".time());
	$param = "?hash=".$hash."&id=" . $_GET['id'] . "&stat=".$onair_stat;

	// 男性情報取得
	$otoko_hash = "";
	$point = "";
	$nick_name = "";
	$assortment = "";
	$mlv1 = "";
		
	$tmpl->assign("machiawase", $row[26]);
	$tmpl->assign("chat_mode", $row[2]);
	$tmpl->assign("stat", $row[3]);
	  
	
	if(isset($_SESSION['user_id'])){

        //get user assortment type
        $sql  = "select member.assortment from male_member member where member.owner_cd = ? and member.user_id = ?";		                
        $con = array($ownerCd,$_SESSION['user_id']);
        $sth = $dbSlave->prepare($sql);
        $result = $dbSlave->execute($sth, $con);
        if(DB::isError($result)){
          err_proc($result->getMessage());
          exit;
        }
        $row = $result->fetchRow();        
        if($row[0] == 0 || $row[0] == '0') {
            $column_type = "free_member_sec";
        } 
        elseif($row[0] == 1 || $row[0] == '1') { 
            $column_type = "pay_member_sec";
        } 
        else {
          continue;
        }
        //get flash video seconds
        $sql = "select {$column_type}, member_assort from audience_setting where member_assort = 'macherie'";                
		$result = $dbSlave->query($sql);
        if(DB::isError($result)){
          print $sql;
          err_proc($result -> getMessage());
        }
        else {
          $row = $result->fetchRow();
          $tmpl->assign("m_overlay_duration", $row[0]);
        }      
	  
		$sql  = "select ";
		$sql .= "male_member.hash, ";					//0
		$sql .= "male_point.point, ";					//1
		$sql .= "male_member.nick_name, ";				//2
		$sql .= "male_member.assortment, ";				//3
		$sql .= "male_member.mlv1 ";					//4
		$sql .= "from male_member left join male_point on male_member.user_id = male_point.user_id and male_member.owner_cd = male_point.owner_cd ";
		$sql .= "where male_member.user_id=? and male_member.owner_cd = ".$ownerCd;
		$sth = $dbMaster->prepare($sql);
		$result = $dbMaster->execute($sth, $_SESSION['user_id']);
		if(DB::isError($result)){
			print $sql;
			err_proc($result->getMessage());
		}
		if($row = $result->fetchRow()){
			$otoko_hash = $row[0];
			$point = $row[1];
			$nick_name = $row[2];
			$assortment = $row[3];
			$mlv1 = $row[4];
			$st = 0;			// 招待
			if($mlv1 == 1 && $point >= 20 && $st_flg == 1){
				$sql  = "select max(UNIX_TIMESTAMP(upd_date) - UNIX_TIMESTAMP(cre_date)) as login_time ";
				$sql .= "from chat_log_all where male_user_id = '" . $_SESSION['user_id'] ."' and ";
				$sql .= "female_user_id = '" . $female_user_id . "' and ";
				$sql .= "cre_date >= now() - interval 4 hour";
				$sth = $dbMaster->prepare($sql);
				$result = $dbMaster->execute($sth);
				if(DB::isError($result)){
					print $sql;
					err_proc($result->getMessage());
				}
				if(!$row = $result->fetchRow()){
					$st = 1;
				}else{
					if($row[0] == null){
						$st = 1;
					}else if($row[0] <= 10){
						$st = 1;
					}
				}
				if($st == 1){
					$sql  = "select max(UNIX_TIMESTAMP(upd_date) - UNIX_TIMESTAMP(cre_date)) as login_time ";
					$sql .= "from chat_log where male_user_id = '" . $_SESSION['user_id'] ."' and ";
					$sql .= "female_user_id = '" . $female_user_id . "'";
					$sth = $dbMaster->prepare($sql);
					$result = $dbMaster->execute($sth);
					if(DB::isError($result)){
						print $sql;
						err_proc($result->getMessage());
					}
					if($row = $result->fetchRow()){
						if($row[0] == null){
							$st = 1;
						}else if($row[0] <= 10){
							$st = 1;
						}else{
							$st = 0;
						}
					}
				}
				if($st == 1){
					$sql = "select 1 from deny_member where from_hash = ? and to_hash = ?";
					$con = array($_GET['id'],$otoko_hash);
					$sth = $dbSlave->prepare($sql);
					$result = $dbSlave->execute($sth,$con);
					if(DB::isError($result)){
						print $sql;
						err_proc($result->getMessage());
					}
					if($row = $result->fetchRow()){
						$st = 0;
					}
				}
			}
			$login = 1;
			$tmpl->assign("login","1");
			$param .= "&user_id=".$_SESSION['user_id']. "&nick_name=".rawurlencode(mb_convert_encoding ($nick_name,"UTF-8","EUC-JP")). "&point=".$point. "&sid=".$sid. "&st=".$st;
		}
	} 
	else {
	  $sql = "select not_member_sec from audience_setting where member_assort = 'macherie'";
	  $result = $dbSlave->query($sql);
	  if(DB::isError($result)){
	    print $sql;
	    err_proc($result->getMessage());
	  }
	  else {
	    $row = $result->fetchRow();
	    $tmpl->assign("m_overlay_duration", $row[0]);
	  }	  
	} 

	if (empty($login)) {
		$tmpl->assign('not_login',1);
	}

	//world
	$world = 0;
	if(isset($_GET['world'])){
		$world = $_GET['world'];
	}
	$param .= "&asp=biglobe";
	$param .= "&chat_mode=".$chat_mode."&ml_hash=".$otoko_hash."&fcs_no=".$fcs_no1."&world=".$world."&st_t=".$st_type. "&event_flg=".$eventLoginFlg;
	if(isset($_SESSION['user_id'])){
		$tmpl->assign("screen_size", '');
	}else{
	    $tmpl->assign("screen_size", 'overlay_'.$screenSize);		
	}

	$tmpl->assign("fcs_no",$fcs_no2);
	$tmpl->assign("param",$param);
	$tmpl->assign("female_id",$_GET['id']);
	if(isset($_GET['world']) && $_GET['world'] == "1"){
		$tmpl->assign( "world", $_GET['world']);
	}else{
		$tmpl->assign( "world", "0");
	}
	if($cookie_cnt > 0){
		$tmpl->assign( "back_id", $cookie_cnt - 1);
	}else{
		$tmpl->assign( "back_id_non", "");
	}
	//Eventファイルオープン
	$fp = fopen('/var/www/livechat/htdocs/include/event/event.csv','r');
	$e_dat = fgetcsv($fp, 32);
	$eventType = $e_dat[0];
	fclose($fp);
/*
	if( $eventType != "0" && $eventLoginFlg == "1" ){
		$tmpl->assign( "event_disp", "");
	}
*/
	$tmpl->flush();
?>
