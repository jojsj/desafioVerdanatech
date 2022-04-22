<?php

class Database
{

  private  $servidor;
  private  $usuario;
  private  $senha;
  private  $dbname;
  private  $conn;

  function __construct()
  {
    $this->servidor = "localhost";
    $this->usuario = "admin";
    $this->senha = "CAFEBABE?i{^8";
    $this->dbname = "database";
  }

  public function getConnection()
  {
    $this->conn = null;
    try {
      $this->conn = new PDO(
        'mysql:host=' . $this->servidor . '; dbname=' . $this->dbname,
        $this->usuario,
        $this->senha,
        array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4'")
      );
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    } catch (PDOException $e) {
      echo json_encode(["error" => $e->getMessage()]);
    }
    return $this->conn;
  }
}
