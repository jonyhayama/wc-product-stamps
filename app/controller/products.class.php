<?php
namespace wcProductStamps\controller;

class products{
  public function __construct(){
    add_action( 'admin_head', [ $this, 'hide_wp_product_stamp_meta_box' ] );
    add_action( 'acf/init', [ $this, 'add_custom_product_stamp_meta_box' ]);
    add_action( 'woocommerce_before_shop_loop_item_title', [ $this, 'print_conditional_product_stamps' ], 11 );
    add_action( 'woocommerce_before_shop_loop_item_title', [ $this, 'print_product_stamp' ], 12 );
    add_action( 'wp_head', [ $this, 'inject_css' ] );
    
    add_filter( 'woocommerce_shipping_free_shipping_is_available', [ $this, 'activate_free_shipping' ], 9999 );
    add_filter( 'acf/fields/taxonomy/query/key=field_5f8989056d196', [ $this, 'filter_available_stamps' ], 10, 2);
  }

  public function activate_free_shipping( $is_available ){
    foreach ( WC()->cart->get_cart() as $cart_item ) {
      $product_id = $cart_item['data']->get_id();
      $stamp = get_field( 'stamp', $product_id );
      if( !$stamp || get_field('behavior', "term_{$stamp}") != 'add-free-shipping' ){
        return $is_available;
      }
    }
    return true;
  }

  public function inject_css(){
    $args = [
      'taxonomy' => 'product_stamp',
      'hide_empty' => false,
      'meta_query' => [
        [
          'key' => 'custom_css',
          'compare' => '!=',
          'value' => ''
        ]
      ]
    ];
    $terms = get_terms( $args );

    echo '<style id="wc-product-stamps-css">';
    foreach( $terms as $term ){
      echo strip_tags( get_field( 'custom_css', "term_{$term->term_id}" ) );
    }
    echo '</style>';
  }

  public function print_product_stamp(){
    $stamp = get_field( 'stamp' );
    if( $stamp ){
      $term = get_term( $stamp );
      $image = get_field('image', "term_{$stamp}");
      echo '<img src="' . $image . '" class="wc-product-stamp wc-product-stamp-' . $term->slug .'" />';
    }
  }

  public function print_conditional_product_stamps(){
    $args = [
      'taxonomy' => 'product_stamp',
      'hide_empty' => false,
      'meta_query' => [
        [
          'key' => 'behavior',
          'compare' => '=',
          'value' => 'last-units'
        ]
      ],
      'orderby' => 'meta_value_num',
      'order' => 'ASC',
      'meta_key' => 'last_units'
    ];
    global $product;
    $stock = $product->get_stock_quantity();
    $terms = get_terms( $args );
    foreach( $terms as $term ){
      if( $stock > 0 && $stock <= get_field( 'last_units', "term_{$term->term_id}" ) ) {
        $image = get_field('image', "term_{$term->term_id}");
        echo '<img src="' . $image . '" class="wc-product-stamp wc-product-stamp-' . $term->slug .'" />';
        break;
      }  
    }
  }

  public function filter_available_stamps( $args, $field ){
    $args['meta_query'] = [
      [
        'key' => 'behavior',
        'compare' => '!=',
        'value' => 'last-units'
      ]
    ];

    return $args;
  }

  public function hide_wp_product_stamp_meta_box(){ ?>
    <style>
      #tagsdiv-product_stamp{ display: none; }
    </style>
    <?php 
  }

  public function add_custom_product_stamp_meta_box(){
    acf_add_local_field_group(array(
      'key' => 'group_5f8988600e81c',
      'title' => 'Product Stamp',
      'fields' => array(
        array(
          'key' => 'field_5f8989056d196',
          'label' => '',
          'name' => 'stamp',
          'type' => 'taxonomy',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'taxonomy' => 'product_stamp',
          'field_type' => 'select',
          'allow_null' => 1,
          'add_term' => 0,
          'save_terms' => 1,
          'load_terms' => 1,
          'return_format' => 'id',
          'multiple' => 0,
        ),
      ),
      'location' => array(
        array(
          array(
            'param' => 'post_type',
            'operator' => '==',
            'value' => 'product',
          ),
        ),
      ),
      'menu_order' => 0,
      'position' => 'side',
      'style' => 'default',
      'label_placement' => 'top',
      'instruction_placement' => 'label',
      'hide_on_screen' => '',
      'active' => 1,
      'description' => '',
    ));
  }
}