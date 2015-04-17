<?php 
require_once 'common_proc.inc';
require_once 'common_db.inc';

class CommonDb{
	
	/* variables */
	var $limit					=	null;
	var $where_value		=	null;
	var $order_by			=	null;
	var $order_by_key	=	null;
	var $group_by			=	null;
	var $like					=	null;
	var $or_like				=	null;
	var $and_like			=	null;
	var $delete				=	null;
	var $tableName			= 	'';
	
	/* arrays */
	var $ar_select			=	array();
	var $ar_tblname 		=	array();
	var $ar_where			=	array();
	var $ar_join				=	array();
	var $ar_orwhere		=	array();
	var $ar_andwhere		=	array();
	var $ar_like				=	array();
	var $ar_limit				=	array();
	var $ar_values			=	array();
	
	
	
	function setTableName($tableName){$this->tableName = $tableName;}
	function getTableName(){return $this->tableName;}
	/* 
	 * SELECT statement of your query
	 *   @params string $select
	 *   @$select the field that are selected
	 */
	function select($select){
		if($select != null){
			$this->ar_select = $select;
		}
		return $this;
	}
	/* 
	 * FROM statement , get where will be your statement came from a table
	 *  @params string $table_name the name of the table you want to select
	 *  
	 *  */
	
	function from($table_name){
		if($table_name != null){
			$this->ar_tblname = $table_name;
		}
		return $this;
	}
	
	/* 
	 * join function used to join 1 table to another
	 *  @parameters	$join_table , $value , $type
	 *  @$join_table specify what table must be joined  
	 *  @$condition specify what field are equal
	 *  @type must be left,right,inner
	 *  @left outer and right outer are for experiment 
	 */
	function join($join_table,$condition,$type){
		if($join_table && $condition && $type){
			$type = strtolower($type);
			switch ($type){
				case 'left':
					$this->ar_join[] = "LEFT JOIN $join_table ON $condition";
					break;
				case 'right':
					$this->ar_join[] = "RIGHT JOIN $join_table ON $condition";
					break;
				case 'inner':
					$this->ar_join[] = "INNER JOIN $join_table ON $condition";
					break;
				case 'left outer':
					//for experiment
					break;
				case 'right outer':
					//for experiment
					break;
			}
		}
		return $this;
	}
	
	/* 
	 * WHERE CONDITION statement in your query, 
	 *  @params string $where is field of your condition statement
	 *  @params string $value the equivalent value of your condition statement
	 *  */
	function where($key, $value=null, $type='AND') {	
			$intro = (empty($this->ar_where))?'WHERE' : $type;
			if($value == null){
				$this->ar_where[] = "$intro $key";
			}else{
				
				$keysplit = split(' ',$key);
				$key1 = (count($keysplit) > 1) ? $keysplit[0].' '.$keysplit[1] : $keysplit[0].' = ' ;
				$this->ar_where[]= "$intro $key1 $value";
			}
			return $this;
	}
	
	
	
	/* 
	 * LIKE function in your statement
	 *  remember you must not call where and like function at the same time it will prompt an error
	 *  @param string $field on  what field you want to search of the same value on your $macth
	 *  @param string $match what will be the value you want to match
	 *  @param string $options on what position will the wildcard be place acceptable $options are before,after and both (and both is the default)
	 *  */
	function like($field, $match, $options='both',$type='AND'){
		$options = strtolower($options);
		$intro = (empty($this->ar_where))?'WHERE' : $type;
		switch($options){
			case 'before':
				$this->ar_where[] = "$intro $field LIKE '%$match' ";
				break;
			case 'after':
				$this->ar_where[] = "$intro $field LIKE  '$match%' ";
				break;
			default:
				$this->ar_where[] = "$intro $field LIKE '%$match%' ";
				break;
		}
		return $this;
	}
	
	
	
	/* 
	 * ORDER BY statement in your query
	 *  @ string $order_by_key order by the field you choose
	 *  @string $order_by_value value of your choice it must be DESC and ASC only
	 *  */
	function order_by($orderByKey, $orderByValue) {
		if($orderByKey != null && $orderByValue != null){
			if(strtolower($orderByValue) == 'desc' || strtolower($orderByValue) == 'asc' ){
				$this->order_by = $orderByValue;
				$this->order_by_key=$orderByKey;
			}else{
				return false;
			}
		}
		return $this;
	}
	
	/* 
	 * GROUP BY statement on your query
	 *  @params string $group_by what field you want to group your query
	 *  */
	function group_by($groupBy){
		$this->group_by = $groupBy;
		return $this;
	}
	
	/* 
	 * LIMIT statement on your query
	 *  @param int $limit to tell how many datas will be display
	 *  @param offset int $offset how many datas ,must be skipped
	 *  */
	function limit($offset, $limit = ""){
		$str = ($limit != '') ? "$offset , $limit" : $offset;	
		$this->limit = " LIMIT " . $str;
		return $this;
	}
	
