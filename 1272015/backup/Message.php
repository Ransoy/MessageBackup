<?php
class Message {
	var $imgDir = '/var/www/livechat/htdocs/imgs/message/';
	var $imgUrl = '/imgs/message/';
	
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
	function Message($id = null) {
		$this->db = new CommonDb();
		if ($id == null) {
			$this->resetProperties();
		} else {
			$this->setId($id);
			$this->initiateProperties();
		}
	}
	
	/* * * * * * * * * * * * * * * * * * * * * * * *
	 * 			GETTERS AND SETTERS 			   *
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
	
	function getImgDir() { return $this->imgDir; }
	function getImgUrl() { return $this->imgUrl; }
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * *
	 * 					Methods/Functions 			 	*
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
			$body = $this->getBody();
			if (!empty($image)) {
				if($this->uploadImage()) {
// 					$this->ManagePoints('192');
					return $this->send();
				}
			} elseif (($body != '' && $body != null)) {
				$this->check_ng_word($body);
				$this->setBody($body);
// 				$this->ManagePoints('191');
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
		if (!empty($image)) {
			$ext = substr($_FILES[$image]["type"], 6);
			$fileName = tempnam($this->getImgDir(), 'img') . ".$ext";
			
			//if (!empty($_SESSION['degree']) && $_SESSION['degree'] != 0) {
				$source = imagecreatefromjpeg($_FILES[$image]['tmp_name']);
				$rotate = $this->fixRotateImage($source);
				if ($rotate) {
					ImageJPEG($rotate, $fileName, 90);
				} else {
					move_uploaded_file($_FILES[$image]['tmp_name'], $fileName);
				}
			/*} else {
				move_uploaded_file($_FILES[$image]['tmp_name'], $fileName);
			}*/
			$fname = basename($fileName);
			$this->setImage($fname);
			unlink(substr($fileName, 0, -4));
			return	file_exists($fileName);
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
		global $ownerCd;
		$this->setFromIp($_SERVER['REMOTE_ADDR']);
		$this->setFromDate(date('Y-m-d H:i:s'));
		$this->db->setTableName('message');
		$data = array(
			'owner_cd'  => $ownerCd,
			'body' 		=> $this->getBody(),
			'image' 	=> $this->getImage(),
			'from_type' => $this->getFromType(),
			'from_id' 	=> $this->getFromId(),
			'from_ip' 	=> $this->getFromIp(),
			'from_date' => $this->getFromDate(),
			'to_type' 	=> $this->getToType(),
			'to_id' 	=> $this->getToId(),
			'is_sent'	=> $this->getIsSent(),
			'is_checked' => 0
		);
		$result = $this->db->insert($data);
		return $result;
	}
	
	/**
	 * Get point settings value
	 * @param int $id - id of point setting
	 * @return row
	 */
	function getPointSettings($id){
	
		$this->db->select('value');
		$this->db->from('point_setting');
		$this->db->where('id',$id);
	
		return $this->db->get_row();
	
	}
	
	/**
	 * Points where deducted by point settings
	 * @param int $pointId - set point id deduction either recieved/send
	 */
	function ManagePoints($pointId){
		$fromType = $this->getFromType();
		
		if($fromType == 1){
			$from = $this->getProfilePoint('male_point',$this->getFromId());
			$to = $this->getProfilePoint('female_point',$this->getToId());
			
			if($pointId == '191'){
				$sendPoint = $this->getPointSettings($pointId);
				$recievePoint = $this->getPointSettings('193');
				$toSendPoints = $from['point'] - $sendPoint['value'];
				$toResPoints = $to['point'] + $recievePoint['value'];
			}
			
			if($pointId == '192'){
				$sendPoint = $this->getPointSettings($pointId);
				$recievePoint= $this->getPointSettings('194');
				$toSendPoints = $from['point'] - $sendPoint['value'];
				$toResPoints = $to['point'] + $recievePoint['value'];
			}
			
			$arrTable[] = array(
					'table' => 'male_point',
					'id' => $this->getFromId(),
					'point' =>$toSendPoints
					
			);
			
			$arrTable[] = array(
					'table' => 'female_point',
					'id' => $this->getToId(),
					'point' =>$toResPoints
					
			);
			
			foreach ($arrTable as $row){
				$data = array('point' => $row['point']);
				$this->db->where('user_id','"'. $row['id'] .'"');
				$result = $this->db->update($row['table'], $data);
			} 
			
			return $result;
		}
	}
	
	/**
	 * Get profile current points
	 * @param string $table - set table for male or female point
	 * @param string $id - user id
	 * @return row
	 */
	function getProfilePoint($table,$id){
	
		$this->db->select('point');
		$this->db->from($table);
		$this->db->where('user_id = "'.$id.'"');
	
		return $this->db->get_row();
	}
	
	
	function saveLog(){
		
		$this->db->setTableName('male_point_log');
		
		$data = array(
				'owner_cd'  => $ownerCd,
				'USER_ID' 	=> $this->getFromId(),
				'POINT_OLD' => $this->getFromType(),
				'POINT' 	=> $this->getFromId(),
				'UPD_MODE' 	=> $this->getFromIp(),
				'CRE_IP' => date('Y-m-d H:i:s'),
				'CRE_ID' 	=> $this->getToId(),
				'CRE_DATE' 	=> $_SERVER['REMOTE_ADDR'],
				'world_flg'	=> 0,
		);
		
		$result = $this->db->insert($data);
		
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
			$filename = $imgDir . $message['image'];
			if (file_exists($filename)) {
				unlink($filename);
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
			$this->db->where('to_type = 3 OR from_type = 3');
			$result = $this->db->get_row();
			return (count($result) > 0)?true:false;
		} else {
			return false;
		}
	}
	
	function getAll($filter, $page, $select, $order = 'ASC', $limit = 10) {
		$this->db->select($select);
		$this->db->from('message');
		if ($filter != '') {
			$this->db->where($filter);
		}
		$this->db->order_by('from_date', $order);
		$offset = $page*$limit;
		$this->db->limit($offset, $limit);
		
		return $this->db->get();
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
	
	/**
	 * Initiate message properties. 
	 * This will be used when constructor has a parameter id otherwise call resetProperties 
	 */
	function initiateProperties() {
		$this->db->select('*');
		$this->db->from('message');
		$this->db->where('id', $this->getId());
		$result = $this->db->get_row();
		if ($result) {
			$this->setBody($result['body']);
			$this->setFromDate($result['from_date']);
			$this->setFromId($result['from_id']);
			$this->setFromIp($result['from_ip']);
			$this->setFromType($result['from_type']);
			$this->setImage($result['image']);
			$this->setIsRead($result['is_read']);
			$this->setIsSent($result['is_sent']);
			$this->setName($result['name']);
			$this->setOwnerCd($result['owner_cd']);
			$this->setReadDate($result['read_date']);
			$this->setToId($result['to_id']);
			$this->setToType($result['to_type']);
		} else {
			$this->resetProperties();
		}
	}
	
	/**
	 * Validates a file, returns true if file is valid otherwise returns error message.
	 * @param String $src
	 * @return Ambigous <boolean, string>
	 */
	function validateImage($src) {
		$imageResult = '';
		if ($_FILES[$src]['tmp_name'] == '') {
			if (1 == $_FILES[$src]['error']) {
				$imageResult = 'Image Size is above server max upload size';
			}
			$imageResult .= '<br/>Image is empty!';
		}
		if (!is_uploaded_file($_FILES[$src]['tmp_name'])) {
			$imageResult .= '<br/>Image not uploaded';
		}
		if ($_FILES[$src]['size'] == 0) {
			$imageResult .= '<br/>Image size is 0';
		}
		if ($_FILES[$src]['size'] > 8388608) {
			$imageResult .= '<br/>Image size is greater than 8mb';
		}
		$size = GetImageSize($_FILES[$src]['tmp_name']);
		if ($size[2] != 1 && $size[2] != 2 && $size[2] != 3) {
			$imageResult .= '<br/>File Not an image';
		}
		return ($imageResult == '') ? true : $imageResult;
	}
	
	/**
	 * 
	 * @param int $fromId
	 * @param String $hashId
	 * @param int $toType
	 * @param int $fromType
	 * @return int total Conversation Message
	 */
	function countMessage($fromId, $toId, $fromType) {
		$this->db->select('count(m.id) as total_message');
		$this->db->from('message as m');
		$this->db->where("((m.from_id = '$fromId' and m.to_id = '$toId') OR (m.to_id = '$fromId' and m.from_id = '$toId'))");
		$this->db->where('m.owner_cd = 1');
		$this->db->where("(NOT EXISTS (SELECT id FROM message_delete WHERE message_id = m.id AND del_by_type = $fromType))");
		$this->db->where("(m.is_sent = 1 OR m.from_type ='$fromType')");
		$result = $this->db->get_row();
		return $result['total_message'];
		
	}
	
	/**
	 * Retrieve the conversation between the two ids
	 * @param int $from_id
	 * @param int $toId
	 * @return retrieve the results of the latest conversation and maximum of 10
	 */
	function getConversation($from_id, $toId, $fromType) {
		global $ownerCd;
		$this->db->select('m.id, m.owner_cd, m.body, m.image, m.from_type, m.from_id, m.to_type, m.to_id, m.from_date, m.is_sent, m.is_read');
		$this->db->from('message as m');
		$this->db->where("((m.from_id ='$from_id' AND m.to_id='$toId') OR (m.to_id ='$from_id' AND m.from_id = '$toId'))");
		$this->db->where('m.owner_cd = ' . $ownerCd);
		$this->db->where("(m.is_sent = 1 OR m.from_type ='$fromType')"); 
		$this->db->where("(NOT EXISTS (SELECT id FROM message_delete WHERE message_id = m.id AND del_by_type = $fromType))");
		$this->db->order_by('m.from_date DESC, m.id', 'DESC');
		$this->db->limit(0,10);
		return $this->db->get();
	}
	
	/**
	 * Checks the message id that has been sent for the recipient if is_read is 0 updates it to 1 and date readed.
	 * @param int $id
	 * @param int $toId
	 */
	function checkIsRead($id, $toId) {
		$data = array('is_read' => 1, 'read_date' => date('Y-m-d H:i:s'));
		$this->db->where("id = $id AND is_read = 0 AND to_id = '$toId'");
		$result = $this->db->update('message', $data);
	}
	
	function check_ng_word(&$msg){
		global $ownerCd,$dbSlave33;
	
		// 禁止ワードを取得
		$this->db->select('ng_word, kubun');
		$this->db->from('mail_ng_word');
		$this->db->where("owner_cd = $ownerCd");
		$result = $this->db->get();
		
		$err_cnt = 0;
		$war_cnt = 0;
		$war_ng_word_list = "";
		$err_ng_word_list = "";
		
		foreach ($result as $r) {
			if(mberegi($r->ng_word, $msg)) {
				if($r->kubun == 1) {
					//お知らせ
					$war_cnt++;
					if ( $war_cnt > 1) {
						$war_ng_word_list .= ", ";
					}
					$war_ng_word_list .= $r->ng_word;
				} else if ($r->kubun == 2) {
					//エラー
					$err_cnt++;
					if ( $err_cnt > 1) {
						$err_ng_word_list .= ", ";
					}
					$err_ng_word_list .= $r->ng_word;
				
					//エラー内容を×××に置換する。
					$msg = str_replace($r->ng_word,"×××",$msg);
						
				}
			}
		}
	
		//お知らせ件数が0件以上の場合は事務局にメールでお知らせ
/*		if ($war_cnt > 0) {
$str = <<<EOM
				
			お知らせ禁止ワードが使用されました。
			ユーザーID　　　　　　　：{$_SESSION['user_id']}
			性別　　　　　　　　　　：女性
			お知らせ禁止ワード数　　：{$war_cnt}
			お知らせ禁止ワード内容　：{$war_ng_word_list}
				
			件名 :
			{$title}
				
			本文 :
			{$msg}
				
EOM;
			p_sendmail('g-support@macherie.tv',"お知らせ禁止ワードチェック報告",$str );
			//禁止ワード文字列と禁止ワード件数をリターン
			return;
		}*/
	}
	
	function rotateImage($img1, $rec) {
		if (function_exists('imagerotate')) {
			return imagerotate($img1, $rec, 0);
		}
	
		//return $img1;
	
		$wid = imagesx($img1);
		$hei = imagesy($img1);
		switch ($rec) {
			case 270:
				$img2 = @imagecreatetruecolor($hei, $wid);
				break;
			case 180:
				$img2 = @imagecreatetruecolor($wid, $hei);
				break;
			default :
				$img2 = @imagecreatetruecolor($hei, $wid);
		}
	
		if ($img2) {
			for ($i = 0;$i < $wid; $i++) {
				for ($j = 0;$j < $hei; $j++) {
					$ref = imagecolorat($img1,$i,$j);
					switch ($rec) {
						case 270:
							if (!@imagesetpixel($img2, ($hei - 1) - $j, $i, $ref)) {
								return false;
							}
							break;
						case 180:
							if(!@imagesetpixel($img2, $wid - $i, ($hei - 1) - $j, $ref)) {
								return false;
							}
							break;
						default:
							if (!@imagesetpixel($img2, $j, ($wid - 1) - $i, $ref)) {
								return false;
							}
					}
				}
			}
			return $img2;
		}
		return false;
	}
	
	function fixRotateImage($srcFile) {
		$isImgModify = true;
		$img1 = $srcFile;
		if (function_exists("exif_read_data")) {
			$exif = exif_read_data($srcFile);
			if (!empty($exif['Orientation'])) {
				switch ($exif['Orientation']) {
					case 8 :
						$img1 = rotateImage($img1, 90);
						break;
					case 3 :
						$img1 = rotateImage($img1, 180);
						break;
					case 6 :
						$img1 = rotateImage($img1, 270);
						break;
					default:
						$isImgModify = false;
				}
			}
		}
	
		return ($isImgModify)? $img1 : false;
	}
	
	
	/* MY TRASH
	 	$this->db->join('message m', 'm.id = md.message_id');
		$this->db->where('(m.from_id', $byId);
		$this->db->where('m.to_id', $byId.')', 'OR');
	 */
	
}