<div class="container mt-5">
    <div class="card text-center">
        <div class="card-body">
            <h1>Thank you for registering.</h1>
            <div class="mt-4">
                <?php
                    echo $this->Html->link('Back to homepage', array('controller' => 'users', 'action' => 'index'), array('class' => 'btn btn-primary'));
                ?>
            </div>
        </div>
    </div>
</div>
