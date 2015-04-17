<?
//==========================================================
// 男性レフトメニュー
//==========================================================
//require_once 'mailBOXMaintenance.inc';
require_once MODELS_DIR . '/sche_right_banner.php';

//Message/Line mail
require_once 'CommonDb.php';
require_once 'message/MessageHelper.php';

function getMacherieLeftMenu($logon_Judge, $portal_Code, $db, $ownerCd, $template){

	$templ_ssl = array("registrationfree_renew.html", "registrationfree.html", "registrationfree_ok.html");

	$portalOption = array( MACHERIE_BIGLOBE => "/biglobe/", MACHERIE_CINEMA => "/cinema/", MACHERIE_RAKUTEN => "/rakuten/", MACHERIE_NORMAL => "/", MACHERIE_WORLD => "/");
	$portalMacherie = $portalOption[$portal_Code];

	$edit_template = substr( $template, strrpos($template, '/') + 1 );
	if( in_array( $edit_template , $templ_ssl ) ){
		$ssl_judge = true;
	} else {
		$ssl_judge = false;
	}

	$str = showLoginArea($logon_Judge, $portal_Code, $portalMacherie, $db, $ownerCd, $ssl_judge);
	$str .= showMenuArea($logon_Judge, $portal_Code, $portalMacherie, $ssl_judge);
	$str .= showOtherArea($portal_Code, $portalMacherie, $ssl_judge);

	return $str;

}

