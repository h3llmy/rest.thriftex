<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Brand_model extends MY_Model
{

    protected $_table_name = 'tbl_brand';
	protected $_primary_key = 'id';
	protected $_order_by = 'id';
	protected $_order_by_type = 'desc';

	public function list_pagination($limit, $page_number, $search = NULL) {
		$offset = ($page_number - 1) * $limit;
	
		// Apply search filters if provided
		if (!empty($search)) {
			$this->db->group_start();
			$this->db->like('id', $search, 'both');
			$this->db->or_like('kategori_id', $search, 'both');
			$this->db->or_like('brand_name', $search, 'both');
			$this->db->or_like('deskripsi', $search, 'both');
			$this->db->or_like('created_at', $search, 'both');
			$this->db->or_like('updated_at', $search, 'both');
			$this->db->group_end();
		}
	
		// Count the total filtered data
		$total_data_count = $this->db->count_all_results($this->_table_name, FALSE);
	
		// Calculate total pages
		$total_pages = ceil($total_data_count / $limit);
	
		// Set limit and offset
		$this->db->limit($limit, $offset);
	
		// Fetch paginated data
		$paginated_data = $this->db->get()->result();
	
		// Return an array containing paginated data, total pages, and total data count
		return array(
			'total_pages' => $total_pages,
			'total_data' => $total_data_count,
			'current_page' => $page_number,
			'data' => $paginated_data,
		);
	}
    
}
