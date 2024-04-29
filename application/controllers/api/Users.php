<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use SebastianBergmann\Environment\Console;

class Users extends RestController {

    protected $user_detail;
    function __construct()
    {
        parent::__construct();
        $this->load->library('Authorization_Token');
        $this->load->helper(array('user_helper'));
        $this->load->model('User_model','user');
        $this->load->model('Brand_model','brand');
        $this->load->model('Validator_model','validator');
        $this->load->library('S3');
    }
    
    public function list_get() {
        $this->authorization_token->authtoken();
        $headers = $this->input->request_headers();
        $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
        if ($decodedToken['data']->role !== 'admin' && $decodedToken['data']->role !== 'validator') {
            return $this->response([
                'message' => 'Forbidden'
            ], 403);
        }

        $limit = (int)($this->input->get('limit') ?? 10);
        $page = (int)($this->input->get('page') ?? 1);
        $role = $this->input->get('role');
        
        $users = $this->user->list_pagination($limit, $page,$this->input->get('search'), $role);
        return $this->response([
            'data' => $users
        ],200);
    }

    public function listvalidator_get() {
        $this->authorization_token->authtoken();
        $headers = $this->input->request_headers();
        $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
        if ($decodedToken['data']->role !== 'admin' && $decodedToken['data']->role !== 'validator') {
            return $this->response([
                'message' => 'Forbidden'
            ], 403);
        }

        $limit = (int)($this->input->get('limit') ?? 10);
        $page = (int)($this->input->get('page') ?? 1);
        
        $users = $this->user->list_pagination($limit, $page,$this->input->get('search'), 'validator');
        return $this->response([
            'data' => $users
        ],200);
    }
    
    public function validators_get() {
        $this->authorization_token->authtoken();
        $headers = $this->input->request_headers();
        $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
        if ($decodedToken['data']->role !== 'admin' && $decodedToken['data']->role !== 'validator') {
            return $this->response([
                'message' => 'Forbidden'
            ], 403);
        }

        $limit = (int)($this->input->get('limit') ?? 10);
        $page = (int)($this->input->get('page') ?? 1);
        
        $users = $this->validator->list_pagination($limit, $page,$this->input->get('search'));
        return $this->response([
            'data' => $users
        ],200);
    }

    public function updatevalidator_post() {
        $this->authorization_token->authtoken();
        $headers = $this->input->request_headers();
        $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
        if ($decodedToken['data']->role !== 'admin') {
            return $this->response([
                'message' => 'Forbidden'
            ], 403);
        }

        $this->user->update([
            'nama' => $this->input->post('nama'),
            'email' => $this->input->post('email'),
            'validator_brand_id' => $this->input->post('validator_brand_id'),
        ], ['id' => $this->input->post('validator_id')]);

        return $this->response([
            'message' => 'success'
        ], 200);
    }

    public function blockuser_post() {
        $this->authorization_token->authtoken();
        $headers = $this->input->request_headers();
        $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
        if ($decodedToken['data']->role !== 'admin') {
            return $this->response([
                'message' => 'Forbidden'
            ], 403);
        }

        $this->user->block_user($this->input->post('user_id'),$this->input->post('is_active'));

        return $this->response([
            'message' => 'success'
        ], 200);
    }
    