function showLoginArea($logon_Judge, $portal_Code, $portalMacherie, $db, $ownerCd, $ssl_judge){
	$macherie_url = MACHERIE_ROOT;
	$macherie_ssl = SSL_MACHERIE; 

	//global $db;
	$prefix_banner = '<link href="/c/s/css/regist_send_common_renew.css" rel="stylesheet" type="text/css" media="all" />';
	//$prefix = '';
#echo $_SERVER['PHP_SELF'];
	// index.phpにだけ表示
	$banner_disp_flg = false;	// 長バナーが出てる時は未ログイン時にはログイン枠を表示しない
	if ($_SERVER['PHP_SELF']=='/index.php') {
		$right_banner_models = new sche_right_banner(DBS1_CD);
		$arr = $right_banner_models->getDispDataFromSiteflgArea('1', '3', '1');

		//$banner_disp_flg = false;	// 長バナーが出てる時は未ログイン時にはログイン枠を表示しない
		$banner_disp_flg = true;	// 長バナーが出てる時は未ログイン時にはログイン枠を表示しない

		switch($portal_Code){
			case MACHERIE_NORMAL:{
				foreach($arr as $key => $val){
					$prefix_banner.="<div class='block'><a href='".$val['url']."' target='_blank'><img src='/images/right_banner/".$val['filename']."' alt='macherie' /></a></div>\n";
					$banner_disp_flg = true;
				}
			}
			break;
			case MACHERIE_WORLD:{
				foreach($arr as $key => $val){
					$prefix_banner.="<div class='block'><a href='".$val['url']."' target='_blank'><img src='/images/right_banner/".$val['filename']."' alt='macherie world'  /></a></div>\n";
					$banner_disp_flg = true;
				}
			}
			break;
			case MACHERIE_CINEMA:{
				foreach($arr as $key => $val){
					$prefix_banner.="<div class='block'><a href='".$val['url']."' target='_blank'><img src='/images/right_banner/".$val['filename']."' alt='シネマ' /></a></div>\n";
					$banner_disp_flg = true;
				}
			}
			break;
			default:break;
		}
	}

	$prefix = $prefix_banner . '<div class="block" id="login">';
	if($logon_Judge){
		$prefix .= "\n".'		<img src="http://c.macherie.tv/c/m/images/renewimages/left_logout_head01.jpg" width="170" height="22" alt="ライブチャットマシェリへログアウト" />'."\n".'		<div id="loginBorder">';
	} else if($portal_Code == MACHERIE_RAKUTEN){
		if ($banner_disp_flg==false) {
			$prefix .= "\n".'<img src="http://c.macherie.tv/c/m/images/renewimages/left_login_head02.jpg" width="170" height="24" alt="ライブチャットマシェリからログイン" />'."\n".'		<div id="loginBorder">'."\n".'		<form action="https://api.id.rakuten.co.jp/openid/auth" method="post">'."\n";
		}
	} else if($ssl_judge && strcmp($_SERVER["HTTP_HOST"], "www.macherie.tv") == 0){
		if ($banner_disp_flg==false) {
			$prefix .= "\n".'<img src="/c/m/images/renewimages/left_login_head02.jpg" width="170" height="24" alt="ライブチャットマシェリからログイン" />'."\n".'		<div id="loginBorder">'."\n"."		<form method='post' name='FLOGIN' id='FLOGIN' action='https://{$_SERVER['HTTP_HOST']}{$portalMacherie}imacherie.php\'>\n";
		}
	} else if(strcmp($_SERVER["HTTP_HOST"], "macherie.tv") == 0){
		if ($banner_disp_flg==false) {
			$prefix .= "\n".'<img src="http://c.macherie.tv/c/m/images/renewimages/left_login_head02.jpg" width="170" height="24" alt="ライブチャットマシェリからログイン" />'."\n".'		<div id="loginBorder">'."\n"."		<form method='post'  id='FLOGIN'name='FLOGIN' action='http://{$_SERVER['HTTP_HOST']}{$portalMacherie}imacherie.php'>\n";
		}
	} else {
		if ($banner_disp_flg==false) {
			$prefix .= "\n".'<img src="http://c.macherie.tv/c/m/images/renewimages/left_login_head02.jpg" width="170" height="24" alt="ライブチャットマシェリからログイン" />'."\n".'		<div id="loginBorder">'."\n"."		<form method='post'  id='FLOGIN' name='FLOGIN' action='http://{$_SERVER['HTTP_HOST']}{$portalMacherie}imacherie.php'>\n";
		}
	}

	$str = $prefix;

	if($logon_Judge){
		if ($banner_disp_flg==false) {
			$str .= '			<table summary="Login">'."\n";
	
/*
		$row = $db->getLogin($ownerCd);
		$top_point=$row[0];
		$top_unread=number_format($row[1]);
		$top_nick_name=$_SESSION['nick_name'];
*/
			if($db->get_login_info($ownerCd,get_uid(),$top_point,$top_unread)){
				$top_nick_name=get_nickname();
			} else {
				err_proc($result->getMessage());
				exit;
			}
			//追加
			if(!$info = $db->get_login_infos($ownerCd,get_uid())){
				err_proc($result->getMessage());
				exit;
			}
			//メール認証	0:なし、1:未認証、2:認証済
			$mail_auth_str = "";
			if($info["mail_auth"] == 1){

				$mail_auth_str = <<<EOM
		<div class="case1 clearfix">
				<div class="oshirase"><img src="http://c.macherie.tv/c/m/images/renewimages/left_logout_oshirase_01.jpg" alt='mail' /></div>
				<div class="tsuuchi"><a href="/message/inbox.php" target="_blank" class="link4">メールアドレスの本人確認をしてください</a></div>
		</div>
EOM;
			}

			$card_auth_str = "";

			//無料会員のみ表示
	        if(isset($info["assortment"]) && $info["assortment"] == 0){
/*
            $card_auth_str = <<<EOM
        <div class="case1 clearfix">
                <div class="oshirase"><img src="http://c.macherie.tv/c/m/images/renewimages/left_logout_star_01.jpg"></div>
                <div class="tsuuchi"><a href="http://www.milu.jp/lp/macherie.php" target="blank" class="link4">
                1000円分のポイントプゼント実施中!!
                </a></div>
        </div>
EOM;
*/
	        }

			//カード認証    0:なし、1:認証済
			if($info["card_auth"] == 0){
				$card_auth_str.= <<<EOM
		<div class="case1 clearfix">
				<div class="oshirase"><img src="http://c.macherie.tv/c/m/images/renewimages/left_logout_star_01.jpg" alt='renew' /></div>
				<div class="tsuuchi"><a href="./regist_credit.php" target="blank" class="link4">
				無料体験ポイントが受け取れます
				</a></div>
		</div>
EOM;
			}

			//点線
			$partition_str = "";
			if(!empty($mail_auth_str) || !empty($card_auth_str)){
				$partition_str = '<img src="http://c.macherie.tv/c/m/images/renewimages/left_login_dot_01.jpg" alt="renew email" />';
			}

			//Line message get unread message
			$messageHelper = new MessageHelper();
			$unread = $messageHelper->getAllUnreadMessage($_SESSION['user_id'], 1);

/** Login後フォーム */
			$str .= <<<EOM
			<tr>
				<th class="chatname"><span class="big">{$top_nick_name}さま</span></th>
			</tr>
		<table>
			<tr>
				<th class="point"><span class="big">ポイント</span></th>
				<th class="point_right"><a href="http://{$_SERVER["HTTP_HOST"]}{$portalMacherie}settlement/credit.php" target="_blank">{$top_point} pt</a></th>
			</tr>
			<tr>
				<th class="no_mail"><span class="big">未読メール</span></th>
				<th class="no_mail_right"><a href="../message/inbox.php" class="link4">{$unread['total']} 通</a></th>
			</tr>
			<tr>
				<td colspan="2">
					<form method="post" action="http://{$_SERVER["HTTP_HOST"]}{$portalMacherie}" name="logout_f">
						<input type="image" src="http://c.macherie.tv/c/m/images/renewimages/left_logout_logout_01.jpg"  onmouseover="this.src='http://c.macherie.tv/c/m/images/renewimages/left_logout_logout_on.jpg'" onmouseout="this.src='http://c.macherie.tv/c/m/images/renewimages/left_logout_logout_01.jpg'" alt="ログアウト" class="submit" value="ログアウト" />
						<input type="hidden" name="logout" value="1" />
					</form>
				</td>
			</tr>

		</table>
{$partition_str}
{$mail_auth_str}
{$card_auth_str}
	</div>
</div>

EOM;
		}else{
			$str = $prefix_banner;
		}
	} else {

		if ($banner_disp_flg==false) {
			$str .= '			<table summary="Login">'."\n";
			$checked = "";
			$user_id = "";
			$password = "";

			if(isset($_COOKIE['save_flg'])){
				if($_COOKIE['save_flg'] == "1"){
					$checked = "checked";
					$user_id = $_COOKIE['save_user_id'];
					$password = $_COOKIE['save_password'];
				}
			}

			switch ($portal_Code){
				case MACHERIE_BIGLOBE:
					$str .= <<<EOM
					<tr>
						<td colspan="2">
							<input type="image" src="http://c.macherie.tv/c/m/images/renewimages/left_login_login_01.jpg"  onmouseover="this.src='http://c.macherie.tv/c/m/images/renewimages/left_login_login_02.jpg'" onmouseout="this.src='http://c.macherie.tv/c/m/images/renewimages/left_login_login_01.jpg'" alt="ログイン" class="submit" value="ログイン" />
							<input type="hidden" name="mode2" value="login" />
							<input type="hidden" name="mode_login" value="login" />
							<input type="hidden" name="direct_top_page" value="1" />
						</td>
					</tr>
EOM;
					break;
				case MACHERIE_RAKUTEN:
					$str .= <<<EOM
					<tr>
						<td colspan="2">
EOM;
					$str .= loginFormRakuten();
					$str .= <<<EOM
					<td colspan="2"><div align="left">
					<center>楽天会員IDでログイン</center>
					<input type="hidden" name="direct_top_page" value="1" />
					</div></td>
					<!--/td-->
					</tr>
EOM;
					break;
				case MACHERIE_CINEMA:
				case MACHERIE_NORMAL:
				case MACHERIE_WORLD:
					$str .= <<<EOM
					<tr>
						<th class="idandmail" cellspacing="0"><span class="big">ID</span><span class="mini">または</span><span class="big">メールアドレス</span></th>
					</tr>
					<tr>
						<td class="idandmail" cellspacing="0"><input name="user_id" id="user_id" type="text" class="login" value="{$user_id}" /></td>
					</tr>
					<tr>
						<th class="idandmail" cellspacing="0"><span class="big">パスワード</span></th>
					</tr>
					<tr>
						<td class="idandmail" cellspacing="0"><input name="password" id="password" type="password" class="login" value="{$password}" /></td>
					</tr>
					<tr>
						<th>
							<div id="regist-cut" cellspacing="0">
								<input type="checkbox" name="save" class="radio" id="save" value="1" {$checked} />
								<span class="forget">次回から入力を省略</span>
							</div>
						</th>
					</tr>
EOM;
					if($ssl_judge){
						$str .= <<<EOM
					<tr>
						<td colspan="2">
							<input type="image" src="/c/m/images/renewimages/left_login_login_01.jpg"  onmouseover="this.src='/c/m/images/renewimages/left_login_login_02.jpg'" onmouseout="this.src='/c/m/images/renewimages/left_login_login_01.jpg'" alt="ログイン" class="submit" value="ログイン" />
							<input type="hidden" name="mode2" value="login" />
							<input type="hidden" name="mode_login" value="login" />
							<input type="hidden" name="direct_top_page" value="1" />
						</td>
					</tr>
EOM;
					}else{
						$str .= <<<EOM
					<tr>
						<td colspan="2">
							<input type="image" src="http://c.macherie.tv/c/m/images/renewimages/left_login_login_01.jpg"  onmouseover="this.src='http://c.macherie.tv/c/m/images/renewimages/left_login_login_02.jpg'" onmouseout="this.src='http://c.macherie.tv/c/m/images/renewimages/left_login_login_01.jpg'" alt="ログイン" class="submit" value="ログイン" />
							<input type="hidden" name="mode2" value="login" />
							<input type="hidden" name="mode_login" value="login" />
							<input type="hidden" name="direct_top_page" value="1" />
						</td>
					</tr>
EOM;
					}
					$str .= <<<EOM
					<tr>
						<th>
							<div id="regist-hajimetenokatahe">
								<span class="mail_icon">
									<a href="{$macherie_url}/mail_resend_renew.php" target="_blank" class="link4">
									<span class="cut">パスワードを忘れた場合</span></a>
								</span>
							</div>
						</th>
					</tr>
EOM;
					break;
			}

			$suffix = <<<EOM
			</table>
		</form>
EOM;
			$str .= $suffix;


			if($portal_Code == MACHERIE_NORMAL || $portal_Code == MACHERIE_WORLD){
	// 楽天ログイン誘導用
				if($ssl_judge){
					$str .= <<<EOM

		</div>
	</div>

			<div class="block">
				<a href="{$macherie_ssl}{$portalMacherie}registrationfree.php">
					<img src="/c/m/images/common/left/btn_left_regist.jpg" onmouseover="this.src='/c/m/images/common/left/btn_left_regist_on.jpg'" onmouseout="this.src='/c/m/images/common/left/btn_left_regist.jpg'" alt="ライブチャットマシェリ新規登録" width="170" height="47" />
				</a>
			</div>

EOM;
					$str .= loginFormOpenId();
					$str .= <<<EOM
					<div class="block">
						<a href="{$macherie_url}/performer/" target="_blank">
							<img src="/c/m/images/common/left/banner_performer.gif" alt="パフォーマー用ログイン" width="170" height="47" />
						</a>
					</div>
EOM;
				}else{
					$str .= <<<EOM

		</div>
	</div>

	<div class="block">
		<a href="{$macherie_ssl}{$portalMacherie}registrationfree.php">
			<img src="http://c.macherie.tv/c/m/images/common/left/btn_left_regist.jpg" onmouseover="this.src='http://c.macherie.tv/c/m/images/common/left/btn_left_regist_on.jpg'" onmouseout="this.src='http://c.macherie.tv/c/m/images/common/left/btn_left_regist.jpg'" alt="ライブチャットマシェリ新規登録" width="170" height="47" />
		</a>
	</div>
EOM;
					$str .= loginFormOpenId();
					$str .= <<<EOM
	<div class="block">
		<a href="{$macherie_url}/performer/" target="_blank">
			<img src="http://c.macherie.tv/c/m/images/common/left/banner_performer.gif" alt="パフォーマー用ログイン" width="170" height="47" />
		</a>
	</div>
EOM;
				}
			} else if($portal_Code == MACHERIE_CINEMA){
				if($ssl_judge){
					$str .= <<<EOM
				</div>
			</div>
			<div class="block">
				<a href="{$macherie_ssl}{$portalMacherie}registrationfree.php">
					<img src="/c/m/images/common/left/btn_left_regist.jpg" onmouseover="this.src='http://c.macherie.tv/c/m/images/common/left/btn_left_regist_on.jpg'" onmouseout="this.src='http://c.macherie.tv/c/m/images/common/left/btn_left_regist.jpg'" alt="ライブチャットマシェリ新規登録" width="170" height="47" />
				</a>
			</div>
EOM;
				}else{
					$str .= <<<EOM
				</div>
			</div>
			<div class="block">
				<a href="{$macherie_ssl}{$portalMacherie}registrationfree.php">
					<img src="http://c.macherie.tv/c/m/images/common/left/btn_left_regist.jpg" onmouseover="this.src='http://c.macherie.tv/c/m/images/common/left/btn_left_regist_on.jpg'" onmouseout="this.src='http://c.macherie.tv/c/m/images/common/left/btn_left_regist.jpg'" alt="ライブチャットマシェリ新規登録" width="170" height="47" />
				</a>
			</div>
EOM;
				}
			} else if($portal_Code == MACHERIE_BIGLOBE){
				$str .= <<<EOM
				</div>
			</div>
			<div class="block">
				<a href="http://{$_SERVER['HTTP_HOST']}{$portalMacherie}registration.php">
					<img src="/c/m/biglobe/images/registration/left_menu_regist.gif" alt="ライブチャットマシェリ新規登録" width="170" height="730" />
				</a>
			</div>
EOM;
			} else {
				$str .= <<<EOM
				</div>
			</div>
			<div class="block">
				<a href="http://{$_SERVER['HTTP_HOST']}{$portalMacherie}registration.php">
					<img src="http://c.macherie.tv/c/m/images/common/left/btn_left_regist.jpg" onmouseover="this.src='http://c.macherie.tv/c/m/images/common/left/btn_left_regist_on.jpg'" onmouseout="this.src='http://c.macherie.tv/c/m/images/common/left/btn_left_regist.jpg'" alt="ライブチャットマシェリ新規登録" width="170" height="47" />
				</a>
			</div>
EOM;
			}
		} else {
			// よくわからんが調整用
			$str.= '</div>';
		}

	}

	return $str;

}

