<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Legit_model extends MY_Model
{

    protected $_table_name = 'tbl_legit_check';
	protected $_primary_key = 'id';
	protected $_order_by = 'id';
	protected $_order_by_type = 'desc';
    

	public function getLegitListAll($limit, $page_number, $search = NULL, $status = NULL) {
		$offset = ($page_number - 1) * $limit;

		$this->db->select('tbl_legit_check.id,tbl_legit_check.case_code,tbl_legit_check.user_id,tbl_legit_check.legit_status,tbl_legit_check.submit_time,tbl_gambar_legit.file_path,tbl_legit_check_detail.nama_item,tbl_validator.check_result,tbl_user.nama,tbl_brand.brand_name');
		$this->db->join('tbl_legit_check_detail','tbl_legit_check_detail.legit_id = tbl_legit_check.id','join');
		$this->db->join('tbl_gambar_legit','tbl_gambar_legit.legit_id = tbl_legit_check.id','join');
		$this->db->join('tbl_validator','tbl_validator.legit_id = tbl_legit_check.id','left');
		$this->db->join('tbl_user','tbl_legit_check.user_id = tbl_user.id','join');
		$this->db->join('tbl_brand','tbl_legit_check_detail.brand_id = tbl_brand.id','join');
		$this->db->order_by('tbl_legit_check.submit_time','desc');
		$this->db->group_by('tbl_gambar_legit.legit_id');
		$this->db->group_by('tbl_validator.legit_id');
		
		$this->db->where('tbl_legit_check.legit_status', 'posted');
		if (!empty($status)) {
			$this->db->where('tbl_validator.check_result', $status);
		}
		if (!empty($search)) {
			$this->db->group_start();
			$this->db->like('tbl_legit_check.case_code', $search);
			$this->db->or_like('tbl_legit_check_detail.nama_item', $search);
			$this->db->or_like('tbl_legit_check_detail.nama_brand', $search);
			$this->db->or_like('tbl_validator.check_result', $search);
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

	public function getLegitListUser($id, $limit, $page_number, $search = NULL){
		if($id == null || empty($id)){
			return null;
		}
		
		$offset = ($page_number - 1) * $limit;

		$this->db->select('tbl_legit_check.id,tbl_legit_check.case_code,tbl_legit_check.user_id,tbl_legit_check.legit_status,tbl_legit_check.submit_time,tbl_gambar_legit.file_path,tbl_legit_check_detail.nama_item,tbl_validator.check_result');
		$this->db->join('tbl_legit_check_detail','tbl_legit_check_detail.legit_id = tbl_legit_check.id','join');
		$this->db->join('tbl_gambar_legit','tbl_gambar_legit.legit_id = tbl_legit_check.id','join');
		$this->db->join('tbl_validator','tbl_validator.legit_id = tbl_legit_check.id','left');
		$this->db->where('tbl_legit_check.legit_status','posted');
		$this->db->where('tbl_legit_check.user_id',$id);
		$this->db->order_by('tbl_legit_check.submit_time','desc');
		$this->db->group_by('tbl_gambar_legit.legit_id');
		$this->db->group_by('tbl_validator.legit_id');

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

	public function getLegitListUserDetail($id = NULL,$case_code=null){
		// if($id == null || empty($id)){
		// 	return null;
		// }
		$this->db->select('tbl_legit_check.id,tbl_legit_check.case_code,tbl_legit_check.user_id,tbl_legit_check.legit_status,tbl_legit_check.submit_time,tbl_legit_check_detail.nama_item,tbl_validator.check_result, 
		tbl_legit_check_detail.toko_pembelian,
		tbl_legit_check_detail.catatan,
		tbl_legit_check_detail.kondisi,
		tbl_legit_check_detail.nama_brand,
		tbl_kategori.kategori_name,
		tbl_legit_check_detail.purchase'
		);
		$this->db->join('tbl_legit_check_detail','tbl_legit_check_detail.legit_id = tbl_legit_check.id','join');
		$this->db->join('tbl_validator','tbl_validator.legit_id = tbl_legit_check.id','left');
		$this->db->join('tbl_kategori','tbl_kategori.id = tbl_legit_check_detail.kategori_id','left');
		if (!empty($id)) {
			$this->db->where('tbl_legit_check.user_id',$id);
		}
		$this->db->where('tbl_legit_check.case_code',$case_code);
		$this->db->order_by('tbl_legit_check.submit_time','desc');
		$this->db->group_by('tbl_validator.legit_id');
		return $this->db->get($this->_table_name)->result();
	}
	public function legit_detail_by_code($case_code=null){
		$this->db->select('tbl_legit_check.id,tbl_legit_check.case_code,tbl_legit_check.user_id,tbl_legit_check.legit_status,tbl_legit_check.submit_time,tbl_legit_check_detail.nama_brand,tbl_legit_check_detail.nama_item,tbl_validator.check_result,tbl_validator.processing_status');
		$this->db->join('tbl_legit_check_detail','tbl_legit_check_detail.legit_id = tbl_legit_check.id','join');
		$this->db->join('tbl_validator','tbl_validator.legit_id = tbl_legit_check.id','left');
		$this->db->where('tbl_legit_check.legit_status','posted');
		$this->db->where('tbl_legit_check.case_code',$case_code);
		$this->db->order_by('tbl_legit_check.submit_time','desc');
		$this->db->group_by('tbl_validator.legit_id');
		return $this->db->get($this->_table_name)->row();
	}

	public function getLegitListByStatus($brand_id,$tipe=null,$brand_name=null, $limit = NULL, $page_number = NULL, $search=NULL){
		$offset = ($page_number - 1) * $limit;
		// $this->db->select('tbl_legit_check.id,tbl_legit_check.case_code,tbl_legit_check.user_id,tbl_legit_check.legit_status,tbl_legit_check.submit_time,tbl_gambar_legit.file_path,tbl_legit_check_detail.nama_item,tbl_legit_check_detail.nama_brand,tbl_validator.check_result,tbl_brand.brand_name');
		$this->db->select('tbl_legit_check.id,tbl_legit_check.case_code,tbl_legit_check.user_id,tbl_legit_check.legit_status,tbl_legit_check.submit_time,tbl_gambar_legit.file_path,tbl_legit_check_detail.nama_item,tbl_legit_check_detail.nama_brand as brand_name,tbl_validator.check_result');
		$this->db->join('tbl_legit_check_detail','tbl_legit_check_detail.legit_id = tbl_legit_check.id','join');
		$this->db->join('tbl_gambar_legit','tbl_gambar_legit.legit_id = tbl_legit_check.id','join');
		$this->db->join('tbl_validator','tbl_validator.legit_id = tbl_legit_check.id','left');
		// $this->db->join('tbl_brand','tbl_legit_check_detail.brand_id = tbl_brand.id','left');
		$this->db->where('tbl_legit_check.legit_status','posted');
		if($brand_id != 999 || $brand_id != '999'){
			$this->db->where('tbl_legit_check_detail.brand_id',$brand_id);
			// $this->db->where('tbl_legit_check_detail.kategori_id',$brand_id);
		}

		if (!empty($search)) {
			$this->db->group_start();
			$this->db->like('tbl_legit_check.case_code', $search);
			$this->db->or_like('tbl_legit_check_detail.nama_item', $search);
			$this->db->or_like('tbl_legit_check_detail.nama_brand', $search);
			$this->db->or_like('tbl_validator.check_result', $search);
			$this->db->group_end();
		}

		if(!empty($tipe)){
			if($tipe == 'complete'){
				$this->db->where('tbl_validator.check_result != ', 'processing');
			} else if ($tipe == 'process') {
				$this->db->where('tbl_validator.check_result', 'processing');
			} else{
				$this->db->where('tbl_validator.check_result',$tipe);
			}
		}
		$this->db->order_by('tbl_legit_check.submit_time','desc');
		$this->db->group_by('tbl_gambar_legit.legit_id');
		$this->db->group_by('tbl_validator.legit_id');

		// Count the total filtered data
		$total_data_count = $this->db->count_all_results($this->_table_name, FALSE);

		// Calculate total pages
		$total_pages = ceil($total_data_count / $limit);

		$this->db->limit($limit, $offset);

		$paginated_data = $this->db->get()->result();
		return array(
			'total_pages' => $total_pages,
			'total_data' => $total_data_count,
			'current_page' => $page_number,
			'data' => $paginated_data,
		);
		// echo $this->db->last_query(); die;
	}

	public function getValidateDetail($case_code){
		// $this->db->select('tbl_legit_check.id,tbl_legit_check.case_code,tbl_legit_check.user_id,tbl_legit_check.legit_status,tbl_legit_check.submit_time,tbl_legit_check_detail.nama_item,tbl_legit_check_detail.catatan,tbl_validator.check_result,tbl_validator.validator_user_id,tbl_kategori.kategori_name,tbl_brand.brand_name');
		$this->db->select('tbl_legit_check.id,tbl_legit_check.case_code,tbl_legit_check.user_id,tbl_legit_check.legit_status,tbl_legit_check.submit_time,tbl_legit_check_detail.nama_item,tbl_legit_check_detail.nama_brand as brand_name,tbl_legit_check_detail.kondisi,tbl_legit_check_detail.toko_pembelian,tbl_legit_check_detail.catatan,tbl_validator.check_result,tbl_validator.processing_status,tbl_validator.validator_user_id,tbl_kategori.kategori_name');
		$this->db->join('tbl_legit_check_detail','tbl_legit_check_detail.legit_id = tbl_legit_check.id','join');
		$this->db->join('tbl_validator','tbl_validator.legit_id = tbl_legit_check.id','left');
		$this->db->join('tbl_kategori','tbl_legit_check_detail.kategori_id = tbl_kategori.id');
		// $this->db->join('tbl_brand','tbl_legit_check_detail.brand_id = tbl_brand.id');
		$this->db->where('tbl_legit_check.legit_status','posted');
		$this->db->where('tbl_legit_check.case_code',$case_code);
		$this->db->order_by('tbl_legit_check.submit_time','desc');
		$this->db->group_by('tbl_validator.legit_id');
		return $this->db->get($this->_table_name)->result();
		// echo $this->db->last_query(); die;
	}

	public function getLegitListPublish($search=null){
		$this->db->select('tbl_legit_check.id,tbl_legit_check.case_code,tbl_legit_check.legit_status,tbl_legit_check.submit_time,tbl_gambar_legit.file_path,tbl_legit_check_detail.nama_item,tbl_validator.check_result');
		$this->db->join('tbl_legit_check_detail','tbl_legit_check_detail.legit_id = tbl_legit_check.id','join');
		$this->db->join('tbl_gambar_legit','tbl_gambar_legit.legit_id = tbl_legit_check.id','join');
		$this->db->join('tbl_validator','tbl_validator.legit_id = tbl_legit_check.id','left');
		if(!empty($search)){
			$this->db->like('tbl_legit_check.case_code',$search);
		}
		$this->db->where('tbl_legit_check.legit_status','posted');
		$this->db->where('tbl_validator.check_result != ', 'processing');
		$this->db->order_by('tbl_legit_check.submit_time','desc');
		$this->db->group_by('tbl_gambar_legit.legit_id');
		$this->db->group_by('tbl_validator.legit_id');
		$this->db->limit(20);
		return $this->db->get($this->_table_name)->result();
		// echo $this->db->last_query(); die;
	}
	public function searchPublishLegit($search=null){
		if ($search !== null) {
			$this->db->like('tbl_legit_check.case_code', $search);
		}
	
		$this->db->select('tbl_legit_check.id,tbl_legit_check.case_code,tbl_legit_check.legit_status,tbl_legit_check.submit_time,tbl_gambar_legit.file_path,tbl_legit_check_detail.nama_brand,tbl_legit_check_detail.nama_item,tbl_validator.check_result');
		$this->db->join('tbl_legit_check_detail','tbl_legit_check_detail.legit_id = tbl_legit_check.id','join');
		$this->db->join('tbl_gambar_legit','tbl_gambar_legit.legit_id = tbl_legit_check.id','left');
		$this->db->join('tbl_validator','tbl_validator.legit_id = tbl_legit_check.id','left');
		// $this->db->like('tbl_legit_check.case_code',$search);
		$this->db->where('tbl_legit_check.legit_status','posted');
		// $this->db->where('tbl_validator.check_result != ', 'processing');
		$this->db->order_by('tbl_legit_check.submit_time','desc');
		$this->db->group_by('tbl_gambar_legit.legit_id');
		$this->db->group_by('tbl_validator.legit_id');
		// $this->db->limit(20);
		return $this->db->get($this->_table_name)->result();
		// echo $this->db->last_query(); die;
	}
}
