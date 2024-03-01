<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sertifikat_model extends MY_Model
{

    protected $_table_name = 'tbl_register_sertifikat';
	protected $_primary_key = 'id';
	protected $_order_by = 'id';
	protected $_order_by_type = 'desc';

    public function createUser($data){
        $this->db->insert('tbl_toko',$data);
        return $this->db->affected_rows();
    }

    public function getSertifListUser($id){
		if($id == null || empty($id)){
			return null;
		}
		$this->db->select('tbl_register_sertifikat.id,tbl_register_sertifikat.user_id,tbl_register_sertifikat.toko_id,tbl_register_sertifikat.sertif_code,tbl_register_sertifikat.note,tbl_register_sertifikat.status,tbl_register_sertifikat.created_at,tbl_toko.nama_toko,tbl_toko.alamat,tbl_toko.toko_code');
		$this->db->join('tbl_toko','tbl_register_sertifikat.toko_id = tbl_toko.toko_code','join');
		$this->db->where('tbl_register_sertifikat.user_id',$id);
		$this->db->order_by('tbl_register_sertifikat.id','desc');
		return $this->db->get($this->_table_name)->result();
		// echo $this->db->last_query(); die;
	}

    // function list($start, $length, $query, $keysearch,$role)
	// {
	// 	// echo $query;
	// 	$this->db->select('tbl_user.id,tbl_user.nama,tbl_user.email,tbl_user.foto,tbl_user.user_code,tbl_toko.id as toko_id,tbl_toko.nama_toko,tbl_toko.alamat,tbl_toko.toko_code');
    //     $this->db->join('tbl_user', 'tbl_toko.user_id = tbl_user.id','join');
	// 	$this->db->group_start();
	// 	$this->db->or_like($keysearch, $query, 'BOTH');
	// 	$this->db->group_end();

	// 	$this->db->where('tbl_user.role',$role);
	// 	$this->db->where('tbl_toko.status','publish');
	// 	$this->db->order_by($this->_table_name.'.'.$this->_primary_key, 'desc');
	// 	return $this->db->get($this->_table_name, $length, $start)->result();
	// }

    // public function count_filter($query)
	// {
	// 	$this->db->select('count("id") as qty');
	// 	$this->db->group_start();
	// 	$this->db->or_like('nama_toko', $query, 'BOTH');
	// 	$this->db->group_end();
	// 	$this->db->where($this->_table_name.'.status','publish');
	// 	return $this->db->get($this->_table_name)->row()->qty;
	// }
    public function listRegis($start, $length, $query, $keysearch,$req_status,$keyall=null){
        $this->db->select('tbl_register_sertifikat.*,tbl_toko.id as toko_id,tbl_toko.nama_toko,tbl_toko.alamat,tbl_toko.toko_code,tbl_user.nama,tbl_user.email');
        $this->db->join('tbl_toko','tbl_register_sertifikat.toko_id = tbl_toko.toko_code','join');
        $this->db->join('tbl_user','tbl_register_sertifikat.user_id = tbl_user.id','join');

		if(!empty($keyall)){
			$this->db->group_start();
			$this->db->like($keyall);
			$this->db->group_end();
		}else{
			$this->db->group_start();
			$this->db->or_like($keysearch, $query, 'BOTH');
			$this->db->group_end();
		}
        // $this->db->group_start();
		// $this->db->or_like($keysearch, $query, 'BOTH');
		// $this->db->group_end();
		if(empty($keyall)){
			$this->db->where('tbl_register_sertifikat.status',$req_status);
		}
        $this->db->order_by($this->_table_name.'.'.$this->_primary_key, 'desc');
        return $this->db->get($this->_table_name,$length, $start)->result();
    }
    public function count_filter($query,$req_status,$keyall=null)
	{
		$this->db->select('count("tbl_register_sertifikat.id") as qty,tbl_user.nama');
        $this->db->join('tbl_user','tbl_register_sertifikat.user_id = tbl_user.id','join');
		if(!empty($keyall)){
			$this->db->group_start();
			$this->db->like($keyall);
			$this->db->group_end();
		}else{
			$this->db->group_start();
			$this->db->or_like('tbl_user.nama', $query, 'BOTH');
			$this->db->group_end();
		}
		if(empty($keyall)){
			$this->db->where('tbl_register_sertifikat.status',$req_status);
		}
		return $this->db->get($this->_table_name)->row()->qty;
	}
}
