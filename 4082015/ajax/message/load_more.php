<?php 
require_once 'CommonDb.php';
require_once 'message/Message.php';
require_once 'message/EmojiClass.php';
/* ****************************************************
 * load all previous messages of the user or  performer
 * all post data are came from the ajax post 
 *****************************************************/
if(isset($_POST['page']) && class_exists('CommonDb') && class_exists('Message') && class_exists('EmojiClass')){
	$emoji = new EmojiClass();
	$message = new Message();
	$db = new CommonDb();
	$page 			= intval($_POST['page']);
	$toType			= $_POST['to_type'];
	$fromType		= $_POST['from_type'];
	$image			= $_POST['image'];
	$fulldate		= $_POST['fulldate'];
	$lastId 		= (isset($_POST['last_id'])) ? $_POST['last_id'] : '0';
	$recordsPerPage = 10;
	$offset = ($page - 1) * $recordsPerPage;
	$fromId = mb_convert_encoding(urldecode($_POST['from_id']), "EUC-JP","UTF-8") ;
	$toId =mb_convert_encoding(urldecode($_POST['to_id']), "EUC-JP","UTF-8") ;
	
	$db->select('m.id, m.owner_cd, m.body, m.image, m.from_type, m.from_id, m.to_type, m.to_id, m.from_date, m.is_sent,m.is_read');
	$db->from('message as m');
	$db->where("(m.from_date < '$fulldate' OR (m.from_date = '$fulldate' AND m.id < $lastId))");
	$db->where("( NOT EXISTS(SELECT message_id from message_delete WHERE message_id = m.id AND  del_by_type = $fromType ))");
	$db->where("((m.from_id ='$fromId' AND m.to_id= '$toId' ) OR (m.to_id ='$fromId' AND m.from_id = '$toId'))");
	$db->where("(m.is_sent = 1 OR m.from_type ='$fromType')");
	$db->where('m.owner_cd = 1');
	$db->order_by('m.from_date DESC,m.id','DESC');
	$db->limit($offset,$recordsPerPage);
	$timeStart = $db->sqlLogging('load_more', 'load more ajax');
	$result = $db->get();
	$db->sqlLoggingTime($timeStart);
	
	
	
	
	
	
	$date = '';
	$html='';
	$result = array_reverse($result);
	$days = array(
					'Sun'=> 'Æü',
					'Mon'=>'·î',
					'Tue'	=>'²Ð',
					'Wed'=>'¿å',
					'Thu'	=>'ÌÚ',
					'Fri'=>'¶â',
					'Sat'	=>'ÅÚ');
	
		 foreach($result as $res){
		 	$read = '';
			$body = (!empty($res->image)) ? "<img class='resized' src='/imgs/message/$res->image' >" : '';
			$body .= $emoji->getEmojiHtml($res->body);
			$newDate = date('m/d',strtotime($res->from_date));
			$posted = date('H:i',strtotime($res->from_date));
			$day = date('D',strtotime($res->from_date));
			$japaneseDay  = $days[$day];
			$messageId = $res->id;
			$notRead = ($res->is_read == 0 ) ? 'not-read' : '';
			if ($fromId != $res->from_id || $toType == 3) {
				switch ($toType) {
					case 1 : $imageProfile =  $image; break;
					case 2 : $imageProfile =  $image; break;
					case 3 : $imageProfile = $image;
				}
				$position = 'message_detail_item--left';
				$performer = 1;
			}else{
				$position = 'message_detail_item--right';
				$read = ($res->is_read == 1) ? '´ûÆÉ' : '' ;
			}
			
			
			if( $date=='' || $date != $newDate) {
				$html  .= "<div class='postdate_box'>
									<span class='postdate' value='$newDate'>$newDate ($japaneseDay)</span>
								</div>";
			}
			
			
			
			$html .=	"<div message-id='$messageId' class='cf message_detail_item $position'>";
			if($performer == 1){
				
				$html .= "<div class='thumb'>
			                          	<img src='/sp/image/spacer.gif'  style=\"background-image: url('$imageProfile')\" />
			                      </div>";
			}
			$html .="<div class='bubble'>
								<p class='desc'> $body </p>
							</div>
								<span class='posttime $notRead' ><p>$read</p>$posted</span>
						</div><!-- end of message_detail_item -->";
			
			
			$date = $newDate;
			$performer = '';
		
		
		//check all messages that are not read
		$message->checkIsRead($res->id,$res->from_id);
		
	}
	echo $html;
} else {
	echo 'fail';
}
/********************************* 
 *@params int $id = the id of message that are not unread
 *@params string to_id = the user_id  of where the message will be sent
 *function to update all the unread messages   
 ********************************/
function checkIsRead($id,$toId){
	$db = new CommonDb();
	$db->where("id = $id AND is_read= 0 AND to_id = '$toId' ");
	$db->update('message',array('is_read'=> 1,'read_date'=>"NOW()"));
	
	
	
}