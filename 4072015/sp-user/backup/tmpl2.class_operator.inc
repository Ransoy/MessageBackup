<?php
/*
 * @Title：パフォーマー管理画面用コンポーネント
 * @Description：ヘッダーと左メニューフッター辺りがここで処理される。
 * @testしてみる。
 * @Author：Satodate
 */

require_once 'tmpl2.class.inc';
require_once 'mailBOXMaintenance.inc';
require_once 'operator/ontime.class.inc';
require_once 'operator/eventRoom.class.inc';
require_once 'operator/profile.class.inc';
require_once 'operator/payment.class.inc';
require_once 'operator/mobile.class.inc';
require_once 'blog/BlogArticleDB.php';
require_once 'CommonDb.php';
require_once 'message/MessageHelper.php';

class Tmpl23 extends Tmpl2
{
	var $nickName;
	var $blog;
	var $hasNewBlogActivity;
	var $ontime;
	var $eventBtn;
	var $linkPath;
	var $profile;
	var $eventRoom;
	var $payment;
	var $mobile;
	var $linkPath_macherie;

	#---------------------------------------------------------
	#	コンストラクタ
	#---------------------------------------------------------
	function Tmpl23 ($filename = "./index.html" , $icode = "" , $ocode = "SJIS")
	{
		$this->fname	= $filename ;
		$this->mainitem = array() ;
		$this->loopitem = array() ;
		$this->loopname = "" ;
		$this->loopone  = "" ;
		$this->linkPath = '/'.OP_DIR;
		$this->linkPath_macherie = MACHERIE_ROOT;

		if( $icode == "" )
		{
			$this->kconv = 0;
			$this->kcin  = "EUC-JP";
			$this->kcout = "SJIS";
		}
		else
		{
			$this->kconv = 1 ;
			$this->kcin  = $icode;
			$this->kcout = $ocode;
		}

		$this->getOperator();
		$this->getMainImage();

		$this->hasNewBlogActivity = false;
	}

	function setFormInstance(&$obj){
		$this->formObj = $obj;
	}

	function setFormObjectItems(){
		$object_Name = $this->formObj->getObjectName();
		for($i=0;$i<count($object_Name);$i++){
			parent::assign($object_Name[$i]. "_l",$this->formObj->getLabel($object_Name[$i]). "　");
			parent::assign($object_Name[$i],$this->formObj->getItem($object_Name[$i]));
		}
	}
	function setFormObjectValues(){
		$object_Name = $this->formObj->getObjectName();
		for($i=0;$i<count($object_Name);$i++){
			parent::assign($object_Name[$i]. "_l",$this->formObj->getLabel($object_Name[$i]) . "　");
			parent::assign($object_Name[$i],$this->formObj->getLabelValue($object_Name[$i]). "　");
		}
	}
	#---------------------------------------------------------
	#	パフォーマープロフィール
	#---------------------------------------------------------
	function getOperator ()
	{
		$profile = new operatorProfile();
		$this->profile = $profile->data;
	}

	#---------------------------------------------------------
	#	ニックネーム取得
	#---------------------------------------------------------
	function getNickName ()
	{
		if($_SESSION['nick_name'] == '')
		{
			$this->nickName = '新人';
		} else {

			$this->nickName = $_SESSION['nick_name'];
		}
	}

	#---------------------------------------------------------
	#	ブログ
	#---------------------------------------------------------
	function getBlog () {
		$baWithNewComment = BlogArticleDB::getBlogArticleIdWithLatestComment($_SESSION['user_id']);
// 		$baWithNewLike = BlogArticleDB::getBlogArticleIdWithLatestLike($_SESSION['user_id']);

		$this->blog = <<<EOM
			<div class="utility_blog_area">
				<h4 class="ttl">ブログ</h4>
				<div class="utility_blog_area_inner">
					<!-- ▼未読のコメント or いいね ある場合 -->
EOM;
		if ($baWithNewComment || $baWithNewLike) {
			$this->hasNewBlogActivity = true;
			$this->blog .= '<ul class="links_new">';
			if ($baWithNewComment) {
				$this->blog .= '<li class="new"><a href="' . $this->linkPath . 'blog/article_detail.php?id=' . $baWithNewComment . '">コメントされました</a></li>';
			}
// 			if ($baWithNewLike) {
// 				$this->blog .= '<li class="new"><a href="' . $this->linkPath . 'blog/article_detail.php?id=' . $baWithNewLike . '">いいねされました</a></li>';
// 			}

			$this->blog .= '</ul>';
		}
		else {
			$this->hasNewBlogActivity = false;
		}
EOM;
		$this->blog .= <<<EOM
					<!-- ▲未読のコメント or いいね ある場合 -->
					<ul class="links">
						<li class="dot"><a href="{$this->linkPath}blog/index.php">ブログを投稿する</a></li>
						<li class="dot"><a href="{$this->linkPath}blog/about.php">スマホ投稿OK！ブログをはじめよう！</a></li>
					</ul>
				</div>
			</div>
EOM;
	}

	#---------------------------------------------------------
	#	オンタイム情報取得
	#---------------------------------------------------------
	function getOnTime ()
	{
		$this->ontime = new ontimeInformation();
		$male_num = $this->ontime->getUser3Hour();

		if($this->ontime->getStats($_SESSION['user_id']))
		{
			$this->ontime = <<<EOM
				<div id="OntimeInformation" class="cmnSection">
					<ul>
						<li>{$this->ontime->data['operator']}人</li>
						<li>{$this->ontime->data['user']}人</li>
					</ul>
					<p>過去３時間以内にログインした会員様の数は</p>
					<p class="result"><strong>{$male_num}</strong>人</p>
					<p><a href="{$this->linkPath}male_search.php">アプローチメールを会員様サーチから送ろう！</a></p>
				</div>
EOM;
		}
		else
		{
			$this->ontime = <<<EOM
				<div id="OntimeInformationShort" class="cmnSection">
					<p class="top">過去３時間以内にログインした会員様の数は</p>
					<p class="result"><strong>{$male_num}</strong>人</p>
					<p><a href="{$this->linkPath}male_search.php">感謝の気持ちを会員様サーチから送ろう！</a></p>
				</div>
EOM;
		}
	}

	#---------------------------------------------------------
	#	報酬＆ポイント
	#---------------------------------------------------------
	function getPayment ()
	{
		$pay = new paymentOperator();
		$this->payment = $pay->data;
	}

	#---------------------------------------------------------
	#	イベントルームボタン
	#---------------------------------------------------------
	function getEventBtn ()
	{
		$opFlg = '';

		if($this->profile['agent_code'] != "")
		{
			$opFlg = "_dairi";
		}

		if($this->profile['world_flg'] == 1)
		{
			$opFlg = '_world';
		}

		$eventClass = new eventRoom($opFlg, $this->mobile['flg']);
		$this->eventBtn = $eventClass->getEventBtn();
	}

