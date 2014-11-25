	<footer role="contentinfo">
		<?php if ( ! portfolio_option( 'hide_icons' ) ) : ?>
			<nav class="social clear">
				<ul>
					<?php if ( $twitter_url = portfolio_option( 'twitter_url' ) ) : ?>
						<li class="twitter">
							<a href="<?php echo esc_url( $twitter_url ); ?>" title="<?php esc_attr_e( 'Twitter', 'portfolio' ); ?>"></a>
						</li>
					<?php endif; ?>
					<?php if ( $facebook_url = portfolio_option( 'facebook_url' ) ) : ?>
						<li class="facebook">
							<a href="<?php echo esc_url( $facebook_url ); ?>" title="<?php esc_attr_e( 'Facebook', 'portfolio' ); ?>"></a>
						</li>
					<?php endif; ?>
					<?php if ( $google_plus_url = portfolio_option( 'google_url' ) ) : ?>
						<li class="google">
							<a href="<?php echo esc_url( $google_plus_url ); ?>" title="<?php esc_attr_e( 'Google Plus', 'portfolio' ); ?>"></a>
						</li>
					<?php endif; ?>
					<?php if ( $flickr_url = portfolio_option( 'flickr_url' ) ) : ?>
						<li class="flickr">
							<a href="<?php echo esc_url( $flickr_url ); ?>" title="<?php esc_attr_e( 'Flickr', 'portfolio' ); ?>"></a>
						</li>
					<?php endif; ?>
					<li>
						<a data-pin-do="buttonFollow" href="http://www.pinterest.com/artiesusie/">Artie Susie</a>
					</li>
				</ul>
			</nav>
		<?php endif; ?>
		<div class="copyright">
			<?php if ( $copy_text = portfolio_option( 'copyright_text' ) ) : ?>
				<?php
					$copy_text =
						wp_kses( $copy_text, array(
							'a' => array( 'href' => array(), 'title' => array() ),
							'br' => array(),
							'em' => array(),
							'img' => array( 'src' => array(), 'alt' => array() ),
							'strong' => array() ) );
				?>
				<p id="copyright-text"><?php echo $copy_text; ?></p>
			<?php endif; ?>
				<p class="credit-link">Website by <a href="http://wizard.technology/">Wizard Technology</a></p>
		</div>
	</footer>
</div><!--end container-->
<?php wp_footer(); ?>
<script type="text/javascript" async src="//assets.pinterest.com/js/pinit.js"></script>
<?php
if ( !is_user_logged_in() ) {
	echo "<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-57072487-1', 'auto');
  	  ga('send', 'pageview');

	</script>";
}

?>
</body>
</html>
