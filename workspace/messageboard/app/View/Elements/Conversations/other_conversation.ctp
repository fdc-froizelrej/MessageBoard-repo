<?php
// SORT OTHER CONVERSATIONS BY LATEST MESSAGE
usort($otherConversations, function ($a, $b) {
    $dateA = isset($a['latestMessage']['Message']['created']) ? strtotime($a['latestMessage']['Message']['created']) : 0;
    $dateB = isset($b['latestMessage']['Message']['created']) ? strtotime($b['latestMessage']['Message']['created']) : 0;
    return $dateB - $dateA;
});
?>

<div class="text-right mr-4">
    <?php echo $this->Html->link(__('New message'), ['action' => 'add'], ['class' => 'btn btn-outline-dark mb-2']); ?>
</div>
<ul class="list-group conversation-list">
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
            <a href="<?php echo $this->Html->url(['controller' => 'Conversations', 'action' => 'view', $conv['Conversation']['id']]); ?>" class="<?php echo $isActive ? 'text-light text-decoration-none' : 'text-dark text-decoration-none'; ?>">
                <img src="<?php echo h(!empty($otherConvUser['profile_picture']) ? $this->Html->url('/uploads/profile_pictures/' . basename($otherConvUser['profile_picture'])) : 'https://static-00.iconduck.com/assets.00/profile-circle-icon-2048x2048-cqe5466q.png'); ?>" alt="User's Profile Picture" class="img-fluid rounded-circle" style="width: 50px; height: 50px;"/> 
                <?php echo h($otherConvUser['name']); ?>
            </a>
            <?php if (!empty($conv['latestMessage'])): ?>
                <div class="small">
                    <p><?php echo h(strlen($lastMessageContent) > 50 ? substr($lastMessageContent, 0, 50) . '...' : $lastMessageContent); ?></p>
                    <p><?php echo h(date('M j, Y, g:i A', strtotime($conv['latestMessage']['Message']['created']))); ?></p>
                </div>
            <?php else: ?>
                <div class="small">
                    <p><?php echo __('No messages yet.'); ?></p>
                </div>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>
