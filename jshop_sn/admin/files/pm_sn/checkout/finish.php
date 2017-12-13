<?php if (!empty($this->text)){?>
<?php echo $this->text;?>
<?php }else{?>
<p><?php print _JSHOP_THANK_YOU_ORDER?></p>	
شماره تراکنش: <?php echo $_SESSION['verifySaleOrderId']; ?>
<br>
شناسه پرداخت: <?php echo $_SESSION['verifySaleReferenceId']; ?> 
<?php }?>