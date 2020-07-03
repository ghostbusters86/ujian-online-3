<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pertemuan_model extends CI_Model{

    public function getDataPertemuan($id)
    {
        $this->datatables->select('a.id_pertemuan, a.token, a.nama_pertemuan, a.materi, b.nama_matkul, a.file_materi, a.tanggal_mulai, a.tanggal_selesai');
        $this->datatables->from('m_pertemuan a');
        $this->datatables->join('matkul b', 'a.id_matkul = b.id_matkul');
        if($id!==null){
            $this->datatables->where('id_dosen', $id);
        }
        $this->datatables->add_column('download', '<a href="'.base_url().'pertemuan/downloadMateri/$1">$1</a>','file_materi');
        return $this->datatables->generate();
    }

    public function getListPertemuan($id, $kelas)
    {
        $this->datatables->select("a.id_pertemuan, e.nama_dosen, d.nama_kelas, a.nama_pertemuan, a.materi, a.file_materi,  b.nama_matkul, a.tanggal_mulai, a.tanggal_selesai, (SELECT COUNT(id) FROM h_absensi h WHERE h.id_mahasiswa = {$id} AND h.id_pertemuan = a.id_pertemuan) AS status, 
        (SELECT keterangan FROM h_absensi h WHERE h.id_mahasiswa = {$id} AND h.id_pertemuan = a.id_pertemuan) AS keterangan, 
        date_format(a.tanggal_mulai,'%m-%d-%Y %H:%i') as tanggal_mulai, date_format(a.tanggal_selesai,'%m-%d-%Y %H:%i') as tanggal_selesai");
        $this->datatables->from('m_pertemuan a');
        $this->datatables->join('matkul b', 'a.id_matkul = b.id_matkul');
        $this->datatables->join('kelas_dosen c', "a.id_dosen = c.dosen_id");
        $this->datatables->join('kelas d', 'c.kelas_id = d.id_kelas');
        $this->datatables->join('dosen e', 'e.id_dosen = c.dosen_id');
        $this->datatables->where('d.id_kelas', $kelas);
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

    public function getPertemuanById($id)
    {
        $this->db->select('*');
        $this->db->from('m_pertemuan a');
        $this->db->join('dosen b', 'a.id_dosen=b.id_dosen');
        $this->db->join('matkul c', 'a.id_matkul=c.id_matkul');
        $this->db->where('id_pertemuan', $id);
        return $this->db->get()->row();
    }

}
