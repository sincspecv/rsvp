<?php
namespace TheFancyRobot\RSVP;
use \PDO;

/*
* Database Object
*/
class DatabaseConnection {
  private $host = DB_HOST;
  private $user = DB_USER;
  private $pass = DB_PASS;
  private $dbname = DB_NAME;
  protected $dbConnect;
  private $stmt = NULL;
  private $result;

/*
 * Creates database connection
 */
  public function __construct() {
    // Set DSN
    $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
    // Set options
    $options = array(
        PDO::ATTR_PERSISTENT        => true,
        PDO::ATTR_ERRMODE           => PDO::ERRMODE_WARNING,
        PDO::ATTR_EMULATE_PREPARES  => false
    );

    // Create a new PDO instanace
    try{
        $this->dbConnect = new PDO($dsn, $this->user, $this->pass, $options);
    }
    // Catch any errors
    catch(PDOException $e) {
        $this->error = $e->getMessage();
    }
  }

  /**
   * Qeury database using PDO prepared query
   * @param  string $query SQL query
   * @return string
   */
  public function preparedQuery($query) {
    //Unset previous stmt
    unset($this->stmt);
    //Set up new prepared statment
    $this->stmt = $this->dbConnect->prepare($query);
  }

  /**
   * Bind parameters in prepared query
   * @param  string $param Parameter to bind
   * @param  string $value Value to bind to parameter
   * @param  string $type  Defines the type of value
   * @return string        
   */
  public function bind($param, $value, $type = null) {
    if (is_null($type)) {
        switch (true) {
            case is_int($value):
                $type = PDO::PARAM_INT;
                break;
            case is_bool($value):
                $type = PDO::PARAM_BOOL;
                break;
            case is_null($value):
                $type = PDO::PARAM_NULL;
                break;
            default:
                $type = PDO::PARAM_STR;
        }
    }
    $this->stmt->bindValue($param, $value, $type);
  }

  /**
   * Execute statement
   */
  public function execute() {
    return $this->stmt->execute();
  }

  /**
   * Get all rows of a table and return as an array
   * @return array 
   */
  public function getAllRows() {
    $this->execute();
    return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Get a single row of a table and return as array
   * @return array
   */
  public function getSingleRow() {
    $this->execute();
    return $this->stmt->fetch(PDO::FETCH_ASSOC);
  }

  /**
   * Return number of rows
   * @param  string $table Database table
   * @return int        
   */
  public function countRows($table) {
    $this->result = $this->dbConnect->query("SELECT * FROM $table");
    return $this->result->rowCount();
  }

  /**
   * Check if table exists
   * @param  string $table Database Table
   * @return boolean       
   */
  public function checkForTable($table) {
    $this->preparedQuery("SHOW TABLES FROM $this->dbname LIKE '$table'");
    $this->result = $this->execute();

    if ($this->result) {
      $rows = $this->stmt->fetch(PDO::FETCH_NUM);
      if ($rows[0]) {
        return TRUE; //Table was found 
      } else {
        return FALSE; //Table was not found
      } 
    }  
  }
}