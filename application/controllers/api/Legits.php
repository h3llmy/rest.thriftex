<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use SebastianBergmann\Environment\Console;
use LasseRafn\Initials\Initials;

class Legits extends RestController {

    protected $user_detail;
    function __construct()
    {
        parent::__construct();
        $this->load->library('Authorization_Token');
        $this->load->helper(array('user_helper'));
        $this->load->model('User_model','user');
        $this->load->model('LegitImage_model','image');
        $this->load->model('Legit_model','legit');
        $this->load->model('LegitDetail_model','legit_detail');
        $this->load->model('Payment_model','payment');
        $this->load->model('Validator_model','validator');
        $this->load->model('Brand_model','brand');
        $this->load->library('Smtp');
        $this->load->library('S3');
        date_default_timezone_set('Asia/Makassar');
    }
    function caseCode($user_id,$brand_id){
        $data_user = $this->user->get_by(array('id' => $user_id),'','',true,array('nama','user_code'));
        $no_prodile = $data_user->user_code ;
        $jumlah_produk_check = $this->legit->count(array('user_id' => $user_id));
        $inisial_profile = (new Initials)->generate($data_user->nama);;
        // $nomor_jenis_barnag = $brand_id;
        // $code = $no_prodile .'-'.$jumlah_produk_check.$inisial_profile.$nomor_jenis_barnag.'X';
        $code = $no_prodile .'-'.$jumlah_produk_check.$inisial_profile.'X';
        return $code;
    }
    public function savelegit_post()
	{   
        $config['upload_path'] = './media/';
        $config['allowed_types'] = 'jpg|jpeg|png|webp|image/jpeg|PNG|JPEG|JPG';
		$config['overwrite']     = FALSE;
		$config['max_size'] = '10000';
		$config['encrypt_name'] = TRUE;
        $this->load->library('upload',$config);
        $this->upload->initialize($config);
        $this->authorization_token->authtoken();
        // $this->authorization_token->authtoken();
        // $headers = $this->input->request_headers();
        // $datauser = $this->authorization_token->validateToken($headers['Authorization']);
        // $this->response($datauser('user_id'));
        // var_dump($datauser);
        try {
            $kategori_id = $this->input->post('kategori');
            $brand = $this->input->post('brand');
            $nama_item = $this->input->post('nama_item');
            $nama_toko = $this->input->post('nama_toko');
            $kondisi_barang = $this->input->post('kondisi_barang');
            $catatan = $this->input->post('catatan');
            $user_id = $this->input->post('user_id');
            $this->form_validation->set_rules('kategori', 'Kategori', 'required');
            $this->form_validation->set_rules('brand', 'Brand', 'required');
            $this->form_validation->set_rules('nama_item', 'Nama Item', 'required');
            $this->form_validation->set_rules('nama_toko', 'Nama Toko', 'required');
            $this->form_validation->set_rules('kondisi_barang', 'Kondisi Barang', 'required');
            $this->form_validation->set_message('required', '{field} tidak boleh kosong!');
            $this->form_validation->set_error_delimiters('', '');
            if(!$this->form_validation->run()) throw new Exception(validation_errors());
            

            $data_foto = array();
            for($i = 0; $i < count(array_filter($_FILES['legitimage']['name']));$i++)
            {
                if(!empty($_FILES['legitimage']['name'][$i])){
    
                    $_FILES['file']['name'] = $_FILES['legitimage']['name'][$i];
                    $_FILES['file']['type'] = $_FILES['legitimage']['type'][$i];
                    $_FILES['file']['tmp_name'] = $_FILES['legitimage']['tmp_name'][$i];
                    $_FILES['file']['error'] = $_FILES['legitimage']['error'][$i];
                    $_FILES['file']['size'] = $_FILES['legitimage']['size'][$i];
                
                    if($this->upload->do_upload('file')){
                        
                        $uploadData = $this->upload->data();
                        $imageUrl = $this->s3->singleUpload($uploadData['full_path']);
                        $data_foto[] = array(
                            'nama_foto' => $imageUrl
                        );
                        unlink($uploadData['full_path']);
                    }else{
                        $error = array('error' => $this->upload->display_errors());
                        $response = array(
                            'status'	=> false,
                            'msg'		=> 'Foto Gagal di Upload!',
                            'eror'  => $error
                        );
                        echo json_encode($response);
                        die;
                    }
                }
            }
            // $data = array(
            //     'user_id'   => $user_id,
            //     'kategori'      => $kategori_id,
            //     'brand'  => $brand_id,
            //     'nama_item'  => $nama_item,
            //     'data_foto' => $data_foto,
            //     'catatan'     => $catatan,
            // );
            // $response = [
            //     'status'    => false,
            //     'message'   => ''
            // ];

            $get_brand_id = $this->brand->get_by(array('brand_name' => $brand),null,null,true,array('id'));
            $case_id = $this->caseCode($user_id,$brand);
            $this->db->trans_begin();
            $data_legit = array(
                'case_code' => $case_id,
                'user_id'   => $user_id,
                'legit_status'  => 'posted',
                'submit_time'   => date('Y-m-d H:i:s'),
                'created_at'    => date('Y-m-d H:i:s')
            );
            $legit_id = $this->legit->insert($data_legit);
            $data_legit_detail = array(
                'legit_id'      => $legit_id,
                'kategori_id'   => $kategori_id,
                'brand_id'      => $get_brand_id->id,
                'nama_brand'    => $brand,
                'nama_item'     => $nama_item,
                'kondisi'       => $kondisi_barang,
                'toko_pembelian' => $nama_toko,
                'catatan'       => $catatan,
                'created_at'    => date('Y-m-d H:i:s')
            );
            $this->legit_detail->insert($data_legit_detail);
            foreach ($data_foto as $key => $value) {
                $data_gambar_legit = array(
                    'legit_id'  =>$legit_id,
                    'file_path' => $value['nama_foto'],
                    'created_at'    => date('Y-m-d H:i:s')
                );
                $this->image->insert($data_gambar_legit);
            }
            $data_payment = array(
                'legit_id'      => $legit_id,
                'payment_type'  => 'free',
                'payment_status'=> 'lunas',
                'payment_total' => '0',
                'created_at'    => date('Y-m-d H:i:s')
            );
            $this->payment->insert($data_payment);
            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                $this->response([
                    'status'    => false,
                    'message'   => 'Terjadi kesalahan'
                ],400);
            }else{
                $this->db->trans_commit();
                // $this->admin_email_notif($case_id);
                $this->response([
                    'status'    => true,
                    'case_id'   => $case_id,
                    'message'   => 'Berhasil'
                ],200);
            }
                // $this->response($data);
            // var_dump($data);
            // $register = $this->user->createUser($data);
            // if($legit_id){
            // }else{
            //     throw new Exception('Register Fail!');
            // }

        } catch (\Throwable $th) {
            $this->response([
                'status' => false,
                'message'   => $th->getMessage(),
                'error_data' => $this->form_validation->error_array()
            ],400);
        }
	}
    public function admin_email_notif($case_id){
        $this->authorization_token->authtoken();
		$subjek = 'Legit Check Baru Masuk #'.$case_id.'';
		$data_pesan = array(
            'case_id'  => $case_id,
        );
        // $this->load->view('email/admin_email_new_legit.php',$data_pesan,false);

		$html = $this->load->view('email/admin_email_new_legit.php',$data_pesan,true);
        $kepada = 'gedesugandi@gmail.com';
        $send = $this->smtp->SendEmail($kepada,$subjek,$html);
	}

    public function data_get(){
        $this->authorization_token->authtoken();
        $headers = $this->input->request_headers();
        $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);

        $user_id = $decodedToken['data']->user_id;
        $dataLegit = $this->legit->getLegitListUser($user_id);
        // var_dump($dataLegit);
        foreach ($dataLegit as $key) {
            // if($key->check_result == 'preview'){
            //     $key->check_result = 'Checking';
            // }else
            if($key->check_result == 'real'){
                $key->check_result = 'Original';
            }
            if($key->check_result == null){
                $key->check_result = 'Waiting';
            }
        }
        $this->response([
            'status' => true,
            'data'  => $dataLegit
        ],200);
        // if($dataLegit){
        // }else{
        //     $this->response([
        //         'status' => false,
        //         'message' => 'User Not Found!'
        //     ],404);
        // }
    }

    public function datadetail_get(){
        $this->authorization_token->authtoken();
        $headers = $this->input->request_headers();
        $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);

        $case_code = $this->get('case_code');
        $user_id = $decodedToken['data']->user_id;
        $dataLegit = $this->legit->getLegitListUserDetail($user_id,$case_code);
        if($dataLegit){
            foreach ($dataLegit as $key) {
                // if($key->check_result == 'preview'){
                //     $key->check_result = 'Checking';
                // }else
                if($key->check_result == 'real'){
                    $key->check_result = 'Original';
                }
                if($key->check_result == null){
                    $key->check_result = 'Waiting';
                }
                $key->image_list = $this->image->get_by(array('legit_id'=>$key->id),'','','',array('file_path'));
                $key->authentic_comment = $this->validator->validator_data_checker($key->id);
            }
            $this->response([
                'status' => true,
                'data'  => $dataLegit,
            ],200);
        }else{
            $this->response([
                'status' => false,
                'message' => 'Case Code Not Found!'
            ],404);
        }
    }

    public function validatordo_get(){
        $this->authorization_token->authtoken();
        $headers = $this->input->request_headers();
        $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
        $tipe = $this->get('tipe');
        // var_dump($decodedToken['data']->validator_kategori_id);
        $nama_brand = '';
        $get_brand_name = $this->brand->get_by(array('id' => $decodedToken['data']->validator_brand_id),null,null,true,array('id','brand_name'));
        if(!empty($get_brand_name)){
            $nama_brand = $get_brand_name->brand_name;
        }
        $dataLegit = $this->legit->getLegitListByStatus($decodedToken['data']->validator_brand_id,$tipe,$nama_brand);
        foreach ($dataLegit as $key) {
            // if($key->check_result == 'preview'){
            //     $key->check_result = 'Checking';
            // }else
            if($key->check_result == 'real'){
                $key->check_result = 'Original';
            }
            if($key->check_result == null){
                $key->check_result = 'Waiting';
            }
        }
        $this->response([
            'status' => true,
            'data'  => $dataLegit,
        ],200);
    }

    public function validdatadetail_get(){
        $this->authorization_token->authtoken();

        $case_code = $this->get('case_code');
        $dataLegit = $this->legit->getValidateDetail($case_code);
        if($dataLegit){
            foreach ($dataLegit as $key) {
                // if($key->check_result == 'preview'){
                //     $key->check_result = 'Checking';
                // }else
                if($key->check_result == 'real'){
                    $key->check_result = 'Original';
                }
                if($key->check_result == null){
                    $key->check_result = 'Waiting';
                }
                $key->image_list = $this->image->get_by(array('legit_id'=>$key->id),'','','',array('file_path'));
                if(!empty($key->validator_user_id)){
                    $key->authentic_comment = $this->validator->validator_data_single($key->id);
                }else{
                    $key->authentic_comment = [];
                }
            }
            $this->response([
                'status' => true,
                'data'  => $dataLegit,
            ],200);
        }else{
            $this->response([
                'status' => false,
                'message' => 'Case Code Not Found!'
            ]);
        }
    }

    public function validation_post() {
        $this->authorization_token->authtoken();
    
        // Validate input data
        $this->form_validation->set_rules('check_result', 'Check Result', 'required');
        $this->form_validation->set_rules('processing_status', 'Processing Status', 'required');
        $this->form_validation->set_rules('check_note', 'Check Note', 'required');
        $this->form_validation->set_rules('legit_id', 'Legit ID', 'required');
        $this->form_validation->set_rules('validator_user_id', 'Validator User ID', 'required');
        $this->form_validation->set_message('required', '{field} tidak boleh kosong!');
        // Check if validation rules are not met
        if (!$this->form_validation->run()) {
            $this->response([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $this->form_validation->error_array()
            ], 400);
        }
    
        $check_result = $this->input->post('check_result');
        $processing_mode = $this->input->post('processing_status');
        $check_note = $this->input->post('check_note');
        $get_legit_code = $this->legit->get_by(array('id' => $this->input->post('legit_id')),null,null,true,array('case_code','user_id'));
        $get_email_user = $this->user->get_by(array('id' => $get_legit_code->user_id),null,null,true,array('email'));
        $data = array(
            'legit_id'  =>$this->input->post('legit_id'),
            'check_result'=> $check_result,
            'processing_status' => $processing_mode,
            'check_note'  => $check_note,
            'validator_user_id' => $this->input->post('validator_user_id'),
            'final_time_check' =>date('Y-m-d H:i:s')
        );
        $check_hasil_sebelumnya = $this->validator->get_by(array('legit_id' => $this->input->post('legit_id')),'','','',);
        if(count($check_hasil_sebelumnya) > 0){
            $register = $this->validator->update($data,array('legit_id' => $this->input->post('legit_id')));
        }else{
            $register = $this->validator->insert($data);
        }
        
        if($register){
            $this->user_notif_validation_process($get_legit_code->case_code,$get_email_user->email);
            $this->response([
                'status' => true,
                'message'   => 'Hasil validasi disimpan',
                'data'  => []
            ],200);
        }else{
            $this->response([
                'status' => true,
                'message'   => 'Terjadi kesalahan',
            ],403);
        }
    }
    public function user_notif_validation_process($case_id,$get_email_user){
        $this->authorization_token->authtoken();
		$subjek = 'Status Cek Legit Anda #'.$case_id.' - thriftex.id';
        // $case_id = '2242-20GSX';
        $data_legit_detail = $this->legit->legit_detail_by_code($case_id);
		$data_pesan = array(
            'case_id'  => $case_id,
            'data_legit_detail' => $data_legit_detail
        );
        // $this->load->view('email/user_notif_validation.php',$data_pesan,false);
		$html = $this->load->view('email/user_notif_validation.php',$data_pesan,true);
        $kepada = $get_email_user;
        $send = $this->smtp->SendEmail($kepada,$subjek,$html);
	}

    public function summaryadmin_get(){
        $this->authorization_token->authtoken();
        $data = array(
            'total_user'  => $this->user->count(array('role' => 'user')),
            'total_validator'=> $this->user->count(array('role' => 'validator')),
            'total_checked'  => $this->validator->count(NULL, array("check_result" => 'processing')),
            'total_progress'  => $this->validator->count(array("check_result" => 'processing')),
        );
        $this->response([
            'status' => true,
            'data'  => $data
        ],200);
    }

    public function totallegit_get(){
        // $this->authorization_token->authtoken();

        // SELECT COUNT(id) as total FROM `tbl_validator` WHERE check_result != 'processing';
        $total = $this->validator->count("check_result != 'processing'");
        $this->response([
            'status' => true,
            'total'  => $total
        ],200);
    }

    public function legitpublish_get(){
        // $this->authorization_token->authtoken();

        // $headers = $this->input->request_headers();
        // $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);

        $dataLegit = $this->legit->getLegitListPublish();
        if($dataLegit){
            foreach ($dataLegit as $key) {
                // if($key->check_result == 'preview'){
                //     $key->check_result = 'Checking';
                // }else
                if($key->check_result == 'real'){
                    $key->check_result = 'Original';
                }
                if($key->check_result == null){
                    $key->check_result = 'Waiting';
                }
            }
            $this->response([
                'status' => true,
                'data'  => $dataLegit
            ],200);
        }else{
            $this->response([
                'status' => false,
                'message' => 'User Not Found!'
            ],404);
        }
    }

    public function searchlegit_post(){
        $this->authorization_token->authtoken();
        $headers = $this->input->request_headers();
        // $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
        $query = $this->input->post('query');
        $dataLegit = $this->legit->searchPublishLegit($query);
        // var_dump($dataLegit);
        // die;
        if($dataLegit){
            foreach ($dataLegit as $key) {
                if($key->check_result == 'real'){
                    $key->check_result = 'Original';
                }
                if($key->check_result == null){
                    $key->check_result = 'Waiting';
                }
                $key->image_list = $this->image->get_by(array('legit_id'=>$key->id),null,null,false,array('file_path'));
                
                if($key->check_result == 'processing'){
                    $badge_color = 'bg-blue-light';
                }elseif($key->check_result == 'Checking'){
                    $badge_color ='bg-yellow-dark';
                }elseif($key->check_result == 'Original'){
                    $badge_color ='bg-green-dark';
                }elseif($key->check_result == 'fake'){
                    $badge_color = 'bg-red-dark';
                }else{
                    $badge_color = 'bg-yellow-light';
                }
                $key->badge_color = $badge_color;
            }
            $this->response([
                'status' => true,
                'data'  => $dataLegit
            ],200);
        }else{
            $this->response([
                'status' => false,
                'message' => 'Case Code Not Found!',
                'data'  =>''
            ],200);
        }
    }

    

}