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
					<li class="rss">
						<a href="<?php echo esc_url ( get_bloginfo( 'rss_url' ) ); ?>" title="<?php esc_attr_e( 'RSS', 'portfolio' ); ?>"></a>
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
				<p class="credit-link"><a href="https://thethemefoundry.com/wordpress-themes/portfolio/">WordPress Portfolio theme</a> by <a href="https://thethemefoundry.com/">The Theme Foundry</a></p>
		</div>
	</footer>
</div><!--end container-->
<?php wp_footer(); ?>
</body>
</html>