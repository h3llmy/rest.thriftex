<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use SebastianBergmann\Environment\Console;

class Email extends RestController {

    function __construct()
    {
        parent::__construct();
        $this->load->library('Smtp');
    }

    public function sendemailkontak_post() {
        try {
            $this->form_validation->set_rules('nama', 'Name', 'required');
            $this->form_validation->set_rules('email', 'Email', 'required');
            $this->form_validation->set_rules('pesan', 'Pesan', 'required');
            $this->form_validation->set_message('required', '{field} tidak boleh kosong!');
            $this->form_validation->set_error_delimiters('', '');
            if(!$this->form_validation->run()) throw new Exception(validation_errors());

            $subjek = 'contact us';
            $data_pesan = array(
                'nama' => $this->input->post('nama'),
                'email' => $this->input->post('email'),
                'no_tlp' => $this->input->post('no_tlp'),
                'pesan' => $this->input->post('pesan'),
            );
            $html = $this->load->view('email/kontak_email.php',$data_pesan,true);
            $kepada = 'Thriftexofficial@gmail.com';
            $send = $this->smtp->SendEmail($kepada,$subjek,$html);
            $this->response($send);
        } catch (\Throwable $th) {
            $this->response([
                'status' => false,
                'message'   => $th->getMessage(),
                'error_data' => $this->form_validation->error_array()
            ], 400);
        }
    }

    public function sendemail_post(){
        $this->load->library('Authorization_Token');
        $this->authorization_token->authtoken();
		$subjek = $this->input->post('subjek');
		$data_pesan = array(
            'nama'  => $this->input->post('nama'),
            'email' => $this->input->post('email'),
            'isi_pesan'  => $this->input->post('isi_pesan'),
        );
		$html = $this->load->view('email/template_email.php',$data_pesan,true);
        $kepada = 'gedesugandi@gmail.com,thriftexcs@gmail.com';
        $send = $this->smtp->SendEmail($kepada,$subjek,$html);
        $this->response($send);
	}

    public function sendemailSertif_post(){
        $this->load->library('Authorization_Token');
        $this->authorization_token->authtoken();
		$subjek = $this->input->post('subjek');
		$data_pesan = array(
            'isi_pesan'  => $this->input->post('isi_pesan'),
        );
        $this->load->view('email/template_email_sertifikat_terima.php',$data_pesan,false);

		// $html = $this->load->view('email/template_email.php',$data_pesan,true);
        // $kepada = 'gedesugandi@gmail.com,thriftexcs@gmail.com';
        // $send = $this->smtp->SendEmail($kepada,$subjek,$html);
        // $this->response($send);
	}

}