<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Barcode_model extends MY_Model
{

    protected $_table_name = 'tbl_barcode';
	protected $_primary_key = 'id';
	protected $_order_by = 'id';
	protected $_order_by_type = 'desc';

	public function count_filter($query,$req_status=null,$keyall=null)
	{
		$this->db->select('count("tbl_barcode.id") as qty');
        // $this->db->join('tbl_user','tbl_register_sertifikat.user_id = tbl_user.id','join');
		// if(!empty($keyall)){
		// 	$this->db->group_start();
		// 	$this->db->like($keyall);
		// 	$this->db->group_end();
		// }else{
		// }
		$this->db->group_start();
		$this->db->or_like($this->_table_name.'.nama_produk', $query, 'BOTH');
		$this->db->group_end();
		$this->db->where('tbl_barcode.status','publish');
		$this->db->where('tbl_barcode.payment_status',$req_status);
		return $this->db->get($this->_table_name)->row()->qty;
	}

	public function listRegis($start, $length, $query, $keysearch,$req_status,$keyall=null){
        $this->db->select('tbl_barcode.*,tbl_user.nama,tbl_user.email');
        $this->db->join('tbl_user','tbl_barcode.user_id = tbl_user.id','join');

		if(!empty($keyall)){
			$this->db->group_start();
			$this->db->like($keyall);
			$this->db->group_end();
		}else{
			$this->db->group_start();
			$this->db->or_like($keysearch, $query, 'BOTH');
			$this->db->group_end();
		}
		$this->db->where('tbl_barcode.status','publish');
		$this->db->where('tbl_barcode.payment_status',$req_status);
        $this->db->order_by($this->_table_name.'.'.$this->_primary_key, 'desc');
        return $this->db->get($this->_table_name,$length, $start)->result();
    }

	function list_akun_qr($start, $length, $query, $keysearch)
	{
		// echo $query;
		$this->db->select('tbl_user.id,tbl_user.nama,tbl_user.email,tbl_user.foto,tbl_user.user_code,tbl_barcode_profile.id_user,tbl_barcode_profile.nama_brand,tbl_barcode_profile.url_toko');
        $this->db->join('tbl_barcode_profile', 'tbl_barcode_profile.id_user = tbl_user.id','join');
		$this->db->group_start();
		$this->db->or_like($keysearch, $query, 'BOTH');
		$this->db->group_end();

		$this->db->where('tbl_user.role','toko');
		$this->db->order_by('tbl_user.id', 'desc');
		return $this->db->get('tbl_user', $length, $start)->result();
	}
    
}
