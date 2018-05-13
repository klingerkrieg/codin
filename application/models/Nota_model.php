<?php

class Nota_model extends CI_Model {

        private $table = 'notas';

        public function salvar($data){
                //Caso a nota venha em branco ele deleta
                if ($data['nota'] == ""){
                        $where = only($data,['idtarefa','idaluno']);
                        $this->db->delete($this->table,$where);
                } else
                if ($this->get($data['idtarefa'],$data['idaluno']) == ""){
                        $data['data_criado'] = Date('Y-m-d H:i:s');
                        $this->db->insert($this->table,$data);
                } else {
                        #$this->output->enable_profiler(TRUE);
                        $data['data_atualizado'] = Date('Y-m-d H:i:s');
                        $where = $data;
                        unset($where['nota']);
                        unset($where['data_atualizado']);
                        
                        $this->db->update($this->table,$data,$where);
                }
        }

        public function get($idtarefa, $idaluno){
                $this->db->select('nota');
                $arr = $this->db->get_where($this->table,['idtarefa'=>$idtarefa,
                                                        'idaluno'=>$idaluno])->row_array();
                if ($arr != ""){
                        return $arr['nota'];
                } else {
                        return "";
                }
        }

        public function getByAlunos($idtarefa, $alunos){
                foreach($alunos as $key=>$aluno){
                        $alunos[$key]["nota"] = $this->get($idtarefa, $aluno["idusuario"]);
                }
                return $alunos;
        }

}