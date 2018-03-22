<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	
	public function index() {

		$dados = array(
			'texto' => 'Esse é um Template Twig'
		);
		$this->twig->display('login', $dados);
	}

	public function logar(){
		$this->load->model('Usuario_model');
		$data = $this->Usuario_model->get(only($_POST,['email','senha']));

		if ($data->num_rows() > 0){
			$_SESSION = $data->row_array();
			Header('Location:'.base_url()."home");
		} else {
			Header('Location:'.base_url()."login?f=1");
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
}
