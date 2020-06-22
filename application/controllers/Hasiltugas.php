<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Hasiltugas extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if (!$this->ion_auth->logged_in()){
			redirect('auth');
		}
		
		$this->load->library(['datatables']);// Load Library Ignited-Datatables
		$this->load->model('Master_model', 'master');
		$this->load->model('Tugas_model', 'tugas');
		
		$this->user = $this->ion_auth->user()->row();
	}
	
	public function output_json($data, $encode = true)
	{
		if($encode) $data = json_encode($data);
		$this->output->set_content_type('application/json')->set_output($data);
	}

	public function data()
	{
		$nip_dosen = null;
		
		if( $this->ion_auth->in_group('dosen') ) {
			$nip_dosen = $this->user->username;
		}

		$this->output_json($this->tugas->getHasilTugas($nip_dosen), false);
	}
    
    public function index()
	{
		$data = [
			'user' => $this->user,
			'judul'	=> 'Tugas',
			'subjudul'=> 'Hasil Tugas',
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('tugas/hasil');
		$this->load->view('_templates/dashboard/_footer.php');
	}
	
	 function detailHasilTugas($id){
		 $data = $this->tugas->detailHasilTugas($id)->result();
		 header('Content-Type: application/json');
		 echo json_encode($data);
	 }

	 function preview($id){
		$path = FCPATH.'uploads/tugasMahasiswa/';
		$file = $path.$id;
		$app= new COM("Word.Application");
		$app->visible = true; 
		$app->Documents->Open($file);
		$app->ActiveDocument->PrintOut();
		$app->ActiveDocument->Close(); $app->Quit();
	 }

    
}