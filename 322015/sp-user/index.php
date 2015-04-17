<?php
require_once 'Owner.inc';
require_once 'common_proc.inc';
require_once 'db_con.inc';
require_once 'sp/boy_sp_top.inc';
require_once 'sp/tmpl2.class_ex.inc';
require_once 'sp/common_proc.inc';

require_once 'blog/common_blog.inc';
require_once 'blog/articleFunc.inc';

	//セッションの復元
//	session_start();
	//広告コード
	if(isset($_GET)){
		foreach($_GET as $key => $value){
			if($key != "m"){
				setcookie ("Advc", "$key",time()+30*24*60*60, "/");
			}
			if ($key=='afid') {
				setcookie ("preaf_afid", "$value",time()+30*24*60*60, "/");
			}
		}
	}

	$tmpl = new Tmpl22($sp_tmpl_dir . "index.html");

	// all allowed.
	$tmpl->assign('show_applisquare', 'defined');
	
	$tmpl->assign ( "date_now", time() );

	if(isset($_SESSION['stat']) && ($_SESSION['stat'] == "boyslogin" || $_SESSION['stat'] == "boyslogin_buy" || $_SESSION['stat'] == "boys_login")){
		$tmpl->assign("login_disp","");
		$tmpl->assign ( "user_id", $_SESSION ['user_id'] );
		// L-チャージキャンペーン用
		$campaign_banner_path = "image/banner/app_mp.png";
		if ( isPaidByMethodFlag( $dbSlave, $_SESSION ['user_id'], 95 ) == false) {
			$campaign_banner_path = "image/banner/app_mp_2times.png";
		}
        $tmpl->assign ( "campaign_banner", $campaign_banner_path );
	}
	else{
		$tmpl->assign("no_login_disp","");
	}

	$db = conDB(getRandomSlave());
	make_blog_content($db);


	$gList = getList($db, $ownerCd);
	$gListHtml = getListHtml($gList);
	$tmpl->assign("girlList", $gListHtml);

//	$tmpl->assign("is_top_page","");
	$tmpl->flush();
//	echo $_SERVER['SERVER_ADDR'];
	exit;


function make_blog_content($dbSlave, $limit = 5) {
	global $tmpl, $blogImgUrl;

	$dbSlave->setFetchMode(DB_FETCHMODE_ASSOC);
	$sql = ' SELECT blog_article.id, blog_article.title, blog_article.view_cnt, blog_article.img_path, blog_article.body, blog_article.cre_id, blog_article.cre_date,' .
			' female_profile.user_id, female_profile.nick_name, female_profile.img' .
			' FROM blog_article' .
			' INNER JOIN female_member ON female_member.user_id=blog_article.cre_id AND (female_member.stat<>6 AND female_member.stat<>7 AND female_member.stat<>9)' .
			' INNER JOIN female_profile ON female_member.user_id=female_profile.user_id' .
			' WHERE blog_article.is_viewable = ?' .
			' ORDER BY blog_article.cre_date DESC' .
			' LIMIT ' . $limit;
	$sth = $dbSlave->prepare($sql);
	$result = $dbSlave->execute($sth, array(1));
	if (DB::isError($result)) {
		echo $sql;
		echo $result->getMessage();
		return;
	}

	$tmpl->loopset('blog_loop');
	while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
		$performerImg = (empty($row['img'])) ? '' : "/imgs/op/120x90/{$row['img']}";
		if(!empty($row['img_path'])) {
			$blogImg = $blogImgUrl . $row['img_path'];
		}
		elseif($thumb = getYTThumb($row['body'])) {
			$blogImg = $thumb;
		}
		else {
			$blogImg = $performerImg;
		}
		$toTime = strtotime($row['cre_date']);
		if ($toTime >= strtotime('-1 day')) {
			$tmpl->assign('blog_is_new', 1);
		}

		$blogId = $row['id'];

		$tmpl->assign('blog_id', $blogId);
		$tmpl->assign('blog_title', $row['title']);
		$tmpl->assign('blog_body', removeYT($row['body']));
		$tmpl->assign('blog_cre_date', date('Y-m-d H:i', $toTime));

		$tmpl->assign('blog_view_cnt', $row['view_cnt']);
		$tmpl->assign('blog_like_cnt', countLike($blogId));
		$tmpl->assign('blog_comment_cnt', countComment($blogId));

		$tmpl->assign('blog_img', $blogImg);

		$tmpl->loopnext();
	}
	$tmpl->loopset('');
}

