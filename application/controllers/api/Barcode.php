<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use SebastianBergmann\Environment\Console;

class Barcode extends RestController {

    function __construct()
    {
        parent::__construct();
        $this->load->library('Authorization_Token');
        $this->load->model('Barcode_model','barcode');
        $this->load->model('Barcode_profile_model','barcode_profile');
        $this->load->model('Barcode_img_model','barcode_img');
        $this->load->model('Barcode_img_produk_model','barcode_img_produk');
        $this->load->model('Barcode_img_lookbook_model', 'barcode_img_lookbook');
        $this->load->model('User_model','user');
    }

    public function pendingbarcode_get(){
        $this->authorization_token->authtoken();
        $headers = $this->input->request_headers();
        $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
        if($decodedToken['status'] == true && $decodedToken['data']->role == 'admin'){
            $req_status = $this->get('payment_status');
            $result['total'] = $this->barcode->count(array('payment_status' => $req_status,'status' => 'publish'));
            $this->response($result);
        }
    }

    public function qrcodelistnew_get(){
        $this->authorization_token->authtoken();
        $headers = $this->input->request_headers();
        $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
        if($decodedToken['status'] == true && $decodedToken['data']->role == 'admin'){
            // $param = $this->get();
            $param = $this->get('datatable');
            $query = $param['sSearch'];
            $start = $param['iDisplayStart'];
            $length = $param['iDisplayLength'];
            $keySearch = 'nama_brand';

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
                    // $temp_array_search['tbl_register_sertifikat.'.$param['mDataProp_'.$i]] = $param['sSearch_'.$i];
                }
            }
            $result['array_temp'] = $temp_array_search;

