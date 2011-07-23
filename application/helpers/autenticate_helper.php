<?php

function setUserSession($usuario){
	$_SESSION['idUsuario'] = $usuario['id'];
	$_SESSION['nomeUsuario'] = $usuario['nome'];
	$_SESSION['senhaUsuario'] = $usuario['senha'];
	$_SESSION['emailUsuario'] = $usuario['email'];
}

function setCookieUser($usuario){
	setcookie("idUsuario",$usuario['id'], time() + 36000000);
	setCookie("nomeUsuario", $usuario['nome'], time() + 36000000);
	setCookie("senhaUsuario", $usuario['senha'], time() + 36000000);
	setCookie("emailUsuario", $usuario['email'], time() + 36000000);
}

function isUserAutenticate(){
	ob_start();
	if (!isset($_SESSION['idUsuario'])){
		$_SESSION['idUsuario'] = -1;
	}

	if(isset($_COOKIE["idUsuario"])){
		$_SESSION['idUsuario'] = $_COOKIE["idUsuario"];
	}

	if($_SESSION['idUsuario'] < 1){
		return false;
	}else{
		return true;
	}
}

function cleanCookieAndSession(){
	session_start();
	setcookie("idUsuario","", time() - 36000000);
	setCookie("nomeUsuario", "", time() - 36000000);
	setCookie("senhaUsuario", "", time() - 36000000);
	setCookie("emailUsuario", "", time() - 36000000);
	session_destroy();
}

?>