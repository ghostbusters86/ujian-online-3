<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tugas_model extends CI_Model{

    public function getDataTugas($id)
    {
        $this->datatables->select('a.id_tugas, a.token, a.nama_tugas, a.deskripsi_tugas, b.nama_matkul, a.file_tugas, a.tanggal_mulai, a.terlambat');
        $this->datatables->from('m_tugas a');
        $this->datatables->join('matkul b', 'a.matkul_id = b.id_matkul');
        if($id!==null){
            $this->datatables->where('dosen_id', $id);
        }
        return $this->datatables->generate();
    }

    public function getListTugas($id, $kelas)
    {
        $this->datatables->select("a.id_tugas, e.nama_dosen, d.nama_kelas, a.nama_tugas,  b.nama_matkul, (SELECT COUNT(id) FROM h_tugas h WHERE h.id_mahasiswa = {$id} AND h.id_tugas = a.id_tugas) AS ada, date_format(a.tanggal_mulai,'%m-%d-%Y %H:%i') as tanggal_mulai, date_format(a.terlambat,'%m-%d-%Y %H:%i') as terlambat");
        $this->datatables->from('m_tugas a');
        $this->datatables->join('matkul b', 'a.matkul_id = b.id_matkul');
        $this->datatables->join('kelas_dosen c', "a.dosen_id = c.dosen_id");
        $this->datatables->join('kelas d', 'c.kelas_id = d.id_kelas');
        $this->datatables->join('dosen e', 'e.id_dosen = c.dosen_id');
        $this->datatables->where('d.id_kelas', $kelas);
        $this->datatables->add_column('action', '<a href="javascript:void(0);" class="detailTugas btn btn-info btn-xs" data-kode="$1"><i class="fa fa-search-plus"></i> Detail </a> <a href="javascript:void(0);" class="uploadTugas btn btn-success btn-xs" data-kode="$1"><i class="fa fa-upload"></i> Upload</a>','id_tugas');
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

    public function cekWaktu($id){
        $this->db->select("date_format(tanggal_mulai,'%d-%m-%Y %H:%i:%s') as tanggal_mulai, date_format(terlambat,'%d-%m-%Y %H:%i:%s') as terlambat");
        $this->db->where('id_tugas', $id);
        return $this->db->get('m_tugas')->row();
    }

    public function cekUpload($id, $mahasiswa){
        $this->db->where('id_tugas', $id);
        $this->db->where('id_mahasiswa', $mahasiswa);
        return $this->db->get('h_tugas');
    }

    function add_tugas($id_tugas, $nim, $new_name, $tanggal){
        $data = array(
            'id_tugas' => $id_tugas,
            'id_mahasiswa' => $nim,
            'tugas_mahasiswa' => $new_name,
            'waktu' => $tanggal
        );
        return $this->db->insert('h_tugas', $data);
    }

    function rowdata($id){
        $this->db->where('id', $id);
        return $this->db->get('h_tugas');
    }

    function update_tugas($id, $tugas, $tanggal){
        $this->db->set('waktu', $tanggal);
        $this->db->set('updated_at', $tanggal);
        $this->db->set('tugas_mahasiswa', $tugas);
        $this->db->where('id', $id);
        $this->db->update('h_tugas');
    }
}
