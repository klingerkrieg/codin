<?php

class Correcoes_model extends CI_Model {

        private $table = 'correcoes';

        public function salvar($data){
                $data['idprofessor'] = $_SESSION['user']['idusuario'];
                if ($data['idcorrecao'] != ""){
                        return $this->atualizar($data);
                } else {
                        return $this->inserir($data);
                }
        }

        public function atualizar($data){
                $data['data_atualizado'] = Date('Y-m-d H:i:s');
                $this->db->update($this->table,$data,['idcorrecao'=>$data['idcorrecao']]);
                return $data['idcorrecao'];
        }

        public function inserir($data){
                //$this->output->enable_profiler(TRUE);

                $data['data_criado'] = Date('Y-m-d H:i:s');
                $this->db->insert($this->table,$data);
                return $this->db->insert_id();

        }

        public function getByArquivo($idarquivo){
                return $this->db->get_where($this->table,['idarquivo'=>$idarquivo])->result_array();
        }

        public function get($idcorrecao){
                return $this->db->get_where($this->table,['idcorrecao'=>$idcorrecao])->row_array();
        }

        public function excluir($data){

                return $this->db->delete($this->table, ["idcorrecao"=>$data['idcorrecao'],
                                                        "idprofessor"=>$_SESSION['user']['idusuario']]);
        }


}