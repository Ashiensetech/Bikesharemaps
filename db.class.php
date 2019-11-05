<?php

class Database
   {
       private $connection;

   function __construct($dbserver,$dbuser,$dbpassword,$dbname)
      {
      $this->dbserver=$dbserver;
      $this->dbuser=$dbuser;
      $this->dbpassword=$dbpassword;
      $this->dbname=$dbname;
      $this->connection = new mysqli($this->dbserver,$this->dbuser,$this->dbpassword,$this->dbname);
      }

   function connect()
      {
      $this->conn=new mysqli($this->dbserver,$this->dbuser,$this->dbpassword,$this->dbname);
      $this->conn->set_charset("utf8");
      $this->conn->autocommit(FALSE);
      if (!$this->conn OR $this->conn->connect_errno) error(_('DB connection error!'));
      return $this->conn;
      }

   function query($query)
      {
      $result=$this->connection->query($query);
      if (!$result) error(_('DB error').' '.$this->conn->error.' '._('in').': '.$query);
      return $result;
      }

   function insertid()
      {
//      return $this->conn->insert_id;
      return $this->connection->insert_id;
      }

}

try {
    $db = new Database($dbserver, $dbuser, $dbpassword, $dbname);
} catch(Exception $e) {
    error_log($e->getMessage());
    exit('Someting weird happened'); //Should be a message a typical user could understand
}

?>