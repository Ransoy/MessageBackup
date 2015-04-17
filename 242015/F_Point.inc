<?
require_once 'error_ex.inc';
require_once 'common_db.inc';
require_once 'Owner.inc';
////////////////////////////////////////////////////////////////////////////////////////////////////

class F_Point {
	var $user_id;

	//コンストラクタ
	function F_Point($user_id,$updid=""){
		$this->user_id = $user_id;
		if($updid == ""){
			$this->updid = $user_id;
		} else {
			$this->updid = $updid;
		}
	}


	//ポイント取得(female_point)
	function GetPoint(){
		global $ownerCd;
		global $dbMaster;

		$sql  = "";
		$sql .= "SELECT	 point ";		//0
		$sql .= "FROM 	female_point ";
		$sql .= "WHERE 	owner_cd = ? ";
		$sql .= "AND 	user_id = ? ";
		$con = array($ownerCd,$this->user_id);
		$sth = $dbMaster->prepare($sql);
		$result = $dbMaster->execute($sth, $con);
		if(DB::isError($result)){
			print $sql;
			err_proc($result->getMessage());
			}
		if(!$row = $result->fetchRow()){
			MyErrorHandler::error("DB取得エラー(female_point)");
			return;
			}
			return $row[0];
		}

	//報酬金額取得(female_point)
	function GetMoney(){
		global $ownerCd;
		global $dbMaster;

		$sql  = "";
		$sql .= "SELECT	 money ";		//0
		$sql .= "FROM 	female_point ";
		$sql .= "WHERE 	owner_cd = ? ";
		$sql .= "AND 	user_id = ? ";
		$con = array($ownerCd,$this->user_id);
		$sth = $dbMaster->prepare($sql);
		$result = $dbMaster->execute($sth, $con);
		if(DB::isError($result)){
			print $sql;
			err_proc($result->getMessage());
			}
		if(!$row = $result->fetchRow()){
			MyErrorHandler::error("DB取得エラー(female_point)");
			return;
			}
			return $row[0];
		}


	//INSERT(point,point_log)
	function InsPoint($point,$money,$mode){
		global $ownerCd;
		global $dbMaster;
			//////////////////////// upd_mode ///////////////////////
			//	新規（管理）	0		C-CHECK決済	5		覗き			13		〆処理			20
			//	新規（通常）	1   	Bitcash決済	6   	チップ			14      選択して下さい	99
			//	管理変更		2     	無料体験	8	    紹介			15
			//	銀行決済		3     	チャット	11	 	
			//	クレジット決済	4     	逆チャット	12     	メール			40
		$data = array();
		$data['owner_cd'] 	= $ownerCd;
		$data['user_id'] 	= $this->user_id;
		$data['point'] 	 	= $point;
		$data['point_old'] 	= 0;
		$data['money'] 		= $money;
		$data['money_old'] 	= 0;
		$data['total_point'] = $point;
		$data['upd_mode'] 	= $mode;
		//(female_point)
		iTSinsert($data,"female_point",$this->updid);
		//(female_point_log)
		$sql =  "INSERT INTO ";
		$sql .= " female_point_log ";
		$sql .= "(owner_cd,user_id,point,point_old,money,money_old,cre_date,cre_ip,cre_id,upd_mode) ";
		$sql .= "VALUES ";
		$sql .= " (?,?,?,0,?,0,now(),?,?,?) ";
		$con = array($ownerCd,$this->user_id,$point,$money,$_SERVER['REMOTE_ADDR'],$this->updid,$mode);
		$sth = $dbMaster->prepare($sql);
		$result = $dbMaster->execute($sth, $con);
		if(DB::isError($result)){
			print $sql;
			err_proc($result->getMessage());
			}
		}