	#---------------------------------------------------------
	#	メイン画像取得
	#---------------------------------------------------------
	function getMainImage ()
	{
		if($this->profile['img']  == '')
		{
			$this->profile['img'] = $this->linkPath.'images/imgTWbGcO.jpg';
		}
		else
		{
			$this->profile['img'] = '/imgs/op/120x90/'.$this->profile['img'];
		}
	}
	#---------------------------------------------------------
	#	モバイルメール取得
	#---------------------------------------------------------
	function checkMobileMailBox ()
	{
		$mobile = new operatorMobile ();
		$mobile->getNewMail ();
		$this->mobile['count'] = $mobile->mail;
		$mobile->getRegistrationFlg ();
		$this->mobile['flg'] = $mobile->registration;
	}

	#---------------------------------------------------------
	#	未読メールチェック 2010/11/30
	#---------------------------------------------------------
	function checkMailBox ()
	{
		global $dbSlave , $ownerCd;
		if( !mailBOXMaintenanceFemale() )
		{
			$sql = '
				SELECT count(*)
				FROM female_mailbox
				LEFT JOIN male_member
				ON female_mailbox.owner_cd = male_member.owner_cd
				AND female_mailbox.from_user_id = male_member.user_id
				WHERE female_mailbox.owner_cd = ?
				AND female_mailbox.user_id = ?
				AND female_mailbox.stat2 = 1
				AND male_member.stat = 1
				AND female_mailbox.cre_date <= now()
			';
			$sth = $dbSlave->prepare($sql);
			$con = array($ownerCd,$_SESSION['user_id']);
			$result = $dbSlave->execute($sth,$con);
			if(DB::isError($result))
			{
				err_proc($result->getMessage());
			}
			$row = $result->fetchRow();

		}
		else
		{
			$row[0] = 0;
		}

		return $row[0];
	}

	#---------------------------------------------------------
	#	チャット待ち人数チェック
	#---------------------------------------------------------
	function checkChatWait ()
	{
		global $dbSlave;

		$sql = "SELECT COUNT(*)
				FROM (
					SELECT user_id,MAX(cre_date)
					FROM `call`
					WHERE female_user_id = ?
					AND cre_date >= now() - interval 3 day
					AND female_show_flg = 0
					GROUP BY user_id,disp_nick_name_flg
				)
				`call` INNER JOIN male_member
				ON male_member.user_id = call.user_id
				AND male_member.stat = 1
		";
		$sth = $dbSlave->prepare($sql);
		$data = array($_SESSION['user_id']);
		$result = $dbSlave->execute($sth, $data);

		if(DB::isError($result)){
			err_proc($result->getMessage());
		}

		if($row = $result->fetchRow()){
			$num = $row[0];
		} else {
			$num = 0;
		}

		return $num;
	}

	#---------------------------------------------------------
	#	スクリプトヘッダー構築
	#---------------------------------------------------------
	function getScriptHeader ()
	{

		$html .= <<<EOM
		<script type="text/javascript" src="{$this->linkPath}js/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="{$this->linkPath}js/jquery-ui-tabs-1.8.16.min.js"></script>
		<script type="text/javascript" src="{$this->linkPath}js/jquery.curvycorners.min.js"></script>
		<script type="text/javascript" src="{$this->linkPath}js/common.js?v=2"></script>
		<script type="text/javascript" src="{$this->linkPath}js/pop_window.js?v=1"></script>
		<script type="text/javascript">
			var _gaq = _gaq || [];
		  	_gaq.push(['_setAccount', 'UA-13149951-1']);
		  	_gaq.push(['_trackPageview']);
			(function() {
				var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  	})();
		</script>
EOM;

		return $html;

	}

