<?

require_once 'error_ex.inc';
require_once 'admin.inc';
require_once 'common_proc.inc';
require_once 'common_db_slave.inc';
require_once 'FormObject.inc';
require_once 'tmpl2.class_ex.inc';
require_once 'Owner.inc';

//  if(!access_level(2)){
//      exit;
//  }

    $fobj = new FormObject("ポイントログ女性：管理",11);

    if(!isset($_POST['mode'])){
        $tmpl = new Tmpl22($tmpl_dir . "manage/point_log_female.html");
        $tmpl->dbgmode(0);
        $today = date('Y/m/d');
        $fobj->setValue("last_from_con",$today);
        $fobj->setValue("last_to_con",$today);
        $tmpl->setFormInstance($fobj);
        $tmpl->setFormObjectItems();
        $tmpl->assign("msg","");
        $tmpl->assign("mode_set","disp");
        $tmpl->flush();
        exit;
    }
    if(isset($_POST['mode'])){
        //入力画面・確認画面から呼ばれた場合
        //入力チェック
        $fobj->importFormData();
        if(!$fobj->validation()){
            $cnt = $fobj->getValidateErrorNum();
            $err_msg = "";
            for($i=0;$i<$cnt;$i++){
                $err_msg .= $fobj->getValidateError();
                $err_msg .= "<br>";
                }
            $tmpl = new Tmpl22($tmpl_dir . "manage/point_log_female.html");
            $tmpl->dbgmode(0);
            $tmpl->setFormInstance($fobj);
            $tmpl->setFormObjectItems();
            $tmpl->assign("msg","<font color=red size=-1>".$err_msg."</font>");
            $tmpl->assign("mode_set","disp");

            $tmpl->flush();
            exit;
        } else {
            $tmpl = new Tmpl22($tmpl_dir . "manage/point_log_female.html");
            $tmpl->dbgmode(0);
            $tmpl->setFormInstance($fobj);
            $tmpl->setFormObjectItems();
            $tmpl->assign("msg","");
            $tmpl->assign("mode_set","disp");

            if ($_POST['last_from_con']<>'' && $_POST['last_to_con']<>'') {
                if (!empty($_POST['user_id_con'])) {
                    // 会員ID指定の場合は３１日以上の場合エラー
                    if ((strtotime($_POST['last_to_con']) - strtotime($_POST['last_from_con'])) >= 7*24*60*60) {
                        $tmpl->assign("msg","<font color=red size=-1>作成日の期間は７日以内で入力してください。</font>");
                        $tmpl->assign("mode_set","disp");
                        $tmpl->flush();
                        exit;
                    }
                } else {
                    // ３日以上の場合エラー
                    if ((strtotime($_POST['last_to_con']) - strtotime($_POST['last_from_con'])) >= 3*24*60*60) {
                        $tmpl->assign("msg","<font color=red size=-1>作成日の期間は３日以内で入力してください。</font>");
                        $tmpl->assign("mode_set","disp");
                        $tmpl->flush();
                        exit;
                    }
                }
            }


        }
    }

    // 会員ランク

    //１ページあたりの表示件数
//  $page_num = 100;
    global $page_num;   // 一覧の最大表示件数

    if(!isset($_POST['pos'])){
        $pos = 0;
    }else{
        if($_POST['pos'] == ""){
            $pos = 0;
        }else{
            $pos = $_POST['pos'];
        }
    }

    //入力された検索条件よりwhere句を生成
    $search_str  = " l.owner_cd = " . $_SESSION['ownerCd'] . " ";

    if(isset($_POST['user_id_con'])){
        if($_POST['user_id_con'] != ""){
            $search_str .= " and  l.user_id = '" . $_POST['user_id_con'] . "' ";
        }
    }
    if(isset($_POST['cre_id_con'])){
        if($_POST['cre_id_con'] != ""){
            $search_str .= " and  l.cre_id = '" . $_POST['cre_id_con'] . "' ";
        }
    }
    if(isset($_POST['last_from_con'])){
        if($_POST['last_from_con'] != ""){
            $search_str .= " and  l.cre_date >= '".$_POST['last_from_con']."' ";
        }
    }
    if(isset($_POST['last_to_con'])){
        if($_POST['last_to_con'] != ""){
            $search_str .= " and  l.cre_date <= '".$_POST['last_to_con']."' + interval 86399 second";
        }
    }
    if(isset($_POST['mode_con'])){
        if($_POST['mode_con'] != 99){
            $search_str .= " and  l.upd_mode = " . $_POST['mode_con'] . " ";
        }
    }
    if(isset($_POST['ip_con'])){
        if($_POST['ip_con'] != ""){
            $search_str .= " and  l.cre_ip = '" . $_POST['ip_con'] . "' ";
        }
    }

    if(isset($_POST['flv1_con'])){
        if($_POST['flv1_con'] != "99"){
            $search_str .= " and  m.flv1 = '" . $_POST['flv1_con'] . "' ";
        }
    }


    //データ件数取得
    $sql  = "";
    $sql .= "select count(*) from female_point_log AS l LEFT JOIN female_member AS m ON l.OWNER_CD=m.OWNER_CD AND l.user_id=m.user_id where $search_str ";
    $result = $dbSlave33->query($sql);
    if(DB::isError($result)){
        print $sql;
        }
    $row = $result -> fetchRow();
    $tmpl->assign("data_num","$row[0]");
    if($row[0] > 0){
        $tmpl->assign("disp_data","1");
        }

