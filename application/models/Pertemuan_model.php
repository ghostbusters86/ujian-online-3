<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pertemuan_model extends CI_Model{

    public function getDataPertemuan($id)
    {
        $this->datatables->select('a.id_pertemuan, a.token, a.nama_pertemuan, a.materi, b.nama_matkul, a.file_materi, a.tanggal_mulai, a.tanggal_selesai, c.nama_kelas');
        $this->datatables->from('m_pertemuan a');
        $this->datatables->join('matkul b', 'a.id_matkul = b.id_matkul');
        $this->datatables->join('kelas c', 'a.id_kelas = c.id_kelas');
        if($id!==null){
            $this->datatables->where('id_dosen', $id);
        }
        $this->datatables->add_column('download', '<a href="'.base_url().'pertemuan/downloadMateri/$1">$1</a>','file_materi');
        return $this->datatables->generate();
    }

    public function getListPertemuan($id, $kelas)
    {
        $this->datatables->select("a.id_pertemuan, e.nama_dosen, d.nama_kelas, a.nama_pertemuan, a.materi, a.file_materi,  b.nama_matkul, a.tanggal_mulai, a.tanggal_selesai, (SELECT COUNT(id_absensi) FROM h_absensi h WHERE h.id_mahasiswa = {$id} AND h.id_pertemuan = a.id_pertemuan) AS status, 
        (SELECT keterangan FROM h_absensi h WHERE h.id_mahasiswa = {$id} AND h.id_pertemuan = a.id_pertemuan) AS keterangan, 
        date_format(a.tanggal_mulai,'%m-%d-%Y %H:%i') as tanggal_mulai, date_format(a.tanggal_selesai,'%m-%d-%Y %H:%i') as tanggal_selesai");
        $this->datatables->from('m_pertemuan a');
        $this->datatables->join('matkul b', 'a.id_matkul = b.id_matkul');
        $this->datatables->join('kelas d', 'a.id_kelas = d.id_kelas');
        $this->datatables->join('dosen e', 'e.id_dosen = a.id_dosen');
        $this->datatables->where('a.id_kelas', $kelas);
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

    function cekwaktu($id){
        $this->db->where('id_pertemuan', $id);
        return $this->db->get('m_pertemuan');
    }

    function simpan_absensi($id, $mahasiswa, $image_name, $jamsekarang, $hadir){
        $query = $this->db->query("
            insert into h_absensi (id_pertemuan, id_mahasiswa, ttd_digital, waktu, keterangan) values ('$id', '$mahasiswa', '$image_name', '$jamsekarang', '$hadir')
        ");
        return $query;
    }

    public function getRekapAbsensi($nip = null)
    {
        $this->datatables->select('b.id_pertemuan, b.nama_pertemuan, b.materi, b.tanggal_mulai, b.tanggal_selesai, e.nama_kelas');
        $this->datatables->select('c.nama_matkul, d.nama_dosen');
        $this->datatables->from('m_pertemuan b');
        $this->datatables->join('h_absensi a', 'a.id_pertemuan = b.id_pertemuan' , 'left');
        $this->datatables->join('matkul c', 'b.id_matkul = c.id_matkul');
        $this->datatables->join('dosen d', 'b.id_dosen = d.id_dosen');
        $this->datatables->join('kelas e', 'b.id_kelas = e.id_kelas');
        $this->datatables->group_by('b.id_pertemuan');
        if($nip !== null){
            $this->datatables->where('d.nip', $nip);
        }
        return $this->datatables->generate();
    }

    function get_all_kelas(){
        $query = $this->db->query("
            select * from kelas order by nama_kelas asc
        ");
        return $query;
    }

    function detail_absensi($kelas, $id_pertemuan){
        $query = $this->db->query("
        select a.*, (select waktu  from h_absensi b  WHERE  b.id_mahasiswa = a.id_mahasiswa and b.id_pertemuan = '$id_pertemuan') as waktu, (select keterangan  from h_absensi b  WHERE  b.id_mahasiswa = a.id_mahasiswa and b.id_pertemuan = '$id_pertemuan') as keterangan  from mahasiswa a
        where a.kelas_id = '$kelas'
        ");
        return $query;
    }

    function get_kelas($id){
        $query = $this->db->query("
            select a.*, b.* from m_pertemuan a join kelas b on a.id_kelas = b.id_kelas where a.id_pertemuan = '$id'
        ");
        return $query;  
    }

    function insert_batch($data){
        return $this->db->insert_batch('h_absensi', $data);
    }

}
