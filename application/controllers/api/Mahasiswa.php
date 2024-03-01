<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Mahasiswa extends RestController {

    function __construct()
    {
        parent::__construct();
        $this->load->library('Authorization_Token');
        $this->load->model('Mahasiswa_model','mahasiswa');
    }
    // public function authtoken(){
    //     $headers = $this->input->request_headers();
    //     $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
    //     if($decodedToken['status'] == false){
    //         $this->response($decodedToken);
    //         die;
    //     }
    // }
    public function register_post()
	{   
		$token_data['user_id'] = 121;
		$token_data['fullname'] = 'code'; 
		$token_data['email'] = 'code@gmail.com';

		$tokenData = $this->authorization_token->generateToken($token_data);

		$final = array();
		$final['token'] = $tokenData;
		$final['status'] = 'ok';
 
		$this->response($final); 

	}
    public function index_get(){
        $this->authorization_token->authtoken();
        // $headers = $this->input->request_headers(); 
        // var_dump($headers['Authorizations']);
        // die;

		// if(isset($headers['Authorization'])){
		// 	$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
		// 	$this->response($decodedToken);  
		// }else{
		// 	$this->response([
        //         'status' => false,
        //         'message' => 'Unauthorized'
        //     ],401);
		// }


        $id = $this->get('id');
        if($id === null ){
            $mahasiswa = $this->mahasiswa->getMahasiswa();
            
        }else{
            $mahasiswa = $this->mahasiswa->getMahasiswa($id);
        }
        if($mahasiswa){
            $this->response([
                'status' => true,
                'data'  => $mahasiswa
            ],200);
        }else{
            $this->response([
                'status' => false,
                'message' => 'id not found'
            ],404);
        }
    }

    public function index_delete(){
        $id = $this->delete('id');
        if($id === null){
            $this->response([
                'status' => false,
                'message' => 'provide an id!'
            ],400);
        }else{
            if($this->mahasiswa->deleteMahasiswa($id) > 0){
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

    public function index_post(){
        
        $data = [
            'nrp'   => $this->post('nrp'),
            'nama'  => $this->post('nama'),
            'email'  => $this->post('email'),
            'jurusan'  => $this->post('jurusan')
        ];

        if($this->mahasiswa->createMahasiswa($data) > 0){
            $this->response([
                'status' => true,
                'message' => 'Data berhasil disimpan'
            ],201);
        }else{
            $this->response([
                'status' => false,
                'message' => 'Gagal menambah data'
            ],404);
        }
    }

    public function index_put(){
        $id = $this->put('id');
        $data = [
            'nrp'   => $this->put('nrp'),
            'nama'  => $this->put('nama'),
            'email'  => $this->put('email'),
            'jurusan'  => $this->put('jurusan')
        ];

        if($this->mahasiswa->updateMahasiswa($data,$id) > 0){
            $this->response([
                'status' => true,
                'message' => 'Data berhasil diupdate'
            ],200);
        }else{
            $this->response([
                'status' => false,
                'message' => 'Gagal edit data'
            ],400);
        }
    }

    public function users_get()
    {
        // Users from a data store e.g. database
        $users = [
            ['id' => 0, 'name' => 'John', 'email' => 'john@example.com'],
            ['id' => 1, 'name' => 'Jim', 'email' => 'jim@example.com'],
        ];

        $id = $this->get( 'id' );
        echo $id;
        die;
        if ( $id === null )
        {
            // Check if the users data store contains users
            if ( $users )
            {
                // Set the response and exit
                $this->response( $users, 200 );
            }
            else
            {
                // Set the response and exit
                $this->response( [
                    'status' => false,
                    'message' => 'No users were found'
                ], 404 );
            }
        }
        else
        {
            if ( array_key_exists( $id, $users ) )
            {
                $this->response( $users[$id], 200 );
            }
            else
            {
                $this->response( [
                    'status' => false,
                    'message' => 'No such user found'
                ], 404 );
            }
        }
    }
}