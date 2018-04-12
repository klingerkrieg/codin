<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Perfil extends CI_Controller {

	
	public function index() {
		$this->twig->display('perfil');
	}


	public function salvar() {

		$path = saveUploadFile("uploads/perfil/{$_SESSION['user']['idusuario']}",'foto');
		if ($path != false){
			miniImage($path, 200, 200);
		}
		
		$this->load->model('Usuario_model');
		$data = only($_POST,['nome','email','senha']);
		if ($path != false){
			$data['foto'] = $path;
		}
		$this->Usuario_model->salvar($data);

		Header('Location:'.base_url()."perfil");
	}
}
