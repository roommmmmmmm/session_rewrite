<?php
/**
 * 初始化，保证在执行操作前，一定会被执行
 */
function sessionBegin(){
    echo "<br />Begin<br />";
    //链接数据库
}
/**
 * 结束操作时的函数
 */
function sessionEnd(){
  echo "<br />End<br />";
  return true;
}
/**
 * 读操作用的函数
 *@param string $sess_id 当前会话的ID
 *@return string 当前session数据区内容
 */
function sessionRead($sess_id){
  echo "<br />Read<br />";
  //链接数据库
  $mysqli=new mysqli('127.0.0.1','root','root','session');
  if ($mysqli->connect_error) {
    die('Connect Error:'.$mysqli->connect_error);
  }
  $mysqli->set_charset('utf8');
  //执行读select操作
  $sql = "SELECT sess_content FROM session WHERE sess_id = '$sess_id'";
  $result = $mysqli->query($sql);
  $row = $result->fetch_assoc();
  if ($row) {
    return $row['sess_content'];
  }else {
    return '';
  }
}
/**
 * 写操作用的函数
 *@param string $sess_id 当前会话的ID
 *@param string $sess_content 已经被序列化的session数据
 *@return bool 写入结果是否成功
 */
function sessionWrite($sess_id,$sess_content){
  echo "<br />Write<br />";
  //链接数据库
  $mysqli=new mysqli('127.0.0.1','root','root','session');
  if ($mysqli->connect_error) {
    die('Connect Error:'.$mysqli->connect_error);
  }
  $mysqli->set_charset('utf8');
  //写入数据
  //利用sess_id判断是否已经存在该记录，存在则replace，不存在则insert
  // replace into : 如果主键存在，则替换，否则插入，语法与insert into 一样
  $sql = "REPLACE INTO session VALUES('$sess_id','$sess_content',unix_timestamp())";
  $result = $mysqli->query($sql);
  return $result;
}
/**
 * 在用户强制执行了session_destroy()的时候，删除对应的session数据
 *@param string $sess_id 当前会话的ID
 *@return boll 删除的结果
 */
function sessionDestroy($sess_id){
  echo "<br />Destroy<br />";
  //链接数据库
  $mysqli=new mysqli('127.0.0.1','root','root','session');
  if ($mysqli->connect_error) {
    die('Connect Error:'.$mysqli->connect_error);
  }
  $mysqli->set_charset('utf8');
  //执行删除操作
  $sql ="DELETE FROM session WHERE sess_id = '$sess_id'";
  $result = $mysqli->query($sql);
  return $result;
}
/**
 * 回收操作用到的函数,有几率执行
  *@param int $maxlifetime 最大有效期
  *@return boll 删除的结果
 */
function sessionGC($maxlifetime){
  echo "<br />GC<br />";
  //链接数据库
  $mysqli=new mysqli('127.0.0.1','root','root','session');
  if ($mysqli->connect_error) {
    die('Connect Error:'.$mysqli->connect_error);
  }
  $mysqli->set_charset('utf8');
 //执行操作
 $sql ="DELETE FROM session WHERE last_write< unix_timestamp()-'$maxlifetime'";
 $result = $mysqli->query($sql);
 return $result;
}

session_set_save_handler('sessionBegin','sessionEnd','sessionRead','sessionWrite','sessionDestroy','sessionGC');
?>
<!-- CREATE TABLE session(
  sess_id VARCHAR(40) NOT NULL PRIMARY KEY,
  sess_content TEXT,
  last_write INT NOT NULL
) ENGINE=MYISAM CHARSET=utf8; -->