	#---------------------------------------------------------
	#	ヘッダー構築
	#---------------------------------------------------------
	function globalHeader ()
	{
		global $dbSlave;
		$this->getNickName();

		/*
		 *面接日から2週間以内のパフォの管理画面TOPにリンクをつける。
		 *リンクにボーナス獲得までのポイント数とボーナス金額を表示
		 */

		$sql = 'SELECT * FROM `campaign_newgirls_bonus` WHERE owner_cd = ? AND user_id = ? LIMIT 1;';
		$end = $dbSlave->getRow($sql, array(1,$_SESSION['user_id']), DB_FETCHMODE_ASSOC);
		if ($end) {
			$bonus="";
		} else {

			// 面接日取得
			$cre_date1 = "select `mensetsu_date` from `female_member` where `OWNER_CD`=? and `USER_ID`=?";
	        $row = $dbSlave->getRow($cre_date1, array(1,$_SESSION['user_id']), DB_FETCHMODE_ASSOC);

			// 獲得ポイント取得
			$sum_point = "select sum(`POINT`-`POINT_OLD`) as pointw from `female_point_log` where `OWNER_CD`=? and `USER_ID`=? AND (`POINT`-`POINT_OLD`) > 0  AND upd_mode IN (11,16,13,12,14,17,40,51)";
			$row2= $dbSlave->getRow($sum_point, array(1,$_SESSION['user_id']), DB_FETCHMODE_ASSOC);

			$bonus="";

			if($row['mensetsu_date']>=date('Y-m-d H:i:s',strtotime("-7days 00:00:00"))){
				if($row2['pointw']>=1000){
					$bonus="<a href=\"/performer/campaign/debut_bonus/\" target=\"_blank\">1000ポイント達成!! 1万円GET !!</a>";
				}else{
					$bonus="<a href=\"/performer/campaign/debut_bonus/\" target=\"_blank\">あと".(1000-$row2['pointw'])."pt獲得で1万円ボーナス</a>";
				}
			}
			// 10日以内
			elseif($row['mensetsu_date']>=date('Y-m-d H:i:s',strtotime("-10days 00:00:00"))){
				if($row2['pointw']>=1000){
					$bonus="<a href=\"/performer/campaign/debut_bonus/\" target=\"_blank\">1000pt達成!! 3千円をもらう！</a>";
				}else{
					$bonus="<a href=\"/performer/campaign/debut_bonus/\" target=\"_blank\">あと".(1000-$row2['pointw'])."pt獲得で3千円ボーナス！</a>";
				}
			}
			// 14日以内
			elseif($row['mensetsu_date']>=date('Y-m-d H:i:s',strtotime("-14days 00:00:00"))){
				if($row2['pointw']>=1000){
					$bonus="<a href=\"/performer/campaign/debut_bonus/\" target=\"_blank\">1000pt獲得!! 千円をもらう！</a>";
				}else{
					$bonus="<a href=\"/performer/campaign/debut_bonus/\" target=\"_blank\">あと".(1000-$row2['pointw'])."pt獲得で千円ボーナス！</a>";
				}
			}
		}

		$html = <<<EOM
			<div id="Header">

				<h1><a href="{$this->linkPath}">Macherie</a></h1>

EOM;
		$agent = getenv( "HTTP_USER_AGENT" );
		if(!$this->profile['agent_code'])
		{
			if(!strstr($agent,"Chrome") && !strstr($agent,"Safari") && !strstr($agent,"Firefox")){
			$html .= <<<EOM
					<div style="position:relative;top:10px;left:45px;"><p style="width:300px; height:30px; float:left;"><a href="./first_manual/" target="_blank"><img src="http://{$_SERVER['HTTP_HOST']}/performer/img/manual.jpg" onmouseover="this.src='http://{$_SERVER['HTTP_HOST']}/performer/img/manual_on.jpg'" onmouseout="this.src='http://{$_SERVER['HTTP_HOST']}/performer/img/manual.jpg'" /></a><div style="position: relative;right: 415px;top: 36px;font-weight: bold;font-size: 15px;>$bonus</div></p>;
					<a href="http://{$_SERVER['HTTP_HOST']}/cs_support/" target="_blank"><img src="{$this->linkPath}img/banner/mail.png" >メール問い合わせ</a>&nbsp;&nbsp;
					<script type="text/javascript" src="/performer/js/favorite.js"></script>&nbsp;&nbsp;<a href="/operator"><img src="{$this->linkPath}img/banner/old_manage.gif" onmouseover="this.src='{$this->linkPath}img/banner/old_manage_on.gif'" onmouseout="this.src='{$this->linkPath}img/banner/old_manage.gif'" /></a></div>
EOM;
				//
			} else {
			$html .= <<<EOM
					<div style="position:relative;top:10px;left:80px;"><p style="width:300px; height:30px; float:left;"><a href="./first_manual/" target="_blank"><img src="http://{$_SERVER['HTTP_HOST']}/performer/img/manual.jpg" onmouseover="this.src='http://{$_SERVER['HTTP_HOST']}/performer/img/manual_on.jpg'" onmouseout="this.src='http://{$_SERVER['HTTP_HOST']}/performer/img/manual.jpg'" /></a><div style="position: relative;right: 415px;top: 36px;font-weight: bold;font-size: 15px;">$bonus</div></p>
					<a href="{$this->linkPath}support/support_tel.php"><img src="{$this->linkPath}img/banner/phone.png" >電話問い合わせ</a>&nbsp;&nbsp;
					<a href="http://{$_SERVER['HTTP_HOST']}/cs_support/" target="_blank"><img src="{$this->linkPath}img/banner/mail.png" >メール問い合わせ</a>&nbsp;&nbsp;<a href="/operator"><img src="{$this->linkPath}img/banner/old_manage.gif" onmouseover="this.src='{$this->linkPath}img/banner/old_manage_on.gif'" onmouseout="this.src='{$this->linkPath}img/banner/old_manage.gif'" /></a></div>
EOM;
				//<a href="/operator"><img src="{$this->linkPath}img/banner/old_manage.gif" onmouseover="this.src='{$this->linkPath}img/banner/old_manage_on.gif'" onmouseout="this.src='{$this->linkPath}img/banner/old_manage.gif'" /></a>
			}
		} /* else {
			if(!strstr($agent,"Chrome") && !strstr($agent,"Safari") && !strstr($agent,"Firefox")){
			$html .= <<<EOM
					<div style="position:relative;top:10px;left:275px;">
					<script type="text/javascript" src="/performer/js/favorite.js"></script>&nbsp;&nbsp;<a href="/operator"><img src="{$this->linkPath}img/banner/old_manage.gif" onmouseover="this.src='{$this->linkPath}img/banner/old_manage_on.gif'" onmouseout="this.src='{$this->linkPath}img/banner/old_manage.gif'" /></a></div>
EOM;
				//<a href="/operator"><img src="{$this->linkPath}img/banner/old_manage.gif" onmouseover="this.src='{$this->linkPath}img/banner/old_manage_on.gif'" onmouseout="this.src='{$this->linkPath}img/banner/old_manage.gif'" /></a>
			} else {
			$html .= <<<EOM
					<div style="position:relative;top:10px;left:340px;">&nbsp;&nbsp;<a href="/operator"><img src="{$this->linkPath}img/banner/old_manage.gif" onmouseover="this.src='{$this->linkPath}img/banner/old_manage_on.gif'" onmouseout="this.src='{$this->linkPath}img/banner/old_manage.gif'" /></a></div>
EOM;
				//<a href="/operator"><img src="{$this->linkPath}img/banner/old_manage.gif" onmouseover="this.src='{$this->linkPath}img/banner/old_manage_on.gif'" onmouseout="this.src='{$this->linkPath}img/banner/old_manage.gif'" /></a>
			}
		} */
		$html .= <<<EOM
				<div id="mail_icon"><a href="{$this->linkPath}mail/mailbox_receive.php?midokuL" class="newWindow"><img src="/performer/img/banner/icon_mail_new.png" ></a></div>
				<dl id="welcome" class="clearfix">
					<dt>お疲れ様です。<strong>{$this->nickName}</strong>さん</dt>
EOM;
					if(!$this->profile['agent_code'])
					{
						$html .= <<<EOM
					<dt><a href="http://www.macherie.tv/" target="_blank"><img src="{$this->linkPath}img/banner/btn_macherie.gif"></a><dt>

					<dd>
						<form method="post" action="{$this->linkPath}logout.php" id="logoutArea" name="logoutArea">
EOM;
					} else {
						$html .= <<<EOM
					<dd>
						<form method="post" action="{$this->linkPath}logout.php?dairi" id="logoutArea" name="logoutArea">
EOM;
					}
		$html .= <<<EOM
							<a href="javascript:logoutF();">ログアウト</a>
							<input type="hidden" name="logout" value="1" />
						</form>
					</dd>
				</dl>

				<dl id="Navigation" class="clearfix">
					<dd class="start"><a href="{$this->linkPath}">HOME</a></dd>
					<dd>
						<strong>設定変更</strong>
						<ul id="naviSetting" class="menuChild">
EOM;

if(!$this->profile['agent_code'])
{
		$html .= <<<EOM

							<li><a href="{$this->linkPath}edit_info/operator_edit_profile.php">プロフィール設定・変更</a></li>
							<li><a href="{$this->linkPath}edit_info/operator_edit_comment.php">プロフィールコメント変更</a></li>
							<li><a href="{$this->linkPath}edit_info/operator_edit_photo.php">フォトギャラリー設定</a></li>
							<li><a href="{$this->linkPath}edit_info/operator_edit_secretname.php">匿名チャットネームの設定</a></li>
							<li><a href="{$this->linkPath}edit_info/operator_edit_skin.php">チャット画面背景変更</a></li>
							<li><a href="{$this->linkPath}edit_info/operator_edit_myinfo.php">個人情報設定・変更</a></li>
EOM;
}else{

		$html .= <<<EOM

							<li><a href="{$this->linkPath}edit_info/operator_edit_photo.php">フォトギャラリーの設定</a></li>
							<li><a href="{$this->linkPath}edit_info/operator_edit_comment.php">お客様に一言</a></li>
							<li><a href="{$this->linkPath}edit_info/operator_edit_profile.php">プロフィール設定・変更</a></li>
							<li><a href="{$this->linkPath}edit_info/operator_edit_skin.php">チャット画面背景変更</a></li>
EOM;
}

		$this->hasNewBlogActivity = BlogArticleDB::hasNewActivity($_SESSION['user_id']);
		$newBlogActivityClass = ($this->hasNewBlogActivity) ? 'new' : '';
		$html .= <<<EOM
						</ul>
					</dd>
					<dd>
						<strong>メール BOX</strong>
						<ul id="naviMailbox" class="menuChild">
							<li><a href="{$this->linkPath}mail/mailbox_receive.php?midoku" class="newWindow">未読メール</a></li>
							<li><a href="{$this->linkPath}mail/mailbox_receive.php" class="newWindow">受信BOX</a></li>
						</ul>
					</dd>
					<dd class="{$newBlogActivityClass}">
						<strong>ブログ</strong>
						<ul id="naviBlog" class="menuChild">
							<li><a href="{$this->linkPath}blog/article_list.php">マイブログ</a></li>
							<li><a href="{$this->linkPath}blog/index.php">新規記事投稿</a></li>
							<li><a href="{$this->linkPath}blog/article_list_others.php">みんなのブログ</a></li>
							<li><a href="{$this->linkPath}blog/about.php">スマホ投稿OK！ブログをはじめよう！</a></li>
						</ul>
					</dd>
					<dd>
EOM;
						if(!$this->profile['agent_code'])
						{
							$html .="<strong>報酬について</strong>";
						}
						else
						{
							$html .="<strong>獲得ポイント</strong>";
						}

		$html .= <<<EOM
						<ul id="naviPayment" class="menuChild">
							<li><a href="{$this->linkPath}payment_system/operator_pay_point.php">獲得ポイント状況</a></li>
							<li><a href="{$this->linkPath}payment_system/operator_pay_bonusinfo.php">ボーナス履歴</a></li>
EOM;

						if(!$this->profile['agent_code'])
						{
							$html .= <<<EOM
							<li><a href="{$this->linkPath}payment_system/operator_pay_access.php">アクセス履歴</a></li>
							<!-- <li><a href="{$this->linkPath}payment_system/operator_pay_bonusinfo.php">ボーナス履歴</a></li> -->
							<!-- <li><a href="{$this->linkPath}payment_system/operator_pay_point.php#compensation">報酬体系</a></li> -->
							<li><a href="{$this->linkPath}payment_system/operator_pay_paidinfo.php">精算履歴</a></li>
							<!-- <li><a href="{$this->linkPath}payment_system/operator_pay_account.php">登録口座情報</a></li> -->
							<!-- <li><a href="{$this->linkPath}payment_system/operator_pay_point.php#attention">精算に関しての注意</a></li> -->
EOM;
						}else
						{
							$html .= <<<EOM
							<li><a href="{$this->linkPath}payment_system/operator_pay_point_search.php">獲得ポイント検索</a></li>
EOM;
						}

		$html .= <<<EOM
						</ul>
					</dd>
EOM;

					if(!$this->profile['agent_code'])
					{
		$html .= <<<EOM
					<dd>
						<strong>お仕事について</strong>
						<ul id="naviWork" class="menuChild">
							<!-- <li><a href="{$this->linkPath}howtowork/step1.php">お仕事の始め方</a></li> -->
							<li><a href="{$this->linkPath}manual/faq/?M=list&id=443">待機・チャット画面</a></li>
							<li><a href="{$this->linkPath}campaign/setting/" target="_blank">カメラ・マイク設定</a></li>
							<li><a href="{$this->linkPath}manual/faq/?M=list&id=425">快適にお仕事頂く為に</a></li>
						</ul>
					</dd>
EOM;
					}

					// 一般パフォのみ表示されるように修正 2013-05-06
//					if(!$this->profile['sub_agent_code'])
					if(!$this->profile['sub_agent_code'] && !$this->profile['agent_code'] )
					{
		$html .= <<<EOM
					<dd>
						<strong>マシェリモバイル</strong>
						<ul id="naviMobile" class="menuChild">
							<li><a href="{$this->linkPath}mobile_tel/">マシェリモバイル設定画面</a></li>
							<li><a href="{$this->linkPath}mobile_tel/access.php">携帯版マシェリ お仕事ページへアクセス</a></li>

							<li><a href="http://m-mb.jp/operator.php?M=OP_MAIL_RECEIVEBOX&FROMIDNO={$this->profile['user_id']}&FROMPASS={$this->profile['password']}" class="newWindow">マシェリモバイルメールBOX</a></li>
							<li><a href="{$this->linkPath}manual/mobile_faq/?M=list&id=76">マシェリモバイルマニュアル</a></li>
						</ul>

					</dd>
EOM;
					}
					if($this->profile['agent_code']){
		$html .= <<<EOM
					<dd class="start"><a href="{$this->linkPath}support/calendar_agent.php">イベントスケジュール</a></dd>
					<dd>
EOM;
					}

					if(!$this->profile['agent_code'])
					{
		$html .= <<<EOM

					<dd>
						<strong>ヘルプ</strong>
						<ul id="naviSupport" class="menuChild">
							<!-- <li><a href="{$this->linkPath}support/faq.php">よくある質問(FAQ)</a></li> -->
							<li><a href="#" onclick="org=document.charset;document.charset='UTF-8';document.tofaq.submit();document.charset=org;">よくある質問(FAQ)</a></li>
							<li><a href="http://{$_SERVER['HTTP_HOST']}/cs_support/" target="_blank">サポートセンター問合わせ</a></li>
							<li><a href="{$this->linkPath}support/support_tel.php">電話でのお問い合わせ</a></li>
							<li><a href="{$this->linkPath}support/bbs.php">フォーラム(BBS)</a></li>
							<li><a href="{$this->linkPath}prohibition/">禁止事項</a></li>
							<li><a href="{$this->linkPath}agreement.php">ご利用規約</a></li>
							<li><a href="{$this->linkPath}manual/faq/?M=detail&qid=3989">推奨環境</a></li>
							<li><a href="{$this->linkPath}info/operator_info_black.php">ブラックリスト</a></li>
							<li><a href="{$this->linkPath}support/operator_staff_list.php">スタッフ紹介</a></li>
		</ul>
					</dd>
					<dd class="end"><a href="javascript:allNavi_manage()">全メニュー表示</a></dd>

EOM;
					}

		$html .= <<<EOM
				</dl>

			</div>
EOM;

		return $html;
	}

