<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ujian_essay_model extends CI_Model {

    public function getDataUjian($id)
    {
        $this->datatables->select('a.id_ujian_essay, a.token, a.nama_ujian_essay, b.nama_matkul, a.jumlah_soal, CONCAT(a.tanggal_mulai, " <br/> (", a.waktu, " Menit)") as waktu, a.jenis');
        $this->datatables->from('m_ujian_essay a');
        $this->datatables->join('matkul b', 'a.id_matkul = b.id_matkul');
        if($id!==null){
            $this->datatables->where('id_dosen', $id);
        }
        return $this->datatables->generate();
    }

    public function getIdMahasiswa($nim)
    {
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

    public function getJumlahSoal($dosen)
    {
        $this->db->select('COUNT(id_soal_essay) as jml_soal');
        $this->db->from('tb_soal_essay');
        $this->db->where('id_dosen', $dosen);
        return $this->db->get()->row();
    }

    public function getUjianById($id)
    {
        $this->db->select('*');
        $this->db->from('m_ujian_essay a');
        $this->db->join('dosen b', 'a.id_dosen=b.id_dosen');
        $this->db->join('matkul c', 'a.id_matkul=c.id_matkul');
        $this->db->where('id_ujian_essay', $id);
        return $this->db->get()->row();
    }
}