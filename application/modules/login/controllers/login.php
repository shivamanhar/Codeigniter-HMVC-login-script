<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class login extends CI_Controller
{

    /**
     * Login Controller
     *
     * Handles user login, creating session variables,
     * recording failed log-ins & redirecting user to
     * members homepage on success.
     *
     * Failed login attempt will redirect user back
     * to login form along with a relevant error
     * message
     */

    /**
     * Display login form on page from which the function is called.
     *
     */
    public function login_form()
    {
        //Load helpers
        $this->load->helper("form");

        //Load Views
        $this->load->view('login_form');
    }



    /**
     * First of all uses the validation library to verify that the user has
     * entered valid data.
     *
     * If valid data has been entered, the details are checked against
     * the database & on success the user is logged in.
     *
     * Failure will record failed login attempt & redirect user.
     *
     */

    public function validate_credentials()
    {
        //Load helpers
        $this->load->helper(array('form', 'url'));

        //Load librarys
        $this->load->library('form_validation');
        $this->load->library('session');


        //Form validation
        $this->form_validation->set_rules("Username", "Username", "required");
        $this->form_validation->set_rules("Password", "Password", "required");


        //IF validation fails, back to form with error
        if ($this->form_validation->run() == FALSE) {

            //Save errors to session
            $errors = validation_errors();
            $this->session->set_flashdata('login_error', $errors);

            //redirect
            redirect('welcome');
        }
        //Valid data, check against database & login if correct.
        else {

            //Load user model
            $this->load->model("login/usermodel");

            //Get post data
            $formUserName = $this->input->post('Username');
            $formPassword = $this->input->post('Password');

            //Log user in.
            $this->log_user_in($formUserName, $formPassword );


        }

    }

    /**
     * 1. Log user details.
     * 2. Record login attempt
     * 3. Reset Failed login attempts to 0.
     * 4. Create session variables.
     *
     * @access public
     * @param string
     * @param string
     */

    public function log_user_in($username, $password)
    {
        //Prepare password for comparison with DB.
        $password = $this->prepare_password($password);
        
        //Get user id
        $fields = array(
            "User_Id"
        );
            
        $userDetails = $this->usermodel->get_user_details($fields, "user",$username);
            
        //get user id from data set.
        $userId = $userDetails['User_Id'];

        //Check if account is locked out
        if ( $this->account_locked($userId) ) {

            //redirect
            $this->session->set_flashdata('login_error', "Too many failed log-ins, please try back in an hour. Alternatively, you can contact our support team.");

            //Back to login form.
            redirect('welcome');

            return false;
        }

        //check details in database against post data.
        if ($this->usermodel->correctUserDetails($username, $password)) {
            //Load user model
            $this->load->model("login/usermodel");

            //Get IP
            $ip = $this->input->ip_address();


            //Current date/time
            $date = new DateTime('now');
            $date = $date->format('Y-m-d H:i:s');

            //Add details to account log table
            $this->usermodel->logDetails($userId, $ip, $date);

            //Reset failed log attempts
            $this->usermodel->failedLogAttempt($userId, FALSE);

            //Set session vars
            $values = array("logged_in" => TRUE,
                "username" => $username,
                "login_time" => $date,
                "ip_address" => $ip);


            $this->session->set_userdata($values);

            //To members home page
            redirect('pages');
        }
        else {

            $this->failedLogin($username);

            //redirect
            $this->session->set_flashdata('login_error', "Sorry, those details are incorrect - please try again");

            //Back to login form.
            redirect('welcome');

        }

    }

    /**
     * 1. Get user details from DB
     * 2. Record failed login attempt using UserID
     *
     * @access public
     * @param string
     */

    public function failedLogin($username)
    {
        $fields = array(
            "User_Id"
        );
        
        //get user details from database, if user name exists
        if ($userDetails = $this->usermodel->get_user_details($fields, "user",$username)) {
            
            //Get user ID
            $userId = $userDetails['User_Id'];
            
            //Record failed login attempt
            $this->usermodel->failedLogAttempt($userId);
        }
    }


    /**
     * 1. Checks if user is logged in.
     * 2. If so return true.
     * 3. If not return false.
     *
     * @access public
     * @return boolean
     */
    public function isLoggedIn()
    {
        $loggedIn = $this->session->userdata('logged_in');

        if (isset($loggedIn) && $loggedIn == TRUE) {
            return true;
        } else {
            $this->session->set_flashdata('login_error', "You must login to view that page");
            //Back to login form.
            redirect('welcome');

            return false;
        }
    }

    /**
     * 1. When given a password returns an encrypted password, salted with encryption key
     *
     * @access public
     * @return String
     */

    public function prepare_password($password) {

        return sha1($password . $config['encryption_key']);

    }

    /**
     * 1. First of all checks if the account has 5 failed login attempts.
     * 2. If so it checks current time, against last login attempt.
     * 3. If difference is > 1 hour then account is not locked. Return False.
     *
     * @access public
     * @return boolean
     */
    public function account_locked($userID)
    {
        //TODO: finish account_locked function.
        //Get user id
        $fields = array(
            "failed_log_attempt",
            "failed_log_time"
        );
        
        //Get failed login attempts from user model.
        $userDetails = $this->usermodel->get_user_details($fields, "account_log",$userID, "User_ID");
        $failedLog = $userDetails["failed_log_attempt"];
        $lastLogin = $userDetails["failed_log_time"];

        //Check if failed log attemps exceeding limit
        if ( $failedLog >= 5 ) {
            //Get current date
            $now = new DateTime("now");
            //$now = $now->format('Y-m-d H:i:s');
            
            $lastLogin = new DateTime("$lastLogin");
            
            $remainder = $now->diff($lastLogin);

            //If difference between two times is not (at least) 1 hour, cannot login. Return TRUE.
            if ($remainder->h == 0) {
                return true;
            }
            else {
                //Reset failed log attempts
                $this->usermodel->failedLogAttempt($userID, FALSE);
                
                //Return false, user can attempt to login.
                return false;
            }
            
        }
        else {
            return false;
        }
    }
}

/* End of file login.php */
/* Location: ./application/modules/login/controllers/login.php */