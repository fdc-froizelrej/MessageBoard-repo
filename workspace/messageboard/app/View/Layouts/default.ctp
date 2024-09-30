<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc.
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

$cakeDescription = __d('cake_dev', 'CakePHP: the rapid development php framework');
$cakeVersion = __d('cake_dev', 'CakePHP %s', Configure::version());
?>
<!DOCTYPE html>
<html>
<head>
    <?php echo $this->Html->charset(); ?>
    <title>
        <?php echo $cakeDescription ?>: <?php echo $this->fetch('title'); ?>
    </title>
    <?php
        echo $this->Html->meta('icon');
        echo $this->Html->css('cake.generic');
        echo $this->Html->css('https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
        echo $this->Html->css('https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
        echo $this->Html->css('https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css');
        echo $this->Html->css('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');
        echo $this->Html->script('https://code.jquery.com/jquery-3.6.0.min.js');
        echo $this->Html->script('https://code.jquery.com/ui/1.12.1/jquery-ui.min.js');
        echo $this->Html->script('https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js');
    ?>
</head>
<body>
    <nav class="navbar navbar-light bg-light mb-4">
        <div class="container">
            <a class="navbar-brand" href="<?php echo $this->Html->url(array('controller' => 'Conversations', 'action' => 'index')); ?>">Messageboard</a>
            <div class="ml-auto d-flex">
                <?php if (AuthComponent::user()): ?>
                    <p class="mx-2 my-2">Welcome, <?php echo h(AuthComponent::user('name'));?>!</p>
                    <div class="dropdown">
                        <button class="btn btn-link p-0" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?php 
                                $userProfilePicture = AuthComponent::user('profile_picture');
                                if (!empty($userProfilePicture)): 
                                    $imageSource = h($this->Html->url('/uploads/profile_pictures/' . basename($userProfilePicture) . '?' . time()));
                            ?>
                                <img src="<?php echo $imageSource; ?>" alt="Profile" class="rounded-circle" style="width: 40px; height: 40px;">
                            <?php else: ?>
                                <img src="https://static-00.iconduck.com/assets.00/profile-circle-icon-2048x2048-cqe5466q.png" alt="Default Profile" class="rounded-circle" style="width: 40px; height: 40px;">
                            <?php endif; ?>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'view', AuthComponent::user('id'))); ?>">Profile</a>
                            <a class="dropdown-item" href="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'logout')); ?>">Logout</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <div id="content">
            <?php echo $this->Flash->render(); ?>
            <?php echo $this->fetch('content'); ?>
        </div>
    </div>
    
    <?php
        echo $this->Html->script('https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js');
        echo $this->Html->script('https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js');
    ?>
</body>
</html>
