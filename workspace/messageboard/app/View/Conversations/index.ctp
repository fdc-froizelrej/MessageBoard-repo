<?php
    // SORT CONVERSATIONS BY LATEST MESSAGE
    usort($conversations, function ($a, $b) {
        $dateA = isset($a['last_created']) ? strtotime($a['last_created']) : 0;
        $dateB = isset($b['last_created']) ? strtotime($b['last_created']) : 0;
        return $dateB - $dateA;
    });
?>
<div class="container">
    <div class="d-flex justify-content-between">
        <h1><?php echo __('Message List'); ?></h1>
        <div>
            <?php echo $this->Html->link(__('New message'), ['action' => 'add'], ['class' => 'btn btn-outline-dark']); ?>
        </div>
    </div>
    <div class="input-group justify-content-start mb-2">
        <?php 
            echo $this->Form->input('search', array('type' => 'text', 'id' => 'search', 'class' => 'form-control', 'placeholder' => 'Search message...', 'label' => false, 'style' => 'resize: none;')); 
            echo $this->Form->button('<i class="fas fa-search"></i>', array('class' => 'btn btn-outline-dark', 'type' => 'button', 'escape' => false, 'id' => 'search-button')); 
        ?>             
    </div>

    <div class="conversations-container row">
        <?php if (empty($conversations)): ?>
            <div class="col-12">
                <div class="alert alert-info">
                    <?php echo __('No conversations found.'); ?>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($conversations as $conversation): ?>
                <div class="col-md-12">
                    <div class="card mb-4" style="cursor: pointer;">
                        <?php
                        $otherUserId = ($conversation['Conversation']['sender_id'] == $currentUserId) ? $conversation['Conversation']['receiver_id'] : $conversation['Conversation']['sender_id'];
                        $otherUser = $users[$otherUserId];

                        $lastMessageContent = $conversation['last_message'];
                        $lastSentDate = !empty($conversation['last_created']) ? h($conversation['last_created']) : '';

                        $lastMessageUserId = isset($conversation['last_message_user_id']) ? $conversation['last_message_user_id'] : null;
                        if ($lastMessageUserId === $currentUserId) {
                            $lastMessageContent = __('You: ') . h($lastMessageContent);
                        }
                        ?>
                        <a href="<?php echo $this->Html->url(['action' => 'view', $conversation['Conversation']['id']]); ?>" style="text-decoration: none; color: inherit;">
                            <div class="card-header d-flex justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="profile-pic me-3">
                                        <img class="img-fluid rounded-circle" src="<?php echo h(!empty($otherUser['profile_picture']) ? $this->Html->url('/uploads/profile_pictures/' . basename($otherUser['profile_picture'])) : 'https://static-00.iconduck.com/assets.00/profile-circle-icon-2048x2048-cqe5466q.png'); ?>" alt="Other User Profile Picture" style="max-width: 100%; height: auto; width: 150px; height: 150px;" />
                                    </div>
                                    <div class="card-title mx-3">
                                        <?php echo h($otherUser['name']); ?>
                                        <div class="text-muted mt-1 d-flex align-items-center">
                                            <?php echo h(strlen($lastMessageContent) > 50 ? substr($lastMessageContent, 0, 50) . '...' : $lastMessageContent); ?>
                                            <small class="text-muted font-italic mx-2"><?php echo $lastSentDate; ?></small>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <?php echo $this->Form->postLink('<i class="fas fa-trash"></i>', ['action' => 'delete', $conversation['Conversation']['id']], ['escape' => false, 'confirm' => __('Are you sure you want to delete conversation with %s?', h($otherUser['name'])), 'class' => 'btn btn-danger']); ?>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <!-- SHOW MORE BUTTON -->
    <?php if ($this->Paginator->hasNext()): ?>
        <div class="text-center col-12">
            <a href="#" id="show-more-conversations" class="mt-3" data-page="<?php echo $this->Paginator->current() + 1; ?>">Show More</a>
        </div>
    <?php endif; ?>
</div>
<script>
    $('#show-more-conversations').on('click', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        
        $.ajax({
            url: '<?php echo $this->Html->url(array('controller' => 'Conversations', 'action' => 'index')); ?>',
            type: 'GET',
            data: { page: page },
            success: function(data) {
                const newConversations = $(data).find('.col-md-12');
                const hasNext = $(data).find('#show-more-conversations').length > 0;

                $('.row').append(newConversations);
                
                if (!hasNext) {
                    $('#show-more-conversations').hide();
                } else {
                    $('#show-more-conversations').data('page', page + 1);
                }
            },
            error: function() {
                alert('Error loading conversations. Please try again.');
            }
        });
    });
    
    $(document).ready(function() {
        $('#search-button').on('click', function() {
            const searchQuery = $('#search').val().trim().toLowerCase();

            $('.col-md-12').show();
            
            $('.col-md-12').filter(function() {
                const otherUserName = $(this).find('.card-title').text().toLowerCase();
                const lastMessageContent = $(this).find('.text-muted').first().text().toLowerCase();
                return !otherUserName.includes(searchQuery) && !lastMessageContent.includes(searchQuery);
            }).hide();
            
            $('#show-more-conversations').hide();
        });

        $('#search').on('keypress', function(e) {
            if (e.which === 13) { 
                $('#search-button').click();
            }
        });
    });
</script>