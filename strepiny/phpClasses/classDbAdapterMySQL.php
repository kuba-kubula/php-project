<?php
class DbAdapterMySQL {

  private $connect;
  
  public function __construct($dbServerHost, $dbUser, $dbPass, $dbSchema, $dbPrefix = '') {
    $this->connect = mysql_pconnect($dbServerHost, $dbUser, $dbPass) or die('Neni pristup na DB server.');
    $this->prefix = $dbPrefix;
    mysql_select_db($dbSchema, $this->connect) or die('Databaze nenalezena');
    mysql_query('SET NAMES utf8', $this->connect);
  }

  public function fetchNum($qry) {
    $qry_result = $this->execute($qry);
    $resultAry = array();
    for($i = 0; $row = mysql_fetch_array($qry_result, MYSQL_NUM); $i++) {
      $resultAry[$i] = $row;
    }
    //array_walk($resultAry, 'convert'); 
    return $resultAry;
  }  
  
  public function fetchAssoc($qry) {
    $qry_result = $this->execute($qry);
    $resultAry = array();
    for($i = 0; $row = mysql_fetch_array($qry_result); $i++) {
      $resultAry[$i] = $row;
    }
    //array_walk($resultAry, 'convert'); 
    return $resultAry;
  }
  
  public function fetch1Assoc($qry) {
    $qry_result = $this->execute($qry);
    $row = mysql_fetch_array($qry_result);
    //array_walk($result, 'convert');
    return $row;
  }
  
  public function update($db_table_name, $column_value_ary, $conditions) {   
    $columnValueStr = "";
    array_walk($column_value_ary, 'decodeStr');    
    foreach($column_value_ary as $rColumn=>$rValue) {
      $columnValueStr .= " $rColumn = '$rValue',";
    }   
    $columnValueStr = substr($columnValueStr, 0, -1);
    $qry =  "UPDATE ".$this->prefix."$db_table_name SET $columnValueStr $conditions";
    $this->execute($qry,false);
  }
  
  public function insert($db_table_name,$column_value_ary) {
    array_walk($column_value_ary, 'decodeStr');   
    $qry =  "INSERT INTO ".$this->prefix."$db_table_name (" 
         . implode(", ", array_keys($column_value_ary)) 
         . ") VALUES ('" 
         . implode("', '", $column_value_ary) . "')";
     //echo "<p> ". $qry . "</p>";
    $this->execute($qry,false);
  }  
  
  
  public function delete($db_table_name,$condition) {
    $qry =  "DELETE FROM ".$this->prefix."$db_table_name $condition";
    $this->execute($qry);   
  }
   
  public function execute($qry, $debug = false){
    if($debug) fileDebug($qry,'./_debug/!qry.sql');  
    $qry_result = mysql_query($qry, $this->connect);
    return $qry_result;
  }
  
  public function close(){
    mysql_close($this->connect);
  }
}

function decodeStr(&$item){
  if(is_array($item)){ 
    array_walk($item, 'decodeStr');  
  }else{
    $item = str_replace('&quot;', '"', $item);
    $item = str_replace("'", "''", $item);
    $item = str_replace("&#039;", "''", $item);
  }
}

function convert(&$item,$key){
  if(is_array($item)){ 
    array_walk($item, 'convert');  
  }else{
    $item = str_replace('"', '&quot;', $item);
    //$item = iconv("ISO-8859-2", "UTF-8", $item);
  }
}
