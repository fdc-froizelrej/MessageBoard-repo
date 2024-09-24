<div class="container mt-5">
    <div class="card">
        <div class="card-body">
        <h1>Log in</h1>
        <?php
            echo $this->Form->create('User', array('class' => 'form-horizontal'));
            echo $this->Form->input('email', array('class' => 'form-control', 'label' => 'Email'));
            echo $this->Form->input('password', array('class' => 'form-control', 'label' => 'Password'));
        ?>
        <div class="text-right">
            <?php
                echo $this->Form->button(__('Log in'), ['class' => 'btn btn-outline-dark']);
                echo $this->Form->end();
            ?>
        </div>

        <div class="mt-3 text-center">
            <?php 
                echo 'Don\'t have an account? ' . $this->Html->link('Sign up', array('controller' => 'users', 'action' => 'register'), array('class' => 'btn btn-link mb-1'));
            ?>
        </div>
    </div>
</div>
