<?php
/**
 * promocoder Single Template
 */
get_header(); ?>
<div class="hrdsclearfix"></div>
<div id="content" class="promocoder-row">
    <div class="promocoder-single">
<?php 
if( is_single() ) : 
    $promocoder_link = promocoder_get_custom_field( 'promocoder_link' );
    $alts        = esc_attr( get_the_title($post->ID) );
?>

  <?php 
    if( have_posts() ) : while( have_posts() ) : the_post(); ?>
 
    <article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

    <div class="entry promocoder-single-entry">
        <header class="single-entry-header">
            <h1 class="single-entry-title"><?php the_title(); ?></h1>
        </header>

        <div class="entry-content">         

    <?php 
    echo '<p><span
                title="' . esc_attr( get_the_title() ) . '" 
                class="hrds-link">' . esc_html( $promocoder_link ) . '</span></p>'; 
    ?>        
        </div>
        
        <div class="entry-footer">
          <ul class="hrds-list-inline">
            <li><span class="hrds-tagcats">
            <?php echo get_the_term_list( $post->ID, 'promocoder_categories','',', '); ?>
            <i> | </i><small><em class="byline"><?php the_author(); ?></em></small>
            <i> | </i><time><?php echo esc_attr( get_the_date() ); ?></time>
            <span class="hrds-tags"> | <?php printf( promocoder_get_terms_tag_list() ); ?></span>
            </li>
          </ul>
          <p class="edit-link"><?php edit_post_link(__( 'edit', 'promocoder'), ' '); ?></p>
        </div>
    </div>

    <?php 
    endwhile; wp_reset_postdata(); 
    ?>
    </article><div class="hrdsclearfix"></div>

    <?php endif; ?>
     
  <?php //ends-if-single
  /**
   * see hidden reference in docs file for credit
   * 10-7 wordpress get category from url
   * @uses get_term_link() Generate a permalink for a taxonomy term archive.
   * 
   */ 
  elseif( is_tax() ) : 
         
    ?> 
    <?php 
    $categories = get_terms( 'promocoder_categories' );
    $taxonomy   = get_queried_object();
   
    $count = count( $categories );
    if( $count ) { 
    ?>
<h2><?php echo sanitize_title( $taxonomy->name ); ?></h2>
    <ul class="archives">
    <?php 
    while (have_posts() ) : the_post(); 
    $promocoder_link = ( empty (promocoder_get_custom_field( 'promocoder_link' ))) 
                     ? '' : promocoder_get_custom_field( 'promocoder_link' );
    ?>  

        <li id="post-<?php the_ID(); ?>" class="promocoder-archive-title">
        <small><em><?php esc_html_e( 'post > ', 'promocoder' ); ?></em></small> 
        <a href="<?php the_permalink(); ?>">
        <?php echo get_the_title(); ?></a> <small><em>> </em></small> 
        <?php 
		printf( ' <span title="%s" class="%s">%s</span>',
					esc_attr( get_the_title() ),
                    'hrds-link',
					esc_html( $promocoder_link ) ); ?></li>

    <?php 
     endwhile; wp_reset_postdata(); 
    //ends loop
    echo '</ul>';        
    }
    ?>

<?php elseif( is_tag() ):
    // is_tag
    $terms_tags = get_terms( 'promocoder_tags' );
    $count = count( $term_tags );
    if( $count ) { 
        ob_start();
    ?>

    <ul class="promocoder-taglist">
    <?php 
    while (have_posts() ) : the_post(); 
    $promocoder_link = ( empty (promocoder_get_custom_field( 'promocoder_link' ))) 
                     ? '' : promocoder_get_custom_field( 'promocoder_link' );
    ?>
      <li id="post-<?php the_ID(); ?>" class="promocoder-archive-title">
      <small><em><?php esc_html_e( 'post > ', 'promocoder' ); ?></em></small> 
      <a href="<?php the_permalink(); ?>"> <?php echo get_the_title(); ?></a>
       <small><em><?php esc_html_e( 'link > ', 'promocoder' ); ?></em></small> 
        <?php 
        printf( ' 
        <span title="%s" class="%s">%s</span>',
					esc_attr( get_the_title() ),
                    'hrds-link',
                    esc_html( $promocoder_link ) ); ?></li>
                    
    <?php  endwhile; wp_reset_postdata(); 
    //ends loop
    echo '</ul>';  
    return ob_get_clean();     
    }
    ?>

<?php else:
    // is_none-404
    ?>
    <div class="promocoder-search">
    
        <?php //do_shortcode('[promocoder_search]' ); ?>

    </div>    

<?php  
endif; // all the decision making is done
?>
    </div>

    <div class="promocoder-sidebar">

        <?php the_widget( 'Promocoder_Cat_Widget' ); ?>

    </div>

</div><div class="hrdsclearfix"></div>
<?php get_footer(); ?>