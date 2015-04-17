<?php

require_once 'common_proc.inc';
require_once 'common_db.inc';
require_once 'tmpl2.class_ex.inc';
require_once 'Owner.inc';

//--------------------------------------
  $tmpl = new Tmpl22($tmpl_dir . 'performer/bulletin_list.html');
  $tmpl->dbgmode(0);
  
  $tmpl->assign('header', getHeader());
  $tmpl->assign('is_first_page', '');
  $tmpl->assign('is_last_page', '');
  
  session_start();
  
  /************
   * DEFAULTS *
   ************/
  $CURR_PAGE = 1;
  $ITEM_LIMIT = 5;
  
  
  /********
   * LIST *
   ********/
  $params = array(1, 1);
  $where = ' WHERE `is_visible`=?';
  switch ($_GET['is']) {
    case 2:
      $where .= ' AND `is_female`=?';
      break;
    case 3:
      $where .= ' AND `is_male`=?';
      break;
    case 4:
      $where .= ' AND `is_macherie`=?';
      break;
    default:
      array_pop($params);
      break;
  }
  
  $flv1 = (isset($_GET['flv1'])) ? $_GET['flv1'] : filterFlv1() ;
  if (0 < $flv1) {
    $where .= " AND find_in_set('$flv1', `flv1`)";
  }
  
  $limit = " LIMIT $ITEM_LIMIT";
  if (isset($_GET['page'])) {
    $CURR_PAGE = $_GET['page'];
    $offset = ($CURR_PAGE - 1) * $ITEM_LIMIT;
    $limit = " LIMIT $offset, $ITEM_LIMIT";
  }
  
  $sql =
    'SELECT `seqno`, `title`, `img_path`, `start_date`, `start_time`, `end_date`, `end_time`,' .
          ' `is_male`, `is_female`, `is_macherie`, `is_new`, `short_msg`, `is_special`' .
     ' FROM `bulletin`' . $where . ' ORDER BY `priority` DESC' . $limit;
  $result = $dbSlave->query($sql, $params);
  
  if (DB::isError($result)) {
    print $sql;
    err_proc($result->getMessage());
  }
  
  $tmpl->loopset('list_loop');
  while ($row = $result->fetchRow()) {
    $tmpl->assign('seqno', $row[0]);
    $tmpl->assign('title', $row[1]);
    $tmpl->assign('img_path', '/imgs/bulletin/' . $row[2]);
    $tmpl->assign('datetime', formatDateTime($row[3], $row[4], $row[5], $row[6]));
    
    if (1 == $row[7]) {
      $tmpl->assign('is_male', 1);
    }
    if (1 == $row[8]) {
      $tmpl->assign('is_female', 1);
    }
    if (1 == $row[9]) {
      $tmpl->assign('is_macherie', 1);
    }
    if (1 == $row[10]) {
      $tmpl->assign('is_new', 1);
    }
    
    $tmpl->assign('short_msg', $row[11]);
    $isSpecial = (1 == $row[12]) ? ' class="is_special"' : '';
    $tmpl->assign('is_special', $isSpecial);
    
		$tmpl->loopnext();
  }
  $tmpl->loopset('');
  
  
  /**************
   * PAGINATION *
   **************/
  $totalCnt = getTotalCount($where, $params);
  if ($ITEM_LIMIT < $totalCnt) {
    $tmpl->assign('has_pagination', 1);
    
    // show link to previous page
    if (1 < $CURR_PAGE)  {
      $tmpl->assign('isnt_first_page', 1);
      $tmpl->assign('prev_page', ($CURR_PAGE - 1));
    }
    else {
      $tmpl->assign('is_first_page', '<em class="btn_gray none">←前のページへ</em>');
    }
    
    // last possible page
    $LAST_PAGE = ceil($totalCnt / $ITEM_LIMIT);
    
    /* Show only 3 page links */
    // default
    $pageA = 1;
    $pageC = min(3, $LAST_PAGE);
    // for pages 3 and up
    if (2 < $CURR_PAGE) {
      $pageC = min(($CURR_PAGE + 1), $LAST_PAGE);
      $pageA = $pageC - 2;
    }
    
    $tmpl->loopset('page_loop');
    for ($i = $pageA; $i <= $pageC; $i++) {
      $tmpl->assign('page', $i);
      $isActivePage = ($i == $CURR_PAGE) ? ' class="select"' : '';
      $tmpl->assign('is_active_page', $isActivePage);
    	$tmpl->loopnext();
    }
    $tmpl->loopset('');
    
    // show last page if number of pages reaches 20 and current page is not within 5 pages from last page
    if (19 < $LAST_PAGE && $CURR_PAGE < ($LAST_PAGE - 5)) {
      $tmpl->assign('show_last_page', 1);
      $tmpl->assign('last_page', $LAST_PAGE);
    }
    
    // show link to next page
    if ($LAST_PAGE > $CURR_PAGE)  {
      $tmpl->assign('isnt_last_page', 1);
      $tmpl->assign('next_page', ($CURR_PAGE + 1));
    }
    else {
      $tmpl->assign('is_last_page', '<em class="btn_gray none">次のページへ→</em>');
    }
  }
  
  $tmpl->flush();
  
  exit;
  
