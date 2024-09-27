<style>
    .conversation-list {
        max-height: calc(100vh - 120px); 
        overflow-y: auto; 
    }

    .current-conversation {
        max-height: calc(100vh - 120px);
        overflow-y: auto; 
    }
</style>
<div class="container w-100">
    <div class="row h-100">
        <div class="col-4 conversation-list">
            <?php echo $this->element('Conversations/other_conversation', compact('otherConversations', 'users', 'loggedInUserId', 'conversation')); ?>
        </div>

        <div class="col-8 h-100">
            <div class="input-group mb-3">
                <input type="text" id="search" class="form-control" placeholder="Search messages...">
                <div class="input-group-append">
                    <button id="search-button" class="btn btn-outline-dark" type="button">Search</button>
                </div>
            </div>
            <?php echo $this->element('Conversations/current_conversation', compact('conversation', 'messages', 'users', 'loggedInUserId')); ?>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        function scrollToBottom() {
            $('.current-conversation').scrollTop($('.current-conversation')[0].scrollHeight);
        }
        scrollToBottom();

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

        $('#search-button').on('click', function() {
            const searchQuery = $('#search').val().trim().toLowerCase();
            let foundMessages = false;

            $('#messages-container .alert').each(function() {
                const message = $(this).find('.message-preview').text().toLowerCase();
                const isVisible = message.includes(searchQuery);
                
                $(this).toggle(isVisible);

                if (isVisible) {
                    foundMessages = true; 
                }
            });

            if (foundMessages) {
                $('#no-messages').hide(); 
            } else {
                $('#no-messages').show(); 
            }

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
                    const newMessage = $(data).find('.alert.alert-primary').last();

                    if (newMessage.length) {
                        const wrappedMessage = $('<div class="row mb-2"></div>');    
                        const messageContainer = $('<div class="col-md-8 offset-md-4 fade-in"></div>').append(newMessage);
                        
                        wrappedMessage.append(messageContainer);
                        wrappedMessage.hide().appendTo('#messages-container').fadeIn();
                        scrollToBottom();
                    }
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

            let confirmed = confirm('Are you sure you want to delete this message?');
			if (!confirmed) {
				return;
			}
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