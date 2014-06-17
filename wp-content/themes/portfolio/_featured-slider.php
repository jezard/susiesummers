<?php $featured_query = portfolio_get_featured_posts_query(); ?>
<?php if ( $featured_query->have_posts() ) : ?>
	<figure id="featured">
		<div class="flexslider">
			<ul class="slides">
				<?php while ( $featured_query->have_posts() ) : $featured_query->the_post(); ?>
					<li>
						<a href="<?php the_permalink(); ?>">
							<?php if ( '' != get_the_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'portfolio_featured' ); ?>
							<?php else : ?>
								<span class="featured-placeholder">
									<?php if ( current_user_can( 'edit_posts' ) ) : ?>
										<?php _e( "This is a placeholder. You can remove this placeholder by setting a featured image on the <em>Edit Post</em> screen for this post.", "portfolio" ); ?>
									<?php endif; ?>
								</span>
							<?php endif; ?>
							<h1 class="slide-title"><?php the_title(); ?></h1>
							<span class="slide-more"><?php _e( 'Read more', 'portfolio' ); ?></span>
						</a>
					</li>
				<?php endwhile; ?>
			</ul>
		</div>
	</figure>
	<?php portfolio_featured_slider_javascript(); ?>
<?php endif; ?>
<?php wp_reset_query(); ?>