function showMenuArea($logon_Judge, $portal_Code, $portalMacherie, $ssl_judge){
	$macherie_url = MACHERIE_ROOT;
	$macherie_ssl = SSL_MACHERIE;

	$str = <<<EOM
<!-- Menu -->
<ul class="block" id="menu">
<li id="mTitle">メインメニュー</li>
<li id="menu1"><a href="http://{$_SERVER["HTTP_HOST"]}{$portalMacherie}">ライブチャットはマシェリ</a></li>
EOM;
/*	新規無料登録独立につきコメント化
	if(!$logon_Judge){
		switch ($portal_Code){
			case MACHERIE_BIGLOBE:
			case MACHERIE_RAKUTEN:
				$str .= "<li id=\"menu2\"><a href=\"http://{$_SERVER['HTTP_HOST']}{$portalMacherie}registration.php\">ライブチャットマシェリ新規登録</a></li>";
				break;
			case MACHERIE_CINEMA:
			case MACHERIE_NORMAL:
			case MACHERIE_WORLD:
				$str .= "<li id=\"menu2\"><a href=\"{$macherie_ssl}{$portalMacherie}registrationfree.php\">ライブチャットマシェリ新規登録</a></li>";
				break;
		}
	}
*/
	$str .= <<<EOM
<li id="menu3"><a href="http://{$_SERVER["HTTP_HOST"]}{$portalMacherie}about.php">マシェリとは</a></li>

<li id="menu5"><a href="http://{$_SERVER["HTTP_HOST"]}{$portalMacherie}settlement/bank.php" target="_blank">ライブチャットマシェリ料金案内</a></li>
<li id="menu6"><a href="http://{$_SERVER["HTTP_HOST"]}{$portalMacherie}list.php">ライブチャットマシェリに出演しているパフォーマー検索</a></li>
<li id="menu7"><a href="http://{$_SERVER["HTTP_HOST"]}{$portalMacherie}imacherie.php">ライブチャット会員様ページ</a></li>
<li id="menu8"><a onclick="org=document.charset;document.charset='UTF-8';document.tofaq.submit();document.charset=org;" href="#">サポート</a></li>
</ul>

<form action="https://secure.okbiz.okwave.jp/faqs/end_user_login" method="post"  target="_blank" name="tofaq" accept-charset="UTF-8" onsubmit="return false;">

  <input id="_username" name="\$username" value="guest" type="hidden" />
  <input id="_password" name="\$password" value="guest" type="hidden" />
  <input id="_http_method" name="\$http_method" value="post" type="hidden" />
  <input id="site_domain" name="site_domain" value="macherie" type="hidden" />

</form>
EOM;
	if(!$logon_Judge && $portal_Code == MACHERIE_BIGLOBE){
		$str .= '';		
	}else{
		if($ssl_judge){
			$str .= <<<EOM
	<div class="block"><a href="javascript:mail({$portal_Code});"><img src="/c/m/images/common/left/banner_mail.gif" alt="メールボックス" width="170" height="47" /></a></div>
EOM;
		}else{
			$str .= <<<EOM
	<div class="block"><a href="javascript:mail({$portal_Code});"><img src="http://c.macherie.tv/c/m/images/common/left/banner_mail.gif" alt="メールボックス" width="170" height="47" /></a></div>
EOM;
		}
	}

	return $str;
}

