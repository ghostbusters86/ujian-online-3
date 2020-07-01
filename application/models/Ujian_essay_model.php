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

    public function getListUjian($id, $kelas)
    {
        $this->datatables->select("a.id_ujian_essay, e.nama_dosen, d.nama_kelas, a.nama_ujian_essay, b.nama_matkul, a.jumlah_soal, CONCAT(a.tanggal_mulai, ' <br/> (', a.waktu, ' Menit)') as waktu,  (SELECT COUNT(id) FROM h_ujian_essay h WHERE h.id_mahasiswa = {$id} AND h.id_ujian_essay = a.id_ujian_essay) AS ada, (SELECT status_penilaian FROM h_ujian_essay h WHERE h.id_mahasiswa = {$id} AND h.id_ujian_essay = a.id_ujian_essay) AS status_penilaian");
        $this->datatables->from('m_ujian_essay a');
        $this->datatables->join('matkul b', 'a.id_matkul = b.id_matkul');
        $this->datatables->join('kelas_dosen c', "a.id_dosen = c.dosen_id");
        $this->datatables->join('kelas d', 'c.kelas_id = d.id_kelas');
        $this->datatables->join('dosen e', 'e.id_dosen = c.dosen_id');
        $this->datatables->where('d.id_kelas', $kelas);
        return $this->datatables->generate();
    }

    public function getSoal($id)
    {
        $ujian = $this->getUjianById($id);
        $order = $ujian->jenis==="acak" ? 'rand()' : 'id_soal_essay';

        $this->db->select('id_soal_essay, soal_essay, file, tipe_file');
        $this->db->from('tb_soal_essay');
        $this->db->where('id_dosen', $ujian->id_dosen);
        $this->db->where('id_matkul', $ujian->id_matkul);
        $this->db->order_by($order);
        $this->db->limit($ujian->jumlah_soal);
        return $this->db->get()->result();
    }

    public function HslUjianById($id, $dt=false)
    {
        if($dt===false){
            $db = "db";
            $get = "get";
        }else{
            $db = "datatables";
            $get = "generate";
        }
        
        $this->$db->select('d.id, a.nama, b.nama_kelas, c.nama_jurusan,  d.nilai');
        $this->$db->from('mahasiswa a');
        $this->$db->join('kelas b', 'a.kelas_id=b.id_kelas');
        $this->$db->join('jurusan c', 'b.jurusan_id=c.id_jurusan');
        $this->$db->join('h_ujian_essay d', 'a.id_mahasiswa=d.id_mahasiswa');
        $this->$db->where(['d.id_ujian' => $id]);
        return $this->$db->$get();
    }

    public function HslUjian($id, $mhs)
    {
        $this->db->select('*, UNIX_TIMESTAMP(tgl_selesai) as waktu_habis');
        $this->db->from('h_ujian_essay');
        $this->db->where('id_ujian_essay', $id);
        $this->db->where('id_mahasiswa', $mhs);
        return $this->db->get();
    }

    public function detail_ujian($id)
    {
        $this->db->select('*');
        $this->db->from('h_ujian_essay');
        $this->db->where('id', $id);
        return $this->db->get();
    }

    public function ambilSoal($pc_urut_soal_arr)
    {
        $this->db->select("*");
        $this->db->from('tb_soal_essay');
        $this->db->where('id_soal_essay', $pc_urut_soal_arr);
        return $this->db->get()->row();
    }

    function update_jawaban($id_tes, $_tidsoal, $jawaban_){
        $this->db->where('id', $id_tes);
        $this->db->where('id_soal_essay', $_tidsoal);
        $this->db->set('jawaban_essay', $jawaban_);
        return $this->db->update('detail_h_ujian_essay');
    }

    function jawab($id){
        $this->datatables->select('a.id, c.nama_ujian_essay, b.nim, b.nama, a.tgl_mulai, a.tgl_selesai, a.nilai, a.status_penilaian');
        $this->datatables->from('h_ujian_essay a');
        $this->datatables->join('mahasiswa b', 'a.id_mahasiswa = b.id_mahasiswa');
        $this->datatables->join('m_ujian_essay c', 'c.id_ujian_essay = a.id_ujian_essay');
        $this->datatables->where('a.id_ujian_essay', $id);
        $this->datatables->add_column('no', '');
        return $this->datatables->generate();
    }

    function detail_ujian_essay($id){
        $this->db->select('*');
        $this->db->from('m_ujian_essay');
        $this->db->where('id_ujian_essay', $id);
        return $this->db->get();
    }

    function ambil_nim($id){
        $this->db->select('a.nim, a.nama');
        $this->db->from('mahasiswa a');
        $this->db->join('h_ujian_essay b', 'a.id_mahasiswa = b.id_mahasiswa');
        $this->db->where('b.id', $id);
        return $this->db->get();
    }

    function ambil_nama_ujian($id){
        $this->db->select('b.nama_ujian_essay');
        $this->db->from('h_ujian_essay a');
        $this->db->join('m_ujian_essay b', 'a.id_ujian_essay = b.id_ujian_essay');
        $this->db->where('a.id', $id);
        return $this->db->get();
    }

    function hasil_jawaban($id){
        $this->db->select('a.id_detail, a.id, a.id_soal_essay, a.jawaban_essay, a.nilai, b.file, b.soal_essay');
        $this->db->from('detail_h_ujian_essay a');
        $this->db->join('tb_soal_essay b', 'a.id_soal_essay = b.id_soal_essay');
        $this->db->where('a.id', $id);
        $this->db->order_by('a.id_detail', 'asc');
        return $this->db->get();
    }


}