<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pages extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index($page = 'home')
	{
                $this->load->library('session');
                
                //Variables for view
                $data['css_main'] = $this->config->item("css_main");
                $data['base']       = $this->config->item('base_url');
                
                //Load necessary views & pass data array to them.
		$this->load->view('includes/header', $data);
                $this->load->view('pages/' . $page, $data);
                $this->load->view("includes/footer", $data);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */