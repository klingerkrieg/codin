<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	
	public function index() {

		$this->twig->display('login');
	}

	public function logar(){
		$this->load->model('Usuario_model');
		$data = $this->Usuario_model->login(only($_POST,['email','senha']));

		if ($data != false){
			$_SESSION['user'] = $data;
			Header('Location:'.base_url()."home");
		} else {
			Header('Location:'.base_url()."login?f=1");
		}
	}

	public function recuperar(){
		$this->twig->display('recuperar');
	}

	public function token($token){
		$this->load->model('Usuario_model');
		$data = $this->Usuario_model->recuperarByToken($token);

		if ($data != false){
			$_SESSION['novaSenha'] = $data;
			$this->twig->display('novaSenha');
		} else {
			$_SESSION['user']['errors'] = "Token inválido.";
			Header('Location:'.base_url()."login/");
		}

	}

	public function enviarEmail(){
		$this->load->model('Usuario_model');
		$data = $this->Usuario_model->recuperar(only($_POST,['email']));

		print_r($data);
		die();
		if ($data != false){
			$this->twig->display('emailEnviado', ["email"=>$data['email']]);
		} else {
			Header('Location:'.base_url()."login/recuperar");
		}
	}

	public function logout(){
		session_destroy();
		Header('Location:'.base_url()."login");
	}

	public function cadastrar() {
		
		$this->twig->display('cadastrar');
	}

	public function salvar() {
		$this->load->model('Usuario_model');
		$this->Usuario_model->inserir(only($_POST,['nome','email','senha']));

		Header('Location:'.base_url()."login");
	}

	public function novaSenha() {
		$this->load->model('Usuario_model');
		$this->Usuario_model->salvar(only($_POST,['senha']));
		
		Header('Location:'.base_url()."login");
	}
}