function showOtherArea($portal_Code, $portalMacherie, $ssl_judge){
	$macherie_url = MACHERIE_ROOT;
	$macherie_ssl = SSL_MACHERIE;

	if($ssl_judge){
		$str = <<<EOM
		<!-- iconHelp -->
		<ul id="iconHelp" class="block">
			<li class="title"><img src="/c/m/images/common/menu/top_icon.gif" alt="アイコン説明" width="170" height="24" /></li>
			<li><img src="/c/m/images/common/menu2/icon_help.gif" alt="2ショットモードでオンライン中" /></li>
		</ul>
		
		
		<!-- 料金案内 -->
		<div class="block">
			<img src="/c/m/images/common/menu/top_price.gif" alt="料金案内" width="170" height="24" />
			<a href="http://{$_SERVER["HTTP_HOST"]}{$portalMacherie}settlement/bank.php" target="_blank"><img src="/c/m/images/common/menu2/settlement.gif" alt="料金案内ページへ" width="170" /></a>
		</div>
		
		<div class="block">
			<img src="/c/m/images/common/menu/top_recommend.gif" alt="推奨環境" width="170" height="24" />
			<dl>
				<dt>■必須プラグイン</dt>
		
				<dd><a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" target="_blank"><img src="/c/s/images/campaign/new_flash_img.gif" alt="FlashPlayer" width="70" height="25" /></a></dd>
				<dt>■推奨ブラウザ</dt>
				<dd>Windows</dd>
				<dd><a href="http://www.microsoft.com/japan/windows/products/winfamily/ie/default.mspx" target="_blank"><img src="/c/m/images/common/left/ie_img.gif" alt="Internet Explorer" width="70" height="26" /></a>&nbsp;&nbsp;<a href="http://www.mozilla-japan.org/products/firefox/" target="_blank"><img src="/c/m/images/common/left/firefox.gif" alt="Fire Fox" width="70" height="25" /></a><br/><span style="font-size:10px">　</span><img src="/c/m/images/pr/pr_border.gif" width="154" height="16" alt="microsoft macherie" /></dd>
		
				<dd class="f10">
					<div style="text-align: center; margin: 0 auto;">
						<script src=https://seal.verisign.com/getseal?host_name={$_SERVER['HTTP_HOST']}&size=S&use_flash=YES&use_transparent=YES&lang=ja></script>
						<br /><a href="http://www.verisign.co.jp/" target="_blank">ベリサインを導入しています</a>
					</div>
				</dd>
			</dl>
		</div>
EOM;
	}else{
		$str = <<<EOM
		<!-- iconHelp -->
		<ul id="iconHelp" class="block">
			<li class="title"><img src="http://c.macherie.tv/c/m/images/common/menu/top_icon.gif" alt="アイコン説明" width="170" height="24" /></li>
			<li><img src="http://c.macherie.tv/c/m/images/common/menu2/icon_help.gif" alt="2ショットモードでオンライン中" /></li>
		</ul>
		
		
		<!-- 料金案内 -->
		<div class="block">
			<img src="http://c.macherie.tv/c/m/images/common/menu/top_price.gif" alt="料金案内" width="170" height="24" />
			<a href="http://{$_SERVER["HTTP_HOST"]}{$portalMacherie}settlement/bank.php" target="_blank"><img src="http://c.macherie.tv/c/m/images/common/menu2/settlement.gif" alt="料金案内ページへ" width="170" /></a>
		</div>
		
		<div class="block">
			<img src="http://c.macherie.tv/c/m/images/common/menu/top_recommend.gif" alt="推奨環境" width="170" height="24" />
			<dl>
				<dt>■必須プラグイン</dt>
		
				<dd><a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" target="_blank"><img src="http://c.macherie.tv/c/s/images/campaign/new_flash_img.gif" alt="FlashPlayer" width="70" height="25" /></a></dd>
				<dt>■推奨ブラウザ</dt>
				<dd>Windows</dd>
				<dd><a href="http://www.microsoft.com/japan/windows/products/winfamily/ie/default.mspx" target="_blank"><img src="http://c.macherie.tv/c/m/images/common/left/ie_img.gif" alt="Internet Explorer" width="70" height="26" /></a>&nbsp;&nbsp;<a href="http://www.mozilla-japan.org/products/firefox/" target="_blank"><img src="http://c.macherie.tv/c/m/images/common/left/firefox.gif" alt="Fire Fox" width="70" height="25" /></a><br/><span style="font-size:10px">　</span><img src="http://c.macherie.tv/c/m/images/pr/pr_border.gif" width="154" height="16" alt="firefox macherie" /></dd>
		
				<dd class="f10">
					<div style="text-align: center; margin: 0 auto;">
						<script src=https://seal.verisign.com/getseal?host_name={$_SERVER['HTTP_HOST']}&size=S&use_flash=YES&use_transparent=YES&lang=ja></script>
						<br /><a href="http://www.verisign.co.jp/" target="_blank">ベリサインを導入しています</a>
					</div>
				</dd>
			</dl>
		</div>
EOM;
	}

	if($portal_Code == MACHERIE_BIGLOBE){
/*
		$str .= <<<EOM
		<!-- Regist -->
		<div class="block">
			<a href="./registration.php">
				<img src="http://c.macherie.tv/c/m/biglobe/images/left/banner_off.gif" border="0" alt="ライブチャットはマシェリ">
			</a>
		</div>
EOM;
 */
	}

	return $str;

}