	/**
	 * @param string $tableName
	 * @param object / array $values
	 * @return boolean
	 */
	function insert($values) {
		global $dbMaster;
		$colName = array();
		$data = array();
		$row = $values;
	
		if (is_object($values)) {
			$row = get_class_vars(get_class($object));
		}
	
		foreach ($row as $key => $value) {
			array_push($colName,$key);
			array_push($data, $value);
		}
	
		$sth = $dbMaster->autoPrepare($this->tableName, $colName, DB_AUTOQUERY_INSERT);
		if (DB::isError($sth)) {
			die($sth->getMessage() . ' insert prepare error.');
		}
	
		$this->result = $dbMaster->execute($sth, $data);
		if (DB::isError($this->result)) {
			echo $this->result->getMessage() . ' insert query error.';
			return false;
		}
		$dbMaster->freePrepared($sth, false);
		return true;
	}
	 function delete() {
	 	global $dbMaster;
	 	$sql = $this->deleteCompile();
	 	$this->result = $dbMaster->query($sql);
	 	if (DB::isError($this->result)) {
		 	print 'SQL : '.$sql ;
	 		print '<br>';
	 		print 'ERROR in delete : '.$this->result->getMessage();
	 		exit;
	 	}else{
	 		$this->_reset_value();
	 		return true;
	 	}
	 	
	 }
	 
	/**
	 *	@param string $tableName the table name you want to update specific datas
	 * @param array $values an array form of values you want to update
	 */
	function update($tableName,$values) {
		 global $dbMaster;
		$nameTable= $this->ar_tblname=$tableName;
		$sql=$this->updateCompile($nameTable, $values);
		
		$sth = $dbMaster->prepare($sql);
		$result = $dbMaster->execute($sth);
		if (DB::isError($result)) {
			trigger_error('CommonDb Class: ' . $result->getMessage() . "\n\tSQL: " . $sql,  E_USER_ERROR);
		}
		$dbMaster->freePrepared($sth, false);
		$this->_reset_value();
		return true;
	}
	/* *
	 * use to get the last id of the query 
	 *  */
	function getLastInsert(){
		$this->select('id');
		$this->from($this->getTableName());
		$this->order_by('id','DESC');
		$return=$this->get_row();
		return $return['id'];
	}
	
	
	
	
	/* 
	 * function sqlQuery is a function that will get the sql statement
	 *  @param $sql string full sql statements
	 *  @param $values array values of your placeholders
	 *  return array of objects or object objects are case sensitive
	 *  */
	function sqlQuery($sql,$values=null){
			global $dbSlave;
			if($sql !=null && is_array($values)){
				$sth = $dbSlave->prepare($sql);
				$result = $dbSlave->execute($sth,$values);
				if (DB::isError($result)) {
					trigger_error('CommonDb Class: ' . $result->getMessage() . "\n\tSQL: " . $sql,  E_USER_ERROR);
				}
				
				$arToReturn = array();
				while($row = $result->fetchRow(DB_FETCHMODE_ASSOC)){
					$arToReturn[] = (object) $row;
				}
				$dbSlave->freePrepared($sth, false);
				return $arToReturn;
			}
	}
	
	/* 
	 * get() is a function use if you want to call multiple data in your query
	 * return an array of objects objects are case sensitive
	 *  */
	function get(){
		global $dbSlave;
		$toReturn = array();
		$sql = $this->compile();
		$sth = $dbSlave->prepare($sql);
		$result = $dbSlave->execute($sth);
		//$data = $dbSlave->getAll($sql);
		if (DB::isError($result)) {
			trigger_error('CommonDb Class: ' . $result->getMessage() . "\n\tSQL: " . $sql,  E_USER_ERROR);
		}
		
		while($row=$result->fetchRow(DB_FETCHMODE_ASSOC)){
			$toReturn[] = (object) $row;
		}
		
		$dbSlave->freePrepared($sth, false);
		$this->_reset_value();
		return $toReturn;
	}
	
	/**
	 * getArray() is a function use if you want to call multiple data in your query
	 * @return Unknown result[]  
	 */
	function getArray(){
		global $dbSlave;
		//$dbSlave->setFetchMode(DB_FETCHMODE_ASSOC);
		$sql = $this->compile();
		$sth = $dbSlave->prepare($sql);
		$result = $dbSlave->execute($sth);
	
		if (DB::isError($result)) {
			die('CommonDb Class: ' . $result->getMessage() . "\n\tSQL: " . $sql);
		}
		$dbSlave->freePrepared($sth, false);
		$this->_reset_value();
		return $result;
	}
	