	#---------------------------------------------------------
	#	ナビメニュー直下のコンテンツ構築
	#---------------------------------------------------------
	function getNaviContents(){

		$html = <<<EOM
			<div id="all_navi" style="display:none">
				<div id="Footer" style="text-align:left">
					<div class="sitemap01" style="padding:10px 0px;">
						<ul>
							<div style="padding:0px 10px;">
							<li><b>設定変更</b></li
							<li><img src="{$this->linkPath}img/banner/pointer.png"><a href="{$this->linkPath}edit_info/operator_edit_profile.php">プロフィール設定・変更</a></li>
							<li><img src="{$this->linkPath}img/banner/pointer.png"><a href="{$this->linkPath}edit_info/operator_edit_comment.php">プロフィールのコメント変更</a></li>
							<li><img src="{$this->linkPath}img/banner/pointer.png"><a href="{$this->linkPath}edit_info/operator_edit_photo.php">フォトギャラリー設定</a></li>
							<li><img src="{$this->linkPath}img/banner/pointer.png"><a href="{$this->linkPath}edit_info/operator_edit_secretname.php">匿名チャットネームの設定</a></li>
							<li><img src="{$this->linkPath}img/banner/pointer.png"><a href="{$this->linkPath}edit_info/operator_edit_photo.php">フォトギャラリー設定・変更</a></li>
							<li><img src="{$this->linkPath}img/banner/pointer.png"><a href="{$this->linkPath}edit_info/operator_edit_skin.php">チャット画面背景変更</a></li>
							<li><img src="{$this->linkPath}img/banner/pointer.png"><a href="{$this->linkPath}edit_info/operator_edit_myinfo.php">個人情報設定・変更</a></li>
							</div>
						</ul>

					</div>
					<div class="sitemap02" style="padding:10px 0px;">
						<ul>
							<li><b>メール BOX</b></li>
							<li><img src="{$this->linkPath}img/banner/pointer.png"><a href="javascript:window.open('{$this->linkPath}mail/mailbox_receive.php?midoku', 'opwin', 'scrollbars=yes, toolbar=no,resizable=yes');void(0);">未読メール</a></li>
							<li><img src="{$this->linkPath}img/banner/pointer.png"><a href="javascript:window.open('{$this->linkPath}mail/write.php', 'opwin', 'scrollbars=yes, toolbar=no,resizable=yes');void(0);">新規作成</a></li>
							<li><img src="{$this->linkPath}img/banner/pointer.png"><a href="javascript:window.open('{$this->linkPath}mail/mailbox_receive.php', 'opwin', 'scrollbars=yes, toolbar=no,resizable=yes');void(0);">受信BOX</a></li>
						</ul>
						<ul>
							<li><b>報酬について</b></li>
							<li><img src="{$this->linkPath}img/banner/pointer.png"><a href="{$this->linkPath}payment_system/operator_pay_point.php">獲得ポイント状況</a></li>
							<li><img src="{$this->linkPath}img/banner/pointer.png"><a href="{$this->linkPath}payment_system/operator_pay_access.php">アクセス履歴</a></li>
							<li><img src="{$this->linkPath}img/banner/pointer.png"><a href="{$this->linkPath}payment_system/operator_pay_bonusinfo.php">ボーナス履歴</a></li>
							<li><img src="{$this->linkPath}img/banner/pointer.png"><a href="{$this->linkPath}payment_system/operator_pay_paidinfo.php">精算履歴</a></li>
						</ul>
					</div>
					<div class="sitemap03" style="padding:10px 0px;">
						<ul>
							<li><b>お仕事について</b></li>
							<!-- <li><img src="{$this->linkPath}img/banner/pointer.png"><a href="{$this->linkPath}howtowork/step1.php">お仕事の始め方</a></li> -->
							<li><img src="{$this->linkPath}img/banner/pointer.png"><a href="{$this->linkPath}manual/faq/?M=list&id=443">待機・チャット画面</a></li>
							<li><img src="{$this->linkPath}img/banner/pointer.png"><a href="{$this->linkPath}campaign/setting/" target="_blank">カメラ・マイク設定</a></li>
							<li><img src="{$this->linkPath}img/banner/pointer.png"><a href="{$this->linkPath}manual/faq/?M=list&id=425">快適にお仕事頂く為に</a></li>
						</ul>
					</div>
					<div class="sitemap04" style="padding:10px 0px;">
						<ul>
							<li><b>マシェリモバイル</b></li>
							<li><img src="{$this->linkPath}img/banner/pointer.png"><a href="{$this->linkPath}mobile_tel/">マシェリモバイル設定画面</a></li>
							<li><img src="{$this->linkPath}img/banner/pointer.png"><a href="{$this->linkPath}mobile_tel/access.php">携帯版マシェリ お仕事ページへアクセス</a></li>
							<li><img src="{$this->linkPath}img/banner/pointer.png"><a href="javascript:window.open('http://m-mb.jp/operator.php?M=OP_MAIL_RECEIVEBOX&FROMIDNO={$this->profile['user_id']}&FROMPASS={$this->profile['password']}', 'opwin', 'scrollbars=yes, toolbar=no,resizable=yes');void(0);">マシェリモバイルメールBOX</a></li>
							<li><img src="{$this->linkPath}img/banner/pointer.png"><a href="{$this->linkPath}manual/mobile_faq/?M=list&id=76">マシェリモバイルマニュアル</a></li>
						</ul>
					</div>

					<div class="sitemap05" style="padding:10px 0px 10px 10px;">
						<ul>
							<li><b>ヘルプ</b></li>
							<!-- <li><img src="{$this->linkPath}img/banner/pointer.png"><a href="{$this->linkPath}support/faq.php">よくある質問(FAQ)</a></li> -->
							<li><img src="{$this->linkPath}img/banner/pointer.png"><a href="#" onclick="org=document.charset;document.charset='UTF-8';document.tofaq.submit();document.charset=org;">よくある質問(FAQ)</a></li>
							<li><img src="{$this->linkPath}img/banner/pointer.png"><a href="http://{$_SERVER['HTTP_HOST']}/cs_support/" target="_blank">サポートセンター問合わせ</a></li>
							<li><img src="{$this->linkPath}img/banner/pointer.png"><a href="{$this->linkPath}support/support_tel.php">電話でのお問い合わせ</a></li>
							<li><img src="{$this->linkPath}img/banner/pointer.png"><a href="{$this->linkPath}support/bbs.php">フォーラム(BBS)</a></li>
							<li><img src="{$this->linkPath}img/banner/pointer.png"><a href="{$this->linkPath}prohibition/">禁止事項</a></li>
							<li><img src="{$this->linkPath}img/banner/pointer.png"><a href="{$this->linkPath}agreement.php">ご利用規約</a></li>
							<li><img src="{$this->linkPath}img/banner/pointer.png"><a href="{$this->linkPath}manual/faq/?M=detail&qid=3989">推奨環境</a></li>
							<li><img src="{$this->linkPath}img/banner/pointer.png"><a href="{$this->linkPath}info/operator_info_black.php">ブラックリスト</a></li>
							<li><img src="{$this->linkPath}img/banner/pointer.png"><a href="{$this->linkPath}support/operator_staff_list.php">スタッフ紹介</a></li>
						</ul>
					</div>
				</div>
			</div>
EOM;

		return $html;
	}

