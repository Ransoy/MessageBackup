<?
require_once 'error_ex.inc';
require_once 'common_db.inc';
require_once 'Owner.inc';
////////////////////////////////////////////////////////////////////////////////////////////////////

class M_Point {
	var $user_id;

	//コンストラクタ
	function M_Point($user_id, $updid = "") {
		$this->user_id = $user_id;
		if ($updid == "") {
			$this->updid = $user_id;
		} else {
			$this->updid = $updid;
		}
	}


	//ポイント取得(male_point)
	function GetPoint() {
		global $ownerCd;
		global $dbMaster;

		$sql  = "";
		$sql  = "SELECT	point ";	//0
		$sql .= "FROM 	male_point ";
		$sql .= "WHERE 	owner_cd = ? ";
		$sql .= "AND 	user_id = ? ";
		$con = array($ownerCd, $this->user_id);
		$sth = $dbMaster->prepare($sql);
		$result = $dbMaster->execute($sth, $con);
		if (DB::isError($result)) {
			//print $sql;
			err_proc($result->getMessage());
		}
		if (!$row = $result->fetchRow()) {
			MyErrorHandler::error("DB取得エラー(male_point)");
			return;
		}
		return $row[0];
	}


	//INSERT(male_point, male_point_log)
	function InsPoint($point, $mode, $siteCd = 1, $terminalType = 1, $deviceType = 1) {
		global $ownerCd;
		global $dbMaster;
			//////////////////////// upd_mode ///////////////////////
			//	新規（管理）	0		C-CHECK決済	5		覗き			13		〆処理			20
			//	新規（通常）	1		Bitcash決済	6		チップ			14		選択して下さい	99
			//	管理変更		2		無料体験	8		紹介			15		携帯クレジット	32
			//	銀行決済		3		チャット	11	 	マイレージ		9		アフィリエイト	33
			//	クレジット決済	4		逆チャット	12		メール			40		Aチェック		34
		$data = array();
		$data['owner_cd']  = $ownerCd;
		$data['user_id']   = $this->user_id;
		$data['point']     = $point;
		$data['point_old'] = 0;
		$data['upd_mode']  = $mode;
		//(male_point)
		iTSinsert($data, "male_point", $this->updid);
		//(male_point_log)
		$sql =  "INSERT INTO ";
		$sql .= " male_point_log ";
		$sql .= "(owner_cd, user_id, point, point_old, cre_ip, cre_id, upd_mode, cre_date) ";
		$sql .= "VALUES ";
		$sql .= " (?, ?, ?, ?, ?, ?, ?, now()) ";
		$con = array($ownerCd, $this->user_id, $point, 0, $_SERVER['REMOTE_ADDR'], $this->updid, $mode);
		$sth = $dbMaster->prepare($sql);
		$result = $dbMaster->execute($sth, $con);
		if (DB::isError($result)) {
			//print $sql;
			err_proc($result->getMessage());
		}

		//extended point log
		//$this->SavePointLogEx($siteCd, $terminalType, $deviceType);
	}


	//UPDATE(male_point, male_point_log)
	function UpdPoint($point, $mode, $siteCd = 1, $terminalType = 1, $deviceType = 1) {
		global $ownerCd;
		global $dbMaster;
			//////////////////////// upd_mode ///////////////////////
			//	新規（管理）	0		C-CHECK決済	5		覗き			13		〆処理			20
			//	新規（通常）	1		Bitcash決済	6		チップ			14		選択して下さい	99
			//	管理変更		2		無料体験	8		紹介			15		携帯クレジット	32
			//	銀行決済		3		チャット	11	 	マイレージ		9		アフィリエイト	33
			//	クレジット決済	4		逆チャット	12		メール			40		Aチェック		34
			//  ラインポイント 100
		//(male_point)
		$sql =  "UPDATE male_point SET ";
		$sql .= "owner_cd=?, user_id=?, point_old = point, point=?, upd_id=?, upd_mode=?, upd_date = now(), upd_ip=? ";
		$sql .= "WHERE user_id = ?";
		$con = array($ownerCd, $this->user_id, $point, $this->updid, $mode, $_SERVER['REMOTE_ADDR'], $this->user_id);
		$sth = $dbMaster->prepare($sql);
		$result = $dbMaster->execute($sth, $con);
		if (DB::isError($result)) {
			//print $sql;
			err_proc($result->getMessage());
		}
		//(male_point_log)
		$sql =  "INSERT INTO ";
		$sql .= " male_point_log ";
		$sql .= "(owner_cd, user_id, point, point_old, cre_date, cre_ip, cre_id, upd_mode) ";
		$sql .= " select owner_cd, user_id, point, point_old, upd_date, upd_ip, upd_id, upd_mode ";
		$sql .= " from male_point where user_id = ? ";
		$sth = $dbMaster->prepare($sql);
		$result = $dbMaster->execute($sth, $this->user_id);
		if (DB::isError($result)) {
			//print $sql;
			err_proc($result->getMessage());
		}

		// skip message temporarily
		if (100 != $mode) return;

		//extended point log
		//$this->SavePointLogEx($siteCd, $terminalType, $deviceType);
	}


