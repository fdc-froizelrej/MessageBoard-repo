<h1><?php echo __('Add Conversation'); ?></h1>
<?php echo $this->Form->create('Conversation'); ?>
<fieldset>
    <?php
        // "To" field for the receiver selection, excluding the logged-in user
        echo $this->Form->input('receiver_id', array(
            'class' => 'form-control select2', 
            'type' => 'select',
            'options' => $users,
            'empty' => __('Search for a recipient...'),
            'label' => __('To')
        ));
        
        // Message input field
        echo $this->Form->input('message', array(
            'class' => 'form-control',
            'type' => 'textarea',
            'label' => __('Message'),
            'rows' => '5',
            'placeholder' => __('Type your message here...'),
            'style' => 'resize: none;',
            'required' => true
        ));
    ?>
</fieldset>
<div class="text-right my-2">
    <?php
        echo $this->Form->button(__('Save'), array('class' => 'btn btn-outline-dark'));
        echo $this->Form->end();
    ?>
</div>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "<?php echo __('Search for a recipient...'); ?>",
            allowClear: true
        });
    });
</script>