            $req_status = $this->get('payment_status');
            $result['sEcho'] = intval($param['sEcho']);
            $result['iTotalRecords'] = $this->barcode->count(array('payment_status' => $req_status,'status' => 'publish'));
            $result['iTotalDisplayRecords'] = $this->barcode->count_filter($query,$req_status,$temp_array_search);
            if ($length == -1) $length = $result['iTotalDisplayRecords'];
            $data = $this->barcode->listRegis($start, $length, $query, $keySearch,$req_status,$temp_array_search);
            // var_dump($data);
            foreach ($data as $key) {
                $kode_qr = $key->user_kode.'-'.$key->ukuran_kode.'-1-'.$key->jumlah.'x';
                $kode_qr_total = $key->user_kode.'-'.$key->ukuran_kode.'-'.$key->jumlah.'-'.$key->jumlah.'x';
                $key->data_qrcode = '<div class="">
                                        <span>Nama Brand : </span><h6>'.$key->nama_brand.'</h6>
                                        <span>Nama Produk : </span><h6>'.$key->nama_produk.'</h6>
                                        <span>Jenis Produk : </span><h6>'.$key->jenis.'</h6>
                                        <span>Ukuran Produk : </span><h6>'.$key->ukuran.'</h6>
                                        <span>Warna : </span><h6>'.$key->warna.'</h6>
                                        <span>Jumlah : </span><h6>'.$key->jumlah.'</h6>
                                        <span>Kode Produk : </span><h6><span>'.$kode_qr.'</span> - <span>'.$kode_qr_total.'</span></h6>
                                    </div>';
                if($key->payment_status == 'pending'){
                    $status_bayar = 'Pending Payment';
                }else{
                    $status_bayar = 'Paid';
                }
                $key->status_bayar = $status_bayar;
                // $key->data_nama = '<h5>#'.$key->sertif_code.'</h5><label for="" class="font-16">'.$key->nama.'<span><small> ('.$key->email.')</small></span></label><p class="font-400 font-12 p-0 m-0">'.$key->email.'</p><p class="m-0"><b>Toko : '.$key->nama_toko.'</b></p><div>Submit time : '.$key->created_at.'</div>';
                // $key->qrcode = $key->toko_code;
            }
            $result['aaData'] = $data;
            $this->response($result);
        }
    }

    public function updatestatus_post(){
        $this->authorization_token->authtoken();
        $data = $this->input->post();
        $data_update = array(
            'payment_status' => $data['payment_status']
        );
        $update = $this->barcode->update($data_update,array('barcode_uuid' => $data['id_barcode']));
        if($update){
            $this->response([
                'status' => true,
                'message'   => 'Berhasil diperbaharui',
            ],200);
        }
    }

    public function listakunqr_get(){
        $this->authorization_token->authtoken();
        $headers = $this->input->request_headers();
        $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
        if($decodedToken['status'] == true && $decodedToken['data']->role == 'admin'){
            // $param = $this->get();
            $param = $this->get('datatable');
            $query = $param['sSearch'];
            $start = $param['iDisplayStart'];
            $length = $param['iDisplayLength'];
            $keySearch = 'nama_brand';

            $result['sEcho'] = intval($param['sEcho']);
            $result['iTotalRecords'] = $this->user->count(array('role' => 'toko'));
            $result['iTotalDisplayRecords'] = $this->user->count_filter($query,'toko');
            if ($length == -1) $length = $result['iTotalDisplayRecords'];
            $data = $this->barcode->list_akun_qr($start, $length, $query, $keySearch);
            foreach ($data as $key) {
                $key->data_nama = '<label for="" class="font-16">'.$key->nama_brand.'</label><p class="font-400 font-12 p-0 m-0">'.$key->email.'</p>';
                // $key->qrcode = $key->toko_code;
            }
            $result['aaData'] = $data;
            $this->response($result);
        }else{
            $this->response([
                'status'    => false,
            ]);
        }
    }

    public function slugg($string){
        $string = utf8_encode($string);
        $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);   
        $string = preg_replace('/[^a-z0-9- ]/i', '', $string);
        $string = str_replace(' ', '-', $string);
        $string = trim($string, '-');
        $string = strtolower($string);
        if (empty($string)) {
            return 'n-a';
        }
        return $string;
    }
    public function registerbrand_post()
	{   
        try {
            
            $nama_brand = $this->input->post('nama_brand');
            $id_user = $this->input->post('id_user');

            $update_user_role = array(
                'role' => 'toko'
            );
            $update_user = $this->user->update($update_user_role,array('id' => $id_user));
            $data_insert = array(
                'id_user'       => $id_user,
                // 'logo'          =>'',
                'nama_brand'    => $nama_brand,
                'url_toko'      => $this->slugg($nama_brand),
                'deskripsi_toko' => ''
            );
            $register = $this->barcode_profile->insert($data_insert);
            
            if($register){
                // $get_user_code = $this->toko->get_by(array('id' => $register),'','',true,array('nama_toko','alamat','toko_code'));
                $this->response([
                    'status' => true,
                    'message'   => 'Register Berhasil',
                    'data'  => []
                    // 'data'  => [
                    //     'nama_toko' => $get_user_code->nama_toko,
                    //     'alamat'    => $get_user_code->alamat,
                    //     'toko_code' => $get_user_code->toko_code,
                    // ]
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

    public function cekurl_get(){
        $cek = $this->input->get();
        $cek_url = $this->barcode_profile->cek_url($cek['url']);
        if(!empty($cek_url)){
            $cek_kode = $this->barcode_img->cek_code($cek['code']);
            if(!empty($cek_kode)){
                $get_img = $this->barcode_img_produk->get_media($cek_kode->id);
                $get_img_lookbook = $this->barcode_img_lookbook->get_media($cek_kode->id);
                $this->response([
                    'status' => true,
                    'message'   => 'Register Berhasil',
                    'data'  => [
                        'profile' => $cek_url,
                        'barcode_info'    => $cek_kode,
                        'barcode_foto'    => $get_img,
                        'barcode_foto_look_book'    => $get_img_lookbook
                    ]
                ],201);
            }else{
                $this->response([
                    'status' => false,
                    'message'   => '404',
                ],201);
            }
        }else{
            $this->response([
                'status' => false,
                'message'   => '404',
            ],404);
        }
    }


}