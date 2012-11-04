<?php

class UserModel extends CI_Model {
    
  var $username;
  var $password;
  
  function __construct(){  
    parent::__construct(); 
    
    //Load database class
    $this->load->database();
  }  
  
  //Function which uses the Codeigniter database class to check if 
  //the user details passed in via parameters match a row in the database
  function correctUserDetails($username, $password ) {

      //Returns dataset
      $sql = "SELECT * from user WHERE
              Username = ? AND
              Password = ?";
      $queryResult = $this->db->query($sql, array($username, $password));
      
      //If row count > 0 then return true, user details are a match.
      if ($queryResult->num_rows()) {
          return true;
      }
      else {
          return false;
      }
 
  }
  
  /**
     * 1. Get user details from DB
     * 2. Record failed login attempt using UserID
     *
     * @access public
     * @param string
  */
  
  public function get_user_details($fields, $theTable, $username, $filter = "Username") {
      
      //Check array
      if (is_array($fields)) {
          
          //String to store the contents of $fields
          $sql_fields = '';
          
          //Loop through fields array
          for ($a = 1; $a <= count($fields); $a++) {
              
              //Save each array value to string
              $sql_fields .= $fields[$a -1];
              
              //Add colon space to separate column names
              if ($a != count($fields)) {
                  $sql_fields .= ", ";
              }
              //If last, then only space required.
              else {
                  $sql_fields .= " ";
              }
              
          }
          
      }

      //Query to retrieve user data.
      $sql = "SELECT $sql_fields
              FROM $theTable 
              WHERE
              $filter = ?";
      $queryResult = $this->db->query($sql, array($username));
      
      $userData = array();
      
      //If user account exists, save data to array which is then returned.
      if ($queryResult->num_rows()) {
      foreach ($queryResult->result() as $row) {
          
          //Save object as associative array
          $userData = (array) $row;

      }
      
      return $userData;
      }
      else {
          return false;
      }
      
  }
  
  //Records login attemps to database.
  public function logDetails($userId, $ip, $date) {
      
      //Check if existing row, if not then first time login
      $sql = "SELECT * from account_log WHERE
              User_ID = ?";
      $queryResult = $this->db->query($sql, array($userId));
      
      
      //Check if there is an entry already, if not create
      if (!$queryResult->num_rows()) {
        $sql = "INSERT into account_log
                (User_ID, last_login_ip, last_login)
                VALUES
                ( ?,?,? )";

        $this->db->query( $sql, array( $userId, $ip, rtrim($date) ) );
      }
      //else update current
      else {
          
          $sql = "UPDATE account_log
                 SET
                 last_login_ip = ?,
                 last_login = ?
                 WHERE
                 User_ID = ?";

          $this->db->query($sql, array($ip,  rtrim($date), $userId));
          
      }
      
  }
  
  //Records a failed login attempt.
  //If FALSE is passed in as second parameter then login count is set to 0
  
  public function failedLogAttempt($userID, $success = true) {
      
      if ($success) {
        //Current date/time
        $date = new DateTime('now');
        $date = $date->format('Y-m-d H:i:s');
            
        $sql = "UPDATE account_log
                SET 
                failed_log_attempt = failed_log_attempt + 1,
                failed_log_time = '$date'
                WHERE
                User_ID = ?";
      }
      else {
        $sql = "UPDATE account_log
                SET 
                failed_log_attempt = 0
                WHERE
                User_ID = ?";    
      }

      $this->db->query($sql, array($userID));
      
  }
  
  /**
     * 1. When given a password returns an encrypted password, salted with encryption key
     *
     * @access public
     * @return String
     */

    public function prepare_password($password) {

        return sha1($password .  $this->config->item('encryption_key')  );

    }
  
  /**
     * createUser()
     * 
     * 1. Given an array creates a new user in database.
     *
     * @access public
     * @param string
  */
  
  public function createUser($userDetails) {

      //db parameterised query.
      $query = "INSERT into user
                (Username,
                Password,
                email,
                privileges)
                VALUES
                (?,
                ?,
                ?,
                2)";

      if ( $this->db->query($query, $userDetails) ) {
          return true;
      }
      else {
          return false;
      }
      
  }
} 
?>
