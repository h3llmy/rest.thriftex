<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use SebastianBergmann\Environment\Console;

class Sertif extends RestController {

    protected $user_detail;
    function __construct()
    {
        parent::__construct();
        $this->load->library('Authorization_Token');
        $this->load->library('Smtp');
        $this->load->helper(array('user_helper'));
        $this->load->model('Sertifikat_model','sertifikat');
        $this->load->model('User_model','user');
        $this->load->model('SertifImage_model','sertifikatimg');
        $this->load->model('Validator_model','validator');
    }
    function caseCode($user_id){
        $data_user = $this->user->get_by(array('id' => $user_id),'','',true,array('nama','user_code'));
        $no_prodile = $data_user->user_code ;
        $jumlah_produk_check = $this->sertifikat->count(array('user_id' => $user_id));
        $code = $no_prodile .'-00'.$jumlah_produk_check+1;
        return $code;
    }
    public function index_post()
	{   
        $this->authorization_token->authtoken();
        try {
            $toko_id = $this->input->post('id_toko');
            $id_user = $this->input->post('user_id');
            $data_foto = $this->input->post('data_foto');
            
            if(count($data_foto) > 0){
                $case_id = $this->caseCode($id_user);
                $data_sertif = array(
                    'user_id'  => $id_user,
                    'toko_id'      => $toko_id,
                    'sertif_code'   => $case_id,
                    'status'  => 'new',
                    'created_at' => date('Y-m-d H:i:s')
                );
                $register = $this->sertifikat->insert($data_sertif);
                if($register){
                    foreach ($data_foto as $key => $value) {
                        $data_gambar_legit = array(
                            'register_sertif_id'  =>$register,
                            'file_path' => $value['nama_foto'],
                            'created_at'    => date('Y-m-d H:i:s')
                        );
                        $this->sertifikatimg->insert($data_gambar_legit);
                    }
                    $this->response([
                        'status' => true,
                        'message'   => 'Register Berhasil',
                        'data'  => []
                    ],201);
                }else{
                    $this->response([
                        'status' => false,
                        'message'   => 'Register Gagal',
                        'data'  => []
                    ],400);
                }
            }else{
                $this->response([
                    'status' => false,
                    'message'   => 'Foto Gagal di upload',
                    'data'  => []
                ],400);
            }

        } catch (\Throwable $th) {
            $this->response([
                'status' => false,
                'message'   => $th->getMessage(),
            ],400);
        }
	}

    public function data_get(){
        $this->authorization_token->authtoken();
        $headers = $this->input->request_headers();
        $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);

