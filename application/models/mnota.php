<?php

class MNota extends CI_Model{

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Inclui ou altera uma nota
	 * @param unknown_type $data
	 */
	function incluirAlterar($nota){
		if (isset($nota['id']) && $nota['id'] > 0){
			$this->db->set($nota);
			$this->db->where('id', $nota['id']);
			$this->db->where('id_usuario', $nota['id_usuario']);
			$this->db->update('Nota');
			return $nota['id'];
		}else{
			$this->db->insert('Nota', $nota);
			$idNota = $this->db->insert_id();
			return $idNota;
		}
	}

	function findByUsuario($idUsuario){
		$data = array();

		$this->db->where('id_usuario = ', $idUsuario);
		$this->db->order_by("id", "asc");
		$Q = $this->db->get('Nota');

		if ($Q->num_rows() > 0){
			foreach ($Q->result_array() as $row){
				$data[] = $row;
			}
		}

		$Q->free_result();
		return $data;
	}

	function delete($id, $idUsuario){
		$this->db->where('id', $id);
		$this->db->where('id_usuario', $idUsuario);
		$this->db->delete('Nota');
	}
}
?>