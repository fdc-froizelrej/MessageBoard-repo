<?php
    // SORT OTHER CONVERSATIONS BY LATEST MESSAGE
    usort($otherConversations, function ($a, $b) {
        $dateA = isset($a['latestMessage']['Message']['created']) ? strtotime($a['latestMessage']['Message']['created']) : 0;
        $dateB = isset($b['latestMessage']['Message']['created']) ? strtotime($b['latestMessage']['Message']['created']) : 0;
        return $dateB - $dateA;
    });
?>
<style>
    .conversation-list {
        max-height: calc(100vh - 120px); /* Adjust based on your layout */
        overflow-y: auto; /* Enable vertical scrolling */
    }

    .current-conversation {
        max-height: calc(100vh - 120px); /* Same height for consistency */
        overflow-y: auto; /* Enable vertical scrolling */
    }
</style>
<div class="container w-100">
    <div class="row h-100">
        <!-- OTHER CONVERSATIONS -->
        <div class="col-4 conversation list">
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
        <div class="col-8 h-100">
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
                                                    <!-- DELETE BUTTON -->
                                                    <?php if ($message['Message']['user_id'] == $loggedInUserId): ?>
                                                        <div class="text-right">
                                                            <button class="btn btn-outline-danger btn-sm delete-message" id="<?php echo $message['Message']['id']; ?>" type="button" onclick="return confirm('<?php echo __('Are you sure you want to delete this message?'); ?>');">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <!-- MESSAGES -->
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
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.current-conversation').scrollTop($('.current-conversation')[0].scrollHeight);
        // MESSAGES PAGINATION
        $('#show-more').on('click', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            const url = '<?php echo $this->Html->url(array('controller' => 'Conversations', 'action' => 'view', $conversation['Conversation']['id'])); ?>';
            
            $.ajax({
                url: url,
                type: 'GET',
                data: { page: page },
                success: function(data) {
                    const newMessages = $(data).find('#messages-container').html();
                    const hasNext = $(data).find('#show-more').length > 0;

                    $('#messages-container').prepend(newMessages);
                    $('#show-more').toggle(hasNext).data('page', hasNext ? page + 1 : page);
                },
                error: function() {
                    alert('Error loading messages. Please try again.');
                }
            });
        });

        // TOGGLE MESSAGE (ELLIPSIS)
        $(document).on('click', '.toggle-message', function(e) {
            e.preventDefault();
            const $link = $(this);
            const fullMessage = $link.data('full-message');
            const isShowingMore = $link.text() === 'Show Less';

            const newHtml = isShowingMore 
                ? fullMessage + ' <a href="#" class="toggle-message" data-full-message="' + fullMessage + '">Show Less</a>' 
                : fullMessage.substring(0, 100) + '... <a href="#" class="toggle-message" data-full-message="' + fullMessage + '">Show More</a>';

            $link.closest('.message-preview').zhtml(newHtml);
        });

        // Search functionality
        $('#search-button').on('click', function() {
            const searchQuery = $('#search').val().trim().toLowerCase();
            $('#messages-container .alert').each(function() {
                const message = $(this).find('.message-preview').text().toLowerCase();
                $(this).toggle(message.includes(searchQuery));
            });

            $('#show-more').toggle(searchQuery.length === 0 && $('#messages-container .alert:visible').length < 50);
        });

        $('#search').on('keypress', function(e) {
            if (e.which === 13) $('#search-button').click();
        });

        // FADE IN ADD
        $('form').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(data) {
                    const newMessage = $(data).find('#messages-container .alert.alert-primary').last();
                    newMessage.addClass('col-md-8 offset-md-4 fade-in').hide().appendTo('#messages-container').fadeIn();
                },
                error: function() {
                    alert('Error sending message. Please try again.');
                }
            });
        });

        // FADE OUT DELETE
        $('.delete-message').on('click', function() {
            const $messageElement = $(this).closest('.alert');
            const messageId = $(this).attr('id');

            $.ajax({
                url: '<?php echo $this->Html->url(array('controller' => 'Messages', 'action' => 'delete')); ?>' + '/' + messageId,
                type: 'POST',
                success: function() {
                    $messageElement.fadeOut(300, function() {
                        $(this).remove();
                    });
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        });
    });
</script>