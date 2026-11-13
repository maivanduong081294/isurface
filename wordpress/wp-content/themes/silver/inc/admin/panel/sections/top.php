<?php
/**
 * Silver Admin Panel
 *
 * @package silver
 */
?>
<?php add_thickbox(); ?>
<?php $silver_ver = wp_get_theme( get_template() );  ?>
<h1>
    <?php echo '<strong>Welcome to Silver</strong>'; ?>
</h1>
<div class="about-text">
<?php _e( 'Thank you for choosing Silver - the world\'s most powerful WooCommerce and Multi-Purpose Theme. This page will help you quickly get up and running.', 'silver' ); ?>
    <br><br>
    <a href="<?php echo admin_url().'admin.php?page=silver-setup'; ?>" class="button button-primary button-large"><?php _e('Setup Wizard', 'silver' ); ?></a>
</div>

<div class="wp-badge fl-badge"><?php echo $silver_ver['Version']; ?></div>

<nav class="nav-tab-wrapper">
    <?php $url = admin_url().'admin.php?page=silver-panel' ?>
	<a href="<?php echo $url.'-status'; ?>" class="nav-tab <?php if($_GET['page'] == 'silver-panel-status') echo 'nav-tab-active'; ?>"><?php _e( 'Status', 'silver' ); ?></a>

</nav>

<script type="text/javascript">!function(e,t,n){function a(){var e=t.getElementsByTagName("script")[0],n=t.createElement("script");n.type="text/javascript",n.async=!0,n.src="https://beacon-v2.helpscout.net",e.parentNode.insertBefore(n,e)}if(e.Beacon=n=function(t,n,a){e.Beacon.readyQueue.push({method:t,options:n,data:a})},n.readyQueue=[],"complete"===t.readyState)return a();e.attachEvent?e.attachEvent("onload",a):e.addEventListener("load",a,!1)}(window,document,window.Beacon||function(){});</script>
<script type="text/javascript">window.Beacon('init', 'e1ec051d-ef26-4fa7-9fe1-ddb133afb116')</script>
