<?
//////////////////////////////////////////////////////////////////////////////////////////
//	�ե����४�֥������ȴ������饹
//////////////////////////////////////////////////////////////////////////////////////////
//require_once('common_db_mysql.inc');								//PEAR DB���饹������
require_once('common_db.inc');								//PEAR DB���饹������

//������
define ("FO_TEXT", 			0);
define ("FO_TEXT_PASSWORD", 		1);
define ("FO_TEXT_AREA", 		2);
define ("FO_HIDDEN", 			3);
define ("FO_RADIO", 			4);
define ("FO_SELECT", 			5);
define ("FO_CHECK_BOX", 		6);
define ("FO_SUBMIT", 			7);
define ("FO_RESET", 			8);
define ("FO_BUTTON", 			9);
/*** ��2004.03.19�� ***/
define ("FO_DISPLAY", 			10);
define ("FO_DISPLAY_SELECT", 	11);
define ("FO_DISPLAY_CHECK_BOX",	12);
/*** ��2004.03.19�� ***/
define ("FO_FILE", 			13);

//���饹���
class FormObject{
	var $form_name;					//�ե�����̾
	var $form_data;					//�ե�������Υե����४�֥�����������
	var $form_value;				//���ڡ��������Ϥ��줿�ǡ����ʤɤ��ݻ�������
	var $errorMsg;
	var $erroePos;

	//���󥹥ȥ饯��
	//p_form_name	:�ե�����̾
	//page_mode		:�ե�����μ����10:���ϲ��̣���11:���ϲ��̣� 20:�����Ѳ��̣�����������
	function FormObject($p_form_name,$page_mode = 10){
		global $dbSlave;

		//�ѿ��ν����
		$this->erroePos = 0;
		$this->errorMsg = Array();
		$this->form_name = $p_form_name;
		$sql = "select 	object_name,
						object_type,
						label,
						options,
						select_box,
						default_value
				from 	sys_form_tables
				where 	form_name = ?";
		$sth = $dbSlave->prepare($sql);
		$result = $dbSlave->execute($sth, $p_form_name);
		if(DB::isError($result)){
			print $sql;
			err_proc($result->getMessage());
			}
		while($row = $result->fetchRow(DB_FETCHMODE_ORDERED)){
			$this->form_data[$row[0]]["object_type"] = $row[1];
			$this->form_value[$row[0]] = $row[5];
			$this->form_data[$row[0]]["label"] = $row[2];
			$this->form_data[$row[0]]["options"] = $row[3];
//			$this->form_data[$row[0]]["select_box"] = $row[4];
			//���쥯�ȥܥå����ȥ饸���ܥ���ξ��Ϲ��ܤ��ɤ߹��ߤ򤹤�ɬ�פ�����
			//�����å��ܥå������ͤ�����Τ��ɤ߹��ߤ�ɬ�פ�����
			if($this->form_data[$row[0]]["object_type"] == FO_RADIO || 								// 4
				$this->form_data[$row[0]]["object_type"] == FO_SELECT || 							// 5
			/*** ��2004.03.19�� ***/
				$this->form_data[$row[0]]["object_type"] == FO_DISPLAY_SELECT || 					// 11
				$this->form_data[$row[0]]["object_type"] == FO_DISPLAY_CHECK_BOX || 				// 12
			/*** ��2004.03.19�� ***/
				$this->form_data[$row[0]]["object_type"] == FO_CHECK_BOX){							// 6
				$sql = "select 	label,
								selected_value
						from 	sys_select_box
						where 	select_box_name = ?
						and 	smode = ?
						order by select_id";
				$sth2 = $dbSlave->prepare($sql);
				$son = Array($row[4],$page_mode);
				$result2 = $dbSlave->execute($sth2, $son);
				if(DB::isError($result2)){
					print $sql;
					err_proc($result2->getMessage());
					}
				//���ꤵ�줿�ӥ⡼�ɤΥǡ������ʤ����ǥե���ȤΥǡ������ߤ���Ǥߤ�
				if($result2->numRows()<=0){
					$son = Array($row[4],"10");
					$result2 = $dbSlave->execute($sth2, $son);
					if(DB::isError($result2)){
						print $sql;
						err_proc($result2->getMessage());
						}
					}
				while($row2 = $result2->fetchRow(DB_FETCHMODE_ORDERED)){
					$this->form_data[$row[0]]["select_box"][$row2[1]] = $row2[0];
					}
				}
			}
		}

