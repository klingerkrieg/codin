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

		$i = 5;
		$path = "";
		$back = "";
		while ($this->uri->segment($i) != false){
			$back = $path;
			$path .= "/".$this->uri->segment($i);
			$i++;
		}
		$back = "/$idaluno" . $back;
		if (substr_count($back,"/") < 2){
			$back = "";
		} else
		if (substr_count($back,"/") == 2){
			$back = "/";
		}
		
		
		#procura os arquivos do aluno
		$nivel = max([substr_count($path,"/") - 1, 0]);


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
														$nivel, $path);
		$alunos 	= $this->Nota_model->getByAlunos($idtarefa,$alunos);


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
		
		$path = saveUploadFile($idtarefa, "arquivo", true);
		if ($path != false){
			$this->load->model('Arquivo_model');
			$data = ['idtarefa'=>$idtarefa, 'do_professor'=>true,
					'caminho'=>$path, 'idusuario'=>$_SESSION['user']['idusuario']];
			$this->Arquivo_model->inserir($data);
		}

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
}
