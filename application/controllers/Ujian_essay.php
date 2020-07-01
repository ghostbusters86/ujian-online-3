<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ujian_essay extends CI_Controller {

	public $mhs, $user;

	public function __construct(){
		parent::__construct();
		if (!$this->ion_auth->logged_in()){
			redirect('auth');
		}
		$this->load->library(['datatables', 'form_validation']);// Load Library Ignited-Datatables
		$this->load->helper('my');
		$this->load->model('Master_model', 'master');
		$this->load->model('Soal_essay_model', 'soal');
		$this->load->model('Ujian_essay_model', 'ujian');
		$this->form_validation->set_error_delimiters('','');

		$this->user = $this->ion_auth->user()->row();
		$this->mhs 	= $this->ujian->getIdMahasiswa($this->user->username);
    }

    public function akses_dosen()
    {
        if ( !$this->ion_auth->in_group('dosen') ){
			show_error('Halaman ini khusus untuk dosen untuk membuat Test Online, <a href="'.base_url('dashboard').'">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
		}
    }

    public function akses_mahasiswa()
    {
        if ( !$this->ion_auth->in_group('mahasiswa') ){
			show_error('Halaman ini khusus untuk mahasiswa mengikuti ujian, <a href="'.base_url('dashboard').'">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
		}
    }

    public function output_json($data, $encode = true)
	{
        if($encode) $data = json_encode($data);
        $this->output->set_content_type('application/json')->set_output($data);
	}
	
	public function json($id=null)
	{
        $this->akses_dosen();

		$this->output_json($this->ujian->getDataUjian($id), false);
	}

    public function master()
	{
        $this->akses_dosen();
        $user = $this->ion_auth->user()->row();
        $data = [
			'user' => $user,
			'judul'	=> 'Ujian Essay',
			'subjudul'=> 'Data Ujian Essay',
			'dosen' => $this->ujian->getIdDosen($user->username),
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('ujian_essay/data');
		$this->load->view('_templates/dashboard/_footer.php');
    }

    public function add()
	{
		$this->akses_dosen();
		
		$user = $this->ion_auth->user()->row();

        $data = [
			'user' 		=> $user,
			'judul'		=> 'Ujian Essay',
			'subjudul'	=> 'Tambah Ujian Essay',
			'matkul'	=> $this->soal->getMatkulDosen($user->username),
			'dosen'		=> $this->ujian->getIdDosen($user->username),
		];

		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('ujian_essay/add');
		$this->load->view('_templates/dashboard/_footer.php');
    }
    
    public function convert_tgl($tgl)
	{
		$this->akses_dosen();
		return date('Y-m-d H:i:s', strtotime($tgl));
	}

	public function validasi()
	{
		$this->akses_dosen();
		
		$user 	= $this->ion_auth->user()->row();
		$dosen 	= $this->ujian->getIdDosen($user->username);
		$jml 	= $this->ujian->getJumlahSoal($dosen->id_dosen)->jml_soal;
		$jml_a 	= $jml + 1; // Jika tidak mengerti, silahkan baca user_guide codeigniter tentang form_validation pada bagian less_than

		$this->form_validation->set_rules('nama_ujian', 'Nama Ujian', 'required|alpha_numeric_spaces|max_length[50]');
		$this->form_validation->set_rules('jumlah_soal', 'Jumlah Soal', "required|integer|less_than[{$jml_a}]|greater_than[0]", ['less_than' => "Soal tidak cukup, anda hanya punya {$jml} soal"]);
		$this->form_validation->set_rules('tgl_mulai', 'Tanggal Mulai', 'required');
		$this->form_validation->set_rules('tgl_selesai', 'Tanggal Selesai', 'required');
		$this->form_validation->set_rules('waktu', 'Waktu', 'required|integer|max_length[4]|greater_than[0]');
		$this->form_validation->set_rules('jenis', 'Acak Soal', 'required|in_list[acak,urut]');
	}

	public function save()
	{
		$this->validasi();
		$this->load->helper('string');

		$method 		= $this->input->post('method', true);
		$dosen_id 		= $this->input->post('dosen_id', true);
		$matkul_id 		= $this->input->post('matkul_id', true);
		$nama_ujian 	= $this->input->post('nama_ujian', true);
		$jumlah_soal 	= $this->input->post('jumlah_soal', true);
		$tgl_mulai 		= $this->convert_tgl($this->input->post('tgl_mulai', 	true));
		$tgl_selesai	= $this->convert_tgl($this->input->post('tgl_selesai', true));
		$waktu			= $this->input->post('waktu', true);
		$jenis			= $this->input->post('jenis', true);
		$token 			= strtoupper(random_string('alpha', 5));

		if( $this->form_validation->run() === FALSE ){
			$data['status'] = false;
			$data['errors'] = [
				'nama_ujian' 	=> form_error('nama_ujian'),
				'jumlah_soal' 	=> form_error('jumlah_soal'),
				'tgl_mulai' 	=> form_error('tgl_mulai'),
				'tgl_selesai' 	=> form_error('tgl_selesai'),
				'waktu' 		=> form_error('waktu'),
				'jenis' 		=> form_error('jenis'),
			];
		}else{
			$input = [
				'nama_ujian_essay' 	=> $nama_ujian,
				'jumlah_soal' 	    => $jumlah_soal,
				'tanggal_mulai' 	=> $tgl_mulai,
				'tanggal_selesai' 	=> $tgl_selesai,
				'waktu' 		    => $waktu,
				'jenis' 		    => $jenis,
			];
			if($method === 'add'){
				$input['id_dosen']	= $dosen_id;
				$input['id_matkul'] = $matkul_id;
				$input['token']		= $token;
				$action = $this->master->create('m_ujian_essay', $input);
			}else if($method === 'edit'){
				$id_ujian = $this->input->post('id_ujian_essay', true);
				$action = $this->master->update('m_ujian_essay', $input, 'id_ujian_essay', $id_ujian);
			}
			$data['status'] = $action ? TRUE : FALSE;
		}
		$this->output_json($data);
    }
    
    public function edit($id)
	{
		$this->akses_dosen();
		
		$user = $this->ion_auth->user()->row();

        $data = [
			'user' 		=> $user,
			'judul'		=> 'Ujian Essay',
			'subjudul'	=> 'Edit Ujian Essay',
			'matkul'	=> $this->soal->getMatkulDosen($user->username),
			'dosen'		=> $this->ujian->getIdDosen($user->username),
			'ujian'		=> $this->ujian->getUjianById($id),
		];

		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('ujian_essay/edit');
		$this->load->view('_templates/dashboard/_footer.php');
    }
    
    public function delete()
	{
		$this->akses_dosen();
		$chk = $this->input->post('checked', true);
        if(!$chk){
            $this->output_json(['status'=>false]);
        }else{
            if($this->master->delete('m_ujian_essay', $chk, 'id_ujian_essay')){
                $this->output_json(['status'=>true, 'total'=>count($chk)]);
            }
        }
	}

	public function refresh_token($id)
	{
		$this->load->helper('string');
		$data['token'] = strtoupper(random_string('alpha', 5));
		$refresh = $this->master->update('m_ujian_essay', $data, 'id_ujian_essay', $id);
		$data['status'] = $refresh ? TRUE : FALSE;
		$this->output_json($data);
	}


	// mahasiswa 

	public function list_json()
	{
		$this->akses_mahasiswa();
		
		$list = $this->ujian->getListUjian($this->mhs->id_mahasiswa, $this->mhs->kelas_id);
		$this->output_json($list, false);
	}
	
	public function list_ujian()
	{
		$this->akses_mahasiswa();

		$user = $this->ion_auth->user()->row();
		
		$data = [
			'user' 		=> $user,
			'judul'		=> 'Ujian Essay',
			'subjudul'	=> 'List Ujian Essay',
			'mhs' 		=> $this->ujian->getIdMahasiswa($user->username),
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('ujian_essay/list');
		$this->load->view('_templates/dashboard/_footer.php');
	}

	public function token($id)
	{
		$this->akses_mahasiswa();
		$user = $this->ion_auth->user()->row();
		
		$data = [
			'user' 		=> $user,
			'judul'		=> 'Ujian Essay',
			'subjudul'	=> 'Token Ujian Essay',
			'mhs' 		=> $this->ujian->getIdMahasiswa($user->username),
			'ujian'		=> $this->ujian->getUjianById($id),
			'encrypted_id' => urlencode($this->encryption->encrypt($id))
		];
		$this->load->view('_templates/topnav/_header.php', $data);
		$this->load->view('ujian_essay/token');
		$this->load->view('_templates/topnav/_footer.php');
	}

	public function cektoken()
	{
		$id = $this->input->post('id_ujian_essay', true);
		$token = $this->input->post('token', true);
		$cek = $this->ujian->getUjianById($id);
		
		$data['status'] = $token === $cek->token ? TRUE : FALSE;
		$this->output_json($data);
	}

	public function encrypt()
	{
		$id = $this->input->post('id', true);
		$key = urlencode($this->encryption->encrypt($id));
		// $decrypted = $this->encryption->decrypt(rawurldecode($key));
		$this->output_json(['key'=>$key]);
	}

	public function index()
	{
		$this->akses_mahasiswa();
		$key = $this->input->get('key', true);
		$id  = $this->encryption->decrypt(rawurldecode($key));
		
		$ujian 		= $this->ujian->getUjianById($id);
		$soal 		= $this->ujian->getSoal($id);
		
		$mhs		= $this->mhs;
		$h_ujian 	= $this->ujian->HslUjian($id, $mhs->id_mahasiswa);
	
		$cek_sudah_ikut = $h_ujian->num_rows();

		if ($cek_sudah_ikut < 1) {
			$soal_urut_ok 	= array();
			$i = 0;
			foreach ($soal as $s) {
				$soal_per = new stdClass();
				$soal_per->id_soal 		= $s->id_soal_essay;
				$soal_per->soal 		= $s->soal_essay;
				$soal_per->file 		= $s->file;
				$soal_per->tipe_file 	= $s->tipe_file;
				$soal_urut_ok[$i] 		= $soal_per;
				$i++;
			}
			$soal_urut_ok 	= $soal_urut_ok;
			$list_id_soal	= "";
			$list_jw_soal 	= "";
			if (!empty($soal)) {
				foreach ($soal as $d) {
					$list_id_soal .= $d->id_soal_essay."|";
					$list_jw_soal .= $d->id_soal_essay."::N|";
				}
			}
			$list_id_soal 	= substr($list_id_soal, 0, -1);
			$list_jw_soal 	= substr($list_jw_soal, 0, -1);
			$waktu_selesai 	= date('Y-m-d H:i:s', strtotime("+{$ujian->waktu} minute"));
			$time_mulai		= date('Y-m-d H:i:s');
			$id_data = uniqid();

			$input = [
				'id'=> $id_data,
				'id_ujian_essay'=> $id,
				'id_mahasiswa'	=> $mhs->id_mahasiswa,
				'list_soal'		=> $list_id_soal,
				'list_jawaban'	=> $list_jw_soal,
				'nilai'			=> 0,
				'nilai_bobot'	=> 0,
				'tgl_mulai'		=> $time_mulai,
				'tgl_selesai'	=> $waktu_selesai,
				'status'		=> 'Y'
			];

			$this->master->create('h_ujian_essay', $input);

			$dt = $this->ujian->detail_ujian($id_data)->row();
			$str = $dt->list_soal;
			$datahaha = explode("|",$str);
			for ($i = 0; $i < sizeof($datahaha); $i++) {
				$datakuhaha[$i] = [
					'id'			=> $id_data,
					'id_soal_essay'	=> $datahaha[$i],
					'nilai'			=> 0
				];
				$this->master->create('detail_h_ujian_essay', $datakuhaha[$i]);
			}

			// // Setelah insert wajib refresh dulu
			redirect('ujian_essay/?key='.urlencode($key), 'location', 301);
			// print_r($a_soal);
		}

		
		$q_soal = $h_ujian->row();
		// print_r($q_soal);
		
		$urut_soal 		= explode("|", $q_soal->list_soal);
		$soal_urut_ok	= array();
		for ($i = 0; $i < sizeof($urut_soal); $i++) {
			$pc_urut_soal	= explode(":",$urut_soal[$i]);
			$ambil_soal 	= $this->ujian->ambilSoal($pc_urut_soal[0]);
			$soal_urut_ok[] = $ambil_soal; 
		}

		$detail_tes = $q_soal;
		$soal_urut_ok = $soal_urut_ok;

		$pc_list_jawaban = explode("|", $detail_tes->list_jawaban);
		$arr_jawab = array();
		foreach ($pc_list_jawaban as $v) {
			$pc_v 	= explode(":", $v);
			$idx 	= $pc_v[0];
			$val 	= $pc_v[1];
			$rg 	= $pc_v[2];

			$arr_jawab[$idx] = array("j"=>$val,"r"=>$rg);
		}

		$html = '';
		$no = 1;
		if (!empty($soal_urut_ok)) {
			foreach ($soal_urut_ok as $s) {
				$path = 'uploads/bank_soal_essay/';
				$vrg = $arr_jawab[$s->id_soal_essay]["r"] == "" ? "N" : $arr_jawab[$s->id_soal_essay]["r"];
				$html .= '<input type="hidden" name="id_soal_'.$no.'" value="'.$s->id_soal_essay.'">';
				$html .= '<input type="hidden" name="rg_'.$no.'" id="rg_'.$no.'" value="'.$vrg.'">';
				$html .= '<div class="step" id="widget_'.$no.'">';

				$html .= '<div class="text-center"><div class="w-25">'.tampil_media($path.$s->file).'</div></div>'.$s->soal_essay;
				
				$html .= '<div  onclick="return simpan_sementara();"><textarea class="form-control" id="opsi_'.$s->id_soal_essay.'" name="opsi_'.$no.'">'.$arr_jawab[$s->id_soal_essay]["j"].'</textarea></div>';
				// }
				$html .= '</div></div>';
				$no++;
			}
		}

		// // Enkripsi Id Tes
		$id_tes = $this->encryption->encrypt($detail_tes->id);

		$data = [
			'user' 		=> $this->user,
			'mhs'		=> $this->mhs,
			'judul'		=> 'Ujian Esssay',
			'subjudul'	=> 'Lembar Ujian Essay',
			'soal'		=> $detail_tes,
			'no' 		=> $no,
			'html' 		=> $html,
			'id_tes'	=> $id_tes
		];
		$this->load->view('_templates/topnav/_header.php', $data);
		$this->load->view('ujian_essay/sheet');
		$this->load->view('_templates/topnav/_footer.php');
	}

	public function simpan_satu()
	{
		// Decrypt Id
		$id_tes = $this->input->post('id', true);
		$id_tes = $this->encryption->decrypt($id_tes);
		
		$input 	= $this->input->post(null, true);
		$list_jawaban 	= "";
		// $id_soal_essay = "";
		$hasil = "";
		for ($i = 1; $i < $input['jml_soal']; $i++) {
			$_tjawab 	= "opsi_".$i;
			$_tidsoal 	= "id_soal_".$i;
			$_ragu 		= "rg_".$i;
			$jawaban_ 	= empty($input[$_tjawab]) ? "" : $input[$_tjawab];
			$list_jawaban	.= "".$input[$_tidsoal].":".$jawaban_.":".$input[$_ragu]."|";
			// $id_soal_essay .= $i;
			$hasil = $jawaban_;
			$this->ujian->update_jawaban($id_tes, $input[$_tidsoal], $hasil);
		}
		$list_jawaban	= substr($list_jawaban, 0, -1);
		// $jawaban_ = $jawaban_;
		$d_simpan = [
			'list_jawaban' => $list_jawaban
		];

		
		
		// Simpan jawaban
		$this->master->update('h_ujian_essay', $d_simpan, 'id', $id_tes);
		$this->output_json(['status'=>true]);
	}

	public function simpan_akhir()
	{
		// Decrypt Id
		$id_tes = $this->input->post('id', true);
		$id_tes = $this->encryption->decrypt($id_tes);
	
		$d_update = [
			'status'		=> 'N'
		];

		$this->master->update('h_ujian_essay', $d_update, 'id', $id_tes);
		$this->output_json(['status'=>TRUE, 'data'=>$d_update, 'id'=>$id_tes]);
	}

	function nilai($id){
		
		$this->akses_dosen();
		$hasil = $this->ujian->detail_ujian_essay($id)->row();
        $user = $this->ion_auth->user()->row();
        $data = [
			'user' => $user,
			'judul'	=> 'Ujian Essay',
			'subjudul'=> 'Data Pengerjaan Ujian Essay',
			'matkul'	=> $this->soal->getMatkulDosen($user->username),
			'dosen' => $this->ujian->getIdDosen($user->username),
			'es'	=> $id,
			'ujian' => $hasil
		];
		
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('ujian_essay/data_nilai');
		$this->load->view('_templates/dashboard/_footer.php');

	}

	public function data_penilaian($id=null)
	{
        $this->akses_dosen();

		$this->output_json($this->ujian->jawab($id), false);
	}

	function hasil_jawaban($id){
		$this->akses_dosen();
		$user = $this->ion_auth->user()->row();
		$jawab = $this->ujian->hasil_jawaban($id)->result();
		$mahasiswa = $this->ujian->ambil_nim($id)->row();
		$ujian = $this->ujian->ambil_nama_ujian($id)->row();
        $data = [
			'user' => $user,
			'judul'	=> 'Ujian Essay',
			'subjudul'=> 'Penilaian Ujian Essay',
			'matkul'	=> $this->soal->getMatkulDosen($user->username),
			'dosen' => $this->ujian->getIdDosen($user->username),
			'id'	=> $id,
			'mahasiswa' => $mahasiswa,
			'jawab' => $jawab,
			'ujian' => $ujian
		];
		
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('ujian_essay/hasil_jawaban');
		$this->load->view('_templates/dashboard/_footer.php');
	}

	function save_hasil_jawaban(){
		$id = $_POST['id_detail'];
		$nilai = $_POST['nilai'];
		$id_hasil = $_POST['id'];
		$bobot = $_POST['bobot'];

		$data = array();
		
		$index = 0; 
		if(is_array($id) || is_object($id))
		{
			foreach($id as $dataId){ 
			array_push($data, array(
				'id_detail'=>$dataId,
				'nilai'=>$nilai[$index],  
			));
			
			$index++;
			}

		}
		
		$sql = $this->ujian->update_batch($data);

		$total_bobot = 0;
		$n = 0;
		$total_nilai = 0;
		foreach ($bobot as $totalbot) {
			$total_bobot = $total_bobot + $totalbot;
			$total_nilai = $total_nilai+($totalbot*$nilai[$n]);
			$n++;
		}
		
		$final_nilai = $total_nilai/$total_bobot;
		
		$this->ujian->update_final($id_hasil, number_format($final_nilai,2), $total_bobot);
		echo "<script>alert('Suksen Menyimpan Data');</script>";
		redirect('ujian_essay/hasil_jawaban/'.$id_hasil);
	}
    
}