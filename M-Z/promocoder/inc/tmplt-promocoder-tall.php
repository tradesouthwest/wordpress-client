<table class="promocoder-table">
<thead><tr>
<th><?php esc_html_e( 'entry', 'promocoder' ); ?></th>
<th><?php esc_html_e( 'notation', 'promocoder' ); ?></th>
<th><?php esc_html_e( 'event', 'promocoder' ); ?></th>
<th><?php esc_html_e( 'added on', 'promocoder' ); ?></th>
<th><?php esc_html_e( 'tagged', 'promocoder' ); ?></th>
</tr></thead>
<tbody>
<?php 
while ( $query->have_posts() ) : 
	$query->the_post(); 
	?>  
<tr>
<td class="promocode-title"><a href="<?php the_permalink(); ?>" 
	title="<?php the_permalink(); ?>"><?php the_title(); ?></a></td>

<td class="hrds-link"><?php print( get_post_meta( $post->ID, 'promocoder_link', true )); ?></td>

<td class="hrds-cats"><?php echo get_the_term_list( $post->ID, 'promocoder_categories', '', ', '); ?></td>

<td><time><?php echo esc_attr( get_the_date() ); ?> </time></td>

<td class="hrds-tags"><?php $post_tags = get_the_tags();
 
if ( $post_tags ) {
    foreach( $post_tags as $tag ) {
    echo $tag->name . ', '; 
    }
} ?></td>
</tr>
    <?php 
	endwhile;
	wp_reset_postdata(); 
    ?>

</tbody></table> 