        $user_id = $decodedToken['data']->user_id;
        $datasertif = $this->sertifikat->getSertifListUser($user_id);
        foreach ($datasertif as $key) {
            // if($key->check_result == 'real'){
            //     $key->check_result = 'Original';
            // }
            // if($key->check_result == null){
            //     $key->check_result = 'Waiting';
            // }
        }
        $this->response([
            'status' => true,
            'data'  => $datasertif
        ],200);
    }

    public function cencel_post(){
        $this->authorization_token->authtoken();
        $sertif_id = $this->input->post('id');
        $delete = $this->sertifikat->delete($sertif_id);
        if($delete){
            $get_image = $this->sertifikatimg->get_by(array('register_sertif_id' => $sertif_id),'','',false,array('file_path'));
            $delete_image = $this->sertifikatimg->delete_by(array('register_sertif_id' => $sertif_id));
            $response = [
                'img_list' => $get_image
            ];
            $this->response([
                'status' => true,
                'message'   => 'Berhasil dihapus',
                'data'  => $response
            ],200);
        }
    }

    public function sertifverif_get(){
        $this->authorization_token->authtoken();
        $headers = $this->input->request_headers();
        $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
        if($decodedToken['status'] == true && $decodedToken['data']->role == 'admin'){
            $param = $this->get('datatable');
            $query = $param['sSearch'];
            $start = $param['iDisplayStart'];
            $length = $param['iDisplayLength'];
            $keySearch = 'nama';

            $temp_array_search = array();
            // $filter_jatuh_tempo='';
            // $filter_date_lunas = false;
            for ($i=0; $i < $param['iColumns']; $i++) { 
                if(!empty($param['sSearch_'.$i])){
                    // if($this->input->get('mDataProp_'.$i) == 'tgl_jatuh_tempo'){
                    //     // $temp_array_search[$this->input->get('mDataProp_'.$i)] = $this->input->get('sSearch_'.$i);
                    //     $filter_jatuh_tempo = $this->input->get('sSearch_'.$i);
                    // }else if($this->input->get('mDataProp_'.$i) == 'status_hutang' && $this->input->get('sSearch_'.$i) == 'lunas'){
                    //     $filter_date_lunas = true;
                    //     $temp_array_search[$this->input->get('mDataProp_'.$i)] = $this->input->get('sSearch_'.$i);
                    // }else{
                    // }
                    $temp_array_search['tbl_register_sertifikat.'.$param['mDataProp_'.$i]] = $param['sSearch_'.$i];
                }
            }
            $result['array_temp'] = $temp_array_search;

            $req_status = $this->get('status');
            $result['sEcho'] = intval($param['sEcho']);
            $result['iTotalRecords'] = $this->sertifikat->count(array('status' => $req_status));
            $result['iTotalDisplayRecords'] = $this->sertifikat->count_filter($query,$req_status,$temp_array_search);
            if ($length == -1) $length = $result['iTotalDisplayRecords'];
            $data = $this->sertifikat->listRegis($start, $length, $query, $keySearch,$req_status,$temp_array_search);
            foreach ($data as $key) {
                $key->data_bukti = $this->sertifikatimg->get_by(array('register_sertif_id' => $key->id),'','',false,array('file_path'));
                $key->data_nama = '<h5>#'.$key->sertif_code.'</h5><label for="" class="font-16">'.$key->nama.'<span><small> ('.$key->email.')</small></span></label><p class="font-400 font-12 p-0 m-0">'.$key->email.'</p><p class="m-0"><b>Toko : '.$key->nama_toko.'</b></p><div>Submit time : '.$key->created_at.'</div>';
                $key->qrcode = $key->toko_code;
            }
            $result['aaData'] = $data;
            $this->response($result);
        }
    }
    public function pendingsertif_get(){
        $this->authorization_token->authtoken();
        $headers = $this->input->request_headers();
        $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
        if($decodedToken['status'] == true && $decodedToken['data']->role == 'admin'){
            $req_status = $this->get('status');
            $result['total'] = $this->sertifikat->count(array('status' => $req_status));
            $this->response($result);
        }
    }
    public function sertifupdate_put(){
        $this->authorization_token->authtoken();
        try {
            $sertif_status = $this->put('sertif_status');
            $catatan = $this->put('catatan');
            $id_sertif = $this->put('id_sertif');

            $data = array(
                'note'      => $catatan,
                'status'  => $sertif_status,
                'updated_at' => date('Y-m-d H:i:s')
            );

            $register = $this->sertifikat->update($data,array('id'=>$id_sertif));
            if($sertif_status == 'rejected' && !empty($catatan) || $catatan != null){
                $subjek = 'Registrasi Sertifikat Produk Anda Ditolak';
                $catatan_email = $catatan;
                $this->sendemailSertif($subjek,$catatan_email,'tolak');
            }else if($sertif_status == 'accepted'){
                $subjek = 'Registrasi Sertifikat Produk Anda Diterima';
                $catatan_email = $catatan;
                $this->sendemailSertif($subjek,$catatan_email,'terima');
            }
            if($register){
                $this->response([
                    'status' => true,
                    'message'   => 'Update Berhasil',
                    'data'  => []
                ],201);
            }else{
                $this->response([
                    'status' => false,
                    'message'   => 'Register Gagal',
                    'data'  => []
                ],400);
            }

        } catch (\Throwable $th) {
            $this->response([
                'status' => false,
                'message'   => $th->getMessage(),
            ],400);
        }
    }

    public function sendemailSertif($subjek,$catatan,$tipe){
        $data_pesan = array(
            'isi_pesan'  => $catatan,
        );
        $html = $this->load->view('email/template_email_sertifikat_'.$tipe.'.php',$data_pesan,true);
        // $kepada = 'gedesugandi@gmail.com,thriftexcs@gmail.com';
        $kepada = 'gedesugandi@gmail.com';
        $this->smtp->SendEmail($kepada,$subjek,$html);
	}


}