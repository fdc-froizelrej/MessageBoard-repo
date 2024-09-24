<div class="container mt-4">
    <?php if (!empty($conversation['Message'])): ?>
        <?php 
            $otherUserId = ($conversation['Conversation']['sender_id'] == $loggedInUserId) ? $conversation['Conversation']['receiver_id'] : $conversation['Conversation']['sender_id'];
            $otherUser = $users[$otherUserId]; 

            echo $this->Form->create('Message', array('url' => ['controller' => 'Messages', 'action' => 'add', $conversation['Conversation']['id']])); 
            echo $this->Form->input('content', array('type' => 'textarea', 'class' => 'form-control', 'rows' => '2', 'placeholder' => 'Type your message...', 'required' => true, 'label' => false));
        ?>
        <div class="text-right">
            <?php	
                echo $this->Form->button('Reply message', array('class' => 'btn btn-outline-dark'));
                echo $this->Form->end();
            ?>
        </div>

        <div class="d-flex align-items-center my-3">
            <div class="profile-pic">
                <img class="img-fluid rounded-circle" src="<?php echo h(!empty($otherUser['profile_picture']) ? $this->Html->url('/uploads/profile_pictures/' . basename($otherUser['profile_picture'])) : 'https://static-00.iconduck.com/assets.00/profile-circle-icon-2048x2048-cqe5466q.png'); ?>" alt="Other User Profile Picture" style="width: 50px; height: 50px;" />
            </div>
            <h3 class="text-dark mx-2"><?php echo h($otherUser['name']); ?></h3>
        </div>
        
        <div class="chatbox">
            <?php foreach ($conversation['Message'] as $message): ?>
                <div class="row mb-2">
                    <div class="<?php echo ($message['user_id'] == $loggedInUserId) ? 'col-md-8 offset-md-4' : 'col-md-8'; ?>">
                        <div class="alert <?php echo ($message['user_id'] == $loggedInUserId) ? 'alert-primary' : 'alert-secondary'; ?>">
                            <strong>
                                <?php echo ($message['user_id'] == $loggedInUserId) ? 'You' : h($otherUser['name']); ?>:
                            </strong>
                            <?php echo h($message['content']); ?>
                            <div class="text-muted small"><?php echo h((new DateTime($message['sent_date']))->format('M d, Y h:i A')); ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <?php echo __('No messages found.'); ?>
        </div>
    <?php endif; ?>
</div>
