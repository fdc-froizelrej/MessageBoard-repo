<?php
// Sort conversations by last_sent_date in descending order
usort($conversations, function ($a, $b) {
	$dateA = isset($a['last_sent_date']) ? strtotime($a['last_sent_date']) : 0;
	$dateB = isset($b['last_sent_date']) ? strtotime($b['last_sent_date']) : 0;
	return $dateB - $dateA;
});
?>

<div class="container">
	<div class="d-flex justify-content-between">
		<h1><?php echo __('Conversations'); ?></h1>
		<div>
			<?php echo $this->Html->link(__('New message'), ['action' => 'add'], ['class' => 'btn btn-outline-dark']); ?>
		</div>
	</div>
	<div class="row">
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
							$lastSentDate = !empty($conversation['last_sent_date']) ? h($conversation['last_sent_date']) : '';

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
											<?php echo $lastMessageContent; ?>
											<small class="text-muted font-italic mx-2"><?php echo $lastSentDate; ?></small>
										</div>
									</div>
								</div>
								<div>
									<?php echo $this->Form->postLink('<i class="fas fa-trash"></i>', array('action' => 'delete', $conversation['Conversation']['id']), array('escape' => false, 'confirm' => __('Are you sure you want to delete conversation with %s?', h($otherUser['name'])), 'class' => 'btn btn-danger')); ?>
								</div>
							</div>
						</a>
					</div>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
</div>