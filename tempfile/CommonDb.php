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
				$this->ar_like[] = "$intro $field LIKE '%$match' ";
				break;
			case 'after':
				$this->ar_like[] = "$intro $field LIKE  '$match%' ";
				break;
			default:
				$this->ar_like[] = "$intro $field LIKE '%$match%' ";
				break;
		}
		return $this;
	}
	
	
	
	/* 
	 * ORDER BY statement in your query
	 *  @ string $order_by_key order by the field you choose
	 *  @string $order_by_value value of your choice it must be DESC and ASC only
	 *  */
	function order_by($order_by_key, $order_by_value) {
		if($order_by_key != null && $order_by_value != null){
			if(strtolower($order_by_value) == 'desc' || strtolower($order_by_value) == 'asc' ){
				$this->order_by = $order_by_value;
				$this->order_by_key=$order_by_key;
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
	function group_by($group_by){
		$this->group_by = $group_by;
		return $this;
	}
	
	/* 
	 * LIMIT statement on your query
	 *  @param int $limit to tell how many datas will be display
	 *  @param offset int $offset where the limit escapes
	 *  */
	function limit($offset, $limit){
			$this->limit = " LIMIT $offset , $limit";
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
			array_push($colName, $key);
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
		return true;
	}
	
	/**
	 *
	 * @param string $tableName        	
	 * @param object $id        	
	 */
	function delete() {
		global $dbMaster;
		$sql = $this->deleteCompile ();
		$this->result = $dbMaster->query ( $sql );
		if (DB::isError ( $this->result )) {
			print 'SQL : ' . $sql;
			print '<br>';
			print 'ERROR in delete : ' . $this->result->getMessage ();
			exit ();
		} else {
			return true;
		}
	}
	
	function deleteCompile() {
		$sql = 'DELETE ';
		if ($this->ar_tblname != null) {
			
			$sql .= "\nFROM " . $this->ar_tblname;
		}
		if (count ( $this->ar_where ) > 0) {
			foreach ( $this->ar_where as $where ) {
				$sql .= "\n" . $where;
			}
		}
		return $sql;
 	}
	
	/**
	 * @param string $tableName
	 * @param object $object - class object
	 * @param int $id - array  Array('id' => 23) length is 1.
	 * @param string $where - optional
	 */
	function update($object, $id, $where = '') {
		global $dbMaster;
	
		if (is_null($id)) {
			print 'Id is not defined.';
			exit;
		}
	
		$row = get_class_vars(get_class($object));
		$updCol = array();
		$data = array();
	
	
		foreach ($row as $key => $value) {
			array_push($updCol, $key);
			array_push($data, $value);
		}
		$condition = '';
		foreach ($id as $key => $value) {
			$condition = $key .' = '. $value;
		}
		$where = $where ? $condition.' AND '.$where : $condition;
		$sth = $dbMaster->autoPrepare($tableName, $updCol, DB_AUTOQUERY_UPDATE, $where);
	
		if (DB::isError($sth)) {
			die($sth->getMessage() . ' update prepare error. ');
		}
	
		$this->result = $dbMaster->execute($sth, $data);
	
		if (DB::isError($this->result)) {
			die($this->result->getMessage(). ' update query error.');
		}
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
		if (DB::isError($data)) {
			trigger_error('CommonDb Class: ' . $data->getMessage() . "\n\tSQL: " . $sql,  E_USER_ERROR);
		}
		
		while($row=$result->fetchRow(DB_FETCHMODE_ASSOC)){
			$toReturn[] = (object) $row;
		}
		
		
		return $toReturn;
		$this->_reset_value();
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
		}
		
		return $row;
		$this->_reset_value();
	}

	
	
	
	/* 
	 * compile() is a function use to compile all your sql statement and must not be called directly
	 *  return sql statement
	 *  */
	function compile(){
		global $dbSlave;
		$dbSlave->setFetchMode(DB_FETCHMODE_ASSOC);
		
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
				$sql .= "\n".$where;
			}
		}
		
	
		
		/* LIKE */
		
		if(count($this->ar_like) > 0){
			foreach($this->ar_like as $like){
				$sql .= "\n".$like;
			}
		}
		
		/* order by */
		if($this->order_by != null){
			$sql	.= "\n ORDER BY ".$this->order_by_key. '  '.$this->order_by;
		}
		
		/* GROUP BY */
		
		if($this->group_by != null){
			$sql .= "\n GROUP BY ".$this->group_by;
		}
		
		/* LIMIT  */
		if($this->limit != null){
			$sql .= "\n".$this->limit;
		}
		
		
		
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