	//�ե����४�֥��������ѥ�٥����
	function getLabel($objectName){
		return $this->form_data[$objectName]["label"];
		}
	//�ե����४�֥�������HTML����
	function getItem2($objectName,$setName,$default_value = ""){

		//�ǥե�����ͤ����ꤵ��Ƥ���Ф����ͤ򥻥å�
		if($default_value != ""){
			$d_val = $default_value;
		}else{
			//�⥸�塼����ǥǥե�����ͤ����ꤵ��Ƥ���Ф����ͤ򥻥å�
			if(isset($this->form_value[$objectName])){
				if(is_array($this->form_value[$objectName])){
					foreach($this->form_value[$objectName] as $k => $v){
						$d_val[$k] = $v;
					}
				} else {
  					$d_val = $this->form_value[$objectName];
				}
			}else{
				$d_val = "";
			}
		}
		//�ƥ����ȥܥå����ξ��
		if($this->form_data[$objectName]["object_type"] == FO_TEXT){
			$html = "<input type=\"text\" name=\"$setName\" id=\"$objectName\" " . $this->form_data[$objectName]["options"] . " value=\"".$d_val."\">";
			return $html;
			}
		//�ѥ���ɥƥ����Ȥξ��
		if($this->form_data[$objectName]["object_type"] == FO_TEXT_PASSWORD){
			$html = "<input type=\"password\" name=\"$setName\" " . $this->form_data[$objectName]["options"] . " value=\"".$d_val."\">";
			return $html;
			}
		//�ƥ����ȥ��ꥢ�ξ��
		if($this->form_data[$objectName]["object_type"] == FO_TEXT_AREA){
			$html = "<textarea name=\"$setName\" " . $this->form_data[$objectName]["options"] . ">".$d_val."</textarea>";
			return $html;
			}
		//HIDDEN�ξ��
		if($this->form_data[$objectName]["object_type"] == FO_HIDDEN){
			$html = "<input type=\"hidden\" name=\"$setName\" " . $this->form_data[$objectName]["options"] . " value=\"".$d_val."\">";
			return $html;
			}
		//���֥ߥåȥܥ���ξ��ξ��
		if($this->form_data[$objectName]["object_type"] == FO_SUBMIT){
			$html = "<input type=\"submit\" name=\"$setName\" " . $this->form_data[$objectName]["options"] . " value=\"".$this->form_data[$objectName]["label"]."\">";
			return $html;
			}
		//���֥ߥåȥܥ���ξ��ξ��
		if($this->form_data[$objectName]["object_type"] == FO_BUTTON){
			$html = "<input type=\"button\" name=\"$setName\" " . $this->form_data[$objectName]["options"] . " value=\"".$this->form_data[$objectName]["label"]."\">";
			return $html;
			}
		//���֥ߥåȥܥ���ξ��ξ��
		if($this->form_data[$objectName]["object_type"] == FO_RESET){
			$html = "<input type=\"reset\" name=\"$setName\" " . $this->form_data[$objectName]["options"] . " value=\"".$this->form_data[$objectName]["label"]."\">";
			return $html;
			}
		//���쥯�ȥܥå����ξ��
		if($this->form_data[$objectName]["object_type"] == FO_SELECT){
			$html = "<select name=\"$setName\"". $this->form_data[$objectName]["options"]  . ">";
			if(isset($this->form_data[$objectName]["select_box"])){
				foreach($this->form_data[$objectName]["select_box"] as $key => $value){
					if($d_val == $key){
						$html .= "<option value=\"$key\" selected=\"selected\">$value</option>\n";
					}else{
						$html .= "<option value=\"$key\">$value</option>\n";
						}
					}
				}
			$html .= "</select>";
			return $html;
			}

		//�饸���ܥ���ξ��
		if($this->form_data[$objectName]["object_type"] == FO_RADIO){
			$html = "";
			if(isset($this->form_data[$objectName]["select_box"])){
				foreach($this->form_data[$objectName]["select_box"] as $key => $value){
					if($d_val == $key){
						$html .= "<input name=\"$setName\" type=\"radio\" value=\"$key\" checked>" . $value;
					}else{
						$html .= "<input name=\"$setName\" type=\"radio\" value=\"$key\">" . $value;
						}
					}
				}
			return $html;
			}
		//�����å��ܥå����ξ��
		if($this->form_data[$objectName]["object_type"] == FO_CHECK_BOX){
			$html = "";
			if(isset($this->form_data[$objectName]["select_box"])){
				foreach($this->form_data[$objectName]["select_box"] as $key => $value){
					if($key != 0){
						// 2004/11/25 Mod by N.Takeyoshi start
//						$html .= "<input type=\"checkbox\" name=\"" .$objectName. "[]\" " . $this->form_data[$objectName]["options"] . " value=\"".$key . "\"";
//						if(is_array($d_val)){
						if(is_array($d_val)){
							$html .= "<input type=\"checkbox\" name=\"" .$objectName. "[]\" " . $this->form_data[$objectName]["options"] . " value=\"".$key . "\"";
						// 2004/11/25 Mod end
							foreach($d_val as $k => $v){
//							print $v. "+".$key;
								if($v == $key){
									$html .= " checked";
								}
							}
						}
						// 2004/11/25 Add by N.Takeyoshi start
						else {
							$html .= "<input type=\"checkbox\" name=\"" .$objectName. "\" " . $this->form_data[$objectName]["options"] . " value=\"".$key . "\"";
							if($d_val == $key){
								$html .= " checked";
							}
						}
						// 2004/11/25 Add end
				  		$html .= ">".$value;
					}
				}
			}
			return $html;
		}
/*** ��2004.03.19�� ***/
		//ɽ���Τߤξ��
		if($this->form_data[$objectName]["object_type"] == FO_DISPLAY){
			$html = $this->getLabelValue($objectName)."<input type=\"hidden\" name=\"$setName\" id=\"$objectName\" value=\"".$d_val."\">";
			return $html;
			}
		//ɽ���Τߤξ��(���쥯�ȥܥå���)
		if($this->form_data[$objectName]["object_type"] == FO_DISPLAY_SELECT){
			$html = $this->getLabelValue($objectName)."<input type=\"hidden\" name=\"$setName\" id=\"$objectName\" value=\"".$d_val."\">";
			return $html;
			}
		//ɽ���Τߤξ��(�����å��ܥå���)
		if($this->form_data[$objectName]["object_type"] == FO_DISPLAY_CHECK_BOX){
			$html = "";
			if(isset($this->form_data[$objectName]["select_box"])){
				foreach($this->form_data[$objectName]["select_box"] as $key => $value){
					if($key != 0){
						$html .= "<input type=\"hidden\" name=\"" .$objectName. "[]\" " . $this->form_data[$objectName]["options"] . " value=\"".$key . "\"";
						if(is_array($d_val)){
							foreach($d_val as $k => $v){
								if($v == $key){
									//$html .= " checked";
									}
								}
							}
							$html .= ">".$value;
						}
					}
				}
			return $html;
			}
/*
		if($this->form_data[$objectName]["object_type"] == FO_DISPLAY_CHECK_BOX){
			$html = $this->getLabelValue($objectName,$d_val)."<input type=\"hidden\" name=\"".$objectName."[]\" value=\"".$d_val."\">";
			return $html;
			}
*/
/*** ��2004.03.19�� ***/
		}
	//�ե����४�֥�������HTML����
	function getItem($objectName,$default_value = ""){

		//�ǥե�����ͤ����ꤵ��Ƥ���Ф����ͤ򥻥å�
		if($default_value != ""){
			$d_val = $default_value;
		}else{
			//�⥸�塼����ǥǥե�����ͤ����ꤵ��Ƥ���Ф����ͤ򥻥å�
			if(isset($this->form_value[$objectName])){
				if(is_array($this->form_value[$objectName])){
					foreach($this->form_value[$objectName] as $k => $v){
						$d_val[$k] = $v;
					}
				} else {
  					$d_val = $this->form_value[$objectName];
				}
			}else{
				$d_val = "";
			}
		}
		//�ƥ����ȥܥå����ξ��
		if($this->form_data[$objectName]["object_type"] == FO_TEXT){
			$html = "<input type=\"text\" name=\"$objectName\" id=\"$objectName\" " . $this->form_data[$objectName]["options"] . " value=\"".str_replace('"','&quot;',$d_val)."\">";
			return $html;
			}
		//�ѥ���ɥƥ����Ȥξ��
		if($this->form_data[$objectName]["object_type"] == FO_TEXT_PASSWORD){
			$html = "<input type=\"password\" name=\"$objectName\" " . $this->form_data[$objectName]["options"] . " value=\"".$d_val."\">";
			return $html;
			}
		//�ƥ����ȥ��ꥢ�ξ��
		if($this->form_data[$objectName]["object_type"] == FO_TEXT_AREA){
			$html = "<textarea name=\"$objectName\" id=\"$objectName\" " . $this->form_data[$objectName]["options"] . ">".$d_val."</textarea>";
			return $html;
			}
		//HIDDEN�ξ��
		if($this->form_data[$objectName]["object_type"] == FO_HIDDEN){
			$html = "<input type=\"hidden\" name=\"$objectName\" " . $this->form_data[$objectName]["options"] . " value=\"".$d_val."\">";
			return $html;
			}
		//���֥ߥåȥܥ���ξ��ξ��
		if($this->form_data[$objectName]["object_type"] == FO_SUBMIT){
			$html = "<input type=\"submit\" name=\"$objectName\" " . $this->form_data[$objectName]["options"] . " value=\"".$this->form_data[$objectName]["label"]."\">";
			return $html;
			}
		//���֥ߥåȥܥ���ξ��ξ��
		if($this->form_data[$objectName]["object_type"] == FO_BUTTON){
			$html = "<input type=\"button\" name=\"$objectName\" " . $this->form_data[$objectName]["options"] . " value=\"".$this->form_data[$objectName]["label"]."\">";
			return $html;
			}
		//���֥ߥåȥܥ���ξ��ξ��
		if($this->form_data[$objectName]["object_type"] == FO_RESET){
			$html = "<input type=\"reset\" name=\"$objectName\" " . $this->form_data[$objectName]["options"] . " value=\"".$this->form_data[$objectName]["label"]."\">";
			return $html;
			}
		//���쥯�ȥܥå����ξ��
		if($this->form_data[$objectName]["object_type"] == FO_SELECT){
			$html = "<select name=\"$objectName\" id=\"$objectName\" ". $this->form_data[$objectName]["options"]  . ">";
			if(isset($this->form_data[$objectName]["select_box"])){
				foreach($this->form_data[$objectName]["select_box"] as $key => $value){
					if($d_val == $key){
						$html .= "<option value=\"$key\" selected=\"selected\">$value</option>\n";
					}else{
						$html .= "<option value=\"$key\">$value</option>\n";
						}
					}
				}
			$html .= "</select>";
			return $html;
			}

		//�饸���ܥ���ξ��
		if($this->form_data[$objectName]["object_type"] == FO_RADIO){
			$html = "";
			if(isset($this->form_data[$objectName]["select_box"])){
				foreach($this->form_data[$objectName]["select_box"] as $key => $value){
					if($d_val == $key){
						$html .= "<input name=\"$objectName\" type=\"radio\" value=\"$key\" checked>" . $value;
					}else{
						$html .= "<input name=\"$objectName\" type=\"radio\" value=\"$key\">" . $value;
						}
					}
				}
			return $html;
			}
		//�����å��ܥå����ξ��
		if($this->form_data[$objectName]["object_type"] == FO_CHECK_BOX){
			$html = "";
			if(isset($this->form_data[$objectName]["select_box"])){
				foreach($this->form_data[$objectName]["select_box"] as $key => $value){
					if($key != 0){
						// 2004/11/25 Mod by N.Takeyoshi start
//						$html .= "<input type=\"checkbox\" name=\"" .$objectName. "[]\" " . $this->form_data[$objectName]["options"] . " value=\"".$key . "\"";
//						if(is_array($d_val)){
						if(is_array($d_val)){
							$html .= "<label><input id=\"$elId\" type=\"checkbox\" name=\"" .$objectName. "[]\" " . $this->form_data[$objectName]["options"] . " value=\"".$key . "\"";
						// 2004/11/25 Mod end
							foreach($d_val as $k => $v){
//								print $v. "+".$key;
								if($v == $key){
									$html .= " checked";
								}
							}
						}
						// 2004/11/25 Add by N.Takeyoshi start
						else {
							$html .= "<input type=\"checkbox\" name=\"" .$objectName. "\" " . $this->form_data[$objectName]["options"] . " value=\"".$key . "\"";
							if($d_val == $key){
								$html .= " checked";
							}
						}
						// 2004/11/25 Add end
				  		$html .= ">".$value."</label>";
					}
				}
			}
			return $html;
		}
/*** ��2004.03.19�� ***/
		//ɽ���Τߤξ��
		if($this->form_data[$objectName]["object_type"] == FO_DISPLAY){
			$html = $this->getLabelValue($objectName)."<input type=\"hidden\" name=\"$objectName\" id=\"$objectName\" value=\"".$d_val."\">";
			return $html;
			}
		//ɽ���Τߤξ��(���쥯�ȥܥå���)
		if($this->form_data[$objectName]["object_type"] == FO_DISPLAY_SELECT){
			$html = $this->getLabelValue($objectName)."<input type=\"hidden\" name=\"$objectName\" id=\"$objectName\" value=\"".$d_val."\">";
			return $html;
			}
		//ɽ���Τߤξ��(�����å��ܥå���)
		if($this->form_data[$objectName]["object_type"] == FO_DISPLAY_CHECK_BOX){
			$html = "";
			if(isset($this->form_data[$objectName]["select_box"])){
				foreach($this->form_data[$objectName]["select_box"] as $key => $value){
					if($key != 0){
						$html .= "<input type=\"hidden\" name=\"" .$objectName. "[]\" " . $this->form_data[$objectName]["options"] . " value=\"".$key . "\"";
						if(is_array($d_val)){
							foreach($d_val as $k => $v){
								if($v == $key){
									//$html .= " checked";
									}
								}
							}
							$html .= ">".$value;
						}
					}
				}
			return $html;
			}
/*
		if($this->form_data[$objectName]["object_type"] == FO_DISPLAY_CHECK_BOX){
			$html = $this->getLabelValue($objectName,$d_val)."<input type=\"hidden\" name=\"".$objectName."[]\" value=\"".$d_val."\">";
			return $html;
			}
*/
/*** ��2004.03.19�� ***/
		//���åץ����ɥե�����ξ��
		if($this->form_data[$objectName]["object_type"] == FO_FILE){
			$html = "<input type=\"file\" name=\"$objectName\" id=\"$objectName\" " . $this->form_data[$objectName]["options"] . " value=\"".$d_val."\">";
			return $html;
		}
	}

// 2004/11/24 add start matsuura
    //�ե���������Ϥ��줿�����������POST�ǡ�
    //�ե����४�֥������Ȥ�ɽ��������Ϥ錄�äƤ����ǡ������ǥե�����ͤȤ��ƻ��Ѥ����
    function importFormData(){
        foreach($_POST as $key => $value){
            if($key != "Submit" and $key != "mode" and $key != "reloadCheck"){
                $this->form_value[$key] = $value;
			}
		}
    }
// 2004/11/24 add end matsuura

