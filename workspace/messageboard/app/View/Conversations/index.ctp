<?php
// SORT CONVERSATIONS BY LATEST MESSAGE
    usort($conversations, function ($a, $b) {
        $dateA = isset($a['last_created']) ? strtotime($a['last_created']) : 0;
        $dateB = isset($b['last_created']) ? strtotime($b['last_created']) : 0;
        return $dateB - $dateA;
    });
?>
<div class="container w-100">
    <div class="d-flex justify-content-between">
        <h1><?php echo __('Message List'); ?></h1>
        <div>
            <?php echo $this->Html->link(__('New message'), array('action' => 'add'), array('class' => 'btn btn-outline-dark')); ?>
        </div>
    </div>
    <div class="input-group justify-content-start mb-2">
        <?php 
            echo $this->Form->input('search', array('type' => 'text', 'id' => 'search', 'class' => 'form-control', 'placeholder' => 'Search message...', 'label' => false, 'style' => 'resize: none;')); 
            echo $this->Form->button('<i class="fas fa-search"></i>', array('class' => 'btn btn-outline-dark', 'type' => 'button', 'escape' => false, 'id' => 'search-button')); 
        ?>             
    </div>

    <div class="conversations-container">
        <?php if (empty($conversations)): ?>
            <div class="col-12">
                <div class="alert alert-info">
                    <?php echo __('No conversations found.'); ?>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($conversations as $conversation): ?>
                    <?= $this->element('conversation', array('conversation' => $conversation, 'currentUserId' => $currentUserId, 'users' => $users)); ?>
                <?php endforeach; ?>
            </div>
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
    $(document).ready(function() {
        $('#show-more-conversations').on('click', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            
            $.ajax({
                url: '<?php echo $this->Html->url(array('controller' => 'Conversations', 'action' => 'index')); ?>',
                type: 'GET',
                data: { page: page },
                success: function(data) {
                    const newConversations = $(data).find('.conversation-card');
                    const hasNext = $(data).find('#show-more-conversations').length > 0;

                    if (newConversations.length > 0) {
                        $('.conversations-container .row').append(newConversations);
                    }

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

        $('#search-button').on('click', function() {
            const searchQuery = $('#search').val().trim().toLowerCase();

            $('.conversation-card').show();
            
            $('.conversation-card').filter(function() {
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

        $('.delete-conversation').on('click', function(e) {
            e.preventDefault();
            const conversationCard = $(this).closest('.conversation-card');
            const conversationId = $(this).attr('id');

            let confirmed = confirm('Are you sure you want to delete this message?');
			if (!confirmed) {
				return;
			}

            $.ajax({
                url: '<?php echo $this->Html->url(array('controller' => 'Conversations', 'action' => 'delete')); ?>' + '/' + conversationId,
                type: 'POST',
                success: function(response) {
                    conversationCard.fadeOut(700, function() {
                        $(this).remove();
                    });

                    console.log('Conversation deleted');
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        });
    });
</script>