/*
    //ページ移動リンク作成
    //前のページ
    if($pos < $page_num){
        $tmpl->assign("prev_link","　");
    }else{
        $tmp = $pos - $page_num;
        $tmpl->assign("prev_link","<a href=\"javascript:goto_page($tmp)\">前のページ</a>");
        }
    //次のページ
    if($pos + $page_num >= $row[0]){
        $tmpl->assign("next_link","　");
    }else{
        $tmp = $pos + $page_num;
        $tmpl->assign("next_link","<a href=\"javascript:goto_page($tmp)\">次ページ</a>");
        }
    //ページ指定
    $i=0;
    $page = 1;
    $str = "";
    while($i<=$row[0]){
        if($i == $pos){
            $str .= $page . "　";
        }else{
            $str .= "<a href=\"javascript:goto_page($i)\">$page</a>　";
            }
        $i+=$page_num;
        $page++;
        }
    $tmpl->assign("page_link","$str");
*/
    // 改ページリンク表示
    $tmpl->assign("page_feed",getPageFeedCreHtml($row[0], $pos));

    //実データ取得
    $sql  = "";
    $sql .= "select l.user_id,";		//0
    $sql .= "l.point,";		//1
    $sql .= "l.point_old,";		//2
    $sql .= "l.point - l.point_old,";		//3
    $sql .= "l.upd_mode,";		//4
    $sql .= "date_format(l.cre_date,'%Y/%m/%d %T'),";		//5
    $sql .= "l.cre_id,";		//6
    $sql .= "l.cre_ip, ";		//7
    $sql .= "l.money, ";		//8
    $sql .= "l.money_old, ";	//9
    $sql .= "l.money - l.money_old, ";	//10
    $sql .= "m.flv1 "; // 11
    $sql .= "from female_point_log AS l LEFT JOIN female_member AS m ON l.OWNER_CD=m.OWNER_CD AND l.user_id=m.user_id where $search_str ";
    $sql .= "order by l.point_log_id DESC";
    $sql .= " LIMIT $page_num OFFSET " . $pos ;
    $result = $dbSlave33->query($sql);
    if(DB::isError($result)){
        print $sql;
        }
    $tmpl->loopset('list_loop');
    while ($row = $result -> fetchRow()){
        $tmpl->assign("id",
        $row[0]);
        $tmpl->assign("user_id",$row[0]);
        $tmpl->assign("point_new","$row[1]");
        $tmpl->assign("point_old",$row[2]);
        $tmpl->assign("point_sub",$row[3]);
        $tmpl->assign("cre_date","$row[5]　");
        $tmpl->assign("cre_id","$row[6]　");
        $tmpl->assign("cre_ip","$row[7]　");
        $tmpl->assign("mode",$fobj->getLabelValue("mode_con",$row[4]));
        $tmpl->assign("money_new","$row[8]　");
        $tmpl->assign("money_old","$row[9]　");
        $tmpl->assign("money_sub","$row[10]　");
        $tmpl->assign('flv1', $fobj->getLabelValue("flv1_con",$row[11]));

        $tmpl->loopnext();
        }
    //HTML出力
    $tmpl->flush();
?>
