<div class="col-md-12 conversation-card" data-conversation-id="<?php echo h($conversation['Conversation']['id']) ?>">
    <div class="card shadow mb-4" style="cursor: pointer;">
        <?php
        $otherUserId = ($conversation['Conversation']['sender_id'] == $currentUserId) ? $conversation['Conversation']['receiver_id'] : $conversation['Conversation']['sender_id'];
        $otherUser = $users[$otherUserId];

        $lastMessageContent = $conversation['last_message'];
        $lastSentDate = !empty($conversation['last_created']) ? h($conversation['last_created']) : '';

        $lastMessageUserId = isset($conversation['last_message_user_id']) ? $conversation['last_message_user_id'] : null;
        if ($lastMessageUserId === $currentUserId) {
            $lastMessageContent = __('You: ') . ($lastMessageContent);
        }
        ?>
        <a href="<?php echo $this->Html->url(array('action' => 'view', $conversation['Conversation']['id'])); ?>" style="text-decoration: none; color: inherit;">
            <div class="card-header d-flex justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="profile-pic me-3">
                        <img class="img-fluid rounded-circle" src="<?php echo h(!empty($otherUser['profile_picture']) ? $this->Html->url('/uploads/profile_pictures/' . basename($otherUser['profile_picture'])) : 'https://static-00.iconduck.com/assets.00/profile-circle-icon-2048x2048-cqe5466q.png'); ?>" alt="Other User Profile Picture" style="max-width: 100%; height: auto; width: 100px; height: 100px;" />
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
                    <button class="btn btn-danger btn-sm delete-conversation" id="<?php echo $conversation['Conversation']['id']; ?>" type="button">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </a>
    </div>
</div>
