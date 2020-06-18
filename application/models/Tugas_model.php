<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tugas_model extends CI_Model{

    public function getDataTugas($id)
    {
        $this->datatables->select('a.id_tugas, a.token, a.nama_tugas, b.nama_matkul, a.file_tugas, a.tanggal_mulai, a.terlambat');
        $this->datatables->from('m_tugas a');
        $this->datatables->join('matkul b', 'a.matkul_id = b.id_matkul');
        if($id!==null){
            $this->datatables->where('dosen_id', $id);
        }
        return $this->datatables->generate();
    }

    public function getIdMahasiswa($nim){
        $this->db->select('*');
        $this->db->from('mahasiswa a');
        $this->db->join('kelas b', 'a.kelas_id=b.id_kelas');
        $this->db->join('jurusan c', 'b.jurusan_id=c.id_jurusan');
        $this->db->where('nim', $nim);
        return $this->db->get()->row();
    }

    public function getIdDosen($nip)
    {
        $this->db->select('id_dosen, nama_dosen')->from('dosen')->where('nip', $nip);
        return $this->db->get()->row();
    }

    public function getMatkulDosen($nip)
    {
        $this->db->select('matkul_id, nama_matkul, id_dosen, nama_dosen');
        $this->db->join('matkul', 'matkul_id=id_matkul');
        $this->db->from('dosen')->where('nip', $nip);
        return $this->db->get()->row();
    }

    public function getTugasById($id)
    {
        $this->db->select('*');
        $this->db->from('m_tugas a');
        $this->db->join('dosen b', 'a.dosen_id=b.id_dosen');
        $this->db->join('matkul c', 'a.matkul_id=c.id_matkul');
        $this->db->where('id_tugas', $id);
        return $this->db->get()->row();
    }
}
