<div class="container">
	<div class="card">
		<div class="card-body">
			<h1>Registration</h1>
			<?php
				echo $this->Form->create('User', array('class' => 'form-horizontal'));
				echo $this->Form->input('name', array('class' => 'form-control', 'label' => 'Name'));
				echo $this->Form->input('email', array('class' => 'form-control', 'label' => 'Email'));
				echo $this->Form->input('password', array('class' => 'form-control', 'label' => 'Password'));
				echo $this->Form->input('confirm_password', array('type' => 'password', 'class' => 'form-control', 'label' => 'Confirm Password'));
			?>
			<div class="d-flex justify-content-end">
				<?php
					echo $this->Form->button(__('Register'), array('class' => 'btn btn-outline-dark'));
					echo $this->Form->end();
				?>
			</div>
			<div class="mt-3 text-center">
				<?php 
					echo 'Already have an account? ' .$this->Html->link('Sign in', array('controller' => 'users', 'action' => 'login'), array('class' => 'btn btn-link mb-1'));
				?>
			</div>
        </div>
	</div>
</div>