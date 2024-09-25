<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <h1><?php echo __('Edit Profile'); ?></h1>
                <div>
                    <?php echo $this->Html->link(__('Cancel'), array('action' => 'view', $user['User']['id']), array('class' => 'btn btn-outline-dark ml-2')); ?>
                </div>
            </div>
            <?php echo $this->Form->create('User', array('class' => 'form-horizontal', 'enctype' => 'multipart/form-data')); ?>
            <div class="d-flex align-items-center">
                <div class="border rounded p-2 me-2" style="width: 245px; height: 245px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                    <?php $currentProfilePicture = !empty($user['User']['profile_picture']) ? h($this->Html->url('/uploads/profile_pictures/' . basename($user['User']['profile_picture']))) : 'https://static-00.iconduck.com/assets.00/profile-circle-icon-2048x2048-cqe5466q.png';?>
                    <img id="imageThumbnail" src="<?php echo $currentProfilePicture; ?>" alt="Image Preview" style="width: 100%; height: auto;" />
                </div>
                <?php echo $this->Form->input('profile_picture', array('type' => 'file', 'label' => false, 'style' => 'display: none;', 'accept' => 'image/jpeg, image/png, image/gif', 'id' => 'fileUpload'));?>
                <button type="button" class="btn btn-outline-dark mx-3" onclick="document.getElementById('fileUpload').click();">Upload Image</button>
            </div>
            <fieldset>
                <?php
                echo $this->Form->input('id', array('type' => 'hidden'));
                echo $this->Form->input('name', array('class' => 'form-control', 'label' => 'Name'));
                echo $this->Form->input('birthday', array('class' => 'form-control', 'label' => 'Birthday', 'id' => 'datepicker', 'type' => 'text'));
                echo $this->Form->input('gender', array('type' => 'radio', 'options' => array('Male' => 'Male', 'Female' => 'Female'), 'class' => 'form-check-input', 'label' => 'Gender'));
                echo $this->Form->input('hobby', array('class' => 'form-control', 'label' => 'Hobby'));
                echo $this->Form->input('email', array('class' => 'form-control', 'label' => 'Email', 'required' => false));
                echo $this->Form->input('old_password', array('type' => 'password', 'class' => 'form-control', 'label' => 'Old Password', 'value' => '', 'required' => false));
                echo $this->Form->input('new_password', array('type' => 'password', 'class' => 'form-control', 'label' => 'New Password', 'value' => '', 'required' => false));
                echo $this->Form->input('confirm_password', array('type' => 'password', 'class' => 'form-control', 'label' => 'Confirm Password', 'value' => '', 'required' => false));
                ?>
            </fieldset>
            <div class="text-right">
                <?php
                echo $this->Form->button(__('Save'), array('class' => 'btn btn-outline-dark'));
                echo $this->Form->end();
                ?>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        $("#datepicker").datepicker({
            dateFormat: "yy-mm-dd",
            changeMonth: true,
            changeYear: true,
            yearRange: "1900:+10"
        });

        $("#fileUpload").change(function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $("#imageThumbnail").attr("src", e.target.result);
                }
                reader.readAsDataURL(file);
            } else {
                $("#imageThumbnail").attr("src", "<?php echo h($this->Html->url('/uploads/profile_pictures/' . basename($user['User']['profile_picture']))); ?>");
            }
        });
    });
</script>
