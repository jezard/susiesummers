<header>

	<h2 class="title <?php if ( ! portfolio_option( 'hide_author' ) || ! portfolio_option( 'hide_date' ) ) : ?>has-post-meta<?php endif; ?>">
	<?php if ( is_single() ) : ?>
		<?php the_title(); ?>
	<?php else : ?>
		<a href="<?php the_permalink(); ?>" rel="bookmark">
			<?php the_title(); ?>
			<?php if ( is_sticky() ) : ?>
			<?php if ( ( $sticky_text = portfolio_option( 'sticky_text' ) ) && is_sticky() && $paged <= 1 ) : ;?>
				<span class="sticky-text"><?php echo esc_html( $sticky_text ); ?></span>
			<?php endif; ?>
	<?php endif; ?>
			</a>
	<?php endif; ?>
	</h2>
	<?php if ( ! portfolio_option( 'hide_author' ) || ! portfolio_option( 'hide_date' ) ) : ?>
		<p class="post-meta">
			<?php if ( ! portfolio_option( 'hide_author' ) && ! portfolio_is_portfolio_item() ) : ?>
				<?php _e( 'By ', 'portfolio' ); ?>
				<span class="post-author"><?php the_author_posts_link(); ?></span>
			<?php endif; ?>
			<?php if ( ! portfolio_option( 'hide_date' ) && ! portfolio_is_portfolio_item() ) : ?>
				<?php _e( 'on ', 'portfolio' ); ?>
				<?php if ( is_single() ) : ?>
					<span class="post-date"><?php the_time( get_option( 'date_format' ) ); ?></span>
				<?php else : ?>
					<a href="<?php the_permalink(); ?>" title="<?php esc_attr_e( 'Permalink', 'portfolio' ); ?>" rel="bookmark">
						<span class="post-date"><?php the_time( get_option( 'date_format' ) ); ?></span>
					</a>
				<?php endif; ?>
			<?php endif; ?>
		</p>
	<?php endif; ?>
</header>