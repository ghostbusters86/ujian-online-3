<?php

class Soal_essay_model extends CI_Model{
    public function getDataSoal($id, $dosen)
    {
        $this->datatables->select("a.id_soal_essay, a.soal_essay, date_format(created_at, '%d-%m-%Y %H:%i') as created_at, date_format(updated_at, '%d-%m-%Y %H:%i') as updated_at, b.nama_matkul, c.nama_dosen");
        $this->datatables->from('tb_soal_essay a');
        $this->datatables->join('matkul b', 'b.id_matkul=a.id_matkul');
        $this->datatables->join('dosen c', 'c.id_dosen=a.id_dosen');
        if ($id!==null && $dosen===null) {
            $this->datatables->where('a.id_matkul', $id);            
        }else if($id!==null && $dosen!==null){
            $this->datatables->where('a.id_dosen', $dosen);
        }
        return $this->datatables->generate();
    }

    public function getSoalById($id)
    {
        return $this->db->get_where('tb_soal_essay', ['id_soal_essay' => $id])->row();
    }

    public function getMatkulDosen($nip)
    {
        $this->db->select('matkul_id, nama_matkul, id_dosen, nama_dosen');
        $this->db->join('matkul', 'matkul_id=id_matkul');
        $this->db->from('dosen')->where('nip', $nip);
        return $this->db->get()->row();
    }

    public function getAllDosen()
    {
        $this->db->select('*');
        $this->db->from('dosen a');
        $this->db->join('matkul b', 'a.matkul_id=b.id_matkul');
        return $this->db->get()->result();
    }
}