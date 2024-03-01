<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Barcode_img_model extends MY_Model
{

    protected $_table_name = 'tbl_barcode_img';
	protected $_primary_key = 'id';
	protected $_order_by = 'id';
	protected $_order_by_type = 'desc';


	public function cek_code($code){
		// echo $query;
		$this->db->select('tbl_barcode_img.id_barcode,tbl_barcode_img.barcode_kode,tbl_barcode_img.file_name,tbl_barcode.*');
        $this->db->join('tbl_barcode', 'tbl_barcode_img.id_barcode = tbl_barcode.id','join');
		$this->db->where('tbl_barcode_img.barcode_kode',$code);
		// $this->db->order_by('tbl_user.id', 'desc');
		return $this->db->get('tbl_barcode_img')->row();
	}

}