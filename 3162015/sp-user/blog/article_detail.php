<?php
require_once 'Owner.inc';
require_once 'common_proc.inc';
require_once 'db_con.inc';
require_once 'sp/boy_login.inc';
if (isset($_GET['sp_login'])) {
	require_once 'sp/boy_sp.inc';
}
else {
	require_once 'sp/boy_sp_top.inc';
}
require_once 'sp/tmpl2.class_ex.inc';
require_once 'sp/common_proc.inc';
require_once 'blog/articleFunc.inc';
require_once 'blog/BlogArticle.php';

if (isset($_GET)) {
	foreach ($_GET as $key => $value) {
		if ($key != 'm') {
			setcookie ("Advc", "$key", time() + 30*24*60*60, "/");
		}
	}
}

// add comment
if (isset($_GET['id'])&&isset($_POST['msg'])) {
	if (sp_isLogin()) {
		$msg = $_POST['msg'];
		if (strlen($msg) > 0) {
			BlogArticleDB::addComment($_GET['id'], $msg, $_SESSION['user_id'], 1);
		}
	}else {
		header('Location: index.php');
		exit;
	}
}

$_SESSION['flash_data'] = 1;

// default page
$commentPage = 1;
$commentLimit = 10;

// load more comments this is an ajax response
if (isset($_GET['article_id']) && isset($_GET['page'])) {
	$id = $_GET['article_id'];
	$commentPage = $_GET['page'];
	$offset = $commentPage * $commentLimit;
	$offset = $offset - 5;
	$limit = ' LIMIT ' . $offset . ' , ' . $commentLimit;
	$comments = getComment($id, $limit);
	$data = '';
	while ($comment = $comments->fetchRow(DB_FETCHMODE_ASSOC)) {
		$date = date('Y-m-d H:i',strtotime($comment['cre_date']));
		$data .= '<div class="comment_item cf">';
		$data .= '<div class="comment_item_inner">';
		$data .= '<div class="comment_content cf">';
		$data .= '<p class="comment_author_name">' . $comment['nick_name'] . '</p>';
		$data .= '<p class="comment_datetime">' . $date . '</p>';
		$data .= '<p class="comment_text">' . nl2br($comment['msg']) . '</p>';
		$data .= '</div></div></div>';
	}

	echo $data;
	exit;
}

// like or unlike an article this is an ajax response
if (isset($_POST['article_id']) && isset($_POST['like'])) {
	$likedBaId = $_POST['article_id'];
	$like = $_POST['like'];
	$userId = (sp_isLogin()) ? $_SESSION['user_id'] : null ;

	// can only like once
	if (is_null($userId)) { // if not logged in, cookie-based
		// check cookie 'ba_l_{id}'
		$hasLiked = BLOG_ARTICLE_COOKIE_LIKE . $likedBaId;
		if (isset($_COOKIE[$hasLiked]) && $_COOKIE[$hasLiked] == 1) {
			print 'Liked already!';
			exit;
		}

		setcookie($hasLiked, 1);
	}
	else { // if logged in
		if (BlogArticleDB::isLikedBy($likedBaId, $userId, BLOG_ARTICLE_CRE_TYPE_USER)) {
			print $userId . ' liked already!';
			exit;
		}
	}

	if ($like == 1) {
		$status = BlogArticleDB::addLike($likedBaId, $userId, 1);
	}
	else {
		$status = BlogArticleDB::removeLike($likedBaId, $_SESSION['user_id'], 1);
	}

	if ($status) {
		print 'ok';
	}
	else {
		print 'error';
	}

	exit;
}

if (!isset($_GET['id']) ) {
	header('Location: index.php');
}

$id = $_GET['id'];
$userId = (sp_isLogin()) ? $_SESSION['user_id'] : null ;
$userType = 1;
$blogArticle = new BlogArticle($id);

if (!$blogArticle->exists()) {
	header('Location: index.php');
}

// show 404 if hidden
if (0 == $blogArticle->getIsViewable()) {
	$tmpl = new Tmpl22($sp_tmpl_dir . '404.html');
	$tmpl->flush();
	exit();
}

