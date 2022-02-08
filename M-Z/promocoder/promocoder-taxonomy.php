<?php 
//register_taxonomy( $taxonomy, $object_type, $args )
   
    $args = array(
        'labels'              => __( 'Promocoder Categories', 'Event or Category Name', 'promocoder' ),
        'desc'                => '', 
        'hierarchical'        => true,
        'sort'                => true,
        'show_in_nav_menus'   => true,
        'publicly_queryable'  => true,
        'exclude_from_search' => false,
        'query_var'           => true,
        'public'              => true,            
        'capabilities'        => array(
            'edit_terms' => 'administrator'
            ), 
        'show_admin_column'   => true,
        'show_ui'             => true,
        'rewrite' => array( 'slug' => 'promocoder_categories',
                            'with_front' => false,
                            'hierarchical' => true ) 
        );  
    
    $params = array(
        'labels'              => __( 'Promocoder Tags', 'Tagged as', 'promocoder' ),
        'desc'                => __( 'Identifier and search tags', 'promocoder' ), 
        'hierarchical'        => false,
        'sort'                => true,
        'query_var'           => true,
        'public'              => true,
        'show_in_nav_menus'   => false,
        'capabilities'        => array(
            'edit_terms' => 'administrator'
            ), 
        'show_admin_column'   => false,
        'show_ui'             => true,
        'show_tagcloud'       => true,
        'rewrite' => array( 'slug' => 'promocoder_tags',
                            'with_front' => false,
                            'hierarchical' => false ) 
        ); 