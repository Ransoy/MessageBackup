<?php
class Message {
	
	/* Properties */
	var $name;
	var $id;
	var $ownerCd;
	var $body;
	var $image;
	var $fromType;
	var $fromId;
	var $fromIp;
	var $fromDate;
	var $toType;
	var $toId;
	var $isSent;
	var $isRead;
	var $readDate;

	var $db;
	
	/* MessageModel Constructor */
	function Message() {
		$this->db = new CommonDb();
		$this->resetProperties();
	}
	
	/* * * * * * * * * * * * * * * * * * * * * * * *
	 * 							GETTERS AND SETTERS 					 *
	 * * * * * * * * * * * * * * * * * * * * * * * */
	function setName($name) {	$this->name = $name; }
	function getName() { return $this->name; }
	
	function setId($id) {	$this->id = $id; }
	function getId() {	return $this->id; }
	
	function setOwnerCd($ownerCd) { $this->ownerCd = $ownerCd; }
	function getOWnerCd() { return $this->ownerCd; }
	
	function setBody($body) { $this->body = $body; }
	function getBody() { return $this->body; }
	
	function setImage($image) { $this->image = $image; }
	function getImage() { return $this->image; }
	
	function setFromType($fromType) { $this->fromType = $fromType; }
	function getFromType() { return $this->fromType; }
	
	function setFromId($fromId) { $this->fromId = $fromId; }
	function getFromId() { return $this->fromId; }
	
	function setFromIp($fromIp) { $this->fromIp = $fromIp; }
	function getFromIp() { return $this->fromIp; }
	
	function setFromDate($fromDate) { $this->fromDate = $fromDate; }
	function getFromDate() { return $this->fromDate; }
	
	function setToType($toType) { $this->toType = $toType; }
	function getToType() { return $this->toType; }
	
	function setToId($toId) { $this->toId = $toId; }
	function getToId() { return $this->toId; }
	
	function setIsSent($isSent) { $this->isSent = $isSent; }
	function getIsSent() { return $this->isSent; }
	
	function setIsRead($isRead) { $this->isRead = $isRead; }
	function getIsRead() { return $this->isRead; }
	
	function setReadDate($readDate) { $this->readDate = $readDate; }
	function getReadDate() { return $this->readDate; }
	
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * *
	 * 							Methods/Functions 								 *
	 * * * * * * * * * * * * * * * * * * * * * * * * * */
	
	/**
	 * Checks whether the message has been deleted by @param $byType.
	 * Must set ID first to use this function.
	 * @param int $byId deteremines what id deleted the message
	 * @param int $byType determines what type of user has deleted the message
	 * @return boolean 
	 */
	function isDeletedBy($byType) {
		$id = $this->getId();
		$this->resetProperties();
		if ($id) {
			$this->db->select('id');
			$this->db->from('message_delete');
			$this->db->where('message_id', $id);
			$this->db->where('del_by_type', $byType);
			$result = $this->db->get_row();
			return (count($result) > 0) ? true:false;
		} else {
			return false;
		}
	}
	
	/**
	 * Creates and sends a new message or image. 
	 * $fromType, $fromId, $toType, $toId must not be empty to create a message or image.
	 * Returns true if successful and false otherwise.
	 * @return boolean
	 */
	function create() {
		$fromType = $this->getFromType();
		$fromId = $this->getFromId();
		$toType = $this->getToType();
		$toId = $this->getToId();
		if (
				!empty($fromType) &&
				!empty($fromId)   &&
				!empty($toType)   &&
				!empty($toId)
		) {
			$image = $this->getImage();
			$body = htmlentities(trim($this->getBody()));
			if (!empty($image)) {
				$this->setImage($this->uploadImage());
			} elseif (($body != '' && $body != null) || !empty($image)) {
				$this->setBody($body);
				return $this->send();
			}
		}
		return false;
	}
	
	/**
	 * Private method in Message Class.
	 * Uploads an image message.
	 * Must set image to upload an image.
	 * @return boolean
	 */
	function uploadImage() {
		$image = $this->getImage();
		if ($image) {
			$path = 'img/';
			$fileName = tempnam($path, 'img') . '.jpg';
			move_uploaded_file($_FILES[$this->getImage()]['tmp_name'], $fileName);
			return	$fileName;	
		} else {
			return false;
		}
	}
	
	/** 
	 * Private method in Message Class.
	 * Saves into the message table.
	 * @return boolean
	 */
	function send() {
		$this->setFromIp($_SERVER['REMOTE_ADDR']);
		$this->setFromDate(date('Y-m-d H:i:s'));
		$this->db->setTableName('message');
		$data = array(
			'body' 			=> $this->getBody(),
			'image' 		=> $this->getImage(),
			'from_type' => $this->getFromType(),
			'from_id' 	=> $this->getFromId(),
			'from_ip' 	=> $this->getFromIp(),
			'from_date' => $this->getFromDate(),
			'to_type' 	=> $this->getToType(),
			'to_id' 		=> $this->getToId()
		);
		$result = $this->db->insert($data);
		if ($result) {
			$data = array('is_sent' => 1);
			$messageId = $this->db->getLastInsert();
			$this->resetProperties();
			$this->db->where('id', $messageId);
			return $db->update('message', $data);
		}
		return false;
	}
	
	/**
	 * Deleting a message actually inserts a data in message_delete table.
	 * Must set an ID first to delete a message.
	 * @param int $byId deleted by
	 * @param int $byType what type of user
	 * @return boolean
	 */
	function delete($byType) {
		$id = $this->getId();
		$this->resetProperties();
		if ($id) {
			$data = array(
				'message_id' 	=> $id,
				'del_by_type' => $byType
			);
			$this->db->setTableName('message_delete');
			return $this->db->insert($data);
		}
		return false;
	}
	
	/**
	 * Deletes an image.
	 * Must set an ID first to delete image.
	 * @param int $id of message
	 * @return boolean
	 */
	function deleteImage() {
		$id = $this->getId();
		$this->resetProperties();
		if ($id) {	
			$this->db->select('image');
			$this->from('message');
			$this->where('id', $id);
			$message = $this->db->get_row();
			if (file_exists($message['image'])) {
				unlink($message['image']);
				return true;
			}
		}
		return false;	
	}
	
	/**
	 * Returns true if the message is from admin.
	 * Must set an ID to use this function.
	 * @return boolean
	 */
	function isAdminMessage() {
		$id = $this->getId();
		$this->resetProperties();
		if ($id) {
			$this->db->select('id');
			$this->db->from('message');
			$this->db->where('owner_cd', '1');
			$result = $this->db->get_row();
			return (count($result) > 0)?true:false;
		} else {
			return false;
		}
	}
	
	
	
	/**
	 * A private method in Message Class.
	 * Resets all the properties.
	 */
	function resetProperties() {
		$this->setBody('');
		$this->setFromDate('');
		$this->setFromId('');
		$this->setFromIp('');
		$this->setFromType('');
		$this->setId('');
		$this->setImage('');
		$this->setIsRead('');
		$this->setIsSent('');
		$this->setName('');
		$this->setOwnerCd('');
		$this->setReadDate('');
		$this->setToId('');
		$this->setToType('');
	}
	
	/* MY TRASH
	 	$this->db->join('message m', 'm.id = md.message_id');
		$this->db->where('(m.from_id', $byId);
		$this->db->where('m.to_id', $byId.')', 'OR');
	 */
	
}