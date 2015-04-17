<?
require_once('CommonDb.php');

class MessageHelper {
	
	var $db;
	
	function MessageHelper() { $this->db = new CommonDB();}

	function getAdmin($fromId, $read = 0, $keyword = '', $hideDeleted = false) {
		$whereRead = ($read == 0)? '' : "(is_read = 0 AND to_id = '$fromId') AND";
		$selectFROM  = "SELECT CASE WHEN from_id = '$fromId' THEN to_id ";
		$selectFROM .= "WHEN to_id = '$fromId' THEN from_id END AS admin_id, body, from_date, image, id, is_read, to_id, from_id FROM message m ";
		$selectFROM .= "WHERE $whereRead ((from_type = 3 AND to_id = '$fromId') OR (to_type = 3 AND from_id = '$fromId')) ";
		$selectFROM .= (!$hideDeleted) ? 'AND (NOT EXISTS (SELECT md.id FROM message_delete as md WHERE md.message_id = m.id AND md.del_by_type = m.to_type))' : '';
		$selectFROM .= "ORDER BY from_date DESC, id DESC";
		
		if ($keyword != '') {
			$keyword = addcslashes($keyword, '!...?');
			$this->db->join("admin as a", "a.ADMIN_ID = msg.admin_id", 'INNER');
			$this->db->where("a.name like '%$keyword%'");
		}
		
		$this->db->select('msg.admin_id, msg.body, msg.from_date, msg.from_id');
		$this->db->from("($selectFROM) as msg");
		/*if (!$hideDeleted) {
			$this->db->where("(NOT EXISTS (SELECT md.id FROM message_delete as md WHERE md.message_id = msg.id AND del_by_type = msg.to_type))");
		}*/
		$this->db->group_by('msg.admin_id');
		$this->db->order_by('msg.from_date DESC, msg.id', 'DESC');
		$timeStart = $this->db->sqlLogging('MessageHelper', 'getAdmin');
		$result = $this->db->get();
		$this->db->sqlLoggingTime($timeStart);
		return $result;
	}
	
	function getAdmin2($fromId, $read = 0, $keyword = '', $hideDeleted = false) {
		$this->db->select("m.from_id as admin_id, m.body, m.from_date");
		$this->db->from('message as m');
		$this->db->where("m.to_id = '$fromId' and m.from_type = 3");
		
		if (!$hideDeleted) {
			$this->db->where('NOT EXISTS (SELECT md.id FROM message_delete md WHERE md.message_id = m.id AND md.del_by_type = m.to_type)');
		}
		if ($keyword != '') {
			$keyword = addcslashes($keyword, '!...?');
			$this->db->where("'マシェリスタッフ' like '%$keyword%'");
		}
		if ($read == 1) { 
			$this->db->where("m.is_read = 0 AND  m.to_id = '$fromId'");
		}
		
		$this->db->order_by('m.from_date DESC, m.id', 'DESC');
		$this->db->limit(1);
		$timeStart = $this->db->sqlLogging('MessageHelper', 'getAdmin');
		$result = $this->db->get();
		$this->db->sqlLoggingTime($timeStart);
		return $result;
	}
	
	
	function getAllFor($fromId, $fromType, $offset = 0, $limit = 0, $keyword ='', $read = '1', $hideDeleted = false) {
		$contactWhere = '';
		$contactGroupBy = '';
		$profileTable = '';
		$hideDelete = '';
		switch($fromType) {
			case 1 :
				$contactWhere = 'c.user_id';
				$contactGroupBy = 'c.performer_id';
				$profileTable = 'female_profile' ;
				break;
			case 2 :
				$contactWhere = "c.performer_id";
				$contactGroupBy = 'c.user_id';
				$profileTable = 'male_member';
				break;
		}
		if (!$hideDeleted) {
			$hideDelete = " AND (NOT EXISTS (SELECT md.id FROM message_delete as md WHERE md.message_id = m.id AND del_by_type = $fromType))";
		}
		$whereRead = ($read == 1)? '' : "(m.is_read = 0 AND m.to_id = '$fromId') AND";
	
		$this->db->select("SQL_CALC_FOUND_ROWS c.id, m.body, m.image, m.from_date, m.from_id, m.to_id");
		$this->db->from('message_contact c');
		
		$joinTable = "(SELECT m.id, m.body, m.image, m.from_date, m.from_id, m.to_id, m.from_type FROM message m where $whereRead (m.is_sent = 1 OR m.from_type = $fromType) $hideDelete ORDER BY m.from_date DESC, m.id DESC) m";
		$condition = "(c.performer_id = m.from_id AND c.user_id = m.to_id) OR (c.performer_id = m.to_id AND c.user_id = m.from_id)";
		
		$this->db->join($joinTable, $condition, 'INNER');
		if ($keyword != '') {
			$keyword = addcslashes($keyword, '!...?');
			$this->db->join("$profileTable as prof", "prof.user_id = $contactGroupBy", 'INNER');
			$this->db->where("prof.nick_name like '%$keyword%'");
		}
		$this->db->where("$contactWhere = '$fromId'");
		$this->db->where("(($contactGroupBy <>  'fmmanager' AND $contactGroupBy <> 'mmmanager' AND $contactGroupBy <> 'systemcall' AND $contactGroupBy <> 'telecom') OR m.from_type <> 3 )");
		$this->db->where("c.id NOT IN (SELECT contact_id FROM message_contact_block WHERE block_by_type = $fromType)");
		$this->db->group_by($contactGroupBy);
		$this->db->order_by('m.from_date DESC, m.id', 'DESC');
		if ($limit != 0) {
			$this->db->limit($offset, $limit);
		}
		$timeStart = $this->db->sqlLogging('MessageHelper', 'getAllFor');
		$result = $this->db->get();
		$this->db->sqlLoggingTime($timeStart);
		return $result;
	}
	
