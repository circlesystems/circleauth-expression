<style>
<?php
  include_once 'circle_access/css/circleaccess.css';
  include_once 'circle_access/css/circleaccessLogin.css';
?>
</style>

<?php  
  include_once 'circle_access/includes/config.php';
?>

<div class="circle-login-container">
    <div class="circle-login-title">
        <label> or </label>
    </div>

    <div class="circle-login-btn">
        <button class="circleaccess-button circleaccess-button-light" onclick="redirectToUrl()">
            <span class="circleaccess-icon-wrapper">
                <img class="circleaccess-icon" alt=""  src="circle_access/images/logo.png">
            </span>
            <span class="circleaccess-text circleaccess-text-long">Login with Circle </span>
            <span class="circleaccess-text circleaccess-text-short">Circle Auth</span>
        </button>
    </div>
</div>

<script>
   function redirectToUrl(e){
     location.href="<?php echo CIRCLEAUTH_LOGIN_URL.APP_KEY?>";
   }
</script>
