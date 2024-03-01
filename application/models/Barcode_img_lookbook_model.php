<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Barcode_img_lookbook_model extends MY_Model
{

    protected $_table_name = 'tbl_barcode_lookbook';
	protected $_primary_key = 'id';
	protected $_order_by = 'id';
	protected $_order_by_type = 'asc';
    

	public function get_media($id_barcode){
		$this->db->select('tbl_barcode_lookbook.*,tbl_media.*');
		$this->db->join('tbl_media','tbl_barcode_lookbook.id_media = tbl_media.id','join');
		$this->db->where('tbl_barcode_lookbook.id_barcode',$id_barcode);
		return $this->db->get('tbl_barcode_lookbook')->result();
	}
}
