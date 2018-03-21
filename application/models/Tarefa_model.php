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
                $data['entrega'] = date_br_to_mysql($data['data']).' '.$data['hora'];
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
                $this->db->select('idtarefa, idturma, titulo, texto, entrega, idprofessor ');
                $arr = $this->db->get_where($this->table,['idtarefa'=>$idtarefa])->row_array();
                $parts = explode(' ',$arr['entrega']);
                $arr['data'] = date_mysql_to_br($parts[0]);
                $arr['hora'] = $parts[1];
                return $arr;
        }

        public function getTarefasByTurma($idturma){
                //trazer tambem se o aluno já entregou essa tarefa
                $sql = "select idtarefa, titulo, texto, entrega, idprofessor, idturma, "
                        ." (select count(*) from aluno_turma where idturma = tarefas.idturma) as qtd_alunos, "
                        ." (select distinct count(idaluno) from arquivos where idtarefa = tarefas.idtarefa and do_professor = 0) as qtd_concluidos "
                        ." from tarefas "
                        ."  where idturma = $idturma ";

                return $this->db->query($sql)->result_array();
        }

        public function excluir($idtarefa){
                return $this->db->delete($this->table, ["idtarefa"=>$idtarefa]);
        }


}