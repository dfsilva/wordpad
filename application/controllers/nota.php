<?php

class Nota extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}

	function index(){
	}

	function salvar(){
		header('Content-type: application/json');
		session_start();
		$data = array(
			'id'  => $this->input->get_post('id', TRUE),
            'titulo'   => $this->input->get_post('titulo', TRUE),
			'texto'   => $this->input->get_post('texto', TRUE),
			'id_usuario'   => $_SESSION['idUsuario']
		);
		$idNota = $this->MNota->incluirAlterar($data);
		
		print '{"success":true,"id":'.$idNota.'}';
	}
	
	function getNotasUsuario(){
		session_start();
		$retorno = $this->MNota->findByUsuario($_SESSION['idUsuario']);
		print json_encode($retorno);
	}
	
	function delete(){
		session_start();
		$retorno = $this->MNota->delete($this->input->get_post('id', TRUE), $_SESSION['idUsuario']);
		print '{"success":true}';
	}
}