<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Validator_model extends MY_Model
{

    protected $_table_name = 'tbl_validator';
	protected $_primary_key = 'id';
	protected $_order_by = 'id';
	protected $_order_by_type = 'desc';
	

	public function validator_data_checker($legit_id){
		$this->db->select('tbl_validator.check_result,tbl_validator.check_note,tbl_validator.validator_user_id,tbl_validator.final_time_check,tbl_user.nama,tbl_user.role');
		$this->db->join('tbl_user','tbl_user.id = tbl_validator.validator_user_id');
		$this->db->where('tbl_validator.legit_id',$legit_id);
		$this->db->where('tbl_user.role','validator');
		$this->db->where('tbl_validator.check_result !=','preview');
		$this->db->group_by('tbl_validator.validator_user_id');
		$this->db->order_by('tbl_validator.id','desc');
		return $this->db->get($this->_table_name)->result();
	}

	public function validator_data_single($legit_id){
		$this->db->select('tbl_validator.check_result,tbl_validator.check_note,tbl_validator.validator_user_id,tbl_validator.final_time_check,tbl_user.nama,tbl_user.role');
		$this->db->join('tbl_user','tbl_user.id = tbl_validator.validator_user_id');
		$this->db->where('tbl_validator.legit_id',$legit_id);
		$this->db->where('tbl_user.role','validator');
		$this->db->group_by('tbl_validator.validator_user_id');
		$this->db->order_by('tbl_validator.id','desc');
		return $this->db->get($this->_table_name)->row();
	}

	public function validator_sumary($validator_user_id,$tipe){
		return $this->db->query("SELECT COUNT(tbl_legit_check.id) as total, tbl_legit_check.legit_status,tbl_validator.check_result,tbl_legit_check_detail.kategori_id,tbl_user.validator_brand_id FROM tbl_legit_check INNER JOIN tbl_validator on tbl_legit_check.id = tbl_validator.legit_id INNER JOIN tbl_legit_check_detail on tbl_legit_check.id = tbl_legit_check_detail.legit_id INNER JOIN tbl_user ON tbl_validator.validator_user_id = tbl_user.id WHERE tbl_validator.validator_user_id = $validator_user_id AND tbl_validator.check_result ='".$tipe."' ")->result();
	}
}
