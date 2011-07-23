<?php

class Usuario extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}

	function index(){
	}

	function cadastrarUsuario(){
		header('Content-type: application/json');
		$senhaLimpa = $this->input->get_post('senha', TRUE);
		$data = array(
			'nome'  => $this->input->get_post('nome', TRUE),
            'email'   => $this->input->get_post('email', TRUE),
			'senha'   => md5($senhaLimpa)
		);
		$usuario = $this->MUsuario->incluirAlterar($data);
		$data['senha'] = $senhaLimpa;
		$this->enviarEmailBemVindo($data);
		print '{"success":true}';
	}


	/**
	 * Utilizado na validacao do email do usuario
	 */
	function validaremail(){
		header('Content-type: application/json');
		$email = $this->input->get_post('fieldValue', TRUE);

		if(isset($email)){
			$usuario = $this->MUsuario->findByEmail($email);
			if($usuario && isset($usuario['id'])){
				print '["email",false,"Email invalido"]';
			}else{
				print '["email",true,"Email valido"]';
			}
		}else{
			print '["email",false,"Email invalido"]';
		}
	}

	/**
	 * Efetua login do usuario, retornando json
	 * para a tela.
	 */
	function login(){
		session_start();
		header('Content-type: application/json');

		$email = $this->input->get_post('email', TRUE);
		$senha = $this->input->get_post('senha', TRUE);
		$manter = $this->input->get_post('manterConectado', TRUE);

		$usuario =  $this->MUsuario->validarUsuario($email, $senha);

		if ($usuario && isset($usuario['id'])){
			setUserSession($usuario);
			if($manter){
				setCookieUser($usuario);
			}
			$data['success'] = true;
			print json_encode($data);
		}else{
			$_SESSION['idUsuario'] = -1;
			$data['success'] = false;
			print json_encode($data);
		}
	}

	/**
	 *
	 * Retorna os dados do usuário logado.
	 */
	function getDadosUsuarioJson(){
		session_start();
		header('Content-type: application/json');
		$usuario =  array(
			'nome'  => "",
            'email'   => ""
            );

            if(isUserAutenticate()){
            	$usuario =  $this->MUsuario->findById($_SESSION['idUsuario']);
            }
            print json_encode($usuario);
	}

	function isUserLogged(){
		session_start();
		header('Content-type: application/json');
		$data['success'] = false;
		if(isUserAutenticate()){
			$data['success'] = true;
		}
		print json_encode($data);
	}

	function logout(){
		cleanCookieAndSession();
		$data['success'] = true;
		print json_encode($data);

	}

	function enviarEmailBemVindo($usuario){
		$this->email->from('wordpad@diegosilva.com.br', 'WordPad - Online');
		$this->email->to($usuario['email']);
		$this->email->bcc('diego@diegosilva.com.br');

		$this->email->subject('Bem vindo ao sistema WordPad - Online');
		$this->email->message('
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
       		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
				<head>
				</head>
				<body>
					Ol&aacute; '.$usuario['nome'].', segue abaixo seus dados para acesso ao sistema WordPad - Online.
				<p>Usu&aacute;rio: '.$usuario['email'].'</p>
				<p>Senha: '.$usuario['senha'].'</p>
				</body>
			</html>
		');	
		$this->email->send();
	}
}