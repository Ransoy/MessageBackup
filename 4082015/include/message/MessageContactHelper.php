<?php

require_once 'CommonDb.php';


class MessageContactHelper{
	var $db;
	function MessageContactHelper(){
		$this->db = new CommonDb();
	}
	
	
	function getAllFor($id, $type, $options = null, $offset = 0, $limit = 0) {
	      $block_contacts = true;
	    //  $type: 1 = user, 2 = performer
        if($type == 1) {
            $profile_type = 'female_profile';
            $profile_type_alias = 'f';
            $type_id = 'user_id';
            $type_on = 'performer_id';
            
            $select = ", f.hash, f.img, f.user_id, f.nick_name";
            
            $joinTable2 = "$profile_type AS $profile_type_alias";
            $joinCondition2 = "$profile_type_alias".".$type_id = contact."."$type_on";
            $joinType2 = 'INNER';
            
            $orderBy = "f.nick_name";
        }
        else {
            $profile_type = 'male_member';
            $profile_type_alias = 'm';
            $type_id = 'performer_id';
            $type_on = 'user_id';
            
            $select = ", m.hash, m1.img, m2.prof_open_flg, m.user_id, m.nick_name";
            
            $joinTable2 = "$profile_type AS $profile_type_alias";
            $joinCondition2 = "$profile_type_alias".".$type_on = contact."."$type_on";
            $joinType2 = 'INNER';
            
            $joinTable3 = "male_profile AS m1";
            $joinCondition3 = "m.user_id = m1.user_id";
            $joinType3= 'INNER';
            
            $joinTable4 = "male_profile2 AS m2";
            $joinCondition4 = "m.user_id = m2.user_id";
            $joinType4= 'LEFT';
            
            $orderBy = "m.nick_name";
        }                
                
        //get user block contacts
        $sql_block_contacts = "SELECT mbc.contact_id FROM message_contact_block AS mbc
                               INNER JOIN message_contact AS mc ON mbc.contact_id = mc.id AND mc."."$type_id = '$id'
                               WHERE mbc.block_by_type = $type";
        
        $select = "contact.id, contact.user_id, contact.performer_id".$select;
        $from = 'message_contact AS contact';
        $where = "contact."."$type_id = '$id' AND contact.owner_cd = 1";
        
        if($options['is_faved'] == 1) {
            $joinTable = 'message_contact_fave AS fave';
            $joinCondition = "fave.contact_id = contact.id AND fave.fave_by_type = $type";
            $joinType = 'INNER';
            $select .= ", contact.id AS fave_id";                   
        }        
        else if($options['is_blocked'] == 1) {
            $joinTable = 'message_contact_block AS block';
            $joinCondition = "block.contact_id = contact.id AND block.block_by_type = $type";
            $joinType = 'INNER';
            $select .= ", contact.id AS block_id";
            $block_contacts = false;
        }
        else if($options['is_from_chat'] == 1) {
          $select .= ", contact.is_from_chat";
          $where .= " AND contact.is_from_chat = 1";
        }        

        
        if($block_contacts) {
          //get user block contacts
          $sql_block_contacts = "SELECT mbc.contact_id FROM message_contact_block AS mbc
                                   INNER JOIN message_contact AS mc ON mbc.contact_id = mc.id AND mc."."$type_id = '$id'
                                           WHERE mbc.block_by_type = $type";
        
          $where .= " AND contact.id NOT IN ($sql_block_contacts)";
        }
                
        $this->db->select($select);
        $this->db->from($from);
        if($joinTable)
            $this->db->join($joinTable, $joinCondition, $joinType);     

        $this->db->join($joinTable2,$joinCondition2,$joinType2);
        
        if($joinTable3)
        	$this->db->join($joinTable3, $joinCondition3, $joinType3);
            $this->db->join($joinTable4, $joinCondition4, $joinType4);
 
        $this->db->where($where);        
        if(count($options)>1)
        	$value = addcslashes($options['name'], '!...?');
            $this->db->like('nick_name',$value,'both');        	

        $this->db->order_by($orderBy,'ASC');
        if($limit)
          $this->db->limit($offset, $limit);  
              	
        $timeStart = $this->db->sqlLogging('MessageContactHelper', 'getAllFor');
        $result = $this->db->get();
        $this->db->sqlLoggingTime($timeStart);
        return $result;       
	}

