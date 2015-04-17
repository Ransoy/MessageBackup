<?php
require_once 'common_proc.inc';
require_once 'Owner.inc';
require_once 'operator/tmpl2.class_operator.inc';
require_once 'operator/operator.inc';
require_once 'message/MessageContactHelper.php';
require_once 'message/MessageContact.php';

/**
 * Default values
 */
$userId = $_SESSION['user_id'];
//template 
$tmpl = new Tmpl23(OP_PATH . 'template/message/message_block_list_edit.html');
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
	$oldUnread = $tmpl->checkMailBox();
	$tmpl->assign('old_unread', $oldUnread);
	//default block contacts
	$count = $contactHelper->getAllBlockedBy($userId, 2, null);	
	$contact = $contactHelper->paginateContact('getAllBlockedBy', $count, '', 10, 2);	
	
	//Loop $contact array data object	
	if($contact['data']){		
		$tmpl->loopset('all_set');
		foreach($contact['data'] as $row) {
		    $tmpl->assign('contactId', $row->id);
			$tmpl->assign('performerId', $row->user_id);
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