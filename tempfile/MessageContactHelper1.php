<?php

require_once 'CommonDb.php';



class MessageContactHelper{
	var $db;
	function MessageContactHelper(){
		$this->db = new CommonDb();
	}
	function getAllFor($id, $type, $options = null, $offset = 0, $limit = 0){
		
		// 		1 = user ,2 =performer
		switch ($type){
			case 1:
				if(is_array($options)){
					if($options['is_blocked'] == 1){
						$this->db->select('contact.user_id,contact.performer_id,block.contact_id,f.user_id,f.nick_name');
						$this->db->from('message_contact AS contact');
						$this->db->join('message_contact_block AS block', 'block.contact_id = contact.id', 'inner');
						$this->db->join('female_profile AS f','f.user_id = contact.performer_id','inner');
						$this->db->where("contact.user_id = '$id' ");
						if(count($options)>1){
							$this->db->like('nick_name',$options['name'],'both');
						}
						
						return $this->db->get();

							
					}else if ($options['is_faved'] == 1){
						$this->db->select('contact.user_id,contact.performer_id,fave.id,fave.contact_id,f.user_id,f.nick_name');
						$this->db->from('message_contact AS contact');
						$this->db->join('message_contact_fave AS fave','fave.id = contact.id','inner');
						$this->db->join('female_profile AS f','f.user_id = contact.performer_id','inner');
						$this->db->where("contact.user_id = '$id' ");
						if(count($options)>1){
							$this->db->like('nick_name',$options['name'],'both');
						}
						return $this->db->get();
							
							
							
					}else if($options['is_from_chat'] == 1) {
						$this->db->select('contact.user_id,contact.performer_id,contact.is_from_chat,f.user_id,f.img,f.hash,f.nick_name');
						$this->db->from('message_contact AS contact');
						$this->db->join('female_profile AS f','f.user_id = contact.performer_id','inner');
						$this->db->where("contact.user_id = '$id' ");
						if(count($options)>1){
							$this->db->like('nick_name',$options['name'],'both');
						}
						return $this->db->get();
							
					}
						
						
				}else{
					$this->db->select('contact.user_id,contact.performer_id,contact.is_from_chat,f.user_id as female_id,f.nick_name,f.hash,f.img,fave.contact_id as fave_id');
					$this->db->from('message_contact AS contact');
					 $this->db->join('message_contact_fave AS fave','fave.id = contact.performer_id','left');
					/*$this->db->join('message_contact_block AS block', 'block.contact_id = contact.id', 'left'); */
					$this->db->join('female_profile AS f','f.user_id = contact.performer_id','left');
					$this->db->where("contact.user_id = '$id' ");
					if($options != null){
						$this->db->like('f.nick_name',$options,'both');
					}
					
					if($limit){
						$this->db->limit($offset,$limit);
					}
					
					return $this->db->get();
					
				}

				break;
			case 2 :
				if(is_array($options)){
					if($options['is_blocked'] == 1){
						$this->db->select('contact.user_id,contact.performer_id,block.contact_id,m.user_id,m.nick_name');
						$this->db->from('message_contact AS contact');
						$this->db->join('message_contact_block AS block', 'block.contact_id = contact.id', 'inner');
						$this->db->join('male_profile AS m','m.user_id = contact.user_id','inner');
						$this->db->where("contact.user_id = '$id' ");
						if(count($options)>1){
							$this->db->like('nick_name',$options['name'],'both');
						}
						return $this->db->get();
					}else if ($options['is_faved'] == 1){
						$this->db->select('contact.user_id,contact.performer_id,fave.contact_id,m.user_id,m.nick_name');
						$this->db->from('message_contact AS contact');
						$this->db->join('message_contact_fave AS fave','fave.contact_id = contact.id','inner');
						$this->db->join('female_profile AS m','m.user_id = contact.user_id','inner');
						$this->db->where("contact.user_id = '$id' ");
						if(count($options)>1){
							$this->db->like('nick_name',$options['name'],'both');
						}
						return $this->db->get();

					}else if($options['is_from_chat'] == 1) {
						$this->db->select('contact.user_id,contact.performer_id,contact.is_from_chat,m.user_id,m.nick_name');
						$this->db->from('message_contact AS contact');
						$this->db->join('male_profile AS m','m.user_id = contact.user_id','inner');
						$this->db->where("contact.user_id = '$id' ");
						if(count($options)>1){
							$this->db->like('nick_name',$options['name'],'both');
						}
						return $this->db->get();
					}
				}else {
					$this->db->select('contact.user_id,contact.performer_id,contact.is_from_chat,m.user_id as male_id,m.nick_name,fave.contact_id as fave_contact,block.contact_id AS block_contact');
					$this->db->from('message_contact AS contact');
					$this->db->join('message_contact_fave AS fave','fave.contact_id = contact.id','inner');
					$this->db->join('message_contact_block AS block', 'block.contact_id = contact.id', 'inner');
					$this->db->join('male_profile AS m','m.user_id = contact.user_id','inner');
					$this->db->where("contact.user_id = '$id' ");
					if($options != null){
						$this->db->like('nick_name',$options,'both');
					}
					
					return $this->db->get();
				}
				break;
		}
	}

	function getAllBlockedBy($id, $type,$options=null){
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
		return $toReturn  = $this->getAllFor($id, $type, $optionsArray);

	}

	function getAllFavedBy($id, $type,$options=null){
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
		return $toReturn  = $this->getAllFor($id, $type, $optionsArray);
	}
	function getAllFromChatOf($id, $type,$options=null){
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

		return $toReturn	=	$this->getAllFor($id, $type, $optionsArray);
	}
}



