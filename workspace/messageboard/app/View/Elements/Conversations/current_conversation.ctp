<div class="card">
    <div class="card-body">
        <?php 
            $otherUserId = ($conversation['Conversation']['sender_id'] == $loggedInUserId) ? $conversation['Conversation']['receiver_id'] : $conversation['Conversation']['sender_id'];
            $otherUser = $users[$otherUserId]; 
        ?>
        <div class="d-flex align-items-center mx-3">
            <div class="profile-pic">
                <a href="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'view', $otherUserId)); ?>">
                    <img class="img-fluid rounded-circle" src="<?php echo h(!empty($otherUser['profile_picture']) ? $this->Html->url('/uploads/profile_pictures/' . basename($otherUser['profile_picture'])) : 'https://static-00.iconduck.com/assets.00/profile-circle-icon-2048x2048-cqe5466q.png'); ?>" alt="User's Profile Picture" style="width: 70px; height: 70px;" />
                </a>
            </div>
            <h3 class="text-dark mx-2"><?php echo h($otherUser['name']); ?></h3>
        </div>

        <?php if (!empty($messages)): ?>
            <div class="container current-conversation mt-1">
                <!-- SHOW MORE BUTTON -->
                <?php if ($this->Paginator->hasNext()): ?>
                    <div class="text-center">
                        <a href="#" id="show-more" class="mt-3" data-page="<?php echo $this->Paginator->current() + 1; ?>">Show More</a>
                    </div>
                <?php endif; ?>

                <!-- MESSAGE CONTAINER -->
                <div id="messages-container">
                    <?php foreach (array_reverse($messages) as $message): ?>
                        <div class="row mb-2">
                            <div class="<?php echo ($message['Message']['user_id'] == $loggedInUserId) ? 'col-md-8 offset-md-4' : 'col-md-8'; ?>">
                                <div class="alert <?php echo ($message['Message']['user_id'] == $loggedInUserId) ? 'alert-primary' : 'alert-secondary'; ?>">
                                    <div class="d-flex justify-content-between">
                                        <strong><?php echo ($message['Message']['user_id'] == $loggedInUserId) ? 'You' : h($otherUser['name']); ?>:</strong>
                                        <?php if ($message['Message']['user_id'] == $loggedInUserId): ?>
                                            <div class="text-right">
                                                <button class="btn btn-outline-danger btn-sm delete-message" id="<?php echo $message['Message']['id']; ?>" type="button">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="message-preview">
                                        <?php if (strlen($message['Message']['content']) > 100): ?>
                                            <?php echo h(substr($message['Message']['content'], 0, 100)) . '... '; ?>
                                            <a href="#" class="toggle-message" data-full-message="<?php echo h($message['Message']['content']); ?>">Show More</a>
                                        <?php else: ?>
                                            <?php echo h($message['Message']['content']); ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-muted small text-right"><?php echo h((new DateTime($message['Message']['created']))->format('M d, Y h:i A')); ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="container">
                <div class="alert alert-info my-5">
                    <?php echo __('No messages yet.'); ?>
                </div>
            </div>
        <?php endif; ?>
        <!-- SHOW IF NO RESULTS IN SEARCH -->
        <div id="no-messages" style="display: none; text-align: center; color: gray;">
            No messages found.
        </div>

        <!-- REPLY MESSAGE -->
        <div class="input-group justify-content-end">
            <?php
                echo $this->Form->create('Message', array('url' => array('controller' => 'Messages', 'action' => 'add', $conversation['Conversation']['id']))); 
            ?>
            <div class="input-group">
                <?php echo $this->Form->input('content', array('type' => 'textarea', 'class' => 'form-control', 'rows' => '1', 'placeholder' => 'Type your message...', 'required' => true, 'label' => false, 'style' => 'resize: none;')); ?>
                <div class="input-group-append">
                    <?php
                        echo $this->Form->button('Reply message', array('class' => 'btn btn-outline-dark'));
                        echo $this->Form->end(); 
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
