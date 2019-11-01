<?php
// used to get mysql databse connection
class Database{

    // specify your own database credentials
    private $host = "10.35.47.127:3306";
    private $db_name = "k87183_jgf_db";
    private $username = "k87183_jgf_api";
    private $password = "1pkEd9@2";
    public $conn;

    // get the database connection
    public function getConnection(){

        $this->conn = null;

        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>