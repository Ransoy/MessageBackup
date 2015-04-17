<?php 

class MessageContact{
	
	var $name;
	var $id;
	var $owner_id;
	var $user_id;
	var $performer_id;
	var $is_from_chat = true;
	
	var $db;
	
	//Initialize database connection
	function MessageContact($id = null){
		$this->db = new CommonDb();
		if ($id == null) {
			$this->resetProperties();
		} else {
			$this->setId($id);
			$this->initiateProperties();
		}
	}
	
	/**
	 * A private method in MessageContact Class.
	 * Resets all the properties.
	 */
	function resetProperties() {
		$this->setId('');
		$this->setOwnerId('');
		$this->setUserId('');
		$this->setPerformerId('');
		$this->setIsFromChat('');
	}
	function initiateProperties() {
		$this->db->select('*');
		$this->db->from('message_contact');
		$this->db->where('id', $this->getId());
		$this->db->sqlLogging('MessageContact', 'initiateProperties');
		$result = $this->db->get_row();
		if ($result) {
			$this->setId($result['id']);
			$this->setOwnerId($result['owner_cd']);
			$this->setUserId($result['user_id']);
			$this->setPerformerId($result['performer_id']);
			$this->setIsFromChat($result['is_from_chat']);
		} else {
			$this->resetProperties();
		}
	}
	
	
	function setId($id){
		$this->id = $id;
	}
	
	function getId(){
		return $this->id;
	}
	
	function setName($name){
		$this->name = $name;
	}
	
	function getName(){
		return $this->name;
	}
	
	function setOwnerId($ownerId){
		$this->owner_id = $ownerId;
	}
	
	function getOwnerId(){
		return $this->owner_id;
	}
	
	function setUserId($userId){
		$this->user_id = $userId;
	}

	function getUserId(){
		return $this->user_id;
	}
	
	function setPerformerId($performerId){
		$this->performer_id = $performerId;
	}
	
	function getPerformerId(){
		return $this->performer_id;
	}
	
	function setIsFromChat($isFromChat){
		$this->is_from_chat = $isFromChat;
	}
	
	function getIsFromChat(){
		return $this->is_from_chat;
	}
	
	// Create contact
	function create(){
		
		$data = array(
				'owner_cd' => $this->getOwnerId(),
				'user_id' => $this->getUserId(),
				'performer_id' => $this->getPerformerId(),
				'is_from_chat' => $this->getIsFromChat()
		);
		
		
		
		$result = $this->getContact();
		
		if($result){
			
			return false;
			
		}else{
			
			$this->db->setTableName('message_contact');
			return $this->db->insert($data);
			
		}
		
	}
	
	function getContact() {
		$this->db->select('*');
		$this->db->from('message_contact');
		$this->db->where('user_id',"'".$this->getUserId()."'");
		$this->db->where('performer_id',"'".$this->getPerformerId()."'");
		$this->db->sqlLogging('MessageContact', 'getContact');
		$result = $this->db->get_row();
		return $result;
	}
	
	/**
	 * Blocks a member as message contact.
	 * @param int $id - id user / performer
	 * @param int $byType - 1 = user, 2 = performer
	 * 
	 */
	function block($id, $byType){

		$data = array(
				'contact_id' => $id,
				'block_by_type' => $byType,
				'block_date' => date('Y-m-d H:i:s'),
				'block_ip'=> $_SERVER['REMOTE_ADDR']
		);
		
		$this->db->setTableName('message_contact_block');
		
		return $this->db->insert($data);
			
	}
	
	/**
	 * Blocks a user as message contact.
	 * @param int $userId - id for user 
	 *
	 */
	function blockUser($userId){
		
		return $this->block($userId, 1);
		
	}
	
	/**
	 * Blocks a performer as message contact.
	 * @param int $performerId - id for performer
	 *
	 */
	function blockPerformer($performerId){

		return $this->block($performerId, 2);

	}
	