	function getAllBlockedBy($id, $type, $options=null, $offset = 0, $limit = 0){
		if($options != null){	    
			$optionsArray = array(
					'name'			=>	$options,
					'is_blocked'	=> 1
			);
		}else{
			$optionsArray	=	array(
					'is_blocked'	=>	1
						
			);
		}
		return $toReturn  = $this->getAllFor($id, $type, $optionsArray, $offset, $limit);

	}

	function getAllFavedBy($id, $type,$options=null , $offset = 0, $limit = 0){
		if($options != null){
			$optionsArray	=	array(
					'name'		=>	$options,
					'is_faved'	=>	1
						
			);
		}else{
			$optionsArray	=	array(
					'is_faved'	=>	1
			);
		}
		return $toReturn  = $this->getAllFor($id, $type, $optionsArray, $offset, $limit);
	}
	function getAllFromChatOf($id, $type,$options=null, $offset = 0, $limit = 0){
		if($options != null){
			$optionsArray	=	array(
					'name'		=>	$options,
					'is_from_chat'	=>	1
						
			);
		}else{
			$optionsArray	=	array(
					'is_from_chat'	=>	1
			);
		}

		return $toReturn	=	$this->getAllFor($id, $type, $optionsArray, $offset, $limit);
	}
	
	/**
	 * Paginate Contact list , limit by 10
	 * @param string $funcName - function name to be called
	 * @param int $count_row - total numbers of data
	 * @param string $options - set arguments for the func_name ex: 'getAllFavedBy'
	 * @param int $limit - set limit data to 1 0
	 * @param int $type - 1 = user , 2 = performer :(default = 1)
	 * @param return multiple data[object]
	 *
	 */
	function paginateContact($funcName, $countRow, $options, $limit , $type = 1){
	
		global $tmpl;
	
		// get offset page
		$page = (isset($_GET['page']))? $_GET['page'] : 1 ;
		$getview = (isset($_GET['v']))? '&v='.$_GET['v'] : '' ;
		$q = '&q='.$_GET['q'];
	
		//set offset - starting
		$offset = $limit * ($page - 1);
		$totalRow = count($countRow);
		$count = 0;
		//call function
		$contactResult= $this->$funcName($_SESSION['user_id'], $type, $options, $offset, $limit);
	
		/*
		 * $prev - prevoius of current id
		 * $next - next of current id
		 */
		$prev = $page - 1;
		$next = $page + 1;
	
		//total of number pages
		$totalRow = ceil($totalRow/$limit);
		
		if($totalRow > 1){
	
			// set prev nav if enable / disable
			if($page > 1){
				$tmpl->assign('prev-href',$prev.$getview.$q);
				$tmpl->assign('prev-disable','');
			}else{
				$tmpl->assign('prev-href','#');
				$tmpl->assign('prev-disable','disable');
			}
	
			//Center pages number navigation
			$pagination .= "<div class='cell--center'><ul>";
			if ($_GET['page'] > 4){
				$pagination .= "<li><a href='?page=1$getview$q'>1</a><li>";
				$pagination .= "<li><span>...</span></li>";
				for ($counter = $_GET['page'] - 2; $counter <= $totalRow; $counter++){
					if($count < 5){
						if ($counter == $page){
							$pagination .= "<li class='current'><span>$counter</span></li>";
						}else{
							$pagination .= "<li><a href='?page=$counter$getview$q'>$counter</a></li>";
						}
						$count++;
					}else{
						$pagination.= "<li><span>...</span></li>";
						$pagination.= "<li><a href='?page=$totalRow$getview$q'>$totalRow</a></li>";
						break;
					}
				}
			}else{
			
				for ($counter = 1;$counter <= $totalRow; $counter++){
					if($count < 5){
						if ($counter == $page){
							$pagination .= "<li class='current'><span>$counter</span></li>";
						}else{
							$pagination .= "<li><a href='?page=$counter$getview$q'>$counter</a></li>";
						}
						$count++;
					}else{
						$pagination.= "<li><span>...</span></li>";
						$pagination.= "<li><a href='?page=$totalRow$getview$q'>$totalRow</a></li>";
						break;
					}
				}
			}
			$pagination .= "</div></ul>";
	
			// set next nav if enable / disable
			if ($page < $counter - 1){
				$tmpl->assign('next-href',$next.$getview.$q);
				$tmpl->assign('next-disable','');
			}else{
				$tmpl->assign('next-href','#');
				$tmpl->assign('next-disable','disable');
			}
		}
	
		$result = array(
				'data' => $contactResult,
				'nav' => $pagination
		);
	
		return $result;	
	}
	