//--------------------------------------
/**
 * Retrieves the header from DB table `bulletin_header`.
 * @global type $dbSlave
 * @return string
 */
function getHeader() {
  global $dbSlave;
  
  $sql = 'SELECT `msg` FROM `bulletin_header` ORDER BY `seqno` DESC LIMIT 1';
  $result = $dbSlave->query($sql);
  
  if (DB::isError($result)) {
    print $sql;
    err_proc($result->getMessage());
    
    return '';
  }
  
  $row = $result->fetchRow();
  return (is_null($row)) ? '' : $row[0];
}

/**
 * Retrieves the header from DB table `bulletin_header`.
 * @global type $dbSlave
 * @return string
 */
function getTotalCount($where, $params) {
  global $dbSlave;
  
  $sql = 'SELECT COUNT(1) FROM `bulletin`' . $where;
  $result = $dbSlave->query($sql, $params);
  
  if (DB::isError($result)) {
    print $sql;
    err_proc($result->getMessage());
    
    return 0;
  }
  
  $row = $result->fetchRow();
  return (is_null($row)) ? 0 : $row[0];
}

function filterFlv1() {
  global $ownerCd, $dbSlave;
  
  $sql  = 'SELECT `FLV1` FROM `female_member` WHERE `OWNER_CD`=? and `USER_ID`=?';
  $result = $dbSlave->query($sql, array($ownerCd, $_SESSION['user_id']));
  
  if (DB::isError($result)) {
    print $sql;
    err_proc($result->getMessage());
    
    return 0;
  }
  
  $row = $result->fetchRow();
  if (is_null($row)) {
    return 0;
  }
  
  $acceptedFlv1 = array(1, 2, 4, 5, 7, 18, 19, 20, 21);
  return (in_array($row[0], $acceptedFlv1)) ? $row[0] : 4;
}

/**
 * Formats the start date and end date to Y年m月d日 H:i~Y年m月d日 H:i.
 * @param string $startDate
 * @param string $endDate
 * @return string
 */
function formatDateTime($startDate, $startTime, $endDate, $endTime) {
  $dateTime = isset($startDate) ? date('Y年m月d日', strtotime($startDate)): '';
  if (isset($startTime)) {
    $dateTime .= ' ' . date('H:i', strtotime($startTime));
  }
  
  if ('' == $dateTime) {
    return '';
  }
  
  if (isset($endDate)) {
    $dateTime .=  '~' . date('Y年m月d日', strtotime($endDate));
    if (isset($startTime)) {
      $dateTime .= ' ' . date('H:i', strtotime($endTime));
    }
  }
  
  return $dateTime;
}
