<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class register extends MX_Controller
{

    /**
     * Registration Controller
     *
     * Handles the registration process.
     *
     */

    /**
     * Create user registration form.
     *
     * @access public
     */
    public function registration_form() {
        
        $this->load->helper("form");
        
        $this->load->library("session");
        
        $this->load->view("registration_form");
    }
    
    /**
     * Validates user input, checks that username is not in use,
     * encrypts password and then enters new record into
     * database.
     * 
     * Finally redirects user.
     *
     * @access public
     */
    public function register_account() {
        
        //Load requird helpers/librarys
        $this->load->library("form_validation");
        $this->load->library("session");
        
        //Load helpers
        $this->load->helper(array('form', 'url'));
        
        //Load database class
        $this->load->database();
        
        //Load model
        $this->load->model("usermodel");
        
        //Validation rules
        $this->form_validation->set_rules("Username", "Username", "trim|min_length[5]|required");
        $this->form_validation->set_rules("Password", "Password", "trim|min_length[5]|required");
        $this->form_validation->set_rules("Email", "E-mail", "trim|valid_email|required");
        
        if ( $this->form_validation->run() == TRUE ) {
            
            //Get user details from post array.
            $username = $this->input->post("Username");
            $password = $this->input->post("Password");
            $email = $this->input->post("Email");
            
            //Save in array for use in function later.
            $userDetails = array("username" => $username,
                                "password" => $password,
                                "email" => $email);
            
            //Check to see if username is unique
            $query = "SELECT Username from user where Username = ?";
            $queryResult = $this->db->query( $query, array($username) ) ;
            $queryResult =  $queryResult->num_rows();
            
            
            if ($queryResult < 1) {
                 
                //Prepare password
                $password = $this->usermodel->prepare_password($password);
                $userDetails['password'] = $password;

                //Insert data into database
                
                if ($this->usermodel->createUser($userDetails)) {
                    echo "User account created";
                }
                else {
                    echo "There was a problem creating the account. Please try again.";
                }
                

                //Redirect user
            }
           
            
        }
        //Form input invalid
        else {
            $errors = 
            $this->load->view("registration_form");
        }
        
    }
    
}

/* End of file register.php */
/* Location: ./application/modules/login/controllers/login.php */