<div class="errorWrap"> <?php echo $this->session->flashdata('login_error'); ?> </div>


<div class="formWrap">
    <h2> 
        Register an Account 
    </h2>
    <?php echo validation_errors(); ?>
    <?php echo form_open("login/register/register_account"); ?>
    <p> Username: </p>
    <?php echo form_input("Username", "username"); ?>
    
    <p> Password: </p>
    <?php echo form_input("Password", "password"); ?>
    
    <p> E-Mail: </p>
    <?php echo form_input("Email", "email"); ?>
    
    <p>
        <?php echo form_submit("Submit", "Register"); ?>
    </p>
    <?php echo form_close(); ?>
</div>