function getListHtml($girlList){

	$html = "";
	if($girlList['onairNode'] == "notNode"){
		$html = '<ul id="pafo_big">オンエアー中のパフォーマーがいません。</ul>';
		return $html;
	}


	$html .= '<ul id="pafo_big">';

	foreach($girlList['onairNode'] as $key => $val ){
		$onairSt = "";
		$onairSt = '<div class="online"><a href="#"><img src="image/icon/performer_online.png"></a></div>';

		if($val['st'] =="2st" || $val['st'] == "pat"){
			//待機中
			$onairSt = '<div class="online-banner banner-style">Online</div>';
			if($val['ma'] == 1) {
				$onairSt = '<div class="status-position">' . $onairSt . '<div class="machiawase-banner banner-style">待ち合わせ</div></div><!-- / .status-position -->';
			}else {
				$onairSt = '<div class="status-position">' . $onairSt . '</div><!-- / .status-position -->';
			}
			//$onairSt = $onairSt . '</div><!-- / .status-wrap --></div>';
		}else if($val['st'] == "2sc" || $val['st'] == "pac"){
			//チャット中
			$onairSt = '<div class="status-position"><div class="chat-banner banner-style">2shot</div></div>';
		}else{
			//オフライン
			$onairSt = '';
		}
		$micIcon = "";
		$micIcon = '<div class="mike"></div>';
		if($val['vo'] == 1){
			$micIcon = '<div class="mike"><img src="image/icon/performer_mike.png"></div>';
		}

		$debut = '';
		if($val['nw'] == 2){
			$debut = '<div class="debut"><img src="image/icon/performer_debut.png"></div>';
		}

		$ru = "";
		if($val['ru'] != ""){
			$ru = '&ru=' . $val['ru'];
		}

		$html .= '<li class="pafo_big_img">';
		$html .= '<div class="image_area">';
		$html .= '<div class="status-wrap"><a href="shicho.php?id='. $val['hs'] . $ru . '"><img src="./image/skeleton.png" style="background: url(http://c.macherie.tv/imgs/op/320x240/' . $val['ph'] . '); background-position:center center; background-repeat:no-repeat; -o-background-size: contain; -moz-background-size: contain; -webkit-background-size: contain; background-size: contain;"></a>';
		$html .= $onairSt;
		$html .= '</div>';
		$html .= $debut;
		$html .= '<p class="area"><em>' . $val['ar'] . '</em></p><p class="age"><em>' . $val['ag'] . '</em></p>';
		$html .= $micIcon;
		$html .= '</li>';

	}

	$html .= '</ul>';

	return $html;


}