function loginFormRakuten(){
	$macherie_url = MACHERIE_ROOT;
	$macherie_ssl = SSL_MACHERIE;

	$str =  <<<EOM
					<input type="image" src="https://webservice.rakuten.co.jp/img/openid/openid_login_M.gif" alt="楽天でログイン" class="submit" value="ログイン" />
					<input type="hidden" name="openid.ns" value="http://specs.openid.net/auth/2.0" />
					<input type="hidden" name="openid.return_to" value="{$macherie_url}/rakuten/if/login.php" />
					<input type="hidden" name="openid.claimed_id" value="http://specs.openid.net/auth/2.0/identifier_select"/>
					<input type="hidden" name="openid.identity" value="http://specs.openid.net/auth/2.0/identifier_select"/>
					<input type="hidden" name="openid.mode" value="checkid_setup"/>
				</td>
			</tr>
			<tr>
EOM;

	return $str;

}
function loginFormOpenId(){
	$macherie_url = MACHERIE_ROOT;
	$macherie_ssl = SSL_MACHERIE;

	if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') || (isset($_SERVER['HTTP_SSL']) && $_SERVER['HTTP_SSL']=='YES')) {
		$image_url = $macherie_ssl;
	} else {
		$image_url = "http://c.macherie.tv";
	}

	$str =  <<<EOM
	<div class="block">
		<div class="openid">
			<div class="openid_contents-tweet">
			<a href="{$macherie_url}/open_id/twitter_login.php" alt="twitterアカウントでログイン・登録" title="twitterアカウントでログイン・登録">
				<img src="{$image_url}/c/m/images/openid/open_tweet_off.png" onmouseover="this.src='{$image_url}/c/m/images/openid/open_tweet_on.png'" onmouseout="this.src='{$image_url}/c/m/images/openid/open_tweet_off.png'"  alt='twitterアカウントでログイン・登録'  />
			</a>
			</div>
	
			<div class="openid_contents-facebook">
			<a href="{$macherie_url}/open_id/facebook_login.php" alt="facebookアカウントでログイン・登録" title="facebookアカウントでログイン・登録">
				<img src="{$image_url}/c/m/images/openid/open_facebook_off.png" onmouseover="this.src='{$image_url}/c/m/images/openid/open_facebook_on.png'" onmouseout="this.src='{$image_url}/c/m/images/openid/open_facebook_off.png'" alt='facebookアカウントでログイン・登録' />
			</a>
			</div>
	
			<div class="openid_contents-google">
			<a href="{$macherie_url}/open_id/login.php?id=https%3A%2F%2Fwww.google.com%2Faccounts%2Fo8%2Fid" alt="googleアカウントでログイン・登録" title="googleアカウントでログイン・登録">
				<img src="{$image_url}/c/m/images/openid/open_google_off.png" onmouseover="this.src='{$image_url}/c/m/images/openid/open_google_on.png'" onmouseout="this.src='{$image_url}/c/m/images/openid/open_google_off.png'"  alt='googleアカウントでログイン・登録' />
			</a>
			</div>
	
			<div class="openid_contents-yahoo">
			<a href="{$macherie_url}/open_id/login.php?id=https%3A%2F%2Fme.yahoo.co.jp" alt="yahooアカウントでログイン・登録" title="yahooアカウントでログイン・登録">
				<img src="{$image_url}/c/m/images/openid/open_yahoo_off.png" onmouseover="this.src='{$image_url}/c/m/images/openid/open_yahoo_on.png'" onmouseout="this.src='{$image_url}/c/m/images/openid/open_yahoo_off.png'" alt='yahooアカウントでログイン・登録' />
			</a>
			</div>
	
			<div class="openid_contents-rakuten">
				<form action="https://api.id.rakuten.co.jp/openid/auth" method="post">
				<input type="hidden" name="openid.ns" value="http://specs.openid.net/auth/2.0" />
				<input type="hidden" name="openid.return_to" value="http://www.macherie.tv/rakuten/if/login.php" />
				<input type="hidden" name="openid.claimed_id" value="http://specs.openid.net/auth/2.0/identifier_select" />
				<input type="hidden" name="openid.identity" value="http://specs.openid.net/auth/2.0/identifier_select" />
				<input type="hidden" name="openid.mode" value="checkid_setup" />
				<input type="image" style="border: 0px;" 
					src="{$image_url}/c/m/images/openid/open_rakuten_off.png" 
					onmouseover="this.src='{$image_url}/c/m/images/openid/open_rakuten_on.png'" 
					onmouseout="this.src='{$image_url}/c/m/images/openid/open_rakuten_off.png'" 
					alt="楽天IDでログイン・登録" title="楽天IDでログイン・登録" />
				</form>
			</div>
	
			<div class="openid_contents-biglobe">
			<form action="http://www.macherie.tv/biglobe/imacherie.php" name="FLOGIN" method="post">
			<input type="hidden" name="mode2" value="login"/>
			<input type="hidden" name="mode_login" value="login"/>
			<input type="image" style="border: 0px;" 
				src="{$image_url}/c/m/images/openid/open_biglobe_off.png" 
				onmouseover="this.src='{$image_url}/c/m/images/openid/open_biglobe_on.png'" 
				onmouseout="this.src='{$image_url}/c/m/images/openid/open_biglobe_off.png'" 
				alt="BIGLOBEアカウントでログイン・登録" title="BIGLOBEアカウントでログイン・登録" />
			</form>
			</div>
	
			<div class="openid_contents-gaibu">
				<a href="{$macherie_url}/faq_openid.php">
				<img src="{$image_url}/c/m/images/openid/open_gaibu_off.png" 
					onmouseover="this.src='{$image_url}/c/m/images/openid/open_gaibu_on.png'" 
					onmouseout="this.src='{$image_url}/c/m/images/openid/open_gaibu_off.png'" 
					alt="外部IDでログインとは" title="外部IDでログインとは"/>
				</a>
			</div>
		</div>
	</div>
EOM;

	return $str;

}
?>