	function blockContact($contact, $type) {
	    $data = array('contact_id' => $contact, 'block_by_type' => $type, 'block_date' => date('Y-m-d H:i:s'), 'block_ip' => $_SERVER['REMOTE_ADDR']);	  
        $this->db->setTableName('message_contact_block');
        $timeStart = $this->db->sqlLogging('MessageContactHelper', 'blockContact');
	    $this->db->insert($data);
	    $this->db->sqlLoggingTime($timeStart);
	}
	
	function unblockContact($contact, $type) {
	    $this->db->from('message_contact_block');
	    $this->db->where('contact_id', $contact);
	    $this->db->where('block_by_type', $type);
	    $timeStart = $this->db->sqlLoggingDelete('MessageContactHelper', 'unblockContact');
	    $this->db->delete();
	    $this->db->sqlLoggingTime($timeStart);
	}
		
	function getContactDetail($fromId, $toId, $toType, $fromType, $hash = ''){
		$result = array();
		if($toType != 3){
			if ($toType == 1) {
				$toWhere = 'contact.user_id';
				$fromWhere = 'contact.performer_id';
			} else {
				$toWhere = 'contact.performer_id';
				$fromWhere = 'contact.user_id';
			}
			//male_profile2.prof_open_flg
			$this->db->select('contact.id, fave.contact_id as fave_id, block.contact_id AS block_id');
			$this->db->from('message_contact AS contact');
			$this->db->join('message_contact_fave AS fave','fave.contact_id = contact.id AND fave.fave_by_type = ' . $fromType,'left');
			$this->db->join('message_contact_block AS block', 'block.contact_id = contact.id AND block.block_by_type = ' . $fromType, 'left');
				
			$this->db->wherev2("$fromWhere = ? AND $toWhere = ?", array($fromId, $toId));
			$timeStart = $this->db->sqlLogging('MessageContactHelper', 'getContactDetail');
			$result = $this->db->get_rowv2();
			$this->db->sqlLoggingTime($timeStart);
		} else {
			$this->db->select("ADMIN_ID as 'to_id', name as 'nick_name'");
			$this->db->from('admin');
			$this->db->where("hash = '$hash'");
			$timeStart = $this->db->sqlLogging('MessageContactHelper', 'getContactDetail');
			$result = $this->db->get_row();
			$this->db->sqlLoggingTime($timeStart);
			if ($result) {
				$result['img'] = '/images/message/ui/ic_cs.png';
			}
		}
		
		return $result;
	}
	
	function checkStatus($hash){
		$this->db->select('stat');
		$this->db->from('male_member');
		$this->db->wherev2(' hash = ? AND (stat <> ? OR stat <> ?)',array($hash,5,9));
		$timeStart = $this->db->sqlLogging('MessageContactHelper', 'checkStatus');
		$result = $this->db->get_rowv2();
		$this->db->sqlLoggingTime($timeStart);
		return $toReturn = ($result) ? true : false ;
	}
	
	
	function checkInvalidStatus($to_type, $id) {
		$this->db->select('stat');
		if ($to_type == 1) {
			$this->db->from('male_member');
		} else {
			$this->db->from('female_member');
		}
		$this->db->where("user_id = '$id'");
		$this->db->where("(stat = 6 OR stat = 9)");
		
		$timeStart = $this->db->sqlLogging('MessageContactHelper', 'checkStatus');
		$result = $this->db->get_row();
		$this->db->sqlLoggingTime($timeStart);
		return $result;
	}
	
	function getUserId($hash, $type) {
		$prefix;
		if ($type == 1) {
			$prefix = 'm';
			$this->db->select("$prefix.user_id as to_id, mp2.prof_open_flg, $prefix.nick_name, mp.img");
			$this->db->from('male_member as ' . $prefix);
			$this->db->join('male_profile mp', "$prefix.user_id = mp.user_id", 'left');
			$this->db->join('male_profile2 mp2', "mp2.user_id = $prefix.user_id", 'left');
		} else {
			$prefix = 'f';
			$this->db->select("$prefix.user_id as to_id, $prefix.nick_name, $prefix.img");
			$this->db->from('female_profile as ' . $prefix);
		}
		$this->db->wherev2($prefix.'.hash = ?', array($hash));
		$timeStart = $this->db->sqlLogging('MessageContactHelper', 'getUserId');
		$result = $this->db->get_rowv2();
		$this->db->sqlLoggingTime($timeStart);
		return $result;
	}
	
	
}