	//�ե���������Ϥ��줿�����������GET�ǡ�
	//�ե����४�֥������Ȥ�ɽ��������Ϥ錄�äƤ����ǡ������ǥե�����ͤȤ��ƻ��Ѥ����
	function importFormDataByGet(){
		//�ͤ����äƤ��ʤ���Τ⤢��Τǽ�������Ƥ���
		foreach($this->form_data as $key => $value){
			$this->form_value[$key] = "";
		}

		foreach($_GET as $key => $value){
			if($key != "Submit" and $key != "submit" and $key != "mode"){
				if(is_array($_GET[$key])){
					foreach($_GET[$key] as $i => $v){
						$this->form_value[$key][$v] = trim($v);
					}
				} else {
					$this->form_value[$key] = trim($value);
				}
			}

		}
	}
	//���ϥ����å�
	function validation(){
		global $dbSlave;
		$check_flg = true;

		$sql  = "select validation_type,";		//0
		$sql .= "validation_operator,";			//1
		$sql .= "validation_value,";			//2
		$sql .= "error_msg, ";					//3
		$sql .= "object_name ";					//4
		$sql .= "from sys_validation_check ";
		$sql .= "where form_name = ? ";
		$sql .= "order by check_id";
		$sth = $dbSlave->prepare($sql);
		$result = $dbSlave->execute($sth, $this->form_name);
		if(DB::isError($result)){
			print $sql;
			err_proc($result->getMessage());
			}
		while($row = $result->fetchRow()){
			$value = $this->form_value[$row[4]];
			if(!$this->validationCheck($row[4],$row[0],$row[1],$row[2],$row[3],$value)){
				$check_flg = false;
				}
			}
		return $check_flg;
		}
	//���顼��å���������
	function locateTopValidateError(){
		$this->erroePos = 0;
		}
	//���顼��å����������������
	function getValidateErrorNum(){
		return count($this->errorMsg);
		}
	//���顼��å���������
	function getValidateError(){
		//�Ǹ�ޤǤ��ä��ʤ�
		if(count($this->errorMsg) < $this->erroePos){
			return NULL;
			}
		$tmp = $this->errorMsg[$this->erroePos];
		$this->erroePos++;
		return $tmp;
		}
	//�ͼ���
	function getLabelValue($objectName,$default_value = ""){
		if(!isset($this->form_data[$objectName])){
			return $default_value;
			}
		if($this->form_data[$objectName]["object_type"] == FO_HIDDEN){
			return "";
			}
		if($default_value != ""){
			$d_val = $default_value;
		}else{
			//�⥸�塼����ǥǥե�����ͤ����ꤵ��Ƥ���Ф����ͤ򥻥å�
			if(isset($this->form_value[$objectName])){
				if(is_array($this->form_value[$objectName])){
					foreach($this->form_value[$objectName] as $k => $v){
						$d_val[$k] = $v;
					}
				} else {
					$d_val = $this->form_value[$objectName];
				}
			}else{
				$d_val = "";
			}
		}
		//���쥯�ȥܥå����ξ��
	/*** ��2004.03.19�� ***/
		//if($this->form_data[$objectName]["object_type"] == FO_SELECT){
		if($this->form_data[$objectName]["object_type"] == FO_SELECT || $this->form_data[$objectName]["object_type"] == FO_DISPLAY_SELECT){
	/*** ��2004.03.19�� ***/
			if(!isset($this->form_data[$objectName]["select_box"][$d_val])){
				return $d_val;
				}
			return $this->form_data[$objectName]["select_box"][$d_val];
			}
		//�饸���ܥ���ξ��
		if($this->form_data[$objectName]["object_type"] == FO_RADIO){
			return $this->form_data[$objectName]["select_box"][$d_val];
			}
		//�����å��ܥå����ξ��
	/*** ��2004.03.19�� ***/
		//if($this->form_data[$objectName]["object_type"] == FO_CHECK_BOX){
		if($this->form_data[$objectName]["object_type"] == FO_CHECK_BOX || $this->form_data[$objectName]["object_type"] == FO_DISPLAY_CHECK_BOX){
	/*** ��2004.03.19�� ***/
			if(is_array($d_val)){
				$out_val = "";
				foreach($d_val as $k => $v){
					$out_val .= $this->form_data[$objectName]["select_box"][$d_val[$k]] . "  ";
				}
				return $out_val;
			} else {
				if(isset($this->form_data[$objectName]["select_box"][0])){
					return $this->form_data[$objectName]["select_box"][0];
				}
			}
			return "";
		}
		//�ƥ����ȥ��ꥢ�ξ��
		if($this->form_data[$objectName]["object_type"] == FO_TEXT_AREA){
			return nl2br($d_val);
		}

		//����ʳ��Ϥ��Τޤ�
		return $d_val;
	}
	//�ͼ���
	function getValue($objectName,$default_value = ""){
		if($default_value != ""){
			$d_val = $default_value;
		}else{
			//�⥸�塼����ǥǥե�����ͤ����ꤵ��Ƥ���Ф����ͤ򥻥å�
			if(isset($this->form_value[$objectName])){
				if(is_array($this->form_value[$objectName])){
					foreach($this->form_value[$objectName] as $k => $v){
						$d_tmp[$k] = $v;
					}
					$d_val = $this->myserialize($d_tmp);
				} else {
  					$d_val = $this->form_value[$objectName];
				}
			}else{
				$d_val = "";
				}
			}
		return $d_val;
		}
	//������
	function setValue($objectName,$pvalue){
		if(!isset($this->form_data[$objectName]["object_type"])){
		/*** ��2004.03.19�� ***/
			//print "FormObject->setValue() : no set OBJECTNAME in sys_form_tables = ".$objectName."<br>";
		/*** ��2004.03.19�� ***/
			return;
			}
		if(isset($pvalue)){
	/*** ��2004.03.19�� ***/
			//if($this->form_data[$objectName]["object_type"] == FO_CHECK_BOX){
			if($this->form_data[$objectName]["object_type"] == FO_CHECK_BOX || $this->form_data[$objectName]["object_type"] == FO_DISPLAY_CHECK_BOX){
	/*** ��2004.03.19�� ***/
				$usvalue = $this->myunserialize($pvalue);
				if(is_array($usvalue)){
					foreach($usvalue as $k => $v){
						$this->form_value[$objectName][$k] = $v;
					}
				}
			} else {
	  			$this->form_value[$objectName] = $pvalue;
			}
		} else {
			$this->form_value[$objectName] = "";
		}
	return;
	}
	//�ե�����Ǥ錄�äƤ����ǡ�����HIDDEN�Ǽ��Υե�������Ϥ�
	function getOnlyHiddenData(){
		$html = "";
		foreach($this->form_value as $key => $value){
			if($this->form_data[$key]["object_type"] == FO_HIDDEN){
				if(is_array($value)){
					foreach($value as $k => $v){
						$html .= "<input type=\"hidden\" name=\"" .$key. "[]\" value=\"$k\">\n";
					}
				} else {
					$html .= "<input type=\"hidden\" name=\"$key\" value=\"$value\">\n";
				}
			}
		}
		return $html;
	}
	function getHiddenData(){
		$html = "";
		foreach($this->form_value as $key => $value){
			if(is_array($value)){
				foreach($value as $k => $v){
					$html .= "<input type=\"hidden\" name=\"" .$key. "[]\" value=\"$k\">\n";
				}
			} else {
				$html .= "<input type=\"hidden\" name=\"$key\" value=\"$value\">\n";
			}

		}
		return $html;
		}
	//���ƤΥ��֥�������̾�����
	function getObjectName(){
		$object_Name = array();
		foreach($this->form_data as $key => $value){
			array_push($object_Name,$key);
			}
		return $object_Name;
		}

