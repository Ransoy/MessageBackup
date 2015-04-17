<?php
require_once 'common_db_in.inc';
require_once 'Owner.inc';

	//オフライン
	$ctop[""][""] = "offh";
	$css[""][""] = "off";
	//２ショット
	$ctop[1][0] = "on2h";
	$css[1][0] = "on";
	//パーティー
	$ctop[1][1] = "onph";
	$css[1][1] = "pon";
	//ツーショット中
	$ctop[2][0] = "2h";
	$css[2][0] = "ch";
	//パーティー中
	$ctop[2][1] = "ph";
	$css[2][1] = "pc";

	$retval = "";

/*
	$sql = "select ";
	$sql .= "female_profile.nick_name, ";
	$sql .= "onair.stat, ";
	$sql .= "case when female_profile.new_face = 1 then ";
	$sql .= 'case when female_member.fine_flg = "1" then "4" else "1" end ';
	$sql .= "when  female_profile.new_face = 2 then ";
	$sql .= 'case when female_member.fine_flg = "1" then "5" else "2" end ';
	$sql .= 'when female_member.fine_flg = "1" then ';
	$sql .= '"3" else "0" ';
	$sql .= "end as cnew, ";
	$sql .= 'case when female_profile.img = "" then "/imgs/op/jyunbi.gif" else  female_profile.img end as img, ';
	$sql .= "onair.chat_mode, ";
	$sql .= 'case when female_profile.mic = 1 then "1" else "0" end as mic ';
	$sql .= "from female_member, female_profile ";
	$sql .= "LEFT OUTER JOIN onair ON female_profile.owner_cd = onair.owner_cd  and ";
	$sql .= "female_profile.user_id = onair.user_id ";
	$sql .= "where ";
	$sql .= "female_member.owner_cd = ? and ";
	$sql .= "female_member.owner_cd = female_profile.owner_cd and ";
	$sql .= "female_member.user_id = ? and ";
	$sql .= "female_member.user_id = female_profile.user_id";
*/
	$sql = "SELECT fp.nick_name,
	               onair.stat,
	               CASE WHEN fp.new_face = 1   THEN
	               CASE WHEN fm.fine_flg = '1' THEN '4'                   ELSE '1'    END
	                    WHEN fp.new_face = 2   THEN
	               CASE WHEN fm.fine_flg = '1' THEN '5'                   ELSE '2'    END
	                    WHEN fm.fine_flg = '1' THEN '3'                   ELSE '0'    END AS cnew,
	               CASE WHEN fp.img = ''       THEN '/imgs/op/jyunbi.gif' ELSE fp.img END AS img,
	               onair.chat_mode,
	               CASE WHEN fp.mic = 1        THEN '1'                   ELSE '0'    END AS mic,
	               fp.clip,
	               onair.machiawase_flg
	          FROM female_member  fm,
	               female_profile fp LEFT OUTER JOIN onair
	                                              ON fp.owner_cd = onair.owner_cd
	                                             AND fp.user_id  = onair.user_id
	         WHERE fm.owner_cd = ?
	           AND fm.owner_cd = fp.owner_cd
	           AND fm.user_id  = ?
	           AND fm.user_id  = fp.user_id";

	$data = array($ownerCd, $_POST['user_id']);
	

	$cache_file = "../data/performer/pIconView_meeting_" . $_POST['user_id'] . ".cache";
	if (file_exists($cache_file) && (filemtime($cache_file) > (time() - 20 ))) {
	  $row = unserialize(file_get_contents($cache_file));
	  $data['nick_name'] = $row[0];
	  $data['stat']      = $row[1];
	  $data['icon_flg']  = $row[2];
	  $data['face']      = $row[3];
	  $data['chat_mode'] = $row[4];
	  $data['mike_flg']  = $row[5];
	  $data['clip']      = $row[6];
	  $data['machiawase_flg'] = $row[7];
	} else {
	$result = $dbMaster->query($sql, $data);
	if(DB::isError($result)){
		err_proc($result->getMessage());
	}

	if($row = $result->fetchRow()){
		$data['nick_name'] = $row[0];
		$data['stat'] = $row[1];
		$data['icon_flg'] = $row[2];
		$data['face'] = $row[3];
		$data['chat_mode'] = $row[4];
		$data['mike_flg'] = $row[5];
		$data['clip']      = $row[6];
		$data['machiawase_flg'] = $row[7];
    		
    		$fp = fopen($cache_file, "w");
    		fwrite($fp, serialize($row));
    		fclose($fp);
    	}    	    
	}

	$cn = stripslashes( addslashes(mb_strimwidth($data['nick_name'], 0, 14, "") ) );
	$st = $ctop[ $data['stat'] ][ $data['chat_mode'] ];
	$cs = $css[ $data['stat'] ][ $data['chat_mode'] ];
	
	if($data['machiawase_flg'] == 1) {
	  $st = $cs = 'machi2h';
	}

	$retval = '<ul class="'.$cs.' gB">'."\n";
	$retval .= '	<li class="tagP">'."\n";
	$retval .= '		<img src="/c/d/images/common/g/'.$st.'.gif" alt="'.$cn.'" width="89" height="27" />';

	switch ($data['icon_flg']) {
		case "1":
			$retval .= '<img src="/c/d/images/common/g/new.gif" width="27" height="27" />'."\n";
			break;
		case "2":
			$retval .= '<img src="/c/d/images/common/g/debut.gif" width="27" height="27" />'."\n";
			break;
		case "3":
			$retval .= '<img src="/c/d/images/common/g/fine.gif" width="27" height="27" />'."\n";
			break;
		case "4":
			$retval .= '<img src="/c/d/images/common/g/fine_new.gif" width="27" height="27" />'."\n";
			break;
		case "5":
			$retval .= '<img src="/c/d/images/common/g/fine_debut.gif" width="27" height="27" />'."\n";
			break;
		case "7":
			$retval .= '<img src="/c/d/images/common/g/fine_check.gif" width="27" height="27" />'."\n";
			break;
		case "8":
			$retval .= '<img src="/c/d/images/common/g/checkmark.gif" width="27" height="27" />'."\n";
			break;
	}

	$retval .= '	</li>'."\n";
	$retval .= '<a href="/performer/edit_info/operator_edit_photo.php" width="120px" height="90px" style="display:block;">';

	if(strcmp($data['face'], "/imgs/op/jyunbi.gif") == 0){
//		$retval .= '	<a href="/performer/edit_info/operator_edit_photo.php" width="120px" height="90px" style="display:block;"><li class="pic" style="background-image:url(/imgs/op/jyunbi.gif);"></li></a>'."\n";
		$retval .= '<li class="pic" style="background-image:url(/imgs/op/jyunbi.gif);"></li></a>'."\n";
	} else {
//		$retval .= '	<a href="/performer/edit_info/operator_edit_photo.php" width="120px" height="90px" style="display:block;"><li class="pic" style="background-image:url(/imgs/op/120x90/'.$data['face'].');"></li></a>'."\n";
		if( $data['clip'] ){
			$retval .= "<li class='pic' style='text-align:center;'>";
// ----------------------------------------------------------------------------
// 横長になるのを防ぐ 2013-06-27
//			$retval .= "<img src='/imgs/op/120x90/{$data['face']}' style='width:120px;height:90px;' />";
			$retval .= "<img src='/imgs/op/120x90/{$data['face']}' style='height:90px;' />";
// ----------------------------------------------------------------------------
			$retval .= "</li></a>\n";
		}else{
			$retval .= '<li class="pic" style="background-image:url(/imgs/op/120x90/'.$data['face'].');"></li></a>'."\n";
		}
	}

	$retval .= '	<li class="name">'.$data['nick_name']."\n";
	$retval .= '		<div class="mail">'."\n";

	if($data['mike_flg'] == 1){
		$retval .= '		<img src="/c/d/images/common/g/thumbvoice.gif" alt="voice" width="17" height="17" /><a href="/performer/message/inbox.php"><img src="/c/d/images/common/g/thumb01mail.gif" width="17" height="17" /></a>'."\n";
	} else {
		$retval .= '<a href="/performer/message/inbox.php"><img src="/c/d/images/common/g/thumb01mail.gif" width="17" height="17" /></a>'."\n";
	}

	echo $retval;

?>