function getList($dbSlave, $ownerCd, $dispryMode = 'tile', $onairMode = 0){

	//require_once('Owner.inc');
	//require_once('common_proc.inc');
	//require_once('db_con.inc');
	//require_once('JSON.php');
	require_once('FormObject.inc');

	$fobj = new FormObject("プロフィール表示");

	//$dispryMode = "tile";
	$onairMode = 0;
	$whereStr = "";
	//if(!empty($_POST['l']) && $_POST['l'] == "list"){
	//	$dispryMode = "list";
	//}

	//if(!empty($_POST['o']) && $_POST['o'] == 1){
	//	$onairMode = 1;
	//}

	if($onairMode == 1){
		$whereStr = " AND onair.stat = 1 ";
	}

	$js_obj['onairNode'] = array();
	$js_obj['onairNodeCont'] = 0;

	$sql = "";
	$sql = "SELECT
			female_profile.hash AS f_hash,
			female_profile.nick_name AS f_nick,
			female_profile.img AS f_img,
			female_profile.mic AS f_mic,
			female_profile.new_face AS f_new,
			female_profile.area AS f_area,
			female_profile.age AS f_age,
			female_profile.height AS f_height,
			female_profile.bust AS f_bust,
			female_profile.type AS f_type,
			female_profile.blood_type AS f_blood_type,
			female_profile.comment1 AS f_comment,
			female_member.fine_flg AS f_fine,
			onair.stat AS o_stat,
			onair.chat_mode AS o_mode,
			onair.mizugi_flg AS o_event,
			(UNIX_TIMESTAMP(now()) - UNIX_TIMESTAMP(onair.`cre_date`)) AS taiki_sec,
			onair.machiawase_flg AS o_machiawase
		FROM
			onair
		LEFT JOIN
			female_profile
		ON
			onair.owner_cd = female_profile.owner_cd and
			onair.user_id = female_profile.user_id
		LEFT JOIN
			female_member
		ON
			onair.owner_cd = female_member.owner_cd and
			onair.user_id = female_member.user_id
		WHERE
			onair.owner_cd = {$ownerCd} AND
			onair.chat_mode = 0 AND
			onair.mizugi_flg = 0
			{$whereStr} AND
			(onair.start_date IS NULL OR onair.start_date < now()) AND
			female_member.stat = 1 AND
			female_member.flv2 = 0 AND
			female_profile.nick_name <> '' AND
			female_profile.nick_name IS NOT NULL AND
			female_profile.img IS NOT NULL
		ORDER BY o_stat, o_machiawase, rand()";


	$st[""][""] = "off";	//オフライン
	$st[1][0] = "2st";		//２ショット待機中
	$st[1][1] = "pat";		//パーティー待機中
	$st[2][0] = "2sc";		//２ショット中
	$st[2][1] = "pac";		//パーティー中

	$sth = $dbSlave->prepare($sql);
	$dbSlave->setFetchMode(DB_FETCHMODE_ASSOC);
	$result = $dbSlave->execute($sth);
	$dbSlave->setFetchMode(DB_FETCHMODE_ORDERED);
	$online_num = 0;
	if($result->numRows() < 1){
		$js_obj['onairNode'] = "notNode";
	}
	else{
		while($row = $result->fetchRow()){
			$data = array();
			if($dispryMode == "tile"){
				$data['hs'] = $row['f_hash'];												//女性ハッシュ
				$data['st'] = $st[$row['o_stat']][$row['o_mode']];							//オンエアー状態
				$data['ma'] = $row['o_machiawase'];				                            //
				$data['vo'] = ($row['f_mic'] == 1) ? "1" : "0";								//音声
				$data['nw'] = $row['f_new'];												//新人Flg
				$data['ph'] = empty($row['f_img']) ? "imgs/op/jyunbi.gif" : $row['f_img'];	//画像
				//$data['ar'] = mb_convert_encoding($fobj->getLabelValue('area',$row['f_area']),'UTF-8','eucJP-win');
				//$data['ag'] = mb_convert_encoding($fobj->getLabelValue('age',$row['f_age']),'UTF-8','eucJP-win');
				$data['ar'] = $fobj->getLabelValue('area',$row['f_area']);
				$data['ag'] = $fobj->getLabelValue('age',$row['f_age']);
			}
			else{
				// スペースが画面上に正常に表示されるように対応
				$row['f_nick'] = str_replace(" ", "&nbsp;", $row['f_nick']);
				$data['hs'] = $row['f_hash'];												//女性ハッシュ
				$data['cn'] = mb_convert_encoding($row['f_nick'],'UTF-8','eucJP-win');		//ニックネーム
				$data['st'] = $st[$row['o_stat']][$row['o_mode']];							//オンエアー状態
				$data['ma'] = $row['o_machiawase'];				                            //
				$data['vo'] = ($row['f_mic'] == 1) ? "1" : "0";								//音声
				$data['nw'] = $row['f_new'];												//新人Flg
				$data['ph'] = empty($row['f_img']) ? "imgs/op/jyunbi.gif" : $row['f_img'];	//画像
				//プロフィール項目
				$data['ar'] = $fobj->getLabelValue('area',$row['f_area']);
				$data['ag'] = $fobj->getLabelValue('age',$row['f_age']);
				$data['hi'] = $fobj->getLabelValue('height',$row['f_height']);
				$data['bu'] = $fobj->getLabelValue('bust',$row['f_bust']);
				$data['ty'] = $fobj->getLabelValue('type',$row['f_type']);
				$data['bt'] = $fobj->getLabelValue('blood_type',$row['f_blood_type']);
				$row['f_comment'] = nl2br(strip_tags($row['f_comment']));

				$moto = 0;
				$mo_cnt = mb_strlen($row['f_comment']);
				if($mo_cnt > 100){
					$moto = 1;
				}
				$data['moto'] = $moto;

				$data['cm_more'] = $row['f_comment'];
				$data['cm'] = mb_strimwidth($row['f_comment'], 0, 100, '...');

			}

			$data['ds'] = 1;
			$data['ru'] = urlencode("index.php?");

			$js_obj['onairNode'][$online_num] = $data;
			$online_num++;
			//テスト用です.
		//	for($testI=0; $testI<200; $testI++){
		//		$js_obj['onairNode'][$online_num] = $data;
		//		$online_num++;
		//	}
		}
	}

	$js_obj['onairNodeCont'] = $online_num;

//	$json = new Services_JSON();
//	$js = $json->encode($js_obj);

	return $js_obj;

}
