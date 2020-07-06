<div class="login-box pt-5">
	<!-- /.login-logo -->
	<div class="login-logo">
		<a href="<?=base_url('login')?>"><b>CBT</b>APP</a>
	</div>

	<div class="login-box-body">
		<h3 class="text-center mt-0 mb-4">
			Reset Password
		</h3>
		<p class="login-box-msg">
		</p>

		<?php echo form_open("auth/rst_pwd_save");?>

			<p>
				<label for="identity">Masukkan Password Baru</label> <br />
				<input type="password" name="reset_pwd" class="form-control" >
				<input type="hidden" name="id" class="form-control" value="<?php echo $this->input->get('reset') ?>" readonly>
			</p>

			<p><?php echo form_submit('submit', 'Reset Password', ['class'=>'btn btn-primary btn-flat btn-block']);?></p>

		<?php echo form_close();?>

    </div>
</div>