<div class="container w-100">
    <div class="row">
        <div class="col-4">
            <ul class="list-group">
                <?php echo $this->Html->link(__('New message'), ['action' => 'add'], ['class' => 'btn btn-outline-dark w-100 mb-2']); ?>
                <?php foreach ($otherConversations as $conv): ?>
                    <?php
                        $otherConvUserId = ($conv['Conversation']['sender_id'] == $loggedInUserId) ? 
                            $conv['Conversation']['receiver_id'] : 
                            $conv['Conversation']['sender_id'];
                        $otherConvUser = $users[$otherConvUserId];

                        $isActive = $conv['Conversation']['id'] == $conversation['Conversation']['id'];

                        $lastMessageUserId = isset($conv['latestMessage']['Message']['user_id']) ? $conv['latestMessage']['Message']['user_id'] : null;
                        $lastMessageContent = isset($conv['latestMessage']['Message']['content']) ? $conv['latestMessage']['Message']['content'] : '';
                        if ($lastMessageUserId === $loggedInUserId) {
                            $lastMessageContent = __('You: ') . ($lastMessageContent);
                        }
                    ?>
                    <li class="list-group-item <?php echo $isActive ? 'bg-light text-muted' : ''; ?>">
                        <a href="<?php echo $this->Html->url(['controller' => 'Conversations', 'action' => 'view', $conv['Conversation']['id']]); ?>">
                            <img src="<?php echo h(!empty($otherConvUser['profile_picture']) ? $this->Html->url('/uploads/profile_pictures/' . basename($otherConvUser['profile_picture'])) : 'https://static-00.iconduck.com/assets.00/profile-circle-icon-2048x2048-cqe5466q.png'); ?>" alt="User's Profile Picture" class="img-fluid rounded-circle" style="width: 50px; height: 50px;"/> 
                            <span class="text-dark" style="text-decoration: none;"><?php echo h($otherConvUser['name']); ?></span>
                        </a>
                        <?php if (!empty($conv['latestMessage'])): ?>
                            <div class="small">
                                <p><?php echo h($lastMessageContent); ?></p>
                                <p class="mb-0"><?php echo h(date('M j, Y, g:i A', strtotime($conv['latestMessage']['Message']['sent_date']))); ?></p>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="col-8">
            <div class="card">
                <div class="card-body">
                    <?php 
                        $otherUserId = ($conversation['Conversation']['sender_id'] == $loggedInUserId) ? $conversation['Conversation']['receiver_id'] : $conversation['Conversation']['sender_id'];
                        $otherUser = $users[$otherUserId]; 
                        echo $this->Form->create('Message', array('url' => array('controller' => 'Messages', 'action' => 'add', $conversation['Conversation']['id']))); 
                    ?>
        
                    <div class="row align-items-center mb-2">
                        <div class="col-md-8">
                            <?php echo $this->Form->input('content', array('type' => 'textarea', 'class' => 'form-control', 'rows' => '1', 'placeholder' => 'Type your message...', 'required' => true, 'label' => false, 'style' => 'resize: none; width: 100%;')); ?>
                        </div>
                        <div class="col-md-4">
                            <?php echo $this->Form->button('Reply message', array('class' => 'btn btn-outline-dark mb-3 w-100')); ?>
                            <?php echo $this->Form->end(); ?>
                        </div>
                    </div>
        
                    <div class="d-flex align-items-center mx-3" style="margin-top: -2em;">
                        <div class="profile-pic">
                            <a href="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'view', $otherUserId)); ?>">
                                <img class="img-fluid rounded-circle" src="<?php echo h(!empty($otherUser['profile_picture']) ? $this->Html->url('/uploads/profile_pictures/' . basename($otherUser['profile_picture'])) : 'https://static-00.iconduck.com/assets.00/profile-circle-icon-2048x2048-cqe5466q.png'); ?>" alt="User's Profile Picture" style="width: 70px; height: 70px;" />
                            </a>
                        </div>
                        <h3 class="text-dark mx-2"><?php echo h($otherUser['name']); ?></h3>
                    </div>
                    <?php if (!empty($messages)): ?>
                        <div class="container mt-1">
                            <div id="messages-container">
                                <?php foreach ($messages as $message): ?>
                                    <div class="row mb-2">
                                        <div class="<?php echo ($message['Message']['user_id'] == $loggedInUserId) ? 'col-md-6 offset-md-6' : 'col-md-6'; ?>">
                                            <div class="alert <?php echo ($message['Message']['user_id'] == $loggedInUserId) ? 'alert-primary' : 'alert-secondary'; ?>">
                                                <div class="d-flex justify-content-between">
                                                    <strong><?php echo ($message['Message']['user_id'] == $loggedInUserId) ? 'You' : h($otherUser['name']); ?>:</strong>
                                                    <?php if ($message['Message']['user_id'] == $loggedInUserId): ?>
                                                        <div class="text-right">
                                                            <?php echo $this->Form->postLink('<i class="fas fa-trash"></i>', array('controller' => 'Messages', 'action' => 'delete', $message['Message']['id'], $conversation['Conversation']['id']), array('escape' => false, 'confirm' => __('Are you sure you want to delete this message?'), 'class' => 'btn btn-outline-danger btn-sm')); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <?php echo h($message['Message']['content']); ?>
                                                <div class="text-muted small text-right"><?php echo h((new DateTime($message['Message']['sent_date']))->format('M d, Y h:i A')); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <?php if ($this->Paginator->hasNext()): ?>
                                <div class="text-center">
                                    <a href="#" id="show-more" class="mt-3" data-page="<?php echo $this->Paginator->current() + 1; ?>">Show More</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="container">
                            <div class="alert alert-info my-5">
                                <?php echo __('No messages yet.'); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('#show-more').on('click', function() {
        const page = $(this).data('page');
        const conversationId = '<?php echo $conversation['Conversation']['id']; ?>';
        
        $.ajax({
            url: '<?php echo $this->Html->url(array('controller' => 'Conversations', 'action' => 'view', $conversation['Conversation']['id'])); ?>',
            type: 'GET',
            data: { page: page },
            success: function(data) {
                const newMessages = $(data).find('#messages-container').html();
                $('#messages-container').append(newMessages); 

                const hasNext = $(data).find('#show-more').length > 0;
                if (!hasNext) {
                    $('#show-more').hide(); 
                } else {
                    $('#show-more').data('page', page + 1); 
                }
            },
            error: function() {
                alert('Error loading messages. Please try again.');
            }
        });
    });
</script>