	//���ꥢ�饤���ؿ�
	function myserialize($assoc){
		if(is_array($assoc)){
			$sdstr = "";
			foreach($assoc as $key => $value){
				$sdstr .= $value . ",";
			}
		} else {
			$sdstr = "";
		}
	return $sdstr;
	}
	//���󥷥ꥢ�饤���ؿ�
	function myunserialize($sdstr){
		if(isset($sdstr)){
			if($sdstr != "" and $sdstr != ","){
				$assoc_tmp = split(",",$sdstr);
				if(is_array($assoc_tmp)){
					$assoc = "";
					foreach($assoc_tmp as $value){
						if($value != ""){
							$assoc[$value] = $value;
						}
					}
					return $assoc;
				} else {
					return "";
				}
			}
//		print "error:myunserialize() serialized string is not set.";
		return "";
		}
//	print "error:myunserialize() serialized string is not set.";
	return "";
	}

  //���ѥ��쥯�ȥܥå����ѥե�����ǡ������å�
  function setItemFromSQL($objectName,$sql,$select_msg=true){
		global $dbSlave;

		$result = $dbSlave->query($sql);
		if(DB::isError($result)){
			print $sql;
			err_proc($result->getMessage());
			}
		if($select_msg){
			$this->form_data[$objectName]["select_box"][0] = "���򤷤Ƥ�������";
			}
		while($row = $result->fetchRow()){
			$this->form_data[$objectName]["select_box"][$row[0]] = $row[1];
			}
		}
	//���쥯�ȥܥå��������ƥ���
	function setClearItem($objectName){
		$this->form_data[$objectName]["select_box"] = NULL;
		}


