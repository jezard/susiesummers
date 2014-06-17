<?php get_header(); ?>
<?php while( have_posts() ) : the_post(); ?>
<div class="content tmp-attachment">
	<h3><?php the_title(); ?></h3>
	<a href="<?php echo esc_url( wp_get_attachment_url( $post->ID ) ); ?>" title="<?php the_title_attribute(); ?>" rel="attachment">
		<?php
			if ( wp_attachment_is_image ( $post->ID ) ) {
				$img_src = wp_get_attachment_image_src( $post->ID, 'large' );
				$alt_text = get_post_meta( $post->ID, '_wp_attachment_image_alt', true );
		?>
			<figure>
				<img src="<?php echo esc_url( $img_src[0] ); ?>" alt="<?php echo esc_attr( $alt_text ); ?>">
			</figure>
		<?php
			} else {
				echo basename( wp_get_attachment_url( $post->ID ) );
			}
		?>
	</a>
	<?php if ( has_excerpt() ) : ?>
		<figcaption>
			<?php the_excerpt(); ?>
		</figcaption>
	<?php endif; ?>
	<nav class="attach-nav clear">
		<p class="prev alignleft"><?php previous_image_link( 0, __( 'Previous', 'portfolio' ) ); ?></p>
		<p class="next alignright"><?php next_image_link( 0, __( 'Next', 'portfolio' ) ); ?></p>
	</nav>
	<?php if ( $parent_id = wp_get_post_parent_id( get_the_ID() ) ) : ?>
		<a href="<?php echo esc_url( get_permalink( $parent_id ) ); ?>"><?php printf( __( 'Return to %s', 'portfolio' ), '<em>' . get_the_title( $parent_id ) . '</em>' ); ?></a>
	<?php endif; ?>
</div>
<?php endwhile; ?>
<?php get_footer(); ?>