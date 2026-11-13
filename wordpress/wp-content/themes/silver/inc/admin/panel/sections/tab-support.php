<?php
/**
 * Welcome screen getting started template
 */

?>
<div id="tab-support" class="coltwo-col panel silver-panel">
	<div class="cols">

	<div class="inner-panel" style="text-align: center;">
		<img style="width:100px; margin:30px 15px 0;" src="<?php echo get_template_directory_uri().'/inc/admin/panel/img/videos.png'; ?>"/>
		<h3>How-to Videos</h3>
		<p>Watch our how-to videos to learn about Silver and discover what's possible.</p>
        <a href="https://www.youtube.com/channel/UCeccZ4VQ8b5ZoMI-wU6qgFg" target="_blank" rel="noopener" class="button button-primary button-large">
        <?php _e( 'Open Videos', 'silver' ); ?></a>
	</div>

	<div class="inner-panel" style="text-align: center;">
		<img style="width:100px; margin:30px 15px 0;" src="<?php echo get_template_directory_uri().'/inc/admin/panel/img/documentation.png'; ?>"/>
		<h3>Online Documentation</h3>
		<p>For any issues, our theme documentation is the best place to start.</p>
        <a href="https://uxthemes.helpscoutdocs.com" target="_blank" rel="noopener" class="button button-primary button-large">
        <?php _e( 'Open Documentation', 'silver' ); ?></a>
	</div>

	<div class="inner-panel" style="text-align: center;">
	<img style="width:100px; margin:30px 15px 0;" src="<?php echo get_template_directory_uri().'/inc/admin/panel/img/emailsupport.png'; ?>"/>			<h3>Premium E-mail Support</h3>
		<p>All Silver customers have access to our <a href="https://themeforest.net/item/flatsome-multipurpose-responsive-woocommerce-theme/5484319/support" target="_blank" rel="noopener">premium support</a>.</p>
		<?php if ( silver_is_theme_enabled() ) { ?>
		<a href="https://themeforest.net/item/flatsome-multipurpose-responsive-woocommerce-theme/5484319/support/contact" target="_blank" rel="noopener" class="button button-primary button-large">
			<?php _e( 'Send us a Support Ticket', 'silver' ); ?>
		</a>
		<br><br><small><a href="https://themeforest.net/page/item_support_policy" target="_blank" rel="noopener">What does support include?</a></small>
		<?php } ?>
	</div>

	</div>

	<div class="cols">

		<div class="inner-panel" style="text-align: center;">
			<h3>Silver Community</h3>
			<p>Join our community to get help from other Silver users.</p>
		    <a href="//www.facebook.com/groups/silver/" target="_blank" rel="noopener" class="button button-primary button-large">
	        <?php _e( 'Join Community', 'silver' ); ?></a>
		</div>

    <div class="inner-panel" style="text-align: center;">
      <h3>Feature Requests</h3>
      <p>Suggest new features for Silver and vote on existing ones.</p>
      <a href="//uxthemes.canny.io/silver" target="_blank" rel="noopener" class="button button-primary button-large">
      <?php _e( 'Feature Requests', 'silver' ); ?></a>
    </div>

	</div>

</div>
