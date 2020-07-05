<div class="login-box pt-5">
	<!-- /.login-logo -->
	<div class="login-logo">
		<a href="<?=base_url('login')?>"><b>CBT</b>APP</a>
	</div>

	<div class="login-box-body">
		<h3 class="text-center mt-0 mb-4">
			<?php echo lang('forgot_password_heading');?>
		</h3>
		<p class="login-box-msg">
		</p>

		<div id="infoMessage" class="text-red text-center"><?php echo $message;?></div>

		<?php echo form_open("auth/forgot_password");?>

			<p>
				<label for="identity"><input type="text" name="rst_pwd" class="form-control" ></label> <br />
				
			</p>

			<p><?php echo form_submit('submit', 'Forgot Password', ['class'=>'btn btn-primary btn-flat btn-block']);?></p>

		<?php echo form_close();?>

    </div>
</div>