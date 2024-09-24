<div class="container">
    <div class="card">
        <div class="card-body">
            <h1><?php echo __('New Message'); ?></h1>
            <?php
                echo $this->Form->create('Message', array('class' => 'form-horizontal'));
                echo $this->Form->input('user_id', array('class' => 'form-control', 'label' => 'To:'));
                echo $this->Form->input('content', array('class' => 'form-control', 'label' => 'Message'));
            ?>
            <div class="d-flex justify-content-end">
                <?php
					echo $this->Html->link(__('Cancel'), array('controller' => 'Conversations', 'action' => 'index'), array('class' => 'btn btn-outline-danger mx-3', 'role' => 'button'));
                    echo $this->Form->button(__('Send'), ['class' => 'btn btn-outline-success']);
                    echo $this->Form->end();
                ?>
            </div>
        </div>
    </div>
</div>
