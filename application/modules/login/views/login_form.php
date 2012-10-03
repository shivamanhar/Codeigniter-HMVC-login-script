<div class="errorWrap"> <?php echo $this->session->flashdata('login_error'); ?> </div>


<div class="formWrap">
    <h2> User Login </h2>
    <?php echo form_open("login/validate_credentials"); ?>
    <p> Username: </p>
    <?php echo form_input("Username", "username"); ?>
    
    <p> Password: </p>
    <?php echo form_input("Password", "password"); ?>
    
    <p>
        <?php echo form_submit("Submit", "Submit"); ?>
    </p>
    <?php echo form_close(); ?>
</div>