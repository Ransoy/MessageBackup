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
	$address_btn   = "�ɲ�";
	$setting_btn   = "����";
	$prev_link     = "����50��";
	$next_link     = "����50��";
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
	//���������
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
				
				//ʣ�������
				if(is_array($_POST['mail_id'])){
					foreach($_POST['mail_id'] as $key => $value){
						$result = $dbMaster->execute($sth, $value);
					}
				//1������
				}else{
						$result = $dbMaster->execute($sth, $_POST['mail_id']);
				}
			}
			if(isset($_POST['mailx_id'])){
				if(is_array($_POST['mailx_id'])){
					//ʣ�������
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
							$msg = "����������᡼�뤬¸�ߤ��ޤ���Ǥ���";
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
							$msg = "����������᡼�뤬¸�ߤ��ޤ���Ǥ���";
							$tmpl->assign("msg" , $msg );
							$tmpl->assign("agent_code", $agent_code_con);
							setDairitenParam($tmpl);
							$tmpl->flush();
							exit;
						}
					}
				}else{
					//1������
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
						$msg = "����������᡼�뤬¸�ߤ��ޤ���Ǥ���";
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
						$msg = "����������᡼�뤬¸�ߤ��ޤ���Ǥ���";
						$tmpl->assign("msg" , $msg );
						$tmpl->assign("agent_code", $agent_code_con);
						setDairitenParam($tmpl);
						$tmpl->flush();
						exit;
					}
				}
			}
		    $tmpl = new Tmpl22($tmpl_dir . "operator/mail_new/receive_end{$template_name}.html");
			$msg  = "������ޤ�����";
			$tmpl->assign("msg" , $msg );
			$tmpl->assign("agent_code", $agent_code_con);
			setDairitenParam($tmpl);
			$tmpl->flush();
			exit;
		}
	}
	
	//------------------
	//���ɥ쥹Ģ��Ͽ
	//------------------
	if(isset($_POST['mode'])){
		if($_POST['mode'] == "insert"){
			//male_user_id����
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
			
			//���ɥ쥹��Ͽ���Ƥ��뤫���ǧ
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
			
			//��Ͽ���ʤ���Х��ɥ쥹Ģ����Ͽ����
			if($result->numRows() < 1){
				$data = array();
				$data['owner_cd']     = $ownerCd;
				$data['user_id']      = $_SESSION['user_id'];
				$data['male_user_id'] = $user_id;
				$data['address_type'] = 0;
				$data['memo']         = "";
				iTSinsert($data,"female_address_book",$_SESSION['user_id']);
			//��Ͽ�����ꡢaddress_type��9(���)�ξ��Ϲ�������
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
			$msg  = "���ɥ쥹Ģ���ɲä��ޤ�����";
			$tmpl->assign("msg" , $msg );
			$tmpl->assign("agent_code", $agent_code_con);
			setDairitenParam($tmpl);
			$tmpl->flush();
			exit;
		}
	}
	
	//------------------
	//����������Ͽ
	//------------------
	if(isset($_POST['mode'])){
		if($_POST['mode'] == "deny"){
			//female_user_id����
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
			
			//���ɥ쥹��Ͽ���Ƥ��뤫���ǧ
			//�����AND address_type = 2
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
			

			//��Ͽ���ʤ���Х��ɥ쥹Ģ����Ͽ���롣������е��ݤ�UPDATE���롣
			//������������ address_type �� former_type �إ��åȤ���
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
			$msg  = "����������ɲä��ޤ�����";
			$tmpl->assign("msg" , $msg );
			$tmpl->assign("agent_code", $agent_code_con);
			setDairitenParam($tmpl);
			$tmpl->flush();
			exit;
		}
	}
	
	//-----------------------------------------------------
	//SQL�������
	//-----------------------------------------------------
	$search_str = "AND female_mailbox.cre_date <= now() ";
	$search_str_mailmag = "AND female_mailmag.send_time <= now() ";
	if(isset($_GET['midoku'])){
		//̤�ɥ᡼��Τߤ�ɽ��
		$search_str .= "AND female_mailbox.stat2 = 1";
		$search_str_mailmag .= "AND female_mailbox_mailmag.stat2 = 1";
		$tmpl->assign("midoku1","&midoku");		//̤�ɤΤ�ɽ����GET�ǡ����򥻥å�
		
	}else{
		//̤�ɡ����ɥ᡼���ɽ��
		$search_str .= "AND female_mailbox.stat2 <> 9 ";
		$search_str .= "AND female_mailbox.stat2 <> 5";
		$search_str_mailmag .= "AND female_mailbox_mailmag.stat2 <> 5 AND female_mailbox_mailmag.stat2 <> 9";
		$tmpl->assign("midoku1","");
	}

	//-----------------------------------------------------
	//�¤��ؤ�����
	//-----------------------------------------------------
	//�¤��ؤ����
	$sort = "date desc";
	$tmpl->assign("icon","<img src=\"images/icon_down.gif\">");
	$tmpl->assign("sortc","desc");

	//�¤��ؤ��ѹ�
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
	//���ڡ����������ɽ������Ⱥ���ɽ������ڡ����������
	//-----------------------------------------------------
	$page_num = 50;	//���ڡ����������ɽ�����
	if(!isset($_POST['pos'])){
		//mail.php����ξ��
		if (isset($_GET['pos'])) {
			$pos = $_GET['pos'];
		} else {
			$pos = 1;
		}
	}else{
		if($_POST['pos'] == ""){
			$pos = 1;
		}else{
			$pos = $_POST['pos'];	//N�ڡ����ܤ�ɽ��
		}
	}

	//-----------------------------------------------------
	//SQL�Υ�ߥå��ͤȥ��ե��å��ͤ򥻥å�
	//-----------------------------------------------------
	$limit = $page_num + 1;		//ɽ�����+1
	$offset = ($pos - 1) * $page_num;		//���Ф��ǡ������

	//-----------------------------------------------------
	//�����᡼���������
	//-----------------------------------------------------
	
	$flg_male_user="";
	
	if(isset($_REQUEST['male_id']) && $_REQUEST['male_id']!="" ){
		if( preg_match("/^[0-9a-zA-Z]+$/i", $_REQUEST['male_id']) ){//hash�äݤ���ĤΤߤ����Ĥ���
			$flg_male_user=$_REQUEST['male_id'];
		}
	}


	//���ݼԤΥϥå�����������
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
	
	
	
	
	$sql = "SELECT female_mailbox.mail_id AS mail_id,";		//0 �᡼��ID
	$sql .= "	female_mailbox.subject,";			//1 ��̾
	$sql .= "	female_mailbox.stat2,";				//2 
	$sql .= "	date_format(female_mailbox.cre_date,'%Y-%m-%d %H:%i'),";	//3 ����
	$sql .= "	mm.nick_name,";						//4 �����˥å��͡���
	$sql .= "	mm.hash,";							//5 �����ϥå���
	$sql .= "	female_mailbox.resend_id,";			//6 �ֿ��᡼��γ�ǧ���ֿ��Ѥʤ��ֿ������᡼���mail_id����̤�ֿ��ʤ�NULL�������
	$sql .= "	female_mailbox.cre_date AS date,";	//7 �¤��ؤ��ΰ�
	$sql .= "	t3.owner_cd,";						//8 ������������Ͽ��ǧ����Ͽ�Ѥʤ飱����̤��Ͽ�ʤ�NULL�������
	$sql .= "	t4.owner_cd, ";						//9 ����������Ͽ��ǧ����Ͽ�Ѥʤ飱����̤��Ͽ�ʤ�NULL�������
	$sql .= "	p2.prof_open_flg, ";				//10
	$sql .= "	0 ";								//11 ���ޥ��ե饰
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
		                       '�ޥ������̳��',
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
	
	$cnt = 0;//�롼�ץ�����
	while($row = $result->fetchRow()){
		$cnt++;
		
		if ($cnt > $page_num) {
			$tmpl->loopset("");
			break;//51���ܤΥǡ����ϼ��Υǡ��������뤫�γ�ǧ�ѤʤΤǽ���������
		}
		
		$tmpl->assign( "mail_id" , $row[0] );
		$tmpl->assign("hash" , $row[5] );
		$tmpl->assign( "cre_date" , $row[3] );
		
		if(!isset($row[1]) || $row[1] == "") {
			$row[1] = "[��̾�ʤ�]";
		}
		
		//�ܥ����ɽ��
		if($row[8] == 1){
			//���ɥ쥹Ģ��Ͽ��
			$tmpl->assign( "okiniiri" , "" );
		}else{
			//���ɥ쥹Ģ̤��Ͽ
			$tmpl->assign("okiniiri","<input type=\"button\" value=\"{$address_btn}\" onClick=\"go_address('$row[5]',{$pos})\">" );
		}
		if($row[9] == 1){
			//����������Ͽ��
			$tmpl->assign("kyohi","");
		}else{
			//��������̤��Ͽ
			$tmpl->assign("kyohi","<input type=\"button\" value=\"{$setting_btn}\" onclick=\"go_deny('$row[5]',{$pos})\">" ) ;
		}

		if($row[2] == 1 || ($row[11] == "1" && is_null($row[2]))){
			//̤��
			$tmpl->assign( "cre_date" , "<b>" . $row[3] ."</b>" );
			
			// ���ޥ��ե饰
			if($row[11] == "1"){
				$tmpl->assign( "subject" ,  "<a href=mail.php?mailx_id=".$row[0]."&pos=".$pos."&".$dairi_param."><b>". htmlspecialchars($row[1]) ."</b></a>");
			}else{
				$tmpl->assign( "subject" ,  "<a href=mail.php?mail_id=".$row[0]."&pos=".$pos."&".$dairi_param."><b>". htmlspecialchars($row[1]) ."</b></a>");
			}
			
			//�ץ�ե����ץ�ե饰
			if($row[10] == "1"){
				$tmpl->assign( "from_nick_name" ,  "<b><a href=\"javascript:func_detail('{$row[5]}')\">".$row[4] ."</a></b>");
			}else{
				$tmpl->assign( "from_nick_name" ,  "<b>" .$row[4] ."</b>");
			}
			
			$tmpl->assign( "img" ,  "<img src=\"images/icon_midoku.gif\" width=\"20\" height=\"15\" alt=\"̤�ɥ᡼��\" title=\"̤�ɥ᡼��\">");
			$tmpl->assign( "jyoutai" , "midoku");		//��
		}else{
			//����
			$tmpl->assign( "cre_date" , $row[3] );
			
			// ���ޥ��ե饰
			if($row[11] == "1"){
				$tmpl->assign( "subject" , "<a href=mail.php?mailx_id=".$row[0]."&pos=".$pos."&".$dairi_param.">" .htmlspecialchars($row[1])."</a>");
			}else{
				$tmpl->assign( "subject" , "<a href=mail.php?mail_id=".$row[0]."&pos=".$pos."&".$dairi_param.">" .htmlspecialchars($row[1])."</a>");
			}
			
			//�ץ�ե����ץ�ե饰
			if($row[10] == "1"){
				$tmpl->assign( "from_nick_name" , "<a href=\"javascript:func_detail('{$row[5]}')\">".$row[4] ."</a>") ;
			}else{
				$tmpl->assign( "from_nick_name" , "" .$row[4] ."") ;
			}
			
			// �ֿ�������
			if(!$row[6]){
				$tmpl->assign("img", "<img src=\"images/icon_kidoku.gif\" width=\"20\" height=\"15\" alt=\"���ɥ᡼��\" title=\"���ɥ᡼��\">");
				$tmpl->assign("jyoutai","kidoku");
			}else{
				//�ֿ����Ƥ�ɽ��
				$tmpl->assign("img" , "<a href=mail_resend.php?mail_id=".$row[6]."&".$dairi_param."><img src=\"images/icon_return.gif\" width=\"20\" height=\"15\" alt=\"�ֿ��᡼�����Ƥ�ɽ��\" title=\"�ֿ��᡼�����Ƥ�ɽ��\">");
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
	//�ڡ�����󥯽���
	//--------------------------------------
	//1�ڡ����ܤ�ɽ��������
	if ($pos == 1) {
		//1�ڡ�����ɽ���ʤΤǡ����ڡ�����󥯤Ϥʤ�	
		$tmpl->assign("prev_link","");
		
		if ($cnt > $page_num) {
			$tmp = $pos + 1;
			$tmpl->assign("next_link","��<a href=\"javascript:goto_page($tmp,'$flg_male_user')\">{$next_link}</a>��");
		}else{
			//�ǡ��������51��̤���ʤΤǡ����ڡ�����󥯤ʤ�
			$tmpl->assign("next_link","");
		}
	}else{//1�ڡ����ܰʳ���ɽ��������
		//1�ڡ����ܰʳ��ʤ顢ɬ�����Ϥ��롣
		$tmp = $pos - 1;
		$tmpl->assign("prev_link","��<a href=\"javascript:goto_page($tmp,'$flg_male_user')\">{$prev_link}</a>��");
		
		if ($cnt > $page_num) {
			$tmp = $pos + 1;
			$tmpl->assign("next_link","��<a href=\"javascript:goto_page($tmp,'$flg_male_user')\">{$next_link}</a>��");
		} else {
			//�ǡ��������51��̤���ʤΤǡ����ڡ�����󥯤ʤ�
			$tmpl->assign("next_link","");
		}
	}

	$tmpl->assign("agent_code", $agent_code_con);
	setDairitenParam($tmpl);
	$tmpl->flush();

?>