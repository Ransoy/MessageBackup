<?
require_once('CommonDb.php');

class MessageHelperTest {
	
	var $db;
	
	function MessageHelperTest() { $this->db = new CommonDB();}

	/**
	* Get all Messages for a certain user type .
	* @param int $id ID of the user.
	* @param int $type User type. 1 for user. 2 for performer. 
	* @param boolean $hideDeleted If set to true show only undeleted messages else show all messages. 
	* @param unknown $options[] Filter for search contact_name, show unread messages and group_by contact_name.
	* @return  object[] Array object with table column name properties.
	*/
	function getAllFor($id, $type, $hideDeleted = false, $options = array()) {
	    $filterUser = ' ((message.to_id = \''.$id.'\' AND message.is_sent = 1) OR message.from_id = \''.$id.'\') AND message.owner_cd = 1 ';
		$sql = '';
		$join = '';
		$group = '';
		$order = '';
		$table = '';
		$limit = '';
		if ($type == 1) {
			$table = 'female_profile';	
		}
		elseif ($type == 2) {
			$table = 'male_member';
		}
		/**
		 * loop for options
		 */
		foreach ($options as $key => $value) {
			if ($key == 'is_read') {
				$isRead = ' AND (message.is_read = '. (int)$value .' AND to_id = \''.$id.'\')';
			}
			elseif ($key == 'filter') {
				$value = addslashes(strtolower($value));
				$filter = ' AND LOWER('. $table.".nick_name) LIKE '%".$value.'%\' ';
			}
			elseif ($key == 'group_by') {
				$groupBy = $value;
			}
			elseif ($key == 'order_by') {
				$orderBy = $value;
			}
			elseif ($key == 'limit') {
				$limit = ' LIMIT '. $value;
			}
		}
		
		$select = 'SELECT message.id, message.owner_cd, message.body, message.image, message.from_type, message.from_id, message.from_ip,
						 message.from_date, message.to_type, message.to_id, message.is_sent, message.is_read, message.read_date, '.$table.'.nick_name as contact_name';
		$from = ' FROM message';
		
		if ($hideDeleted) {
			$where = ' WHERE message.id NOT IN (SELECT message_id FROM message_delete WHERE del_by_type = '.$type.') AND '.$filterUser. $isRead;
			$filterUser = '';
			$isRead = '';
		} else {
			$where = ' WHERE '.$filterUser.$isRead;
			$filterUser = '';
			$isRead = '';
		}
				
		if ($orderBy) {
			$order = ' ORDER BY '.$orderBy;
		}
		
		//append for querying 
		$sql = $select.$from.$join;
		$sql .= ' INNER JOIN '.$table.' ON (message.to_id = '.$table.'.user_id OR message.from_id = '.$table.'.user_id)  ';
		if ($filter) {
			$sql .= $filter;
		}
		
		$sql .= $where.$group.$order;	
		
		if ($groupBy) {
			$sql = 'SELECT * FROM ('. $sql .') as tb1 GROUP BY '.$groupBy. ' ORDER BY from_date desc '. $limit; 
		}
		//echo $sql;
		return $sql;
	}
	
	/**
	 * Get unread messages only.
	 * @param int $id Sender id
	 * @param int $type Sender member type 1 for user and 2 for performer.
	 * @param unknown $options[]  Filter for search contact_name or show unread messages.
	 * @return object[] Array object with table column name properties.
	 */
	function getAllUnreadFor($id, $type, $options) {
		$options['is_read'] = false;
		return $this->getAllFor(
			$id,
			$type, 
			true,
			$options
		);
	}
	
