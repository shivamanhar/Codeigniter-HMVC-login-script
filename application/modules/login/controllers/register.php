<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class register extends CI_Controller
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
        
        //Validation rules
        $this->form_validation->set_rules("Username", "Username", "trim|min_length[5]|required");
        $this->form_validation->set_rules("Password", "Password", "trim|min_length[5]|required");
        $this->form_validation->set_rules("Email", "E-mail", "trim|valid_email|required");
        
        if ( $this->form_validation->run() == TRUE ) {
            
            //Check to see if username is unique
            
            //Prepare password
            
            //Insert data into database
            
            //Redirect user
            echo "hi";
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