	//���ϥǡ��������å�
	function validationCheck($object_name,$validation_type,$validation_operator,$validation_value,$error_msg,$value){
		if($value != ""){
			if($validation_type == "mail"){				//�᡼������å�
				if(strlen($value) < 2){
					array_push($this->errorMsg,$error_msg);
					return false;
					}
				if(!preg_match("/^[^@]+@[^.]+\..+/", $value)){
					array_push($this->errorMsg,$error_msg);
					return false;
					}
				if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/",$value)) {
					array_push($this->errorMsg,$error_msg);
					return false;
					}
				}
			if($validation_type == "hankaku_only"){		//Ⱦ�ѱѿ��������å�
				if(ereg("[^0-9a-zA-Z]+", $value)){
					array_push($this->errorMsg,$error_msg);
					return false;
					}
				}
			if($validation_type == "hankaku+"){		//Ⱦ�ѱѿ���+��������å�
				if(ereg("[^0-9a-zA-Z_~:./-@]+", $value)){
					array_push($this->errorMsg,$error_msg);
					return false;
					}
				}
			if($validation_type == "num_only"){			//Ⱦ�ѿ��������å�
// 2004/11/29 Mod by N.Takeyoshi start
//				if(ereg("[^0-9]+", $value)){
				if(ereg("[^0-9_.]+", $value)){
// 2004/11/29 Mod end
					array_push($this->errorMsg,$error_msg);
					return false;
					}
				}
			if($validation_type == "num+"){			//Ⱦ�ѿ���+��������å�
				if(ereg("[^0-9_.-]+", $value)){
					array_push($this->errorMsg,$error_msg);
					return false;
					}
				}

			if($validation_type == "speace_exist"){		//����¸�ߥ����å�
				if(mbereg_replace(' ','',$value) != $value){
					array_push($this->errorMsg,$error_msg);
					return false;
					}
				if(mbereg_replace('��','',$value) != $value){
					array_push($this->errorMsg,$error_msg);
					return false;
					}
				}
			if($validation_type == "zenkaku_only"){		//���ѥ����å�
				if(mb_convert_kana($value,"ASKV") != $value){
					array_push($this->errorMsg,$error_msg);
					return false;
					}
				}
			if($validation_type == "date"){		//����
				if(!preg_match("/^(\d\d\d\d)\/(\d\d)\/(\d\d)$/",$value,$year_month_day)){
					array_push($this->errorMsg,$error_msg);
					return false;
					}
				if(!checkdate ($year_month_day[2],$year_month_day[3],$year_month_day[1])){
					array_push($this->errorMsg,$error_msg);
					return false;
					}
				}
			}

