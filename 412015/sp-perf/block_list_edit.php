<?php
	session_start ();	
    require_once 'common_db.inc';
    require_once 'sp/sp_performer_tmpl.class.inc';
	require_once 'message/MessageContactHelper.php';
	require_once 'message/MessageContact.php';
		
	/**
	 * Default values
	 */
	$userId = $_SESSION['user_id'];	
	$nick = '';
	$img = '';	
	$tmpl = new TmplSPPerformer($sp_performer_dir .'message/message_block_list_edit.html' );	
	$contactHelper = new MessageContactHelper();
	$contacts = new MessageContact();
	
	/**
	 * Block contacts	
	 * Go back to page 1
	 */	
	if(isset($_POST['contact_ids'])) {	  
		$count = count($_POST['contact_ids']);
        $contact_ids = $_POST['contact_ids'];   
        for($i = 0; $i < $count; $i++) {      
        	$id = explode(':', $contact_ids[$i]);    
  			$contacts->unblockPerformer($id[0], $userId, $id[1]);
        }
        unset($_GET['page']);	  
	}
	
	//default block contacts
	$count = $contactHelper->getAllBlockedBy($userId, 2, null);	
	$contact = $contactHelper->paginateContact('getAllBlockedBy', $count, '', 10, 2);	
	
	//Loop $contact array data object	
	if($contact['data']){		
		$tmpl->loopset('all_set');
		foreach($contact['data'] as $row) {
		    $tmpl->assign('contactId', $row->id);
			$tmpl->assign('performerId', $row->user_id);
			$stat = $contactHelper->checkInvalidStatus(1,$row->user_id);
			$nick = $row->nick_name;
			$img = '/imgs/member/320x240/'.$row->img;
			if($stat){
				$nick = '退会したユーザ';
				$img = '/img/noimage.gif';
			}
			$tmpl->assign('name', $nick);
			$tmpl->assign('image', $img);			
			$tmpl->loopnext();			
		}	
		$tmpl->loopset('');

		//set assign nav
		$tmpl->assign('nav', $contact['nav']);
		
	}else{
		$tmpl->assign('result', '1');
	}	
	$tmpl->flush();
	exit();
?>