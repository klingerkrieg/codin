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

        public function getArquivosByTarefa($idtarefa, $doProfessor = true){
                //trazer tambem se o aluno já entregou essa tarefa
                $this->db->select('caminho, idprofessor ');
                return $this->db->get_where($this->table,['idtarefa'=>$idtarefa,
                                                          'do_professor'=>$doProfessor])->result_array();;
        }

        public function excluirByTarefa($idtarefa){
                return $this->db->delete($this->table, ["idtarefa"=>$idtarefa]);
        }


}