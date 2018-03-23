<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if (_v($_SESSION,'idusuario') == ''){
			Header('Location:login');
		}

		
	}

	public function index() {

		if ($_SESSION['is_professor'] == true){
			$this->twig->display('./professores/home');
		} else {
			$this->twig->display('./alunos/home');
		}
		
	}
}
