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
    
}