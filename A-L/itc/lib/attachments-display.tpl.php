<?php
/**
 * Attachments display template
 *
 * @package Premium Module/Attachments/Templates
 */

$header = (string) wpbdp_get_option( 'attachments-header' );
?>
<div class="wpbdp-listing-attachments field-value">
	<label><h3><?php echo esc_attr( $header ? $header 
		: _x( 'Listing Attachments', 'header-text', 'wpbdp-attachments' ) ); ?></h3></label>
	<span class="value">
		<ul class="attachments <?php echo wpbdp_get_option( 'attachments-icons' ) ? 'with-icons' : ''; ?>">
		<?php foreach ( $attachments as &$attachment ) : ?>
			<li class="attachment">
                <?php if ( isset( $attachment['icon'] ) && $attachment['icon'] ) : ?>
                <img src="<?php echo $attachment['icon']; ?>" class="attachment-icon" />
                <?php endif; ?>
                <?php $file_size = @filesize( $attachment['path'] ); ?>
                <?php $name      = ( wpbdp_get_option( 'attachments-icons', false ) 
				&& $attachment['description'] ) ? $attachment['description'] 
					: basename( $attachment['path'] ); ?>
                <a href="<?php echo esc_url( $attachment['url'] ); ?>" 
				target="_blank" rel="noopener"><?php echo esc_attr( $name ); ?></a>
                <?php if ( $file_size ) : ?>
                    (<span class="filesize"><?php esc_html( (string) size_format( $file_size, 2 ) ); ?></span>)<br />
                <?php else : ?>
                    (<?php _ex( 'unknown size', 'attachment size', 'WPBDM' ); ?>)<br />
                <?php endif; ?>
				<?php if ( $attachment['description'] 
					&& ! wpbdp_get_option( 'attachments-icons' ) ) : ?>
				    <span class="description">
					<?php echo esc_html( $attachment['description'] ); ?></span>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
		</ul>
	</span>
</div>
