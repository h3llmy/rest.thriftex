<?php 
defined('BASEPATH') OR exit('No direct script access allowed');


class MY_Model extends CI_Model{
	protected $_table_name;
	protected $_order_by;
	protected $_order_by_type;
	protected $_primary_filter = 'intval';
	protected $_primary_key;
	protected $_type;
	public $rules;

	public function __construct()
	{
		parent::__construct();
	}


	public function insert($data,$batch=FALSE){
		if($batch == TRUE){
			$this->db->insert_batch($this->_table_name,$data);
		}else{
			$this->db->set($data);
			$this->db->insert($this->_table_name);
			$id = $this->db->insert_id();
			return $id;
		}
	}

	public function update($data,$where=array()){
		$this->db->set($data);
		$this->db->where($where);
		return $this->db->update($this->_table_name);
	}

	public function get($id=NULL, $single=FALSE){
		if($id != NULL){
			$filter = $this->_primary_filter;
			$id = $filter($id);
			$this->db->where($this->_primary_key,$id);
			$method = 'row';
		}elseif($single == TRUE){
			$method = 'row';
		}else{
			$method = 'result';
		}

		if($this->_order_by_type){
			$this->db->order_by($this->_order_by,$this->_order_by_type);
		}else{
			$this->db->order_by($this->_order_by);
		}
		return $this->db->get($this->_table_name)->$method();
	}

	public function get_by($where = null,$limit = null,$offset = null,$single = false,$select = null){
		if($select != null){
			$this->db->select($select);
		}

		if($where){
			$this->db->where($where);
		}
		if(($limit) && ($offset)){
			$this->db->limit($limit,$offset);
		}else if($limit){
			$this->db->limit($limit);
		}

		return $this->get(NULL,$single);
	}


	public function delete($id){
		$filter = $this->_primary_filter;
		$id = $filter($id);

		if(!$id){
			return FALSE;
		}

		$this->db->where($this->_primary_key,$id);
		$this->db->limit(1);
		return $this->db->delete($this->_table_name);
	}

	public function delete_by($where = NULL){
		if($where){
			$this->db->where($where);
		}
		return $this->db->delete($this->_table_name);
	}

	public function count($where = NULL){
		if(!empty($this->_type)){
			$where['post_type'] = $this->_type;
		}

		if($where){
			$this->db->where($where);
		}
		$this->db->from($this->_table_name);
		return $this->db->count_all_results();
	}
	
	public function generateCode($kodeunique,$kode_field,$tipe=null){
		$this->db->select('RIGHT({PRE}'.$this->_table_name.'.'.$kode_field.',7) as kode_barang', FALSE);
		if(!empty($tipe)){
			$this->db->where($tipe);
		}
		$this->db->order_by('kode_barang','DESC');
		$this->db->limit(1);
		$query = $this->db->get($this->_table_name);
			if($query->num_rows() <> 0){      
				$data = $query->row();
				$kode = intval($data->kode_barang) + 1; 
			}
			else{      
				$kode = 1;  
			}
		$batas = str_pad($kode, 7, "0", STR_PAD_LEFT);
		$kodetampil = $kodeunique.date('y').date('m').date('d').$batas;
		return $kodetampil;  
	}

	public function generateCodeSupplier($kodeunique,$kode_field){
		$this->db->select('RIGHT({PRE}'.$this->_table_name.'.'.$kode_field.',7) as kode_service', FALSE);
		$this->db->order_by('kode_service','DESC');
		$this->db->limit(1);
		$query = $this->db->get($this->_table_name);
			if($query->num_rows() <> 0){
				$data = $query->row();
				$kode = intval($data->kode_service) + 1;
			}
			else{
				$kode = 1;
			}
		$batas = str_pad($kode, 7, "0", STR_PAD_LEFT);
		$kodetampil = $kodeunique.$batas;
		return $kodetampil;
	}

	

}