	/** Select query statements.
	 * string $sql 
	 * @return unknown $result[]
	 */
	function query($sql, $dbType = 1) {
		if ($dbType) {
			global $dbSlave;
			$database = $dbSlave;
		}
		else {
			global $dbMaster;
			$database = $dbMaster;
		}
		
		$sth = $database->prepare($sql);
		$result =  $database->execute($sth);
		
		if (DB::isError($result)) {
			die('CommonDB query sql : '.$sql . 'Error : '. $result->getMessage());
			return false;
		}
		$toReturn = array();
		while($row=$result->fetchRow(DB_FETCHMODE_ASSOC)){
			$toReturn[] = (object) $row;
		}
		$this->_reset_value();
		return $toReturn;
	}
	
	function checkQuery(){	
		$sql = $this->compile();
		$this->_reset_value();
		return $sql;
	}
	
	//var $time_start = '';
	var $sqlLog = '';
	function sqlLogging($file, $method, $query = '') {
		$sql = ($query == '') ? $this->compile() : $query;
		$format = "@$file - $method - " . preg_replace('/\s+/', ' ', $sql);
		$this->sqlLog = $format;
		return microtime(true);
	}
	
	function sqlLoggingInsert($file, $method, $data) {
		$sql = "INSERT INTO " . $this->tableName . " (".implode(", ", array_keys($data)).") VALUES (".implode(", ", array_values($data)) . ")";
		return $this->sqlLogging($file, $method, $sql);
	}
	//updateQuery($tableName,$values)
	function sqlLoggingUpdate($file, $method, $data, $table) {
		$sql = $this->updateCompile($table, $data);
		return $this->sqlLogging($file, $method, $sql);
	}
	
	function sqlLoggingDelete($file, $method) {
		$sql = $this->deleteCompile();
		return $this->sqlLogging($file, $method, $sql);
	}
	
