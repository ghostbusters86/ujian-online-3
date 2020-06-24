<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tugas extends CI_Controller {

	public $mhs, $user;

	public function __construct(){
		parent::__construct();
		if (!$this->ion_auth->logged_in()){
			redirect('auth');
		}
		$this->load->library(['datatables', 'form_validation', 'encrypt']);// Load Library Ignited-Datatables
		$this->load->helper('my');
		$this->load->model('Master_model', 'master');
		$this->load->model('Tugas_model', 'tugas');
		$this->form_validation->set_error_delimiters('','');

		$this->user = $this->ion_auth->user()->row();
		$this->mhs 	= $this->tugas->getIdMahasiswa($this->user->username);
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
	
	public function master()
	{
        $this->akses_dosen();
        $user = $this->ion_auth->user()->row();
        $data = [
			'user' => $user,
			'judul'	=> 'Tugas',
			'subjudul'=> 'Data Tugas',
			'dosen' => $this->tugas->getIdDosen($user->username),
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('tugas/data');
		$this->load->view('_templates/dashboard/_footer.php');
	}

	public function output_json($data, $encode = true)
	{
        if($encode) $data = json_encode($data);
        $this->output->set_content_type('application/json')->set_output($data);
	}

	public function json($id=null)
	{
        $this->akses_dosen();

		$this->output_json($this->tugas->getDataTugas($id), false);
	}

	public function add()
	{
		$this->akses_dosen();
		
		$user = $this->ion_auth->user()->row();

        $data = [
			'user' 		=> $user,
			'judul'		=> 'Tugas',
			'subjudul'	=> 'Tambah Tugas',
			'matkul'	=> $this->tugas->getMatkulDosen($user->username),
			'dosen'		=> $this->tugas->getIdDosen($user->username),
		];

		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('tugas/add');
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
		$dosen 	= $this->tugas->getIdDosen($user->username);

		$this->form_validation->set_rules('nama_tugas', 'Nama Tugas', 'required|alpha_numeric_spaces|max_length[50]');
		$this->form_validation->set_rules('deskripsi', 'Deskripsi Tugas', 'required');
		$this->form_validation->set_rules('tgl_mulai', 'Tanggal Mulai', 'required');
		$this->form_validation->set_rules('tgl_selesai', 'Tanggal Selesai', 'required');
	}

	public function file_config()
    {
        // $allowed_type 	= [
        //     "image/jpeg", "image/jpg", "image/png", "image/gif",
        //     "audio/mpeg", "audio/mpg", "audio/mpeg3", "audio/mp3", "audio/x-wav", "audio/wave", "audio/wav",
        //     "video/mp4", "application/octet-stream"
        // ];
        $config['upload_path']      = FCPATH.'uploads/tugas/';
        $config['allowed_types']    = 'doc|docx|ppt|pptx|xls|xlsx|pdf';
        $config['encrypt_name']     = TRUE;
        
        return $this->load->library('upload', $config);
    }

	public function save()
	{
		$this->validasi();
		$this->file_config();
		$this->load->helper('string');

		$method 		= $this->input->post('method', true);
		$dosen_id 		= $this->input->post('dosen_id', true);
		$matkul_id 		= $this->input->post('matkul_id', true);
		$nama_tugas 	= $this->input->post('nama_tugas', true);
		$deskripsi 		= $this->input->post('deskripsi', true);
		$tgl_mulai 		= $this->convert_tgl($this->input->post('tgl_mulai', 	true));
		$tgl_selesai	= $this->convert_tgl($this->input->post('tgl_selesai', true));
		// $file_tugas		= $this->input->post('file_tugas', true);
		$token 			= strtoupper(random_string('alpha', 5));

		if( $this->form_validation->run() === FALSE ){
			$data['status'] = false;
			$data['errors'] = [
				'nama_tugas' 	=> form_error('nama_tugas'),
				'deskripsi' 	=> form_error('deskripsi'),
				'tgl_mulai' 	=> form_error('tgl_mulai'),
				'tgl_selesai' 	=> form_error('tgl_selesai'),
			];
		}else{
			$input = [
				'nama_tugas' 	=> $nama_tugas,
				'deskripsi_tugas'=> $deskripsi,
				'tanggal_mulai' => $tgl_mulai,
				'terlambat' 	=> $tgl_selesai,
			];
			if($method === 'add'){
				$input['dosen_id']	= $dosen_id;
				$input['matkul_id'] = $matkul_id;
				$input['token']		= $token;
				if(!empty($_FILES['file_tugas']['name'])){
					if (!$this->upload->do_upload('file_tugas')){
						$error = $this->upload->display_errors();
						show_error($error, 500, 'File Tugas Error');
						exit();
					}else{
						$input['file_tugas'] = $this->upload->data('file_name');
					}
				}else{
					$input['file_tugas'] = '';
				}
				$action = $this->master->create('m_tugas', $input);
			}else if($method === 'edit'){
				$img_src = FCPATH.'uploads/tugas/';
				$getTugas = $this->tugas->getTugasById($this->input->post('id_tugas', true));
				if(!empty($_FILES['file_tugas']['name'])){
					if (!$this->upload->do_upload('file_tugas')){
						$error = $this->upload->display_errors();
						show_error($error, 500, 'File Tugas Error');
						exit();
					}else{
						if(!unlink($img_src.$getTugas->file_tugas)){
							show_error('Error saat delete file <br/>'.var_dump($getsoal), 500, 'Error Edit File');
							exit();
						}
						$input['file_tugas'] = $this->upload->data('file_name');
					}
				}else{
					$input['file_tugas'] = $getTugas->file_tugas;
				}
				$id_tugas = $this->input->post('id_tugas', true);
				$action = $this->master->update('m_tugas', $input, 'id_tugas', $id_tugas);
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
			'judul'		=> 'Tugas',
			'subjudul'	=> 'Edit Tugas',
			'matkul'	=> $this->tugas->getMatkulDosen($user->username),
			'dosen'		=> $this->tugas->getIdDosen($user->username),
			'tugas'		=> $this->tugas->getTugasById($id),
		];

		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('tugas/edit');
		$this->load->view('_templates/dashboard/_footer.php');
	}

	public function delete()
	{
		$this->akses_dosen();
		$chk = $this->input->post('checked', true);
        if(!$chk){
            $this->output_json(['status'=>false]);
        }else{
            if($this->master->delete('m_tugas', $chk, 'id_tugas')){
                $this->output_json(['status'=>true, 'total'=>count($chk)]);
            }
        }
	}

	public function refresh_token($id)
	{
		$this->load->helper('string');
		$data['token'] = strtoupper(random_string('alpha', 5));
		$refresh = $this->master->update('m_tugas', $data, 'id_tugas', $id);
		$data['status'] = $refresh ? TRUE : FALSE;
		$this->output_json($data);
	}









	public function list_json()
	{
		$this->akses_mahasiswa();
		
		$list = $this->tugas->getListTugas($this->mhs->id_mahasiswa, $this->mhs->kelas_id);
		$this->output_json($list, false);
	}
	
	public function list_tugas()
	{
		$this->akses_mahasiswa();

		$user = $this->ion_auth->user()->row();

		
		$data = [
			'user' 		=> $user,
			'judul'		=> 'Tugas',
			'subjudul'	=> 'List Tugas',
			'mhs' 		=> $this->tugas->getIdMahasiswa($user->username),
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('tugas/list');
		$this->load->view('_templates/dashboard/_footer.php');
	}

	public function detailTugas($id){
		$data = $this->tugas->getTugasById($id);
		header('Content-Type: application/json');
		echo json_encode($data);
	}

	public function cekWaktu($id){
		$data = $this->tugas->cekWaktu($id);
		header('Content-Type: application/json');
		if($data->terlambat < date("d-m-Y H:i:s")){
			$hasil = 'telat';
		}else{
			$hasil = 'tidak telat';
		}

		if($data->tanggal_mulai > date("d-m-Y H:i:s")){
			$mulai = 'belum mulai';
		}else{
			$mulai = 'sudah mulai';
		}
		echo json_encode(array('hasil'=>$hasil, 'mulai'=>$mulai));
	}

	public function uploadTugasMahasiswa($id){
		$user = $this->ion_auth->user()->row();
		$mahasiswa 	= $this->tugas->getIdMahasiswa($this->user->username);
		$id_mahasiswa = $mahasiswa->id_mahasiswa;
		$cekdata = $this->tugas->cekUpload($id, $id_mahasiswa)->num_rows();
		$data = array($id, $id_mahasiswa);
		if($cekdata > 0){
			$rowdata = $this->tugas->cekUpload($id, $id_mahasiswa)->row();
			$hasil = 'ditemukan';
		}else{
			$rowdata = '';
			$hasil = 'zonk';
		}
		echo json_encode(array('data'=>$data, 'rowdata'=>$rowdata, 'hasil'=>$hasil));
	}

	public function do_upload_mahasiswa(){
		date_default_timezone_set("Asia/Bangkok");
		$method = $this->input->post('method');
		$id_tugas = $this->input->post('id_tugas');
		$nim = $this->input->post('nim');

		$config['upload_path'] = FCPATH.'uploads/tugasMahasiswa/';
		$config['encrypt_name'] = TRUE;
		$config['allowed_types'] = 'doc|docx|ppt|pptx|xls|xlsx|pdf';
		$config['max_size'] = 2000;
		$this->load->library('upload', $config);

		$file = FCPATH.'uploads/tugasMahasiswa/';
		

		if($method == 'add'){
			if (!$this->upload->do_upload('file_tugas')) {
				$data = array('pesan' => $this->upload->display_errors());
			} else {
				$tanggal = date("Y-m-d H:i:s");
				$new_name = $this->upload->data('file_name');
				$ext = $this->upload->data('file_ext');
				$this->tugas->add_tugas($id_tugas, $nim, $new_name, $tanggal, $ext);
				$data = array('pesan' => 'upload berhasil');
			}

		}else{
			$id_hasil_tugas = $this->input->post('id_hasil_tugas');
			$rowdata = $this->tugas->rowdata($id_hasil_tugas)->row();
			$fileLama = $rowdata->tugas_mahasiswa;
			if (!$this->upload->do_upload('file_tugas')) {
				$data = array('pesan' => $this->upload->display_errors());
			} else {
				$tanggal = date("Y-m-d H:i:s");
				$new_name = $this->upload->data('file_name');
				$ext = $this->upload->data('file_ext');
				$this->tugas->update_tugas($id_hasil_tugas, $new_name, $tanggal, $ext);
				unlink($file.$fileLama);
				$data = array('pesan' => 'upload berhasil');
			}
		}
		echo json_encode($data);
	}

	function downloadTugasMahasiswa($file_mahasiswa){
		$path = FCPATH.'uploads/tugasMahasiswa/';
		$file = $path.$file_mahasiswa;
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($file));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: private');
		header('Pragma: private');
		header('Content-Length: ' . filesize($file));
		ob_clean();
		flush();
		readfile($file);
		
		exit;
	}

	function downloadTugas($file){
		$path = FCPATH.'uploads/tugas/';
		$file = $path.$file;
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($file));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: private');
		header('Pragma: private');
		header('Content-Length: ' . filesize($file));
		ob_clean();
		flush();
		readfile($file);
		
		exit;
	}

}