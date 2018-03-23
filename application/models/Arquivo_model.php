<?php

class Arquivo_model extends CI_Model {

        private $table = 'arquivos';

        public function inserir($data){
                //$this->output->enable_profiler(TRUE);

                $data['data_criado'] = Date('Y-m-d H:i:s');
                unset($data['data']);
                unset($data['hora']);
                $this->db->insert($this->table,$data);
                return $this->db->insert_id();

        }

        public function getByAlunos($idtarefa, $alunos){
                $this->load->model('Arquivo_model');
                for($i = 0; $i < sizeof($alunos); $i++ ){
                        $alunos[$i]['respostas'] = $this->getByTarefa($idtarefa, false, $alunos[$i]['idusuario']);
                }
                return $alunos;
        }

        public function getByTarefas($tarefas){
                $this->load->model('Arquivo_model');
                for($i = 0; $i < sizeof($tarefas); $i++ ){
                        $tarefas[$i]['arquivos'] = $this->getByTarefa($tarefas[$i]['idtarefa']);
                }
                return $tarefas;
        }

        public function getByTarefa($idtarefa, $doProfessor = true, $idaluno = null){
                //trazer tambem se o aluno já entregou essa tarefa
                $this->db->select('caminho, idusuario ');
                $where = ['idtarefa'=>$idtarefa, 'do_professor'=>$doProfessor];
                #Caso tenha um aluno como filtro
                if ($idaluno != null){
                        $where['idusuario'] = $idaluno;
                }
                $arquivos = $this->db->get_where($this->table,$where)->result_array();;
                
                for ($y = 0; $y < sizeof($arquivos); $y++){
                        $arquivos[$y]['nome'] = getEnds($arquivos[$y]['caminho'],"/");
                        $arquivos[$y]['ext'] = getEnds($arquivos[$y]['caminho'],".");
                }
                return $arquivos;
        }

        public function excluirByTarefa($idtarefa){
                
		deleteFolder('./uploads/'.$idtarefa);
                return $this->db->delete($this->table, ["idtarefa"=>$idtarefa]);
        }


}