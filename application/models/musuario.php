<?php

class MUsuario extends CI_Model{

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Inclui ou altera um usuario.
	 * @param unknown_type $data
	 */
	function incluirAlterar($data){
		if (isset($data['id']) && $data[id] > 0){
			$this->db->set($data);
			$this->db->where('id', $data['id']);
			$this->db->update('Usuario');
			$id = $data['id'];
		}else{
			$this->db->insert('Usuario', $data);
			$id =   $this->db->insert_id();
		}
		$this->db->where('id', $id);
		$Q = $this->db->get('Usuario');
		return $Q->row_array();
	}


	function findByEmail($email){
		$this->db->where('email', $email);
		$Q = $this->db->get('Usuario');
		return $Q->row_array();
	}

	function validarUsuario($email, $senha){
		$this->db->where('email',$email);
		$this->db->where('senha', md5($senha));
		$this->db->limit(1);
		$Q = $this->db->get('Usuario');
		return $Q->row_array();
	}
	
	function findById($id){
		$this->db->where('id', $id);
		$Q = $this->db->get('Usuario');
		return $Q->row_array();
	}
}
?>