<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use SebastianBergmann\Environment\Console;

class Brand extends RestController{
    function __construct()
    {
        parent::__construct();
        $this->load->library('Authorization_Token');
        $this->load->model('Brand_model','brand');
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
        
        $users = $this->brand->list_pagination($limit, $page,$this->input->get('search'));
        return $this->response([
            'data' => $users
        ],200);
    }

    public function create_post() {
        $this->authorization_token->authtoken();
        $headers = $this->input->request_headers();
        $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
        if ($decodedToken['data']->role !== 'admin' && $decodedToken['data']->role !== 'validator') {
            return $this->response([
                'message' => 'Forbidden'
            ], 403);
        }
        $config['upload_path'] = './media/';
        $config['allowed_types'] = 'jpg|jpeg|png|webp|image/jpeg|PNG|JPEG|JPG';
		$config['overwrite']     = FALSE;
		$config['max_size'] = '10000';
		$config['encrypt_name'] = TRUE;
        $this->load->library('upload',$config);
        $this->upload->initialize($config);
        try {
            $this->form_validation->set_rules('brand_name', 'Brand Name', 'required');
            $this->form_validation->set_message('required', '{field} tidak boleh kosong!');
    
            // Run form validation
            if (!$this->form_validation->run()) {
                throw new Exception(validation_errors());
            }

            $data_foto = array();
            if($this->upload->do_upload('foto')){
                        
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

            $this->brand->insert([
                'brand_name' => $this->input->post('brand_name'),
                'foto' => $data_foto[0]['nama_foto'],
            ]);

            $this->response([
                'message' => 'brand berhasil dibuat',
            ], 200);
            
        } catch (\Throwable $th) {
            // Handle exceptions
            $this->response([
                'status' => false,
                'message'   => $th->getMessage(),
                'error_data' => [
                    'brand_name' => form_error('brand_name'),
                    'deskripsi' => form_error('deskripsi'),
                    'kategori_id' => form_error('kategori_id'),
                ]
            ], 400);
        }
        
    }

    public function update_post() {
        $this->authorization_token->authtoken();
        $headers = $this->input->request_headers();
        $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
        if ($decodedToken['data']->role !== 'admin' && $decodedToken['data']->role !== 'validator') {
            return $this->response([
                'message' => 'Forbidden'
            ], 403);
        }
        $config['upload_path'] = './media/';
        $config['allowed_types'] = 'jpg|jpeg|png|webp|image/jpeg|PNG|JPEG|JPG';
        $config['overwrite']     = FALSE;
        $config['max_size'] = '10000';
        $config['encrypt_name'] = TRUE;
        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        try {
            $this->form_validation->set_rules('brand_name', 'Brand Name', 'required');
            $this->form_validation->set_message('required', '{field} tidak boleh kosong!');
    
            // Run form validation
            if (!$this->form_validation->run()) {
                throw new Exception(validation_errors());
            }
    
            $data_foto = array();
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
                    'eror'  => $error
                );
                echo json_encode($response);
                die;
            }
    
            // Update the brand with the provided ID
            $this->brand->update([
                'brand_name' => $this->input->post('brand_name'),
                'foto' => $data_foto[0]['nama_foto'],
            ], ['id' => $this->input->post('id')]);
    
            $this->response([
                'message' => 'Brand berhasil diupdate',
            ], 200);
    
        } catch (\Throwable $th) {
            // Handle exceptions
            $this->response([
                'status' => false,
                'message'   => $th->getMessage(),
                'error_data' => [
                    'brand_name' => form_error('brand_name'),
                    'deskripsi' => form_error('deskripsi'),
                    'kategori_id' => form_error('kategori_id'),
                ]
            ], 400);
        }
    }
    

    public function detail_get() {
        $this->authorization_token->authtoken();
        $headers = $this->input->request_headers();
        $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
        if ($decodedToken['data']->role !== 'admin' && $decodedToken['data']->role !== 'validator') {
            return $this->response([
                'message' => 'Forbidden'
            ], 403);
        }

        $brand_id = $this->input->get('id');
        
        $brand = $this->brand->get($brand_id, true);
        return $this->response([
            'data' => $brand
        ],200);
    }

    public function delete_post() {
        $this->authorization_token->authtoken();
        $headers = $this->input->request_headers();
        $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
        if ($decodedToken['data']->role !== 'admin' && $decodedToken['data']->role !== 'validator') {
            return $this->response([
                'message' => 'Forbidden'
            ], 403);
        }

        $brand_id = $this->input->get('id');
        
        $brand = $this->brand->delete($brand_id, true);
        return $this->response([
            'message' => 'data berhasil dihapus'
        ],200);
    }
}