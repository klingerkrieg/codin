<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tarefas extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if (_v($_SESSION,'user') == ''){
			Header('Location:login');
		}
	}

	public function index($idtarefa, $idaluno=null){


		
		$idpasta = $this->uri->segment(5);
		//anula a chave do aluno para exibir todos os alunos ao clicar na pasta de voltar
		if ($idpasta == ""){
			$idaluno = null;
		}


		$this->load->model('Tarefa_model');
		$this->load->model('Turma_model');
		$this->load->model('Arquivo_model');
		$this->load->model('Nota_model');

		$tarefa 	= $this->Tarefa_model->get($idtarefa);
		$turma  	= $this->Turma_model->get($tarefa['idturma']);

		$arquivos  	= $this->Arquivo_model->getByTarefa($tarefa['idtarefa']);

		$alunos 	= $this->Turma_model->getAlunos($tarefa['idturma'], $idaluno);
		$alunos 	= $this->Arquivo_model->getByAlunos($tarefa['idtarefa'],
														$alunos, 
														$idaluno,
														$idpasta);

		$alunos 	= $this->Nota_model->getByAlunos($idtarefa,$alunos);

		$back = -1;
		if (count($alunos) > 0 && count($alunos[0]['respostas']) > 0){
			$back = $alunos[0]['respostas'][0]['back'];
		}


		$this->twig->display('professores/tarefa', ['turma'=>$turma,
												'tarefa'=>$tarefa,
												"arquivos"=>$arquivos,
												"voltar"=>$back,
												"alunos"=>$alunos]);

	}

	public function salvar() {


		$this->load->model('Tarefa_model');
		$data = only($_POST,['idtarefa','titulo','data','hora','texto','idturma']);
		$data['idprofessor'] = $_SESSION['user']['idusuario'];
		$idtarefa = $this->Tarefa_model->salvar($data);

		$this->load->model('Arquivo_model');
		$this->Arquivo_model->uploadFiles($idtarefa,"arquivo",true);
		
		print $idtarefa;
	}

	public function salvarNota($idtarefa,$idaluno){
		#salva
		$this->load->model('Nota_model');
		$data = only($_POST,['nota']);
		$data['idtarefa'] = $idtarefa;
		$data['idaluno'] = $idaluno;
		$this->Nota_model->salvar($data);
		#confirma
		$nota = $this->Nota_model->get($idtarefa,$idaluno);
		
		if ($_POST['nota'] == $nota){
			print 'ok';
		}
	}

	public function get($idtarefa){
		$this->load->model('Tarefa_model');
		$tarefa =  $this->Tarefa_model->get($idtarefa);

		$this->load->model('Arquivo_model');
		$tarefa['arquivos'] = $this->Arquivo_model->getByTarefa($tarefa['idtarefa']);

		print json_encode($tarefa);
	}


	public function listar($idturma){

		$this->load->model('Tarefa_model');
		$tarefas =  $this->Tarefa_model->getByTurmaProfessor($idturma);

		$this->twig->display('professores/lista_tarefas', ['tarefas'=>$tarefas]);
	}

	public function excluir($idtarefa){
		$this->load->model('Tarefa_model');
		$this->load->model('Turma_model');

		$tarefa = $this->Tarefa_model->get($idtarefa);
		$turma = $this->Turma_model->get($tarefa['idturma']);

		$this->Tarefa_model->excluir($idtarefa);

		print $turma['chave'];

	}


	public function download($idtarefa, $idaluno){
		#cria o zip
		$this->load->model('Arquivo_model');
		$path = $this->Arquivo_model->makeDownload($idtarefa, $idaluno);
		$fileName = substr($path, strrpos($path,"/")+1);

		#faz o download do zip
		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename="'.basename($fileName).'"');
		header('Content-Length: ' . filesize($path));
		header("Content-Transfer-Encoding: binary");
		header("Pragma: no-cache"); 
		header("Expires: 0"); 
		ob_clean();
		flush();
		readfile($path);
		#deleta o zip
		unlink($path);
	}
}
