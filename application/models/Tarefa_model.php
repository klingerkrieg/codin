<?php

class Tarefa_model extends CI_Model {

        private $table = 'tarefas';

        public function inserir($data){
                //$this->output->enable_profiler(TRUE);

                
                $data['data_criado'] = Date('Y-m-d H:i:s');
                $data['entrega'] = date_br_to_mysql($data['data']).' '.$data['hora'];
                unset($data['data']);
                unset($data['hora']);
                $this->db->insert($this->table,$data);
                return $this->db->insert_id();

        }


}