    public function register_post()
	{   
        try {
            $nama = $this->input->post('nama');
            $email = $this->input->post('email');
            $password = $this->input->post('password');
            $this->form_validation->set_rules('nama', 'Nama', 'required');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[tbl_user.email]',array('is_unique' => 'Email sudah pernah terdaftar!'));
            $this->form_validation->set_rules('password', 'Password', 'required');
            $this->form_validation->set_rules('passconf', 'Konfirmsi Password', 'required|matches[password]',
                                                        array('matches' => 'Password Konfirmasi harus sama!')
            );
            $this->form_validation->set_message('required', '{field} tidak boleh kosong!');
            $this->form_validation->set_error_delimiters('', '');
            if(!$this->form_validation->run()) throw new Exception(validation_errors());

            // $data = array(
            //     'nama'      => $nama,
            //     'username'  => generate_username($nama),
            //     'password'  => bCrypt($password,12),
            //     'email'     => $email,
            //     'foto'      => '',
            // );
            $data['nama'] = $nama;
            $data['username'] = generate_username($nama);
            $data['password'] = bCrypt($password,12);
            $data['email'] = $email;
            $data['foto'] = '';
            if($this->input->post('role') != null){
                $data['role'] = $this->input->post('role');
                $data['validator_brand_id'] = $this->input->post('validator_brand_id');
                // $data['validator_kategori_id'] = $this->input->post('validator_kategori_id');
            } else if ($this->input->post('role') == 'admin') {
                $data['validator_brand_id'] = '999';
            }
            $data['user_code'] = intCodeRandom(4);

            $register = $this->user->createUser($data);
            if($register){
                $this->response([
                    'status' => true,
                    'message'   => 'Register Berhasil',
                    'data'  => []
                ],200);
            }else{
                throw new Exception('Register Fail!');
            }

        } catch (\Throwable $th) {
            $this->response([
                'status' => false,
                'message'   => $th->getMessage(),
                'error_data'    => [
                    'nama'  => form_error('nama'),
                    'email' => form_error('email'),
                    'password' => form_error('password'),
                    'passconf' => form_error('passconf'),
                ]
            ],400);
        }
	}

    public function googleauth_post(){
        $post = $this->input->post();
        $email = $this->input->post('email_address');
        if(isset($email)){
            $cek_email = $this->user->get_by(array('email' => $email),1,NULL,TRUE,array('id','nama','username','password','email','role','register_tipe','validator_brand_id','validator_kategori_id','user_code','no_hp','jenis_kelamin'));
            if(!empty($cek_email)){
                $token_data = array(
                    'user_id'   => $cek_email->id,
                    'nama'  => $cek_email->nama,
                    'username' => $cek_email->username,
                    'email'  => $cek_email->email,
                    'role'  => $cek_email->role,
                    'foto'  => $cek_email->foto,
                    'validator_brand_id'    => $cek_email->validator_brand_id,
                    'validator_kategori_id'    => $cek_email->validator_kategori_id,
                    'user_code' => $cek_email->user_code,
                    'no_hp'  => (!empty($this->cek_email->no_hp))?$this->cek_email->no_hp:'-',
                    'jenis_kelamin'  => (!empty($this->cek_email->jenis_kelamin))?$this->cek_email->jenis_kelamin:'-',
                );
                $token = $this->authorization_token->generateToken($token_data);
                //update user info to google account
                $update_akun_user_to_google = array(
                    'foto'  => $post['profile_picture'],
                    'register_tipe' => 'google',
                    'gid' => $post['gid'],
                    'updated_at' => date('Y-m-d H:i:s')
                );
                $this->user->update($update_akun_user_to_google,array('email'=>$email));
                $this->response([
                    'status' => true,
                    'uid'   => $cek_email->id,
                    'message'   => 'Login Berhasil!',
                    'token'  => $token
                ],200);
                // if($cek_email == 'google'){
                // }else{
                //     $this->response([
                //         'status' => false,
                //         'message'   => 'Maaf, akun email anda terdaftar manual ',
                //     ],200);
                // }
            }else{
                //register new email
                $data['nama'] = $post['full_name'];
                $data['username'] = generate_username($post['full_name']);
                $data['password'] = '';
                $data['email'] = $email;
                $data['foto'] = $post['profile_picture'];
                $data['role'] = 'user';
                $data['register_tipe'] = 'google';
                $data['validator_kategori_id'] = 0;
                $data['user_code'] = intCodeRandom(4);
                $data['gid'] = $post['gid'];
                $data['updated_at'] = date('Y-m-d H:i:s');

                $register = $this->user->insert($data);
                if($register){
                    $cek_email = $this->user->get_by(array('id' => $register),1,NULL,TRUE,array('id','nama','username','password','email','role','register_tipe','validator_brand_id','validator_kategori_id','user_code'));
                    $token_data = array(
                        'user_id'   => $cek_email->id,
                        'nama'  => $cek_email->nama,
                        'username' => $cek_email->username,
                        'email'  => $cek_email->email,
                        'role'  => $cek_email->role,
                        'validator_brand_id'    => $cek_email->validator_brand_id,
                        'validator_kategori_id'    => $cek_email->validator_kategori_id,
                        'user_code' => $cek_email->user_code
                    );
                    $token = $this->authorization_token->generateToken($token_data);
                    $this->response([
                        'status' => true,
                        'uid'   => $cek_email->id,
                        'message'   => 'Login Berhasil!',
                        'token'  => $token
                    ],200);
                }else{
                    $this->response([
                        'status' => false,
                        'message'   => 'Gagal, silahkan ulangi kembali',
                        'data'  => []
                    ],500);
                }
            }
            // $this->response([
            //     'status' => false,
            //     'email'   => '$cek_email',
            // ]);
        }
    }

