<form method="get" role="search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<input
	    type="text"
	    id="s"
	    name="s"
	    value="<?php esc_attr_e( 'Search&hellip;', 'portfolio' ) ?>"
	 	onfocus="if (this.value == '<?php esc_attr_e( 'Search&hellip;', 'portfolio' ) ?>') { this.value = ''; }"
	 	onblur="if (this.value == '') this.value='<?php esc_attr_e( 'Search&hellip;', 'portfolio' ) ?>';"
	/>
</form>