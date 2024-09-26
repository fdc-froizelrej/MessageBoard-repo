<?php
    // SORT OTHER CONVERSATIONS BY LATEST MESSAGE
    usort($otherConversations, function ($a, $b) {
        $dateA = isset($a['latestMessage']['Message']['created']) ? strtotime($a['latestMessage']['Message']['created']) : 0;
        $dateB = isset($b['latestMessage']['Message']['created']) ? strtotime($b['latestMessage']['Message']['created']) : 0;
        return $dateB - $dateA;
    });
?>
<div class="container w-100">
    <div class="row">

        <!-- OTHER CONVERSATIONS -->
        <div class="col-4">
            <div class="text-right mr-4">
                <?php echo $this->Html->link(__('New message'), array('action' => 'add'), array('class' => 'btn btn-outline-dark mb-2')); ?>
            </div>
            <ul class="list-group">
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
                    <li class="list-group-item <?php echo $isActive ? 'bg-secondary text-light' : 'bg-light text-dark'; ?>">
                        <a href="<?php echo $this->Html->url(array('controller' => 'Conversations', 'action' => 'view', $conv['Conversation']['id'])); ?>" class="<?php echo $isActive ? 'text-light text-decoration-none' : 'text-dark text-decoration-none'; ?>">
                            <img src="<?php echo h(!empty($otherConvUser['profile_picture']) ? $this->Html->url('/uploads/profile_pictures/' . basename($otherConvUser['profile_picture'])) : 'https://static-00.iconduck.com/assets.00/profile-circle-icon-2048x2048-cqe5466q.png'); ?>" alt="User's Profile Picture" class="img-fluid rounded-circle" style="width: 50px; height: 50px;"/> 
                            <?php echo h($otherConvUser['name']); ?>
                        </a>
                        <?php if (!empty($conv['latestMessage'])): ?>
                            <div class="small">
                                <p><?php echo h(strlen($lastMessageContent) > 50 ? substr($lastMessageContent, 0, 50) . '...' : $lastMessageContent); ?></p>
                                <p><?php echo h(date('M j, Y, g:i A', strtotime($conv['latestMessage']['Message']['created']))); ?></p>
                            </div>
                            <?php else:?>
                                <div class="small">
                                    <p><?php echo __('No messages yet.'); ?></p>
                                </div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- CURRENT CONVERSATION -->
        <div class="col-8">
            <!-- SEARCH MESSAGE -->
            <div class="input-group justify-content-end mb-2">
                <?php 
                    echo $this->Form->input('search', array('type' => 'text', 'id' => 'search', 'class' => 'form-control', 'placeholder' => 'Search message...', 'label' => false, 'style' => 'resize: none;')); 
                    echo $this->Form->button('<i class="fas fa-search"></i>', array('class' => 'btn btn-outline-dark', 'type' => 'button', 'escape' => false, 'id' => 'search-button')); 
                ?>             
            </div>

            <div class="card">
                <div class="card-body">
                    <?php 
                        $otherUserId = ($conversation['Conversation']['sender_id'] == $loggedInUserId) ? $conversation['Conversation']['receiver_id'] : $conversation['Conversation']['sender_id'];
                        $otherUser = $users[$otherUserId]; 
                        echo $this->Form->create('Message', array('url' => array('controller' => 'Messages', 'action' => 'add', $conversation['Conversation']['id']))); 
                    ?>
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <?php echo $this->Form->input('content', array('type' => 'textarea', 'class' => 'form-control', 'rows' => '1', 'placeholder' => 'Type your message...', 'required' => true, 'label' => false, 'style' => 'resize: none;')); ?>
                        </div>
                        <div class="col-md-4 text-right">
                            <?php echo $this->Form->button('Reply message', array('class' => 'btn btn-outline-dark mb-3 ')); ?>
                            <?php echo $this->Form->end(); ?>
                        </div>
                    </div>
        
                    <div class="d-flex align-items-center mx-3">
                        <div class="profile-pic">
                            <a href="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'view', $otherUserId)); ?>">
                                <img class="img-fluid rounded-circle" src="<?php echo h(!empty($otherUser['profile_picture']) ? $this->Html->url('/uploads/profile_pictures/' . basename($otherUser['profile_picture'])) : 'https://static-00.iconduck.com/assets.00/profile-circle-icon-2048x2048-cqe5466q.png'); ?>" alt="User's Profile Picture" style="width: 70px; height: 70px;" />
                            </a>
                        </div>
                        <h3 class="text-dark mx-2"><?php echo h($otherUser['name']); ?></h3>
                    </div>
                    <?php if (!empty($messages)): ?>
                        <div class="container mt-1">

                            <!-- SHOW MORE BUTTON -->
                            <?php if ($this->Paginator->hasNext()): ?>
                                <div class="text-center">
                                    <a href="#" id="show-more" class="mt-3" data-page="<?php echo $this->Paginator->current() + 1; ?>">Show More</a>
                                </div>
                            <?php endif; ?>

                            <!-- MESSAGES -->
                            <div id="messages-container">
                                <?php foreach (array_reverse($messages) as $message): ?>
                                    <div class="row mb-2">
                                        <div class="<?php echo ($message['Message']['user_id'] == $loggedInUserId) ? 'col-md-8 offset-md-4' : 'col-md-8'; ?>">
                                            <div class="alert <?php echo ($message['Message']['user_id'] == $loggedInUserId) ? 'alert-primary' : 'alert-secondary'; ?>">
                                                <div class="d-flex justify-content-between">
                                                    <strong><?php echo ($message['Message']['user_id'] == $loggedInUserId) ? 'You' : h($otherUser['name']); ?>:</strong>
                                                    <?php if ($message['Message']['user_id'] == $loggedInUserId): ?>
                                                        <div class="text-right">
                                                            <?php echo $this->Form->postLink('<i class="fas fa-trash"></i>', array('controller' => 'Messages', 'action' => 'delete', $message['Message']['id'], $conversation['Conversation']['id']), array('escape' => false, 'confirm' => __('Are you sure you want to delete this message?'), 'class' => 'btn btn-outline-danger btn-sm')); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="message-preview">
                                                    <?php if (strlen($message['Message']['content']) > 50): ?>
                                                        <?php echo h(substr($message['Message']['content'], 0, 50)) . '... '; ?>
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
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('#show-more').on('click', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        
        $.ajax({
            url: '<?php echo $this->Html->url(array('controller' => 'Conversations', 'action' => 'view', $conversation['Conversation']['id'])); ?>',
            type: 'GET',
            data: { page: page},
            success: function(data) {
                const newMessages = $(data).find('#messages-container').html();
                const hasNext = $(data).find('#show-more').length > 0;

                $('#messages-container').prepend(newMessages);
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
    
    $(document).on('click', '.toggle-message', function(e) {
        e.preventDefault();
        const $link = $(this);
        const fullMessage = $link.data('full-message');
        
        if ($link.text() === 'Show More') {
            $link.closest('.message-preview').html(fullMessage + ' <a href="#" class="toggle-message" data-full-message="' + fullMessage + '">Hide</a>');
        } else {
            const truncatedMessage = fullMessage.substring(0, 50) + '... ';
            $link.closest('.message-preview').html(truncatedMessage + ' <a href="#" class="toggle-message" data-full-message="' + fullMessage + '">Show More</a>');
        }
    });

    $(document).ready(function() {
        $('#search-button').on('click', function() {
            const searchQuery = $('#search').val().trim().toLowerCase();

            $('#messages-container .alert').each(function() {
                const message = $(this).find('.message-preview').text().toLowerCase();
                if (message.includes(searchQuery)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });

            if (searchQuery.length > 0) {
                $('#show-more').hide();
            } else {
                if ($('#messages-container .alert:visible').length < 50) { 
                    $('#show-more').show();
                }
            }
        });

        $('#search').on('keypress', function(e) {
            if (e.which === 13) { 
                $('#search-button').click();
            }
        });
    });
</script>