    public function login_post(){

        $email = $this->input->post('email');
        $password = $this->input->post('password');
        if(isset($email)){
            $cek_email = $this->user->get_by(array('email' => $email),1,NULL,TRUE,array('id','nama','username','password','email','role','register_tipe','validator_brand_id','validator_kategori_id','user_code','no_hp','jenis_kelamin', 'foto', 'is_active'));
            if ($cek_email->is_active == FALSE) {
                $this->response([
                    'status' => false,
                    'message'   => 'akun tidak aktif!',
                ],400);
            }
            if($cek_email != null){
                $this->user_detail = $cek_email;
            }else{
                $this->response([
                    'status' => false,
                    'message'   => 'Email tidak terdaftar!',
                ],400);
            }
        }
        try {
            if($this->user_detail->register_tipe == 'google'){
                $this->response([
                    'status' => false,
                    'message'   => 'Gagal, email anda terhubung dengan metode login akun Google',
                ],200);
            }
            $this->form_validation->set_rules('email', 'Email', 'required');
            $this->form_validation->set_rules('password', 'Password', 'required|callback_password_check');

            $this->form_validation->set_message('required', '{field} tidak boleh kosong!');
            $this->form_validation->set_error_delimiters('', '');
            if(!$this->form_validation->run()) throw new Exception(validation_errors());

            $token_data = array(
                'user_id'   => $this->user_detail->id,
                'nama'  => $this->user_detail->nama,
                'username' => $this->user_detail->username,
                'email'  => $this->user_detail->email,
                'role'  => $this->user_detail->role,
                'foto'  => $this->user_detail->foto,
                'no_hp'  => $this->user_detail->no_hp,
                'jenis_kelamin'  => $this->user_detail->jenis_kelamin,
                'validator_brand_id'    => $this->user_detail->validator_brand_id,
                'validator_kategori_id'    => $this->user_detail->validator_kategori_id,
                'user_code' => $this->user_detail->user_code
            );

            $token = $this->authorization_token->generateToken($token_data);
            $token_refresh = $this->authorization_token->generateTokenRefresh($token_data);

            $this->response([
                'status' => true,
                'uid'   => $this->user_detail->id,
                'message'   => 'Login Berhasil!',
                'access_token'  => $token,
                'refresh_token' => $token_refresh
            ],200);
            
        } catch (\Throwable $th) {
            $this->response([
                'status' => false,
                'message'   => $th->getMessage(),
            ],400);
        }
    }

    public function refresh_token_post() {
        $refresh_token = $this->input->post('refresh_token');

        $decodedToken = $this->authorization_token->validateTokenRefresh($refresh_token);

        $token_data = array(
            'user_id'   => $decodedToken['data']->user_id,
            'nama'  => $decodedToken['data']->nama,
            'username' => $decodedToken['data']->username,
            'email'  => $decodedToken['data']->email,
            'role'  => $decodedToken['data']->role,
            'foto'  => $decodedToken['data']->foto,
            'no_hp'  => $decodedToken['data']->no_hp,
            'jenis_kelamin'  => $decodedToken['data']->jenis_kelamin,
            'validator_brand_id'    => $decodedToken['data']->validator_brand_id,
            'validator_kategori_id'    => $decodedToken['data']->validator_kategori_id,
            'user_code' => $decodedToken['data']->user_code
        );

        $token = $this->authorization_token->generateToken($token_data);
        $token_refresh = $this->authorization_token->generateTokenRefresh($token_data);

        return $this->response([
            'access_token'  => $token,
            'refresh_token' => $token_refresh
        ], 200);
    }

    public function password_check($str){
		$user_detail = $this->user_detail;
		if($user_detail){
			if($user_detail->password == crypt($str,$user_detail->password)){
				return TRUE;
			}else{
				$this->form_validation->set_message('password_check','Password salah!');
				return FALSE;
			}
		}else{
			$this->form_validation->set_message('password_check','Password salah!');
        var_dump($user_detail);

			return FALSE;
		}
	}

