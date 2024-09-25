<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <h1><?php echo __('User Profile'); ?></h1>
                <?php if (AuthComponent::user() && AuthComponent::user('id') === $user['User']['id']): ?>
                    <div class="text-right">
                        <?php echo $this->Html->link(__('Edit Profile'), ['action' => 'edit', $user['User']['id']], ['class' => 'btn btn-outline-dark']); ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="row">
                <div class="col-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <div class="border rounded p-2 me-2" style="width: 100%; height: auto; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                <?php if (!empty($user['User']['profile_picture'])): ?>
                                    <img class="img-fluid" src="<?php echo h($this->Html->url('/uploads/profile_pictures/' . basename($user['User']['profile_picture']))); ?>" alt="Profile Picture" style="max-width: 100%; height: auto;"/>
                                <?php else: ?>
                                    <img class="img-fluid" src="https://static-00.iconduck.com/assets.00/profile-circle-icon-2048x2048-cqe5466q.png" alt="Default Profile Picture" style="max-width: 100%; height: auto;"/>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-9">
                    <div class="d-flex flex-column">
                        <div class="d-flex align-items-center">
                            <h2 class="text-dark"><?php echo h($user['User']['name']) . ($age !== null ? ', ' . h($age) : ''); ?></h2>
                        </div>
                        <div class="d-flex align-items-center mt-3">
                            <h5 class="text-dark">Gender:&nbsp;</h5>
                            <h5><?php echo h($user['User']['gender']); ?></h5>
                        </div>
                        <div class="d-flex align-items-center mt-3">
                            <h5 class="text-dark">Birthday:&nbsp;</h5>
                            <h5><?php echo !empty($user['User']['birthday']) ? h((new DateTime($user['User']['birthday']))->format('F d, Y')) : __(''); ?></h5>
                        </div>
                        <div class="d-flex align-items-center mt-3">
                            <h5 class="text-dark">Joined:&nbsp;</h5>
                            <h5><?php echo h(date('F d, Y h:i A', strtotime($user['User']['joined_date']))); ?></h5>
                        </div>
                        <div class="d-flex align-items-center mt-3">
                            <h5 class="text-dark">Last Login:&nbsp;</h5>
                            <h5><?php echo h(date('F d, Y h:i A', strtotime($user['User']['last_logged_in']))); ?></h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 mt-4">
                <h4 class="text-dark">Hobby:&nbsp;</h4>
                <p><?php echo h($user['User']['hobby']); ?></p>
            </div>
        </div>
    </div>
</div>