	#---------------------------------------------------------
	#	左のコンテンツ構築
	#---------------------------------------------------------
	function getLeftContents ()
	{
		
		$this->getBlog();
		$this->getOnTime();
		$this->getPayment();
		$this->checkMobileMailBox();
		$this->getEventBtn();
		$newMail = $this->checkMailBox();
		$waitChat = $this->checkChatWait();
		$messageHelper = new MessageHelper();
		$unread = $messageHelper->getAllUnreadMessage($_SESSION['user_id'], 2);


		if(!$this->profile['agent_code']){
			$html = <<<EOM
				<div id="LoginButtonBlock" class="cmnSection">
					<ul class="clearfix">
						<div class="left_btn"><a href="/performer/schedule_calendar.php"><img src="{$this->linkPath}img/banner/btn_left_ive_cal_off.png" onmouseover="this.src='{$this->linkPath}img/banner/btn_left_ive_cal_on.png'" onmouseout="this.src='{$this->linkPath}img/banner/btn_left_ive_cal_off.png'" /></a></div>
EOM;
		} else {
			$html = <<<EOM
				<div id="LoginButtonBlock" class="cmnSection">
					<ul class="clearfix">
EOM;
		}
		if($this->mobile['flg']){
			$html .= "<li id=\"shot2Connection\"><a href=\"{$this->linkPath}chat/chat_top.php?chat_mode=0&camp=0\">2ショット接続</a></li>";
		} else {
			$html .= "<li id=\"shot2Connection\"><a href=\"{$this->linkPath}chat/chat_top.php?con_mode=0&chat_mode=0&camp=0\">2ショット接続</a></li>";
		}

// 2013-05-07 ポップアップ用ウィンドウのdivタグ追加
/*
					<div class="mic_on_off"><a href="" onclick="return popDisp(2013,this)" target="_parent">マイク あり・なし の注意について</a></div>
	<div class="popWindow popWin2013">
		<a class="popClose" href="javascript:;" onclick="popDisp(2013)"></a>
		<div class="popBody">
			<img src="http://www.macherie.tv/performer/images/mic_on_off.png" />
		</div>
	</div>
*/
		$html .= <<<EOM
					<li id="partyConnection"><a href="{$this->linkPath}chat/party.php?chat_mode=1">パーティーチャット接続</a></li>
					{$this->eventBtn}
					<li id="mikeDirect" style="line-height:24px;"></li>
					<div class="mic_on_off"><a href="" onclick="return popDisp(2013,this)" target="_parent">マイク あり・なし の注意について</a></div>
	<div class="popWindow popWin2013">
		<a class="popClose" href="javascript:;" onclick="popDisp(2013)"></a>
		<div class="popBody">
			<img src="http://www.macherie.tv/performer/images/mic_on_off.png" />
		</div>
	</div>
					<li id="testConnection"><a href="{$this->linkPath}connect/operator_camera_check.php">カメラの接続テスト</a></li>
					<input type="hidden" id="user_id" name="user_id" value="{$_SESSION['user_id']}">
				</ul>
			</div>

			<div style="position:relative;">
				<div id="mail_icon_mini1"><a href="{$this->linkPath}mail/mailbox_receive.php?midokuL" class="newWindow"><img src="/performer/img/banner/icon_new.png"></a></div>
				<div id="SubInformation" class="clearfix cmnSection">
					<ul>
						<li id="NewMail"><a href="{$this->linkPath}message/inbox.php" >{$unread['total']}件</a></li>
						<li id="WaitingChat"><a href="{$this->linkPath}memo/call_wait.php">{$waitChat}件</a></li>
					</ul>
				</div>
			</div>
EOM;
			if(!$this->profile['sub_agent_code'])
			{
			$html .= <<<EOM
			<div id="MobileInformation" class="clearfix cmnSection">
				<ul>
					<li id="mobileMail"><a href="http://m-mb.jp/operator.php?M=OP_MAIL_RECEIVEBOX&FROMIDNO={$this->profile['user_id']}&FROMPASS={$this->profile['password']}" class="newWindow">{$this->mobile['count']}件</a></li>
				</ul>
			</div>
EOM;
			}

		//ブログ
		$html .= $this->blog;

		//オンタイム接続情報
		$html .= $this->ontime;

		$html .= <<<EOM
			<div id="OperatorStats" class="cmnSection clearfix">
				<div id="mail_icon_mini2"><a href="{$this->linkPath}mail/mailbox_receive.php?midokuL" class="newWindow"><img src="/performer/img/banner/icon_new.png"></a></div>

				<div id="gallery">
					<!-- img src="{$this->profile['img']}" alt="{$this->nickName}" /-->
					<div id="pIcon"></div>
					<ul>
						<li>
							<img src="{$this->linkPath}img/banner/prof_icon.gif" >
							<a href="{$this->linkPath}edit_info/operator_edit_profile.php">プロフィール編集</a>
						</li>
					</ul>
				</div>

				<dl>
					<dt>{$this->nickName}</dt>
EOM;
					if(!isset($this->payment['adjustable'])) $this->payment['adjustable'] = "";
					if(!$this->profile['agent_code'])
					{
						$html .= "今月の獲得報酬<br /><span id='pafo1'>{$this->payment['hold_point']}pt</span><br /><span id='pafo1'>{$this->payment['reward_money']}円</span><br />";
						$html .= "<span id='pafo3'>精算可能金額</span><br /><span id='pafo2'>{$this->payment['adjustable']}</span><br />";
						$html .= "<ul id=\"statsSetting\" class=\"clearfix\"><li class=\"payment\"><a href=\"{$this->linkPath}payment_system/operator_pay_point.php\">もっと詳しく</a></li></ul>";
					}
					else
					{
						$html .= "<dd>獲得ポイント<br /><span>{$this->payment['hold_point']}pt<br /><a href=\"{$this->linkPath}payment_system/operator_pay_point.php\">獲得ポイント</a></dd>";
					}

		$html .= <<<EOM
					<!-- <dd>目標金額まで<br /><span>円</span></dd> -->
					<!-- <dd>今月の目標金額<br /><span>円</span></dd> -->
					<dt>
						<img src="{$this->linkPath}img/customer_view/customer_view_s.png" border="0" onMouseOver="this .src='{$this->linkPath}img/customer_view/customer_view_s_ov.png'" onMouseOut="this .src='{$this->linkPath}img/customer_view/customer_view_s.png'" value="お客様視点"
						onClick="javascript:window.open('{$this->linkPath_macherie}/chat/shicho.php?id={$this->profile['hash']}');void(0);" style="margin:-1px 0 0 0;">
					</dt>
				</dl>
				<ul class="buttonArea clearfix">
					<div><a href="{$this->linkPath}message/inbox.php" ><img src="{$this->linkPath}img/banner/mail_box.png" /></a></div>
					<div style="position:relative;top:6px;"><a href="{$this->linkPath}memo/chat_history.php"><img src="{$this->linkPath}img/banner/chat_history.png" /></a></div>
				</ul>

				<ul id="statsSetting" class="clearfix">
					<!-- <li class="clientComment"><a href="{$this->linkPath}edit_info/operator_edit_comment.php">お客様に一言</a></li> -->
EOM;
/*
					if(!$this->profile['agent_code'])
					{
						$html .="<li class=\"payment\"><a href=\"{$this->linkPath}payment_system/operator_pay_point.php\">報酬清算</a></li>";
					}
					else
					{
						$html .="<li class=\"payment\"><a href=\"{$this->linkPath}payment_system/operator_pay_point.php\">獲得ポイント</a></li>";
					}
*/
		$html .= <<<EOM
					<li class="newMember"><a href="{$this->linkPath}memo/new_male_regist.php">新規会員様</a></li>
					<li class="memo"><a href="{$this->linkPath}memo/operator_memo_info.php">お客様メモ</a></li>
					<!-- <li class="target"><a href="{$this->linkPath}">目標設定</a></li> -->
				</ul>

			</div>

			<div id="bannerBlock" class="cmnSection">
EOM;
			if(!$this->profile['agent_code']){
				$html .= <<<EOM

				<div style="padding-bottom:10px;" class="rollover">
					<a href="{$this->linkPath}campaign/debut_mark/" target="_blank"><img src="{$this->linkPath}img/banner/debut_btn2.jpg" /></a>
				</div>




EOM;
			}
		$html .= <<<EOM
			</div>
EOM;
		return $html;
	}

