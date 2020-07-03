<?php

class Absensi extends CI_Controller{
    public $mhs, $user;

	public function __construct(){
		parent::__construct();
		if (!$this->ion_auth->logged_in()){
			redirect('auth');
		}
		$this->load->library(['datatables', 'form_validation', 'encrypt']);// Load Library Ignited-Datatables
		$this->load->helper('my');
		$this->load->model('Master_model', 'master');
		$this->load->model('Pertemuan_model', 'pertemuan');
		$this->form_validation->set_error_delimiters('','');

		$this->user = $this->ion_auth->user()->row();
		$this->mhs 	= $this->pertemuan->getIdMahasiswa($this->user->username);
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

		$this->output_json($this->pertemuan->getDataPertemuan($id), false);
    }


    public function list_json()
	{
		$this->akses_mahasiswa();
		
		$list = $this->pertemuan->getListPertemuan($this->mhs->id_mahasiswa, $this->mhs->kelas_id);
		$this->output_json($list, false);
	}
    
    
    // mahasiswa 
    

	public function list_pertemuan()
	{
		$this->akses_mahasiswa();

		$user = $this->ion_auth->user()->row();

		
		$data = [
			'user' 		=> $user,
			'judul'		=> 'Absensi',
			'subjudul'	=> 'List Pertemuan',
			'mhs' 		=> $this->pertemuan->getIdMahasiswa($user->username),
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('absensi/list');
		$this->load->view('_templates/dashboard/_footer.php');
	}


}