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

	function cekdata($id){
		$jamsekarang = date("Y-m-d H:i:s");
		$mahasiswa 	= $this->pertemuan->getIdMahasiswa($this->user->username);
		$id_mahasiswa = $mahasiswa->id_mahasiswa;
		$cek = $this->pertemuan->cekwaktu($id)->row();
		if($jamsekarang > $cek->tanggal_selesai){
			$status = 'telat';
			$data = 'kosong';
		}else{
			$status = 'masuk';
			$data = $cek;
		}
		header('Content-Type: application/json');
		echo json_encode(array('data'=>$data, 'status'=>$status, 'id_mhs'=>$id_mahasiswa));
	}

	function simpan_absensi(){
		$id = $this->input->post('id_pertemuan');
		$mahasiswa = $this->input->post('id_mahasiswa');
		$token = $this->input->post('token');
		$jamsekarang = date("Y-m-d H:i:s");
		$cek = $this->pertemuan->cekwaktu($id)->row();
		if($jamsekarang > $cek->tanggal_selesai){
			$hasil['status'] = 'Terlambat';
		}else{
			if($cek->token != $token){
				$hasil['status'] = 'Token Salah';
			}else{
				$hasil['status'] = 'Berhasil';
				$ttd = uniqid();

				$this->load->library('ciqrcode'); //pemanggilan library QR CODE
 
                $config['cacheable']    = true; //boolean, the default is true
                $config['cachedir']     = './assets/'; //string, the default is application/cache/
                $config['errorlog']     = './assets/'; //string, the default is application/logs/
                $config['imagedir']     = './uploads/absensi/'; //direktori penyimpanan qr code
                $config['quality']      = true; //boolean, the default is true
                $config['size']         = '1024'; //interger, the default is 1024
                $config['black']        = array(224,255,255); // array, default is array(255,255,255)
                $config['white']        = array(70,130,180); // array, default is array(0,0,0)
                $this->ciqrcode->initialize($config);
        
                $image_name=$ttd.'.png'; //buat name dari qr code sesuai dengan nim
        
                $params['data'] = $ttd; //data yang akan di jadikan QR CODE
                $params['level'] = 'H'; //H=High
                $params['size'] = 10;
                $params['savename'] = FCPATH.$config['imagedir'].$image_name; //simpan image QR CODE ke folder assets/images/
				$this->ciqrcode->generate($params); // fungsi untuk generate QR CODE
				
				$hadir = 'H';
				$this->pertemuan->simpan_absensi($id, $mahasiswa, $image_name, $jamsekarang, $hadir);
			}
			
			
		}
		header('Content-Type: application/json');
		echo json_encode($hasil);
	}


}