    public function validatetoken_post(){
        $headers = $this->input->request_headers();
        if(isset($headers['Authorization'])){
			$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
            $get_user = $this->user->get_by(array('id'=>$decodedToken['data']->user_id),null,null,true,array('id as user_id','nama','username','email','role','no_hp','jenis_kelamin','validator_brand_id','validator_kategori_id','user_code'));
            if($get_user){
                $this->response([
                    'status' => true,
                    'data' => $get_user
                ],200);
                // echo json_encode($get_user);
                // $this->response($decodedToken);  
            }
		}else{
			$this->response([
                'status' => false,
                'message' => 'Unauthorized'
            ],401);
		}
    }
    // public function checkuserdetail_post(){
    //     $headers = $this->input->request_headers();
    //     if(isset($headers['Authorization'])){
	// 		$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
    //         $get_user = $this->user->get_by(array('id'=>$decodedToken['data']->user_id),null,null,true,array('id','nama','username','email','role','no_hp','jenis_kelamin'));
    //         if($get_user){
    //             $this->response($get_user);  
    //         }
	// 	}else{
	// 		$this->response([
    //             'status' => false,
    //             'message' => 'Unauthorized'
    //         ],401);
	// 	}
    // }

    public function datasummary_get(){
        $this->authorization_token->authtoken();
        $headers = $this->input->request_headers();
        $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);

