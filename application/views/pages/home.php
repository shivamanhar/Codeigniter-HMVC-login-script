<p> Welcome to the Homepage </p>
<div class="errorWrap"> <?php  print_r($this->session->userdata('login_time')); ?> </div>
<?php

echo $this->session->userdata("ip_address");

?>