$creator = $blogArticle->getCreator();
$creatorStat = $creator['f_stat'];
if (6 == $creatorStat ||
		7 == $creatorStat ||
		9 == $creatorStat) {
	$tmpl = new Tmpl22($sp_tmpl_dir . '404.html');
	$tmpl->flush();
	exit;
}

$tmpl = new Tmpl22($sp_tmpl_dir . 'blog/article_detail.html');

if (sp_isLogin()) {
	$tmpl->assign('no_login_disp', '');
	$tmpl->assign('show_comment_form', '');
	if ($blogArticle->isLikedBy($userId, $userType)) {
		$tmpl->assign('on', 'on');
	}
	else {
		$tmpl->assign('on', '');
	}
} else {
	$tmpl->assign('login_disp', '');
	$tmpl->assign('no_comment_form',1);

	// check cookie 'ba_l_{id}'
	$hasLiked = BLOG_ARTICLE_COOKIE_LIKE . $id;
	if (isset($_COOKIE[$hasLiked]) && $_COOKIE[$hasLiked] == 1) {
		$tmpl->assign('on', 'on');
	}
	else {
		$tmpl->assign('on', '');
	}
}


$tmpl->assign('id',$id);

$viewCnt = countView($id);
addViewCount($id,$viewCnt);
$tmpl->assign('view_cnt', $viewCnt + 1);

$commentCnt = countComment($id);
$commentPages = ceil(($commentCnt + 5) / $commentLimit);
$tmpl->assign('pages', $commentPages);

$tmpl->assign('title', $blogArticle->getTitle());
if (strlen($blogArticle->getImage()) > 0 ) {
	$tmpl->assign('blog_img',"/imgs/blog/".$blogArticle->getImage());
}
$tmpl->assign('body', nl2br2($blogArticle->getBody()));
$tmpl->assign('date', date('Y-m-d H:i', strtotime($blogArticle->getCreatedDate())));
// スペースが画面上に正常に表示されるように対応
$creator['nick_name'] = str_replace(" ", "&nbsp;", $creator['nick_name']);
$tmpl->assign('performer_name', $creator['nick_name']);
$tmpl->assign('performer_img', '/imgs/op/320x240/' . $creator['img']);
$tmpl->assign('comment_cnt', $commentCnt);
$tmpl->assign('like_cnt', countLike($id));
$tmpl->assign('performer_hash', $creator['hash']);

$status = '';
$chatShicho="onclick=MO('{$creator['hash']}')";
if ($creator['stat'] == 0) {
	$status = '<span class="status offline">オフライン</span>';
}
else if ($creator['stat'] == 2) {
	$status = '<span class="status onchat">チャット中</span>';
}
else {
	$status = '<span class="status online">オンライン</span>';
}
$tmpl->assign('chat_shicho', $chatShicho);

$limit = ' LIMIT 0 , 5 ';
//if comments > 5 show a load more button
if ($commentPages > 1) {
	$tmpl->assign('page', $commentPage);
}

$comments = getComment($id, $limit);
$tmpl->loopset('loop_comment');
while ($comment = $comments->fetchRow(DB_FETCHMODE_ASSOC)) {
	$tmpl->assign('msg', nl2br($comment['msg']));
	$tmpl->assign('created', date('Y-m-d H:i', strtotime($comment['cre_date'])));
	$tmpl->assign('nick_name', $comment['nick_name']);
	$tmpl->loopnext();
}
$tmpl->loopset('');

$tmpl->assign('status',$status);
$prevID = getPrevID($id, $blogArticle->getCreatedId());
$nextID = getNextID($id, $blogArticle->getCreatedId());
$prev_statusID = $prevID == '' ? 'disable ': '';
$next_statusID = $nextID == '' ? 'disable ': '';

$prev = getPrevID($id);
$next = getNextID($id);
$prev_status = $prev == '' ? 'disable ': '';
$next_status = $next == '' ? 'disable ': '';

$tmpl->assign('prev_statusID', $prev_statusID);
$tmpl->assign('next_statusID', $next_statusID);
$tmpl->assign('prev_status', $prev_status);
$tmpl->assign('next_status', $next_status);

$tmpl->assign('prev', $prev);
$tmpl->assign('next', $next);
$tmpl->assign('prevID', $prevID);
$tmpl->assign('nextID',$nextID);

$tmpl->flush();
exit;
