<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Barcode_profile_model extends MY_Model
{

    protected $_table_name = 'tbl_barcode_profile';
	protected $_primary_key = 'id';
	protected $_order_by = 'id';
	protected $_order_by_type = 'desc';


	public function cek_url($url){
		// echo $query;
		$this->db->select('tbl_barcode_profile.id_user,tbl_barcode_profile.nama_brand,tbl_barcode_profile.url_toko,tbl_barcode_profile.deskripsi_toko');
        // $this->db->join('tbl_barcode_profile', 'tbl_barcode_profile.id_user = tbl_user.id','join');
		$this->db->where('tbl_barcode_profile.url_toko',$url);
		// $this->db->order_by('tbl_user.id', 'desc');
		return $this->db->get('tbl_barcode_profile')->row();
	}

}