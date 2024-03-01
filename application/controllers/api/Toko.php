<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use SebastianBergmann\Environment\Console;

class Toko extends RestController {

    protected $user_detail;
    function __construct()
    {
        parent::__construct();
        $this->load->library('Authorization_Token');
        $this->load->helper(array('user_helper'));
        $this->load->model('toko_model','toko');
        $this->load->model('Validator_model','validator');
    }
    public function register_post()
	{   
        try {
            $nama_toko = $this->input->post('nama_toko');
            $alamat_toko = $this->input->post('alamat');
            $id_user = $this->input->post('id_user');

            $data = array(
                'user_id'  => $id_user,
                'nama_toko'      => $nama_toko,
                'alamat'  => $alamat_toko,
                'toko_code' => intCodeRandom(4).$id_user,
                'status'    => 'publish',
                'created_at' => date('Y-m-d H:i:s')
            );
            
            $register = $this->toko->insert($data);
            if($register){
                $get_user_code = $this->toko->get_by(array('id' => $register),'','',true,array('nama_toko','alamat','toko_code'));
                $this->response([
                    'status' => true,
                    'message'   => 'Register Berhasil',
                    'data'  => [
                        'nama_toko' => $get_user_code->nama_toko,
                        'alamat'    => $get_user_code->alamat,
                        'toko_code' => $get_user_code->toko_code,
                    ]
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
    public function registerupdate_put(){
        try {
            $nama_toko = $this->put('nama_toko');
            $alamat_toko = $this->put('alamat');
            $id = $this->put('toko_id');

            $data = array(
                'nama_toko'      => $nama_toko,
                'alamat'  => $alamat_toko,
            );
            
            $register = $this->toko->update($data,array('id'=>$id));
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
    public function toko_delete(){
        $id = $this->delete('id');
        // var_dump($this->delete('id'));s
        if($id === null){
            $this->response([
                'status' => false,
                'message' => 'provide an id!'.$id
            ],400);
        }else{
            if($this->toko->delete($id)){
                $this->response([
                    'status' => true,
                    'id'  => $id,
                    'message' => 'Berhasil terhapus'
                ],200);
            }else{
                $this->response([
                    'status' => false,
                    'message' => 'id not found'
                ],404);
            }
        }
    }

    public function checkcode_get(){
        $code = $this->get('toko_code');
        $get_user_code = $this->toko->get_by(array('toko_code' => $code),'','',true,array('nama_toko','alamat','toko_code'));
        if($get_user_code){
            $this->response([
                'status' => true,
                'data'  => [$get_user_code]
            ],201);
        }else{
            $this->response([
                'status' => false,
                'message' => 'Qr Code tidak ditemukan'
            ],404);
        }
    }
    public function tokolist_get(){
        $this->authorization_token->authtoken();
        $headers = $this->input->request_headers();
        $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
        if($decodedToken['status'] == true && $decodedToken['data']->role == 'admin'){
            $param = $this->get('datatable');
            // var_dump($param);
            // die;
            $query = $param['sSearch'];
            $start = $param['iDisplayStart'];
            $length = $param['iDisplayLength'];
            $keySearch = 'nama_toko';

            $role = $this->get('role');
            $result['sEcho'] = intval($param['sEcho']);
            $result['iTotalRecords'] = $this->toko->count();
            $result['iTotalDisplayRecords'] = $this->toko->count_filter($query);
            if ($length == -1) $length = $result['iTotalDisplayRecords'];
            $data = $this->toko->list($start, $length, $query, $keySearch,$role);
            foreach ($data as $key) {
                $key->data_nama = '<label for="" class="font-16">'.$key->nama_toko.'</label><p class="font-400 font-12 p-0 m-0">'.$key->email.'</p>';
                $key->qrcode = $key->toko_code;
            }
            $result['aaData'] = $data;
            $this->response($result);
        }else{
            $this->response([
                'status'    => false,
            ]);
        }
    }


    public function TokoListPublic_get(){
        $this->authorization_token->authtoken();
        $data['list_toko'] = $this->toko->listToko();
        $this->response($data);
    }


}