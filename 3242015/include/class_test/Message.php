<?php

require_once 'sp/common_proc.inc';
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
	var $point;
	var $totalpoint;
	
	var $deductPoints;
	var $toSendPoints;
	var $toResPoints;
	var $totalMoney;
	
	var $db;
	
	var $emoji;
	
	/* MessageModel Constructor */
	function Message($id = null) {
		$this->db = new CommonDb();
		if ($id == null) {
			$this->resetProperties();
		} else {
			$this->setId($id);
			$this->initiateProperties();
		}
		$this->emoji = new EmojiClass();
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
	
	function setPoint($point){ return $this->point = $point; }
	function getPoint(){ return $this->point; }

	function setTPoint($point){ return $this->totalpoint = $point; }
	function getTPoint(){ return $this->totalpoint; }
	
	function setToSendPoints($point) { $this->toSendPoints = $point; }
	function getToSendPoints() { return $this->toSendPoints; }
	
	function setToResPoints($point) { $this->toResPoints = $point; }
	function getToResPoints() { return $this->toResPoints; }
	
	function setTotalMoney($point) { $this->totalMoney = $point; }
	function getTotalMoney() { return $this->totalMoney; }
	
	function setDeductPoints($point){ $this->deductPoints = $point; }
	function getDeductPoints(){ return $this->deductPoints; }
	
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
			$timeStart = $this->db->sqlLogging('Message', 'isDeletedBy');
			$result = $this->db->get_row();
			$this->db->sqlLoggingTime($timeStart);
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
		$bodyIndex = '';
		$imageIndex = '';
		
		if (
				!empty($fromType) &&
				!empty($fromId)   &&
				!empty($toType)   &&
				!empty($toId)
		) {
			$image = $this->getImage();
			$body = $this->getBody();
			if ((!empty($image) && $this->uploadImage()) || ($body != '' && $body != null)) {
				if ($body != '') {
					$this->check_ng_word($body);
					$this->setBody($body);
				}
				if ($this->send()) {
					$result = false;
						
					if ($this->getImage()) { //img		
						$imageIndex = 'image';
						$result = '<image class="resized" src="'. $this->getImgUrl() . $this->getImage().'" />';
					}
					
					if ($body != '') { //message
						
						$bodyIndex = 'message';
						$result .= $this->emoji->getEmojiHtml($this->getBody());
					}
					
					if ($this->getFromType() == 1 && $result) {
						$this->ManagePoints($bodyIndex, $imageIndex);
						$this->updatePoints(
							$this->getToSendPoints(),
							$this->getToResPoints(),
							$this->getTotalMoney()
						);
					}
					return $result;
				}
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
			
			$date = date('Ymd');
			$currentPath = $this->getImgDir() . $date;
			if (!file_exists($currentPath)) {
				$oldmask = umask(0);
				mkdir($currentPath, 0777);
				umask($oldmask);
			}
			
			
			$filepath = tempnam($currentPath, 'img');
			
			$fileSource = $_FILES[$image]['tmp_name'];
			//$ext = substr($_FILES[$image]["type"], 6);
			
			$size = GetImageSize($fileSource);
			$width  = $size[0];
			$height = $size[1];
			$ext = $this->getExtenstion($fileSource);
			$fileName = $filepath . ".$ext";
			if (($ext == 'jpeg' || $ext == 'jpg' || $ext == 'JPEG')) {
				$fileProcess = $this->fixRotateImage($fileSource, $ext, $size);
			} else {
				$fileProcess = ($width > 440 && !($ext == 'gif' || $ext == 'GIF')) ? $this->imageResize($fileSource, $ext, 440, 0) : $fileSource;
			}
			if ($fileProcess != $fileSource) {
				$this->saveProcessFile($fileProcess, $fileName, $ext);
			} else {
				move_uploaded_file($fileProcess, $fileName);
			}
			$fname = $date . '/' .basename($fileName);
			$this->setImage($fname);
			unlink($filepath);
			return	file_exists($fileName);
		} else {
			return false;
		}
	}
	
	function saveProcessFile($file, $des, $ext) {
		switch($ext) {
			case 'png' 	: 
			case 'PNG' 	:
				imagepng ($file, $des, 90);
				break;
			case 'jpg' 	: 
			case 'JPG' 	:
			case 'jpeg' :
			case 'JPEG' :
				imagejpeg($file, $des, 90);
				break;
			case 'gif' 	:
			case 'GIF'	:
				imagegif($file, $des, 90);
				 break;
		}
	}
	
	function createImageFrom($src, $ext) {
		$img = '';
		switch($ext) {
			case 'png' 	:
			case 'PNG' 	:
				$img = imagecreatefrompng($src);
				break;
			case 'jpg' 	:
			case 'JPG' 	:
			case 'jpeg' :
			case 'JPEG' :
				$img = imagecreatefromjpeg($src);
				break;
			case 'gif' 	:
			case 'GIF'	:
				$img = imagecreatefromgif($src);
				break;
		}
		return $img;
	}
	
	function getExtenstion($file) {
		$ext = 'jpg';
		switch(exif_imagetype($file)) {
			case IMAGETYPE_GIF: $ext = 'gif'; break;
			case IMAGETYPE_JPEG: $ext = 'jpg'; break;
			case IMAGETYPE_PNG: $ext = 'png'; break;
		}
		return $ext;
	}
	function imageResize($src, $ext, $width = 0, $height = 0, $picLvl = 90){
		if (0 == $width && 0 == $height) {
			return;
		}
	
		$sizeArr = GetImageSize($src);
		if (0 != $sizeArr[1]) {
			$srcWidth = $sizeArr[0];
			$srcHeight = $sizeArr[1];
	
			if (0 == $height) {
				$newWidth = $width;
				$rate = ($newWidth / $srcWidth);
				$newHeight = $rate * $srcHeight;
			}
			else if (0 == $width) {
				$newHeight = $height;
				$rate = ($newHeight / $srcHeight);
				$newWidth = $rate * $srcWidth;
			}
			else {
				$rateW = $srcWidth / $width;
				$rateH = $srcHeight / $height;
				$scc = max($rateW, $rateH);
	
				$newWidth = $srcWidth / $scc;
				$newHeight = $srcHeight / $scc;
			}
	
			$newImg = imagecreatetruecolor($newWidth, $newHeight);
			$color  = imagecolorallocate ($newImg, 255, 255, 255);
			
			switch ($ext) {
				case 'png' 	:
				case 'PNG' 	:
					imagealphablending($newImg, false);
					imagesavealpha($newImg,true);
					$transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
					imagefilledrectangle($newImg, 0, 0, $newWidth, $newHeight, $transparent);
					break;
				default:
					imagefill($newImg, 0, 0, $color);
			}
			
			$source = $this->createImageFrom($src, $ext);
			imagecopyresampled($newImg, $source, 0, 0, 0, 0, $newWidth, $newHeight, $srcWidth, $srcHeight);
			//imagejpeg($newImg, $dest, $picLvl);
			
			// cleanup
			//imagedestroy($color);
			imagedestroy($source);
			return $newImg;
			//imagedestroy($newImg);
		}
	}
	
	function rotateImage($img1, $rec) {
		if (function_exists('imagerotate')) {
			return imagerotate($img1, $rec, 0);
		}
	
		//return $img1;
		//$img = ImageCreateFromJPEG($img1);
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
	
	function fixRotateImage($srcFile, $ext, $size) {
		$isImgModify = true;
		$img1 = $this->createImageFrom($srcFile, $ext);
		$width  = $size[0];
		$height = $size[1];
		$max = 440;
		if (function_exists("exif_read_data")) {
			$exif = exif_read_data($srcFile);
			if (!empty($exif['Orientation'])) {
				switch ($exif['Orientation']) {
					case 8 :
						if ($max < $height) $img1 = $this->imageResize($srcFile, $ext, 0, $max);
						$img1 = $this->rotateImage($img1, 90);
						break;
					case 3 :
						if ($max < $width) $img1 = $this->imageResize($srcFile, $ext, $max, 0);
						$img1 = $this->rotateImage($img1, 180);
						break;
					case 6 :
						if ($max < $height) $img1 = $this->imageResize($srcFile, $ext, 0, $max);
						$img1 = $this->rotateImage($img1, 270);
						break;
					default:
						if ($max < $width) {
							$img1 = $this->imageResize($srcFile, $ext, $max, 0);
						} else {
							$isImgModify = false;
						}
				}
			} else {
				if ($max < $width) $img1 = $this->imageResize($srcFile, $ext, $max, 0);
				else $isImgModify = false;
			}
		} else {
			if ($max < $width) $img1 = $this->imageResize($srcFile, $ext, $max, 0);
			else $isImgModify = false;
		}
	
		return ($isImgModify)? $img1 : $srcFile;
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
		$timeStart = $this->db->sqlLoggingInsert('Message', 'send', $data);
		$result = $this->db->insert($data);
		$this->db->sqlLoggingTime($timeStart);
		return $result;
	}
	
	/**
	 * Get point settings value
	 * @param int $id - id of point setting
	 * @return row
	 */
	function getPointSettings($bodyId,$imgId){
		/* echo 'message'.$bodyId .'image'.$imgId; */
		$this->db->select("SUM(value) as totalval");
		$this->db->from("point_setting");
		$this->db->where("id IN ('$bodyId','$imgId')");
		$timeStart = $this->db->sqlLogging('Message', 'getPointSettings');
		$result = $this->db->get_row();
		$this->db->sqlLoggingTime($timeStart);
		return $result;
	}
	
	/**
	 * Points where deducted by point settings
	 * @param int $pointId - set point id deduction either recieved/send
	 */
	function ManagePoints($body,$image){
		
		require_once 'M_Point.inc';
		require_once 'F_Point.inc';
		
		$fromType = $this->getFromType();
		
		$stat = 0; //male stat 0 free member, 1 charged , 2 for gold member
		$yenIndex = 0; //add 6 to index if general woman in yen else 36
		$pointIndex = 0; //add 12 to index if general woman in yen else 42
		
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
			$Mpoints = new M_Point($this->getFromId(),$this->getToId());
			$Fpoints = new F_Point($this->getToId(),$this->getFromId());
			
			/*
			 * Get stat for male/female member
			 */
			$memStat = $this->getProfilePoint('male_point',$this->getFromId());
			$femStat = $this->getProfilePoint('female_point',$this->getToId());
			
			$maleP = $Mpoints->GetPoint();
			$femaleP = $Fpoints->GetPoint();
			$femaleM = $Fpoints->GetMoney();
			
			if($memStat['assortment'] == 0){
				$stat = 0;
			}
			
			if($memStat['gold_flg'] == 1 && $memStat['assortment'] == 1){
				$stat = 2;
			}
			
			if($memStat['gold_flg'] == 0 && $memStat['assortment'] == 1){
				$stat = 1;
			}
			
			$index = 1;
			if(in_array($femStat['FLV1'],$arrAgency)){
					$index = 2;
			}

			if(isSmartPhone()){	
				$bodyID = ($body != '')? $arrSP[$body][$stat] : '';
				$imgID = ($image != '')? $arrSP[$image][$stat] : '';
			}else{
				$bodyID = ($body != '')? $arrPC[$body][$stat] : '';
				$imgID = ($image != '')? $arrPC[$image][$stat] : '';
			}
	
			$sendPoint = $this->getPointSettings($bodyID,$imgID);
			$recievePoint = $this->getPointSettings(
										($bodyID != '')? $arrPoint[$bodyID][$index][1] : '',
										($imgID != '')?  $arrPoint[$imgID][$index][1] : ''
							);
			$money = $this->getPointSettings(
										($bodyID != '')? $arrPoint[$bodyID][$index][0] : '',
										($imgID != '')? $arrPoint[$imgID][$index][0] : ''
							);

			$totalMoney = $femaleM + $money['totalval'];
			$toSendPoints = $maleP - $sendPoint['totalval'];
			$toResPoints = $femaleP + $recievePoint['totalval'];
			
			$this->setDeductPoints($sendPoint['totalval']);
			$this->setToSendPoints($toSendPoints);
			$this->setToResPoints($toResPoints);
			$this->setTotalMoney($totalMoney);
			
			//$Mpoints->UpdPoint($toSendPoints,100);

			//$Fpoints->UpdPoint($toResPoints,$totalMoney,100);
			
			return true;
		//}
	}
	
	function updatePoints($toSendPoints, $toResPoints, $totalMoney) {
		//Initialize point settings
		if (
			$toSendPoints >= 0 &&
			!empty($toResPoints) &&
			!empty($totalMoney)
		) {
			$Mpoints = new M_Point($this->getFromId(),$this->getToId());
			$Fpoints = new F_Point($this->getToId(),$this->getFromId());
			
			$terminal = (isSmartPhone())? 3 : 1 ;
			if(isAndroid()){
				$deviceType = 2;
			}elseif(isIOS()){
				$deviceType = 3;
			}else{
				$deviceType = 1;
			}
			$Mpoints->AddPoint(($this->getDeductPoints()*-1), 100, 1, $terminal, $deviceType);
			$Fpoints->UpdPoint($toResPoints, $totalMoney, 100);
		}
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
		$timeStart = $this->db->sqlLogging('Message', 'getProfilePoint');
		$result = $this->db->get_row();
		$this->db->sqlLoggingTime($timeStart);
		return $result;
	}
	
	/**
	 * Saving all point transaction
	 * @return boolean
	 */
	function saveLog(){
	
		$this->db->setTableName('male_point_log');
	
		$data = array(
				'owner_cd'  => 1,
				'USER_ID' 	=> $this->getFromId(),
				'POINT_OLD' => $this->getPoint(),
				'POINT' 	=> $this->getTPoint(),
				'UPD_MODE' => '100',
				'CRE_IP' => $_SERVER['REMOTE_ADDR'],
				'CRE_ID' 	=> $this->getToId(),
				'CRE_DATE' 	=> date('Y-m-d H:i:s'),
				'world_flg'	=> 0,
		);
		$timeStart = $this->db->sqlLoggingInsert('Message', 'saveLog', $data);
		$result = $this->db->insert($data);
		$this->db->sqlLoggingTime($timeStart);
		return $result;
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
			$timeStart = $this->db->sqlLoggingInsert('Message', 'delete', $data);
			$result = $this->db->insert($data);
			$this->db->sqlLoggingTime($timeStart);
			return $result;
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
			$timeStart = $this->db->sqlLogging('Message', 'deleteImage');
			$message = $this->db->get_row();
			$this->db->sqlLoggingTime($timeStart);
			$filename = $this->getImgDir() . $message['image'];
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
			$timeStart = $this->db->sqlLogging('Message', 'isAdminMessage');
			$result = $this->db->get_row();
			$this->db->sqlLoggingTime($timeStart);
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
		$timeStart = $this->db->sqlLogging('Message', 'getAll');
		$result = $this->db->get();
		$this->db->sqlLoggingTime($timeStart);
		return $result;
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
		$this->setToSendPoints('');
		$this->setToResPoints('');
		$this->setTotalMoney('');
	}
	
	/**
	 * Initiate message properties. 
	 * This will be used when constructor has a parameter id otherwise call resetProperties 
	 */
	function initiateProperties() {
		$this->db->select('*');
		$this->db->from('message');
		$this->db->where('id', $this->getId());
		$timeStart = $this->db->sqlLogging('Message', 'initiateProperties');
		$result = $this->db->get_row();
		$this->db->sqlLoggingTime($timeStart);
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
				$imageResult = '画像イメージがサーバのアップロードサイズの上限を超えています。<br/> ';
			}
			$imageResult .= '画像がありません。<br/>';
		}
		if ($_FILES[$src]['size'] == 0) {
			$imageResult .= '画像のサイズが 0 になる。<br/>';
		}
		if ($_FILES[$src]['size'] > 8388608) {
			$imageResult .= 'ファイルサイズの上限を超えています。<br/>';
		}
		$size = GetImageSize($_FILES[$src]['tmp_name']);
		if ($size[2] != 1 && $size[2] != 2 && $size[2] != 3) {
			$imageResult .= 'ファイルは画像ではありません。<br/>';
		}
		if (!is_uploaded_file($_FILES[$src]['tmp_name'])) {
			$imageResult .= 'イメージをアップロードしません。<br/>';
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
	function countMessage($fromId, $toId, $fromType, $toType = '') {
		global $ownerCd;
		$this->db->select('count(1) as total_message');
		$this->db->from('message as m');
		if ($toType == 3) {
			$this->db->where("m.from_type = 3 AND m.to_id ='$fromId'");
		} else {
			$this->db->where("((m.from_id = '$fromId' and m.to_id = '$toId') OR (m.to_id = '$fromId' and m.from_id = '$toId'))");
		}
		$this->db->where("(m.is_sent = 1 OR m.from_type ='$fromType')");
		$this->db->where("(NOT EXISTS (SELECT id FROM message_delete WHERE message_id = m.id AND del_by_type = $fromType))");
		$this->db->where('m.owner_cd = ' . $ownerCd);
		$this->db->limit(1);
		$timeStart = $this->db->sqlLogging('Message', 'countMessage');
		$result = $this->db->get_row();
		$this->db->sqlLoggingTime($timeStart);
		return $result['total_message'];
	}
	
	
	/**
	 * Retrieve the conversation between the two ids
	 * @param int $from_id
	 * @param int $toId
	 * @return retrieve the results of the latest conversation and maximum of 10
	 */
	function getConversation($from_id, $toId, $fromType, $toType = '') {
		global $ownerCd;
		$this->db->select('m.id, m.body, m.image, m.from_type, m.from_id, m.from_date, m.is_read');
		$this->db->from('message as m');
		
		if ($toType == 3) {
			$this->db->where("m.from_type = $toType AND m.to_id ='$from_id'");
		} else {
			$this->db->where("((m.from_id ='$from_id' AND m.to_id='$toId') OR (m.to_id ='$from_id' AND m.from_id = '$toId'))");
			$this->db->where("((m.from_id <>  'fmmanager' AND m.from_id <> 'mmmanager' AND m.from_id <> 'systemcall' AND m.from_id <> 'telecom') OR m.from_type <> 3 )");
		}
		
		$this->db->where("(NOT EXISTS (SELECT id FROM message_delete WHERE message_id = m.id AND del_by_type = $fromType))");
		$this->db->where("(m.is_sent = 1 OR m.from_type ='$fromType')");
		$this->db->where('m.owner_cd = ' . $ownerCd);
		$this->db->order_by('m.from_date DESC, m.id', 'DESC');
		$this->db->limit(0,10);
		$timeStart = $this->db->sqlLogging('Message', 'getConversation');
		$result = $this->db->get();
		$this->db->sqlLoggingTime($timeStart);
		return $result;
	}
	
	/**
	 * Checks the message id that has been sent for the recipient if is_read is 0 updates it to 1 and date readed.
	 * @param int $id
	 * @param int $toId
	 */
	function checkIsRead($id, $toId) {
		$data = array('is_read' => 1, 'read_date' => date('Y-m-d H:i:s'));
		$this->db->where("id = $id AND is_read = 0 AND to_id = '$toId'");
		$timeStart = $this->db->sqlLoggingUpdate('Message', 'checkIsRead', $data, 'message');
		$result = $this->db->update('message', $data);
		$this->db->sqlLoggingTime($timeStart);
		return $result;
	}
	
	function isRead($id) {
		$this->db->select('id');
		$this->db->from('message');
		$this->db->where("id = $id AND is_read = 1");
		$timeStart = $this->db->sqlLogging('Message', 'isRead');
		$result = $this->db->get_row();
		$this->db->sqlLoggingTime($timeStart);
		return ($result) ? true : false;
	}
	
	function getLastMessage($fromId, $toId) {
		$this->db->select('id');
		$this->db->from('message');
		$this->db->where("from_id = '$fromId' AND to_id = '$toId' ");
		$this->db->order_by('1', 'DESC');
		$timeStart = $this->db->sqlLogging('Message', 'getLastMessage');
		$result = $this->db->get_row();
		$this->db->sqlLoggingTime($timeStart);
		return $result['id'];
	}
	
	function strip_emoji($body) {
		return preg_replace('/([\uE000-\uF8FF]|\uD83C[\uDF00-\uDFFF]|\uD83D[\uDC00-\uDDFF])/g', '', $body);
	}
	
	
	
	function check_ng_word(&$msg){
		global $ownerCd,$dbSlave33;
	
		// 禁止ワードを取得
		$this->db->select('ng_word, kubun');
		$this->db->from('mail_ng_word');
		$this->db->where("owner_cd = $ownerCd");
		$timeStart = $this->db->sqlLogging('Message', 'check_ng_word');
		$result = $this->db->get();
		$this->db->sqlLoggingTime($timeStart);
		
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
		if ($war_cnt > 0) {
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
		}
	}
	
	
	
	
	/* MY TRASH
	 	$this->db->join('message m', 'm.id = md.message_id');
		$this->db->where('(m.from_id', $byId);
		$this->db->where('m.to_id', $byId.')', 'OR');
	 */
	
}