        $tipe = $this->get('tipe');
        $user_id = $decodedToken['data']->user_id;
        $dataSumary = $this->validator->validator_sumary($user_id,$tipe);
        if($dataSumary){
            $this->response([
                'status' => true,
                'data'  => $dataSumary,
            ],200);
        }else{
            $this->response([
                'status' => false,
                'message' => 'Case Code Not Found!'
            ],404);
        }
    }

    public function userlist_get(){
        $this->authorization_token->authtoken();
        $headers = $this->input->request_headers();
        $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
        if($decodedToken['status'] == true && $decodedToken['data']->role == 'admin'){
            $param = $this->get('datatable');
            $query = $param['sSearch'];
            $start = $param['iDisplayStart'];
            $length = $param['iDisplayLength'];
            $keySearch = 'nama';

            $role = $this->get('role');
            $result['sEcho'] = intval($param['sEcho']);
            $result['iTotalRecords'] = $this->user->count('role = "'.$role.'"');
            $result['iTotalDisplayRecords'] = $this->user->count_filter($query,$role);
            if ($length == -1) $length = $result['iTotalDisplayRecords'];
            $data = $this->user->list($start, $length, $query, $keySearch,$role);
            foreach ($data as $key) {
                $key->data_nama = '<label for="" class="font-16">'.$key->nama.'</label><p class="font-400 font-12 p-0 m-0">'.$key->email.'</p>';
            }
            $result['aaData'] = $data;
            $this->response($result);
        }else{
            $this->response([
                'status'    => false,
                'message'   => "hanya bisa diakses oleh admin"
            ]);
        }
    }

    public function userlistselect_get(){
        $this->authorization_token->authtoken();
        $headers = $this->input->request_headers();
        $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
        if($decodedToken['status'] == true && $decodedToken['data']->role == 'admin'){
            $param = $this->get('select2');
            $query = $param['q'];
            $keySearch = 'email';

            $role = $this->get('role');
            $data = $this->user->list('','',$query, $keySearch,$role);
            foreach ($data as $key) {
                // $key->data_nama = '<label for="" class="font-16">'.$key->nama.'</label><p class="font-400 font-12 p-0 m-0">'.$key->email.'</p>';
            }
            $result['status'] = 200;
            $result['data'] = $data;
            $this->response($result);
        }else{
            $this->response([
                'status'    => false,
            ]);
        }
    }

    public function updateuserprofile_post() {
        $config['upload_path'] = './media/';
        $config['allowed_types'] = 'jpg|jpeg|png|webp|image/jpeg|PNG|JPEG|JPG';
        $config['overwrite'] = FALSE;
        $config['max_size'] = '10000';
        $config['encrypt_name'] = TRUE;
        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        try {
            $this->form_validation->set_rules('username', 'Username', 'required');
            $this->form_validation->set_rules('nama', 'Nama', 'required');
            $this->form_validation->set_rules('no_hp', 'Phone Number', 'required');
            $this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'required');
            
            if (!empty($this->input->post('old_password'))) {
                $this->form_validation->set_rules('old_password', 'Old Password', 'required|callback_password_check');
                $this->form_validation->set_rules('password', 'Password', 'required');
                $this->form_validation->set_rules('passconf', 'Konfirmasi Password', 'required|matches[password]');
            }
    
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email', array('matches' => 'Password Konfirmasi harus sama!'));
            $this->form_validation->set_message('required', '{field} tidak boleh kosong!');
            
            $this->authorization_token->authtoken();
            $headers = $this->input->request_headers();
            $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
            
            $this->user_detail = $this->user->get($decodedToken['data']->user_id, true);
            
            // Run form validation
            if (!$this->form_validation->run()) {
                throw new Exception(validation_errors());
            }
    
            $data_foto = array();
            // Check if a file is uploaded
            if (!empty($_FILES['foto']['name'])) {
                if ($this->upload->do_upload('foto')) {
                    $uploadData = $this->upload->data();
                    $imageUrl = $this->s3->singleUpload($uploadData['full_path']);
                    $data_foto[] = array(
                        'nama_foto' => $imageUrl
                    );
                    unlink($uploadData['full_path']);
                } else {
                    $error = array('error' => $this->upload->display_errors());
                    $response = array(
                        'status' => false,
                        'msg' => 'Foto Gagal di Upload!',
                        'error' => $error
                    );
                    echo json_encode($response);
                    die;
                }
            }

            $user_updated_data = [
                "username" => $this->input->post('username'),
                "nama" => $this->input->post('nama'),
                "no_hp" => $this->input->post('no_hp'),
                "jenis_kelamin" => $this->input->post('jenis_kelamin'),
                "email" => $this->input->post('email'),  
                'foto' => !empty($data_foto) ? $data_foto[0]['nama_foto'] : $this->user_detail->foto
            ];

            if (!empty($this->input->post('password'))) {
                $user_updated_data['password'] = bCrypt($this->input->post('password'), 12);
            }
    
            // Update user profile
            $user = $this->user->update($user_updated_data, ['id' => $decodedToken['data']->user_id]);
    
            $user_detail = $this->user->get($decodedToken['data']->user_id, TRUE);
    
            $token_data = array(
                'user_id' => $user_detail->id,
                'nama' => $user_detail->nama,
                'username' => $user_detail->username,
                'email' => $user_detail->email,
                'role' => $user_detail->role,
                'no_hp' => $user_detail->no_hp,
                'foto' => $user_detail->foto,
                'jenis_kelamin' => $user_detail->jenis_kelamin,
                'validator_brand_id' => $user_detail->validator_brand_id,
                'validator_kategori_id' => $user_detail->validator_kategori_id,
                'user_code' => $user_detail->user_code
            );
    
            $token = $this->authorization_token->generateToken($token_data);
            $token_refresh = $this->authorization_token->generateTokenRefresh($token_data);
    
            if ($user === TRUE) {
                // Return response
                $this->response([
                    "message" => "user updated",
                    "access_token" => $token,
                    "refresh_token" => $token_refresh,
                ]);
            } else {
                $this->response([
                    "message" => 'fail to update user'
                ], 400);
            }
        } catch (\Throwable $th) {
            // Handle exceptions
            $this->response([
                'status' => false,
                'message' => $th->getMessage(),
                'error_data' => [
                    'nama' => form_error('nama'),
                    'username' => form_error('username'),
                    'no_hp' => form_error('no_hp'),
                    'jenis_kelamin' => form_error('jenis_kelamin'),
                    'old_password' => form_error('old_password'),
                    'password' => form_error('password'),
                    'passconf' => form_error('passconf'),
                    'email' => form_error('email'),
                ]
            ], 400);
        }
    }
    

    public function checkusername_post(){
        $this->authorization_token->authtoken();
        $headers = $this->input->request_headers();
        $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
        $data = $this->input->post();
        $cek_username = $this->user->get_by(array('username' => $data['username']),null,null,true,array('username'));
        if(!empty($cek_username)){
            if($cek_username->username == $decodedToken['data']->username){
                $this->response([
                    'availability'    => true,
                ]);    
            }else{
                $this->response([
                    'availability'    => false,
                ]);
            }
        }else{
            $this->response([
                'availability'    => true,
            ]);
        }
    }
    public function saveuseredit_post(){
        $this->authorization_token->authtoken();
        $headers = $this->input->request_headers();
        $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
        // var_dump($decodedToken);
        // die;
        try{

            $this->form_validation->set_rules('nama', 'Nama', 'required');
            $this->form_validation->set_rules('username', 'Username', 'required');
            $this->form_validation->set_message('required', '{field} tidak boleh kosong!');
            $this->form_validation->set_error_delimiters('', '');
            
            if(!$this->form_validation->run()) throw new Exception(validation_errors());

            $data = $this->input->post();
            $data = array(
                'nama'              =>$data['nama'],
                'username'          =>$data['username'],
                'no_hp'             =>$data['no_hp'],
                'jenis_kelamin'     =>$data['jenis_kelamin']
            );
            $register = $this->user->update($data,array('id'=>$decodedToken['data']->user_id));
            if($register){
                $this->response([
                    'status' => true,
                    'message'   => 'Register Berhasil',
                    'data'  => []
                ],200);
            }else{
                throw new Exception('Register Fail!');
            }
        } catch (\Throwable $th) {
            $this->response([
                'status' => false,
                'message'   => $th->getMessage(),
                'error_data'    => [
                    'nama'  => form_error('nama'),
                    'username' => form_error('username'),
                ]
            ],400);
        }
    }


    public function validatoredit_post(){
        $this->authorization_token->authtoken();
        $headers = $this->input->request_headers();
        $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
       
        $data = $this->input->post();
        $data_update = array(
            'validator_brand_id'    => $data['validator_brand_id']
        );
        $update = $this->user->update($data_update,array('id'=>$data['id_user']));
        if($update){
            $this->response([
                'status' => true,
                'message'   => 'Update Berhasil',
                'data'  => []
            ],200);
        }else{
            $this->response([
                'status' => false,
                'message'   => 'terjadi kesalahan',
            ],400);
        }
    }

    public function updatepassword_post() {
        try {
            $decodedToken = $this->authorization_token->validateToken();
           
            $this->form_validation->set_rules('password', 'Password', 'required');
            $this->form_validation->set_message('required', '{field} tidak boleh kosong!');
            $this->form_validation->set_rules('passconf', 'Konfirmsi Password', 'required|matches[password]',
                                                array('matches' => 'Password Konfirmasi harus sama!')
            );
            $this->form_validation->set_error_delimiters('', '');
            if(!$this->form_validation->run()) throw new Exception(validation_errors());
            
            $data = $this->input->post();
    
            $update_data = array(
                'password' => bCrypt($data['password'], 12)
            );
    
            $update = $this->user->update($update_data, array('id' => $decodedToken['data']->user_id));
    
            if ($update) {
                $this->response([
                    'status' => true,
                    'message'   => $decodedToken['data'],
                    'data'  => []
                ],200);
            } else {
                $this->response([
                    'status' => false,
                    'message'   => 'terjadi kesalahan',
                ],400);
            }
        } catch (\Throwable $th) {
            $this->response([
                'status' => false,
                'message'   => $th->getMessage(),
                'error_data'    => $this->form_validation->error_array()
            ],400);
        }
    }

    public function validatordelete_post(){
        try {
            $decodedToken = $this->authorization_token->validateToken();
            if ($decodedToken['data']->role != 'admin') {
                $this->response([
                    'status' => false,
                    'message'   => 'terjadi kesalahan',
                ],401);
            }
            $this->form_validation->set_rules('id_user', 'Id User', 'required');
            $this->form_validation->set_error_delimiters('', '');
            if(!$this->form_validation->run()) throw new Exception(validation_errors());
           
            $data = $this->input->post();
            $delete = $this->user->delete_user($data['id_user']);
            if($delete){
                $this->response([
                    'status' => true,
                    'message'   => 'Berhasil dihapus',
                    'data'  => []
                ],200);
            }else{
                $this->response([
                    'status' => false,
                    'message'   => 'terjadi kesalahan',
                ],400);
            }
        } catch (\Throwable $th) {
            $this->response([
                'status' => false,
                'message'   => $th->getMessage(),
                'error_data'    => $this->form_validation->error_array()
            ],400);
        }
    }

}