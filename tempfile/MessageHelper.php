<?
require_once('CommonDB.php');

class MessageHelper {
	
	var $db;
	function MessageHelper() { $db = new CommonDB();}

	/**
	* Get all Messages for a certain user type .
	* @param int $id ID of the user.
	* @param int $type User type. 1 for user. 2 for performer. 
	* @param boolean $hideDeleted If set to true show only undeleted messages else show all messages. 
	* @param unknown $options[] Filter for search contact_name, show unread messages and group_by contact_name+.
	* @return  object[] Array object with table column name properties.
	*/
	function getAllFor($id, $type, $hideDeleted = false, $options = array()) {
		global $ownerCd;
		$filter = '';
		//check where to join for contact name
		if ($type == 1) {
			$toType = 2;
			$contactTable =  'male_member';
		}
		elseif ($type == 2) {
			$toType = 1;
			$contactTable = 'female_profile';
		}
		
		$db->select('message.id, message.owner_cd, message.body, message.image, message.from_type, message.from_id, message.from_date, message.to_type, message.to_id, message.is_sent, message.is_read, message.read_date, '.$contactTable. '.nick_name as contact_name');	
		$db->from('message');
		
		if ($hideDeleted) {
			//options
			foreach ($options as $key =>  $value) {		
				if (is_bool($value)) {
					$condition .= ' AND '.$key.' = '.(int)$value;
				}
				else {
					if($key == 'contact_name') {
						$filter = " AND $key LIKE '%$value%'";
					} 
					elseif ($key == 'group_by') {
						$groupBy = $value;
					}
					 
				}
			}
			
			$db->join(
				'message_delete',
				'message.id <> message_delete.message_id AND message.from_id = ' . $id .' AND message.to_id = '. $id . ' AND from_type = '. $type . ' AND to_type =  '. $toType . 'AND owner_cd ='. $ownerCd . $condition ,
				'inner'
			);
		}
		else {
			$db->where('from_id', $id);
			$db->where('to_id', $id);
			$db->where('from_type', $type);
			$db->where('to_type', $toType);
			$db->where('owner_cd', $ownerCd);
				//options 
				foreach ($options as $key => $value) {
					if (is_bool($value)) {
						$db->where($key, (int)$value);
					}
					else {
						
						if($key == 'contact_name') {
							$db->where($key.' LIKE', "'%$value%'");
						}
						elseif ($key == 'group_by') {
							$groupBy = $value;
						}
					}
				}
		}

		$db->join(
			$contactTable,
			'message.from_id = '.$contactTable.'.user_id AND '.$contactTable.'.owner_cd ='.$ownerCd .' AND '.$contactTable.$filter ,
			'left'	
		);
		
		if ($groupBy) {
			$db->group_by(' group by '.$value);
		}
		return $db->get();
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
			$femaleId = $toId;
		} 
		elseif ($type == 2) {
			$userId = $toId;
			$femaleId = $id;
		}
		
		$db->select('point');
		$db->from('male_point');
		$db->join(
			'male_profile',
			'male_point.user_id = male_profile.user_id AND male_profile.user_id = ' . $userId .' AND male_profile.owner_cd = '.$ownerCd,
			'inner'
		);
		$result = $db->get();
		if ( $row = array_pop($result)) {
			$point = $row->point;
		} 
		else {
			print 'No point data.';
			exit;
		}
			
		$db->select('stat');
		$db->from('onair');
		$db->where('user_id', $femaleId);
		$result = $db->get();
		if ($row = array_pop($result)) {
			$stat = $row->stat;
		} 
		else {
			print 'No stat data.';
			exit;
		}
		
		if ($point >= 0.5 && $stat == 1) {
			return true;
		}
		return false;
	}
	
}