	function countAllFor() {
		$this->db->select('FOUND_ROWS() as total');
		$timeStart = $this->db->sqlLogging('MessageHelper', 'countAllFor');
		$result = $this->db->get_row();
		$this->db->sqlLoggingTime($timeStart);
		return $result;
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
			
			$this->db->select('point');
			$this->db->from('male_point');
			$this->db->where('user_id', '\''.$userId.'\'');
			$this->db->where('owner_cd', $ownerCd);
			$timeStart = $this->db->sqlLogging('MessageHelper', 'canSend point');
			$point = $this->db->get();
			$this->db->sqlLoggingTime($timeStart);
			$minPoint = $this->getMinPoint($userId);
			
			if ($point[0]->point < $minPoint) {
				return false;
			}
			
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
		$timeStart = $this->db->sqlLogging('MessageHelper', 'canSend user');
		$user = $this->db->get();
		$this->db->sqlLoggingTime($timeStart);
		if ($user[0]->count == 0) {
			return false;
		}
		
		//check performer valid id.
		$this->db->select('count(1) as count');
		$this->db->from('female_profile');
		$this->db->where('user_id', '\''.$femaleId.'\'');
		$this->db->where('owner_cd', $ownerCd);
		$timeStart = $this->db->sqlLogging('MessageHelper', 'canSend performer');
		$performer = $this->db->get();
		$this->db->sqlLoggingTime($timeStart);
		if ($performer[0]->count == 0) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Count unread message of the user.
	 * @param string $toId
	 * @param string $fromId
	 * @return int
	 */
	
	function countUnreadMessage($toId, $type, $fromId, $is_admin = '') {
		global $ownerCd;
		if ($is_admin == '') {
			$sql = 'select count(1) as count from message where (to_id = \''.$toId.'\' AND from_id = \''.$fromId.'\') AND 
					 is_read = 0 AND owner_cd = '.$ownerCd.' AND is_sent = 1 AND to_type = '.$type.' AND
					id NOT IN(SELECT message_id FROM message_delete WHERE del_by_type = '.$type.')  LIMIT 1';
		} else {
			$sql = 'select count(1) as count from message where (to_id = \''. $toId . '\' AND from_type = 3) AND is_read = 0 AND owner_cd = ' . $ownerCd.
			 		' AND is_sent = 1 AND to_type = ' .$type. ' AND id NOT IN(SELECT message_id FROM message_delete WHERE del_by_type = '.$type.') LIMIT 1';
		}
		$timeStart = $this->db->sqlLogging('MessageHelper', 'countUnreadMessage', $sql);
		$result = $this->db->query($sql);
		$this->db->sqlLoggingTime($timeStart);
		/*if ($row = $result->fetchRow()) {
			return $row['count'];
		}*/
		foreach ($result as $row) {
			return $row->count;
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
			$this->db->select('f.img, f.hash, f.nick_name as contact_name');
			$this->db->from('female_profile as '.$prefix);
			$this->db->where($prefix.'.user_id' , '\''.$id.'\'');
		}
		elseif ($type == 2) {
			$prefix = 'm';
			$this->db->select('mf.img, m.hash, m.nick_name as contact_name');
			$this->db->from('male_profile as mf');
			$this->db->join('male_member as '.$prefix,'m.user_id = mf.user_id','left');
			$this->db->where($prefix.'.user_id' , '\''.$id.'\'');
		} else if ($type == 3) {
			$prefix = 'a';
			$this->db->select('a.hash, a.name as admin_name');
			$this->db->from('admin as ' . $prefix);
			$this->db->where("$prefix.ADMIN_ID = '$id'");
		}
		
		$this->db->where($prefix.'.owner_cd', $ownerCd);
		$timeStart = $this->db->sqlLogging('MessageHelper', 'getUserInfo');
		$result = $this->db->get();
		$this->db->sqlLoggingTime($timeStart);
		return array_pop($result);
	}

	/**
	 * Get performer status if online or chatting
	 */
	function checkPerformerStatus($id) {
		$this->db->select('stat');
		$this->db->from('onair');
		$this->db->where('user_id' , '\''.$id.'\'');
		$timeStart = $this->db->sqlLogging('MessageHelper', 'checkPerformerStatus');
		$result = $this->db->get();
		$this->db->sqlLoggingTime($timeStart);
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
            if ($receiverId == 'admin') {
	         	$sql = 'SELECT id FROM message WHERE to_id = \''.$userId.'\' AND from_type = 3 AND owner_cd = '.$ownerCd.'  AND
				 	id NOT IN (Select message_id from message_delete where del_by_type = '.$type.')';
            } else {
            	$sql = 'SELECT id FROM message WHERE ((from_id = \''.$userId.'\' AND to_id = \''.$receiverId.'\') OR
					(to_id = \''.$userId.'\' AND from_id = \''.$receiverId.'\')) AND owner_cd = '.$ownerCd.'  AND
				 	id NOT IN (Select message_id from message_delete where del_by_type = '.$type.')';
            }
           $timeStart = $this->db->sqlLogging('MessageHelper', 'deleteInbox', $sql);
           $result = $this->db->query($sql);
           $this->db->sqlLoggingTime($timeStart);
            /**
             * Insert ID to the message_delete table.
            */
           	
			foreach($result as $row) {
				$data = array('message_id'=> $row->id, 'del_by_type' => $type);
				$timeStart = $this->db->sqlLoggingInsert('MessageHelper', 'deleteInbox', $data);
				$success = $this->db->insert($data);
				$this->db->sqlLoggingTime($timeStart);
			}
        }
        return $success;
	}
	
	function checkMessage($toId, $fromId, $fromDate = '') {
		global $ownerCd;
		$this->db->select('id, body, image, from_date');
		$this->db->from('message');
		$this->db->where("is_sent = 1 AND owner_cd = $ownerCd");
		
		$whereFromTo = "to_id = '$toId' AND from_id = '$fromId'";
		$whereDate = ($fromDate == "") ?'': "from_date > '$fromDate' OR";
		$whereIsRead = "is_read = 0";
		
		$this->db->where("$whereFromTo AND ($whereDate $whereIsRead)");
		//$this->db->where('is_read = ', '0', 'OR');
		$timeStart = $this->db->sqlLogging('MessageHelper', 'checkMessage');
		$result = $this->db->get();
		$this->db->sqlLoggingTime($timeStart);
		return $result;
	}
	
	/*
	 * Get all unread message in inbox
	 * @param $fromId
	 */
	function getAllUnreadMessage($fromId, $type = '') {
		global $ownerCd;
		$this->db->select('SQL_CALC_FOUND_ROWS m.id');
		$this->db->from('message m');
		$this->db->join('message_contact c', "((c.user_id = '$fromId' AND c.performer_id = m.from_id) OR (c.performer_id = '$fromId' AND c.user_id = m.from_id))", 'LEFT');
		$this->db->where("m.to_id = '$fromId' AND m.is_read = 0");
		$this->db->group_by('m.id');
		$unread = $this->db->get_row();
		
		$this->db->select('FOUND_ROWS() as total');
		$result = $this->db->get_row();
		return $result;
	}
	
	/**
	 * Points where deducted by point settings
	 * @param int $pointId - set point id deduction either recieved/send
	 */
	function getMinPoint($fromId){
		//return 1;
		//require_once 'M_Point.inc';
		//require_once 'F_Point.inc';
		
		$stat = 0; //male stat 0 free member, 1 charged , 2 for gold member
		//$yenIndex = 0; //add 6 to index if general woman in yen else 36
		//$pointIndex = 0; //add 12 to index if general woman in yen else 42
	
		//agency woman
		$arrAgency = array(6,22,23,24,25);
	
		//PC point settings value
		$arrPC = array(
			'message' => array(
				151,
				152,
				153
			),
			'image' =>	 array(
				109,
				110,
				111
			),
		);
		//SP point settings value
		$arrSP = array(
			'message' => array(
				154,
				155,
				156
			),
			'image' =>	 array(
				112,
				113,
				114
			),
		);
		
	
		$arrPoint = array(
			151 => array(
				1 => array('97', '103'),// money , point
				2 => array('127', '133')
			),
			152 => array(
				1 => array('98', '104'),
				2 => array('128', '134')
			),
			153 => array(
				1 => array('99', '105'),
				2 => array('129', '135')
			),
			154	=> array(
				1 => array('100', '106'),
				2 => array('130', '136')
			),
			155	=> array(
				1 => array('101', '107'),
				2 => array('131', '137')
			),
			156 => array(
				1 => array('102', '108'),
				2 => array('132', '138')
			),
			109 => array(
				1 => array('115', '121'),
				2 => array('139', '145')
			),
			110	=> array(
				1 => array('116', '122'),
				2 => array('140', '146')
			),
			111 => array(
				1 => array('117', '123'),
				2 => array('141', '147')
			),
			112	=> array(
				1 => array('118', '124'),
				2 => array('142', '148')
			),
			113	=> array(
				1 => array('119', '125'),
				2 => array('143', '149')
			),
			114	=> array(
				1 => array('120', '126'),
				2 => array('144', '150')
			),
		);
			
	
		//if ($fromType == 1) {
	
		//Initialize point settings
		//$Mpoints = new M_Point($this->getFromId(),$this->getToId());
			
		/*
		 * Get stat for male/female member
		*/
		$memStat = $this->getProfilePoint('male_point',$fromId);
			
		if($memStat['assortment'] == 0){
			$stat = 0;
		}
			
		if($memStat['gold_flg'] == 1 && $memStat['assortment'] == 1){
			$stat = 2;
		}
			
		if($memStat['gold_flg'] == 0 && $memStat['assortment'] == 1){
			$stat = 1;
		}
			
		/*$index = 1;
		if(in_array($femStat['FLV1'],$arrAgency)){
			$index = 2;
		}*/
		
		if(isSmartPhone()){
			/*$bodyID = ($body != '')? $arrSP[$body][$stat] : '';
			$imgID = ($image != '')? $arrSP[$image][$stat] : '';*/
			$messagePoint = $this->getPointValue($arrSP['message'][$stat]);
			$imagePoint = $this->getPointValue($arrSP['image'][$stat]);
			
		}else{
			/*$bodyID = ($body != '')? $arrPC[$body][$stat] : '';
			$imgID = ($image != '')? $arrPC[$image][$stat] : '';*/
			$messagePoint = $this->getPointValue($arrPC['message'][$stat]);
			$imagePoint = $this->getPointValue($arrPC['image'][$stat]);
		}
		$minPoint = ($messagePoint < $imagePoint) ? $messagePoint : $imagePoint;
		//$sendPoint = $this->getPointSettings($bodyID,$imgID);
		return $minPoint;
	}
	
	function getPointValue($id) {
		$this->db->select("value as min_total");
		$this->db->from("point_setting");
		$this->db->where("id = $id");
		$timeStart = $this->db->sqlLogging('Message Helper', 'getMinPoint');
		$result = $this->db->get_row(); //min point result
		$this->db->sqlLoggingTime($timeStart);
		return $result['min_total'];
	}
	
	/**
	 * Get profile current points
	 * @param string $table - set table for male or female point
	 * @param string $id - user id
	 * @return row
	 */
	function getProfilePoint($table,$id, $pointOnly = ''){
	
		if($table == 'female_point'){
				
			$sel =  ($pointOnly != '') ? 'fp.point' : 'f.stat,f.FLV1,f.USER_ID,fp.point,fp.point_old,fp.money,fp.money_old';
			$joinT = 'female_member';
			$alias = 'fp';
			$alias1 = 'f';
		}else{
			$sel =  ($pointOnly != '') ? 'mp.point' : 'm.assortment,m.gold_flg,mp.point,mp.point_old';
			$joinT = 'male_member';
			$alias = 'mp';
			$alias1 = 'm';
		}
	
		$this->db->select($sel);
		$this->db->from($table.' as '. $alias);
		$this->db->join($joinT.' as '.$alias1 ,$alias.'.user_id = '.$alias1.'.user_id','INNER');
		$this->db->where($alias.'.user_id = "'.$id.'"');
		$timeStart = $this->db->sqlLogging('Message Helper', 'getProfilePoint');
		$result = $this->db->get_row();
		$this->db->sqlLoggingTime($timeStart);
		return $result;
	}
	
}