	//ADD_UPDATE(point, point_log)
	/**
	 *
	 * @param float $point
	 * @param int $mode
	 * @param int $siteCd Defaults to 1 (Macherie). 2 for Machevara.
	 * @param int $terminalType Defaults to 1 (PC). 2 for MB. 3 for SP.
	 * @param int $deviceType Defaults to 1 (PC). 2 for Android. 3 for iOS. 4 for others.
	 */
	function AddPoint($point, $mode, $siteCd = 1, $terminalType = 1, $deviceType = 1) {
		global $ownerCd;
		global $dbMaster;
			//////////////////////// upd_mode ///////////////////////
			//	新規（管理）	0		C-CHECK決済	5		覗き			13		〆処理			20
			//	新規（通常）	1		Bitcash決済	6		チップ			14		選択して下さい	99
			//	管理変更		2		無料体験	8		紹介			15		携帯クレジット	32
			//	銀行決済		3		チャット	11	 	マイレージ		9		アフィリエイト	33
			//	クレジット決済	4		逆チャット	12		メール			40		Aチェック		34
		//(male_point)
		$sql =  "UPDATE male_point SET ";
		$sql .= "owner_cd=?, user_id=?, point_old=point, point= point + ?, upd_id=?, upd_mode=?, upd_date = now(), upd_ip=? ";
		$sql .= "WHERE user_id = ?";
		$con = array($ownerCd, $this->user_id, $point, $this->updid, $mode, $_SERVER['REMOTE_ADDR'], $this->user_id);
		$sth = $dbMaster->prepare($sql);
		$result = $dbMaster->execute($sth, $con);
		if (DB::isError($result)) {
			//print $sql;
			err_proc($result->getMessage());
			}
		//(male_point_log)
		$sql =  "INSERT INTO ";
		$sql .= " male_point_log ";
		$sql .= "(owner_cd, user_id, point, point_old, cre_date, cre_ip, cre_id, upd_mode) ";
		$sql .= " select owner_cd, user_id, point, point_old, upd_date, upd_ip, upd_id, upd_mode ";
		$sql .= " from male_point where user_id = ?";
		$sth = $dbMaster->prepare($sql);
		$result = $dbMaster->execute($sth, $this->user_id);
		if (DB::isError($result)) {
			//print $sql;
			err_proc($result->getMessage());
		}
			//extended point log
			$this->SavePointLogEx($siteCd, $terminalType, $deviceType);
			
	}

	/**
	 * Inserts an entry to the male_point_log_ex table.
	 * @param int $siteCd
	 * @param int $terminalType
	 * @param int $deviceType
	 */
	function SavePointLogEx($siteCd, $terminalType, $deviceType) {
		global $dbMaster;

		//(male_point_log_ex)
		$sql =  "INSERT INTO ";
		$sql .= " male_point_log_ex ";
		$sql .= "(user_id, point_old, point, use_point, upd_mode, cre_date, cre_ip, cre_id, site_cd, terminal_type, device_type) ";
		$sql .= " select user_id, point_old, point, point-point_old, upd_mode, upd_date, upd_ip, upd_id, $siteCd, $terminalType, $deviceType ";
		$sql .= " from male_point where user_id = ?";
		$sth = $dbMaster->prepare($sql);
		$result = $dbMaster->execute($sth, $this->user_id);
		if (DB::isError($result)) {
			//print $sql;
			err_proc($result->getMessage());
		}
	}
}
?>
