<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Proba extends CI_Controller {

	/**
	 * Index Page for this controller.
	 */
	public function index($arg)
	{
	  $data['arg'] = $arg;
		$this->load->view('proba/home', $data);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
