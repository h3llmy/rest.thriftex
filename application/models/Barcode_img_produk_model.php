<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Barcode_img_produk_model extends MY_Model
{

    protected $_table_name = 'tbl_barcode_img_produk';
	protected $_primary_key = 'id';
	protected $_order_by = 'id';
	protected $_order_by_type = 'desc';

	public function get_media($id_barcode){
		$this->db->select('tbl_barcode_img_produk.*,tbl_media.*');
		$this->db->join('tbl_media','tbl_barcode_img_produk.id_media = tbl_media.id','join');
		$this->db->where('tbl_barcode_img_produk.id_barcode',$id_barcode);
		return $this->db->get('tbl_barcode_img_produk')->result();
	}
	
}