	function getFooter ()
	{
		$year = date("Y");
		$html = <<<EOM
			<div id="Footer" style="background:#e9e9e9; margin-top:20px;">
EOM;
		if(!$this->profile['agent_code']){
			$html .= <<<EOM
				<div style="text-align:left; background-image:url(img/sitemap_bg.png);background-repeat:no-repeat;">
				</br><b>　-サイトマップ-</b></br>
				</div>
				<div class="sitemap01" style="padding:10px 0px 20px 0px;">
					<ul>
						<div style="padding:0px 0px 5px 10px;">
						<a href="{$this->linkPath}schedule_calendar.php"><img src="{$this->linkPath}img/banner/btn_ive_cal.png" onmouseover="this.src='{$this->linkPath}img/banner/btn_ive_cal_on.png'" onmouseout="this.src='{$this->linkPath}img/banner/btn_ive_cal.png'" /></a>
						</div>

						<div style="padding:0px 0px 5px 10px;">
EOM;
			if($this->mobile['flg']){
				$html .= "<a id=\"map2Shot\" href=\"{$this->linkPath}chat/chat_top.php?chat_mode=0&camp=0\"><img src=\"{$this->linkPath}img/banner/btn_2shot.png\" onmouseover=\"this.src='{$this->linkPath}img/banner/btn_2shot_on.png'\" onmouseout=\"this.src='{$this->linkPath}img/banner/btn_2shot.png'\" /></a>";
			} else {
				$html .= "<a id=\"map2Shot\" href=\"{$this->linkPath}chat/chat_top.php?con_mode=0&chat_mode=0&camp=0\"><img src=\"{$this->linkPath}img/banner/btn_2shot.png\" onmouseover=\"this.src='{$this->linkPath}img/banner/btn_2shot_on.png'\" onmouseout=\"this.src='{$this->linkPath}img/banner/btn_2shot.png'\" /></a>";
			}

			$html .= <<<EOM
						</div>
						<div style="padding:0px 0px 5px 10px;">
						<a id="mapParty" href="{$this->linkPath}chat/party.php?chat_mode=1"><img src="{$this->linkPath}img/banner/btn_party.png" onmouseover="this.src='{$this->linkPath}img/banner/btn_party_on.png'" onmouseout="this.src='{$this->linkPath}img/banner/btn_party.png'" /></a>
						</div>
						<div style="padding:0px 0px 5px 10px;">
						<a href="{$this->linkPath}connect/operator_camera_check.php"><img src="{$this->linkPath}img/banner/btn_cameratest_ora.png" onmouseover="this.src='{$this->linkPath}img/banner/btn_cameratest_ora_on.png'" onmouseout="this.src='{$this->linkPath}img/banner/btn_cameratest_ora.png'" /></a>
						</div>
						<div style="padding:0px 0px 10px 10px;">
						<a href="{$this->linkPath}support/bbs.php"><img src="{$this->linkPath}img/banner/btn_bbs.png" onmouseover="this.src='{$this->linkPath}img/banner/btn_bbs_on.png'" onmouseout="this.src='{$this->linkPath}img/banner/btn_bbs.png'" /></a>
						</div>
						<div style="padding:0px 0px 0px 6px;">
						<li><img src="{$this->linkPath}img/banner/pointer.png" /><a href="http://www.macherie.tv/" target="_blank">マシェリ ﾒｲﾝｻｲﾄ(会員様側)</a></li>
						<li><img src="{$this->linkPath}img/banner/pointer.png" /><a href="http://m-mb.jp/" target="_blank">マシェリモバイル</a></li>
						</div>
					</ul>
				</div>
				<div class="sitemap02" style="padding:10px 0px 20px 0px;">
					<ul>
						<li><b>はじめに</b></li>
						<li><img src="{$this->linkPath}img/banner/pointer.png" /><a href="{$this->linkPath}prohibition/">禁止事項について</a></li>
						<li><img src="{$this->linkPath}img/banner/pointer.png" /><a href="{$this->linkPath}agreement.php">ご利用規約について</a></li>
					</ul>
					</br>
					<ul>
						<li><b>メール</b></li>
						<li><img src="{$this->linkPath}img/banner/pointer.png" /><a href="{$this->linkPath}message/inbox.php" target="_blank">未読メール</a></li>
						<li><img src="{$this->linkPath}img/banner/pointer.png" /><a href="{$this->linkPath}mail/mailbox_receive.php" target="_blank">メールボックス</a></li>
					</ul>
				</div>
				<div class="sitemap03" style="padding:10px 0px 20px 0px;">
					<ul>
						<div style="margin-left:20px;">
						<li><b>アクセス情報</b></li>
						<li><img src="{$this->linkPath}img/banner/pointer.png" /><a href="{$this->linkPath}payment_system/operator_pay_access.php">アクセス履歴</a></li>
						<li><img src="{$this->linkPath}img/banner/pointer.png" /><a href="{$this->linkPath}payment_system/operator_pay_bonusinfo.php">ボーナス履歴</a></li>
						<li><img src="{$this->linkPath}img/banner/pointer.png" /><a href="{$this->linkPath}memo/chat_history.php">チャット履歴</a></li>
						<li><img src="{$this->linkPath}img/banner/pointer.png" /><a href="{$this->linkPath}male_search.php">会員様サーチ機能</a></li>
						<li><img src="{$this->linkPath}img/banner/pointer.png" /><a href="{$this->linkPath}payment_system/operator_pay_point.php">今月の獲得ポイント状況</a></li>
						<li><img src="{$this->linkPath}img/banner/pointer.png" /><a href="{$this->linkPath}payment_system/operator_pay_paidinfo.php">精算履歴一覧</a></li>
						<li><img src="{$this->linkPath}img/banner/pointer.png" /><a href="{$this->linkPath}payment_system/operator_pay_point.php#compensation">報酬体系一覧</a></li>
						</div>
					</ul>
				</div>
				<div class="sitemap04" style="padding:10px 0px 20px 0px;">
					<ul>
						<div style="margin-left:40px;">
						<li><b>お仕事マニュアル</b></li>
						<li><img src="{$this->linkPath}img/banner/pointer.png" /><a href="{$this->linkPath}manual/faq/?M=list&id=443">チャット画面操作方法</a></li>
						<li><img src="{$this->linkPath}img/banner/pointer.png" /><a href="{$this->linkPath}movie/" target="_blank">マシェリ動画マニュアル</a></li>
						<li><img src="{$this->linkPath}img/banner/pointer.png" /><a href="{$this->linkPath}work_explanation/photo.php" target="_blank">トップ写真撮影テクニック</a></li>
						<li><img src="{$this->linkPath}img/banner/pointer.png" /><a href="{$this->linkPath}work_explanation/waiting.php" target="_blank">待機テクニック</a></li>
						<li><img src="{$this->linkPath}img/banner/pointer.png" /><a href="{$this->linkPath}campaign/setting/" target="_blank">カメラ・マイク　解消方法</a></li>
						<li><img src="{$this->linkPath}img/banner/pointer.png" /><a href="{$this->linkPath}manual/faq/?M=list&id=423">キャッシュクリア</a></li>
						<li><img src="{$this->linkPath}img/banner/pointer.png" /><a href="{$this->linkPath}manual/faq/?M=detail&qid=3989">推奨環境</a></li>
						</div>
					</ul>
				</div>
				<div class="sitemap05" style="padding:10px 0px 20px 0px;">
					<ul>
						<li><b>情報編集</b></li>
						<li><img src="{$this->linkPath}img/banner/pointer.png" /><a href="{$this->linkPath}edit_info/operator_edit_profile.php">プロフィール変更</a></li>
						<li><img src="{$this->linkPath}img/banner/pointer.png" /><a href="{$this->linkPath}edit_info/operator_edit_myinfo.php">個人情報変更</a></li>
					</ul>
					</br>
					<ul>
						<li><b>サポート</b></li>
						<li><img src="{$this->linkPath}img/banner/pointer.png" /><a href="http://{$_SERVER['HTTP_HOST']}/cs_support/" target="_blank">メール問い合わせ</a></li>
						<li><img src="{$this->linkPath}img/banner/pointer.png" /><a href="{$this->linkPath}support/support_tel.php">電話での問い合わせ</a></li>
						<li><img src="{$this->linkPath}img/banner/pointer.png" /><a href="{$this->linkPath}support/operator_staff_list.php">スタッフ紹介</a></li>
						<li><img src="{$this->linkPath}img/banner/pointer.png" /><a href="{$this->linkPath}recruit/index_recruit.php">運営スタッフ募集中！</a></li>
					</ul>
				</div>
				<div style="clear: both; background-image:url(img/footer_bg.png);background-repeat:no-repeat;">
					<address>Copyright &copy; Macherie All Rights Reserved. </address>
				</div>
			</div>

			<div id="pWindow" style="display:none" >

			</div>
EOM;
		} else {
			$html .= <<<EOM
				<div style="clear: both">
					<address>Copyright &copy; 2004-{$year} Macherie All Rights Reserved. </address>
				</div>
			</div>
EOM;
		}

		if(strcmp($this->fname, OP_PATH.'template/schedule_calendar.html') != 0){

			$html .= <<<EOM
			</div>
			<link rel="stylesheet" type="text/css" href="{$this->linkPath}css/pop_window.css?v=1" />
			<script type="text/javascript" src="{$this->linkPath}js/jquery-1.7.1.min.js"></script>
			<script type="text/javascript" src="{$this->linkPath}js/jquery-ui-tabs-1.8.16.min.js"></script>
			<script type="text/javascript" src="{$this->linkPath}js/jquery.curvycorners.min.js"></script>
			<script type="text/javascript" src="{$this->linkPath}js/common.js?v=2"></script>
			<script type="text/javascript">
			   var icon_flag = 1;
			</script>
			<script type="text/javascript" src="{$this->linkPath}js/pop_window.js?v=1"></script>
			<script type="text/javascript">
			  var _gaq = _gaq || [];
			  _gaq.push(['_setAccount', 'UA-13149951-1']);
			  _gaq.push(['_trackPageview']);

			  (function() {
				var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			  })();
			</script>

<form action="https://secure.okbiz.okwave.jp/faqs/end_user_login" method="post" target="_blank" name="tofaq" accept-charset="UTF-8" onsubmit="return false;">

  <input id="_username" name="\$username" value="performer" type="hidden">
  <input id="_password" name="\$password" value="machelogin" type="hidden">

  <input id="_http_method" name="\$http_method" value="post" type="hidden">
  <input id="site_domain" name="site_domain" value="performer" type="hidden">

</form>

EOM;
		} else {
			$html .= "\n</div>";
		}

		return $html;
	}

	#---------------------------------------------------------
	#	吐き出し
	#---------------------------------------------------------
	function flush($outvar = 0)
	{
		if(!isset($_SESSION))
		{
			session_start();
		}
		parent::loopset("") ;
		parent::assign( 'header' , $this->globalHeader());
		if(!$this->profile['agent_code']){
			parent::assign( 'navi_contents' , $this->getNaviContents());
		}
		if(strcmp($this->fname, OP_PATH.'template/schedule_calendar.html') == 0){
			parent::assign( 'script_header' , $this->getScriptHeader());
		}
		parent::assign( 'utilities' , $this->getLeftContents());
		parent::assign( 'footer' , $this->getFooter());
		parent::flush($outvar);
		return;
	}
}

?>
