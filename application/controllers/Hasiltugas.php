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

	 public function import($import_data = null)
	{
		$data = [
			'user' => $this->ion_auth->user()->row(),
			'judul'	=> 'Jurusan',
			'subjudul' => 'Import Jurusan'
		];
		if ($import_data != null) $data['import'] = $import_data;

		// print_r($data['import']);

		$this->load->view('_templates/dashboard/_header', $data);
		$this->load->view('tugas/import');
		$this->load->view('_templates/dashboard/_footer');
	}

	 function preview($id, $ext){
		$file = FCPATH.'uploads/tugasMahasiswa/';
		$ext = $ext;

			switch ($ext) {
				case '.xlsx':
					$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
					break;
				case '.xls':
					$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
					break;
				case '.csv':
					$reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
					break;
				default:
					echo "unknown file ext";
					die;
			}

			$spreadsheet = $reader->load($file.$id);
			$sheetData = $spreadsheet->getActiveSheet()->toArray();
			$jurusan = [];
			for ($i = 1; $i < count($sheetData); $i++) {
				if ($sheetData[$i][0] != null) {
					$jurusan[] = $sheetData[$i][0];
				}
			}

			// unlink($file);

			$this->import($jurusan);
	 }

    
}