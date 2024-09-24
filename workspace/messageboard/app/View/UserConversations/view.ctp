<div class="userConversations view">
<h2><?php echo __('User Conversation'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($userConversation['UserConversation']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('User'); ?></dt>
		<dd>
			<?php echo $this->Html->link($userConversation['User']['name'], array('controller' => 'users', 'action' => 'view', $userConversation['User']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Conversation'); ?></dt>
		<dd>
			<?php echo $this->Html->link($userConversation['Conversation']['id'], array('controller' => 'conversations', 'action' => 'view', $userConversation['Conversation']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Is Deleted'); ?></dt>
		<dd>
			<?php echo h($userConversation['UserConversation']['is_deleted']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit User Conversation'), array('action' => 'edit', $userConversation['UserConversation']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete User Conversation'), array('action' => 'delete', $userConversation['UserConversation']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $userConversation['UserConversation']['id']))); ?> </li>
		<li><?php echo $this->Html->link(__('List User Conversations'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User Conversation'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User'), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Conversations'), array('controller' => 'conversations', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Conversation'), array('controller' => 'conversations', 'action' => 'add')); ?> </li>
	</ul>
</div>
