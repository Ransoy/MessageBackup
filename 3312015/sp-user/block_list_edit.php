<?php

	session_start ();
	require_once 'Owner.inc';
	require_once 'common_proc.inc';
	require_once 'db_con.inc';
	require_once 'sp/boy_login.inc';
	require_once 'sp/boy_sp.inc';
	require_once 'sp/tmpl2.class_ex.inc';
	require_once 'sp/common_proc.inc';
	require_once 'mc_session_routines.inc';	
	require_once 'message/MessageContactHelper.php';
	require_once 'message/MessageContact.php';
	require_once 'imacherie_male.inc';
	
	/**
	 * Default values
	 */
	$userId = $_SESSION['user_id'];		
	$tmpl = new Tmpl22($sp_tmpl_dir .'message/message_block_list_edit.html' );	
	
	$myInfo = myInformation();
	$oldUnread = $myInfo['midoku_num'];
	$tmpl->assign('old_unread', $oldUnread);
	
	$contactHelper = new MessageContactHelper();
	$contacts = new MessageContact();
	
	
	/**
	 * Block contacts	
	 * Go back to page 1
	 */	
	if(isset($_POST['contact_ids'])) {	   
       $ids = $_POST['contact_ids'];   
       $count = count($ids);
        for($i = 0; $i < $count; $i++) {
        	$id = explode(':', $ids[$i]);            
            $contacts->unblockUser($id[0],$userId, $id[1]);
        }
        unset($_GET['page']);	  
	}
	
	
	//default block contacts
	$contact = $contactHelper->getAllBlockedBy($userId, 1, null);	
	$contact = $contactHelper->paginateContact('getAllBlockedBy', $contact, '', 10, 1);	
	
	//Loop $contact array data object	
	if($contact['data']){		
		$tmpl->loopset('all_set');
		foreach($contact['data'] as $row) {
	
		    $tmpl->assign('contactId', $row->id.':'.$row->performer_id);
			$tmpl->assign('id', $row->hash);
			$tmpl->assign('name', $row->nick_name);
			$tmpl->assign('image', $row->img);			
			$tmpl->loopnext();			
		}	
		$tmpl->loopset('');

		//set assign nav
		$tmpl->assign('nav', $contact['nav']);
		
	}else{
		$tmpl->assign('result', '1');
	}	
	$tmpl->flush();
?>