	/**
	 * Unblocks a member as message contact.
	 */
	function unblock($id,$type,$fromId,$toId){
		
		$this->db->from('message_contact_block');
		$this->db->where("contact_id = $id");
		$this->db->where("block_by_type = $type");
		$result = $this->db->delete();
		if($fromId !=null && $toId != null){
			$this->updateMessage($fromId, $toId);
		}
		
		
		
		
		
		 
	}
	
	function updateMessage($fromId, $toId){
		$this->db->where("to_id = '$fromId' AND from_id = '$toId' AND is_sent = 0");
		$this->db->update('message',array('is_sent'=>1,'from_date'=> date('Y-m-d H:i:s')));
		
		
		
	}
	
	/**
	 * Unblocks a user as message contact.
	 * @param int $byUserId - message contact id
	 * 
	 */
	function unblockUser($userId,$fromId=null,$toId=null){
		
		return $this->unblock($userId,1,$fromId,$toId);
	
	}
	
	/**
	 * Unblocks a performer as message contact.
	 * @param int $performerId - identifier
	 * 
	 */
	function unblockPerformer($performerId,$fromId=null,$toId=null){
		
		return $this->unblock($performerId,2,$fromId,$toId);
		
	}
	
	/**
	 * Adds a member as favorite message contact.
	 * 
	 */
	function fave($id, $byType){
		
		$data = array(
				'contact_id' => $id,
				'fave_by_type' => $byType,
				'fave_date' => date('Y-m-d H:i:s'),
				'fave_ip' => $_SERVER['REMOTE_ADDR']
		);
		
		$this->db->setTableName('message_contact_fave');
		
		return $this->db->insert($data);
		
	}
	
	/**
	 * Adds a user as favorite message contact.
	 * @param int $byUserId - message contact id
	 *
	 */
	function faveUser($userId){
		
		return $this->fave($userId, 2);
		
	}
	
	/**
	 * Adds a performer as favorite message contact.
	 * @param int $performerId - identifier 
	 * 
	 */
	function favePerformer($performerId){
		
		return $this->fave($performerId, 1);
		
	}
	
	/**
	 * Removes a member as favorite message contact
	 * 
	 */
	function unfave($id){
		
		$this->db->from('message_contact_fave');
		$this->db->where('contact_id',$id);
		
		return $this->db->delete();
		
	}
	
	/**
	 * Removes a user as favorite message contact.
	 * @param int $performerId - identifier
	 * @param int $byUserId - message contact id
	 * 
	 */
	function unfaveUser($userId){
			
		return $this->unfave($userId);
		
	}
	
	/**
	 * Removes a performer as favorite message contact.
	 * @param int $performerId - identifiers
	 * @param int $byUserId - message contact id
	 * 
	 */
	function unfavePerformer($performerId){
		
		return $this->unfave($performerId);
		
	}
	
	/**
	 * Check contact member if block by user / performer
	 * @param int $id of message contact
	 * @param int $byId - id of member
	 * @param int $byType - 1 = user, 2 = performer
	 * @param return true / false;
	 * 
	 */
	function isBlockedBy($id, $byType){
		
		$this->db->select('*');
		$this->db->from('message_contact_block');
		$this->db->where('contact_id',$id);
		$this->db->where('block_by_type',$byType);
		$this->db->sqlLogging('MessageContact', 'isBlockedBy');
		$result = $this->db->get_row();
		
		return ($result)? true : false ;
		
	}
	
	/**
	 * check contact member if favorite by user / performer
	 * @param int id of message contact
	 * @param int byId - id of member
	 * @param int byType - 1 = user, 2 = performer 
	 * return true / false;
	 * 
	 */
	function isFaveBy($id, $byType){
		
		$this->db->select('*');
		$this->db->from('message_contact_fave');
		$this->db->where('contact_id',$id);
		$this->db->where('fave_by_type',$byType);
		$this->db->sqlLogging('MessageContact', 'isFaveBy');
		$result = $this->db->get_row();
		
		return ($result)? true : false ;
	}
	
	
	
}

?>