	/**
	 * Check if user or performer can send message.
	 * @param int $id Sender ID
	 * @param int $type User type 1for user and  2 for  performer
	 * @param int $told Receiver ID
	 * @return boolean
	 */
	function canSend($id, $type, $told) {
		global $ownerCd;
		if ($type == 1) {
			$userId = $id ;
			$femaleId = $told;
		} 
		elseif ($type == 2) {
			$userId = $told;
			$femaleId = $id;
		}
		//check user valid id.
		$this->db->select('count(1) as count');
		$this->db->from('male_profile');
		$this->db->where('user_id', '\''.$userId.'\'');
		$this->db->where('owner_cd', $ownerCd);
		//$user = $this->db->get();
		echo $this->db->checkQuery();
		/*if ($user[0]->count == 0) {
			return false;
		}*/
		
		//check performer valid id.
		$this->db->select('count(1) as count');
		$this->db->from('female_profile');
		$this->db->where('user_id', '\''.$femaleId.'\'');
		$this->db->where('owner_cd', $ownerCd);
		echo '<br/>';
		echo $this->db->checkQuery();
		/*$performer = $this->db->get();
		if ($performer[0]->count == 0) {
			return false;
		}*/
		
		$this->db->select('point');
		$this->db->from('male_point');
		$this->db->where('user_id', '\''.$userId.'\'');
		$this->db->where('owner_cd', $ownerCd);
		/*$point = $this->db->get();

		if ($point[0]->point < 0.5) {
			return false;
		}
		return true;*/
		echo '<br/>';
		echo $this->db->checkQuery();
	}
	
	/**
	 * Count unread message of the user.
	 * @param string $toId
	 * @param string $fromId
	 * @return int
	 */
	function countUnreadMessage($toId, $type, $fromId) {
		global $ownerCd;
		$sql = 'select count(1) as count from message where (to_id = \''.$toId.'\' AND from_id = \''.$fromId.'\') AND 
				 is_read = 0 AND owner_cd = '.$ownerCd.' AND is_sent = 1 AND to_type = '.$type.' AND
				id NOT IN(SELECT message_id FROM message_delete WHERE del_by_type = '.$type.')  LIMIT 1';
		$result = $this->db->query($sql);
		if ($row = $result->fetchRow()) {
			return $row['count'];
		}
	}
	
	/**
	 * Get sender info 
	 * @param int $id
	 * @param int $type
	 * @return object
     */
	function getUserInfo($id, $type) {
		global $ownerCd;
		if ($type == 1) {
			$prefix = 'f';
			$this->db->select('f.img, f.hash');
			$this->db->from('female_profile as '.$prefix);
		}
		elseif ($type == 2) {
			$prefix = 'm';
			$this->db->select('mf.img, m.hash');
			$this->db->from('male_profile as mf');
			$this->db->join('male_member as '.$prefix,'m.user_id = mf.user_id','left');
		}
		$this->db->where($prefix.'.user_id' , '\''.$id.'\'');
		$this->db->where($prefix.'.owner_cd', $ownerCd);
		/*$result = $this->db->get();
		return array_pop($result);*/
		echo $this->db->checkQuery();
	}

	/**
	 * Get performer status if online or chatting
	 */
	function checkPerformerStatus($id) {
		$this->db->select('stat');
		$this->db->from('onair');
		$this->db->where('user_id' , '\''.$id.'\'');
		$result = $this->db->get();
		$result = array_pop($result);
		return $result->stat;
	}
	
	/**
	 * Delete inbox.
	 * @param array $receiverIds
	 * @param int $type
	 * @param int $userId
	 * @return boolean
	 */
	function deleteInbox($receiverIds, $type, $userId) {	    
		global $ownerCd;
		$success = true;
		$this->db->setTableName('message_delete');
		$countId = count($receiverIds);
        for($i = 0; $i < $countId; $i++) {
            $receiverId = $receiverIds[$i];
            /**
             * Query IDs of the message you want to delete.
             */
         	$sql = 'SELECT id FROM message WHERE ((from_id = \''.$userId.'\' AND to_id = \''.$receiverId.'\') OR 
				(to_id = \''.$userId.'\' AND from_id = \''.$receiverId.'\')) AND owner_cd = '.$ownerCd.' AND is_sent = 1  AND
			 	id NOT IN (Select message_id from message_delete where del_by_type = '.$type.')';
         	
           $result = $this->db->query($sql);
            /**
             * Insert ID to the message_delete table.
            */
			while ($row = $result->fetchRow()) {
				$success = $this->db->insert(array('message_id'=> $row['id'], 'del_by_type' => $type));
			}
        }
        return $success;
	}
	
	function checkMessage($toId, $fromId) {
		global $ownerCd;
		$this->db->select('id, body, image, from_date');
		$this->db->from('message');
		$this->db->where("to_id = '$toId' AND from_id = '$fromId' AND is_read = 0 AND is_sent = 1 AND owner_cd = $ownerCd");
		return $this->db->get();
	}
	
} ?>


