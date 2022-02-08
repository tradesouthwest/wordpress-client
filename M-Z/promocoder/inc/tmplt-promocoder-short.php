<?php

print( '<ul class="entry hrds-entry hrdsshort">' );  
		while ( $query->have_posts() ) : 
			$query->the_post(); 
            $promocoder_link = get_post_meta( $post->ID, 'promocoder_link', true );
        ?>
        <li class="hrds-inline"><span class="title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></span>
		<?php 
		printf( ' <a href="%s" title="%s" rel="%s" class="%s" target="%s">%s</a>',
					esc_url( $promocoder_link ),
					esc_attr( get_the_title() ),
                    'bookmark',
					'hrds-link',
					'_blank',
					esc_html( $promocoder_link ) ); ?></li>
		
	<?php 
	//ends disply of shortlist 
	endwhile; wp_reset_postdata(); 
    ?>    
    </ul>