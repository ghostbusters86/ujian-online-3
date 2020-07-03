<?php

class Pertemuan extends CI_Controller{
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
	
	public function master()
	{
        $this->akses_dosen();
        $user = $this->ion_auth->user()->row();
        $data = [
			'user' => $user,
			'judul'	=> 'Pertemuan',
			'subjudul'=> 'Data Pertemuan',
			'dosen' => $this->pertemuan->getIdDosen($user->username),
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('pertemuan/data');
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

		$this->output_json($this->pertemuan->getDataPertemuan($id), false);
    }
    
    public function add()
	{
		$this->akses_dosen();
		
		$user = $this->ion_auth->user()->row();

        $data = [
			'user' 		=> $user,
			'judul'		=> 'Pertemuan',
			'subjudul'	=> 'Tambah Pertemuan',
			'matkul'	=> $this->pertemuan->getMatkulDosen($user->username),
			'dosen'		=> $this->pertemuan->getIdDosen($user->username),
		];

		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('pertemuan/add');
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
		$dosen 	= $this->pertemuan->getIdDosen($user->username);

		$this->form_validation->set_rules('nama_pertemuan', 'Nama Pertemuan', 'required|alpha_numeric_spaces|max_length[50]');
		$this->form_validation->set_rules('materi', 'Materi Pertemuan', 'required');
		$this->form_validation->set_rules('tgl_mulai', 'Tanggal Mulai', 'required');
		$this->form_validation->set_rules('tgl_selesai', 'Tanggal Selesai', 'required');
	}

	public function file_config()
    {
        $config['upload_path']      = FCPATH.'uploads/pertemuan/';
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
		$nama_pertemuan = $this->input->post('nama_pertemuan', true);
		$materi 		= $this->input->post('materi', true);
		$tgl_mulai 		= $this->convert_tgl($this->input->post('tgl_mulai', 	true));
		$tgl_selesai	= $this->convert_tgl($this->input->post('tgl_selesai', true));
		// $file_pertemuan		= $this->input->post('file_pertemuan', true);
		$token 			= uniqid();

		if( $this->form_validation->run() === FALSE ){
			$data['status'] = false;
			$data['errors'] = [
				'nama_pertemuan' 	=> form_error('nama_pertemuan'),
				'materi' 	=> form_error('materi'),
				'tgl_mulai' 	=> form_error('tgl_mulai'),
				'tgl_selesai' 	=> form_error('tgl_selesai'),
			];
		}else{
			$input = [
				'nama_pertemuan' 	=> $nama_pertemuan,
				'materi'            => $materi,
				'tanggal_mulai'     => $tgl_mulai,
                'tanggal_selesai' 	=> $tgl_selesai,
                'token'             => $token
			];
			if($method === 'add'){
				$input['id_dosen']	= $dosen_id;
				$input['id_matkul'] = $matkul_id;
				$input['token']		= $token;
				if(!empty($_FILES['file_materi']['name'])){
					if (!$this->upload->do_upload('file_materi')){
						$error = $this->upload->display_errors();
						show_error($error, 500, 'File Materi Error');
						exit();
					}else{
						$input['file_materi'] = $this->upload->data('file_name');
					}
				}else{
					$input['file_materi'] = '';
                }
                
                $this->load->library('ciqrcode'); //pemanggilan library QR CODE
 
                $config['cacheable']    = true; //boolean, the default is true
                $config['cachedir']     = './assets/'; //string, the default is application/cache/
                $config['errorlog']     = './assets/'; //string, the default is application/logs/
                $config['imagedir']     = './uploads/qrcode/'; //direktori penyimpanan qr code
                $config['quality']      = true; //boolean, the default is true
                $config['size']         = '1024'; //interger, the default is 1024
                $config['black']        = array(224,255,255); // array, default is array(255,255,255)
                $config['white']        = array(70,130,180); // array, default is array(0,0,0)
                $this->ciqrcode->initialize($config);
        
                $image_name=$token.'.png'; //buat name dari qr code sesuai dengan nim
        
                $params['data'] = $token; //data yang akan di jadikan QR CODE
                $params['level'] = 'H'; //H=High
                $params['size'] = 10;
                $params['savename'] = FCPATH.$config['imagedir'].$image_name; //simpan image QR CODE ke folder assets/images/
                $this->ciqrcode->generate($params); // fungsi untuk generate QR CODE

				$action = $this->master->create('m_pertemuan', $input);
			}else if($method === 'edit'){
				$img_src = FCPATH.'uploads/pertemuan/';
				$getPertemuan = $this->pertemuan->getPertemuanById($this->input->post('id_pertemuan', true));
				if(!empty($_FILES['file_materi']['name'])){
					if (!$this->upload->do_upload('file_materis')){
						$error = $this->upload->display_errors();
						show_error($error, 500, 'File Materi Error');
						exit();
					}else{
						if(!unlink($img_src.$getPertemuan->file_materi)){
							show_error('Error saat delete file <br/>'.var_dump($getsoal), 500, 'Error Edit File');
							exit();
						}
						$input['file_materi'] = $this->upload->data('file_name');
					}
				}else{
					$input['file_materi'] = $getPertemuan->file_materi;
				}
				$id_pertemuan = $this->input->post('id_pertemuan', true);
				$action = $this->master->update('m_pertemuan', $input, 'id_pertemuan', $id_pertemuan);
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
			'judul'		=> 'Pertemuan',
			'subjudul'	=> 'Edit Pertemuan',
			'matkul'	=> $this->pertemuan->getMatkulDosen($user->username),
			'dosen'		=> $this->pertemuan->getIdDosen($user->username),
			'pertemuan'	=> $this->pertemuan->getPertemuanById($id),
		];

		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('pertemuan/edit');
		$this->load->view('_templates/dashboard/_footer.php');
    }
    
    public function delete()
	{
		$this->akses_dosen();
		$chk = $this->input->post('checked', true);
        if(!$chk){
            $this->output_json(['status'=>false]);
        }else{
            if($this->master->delete('m_pertemuan', $chk, 'id_pertemuan')){
                $this->output_json(['status'=>true, 'total'=>count($chk)]);
            }
        }
	}
	
    

}