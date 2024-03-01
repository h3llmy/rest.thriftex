<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use LasseRafn\Initials\Initials;
class Welcome extends CI_Controller {

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
	 * @see https://codeigniter.com/userguide3/general/urls.html
	 */
	public function index()
	{
		$this->load->model('Payment_model','payment');
		$data_payment = array(
			'legit_id'      => 1,
			'payment_type'  => 'free',
			'payment_status'=> 'lunas',
			'payment_total' => 0,
			'created_at'    => date('Y-m-d H:i:s')
		);
		$this->payment->insert($data_payment);
		$this->load->view('welcome_message');
	}
}
