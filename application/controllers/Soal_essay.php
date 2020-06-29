<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Soal_essay extends CI_Controller {
    public function __construct(){
		parent::__construct();
		if (!$this->ion_auth->logged_in()){
			redirect('auth');
		}else if ( !$this->ion_auth->is_admin() && !$this->ion_auth->in_group('dosen') ){
			show_error('Hanya Administrator dan dosen yang diberi hak untuk mengakses halaman ini, <a href="'.base_url('dashboard').'">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
		}
		$this->load->library(['datatables', 'form_validation']);// Load Library Ignited-Datatables
		$this->load->helper('my');// Load Library Ignited-Datatables
		$this->load->model('Master_model', 'master');
		$this->load->model('Soal_essay_model', 'soal_essay');
		$this->form_validation->set_error_delimiters('','');
	}

	public function output_json($data, $encode = true)
	{
        if($encode) $data = json_encode($data);
        $this->output->set_content_type('application/json')->set_output($data);
    }

    public function index()
	{
        $user = $this->ion_auth->user()->row();
		$data = [
			'user' => $user,
			'judul'	=> 'Soal Essay',
			'subjudul'=> 'Bank Soal Essay'
        ];
        
        if($this->ion_auth->is_admin()){
            //Jika admin maka tampilkan semua matkul
            $data['matkul'] = $this->master->getAllMatkul();
        }else{
            //Jika bukan maka matkul dipilih otomatis sesuai matkul dosen
            $data['matkul'] = $this->soal_essay->getMatkulDosen($user->username);
        }

		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('soal_essay/data');
		$this->load->view('_templates/dashboard/_footer.php');
    }

    public function data($id=null, $dosen=null)
	{
		$this->output_json($this->soal_essay->getDataSoal($id, $dosen), false);
    }

    public function add()
	{
        $user = $this->ion_auth->user()->row();
		$data = [
			'user'      => $user,
			'judul'	    => 'Soal Essay',
            'subjudul'  => 'Buat Soal Essay'
        ];

        if($this->ion_auth->is_admin()){
            //Jika admin maka tampilkan semua matkul
            $data['dosen'] = $this->soal_essay->getAllDosen();
        }else{
            //Jika bukan maka matkul dipilih otomatis sesuai matkul dosen
            $data['dosen'] = $this->soal_esay->getMatkulDosen($user->username);
        }

		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('soal_essay/add');
		$this->load->view('_templates/dashboard/_footer.php');
    }

    public function validasi()
    {
        if($this->ion_auth->is_admin()){
            $this->form_validation->set_rules('dosen_id', 'Dosen', 'required');
        }
        $this->form_validation->set_rules('bobot', 'Bobot Soal', 'required|max_length[2]');
    }

    public function file_config()
    {
        $allowed_type 	= [
            "image/jpeg", "image/jpg", "image/png", "image/gif",
            "audio/mpeg", "audio/mpg", "audio/mpeg3", "audio/mp3", "audio/x-wav", "audio/wave", "audio/wav",
            "video/mp4", "application/octet-stream"
        ];
        $config['upload_path']      = FCPATH.'uploads/bank_soal_essay/';
        $config['allowed_types']    = 'jpeg|jpg|png|gif|mpeg|mpg|mpeg3|mp3|wav|wave|mp4';
        $config['encrypt_name']     = TRUE;
        
        return $this->load->library('upload', $config);
    }

    public function save()
    {
        date_default_timezone_set("Asia/Jakarta");
        $method = $this->input->post('method', true);
        $this->validasi();
        $this->file_config();

        
        if($this->form_validation->run() === FALSE){
            $method==='add'? $this->add() : $this->edit();
        }else{
            $data = [
                'soal_essay'      => $this->input->post('soal', true),
                'bobot'     => $this->input->post('bobot', true),
            ];
            
            
        $img_src = FCPATH.'uploads/bank_soal_essay/';
        $getsoal = $this->soal_essay->getSoalById($this->input->post('id_soal', true));
        
        $error = '';
        
        if(!empty($_FILES['file_soal']['name'])){
            if (!$this->upload->do_upload('file_soal')){
                $error = $this->upload->display_errors();
                show_error($error, 500, 'File Soal Error');
                exit();
            }else{
                if($method === 'edit'){
                    if(!unlink($img_src.$getsoal->file)){
                        show_error('Error saat delete gambar <br/>'.var_dump($getsoal), 500, 'Error Delete Gambar');
                        exit();
                    }
                    // print_r($getsoal->file);
                }
                $data['file'] = $this->upload->data('file_name');
                $data['tipe_file'] = $this->upload->data('file_type');
            }
        }
        
            
                
            if($this->ion_auth->is_admin()){
                $pecah = $this->input->post('dosen_id', true);
                $pecah = explode(':', $pecah);
                $data['id_dosen'] = $pecah[0];
                $data['id_matkul'] = end($pecah);
            }else{
                $data['id_dosen'] = $this->input->post('dosen_id', true);
                $data['id_matkul'] = $this->input->post('matkul_id', true);
            }

            if($method==='add'){
                //push array
                // $data['created_on'] = time();
                // $data['updated_on'] = time();
                //insert data
                $this->master->create('tb_soal_essay', $data);
            }else if($method==='edit'){
                //push array
                $data['updated_at'] = date("Y-m-d H:i:s");
                //update data
                $id_soal = $this->input->post('id_soal', true);
                $this->master->update('tb_soal_essay', $data, 'id_soal_essay', $id_soal);
            }else{
                show_error('Method tidak diketahui', 404);
            }
            redirect('soal_essay');
        }
    }

    public function detail($id)
    {
        $user = $this->ion_auth->user()->row();
		$data = [
			'user'      => $user,
			'judul'	    => 'Soal Essay',
            'subjudul'  => 'Edit Soal Essay',
            'soal'      => $this->soal_essay->getSoalById($id),
        ];

        $this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('soal_essay/detail');
		$this->load->view('_templates/dashboard/_footer.php');
    }


    public function edit($id)
	{
		$user = $this->ion_auth->user()->row();
		$data = [
			'user'      => $user,
			'judul'	    => 'Soal Essay',
            'subjudul'  => 'Edit Soal Essay',
            'soal'      => $this->soal_essay->getSoalById($id),
        ];
        
        if($this->ion_auth->is_admin()){
            //Jika admin maka tampilkan semua matkul
            $data['dosen'] = $this->soal_essay->getAllDosen();
        }else{
            //Jika bukan maka matkul dipilih otomatis sesuai matkul dosen
            $data['dosen'] = $this->soal_essay->getMatkulDosen($user->username);
        }

		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('soal_essay/edit');
		$this->load->view('_templates/dashboard/_footer.php');
	}


}