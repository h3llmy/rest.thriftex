<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends MY_Model
{

    protected $_table_name = 'tbl_user';
	protected $_primary_key = 'id';
	protected $_order_by = 'id';
	protected $_order_by_type = 'desc';
    // public function getMahasiswa($id = null){
    //     if($id == null){
    //         return $this->db->get('mahasiswa')->result_array();
    //     }else{
    //         return $this->db->get_where('mahasiswa',['id' => $id])->result_array();
    //     }
    // }

    // public function deleteMahasiswa($id){
    //     $this->db->delete('mahasiswa',['id'=>$id]);
    //     return $this->db->affected_rows();
    // }

	public function list_pagination($limit, $page_number, $search = NULL, $role = NULL) {
		$offset = ($page_number - 1) * $limit;
	
		// Initialize the query builder
		$this->db->select('id, nama, username, email, foto, role, register_tipe, validator_brand_id, validator_kategori_id, user_code, gid, no_hp, jenis_kelamin, is_active, created_at, updated_at');
	
		// Apply search filters if provided
		if (!empty($search)) {
			$this->db->group_start();
			$this->db->like('id', $search, 'both');
			$this->db->or_like('nama', $search, 'both');
			$this->db->or_like('username', $search, 'both');
			$this->db->or_like('email', $search, 'both');
			$this->db->or_like('role', $search, 'both');
			$this->db->or_like('register_tipe', $search, 'both');
			$this->db->or_like('user_code', $search, 'both');
			$this->db->group_end();
		}

		if (!empty($role)) {
			$this->db->where($this->_table_name.'.role',$role);
		}
	
		// Count the total filtered data
		$total_data_count = $this->db->count_all_results($this->_table_name, FALSE);
	
		// Calculate total pages
		$total_pages = ceil($total_data_count / $limit);
	
		// Set limit and offset
		$this->db->limit($limit, $offset);
	
		// Fetch paginated data
		$paginated_data = $this->db->get()->result();

		foreach ($paginated_data as $data) {
			$data->is_active = $data->is_active == '1' ? TRUE : FALSE;
		}
	
		// Return an array containing paginated data, total pages, and total data count
		return array(
			'total_pages' => $total_pages,
			'total_data' => $total_data_count,
			'current_page' => $page_number,
			'data' => $paginated_data,
		);
	}
	
    public function createUser($data){
		// Check if the email already exists
		$existingUser = $this->db->get_where('tbl_user', array('email' => $data['email']))->row_array();

		if ($existingUser) {
			// Email already taken, return an error code or message
			throw new  Exception('email sudah terdaftar');// You can choose an appropriate value to indicate the error
		}
		
        $this->db->insert('tbl_user',$data);
        return $this->db->affected_rows();
    }

    function list($start, $length, $query, $keysearch,$role)
	{
		// echo $query;
		$kolom = ['nama','', 'username', 'email','foto','user_code'];
		$this->db->select('tbl_user.id,tbl_user.nama,tbl_user.username,tbl_user.email,tbl_user.foto,tbl_user.role,tbl_user.validator_brand_id,tbl_user.validator_kategori_id,tbl_user.user_code');
		// tbl_produk_stok.id_barang,tbl_produk_stok.jumlah,tbl_produk_stok.harga_beli
		$this->db->group_start();
		$this->db->or_like($keysearch, $query, 'BOTH');
		$this->db->group_end();

		// if ($this->input->get('iSortCol_0')) {
		// 	for ($i = 0; $i < intval($this->input->get('iSortingCols', TRUE)); $i++) {
		// 		if ($this->input->get('bSortable_' . intval($this->input->get('iSortCol_' . $i)), TRUE) == "true") {
		// 			$this->db->order_by($kolom[intval($this->input->get('iSortCol_' . $i, TRUE))], $this->input->get('sSortDir_' . $i, TRUE));
		// 		}
		// 	}
		// }
		$this->db->where('role',$role);
		$this->db->order_by($this->_table_name.'.'.$this->_primary_key, 'desc');
		return $this->db->get($this->_table_name, $length, $start)->result();
		// echo $this->db->last_query(); die;
	}

    public function count_filter($query,$role)
	{
		$this->db->select('count("id") as qty');
		$this->db->group_start();
		$this->db->or_like('nama', $query, 'BOTH');
		$this->db->group_end();
		$this->db->where($this->_table_name.'.role',$role);
		return $this->db->get($this->_table_name)->row()->qty;
	}

	public function delete_user($userId) {
		// Check if the user with the given ID exists
		$existingUser = $this->db->get_where($this->_table_name, array('id' => $userId))->row_array();
	
		if (!$existingUser) {
			// User not found, return an error code or message
			throw new  Exception('user tidak ditemukan'); // You can choose an appropriate value to indicate the error
		}
	
		// User exists, proceed with the deletion
		$this->db->where('id', $userId);
		$this->db->delete($this->_table_name);
		
		return $this->db->affected_rows();
	}

	public function block_user($userId, $isActive = TRUE) {
		$isActive = filter_var($isActive, FILTER_VALIDATE_BOOLEAN);
		// Check if the user with the given ID exists
		$existingUser = $this->db->get_where($this->_table_name, array('id' => $userId))->row_array();
	
		if (!$existingUser) {
			// User not found, return an error code or message
			throw new  Exception('user tidak ditemukan'); // You can choose an appropriate value to indicate the error
		}
	
		// User exists, proceed with the deletion
		$this->db->set(['is_active' => $isActive]);
		$this->db->where('id', $userId);
		$this->db->update($this->_table_name);
		
		return $this->db->affected_rows();
	}
}