	//UPDATE(point,point_log)
	function UpdPoint($point,$money,$mode){
		global $ownerCd;
		global $dbMaster;
			//////////////////////// upd_mode ///////////////////////
			//	新規（管理）	0		C-CHECK決済	5		覗き			13		〆処理			20
			//	新規（通常）	1   	Bitcash決済	6   	チップ			14      選択して下さい	99
			//	管理変更		2     	無料体験	8	    紹介			15
			//	銀行決済		3     	チャット	11	 	
			//	クレジット決済	4     	逆チャット	12     	メール			40
		//(female_point)
		$sql =  "UPDATE female_point SET ";
		$sql .= "owner_cd=?,user_id=?,total_point=total_point+(?-point),point_old = point,point=?,money_old=money,money=?,upd_id=?,upd_mode=?,upd_date = now(),upd_ip=? ";
		$sql .= "WHERE user_id = ?";
		$con = array($ownerCd,$this->user_id,$point,$point,$money,$this->updid,$mode,$_SERVER['REMOTE_ADDR'],$this->user_id);
		$sth = $dbMaster->prepare($sql);
		$result = $dbMaster->execute($sth, $con);
		if(DB::isError($result)){
			print $sql;
			err_proc($result->getMessage());
			}
		//(female_point_log)
		$sql =  "INSERT INTO ";
		$sql .= " female_point_log ";
		$sql .= "(owner_cd,user_id,point,point_old,money,money_old,cre_date,cre_ip,cre_id,upd_mode) ";
		$sql .= " select owner_cd,user_id,point,point_old,money,money_old,upd_date,upd_ip,upd_id,upd_mode ";
		$sql .= " from female_point where user_id = ?";
		$sth = $dbMaster->prepare($sql);
		$result = $dbMaster->execute($sth, $this->user_id);
		if(DB::isError($result)){
			print $sql;
			err_proc($result->getMessage());
			}
		}


	//ADD_UPDATE(point,point_log)
	function AddPoint($point,$money,$mode){
		global $ownerCd;
		global $dbMaster;
			//////////////////////// upd_mode ///////////////////////
			//	新規（管理）	0		C-CHECK決済	5		覗き			13		〆処理			20
			//	新規（通常）	1   	Bitcash決済	6   	チップ			14      選択して下さい	99
			//	管理変更		2     	無料体験	8	    紹介			15
			//	銀行決済		3     	チャット	11	 	
			//	クレジット決済	4     	逆チャット	12     	メール			40
		//(female_point)
		$sql =  "UPDATE female_point SET ";
		$sql .= " owner_cd = ?,";
		$sql .= " user_id = ?,";
		$sql .= " total_point = total_point + ?,";
		$sql .= " point_old = point,";
		$sql .= " point = point + ?,";
		$sql .= " money_old = money,";
		$sql .= " money = money + ?,";
		$sql .= " upd_id = ?,";
		$sql .= " upd_mode = ?,";
		$sql .= " upd_date = now(),";
		$sql .= " upd_ip = ? ";
		$sql .= "WHERE user_id = ?";
		$con = array($ownerCd,$this->user_id,$point,$point,$money,$this->updid,$mode,$_SERVER['REMOTE_ADDR'],$this->user_id);
		$sth = $dbMaster->prepare($sql);
		$result = $dbMaster->execute($sth, $con);
		if(DB::isError($result)){
			print $sql;
			err_proc($result->getMessage());
			}
		//(female_point_log)
		$sql =  "INSERT INTO ";
		$sql .= " female_point_log ";
		$sql .= "(owner_cd,user_id,point,point_old,money,money_old,cre_date,cre_ip,cre_id,upd_mode) ";
		$sql .= " select owner_cd,user_id,point,point_old,money,money_old,upd_date,upd_ip,upd_id,upd_mode ";
		$sql .= " from female_point where user_id = ?";
		$sth = $dbMaster->prepare($sql);
		$result = $dbMaster->execute($sth, $this->user_id);
		if(DB::isError($result)){
			print $sql;
			err_proc($result->getMessage());
			}
		}

}
?>
