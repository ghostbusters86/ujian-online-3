<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
	use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

class Auth extends CI_Controller
{
	public $data = [];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('form_validation');
		$this->load->helper(['url', 'language']);
		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
		$this->lang->load('auth');

		require APPPATH.'libraries/PHPmailer/src/Exception.php';
        require APPPATH.'libraries/PHPmailer/src/PHPMailer.php';
        require APPPATH.'libraries/PHPmailer/src/SMTP.php';
	}

	public function output_json($data)
	{
		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	public function index()
	{
		if ($this->ion_auth->logged_in()){
			$user_id = $this->ion_auth->user()->row()->id; // Get User ID
			$group = $this->ion_auth->get_users_groups($user_id)->row()->name; // Get user group
			redirect('dashboard');
		}
		$this->data['identity'] = [
			'name' => 'identity',
			'id' => 'identity',
			'type' => 'text',
			'placeholder' => 'Email',
			'autofocus'	=> 'autofocus',
			'class' => 'form-control',
			'autocomplete'=>'off',
			'value' => (get_cookie('uemail')?get_cookie('uemail'):'')
		];
		$this->data['password'] = [
			'name' => 'password',
			'id' => 'password',
			'type' => 'password',
			'placeholder' => 'Password',
			'class' => 'form-control',
			'value' => (get_cookie('upassword')?get_cookie('upassword'):'')
		];
		$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

		$this->load->view('_templates/auth/_header.php');
		$this->load->view('auth/login', $this->data);
		$this->load->view('_templates/auth/_footer.php');
	}

	public function cek_login()
	{
		$this->form_validation->set_rules('identity', str_replace(':', '', $this->lang->line('login_identity_label')), 'required|trim');
		$this->form_validation->set_rules('password', str_replace(':', '', $this->lang->line('login_password_label')), 'required|trim');

		if ($this->form_validation->run() === TRUE)	{
			$remember = (bool)$this->input->post('remember');
			if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember)){
				$this->cek_akses();
				if($remember == TRUE){
					$this->input->set_cookie('uemail', $this->input->post('identity'), 86500); 
					$this->input->set_cookie('upassword', $this->input->post('password'), 86500);
				}else{
					delete_cookie('uemail'); /* Delete email cookie */
                    delete_cookie('upassword'); /* Delete password cookie */
				}
				
			}else {
				$data = [
					'status' => false,
					'failed' => 'Incorrect Login',
				];
				$this->output_json($data);
			}
		}else{
			$invalid = [
				'identity' => form_error('identity'),
				'password' => form_error('password')
			];
			$data = [
				'status' 	=> false,
				'invalid' 	=> $invalid
			];
			$this->output_json($data);
		}
	}

	public function cek_akses()
	{
		if (!$this->ion_auth->logged_in()){
			$status = false; // jika false, berarti login gagal
			$url = 'auth'; // url untuk redirect
		}else{
			$status = true; // jika true maka login berhasil
			$url = 'dashboard';
		}

		$data = [
			'status' => $status,
			'url'	 => $url
		];
		$this->output_json($data);
	}

	public function logout()
	{
		$this->ion_auth->logout();
		redirect('login','refresh');
	}

	/**
	 * Forgot password
	 */
	public function forgot_password()
	{
		$this->data['title'] = $this->lang->line('forgot_password_heading');
		
		// setting validation rules by checking whether identity is username or email
		if ($this->config->item('identity', 'ion_auth') != 'email')
		{
			$this->form_validation->set_rules('identity', $this->lang->line('forgot_password_identity_label'), 'required');
		}
		else
		{
			$this->form_validation->set_rules('identity', $this->lang->line('forgot_password_validation_email_label'), 'required|valid_email');
		}


		if ($this->form_validation->run() === FALSE)
		{
			$this->data['type'] = $this->config->item('identity', 'ion_auth');
			// setup the input
			$this->data['identity'] = [
				'name' 	=> 'identity',
				'id'	=> 'identity',
				'class'	=> 'form-control',
				'autocomplete'	=> 'off',
				'autofocus'	=> 'autofocus'
			];

			if ($this->config->item('identity', 'ion_auth') != 'email')
			{
				$this->data['identity_label'] = $this->lang->line('forgot_password_identity_label');
			}
			else
			{
				$this->data['identity_label'] = $this->lang->line('forgot_password_email_identity_label');
			}

			// set any errors and display the form
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
			$this->load->view('_templates/auth/_header', $this->data);
			$this->load->view('auth/forgot_password');
			$this->load->view('_templates/auth/_footer');
		}
		else
		{
			$identity_column = $this->config->item('identity', 'ion_auth');
			$identity = $this->ion_auth->where($identity_column, $this->input->post('identity'))->users()->row();

			if (empty($identity))
			{

				if ($this->config->item('identity', 'ion_auth') != 'email')
				{
					$this->ion_auth->set_error('forgot_password_identity_not_found');
				}
				else
				{
					$this->ion_auth->set_error('forgot_password_email_not_found');
				}

				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect("auth/forgot_password", 'refresh');
			}

			// run the forgotten password method to email an activation code to the user
			$forgotten = $this->ion_auth->forgotten_password($identity->{$this->config->item('identity', 'ion_auth')});

			if ($forgotten)
			{
				$email_tujuan = $this->input->post('identity');
				$code = uniqid(true); //Untuk kode atau parameter acak
				// PHPMailer object
				$response = false;
				$mail = new PHPMailer();
		
				// SMTP configuration
				$mail->isSMTP();
				$mail->Host     = 'smtp.gmail.com'; //sesuaikan sesuai nama domain hosting/server yang digunakan
				$mail->SMTPAuth = true;
				$mail->Username = 'testinguser3000@gmail.com'; // user email
				$mail->Password = 'iniTesting_user'; // password email
				$mail->SMTPSecure = 'ssl';
				$mail->Port     = 465;
		
				$mail->setFrom('testinguser3000@gmail.com', ''); // user email
				$mail->addReplyTo('no-reply@gmail.com', ''); //user email
		
				// Add a recipient
				$mail->addAddress($email_tujuan); //email tujuan pengiriman email
				
				$url = "http://" . $_SERVER["HTTP_HOST"] .dirname($_SERVER["PHP_SELF"]). "/rst_pwd?reset=$code";
				// Email subject
				$mail->Subject = 'Link reset password'; //subject email
		
				// Set email format to HTML
				$mail->isHTML(true);
		
				// Email body content
				

				$mailContent   = "<h1>Permintaan reset password</h1><p> Klik <a href='$url'>link ini</a> untuk mereset password, link akan kadaluarsa selama 5 menit</p>" ;

				$mail->Body = $mailContent;
		
				// Send email
				if(!$mail->send()){
					echo 'Message could not be sent.';
					echo 'Mailer Error: ' . $mail->ErrorInfo;
				}else{
					$startTime = date("Y-m-d H:i:s");
					$cenvertedTime = date('Y-m-d H:i:s',strtotime('+5 minutes',strtotime($startTime)));
					$this->load->model('reset_password_model');
					$this->reset_password_model->insert_data($email_tujuan, $code, $cenvertedTime);
					$this->session->set_flashdata('success', $this->ion_auth->messages());
					redirect("auth/forgot_password", 'refresh'); //we should display a confirmation page here instead of the login page
				}
				// if there were no errors
				
			}
			else
			{
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect("auth/forgot_password", 'refresh');
			}
		}
	}

	/**
	 * Reset password - final step for forgotten password
	 *
	 * @param string|null $code The reset code
	 */
	public function reset_password($code = NULL)
	{
		if (!$code)
		{
			show_404();
		}

		$this->data['title'] = $this->lang->line('reset_password_heading');
		
		$user = $this->ion_auth->forgotten_password_check($code);

		if ($user)
		{
			// if the code is valid then display the password reset form

			$this->form_validation->set_rules('new', $this->lang->line('reset_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[new_confirm]');
			$this->form_validation->set_rules('new_confirm', $this->lang->line('reset_password_validation_new_password_confirm_label'), 'required');

			if ($this->form_validation->run() === FALSE)
			{
				// display the form

				// set the flash data error message if there is one
				$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

				$this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
				$this->data['new_password'] = [
					'name' => 'new',
					'id' => 'new',
					'type' => 'password',
					'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
				];
				$this->data['new_password_confirm'] = [
					'name' => 'new_confirm',
					'id' => 'new_confirm',
					'type' => 'password',
					'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
				];
				$this->data['user_id'] = [
					'name' => 'user_id',
					'id' => 'user_id',
					'type' => 'hidden',
					'value' => $user->id,
				];
				$this->data['csrf'] = $this->_get_csrf_nonce();
				$this->data['code'] = $code;

				// render
				$this->load->view('_templates/auth/_header');
				$this->load->view('auth/reset_password', $this->data);
				$this->load->view('_templates/auth/_footer');
			}
			else
			{
				$identity = $user->{$this->config->item('identity', 'ion_auth')};

				// do we have a valid request?
				if ($this->_valid_csrf_nonce() === FALSE || $user->id != $this->input->post('user_id'))
				{

					// something fishy might be up
					$this->ion_auth->clear_forgotten_password_code($identity);

					show_error($this->lang->line('error_csrf'));

				}
				else
				{
					// finally change the password
					$change = $this->ion_auth->reset_password($identity, $this->input->post('new'));

					if ($change)
					{
						// if the password was successfully changed
						$this->session->set_flashdata('message', $this->ion_auth->messages());
						redirect("auth/login", 'refresh');
					}
					else
					{
						$this->session->set_flashdata('message', $this->ion_auth->errors());
						redirect('auth/reset_password/' . $code, 'refresh');
					}
				}
			}
		}
		else
		{
			// if the code is invalid then send them back to the forgot password page
			$this->session->set_flashdata('message', $this->ion_auth->errors());
			redirect("auth/forgot_password", 'refresh');
		}
	}

	/**
	 * Activate the user
	 *
	 * @param int         $id   The user ID
	 * @param string|bool $code The activation code
	 */
	public function activate($id, $code = FALSE)
	{
		$activation = FALSE;

		if ($code !== FALSE)
		{
			$activation = $this->ion_auth->activate($id, $code);
		}
		else if ($this->ion_auth->is_admin())
		{
			$activation = $this->ion_auth->activate($id);
		}

		if ($activation)
		{
			// redirect them to the auth page
			$this->session->set_flashdata('message', $this->ion_auth->messages());
			redirect("auth", 'refresh');
		}
		else
		{
			// redirect them to the forgot password page
			$this->session->set_flashdata('message', $this->ion_auth->errors());
			redirect("auth/forgot_password", 'refresh');
		}
	}

	/**
	 * @return array A CSRF key-value pair
	 */
	public function _get_csrf_nonce()
	{
		$this->load->helper('string');
		$key = random_string('alnum', 8);
		$value = random_string('alnum', 20);
		$this->session->set_flashdata('csrfkey', $key);
		$this->session->set_flashdata('csrfvalue', $value);

		return [$key => $value];
	}

	/**
	 * @return bool Whether the posted CSRF token matches
	 */
	public function _valid_csrf_nonce(){
		$csrfkey = $this->input->post($this->session->flashdata('csrfkey'));
		if ($csrfkey && $csrfkey === $this->session->flashdata('csrfvalue'))
		{
			return TRUE;
		}
			return FALSE;
	}

	public function _render_page($view, $data = NULL, $returnhtml = FALSE)//I think this makes more sense
	{

		$viewdata = (empty($data)) ? $this->data : $data;

		$view_html = $this->load->view($view, $viewdata, $returnhtml);

		// This will return html on 3rd argument being true
		if ($returnhtml)
		{
			return $view_html;
		}
	}

	function rst_pwd(){
		$id = $this->input->get('reset');
		// echo $id;
		$this->load->model('reset_password_model');
		$cek = $this->reset_password_model->cekdata($id)->row();
		$tanggalsekarang = date('Y-m-d H:i:s');
		if($tanggalsekarang > $cek->kadaluarsa){
			show_404();
		}else{
			$this->data['token'] = [
				'email' 	=> $cek->email,
			];
			$this->load->view('_templates/auth/_header', $this->data);
			$this->load->view('auth/rst_pwd');
			$this->load->view('_templates/auth/_footer');
		}
	}

	function rst_pwd_save(){
		$reset = $this->input->post('id');
		$password = $this->input->post('reset_pwd');
		$this->load->model('reset_password_model');
		$cek = $this->reset_password_model->cekdata($reset)->row();

		// $this->form_validation->set_rules('new', $this->lang->line('reset_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']');

		// if ($this->form_validation->run() === FALSE){
		// 	echo '<script type="text/javascript">
		// 			alert("Gagal Reset Password");
		// 			window.location = "'.base_url().'/auth/rst_pwd/reset='.$reset.'";
		// 		</script>';
		// }else{
			$identity = $cek->email;
			$change = $this->ion_auth->ubah_password($identity,$password);
			if($change){
				$data = [
					'status' 	=> true,
					'msg'		=> 'Berhasil'
				];
			}
			else{
				$data = [
					'status' 	=> false,
					'msg'		=> $this->ion_auth->errors()
				];
			}
			// print_r($data);
			echo '<script type="text/javascript">
					alert("Password Berhasil diubah");
					window.location = "'.base_url().'";
				</script>';
		// }
	}

}
