<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Correcoes extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if (_v($_SESSION,'user') == ''){
			Header('Location:login');
		}

	}


}
