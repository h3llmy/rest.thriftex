<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Smtp{

	public function SendEmail($kepada,$subjek,$pesan,$buffer=null){
		// $_this =& get_instance();
		// $config = [
		// 	'protocol' => 'smtp',
		// 	'smtp_host' => 'ssl://smtp.googlemail.com',
		// 	'smtp_user' => 'gedesugandi@gmail.com',
		// 	'smtp_pass' => 'idmxuxynxguigynh',
		// 	'smtp_port' => 465,
		// 	'mailtype' => 'html',
		// 	'charset' => 'utf-8',
		// 	'newline' => "\r\n"
		// ];
		// $_this->load->library('email', $config);
		// $_this->email->initialize($config);
		// $_this->email->from('gedesugandi@gmail.com', 'Tude Kebaya');
		// $_this->email->to($kepada);
		
		$_this =& get_instance();
		// $config = [
		// 	'protocol' => 'smtp',
		// 	'smtp_host' => 'ssl://smtp.googlemail.com',
		// 	'smtp_user' => 'kebayatude@gmail.com',
		// 	'smtp_pass' => 'mwqntoowdztazxpi',
		// 	'smtp_port' => 465,
		// 	'mailtype' => 'html',
		// 	'charset' => 'utf-8',
		// 	'newline' => "\r\n"
		// ];
		$config = [
			'protocol' => 'smtp',
			'smtp_host' => 'ssl://smtp.googlemail.com',
			'smtp_user' => 'thriftexofficial@gmail.com',
			'smtp_pass' => 'vtpxagsoquywuizj',
			'smtp_port' => 465,
			'mailtype' => 'html',
			'charset' => 'utf-8',
			'newline' => "\r\n"
		];
		// $config = [ 
		// 	'protocol' => 'smtp',
		// 	'smtp_host' => 'mail.thriftex.id',
		// 	'smtp_user' => 'info@thriftex.id',
		// 	'smtp_pass' => 'Thriftex2023#',
		// 	'smtp_port' => 465,
		// 	'mailtype' => 'html',
		// 	'charset' => 'utf-8',
		// 	'newline' => "\r\n"
		// ];
		$_this->load->library('email', $config);
		$_this->email->initialize($config);
		$_this->email->from('info@thriftex.id', 'thriftex.id');
		$_this->email->to($kepada);


		$_this->email->subject($subjek);
        $_this->email->message($pesan);
		if(!empty($buffer)){
			$_this->email->attach($buffer, 'attachment', 'report.pdf', 'application/pdf');
		}
		if ($_this->email->send()) {
			$response = array(
				'status'	=> true,
				'msg'	=> 'Email berhasil dikirim'
			);
		} else {
			$response = array(
				'status'	=> false,
				'msg'	=> $_this->email->print_debugger()
			);
		}
		return $response;
	}
}
