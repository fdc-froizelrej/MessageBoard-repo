<style>
    .current-conversation {
        max-height: calc(62vh - 120px);
        overflow-y: auto; 
    }
</style>
<div class="container w-100">
    <div class="d-flex">
        <button class="btn btn-outline-dark mb-3 mr-3" onclick="window.location.href='<?php echo $this->Html->url(array('action' => 'index')); ?>'">Back</button>
        <div class="input-group mb-3">
            <input type="text" id="search" class="form-control" placeholder="Search messages...">
            <div class="input-group-append">
                <button id="clear-button" class="btn btn-outline-dark" type="button">Clear</button>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center mx-3">
                <div class="profile-pic">
                    <a href="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'view', $otherUser['User']['id'])); ?>">
                        <img class="img-fluid rounded-circle" src="<?php echo h(!empty($otherUser['User']['profile_picture']) ? $this->Html->url('/uploads/profile_pictures/' . basename($otherUser['User']['profile_picture'])) : 'https://static-00.iconduck.com/assets.00/profile-circle-icon-2048x2048-cqe5466q.png'); ?>" alt="User's Profile Picture" style="width: 70px; height: 70px;" />
                    </a>
                </div>
                <h3 class="text-dark mx-2"><?php echo h($otherUser['User']['name']); ?></h3>
            </div>

            <!-- REPLY MESSAGE -->
            <div class="input-group w-100 my-3 justify-content-end">
                <textarea name="content" class="content form-control" rows="2" placeholder="Type your message...." style="resize:none"></textarea>
                <button class="reply-message btn btn-outline-dark">Reply message</button>
            </div>

            <div class="container current-conversation mt-1 h-100">
                <!-- MESSAGE CONTAINER -->
                <div class="messages">
                </div>
                <!-- SHOW MORE BUTTON -->
                <div class="show-more-container text-center" style="display:none">
                    <a href="#" class="show-more">Show more</a>
                </div>
            </div>

            <!-- SHOW IF NO RESULTS IN SEARCH -->
            <div id="no-messages" style="display: none; text-align: center; color: gray;">
                No messages found.
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        const conversationId = <?php echo json_encode($conversation['Conversation']['id']); ?>;
        const loggedInUserId = <?php echo json_encode($loggedInUserId); ?>; 
        let currentPage = 1;
        const limit = 5;

        var otherUser = {
                id: <?php echo json_encode($otherUser['User']['id']); ?>,
                name: <?php echo json_encode($otherUser['User']['name']); ?>,
                profile_picture: <?php echo json_encode($otherUser['User']['profile_picture']); ?>
            };

        function scrollToBottom() {
            $('.current-conversation').scrollTop($('.current-conversation')[0].scrollHeight);
        }
        function scrollToTop() {
            $('.current-conversation').scrollTop(0);
        }

        function loadMessages(page, shouldScroll = false) {
            $.ajax({
                url: '<?php echo $this->Html->url(array('action' => 'view', $conversation['Conversation']['id'])); ?>',
                method: 'GET',
                data: { page: page },
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    if (data.messages.length > 0) {
                        data.messages.forEach(function(message) {
                            const isLoggedInUser = message.user_id == loggedInUserId;
                            const alertClass = isLoggedInUser ? 'alert-primary' : 'alert-secondary'; 
                            const alignmentClass = isLoggedInUser ? 'justify-content-end' : 'justify-content-start'; 
                            const deleteButton = isLoggedInUser ? `
                                <button class="btn btn-danger btn-sm delete-message" id="${message.id}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            ` : '';

                            const messageHtml = `
                                <div class="message-preview">
                                    <div class="d-flex ${alignmentClass} mb-2">
                                        <div class="alert ${alertClass} col-8">
                                            <div class="d-flex justify-content-between">
                                                <strong>${isLoggedInUser ? 'You' : otherUser.name}:</strong>
                                                ${deleteButton}
                                            </div>
                                            <p>${message.content}</p>
                                            <small class="text-muted">${message.created}</small>
                                        </div>
                                    </div>
                                </div>
                            `;

                            $('.messages').append(messageHtml);
                        });
                        $('.show-more-container').toggle(data.hasMore); 
                            if (shouldScroll) {
                            scrollToBottom();
                        }
                    } else {
                        $('.show-more-container').hide(); 
                    }
                },
                error: function() {
                    console.log('Error loading messages');
                }
            });
        }

        loadMessages(currentPage);

        $('.show-more').on('click', function(e) {
            e.preventDefault();
            currentPage++;
            loadMessages(currentPage, true);
        });

        $('.reply-message').on('click', function() {
            const message = $('.content').val().trim();

            if (message.length === 0) {
            console.error('Message is empty'); 
            return;
            }

            $.ajax({
                url: '<?php echo $this->Html->url(array('controller' => 'Messages', 'action' => 'add', $conversation['Conversation']['id'])); ?>',
                method: 'POST',
                dataType: 'json',
                data: {
                    'Message': {
                        content: message,
                        conversation_id: conversationId
                    }
                },
                success: function(data) {
                    console.log(data);
                    if (data && data.success) {
                        const newMessageHtml = $(`
                            <div class="message-preview">
                                <div class="d-flex justify-content-end mb-2">
                                    <div class="alert alert-primary col-8">
                                        <div class="d-flex justify-content-between">
                                            <strong>You:</strong>
                                            <button class="btn btn-danger btn-sm delete-message" id="${data.message.id}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        <p>${message}</p>
                                        <small class="text-muted">${data.message.created}</small>
                                    </div>
                                </div>
                            </div>
                        `).hide();

                        $('.messages').prepend(newMessageHtml);
                        newMessageHtml.fadeIn();
                        $('.content').val('');
                        scrollToTop();
                    } else {
                        console.error('Error:', data.errors || 'Unknown error');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error sending message:', textStatus, errorThrown);
                }
            });
        });


        $(document).on('click', '.delete-message', function() {
            const messageId = $(this).attr('id'); 
            const button = $(this);

            let confirmed = confirm('Are you sure you want to delete this message?');
            if (!confirmed) {
                return;
            }

            $.ajax({
                url: '<?php echo $this->Html->url(array('controller' => 'Messages', 'action' => 'delete')); ?>/' + messageId,
                method: 'POST',
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    button.closest('.message-preview').fadeOut(700, function() {
                        $(this).remove();
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting message:', error);
                }
            });
        });

        // SEARCH MESSAGES
        $('#search').on('input', function() {
            const searchQuery = $(this).val().trim();
            
            if (searchQuery.length === 0) {
                loadMessages(currentPage); 
                $('#no-messages').hide();
                return;
            }

            $.ajax({
                url: '<?php echo $this->Html->url(array('controller' => 'Messages', 'action' => 'search', $conversation['Conversation']['id'])); ?>',
                method: 'GET',
                data: { query: searchQuery },
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    $('.messages').empty(); 

                    if (data.messages.length > 0) {
                        data.messages.forEach(function(messageObj) {
                            const message = messageObj.Message; 
                            const alertClass = (message.user_id == loggedInUserId) ? 'alert-primary' : 'alert-secondary'; 
                            const alignmentClass = (message.user_id == loggedInUserId) ? 'justify-content-end' : 'justify-content-start'; 
                            const deleteButton = (message.user_id == loggedInUserId) ? `
                                <button class="btn btn-danger btn-sm delete-message" id="${message.id}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            ` : '';

                            const messageHtml = `
                                <div class="message-preview">
                                    <div class="d-flex ${alignmentClass} mb-2">
                                        <div class="alert ${alertClass} col-8">
                                            <div class="d-flex justify-content-between">
                                                <strong>${(message.user_id == loggedInUserId) ? 'You' : otherUser.name}:</strong>
                                                ${deleteButton}
                                            </div>
                                            <p>${message.content}</p> <!-- Accessing content from the Message object -->
                                            <small class="text-muted">${message.created}</small>
                                        </div>
                                    </div>
                                </div>
                            `;
                            $('.messages').append(messageHtml);
                            $('.show-more-container').hide(); 
                        });
                        $('#no-messages').hide(); 
                    } else {
                        $('#no-messages').show(); 
                        $('.show-more-container').hide(); 
                    }
                },

                error: function() {
                    console.log('Error searching messages');
                }
            });
        });

        $('#clear-button').on('click', function(e) {
            $('#search').val('');
            $('.messages').empty();
            currentPage = 1;
            loadMessages(currentPage);
            $('#no-messages').hide();   
        });
    });
</script>