	function sqlLoggingTime($timeStart) {
		if ($this->sqlLog != '') {
			$timeEnd = microtime(true);
			$time = $timeEnd - $timeStart;
			$finalLog = (number_format($time, 7, '.', '')) . ' : ' . $this->sqlLog;
			$fp = fopen('/var/www/livechat/htdocs/message/log.txt', 'a');
			fwrite($fp, PHP_EOL . $finalLog);
			fclose($fp);
			
		}
	}
	
	
	/* 
	 * get_row() function is use if you want to call a single data in your query it only produces only 1 result
	 *  */
	function get_row(){
		global $dbSlave;
		$sql = $this->compile();
		
		$sth = $dbSlave->prepare($sql);
		$result = $dbSlave->execute($sth);
		$row = $result->fetchRow(DB_FETCHMODE_ASSOC);
		if (DB::isError($result)) {
			trigger_error('CommonDb Class: ' . $result->getMessage() . "\n\tSQL: " . $sql,  E_USER_ERROR);
			return false;
		}
		$this->_reset_value();
		return $row;
		
		
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	//////////////////////  v2 here ////////////////////////////////////////////
	
	/***************************
	 * wherev2 and get_rowv2 other way to condition your statement using the PDO style
	* @params string $key = your condition
	* @params array $value = array of values that must be the value of each placeholder
	*****************************/
	
	function wherev2($key, $value=null,$type = 'AND') {
		$intro = (empty($this->ar_where))?'WHERE' : $type;
		$this->ar_where[] = "$intro $key";
		if($value != null) {
			foreach($value as $val){
				$this->ar_values[] =$val;
			}
		}
		
		
		return $this;
	}
	
	function get_rowv2(){
		global $dbSlave;
		$sql = $this->compile();
		$value = $this->ar_values;
		$sth = $dbSlave->prepare($sql);
		$result = $dbSlave->execute($sth,$value);
		$row = $result->fetchRow(DB_FETCHMODE_ASSOC);
		if (DB::isError($result)) {
			trigger_error('CommonDb Class: ' . $result->getMessage() . "\n\tSQL: " . $sql,  E_USER_ERROR);
			return false;
		}
		$this->_reset_value();
		return $row;
	}
	
	/* **********************
	 * get the results of your query using pdo style
	 *
	 *
	 ************************/
	function results(){
		global $dbSlave;
		$toReturn = array();
		$value = $this->ar_values;
		$sql = $this->compile();
		$sth = $dbSlave->prepare($sql);
		$result = $dbSlave->execute($sth, $value);
		if (DB::isError($result)) {
			trigger_error('CommonDb Class: ' . $result->getMessage() . "\n\tSQL: " . $sql,  E_USER_ERROR);
		}
	
		while($row=$result->fetchRow(DB_FETCHMODE_ASSOC)){
			$toReturn[] = (object) $row;
		}
	
		$this->_reset_value();
		return $toReturn;
	
	}
	
	function deleteQuery(){
		global $dbSlave;
		$value = $this->ar_values;
		$sql = $this->deleteQueryCompile();
		
		$sth = $dbSlave->prepare($sql);
		$result = $dbSlave->execute($sth,$value);
		if (DB::isError($result)) {
			trigger_error('CommonDb Class: ' . $result->getMessage() . "\n\tSQL: " . $sql,  E_USER_ERROR);
			return false;
		}else{
			return true;
		}
	}
	
	function updateQuery($tableName,$values){
		global $dbMaster;
		$nameTable= $this->ar_tblname=$tableName;
		$sql=$this->updateQueryCompile($nameTable, $values);
		$sth = $dbMaster->prepare($sql);
		$result = $dbMaster->execute($sth,$values);
		if (DB::isError($result)) {
			trigger_error('CommonDb Class: ' . $result->getMessage() . "\n\tSQL: " . $sql,  E_USER_ERROR);
		}
		return true;
	}
	
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////

	
	function deleteCompile(){
		$sql = 'DELETE ';
		if($this->ar_tblname != null){
				
			$sql	.="\nFROM ".$this->ar_tblname;
		}
		if(count($this->ar_where) > 0){
			foreach ($this->ar_where as $where){
				$sql .= "\n".$where;
			}
		}
		
		return $sql;
	}
	
	
	function updateCompile($tableName, $values){
		$sql 	 = 'UPDATE '.$tableName;
		$sql	.="\n SET ";
		if(count($values) > 0){
			$counter =1;
			foreach($values as $key=> $val){
				$values = (!is_int($val)) ? $key. ' = "'. $val.'"' : $key. ' = '. $val ;
				$sql  .= ($counter == 1) ? $values: ' , '.$values ;
				$counter++;
			}
			
			
			
		}
		if(count($this->ar_where) > 0){
			foreach ($this->ar_where as $where){
				$sql .= "\n".$where;
			}
		}
		
		return $sql;
	}
	
	
	
	/* 
	 * compile() is a function use to compile all your sql statement and must not be called directly
	 *  return sql statement
	 *  */
	function compile() {
		global $dbSlave;
		//$dbSlave->setFetchMode(DB_FETCHMODE_ASSOC);
		
		/* SELECT */
		$sql = "SELECT ".$this->ar_select;
		
		/* FROM */
		
		if($this->ar_tblname != null){
			
			$sql	.="\nFROM ".$this->ar_tblname;
		}
		
		/* JOIN */
		if(count($this->ar_join) > 0){
			foreach($this->ar_join as $join){
				$sql	.= "\n ".$join;
			}
		}
		
		/* WHERE */
		
		if(count($this->ar_where) > 0){
			foreach ($this->ar_where as $where){
				
				$sql .= "\n $where ";
			}
		}
		
		/* GROUP BY */
		
		if($this->group_by != null){
			$sql .= "\n GROUP BY ".$this->group_by;
		}
		
		/* order by */
		if($this->order_by != null){
			$sql	.= "\n ORDER BY ".$this->order_by_key. '  '.$this->order_by;
		}
		
		/* LIMIT  */
		if($this->limit != null){
			$sql .= "\n".$this->limit;
		}
		
		return $sql;
		
		
	}
	
	function deleteQueryCompile(){
		$sql = 'DELETE';
		if($this->ar_tblname != null){
		
			$sql	.="\nFROM ".$this->ar_tblname;
		}
		if(count($this->ar_where) > 0){
			foreach ($this->ar_where as $where){
				$sql .= "\n".$where;
			}
		}
		return $sql;
		
		
	}
	
	function updateQueryCompile($tableName, $values){
		$sql 	 = 'UPDATE '.$tableName;
		$sql	.="\n SET ";
		if(count($values) > 0){
			$counter =1;
			foreach($values as $key=> $val){
				$sql  .= ($counter == 1) ? " $key = ? " : " , $key = ? " ;
				$counter++;
			}
			
		}
		if(count($this->ar_where) > 0){
			foreach ($this->ar_where as $where){
				$sql .= "\n".$where;
			}
		}
		$this->_reset_value();
		return $sql;
	}
	
	/* 
	 * function use to reset the values of your vars;
	 *  
	 *  */
	function _reset_value(){
		
		$data	=	array(
				'limit'						=> null,
				'where_value'		=> null,
				'order_by'				=> null,
				'order_by_key'		=> null,
				'group_by'				=> null,
				'ar_select'				=>array(),
				'ar_tblname'			=>array(),
				'ar_where'				=>array(),
				'ar_join'				=>array(),
				'ar_orwhere'			=>array(),
				'ar_andwhere'		=>array(),
				'ar_values'			=>array()
				
				
		);
		$this->_reset_run($data);
	}
	
	/* 
	 * accepts the array of data that will be emptied
	 *  
	 *  */
	function _reset_run($data){
		
		foreach ($data as $key => $default){
			$this->$key= $default;
		}
		
	}
	
	
	
}



?>