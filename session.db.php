<?php
/**
 * 重写session存储机制，将session写入数据库
 */
class SysSession
{
  private $link;
  /**
   *
   */
  public function open(){
    echo "open<br/>";
    //链接数据库
    try {
      $this->link = new PDO('mysql:host=localhost;dbname=session','root','root');
      return true;
    } catch (PDOException $e) {
      $e->getMessage();
    }
  }
  public function close(){
    echo "close<br/>";
    $this->link=null;
    return true;
  }
  public function read($sess_id){
    echo "read<br/>";
    $sql = "SELECT sess_content FROM session WHERE sess_id = '$sess_id'";
    $stmt = $this->link->query($sql);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
      return $row['sess_content'];
      // var_dump($row);
    }else {
      return '';
    }
  }
  public function write($sess_id,$sess_content){
    echo "write<br/>";
    $sql = "REPLACE INTO session VALUES('$sess_id','$sess_content',unix_timestamp())";
    $result = $this->link->exec($sql);
    return $result;
  }
  public function destroy($sess_id){
    echo "destroy<br/>";
    $sql ="DELETE FROM session WHERE sess_id = '$sess_id'";
    $result = $this->link->exec($sql);
    return $result;
  }
  public function gc($maxlifetime){
    echo "gc<br/>";
    $sql ="DELETE FROM session WHERE last_write< unix_timestamp()-'$maxlifetime'";
    $result = $this->link->exec($sql);
    return $result;
  }
}
$handler = new SysSession();
session_set_save_handler(
    array($handler, 'open'),
    array($handler, 'close'),
    array($handler, 'read'),
    array($handler, 'write'),
    array($handler, 'destroy'),
    array($handler, 'gc')
    );
