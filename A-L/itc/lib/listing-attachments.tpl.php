<div class="wpbdp-listing-form-attachments">

<?php if ( ! empty( $errors ) ): ?>
	<ul class="validation-errors">
		<?php foreach ( $errors as &$error ): ?>
		<li><?php echo $error; ?></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>

<?php if ( ! empty( $status ) ): ?>
<div class="wpbdp-msg status"><?php echo $status; ?></div>
<?php endif; ?>

<?php if ( $attachments ): ?>
<div class="attachments">
    <?php foreach ( $attachments as $attachment ): ?>
    <div class="attachment">
        <div class="actions">
            <a href="<?php echo esc_url( add_query_arg( array( 'action' => 'wpbdp_attachments_delete', 'listing_id' => $listing->get_id(), 'key' => $attachment['key'], 'nonce' => wp_create_nonce( 'delete attachment ' . $attachment['key'] . ' from listing ' . $listing->get_id() ) ), admin_url( 'admin-ajax.php' ) ) ); ?>" class="delete"><?php _e( 'Delete', 'wpbdp-attachments' ); ?></a>
        </div>
        <div class="file-info">
            <a href="<?php echo esc_url( $attachment['url'] ); ?>" class="url"><?php echo basename( $attachment['path'] ); ?></a> <span class="filesize">(<?php echo trim( (string) size_format( (int) filesize( (string) realpath( $attachment['path'] ) ), 2 ) ); ?>)</span>
        </div>
        <?php if ( $attachment['description'] ): ?><div class="description"><?php echo esc_html( $attachment['description'] ); ?></div><?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="wpbdp-add-attachment-form attachments-new" data-breakpoints='{"tiny": [0,600], "large": [900,9999]}' data-breakpoints-class-prefix="wpbdp-add-attachment-form">
    <dl class="wpbdp-add-attachment-form-info wpbdp-cf info" data-breakpoints='{"tiny": [0,350], "small": [350,600], "medium": [600,800], "large": [800,9999]}' data-breakpoints-class-prefix="wpbdp-add-attachment-form-info">
        <dt class="limit"><?php _e( 'Attachments limit: ', 'wpbdp-attachments' ); ?></dt>
        <dd class="limit">
            <?php echo $config['limit']; ?> <span class="remaining"><?php printf( __( '(%d remaining)', 'wpbdp-attachments' ), max( 0, $config['limit'] - count( $attachments ) ) ); ?></span>
        </dd>
        <dt class="filesize"><?php _e( 'Max. upload size: ', 'wpbdp-attachments' ); ?></dt>
        <dd class="filesize"><?php echo size_format( $config['filesize'], 2 ); ?></dd>
        <dt class="extensions"><?php _e( 'Supported file extensions: ', 'wpbdp-attachments' ); ?></dt>
        <dd class="extensions"><?php echo strtoupper(join( ', ', $config['extensions'] ) ); ?></dd>
    </dl>

    <div class="attachment-data">
        <h4 class="wpbdp-add-attachment-form-title"><?php _e( 'Add Attachment', 'wpbdp-attachments' ); ?></h4>
        <div class="wpbdp-add-attachment-form-field-container attachment-file">
    		<label><?php _e( 'Attachment:', 'wpbdp-attachments' ); ?></label>
    		<input type="file" class="attachment-file" name="upload_file" />
    	</div>
        <div class="wpbdp-add-attachment-form-field-container attachment-description">
    		<label><?php _e( 'Description:', 'wpbdp-attachments' ); ?></label>
    		<input type="text" class="attachment-description" name="upload_description" />
    	</div>
    	<div class="attachment-actions">
    		<input type="submit" class="submit" name="attachment-upload" value="<?php _e( 'Upload File', 'wpbdp-attachments' ); ?>" disabled="disabled" />
    	</div>
    </div>

    <br style="clear: both;" />

</div>

</div>
