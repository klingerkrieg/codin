<?php

class Tarefa_model extends CI_Model {

        private $table = 'tarefas';

        public function salvar($data){
                if ($data['idtarefa'] != ""){
                        return $this->atualizar($data);
                } else {
                        return $this->inserir($data);
                }
        }

        public function atualizar($data){
                $data['data_atualizado'] = Date('Y-m-d H:i:s');

                if (isset($data['data']) && $data['data'] != ""){
                        $data['entrega'] = date_br_to_mysql($data['data']).' '.$data['hora'];
                }
                unset($data['data']);
                unset($data['hora']);
                $this->db->update($this->table,$data,['idtarefa'=>$data['idtarefa']]);
                return $data['idtarefa'];
        }

        public function inserir($data){
                //$this->output->enable_profiler(TRUE);
                $data['data_criado'] = Date('Y-m-d H:i:s');
                $data['entrega'] = date_br_to_mysql($data['data']).' '.$data['hora'];
                unset($data['data']);
                unset($data['hora']);
                $this->db->insert($this->table,$data);
                return $this->db->insert_id();

        }

        public function get($idtarefa){
                $this->db->select('idtarefa, idturma, nota_max, titulo, texto, entrega, idprofessor ');
                $arr = $this->db->get_where($this->table,['idtarefa'=>$idtarefa])->row_array();
                $parts = explode(' ',$arr['entrega']);
                if ($parts[0] != ""){
                        $arr['data'] = date_mysql_to_br($parts[0]);
                        $arr['hora'] = $parts[1];
                }
                return $arr;
        }

        
        public function getByTurmaProfessor($idturma){
                //trazer tambem se o aluno já entregou essa tarefa
                $sql = "select idtarefa, titulo, texto, entrega, idprofessor, idturma, "
                        ." (select count(*) from aluno_turma where idturma = tarefas.idturma) as qtd_alunos, "
                        ." (select count(distinct idusuario) from arquivos where idtarefa = tarefas.idtarefa and do_professor = 0) as qtd_concluidos "
                        ." from tarefas "
                        ."  where idturma = $idturma ";

                $tarefas = $this->db->query($sql)->result_array();
                $this->load->model('Arquivo_model');
                return $this->Arquivo_model->getByTarefas($tarefas);
        }

        public function getByTurmaAluno($idturma){
                $sql = "select idtarefa, titulo, texto, entrega, idprofessor, idturma, "
                        ." (select count(distinct idusuario) from arquivos "
                                ." where idtarefa = tarefas.idtarefa and "
                                ." do_professor = 0 and arquivos.idusuario = {$_SESSION['user']['idusuario']} ) as concluido "
                        ." from tarefas "
                        ." where idturma = $idturma ";

                $tarefas = $this->db->query($sql)->result_array();
                $this->load->model('Arquivo_model');
                return $this->Arquivo_model->getByTarefas($tarefas);
        }
        
        public function getByTurma($idturma){
                return $this->db->get_where($this->table, ["idturma"=>$idturma])->result_array();
        }

        public function excluir($idtarefa){
                $this->load->model('Arquivo_model');
		$this->Arquivo_model->excluirByTarefa($idtarefa);

                return $this->db->delete($this->table, ["idtarefa"=>$idtarefa]);
        }


}