		if($validation_type == "length"){				//ʸ���������å�
			$length = mb_strlen($value);
			switch($validation_operator){
				case '>':
					if($validation_value <= $length){
						array_push($this->errorMsg,$error_msg);
						return false;
						}
					break;
				case '<':
					if($validation_value >= $length){
						array_push($this->errorMsg,$error_msg);
						return false;
						}
					break;
				case '>=':
					if($validation_value < $length){
						array_push($this->errorMsg,$error_msg);
						return false;
						}
					break;
				case '<=':
					if($validation_value > $length){
						array_push($this->errorMsg,$error_msg);
						return false;
						}
					break;
				case '==':
					if($validation_value != $length){
						array_push($this->errorMsg,$error_msg);
						return false;
						}
					break;
				}
			}
		if($validation_type == "byte"){					//�Х��ȿ������å�
			$byte = strlen($value);
			switch($validation_operator){
				case '>':
					if($validation_value < $byte){
						array_push($this->errorMsg,$error_msg);
						return false;
						}
					break;
				case '<':
					if($validation_value > $byte){
						array_push($this->errorMsg,$error_msg);
						return false;
						}
					break;
				case '>=':
					if($validation_value <= $byte){
						array_push($this->errorMsg,$error_msg);
						return false;
						}
					break;
				case '<=':
					if($validation_value >= $byte){
						array_push($this->errorMsg,$error_msg);
						return false;
						}
					break;
				case '==':
					if($validation_value != $byte){
						array_push($this->errorMsg,$error_msg);
						return false;
						}
					break;
				}
			}
		if($validation_type == "value"){					//�ͥ����å�
			switch($validation_operator){
				case '>':
					if($validation_value < $value){
						array_push($this->errorMsg,$error_msg);
						return false;
						}
					break;
				case '<':
					if($validation_value > $value){
						array_push($this->errorMsg,$error_msg);
						return false;
						}
					break;
				case '>=':
					if($validation_value <= $value){
						array_push($this->errorMsg,$error_msg);
						return false;
						}
					break;
				case '<=':
					if($validation_value >= $value){
						array_push($this->errorMsg,$error_msg);
						return false;
						}
					break;
				case '==':
					if($validation_value == $value){
						array_push($this->errorMsg,$error_msg);
						return false;
						}
					break;
				}
			}

	//CHECKBOX'S CHECKER
		if($validation_type == "chkbox"){					//�����å��ܥå��������å�
			if(is_array($value)){
				return true;
			} else {
				array_push($this->errorMsg,$error_msg);
				return false;
			}
		}
		if($validation_type == "chknum"){					//�����å��ܥå��������å��������å�
			if(is_array($value)){
				$cnt = 0;
				foreach($value as $k => $v){
					$cnt++;
				}
				switch($validation_operator){
					case '>':
						if($cnt > $validation_value){
							array_push($this->errorMsg,$error_msg);
							return false;
						}
					break;
					case '<':
						if($cnt < $validation_value){
							array_push($this->errorMsg,$error_msg);
							return false;
						}
					break;
					case '>=':
						if($cnt >= $validation_value){
							array_push($this->errorMsg,$error_msg);
							return false;
						}
					break;
					case '<=':
						if($cnt <= $validation_value){
							array_push($this->errorMsg,$error_msg);
							return false;
						}
					break;
					case '==':
						if($cnt == $validation_value){
							array_push($this->errorMsg,$error_msg);
							return false;
						}
					break;
				}
			}
		}

		return true;
		}
	}
?>