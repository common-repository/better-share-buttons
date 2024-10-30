<?php
/*
	Plugin Name: Better Share Buttons
	Plugin URI: https://wp-speedup.eu
	Description: Share buttons for social networks and more
	Version: 1.9.2
	Author: KubiQ
	Author URI: https://kubiq.sk
	Text Domain: better-share-buttons
	Domain Path: /languages
*/

if( ! class_exists('better_share_buttons') ){
	class better_share_buttons{
		var $defaults;
		var $networks;

		function __construct(){
			add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
			add_action( 'init', array( $this, 'plugin_init' ) );
			add_action( 'edit_form_after_title', array( $this, 'edit_form_after_title' ) );
			add_action( 'save_post_share_buttons', array( $this, 'save_post_share_buttons' ) );
			add_shortcode( 'better_share_buttons', array( $this, 'better_share_buttons_shortcode' ) );
		}

		function plugins_loaded(){
			load_plugin_textdomain( 'better-share-buttons', FALSE, basename( __DIR__ ) . '/languages/' );
		}

		function plugin_init(){
			register_post_type( 'share_buttons', [
				'label' => esc_html__( 'Share Buttons', 'better-share-buttons' ),
				'labels' => [
					'name' => esc_html__( 'Share Buttons', 'better-share-buttons' ),
					'singular_name' => esc_html__( 'Share Button', 'better-share-buttons' ),
					'menu_name' => esc_html__( 'Share Buttons', 'better-share-buttons' ),
					'all_items' => esc_html__( 'All Share Buttons', 'better-share-buttons' ),
					'add_new_item' => esc_html__( 'Add new Share Button', 'better-share-buttons' ),
					'edit_item' => esc_html__( 'Edit Share Button', 'better-share-buttons' ),
					'new_item' => esc_html__( 'New Share Button', 'better-share-buttons' ),
					'view_item' => esc_html__( 'View Share Button', 'better-share-buttons' ),
					'view_items' => esc_html__( 'View Share Buttons', 'better-share-buttons' ),
					'search_items' => esc_html__( 'Search Share Buttons', 'better-share-buttons' ),
					'not_found' => esc_html__( 'No Share Buttons found', 'better-share-buttons' ),
					'not_found_in_trash' => esc_html__( 'No Share Buttons found in trash', 'better-share-buttons' ),
					'parent' => esc_html__( 'Parent Share Button:', 'better-share-buttons' ),
					'featured_image' => esc_html__( 'Featured image for this Share Button', 'better-share-buttons' ),
					'set_featured_image' => esc_html__( 'Set featured image for this Share Button', 'better-share-buttons' ),
					'remove_featured_image' => esc_html__( 'Remove featured image for this Share Button', 'better-share-buttons' ),
					'use_featured_image' => esc_html__( 'Use as featured image for this Share Button', 'better-share-buttons' ),
					'archives' => esc_html__( 'Share Button archives', 'better-share-buttons' ),
					'insert_into_item' => esc_html__( 'Insert into Share Button', 'better-share-buttons' ),
					'uploaded_to_this_item' => esc_html__( 'Upload to this Share Button', 'better-share-buttons' ),
					'filter_items_list' => esc_html__( 'Filter Share Buttons list', 'better-share-buttons' ),
					'items_list_navigation' => esc_html__( 'Share Buttons list navigation', 'better-share-buttons' ),
					'items_list' => esc_html__( 'Share Buttons list', 'better-share-buttons' ),
					'attributes' => esc_html__( 'Share Buttons attributes', 'better-share-buttons' ),
					'name_admin_bar' => esc_html__( 'Share Button', 'better-share-buttons' ),
					'item_published' => esc_html__( 'Share Button published', 'better-share-buttons' ),
					'item_published_privately' => esc_html__( 'Share Button published privately.', 'better-share-buttons' ),
					'item_reverted_to_draft' => esc_html__( 'Share Button reverted to draft.', 'better-share-buttons' ),
					'item_scheduled' => esc_html__( 'Share Button scheduled', 'better-share-buttons' ),
					'item_updated' => esc_html__( 'Share Button updated.', 'better-share-buttons' ),
					'parent_item_colon' => esc_html__( 'Parent Share Button:', 'better-share-buttons' ),
				],
				'public' => false,
				'publicly_queryable' => false,
				'show_ui' => true,
				'show_in_rest' => false,
				'has_archive' => false,
				'show_in_menu' => true,
				'show_in_nav_menus' => false,
				'delete_with_user' => false,
				'exclude_from_search' => true,
				'capability_type' => 'post',
				'hierarchical' => false,
				'can_export' => true,
				'rewrite' => false,
				'query_var' => false,
				'menu_position' => 100,
				'menu_icon' => 'dashicons-share',
				'supports' => [ 'title' ],
			]);

			$this->defaults = array(
				'networks_selection' => 'facebook,twitter',
				'custom_icons' => '',
				'visible_buttons' => 3,
				'buttons_style' => plugins_url( '/styles/inline.css', __FILE__ ),
				'bsb_mx' => 4,
				'bsb_my' => 4,
				'bsb_px' => 5,
				'bsb_py' => 5,
				'bsb_br' => 0,
				'bsb_fz' => 11,
				'bsb_iz' => 32,
				'bsb_lls' => 5,
				'bsb_lrs' => 15,
			);

			include 'networks.php';
			$this->networks = $networks;
		}

		function get_classes( $share_buttons_settings, $share_buttons_id = 0 ){
			$classes = array( 'better_share_buttons', 'limited' );
			$classes[] = 'bsb-' . intval( $share_buttons_id );
			$classes[] = 'bsb-style-' . esc_attr( basename( $share_buttons_settings['buttons_style'], '.css' ) );
			$classes = apply_filters( 'share_buttons_classes', $classes, $share_buttons_settings, $share_buttons_id );
			return implode( ' ', $classes );
		}

		function get_custom_styles( $share_buttons_settings, $share_buttons_id = 0 ){
			$style = array();
			$style[] = '--bsb-mx:' . intval( $share_buttons_settings['bsb_mx'] ) . 'px';
			$style[] = '--bsb-my:' . intval( $share_buttons_settings['bsb_my'] ) . 'px';
			$style[] = '--bsb-px:' . intval( $share_buttons_settings['bsb_px'] ) . 'px';
			$style[] = '--bsb-py:' . intval( $share_buttons_settings['bsb_py'] ) . 'px';
			$style[] = '--bsb-br:' . intval( $share_buttons_settings['bsb_br'] ) . 'px';
			$style[] = '--bsb-fz:' . intval( $share_buttons_settings['bsb_fz'] ) . 'px';
			$style[] = '--bsb-iz:' . intval( $share_buttons_settings['bsb_iz'] ) . 'px';
			$style[] = '--bsb-lls:' . intval( $share_buttons_settings['bsb_lls'] ) . 'px';
			$style[] = '--bsb-lrs:' . intval( $share_buttons_settings['bsb_lrs'] ) . 'px';
			$style = apply_filters( 'share_buttons_styles', $style, $share_buttons_settings, $share_buttons_id );
			return implode( ';', $style );
		}

		function get_link_atts( $slug = false ){
			$atts = array();
			if( $slug && isset( $this->networks[ $slug ] ) ){
				$atts = $this->networks[ $slug ];

				$post_url = apply_filters( 'better_share_buttons_permalink', get_permalink() );
				$post_title = apply_filters( 'better_share_buttons_title', get_the_title() );
				$site_name = get_bloginfo('name');

				if( ! isset( $atts['slug'] ) ){
					$atts['slug'] = $slug;
				}

				if( isset( $atts['url'] ) && $atts['url'] ){
					$atts['url'] = str_replace(
						array(
							'{{URL}}',
							'{{TITLE}}',
							'{{SITE}}',
						),
						array(
							$post_url,
							$post_title,
							$site_name,
						),
						$atts['url']
					);
				}else{
					$atts['url'] = '#';
				}

				if( ! isset( $atts['target'] ) ){
					$atts['target'] = '_blank';
				}

				if( ! isset( $atts['title'] ) ){
					$atts['title'] = $slug;
				}

				if( isset( $atts['onClick'] ) && $atts['onClick'] ){
					$atts['onClick'] = str_replace(
						array(
							'{{URL}}',
							'{{TITLE}}',
							'{{SITE}}',
						),
						array(
							$post_url,
							$post_title,
							$site_name,
						),
						$atts['onClick']
					);
				}elseif( substr( $atts['url'], 0, 10 ) != 'javascript' ){
					$atts['onClick'] = "window.open(this.href,'targetWindow','toolbar=no,location=0,status=no,menubar=no,scrollbars=yes,resizable=yes,width=600,height=400,top='+(screen.height/2-200)+',left='+(screen.width/2-300)); return false;";
				}
			}
			return $atts;
		}

		function edit_form_after_title( $post ){
			if( $post->post_type != 'share_buttons' ) return;

			wp_enqueue_script('jquery-ui-autocomplete');
			wp_enqueue_script('jquery-ui-sortable');

			$loaded_styles = array();

			$default_folder = __DIR__ . '/styles/';
			$default_styles = scandir( $default_folder, 1 );
			foreach( $default_styles as $style ){
				if( ! is_dir( $default_folder . $style ) && substr( $style, -4 ) == '.css' ){
					$style_name = esc_attr( sanitize_title( substr( $style, 0, -4 ) ) );
					$loaded_styles[ $style_name ] = plugins_url( '/styles/' . $style, __FILE__ );
					wp_enqueue_style( 'better_share_buttons_' . esc_attr( $style ), $loaded_styles[ $style_name ] );
				}
			}

			$theme_folder = get_stylesheet_directory() . '/better-share-buttons/';
			if( file_exists( $theme_folder ) ){
				$theme_styles = scandir( $theme_folder, 1 );
				foreach( $theme_styles as $style ){
					if( ! is_dir( $theme_folder . $style ) && substr( $style, -4 ) == '.css' ){
						$style_name = esc_attr( sanitize_title( substr( $style, 0, -4 ) ) );
						$loaded_styles[ $style_name ] = get_stylesheet_directory_uri() . '/better-share-buttons/' . $style;
						wp_enqueue_style( 'better_share_buttons_' . esc_attr( $style ), $loaded_styles[ $style_name ] );
					}
				}
			}

			$share_buttons_settings = get_post_meta( $post->ID, 'share_buttons_settings', 1 );
			$share_buttons_settings = is_array( $share_buttons_settings ) ? array_merge( $this->defaults, $share_buttons_settings ) : $this->defaults; ?>
			
			<br>
			
			<div class="below-h2 notice notice-info">
				<p><?php _ex( 'Put this shortcode wherever you want to see these social share buttons:', 'backend', 'better-share-buttons' ) ?> <code style="user-select:all">[better_share_buttons id="<?php echo $post->ID ?>"]</code></p>
			</div>
			
			<h3><?php _ex( 'Select share options', 'backend', 'better-share-buttons' ) ?></h3>

			<input type="hidden" name="networks_selection" id="networks_selection" value="<?php echo esc_attr( $share_buttons_settings['networks_selection'] ) ?>">
			<input type="search" id="networks_search" placeholder="<?php _ex( 'Search for social networks...', 'backend', 'better-share-buttons' ) ?>">

			<input type="hidden" name="custom_icons" id="custom_icons" value="<?php echo esc_attr( $share_buttons_settings['custom_icons'] ) ?>">

			<div id="networks_selection_sortable" class="<?php echo esc_attr( $this->get_classes( $share_buttons_settings, $post->ID ) ) ?>" style="<?php echo esc_attr( $this->get_custom_styles( $share_buttons_settings, $post->ID ) ) ?>"></div>

			<div class="share_buttons_settings">
				<div class="grid">
					<label>
						<?php _ex( 'Buttons style', 'backend', 'better-share-buttons' ) ?>
						<select name="buttons_style"><?php
							foreach( $loaded_styles as $style => $style_url ){
								echo '<option value="' . esc_url( $style_url ) . '"' . ( $share_buttons_settings['buttons_style'] == esc_url( $style_url ) ? ' selected' : '' ) . '>' . esc_html( $style ) . '</option>';
							} ?>
						</select>
					</label>

					<label>
						<?php _ex( 'Visible items', 'backend', 'better-share-buttons' ) ?>
						<input type="number" name="visible_buttons" value="<?php echo intval( $share_buttons_settings['visible_buttons'] ) ?>" required>
					</label>
					
					<label>
						<?php _ex( 'Button horizontal spacing', 'backend', 'better-share-buttons' ) ?>
						<span>
							<input type="number" name="bsb_mx" min="0" step="1" value="<?php echo intval( $share_buttons_settings['bsb_mx'] ) ?>" class="desing-item" required>
							<span>&nbsp;px</span>
						</span>
					</label>

					<label>
						<?php _ex( 'Button vertical spacing', 'backend', 'better-share-buttons' ) ?>
						<span>
							<input type="number" name="bsb_my" min="0" step="1" value="<?php echo intval( $share_buttons_settings['bsb_my'] ) ?>" class="desing-item" required>
							<span>&nbsp;px</span>
						</span>
					</label>

					<label>
						<?php _ex( 'Button horizontal inner spacing', 'backend', 'better-share-buttons' ) ?>
						<span>
							<input type="number" name="bsb_px" min="0" step="1" value="<?php echo intval( $share_buttons_settings['bsb_px'] ) ?>" class="desing-item" required>
							<span>&nbsp;px</span>
						</span>
					</label>

					<label>
						<?php _ex( 'Button vertical inner spacing', 'backend', 'better-share-buttons' ) ?>
						<span>
							<input type="number" name="bsb_py" min="0" step="1" value="<?php echo intval( $share_buttons_settings['bsb_py'] ) ?>" class="desing-item" required>
							<span>&nbsp;px</span>
						</span>
					</label>

					<label>
						<?php _ex( 'Button radius', 'backend', 'better-share-buttons' ) ?>
						<span>
							<input type="number" name="bsb_br" min="0" step="1" value="<?php echo intval( $share_buttons_settings['bsb_br'] ) ?>" class="desing-item" required>
							<span>&nbsp;px</span>
						</span>
					</label>

					<label>
						<?php _ex( 'Button icon size', 'backend', 'better-share-buttons' ) ?>
						<span>
							<input type="number" name="bsb_iz" min="0" step="1" value="<?php echo intval( $share_buttons_settings['bsb_iz'] ) ?>" class="desing-item" required>
							<span>&nbsp;px</span>
						</span>
					</label>

					<label>
						<?php _ex( 'Label font size', 'backend', 'better-share-buttons' ) ?>
						<span>
							<input type="number" name="bsb_fz" min="0" step="1" value="<?php echo intval( $share_buttons_settings['bsb_fz'] ) ?>" class="desing-item" required>
							<span>&nbsp;px</span>
						</span>
					</label>

					<label>
						<?php _ex( 'Label left spacing', 'backend', 'better-share-buttons' ) ?>
						<span>
							<input type="number" name="bsb_lls" min="0" step="1" value="<?php echo intval( $share_buttons_settings['bsb_lls'] ) ?>" class="desing-item" required>
							<span>&nbsp;px</span>
						</span>
					</label>

					<label>
						<?php _ex( 'Label right spacing', 'backend', 'better-share-buttons' ) ?>
						<span>
							<input type="number" name="bsb_lrs" min="0" step="1" value="<?php echo intval( $share_buttons_settings['bsb_lrs'] ) ?>" class="desing-item" required>
							<span>&nbsp;px</span>
						</span>
					</label>
				</div>

				<?php /* ?>
				<div class="icons" style="display:flex;flex-wrap:wrap;gap:20px;margin:40px 0 0">
					<?php foreach( $this->networks as $network ): ?>
						<?php foreach( $network['icon'] as $icon ): ?>
							<svg style="border:1px solid #f00" title="<?php echo esc_attr( $network['title'] ) ?>" viewBox="0 0 32 32" width="32" height="32" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="<?php echo esc_attr( $icon ) ?>"/></svg>
						<?php endforeach ?>
					<?php endforeach ?>
				</div>
				<?php */ ?>
			</div>

			<style>
			.ui-autocomplete{
				max-height: 150px;
				padding: 5px 0;
				overflow-y: auto;
				overflow-x: hidden;
			}
			.ui-autocomplete li{
				padding: 0;
			}
			.ui-autocomplete li .ui-menu-item-wrapper{
				padding: 6px 8px;
			}
			.ui-autocomplete li .ui-state-active{
				background: #74AFE4;
			}
			.ui-state-highlight{
				opacity: 0.5;
			}
			#networks_search{
				width: 100%;
				height: 40px;
			}
			#networks_selection_sortable{
				margin: 30px 0;
			}
			#networks_selection_sortable a{
				position: relative;
				cursor: move;
			}
			#networks_selection_sortable a em{
				position: absolute;
				top: -4px;
				right: -4px;
				display: flex;
				align-items: center;
				justify-content: center;
				width: 16px;
				height: 16px;
				background: #fff;
				border: 1px solid #000;
				border-radius: 50%;
				text-decoration: none;
				font-size: 14px;
				font-style: normal;
				color: #000;
				transform-origin: center;
				transform: scale(0);
				pointer-events: none;
				transition: all .3s ease;
				transition-property: transform, color, border-color;
			}
			#networks_selection_sortable a em.change-icon{
				top: calc( 50% - 8px );
				right: auto;
				left: -8px;
			}
			#networks_selection_sortable a:hover em{
				transform: scale(1);
				pointer-events: all;
				cursor: pointer;
			}
			#networks_selection_sortable a em:hover{
				transform: scale(1.05);
				border-color: #C91D2C;
				color: #C91D2C;
			}
			.share_buttons_settings .grid{
				display: grid;
				grid-template-columns: repeat( auto-fit, 210px );
				gap: 30px;
			}
			.share_buttons_settings .grid + .grid{
				margin-top: 30px;
			}
			.share_buttons_settings label{
				display: block;
				font-weight: bold;
			}
			.share_buttons_settings label > span{
				display: flex;
				align-items: center
			}
			.share_buttons_settings label :is( input[type=number], select ){
				width: 100%;
				margin-top: 5px;
			}
			</style><?php

			foreach( $this->networks as $slug => $network ){
				$this->networks[ $slug ]['icon'] = apply_filters( 'share_buttons_icons', $network['icon'], $slug, [], NULL );
			}

			?>
			<script>
				var bsb_icon = '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="{{PATH}}"/></svg>';
				var networks = <?php echo json_encode( $this->networks ) ?>;

				jQuery(document).ready(function($){
					function renderNetworksSelection(){
						var networks_selection = $('#networks_selection').val();
						if( networks_selection ){
							let custom_icons = $('#custom_icons').val() ? JSON.parse( $('#custom_icons').val() ) : {};
							networks_selection = networks_selection.split(',');
							$('#networks_selection_sortable').html('');
							$.each( networks_selection, function( i, network ){
								let icon_index = custom_icons.hasOwnProperty( network ) ? parseInt( custom_icons[ network ] ) : 0;
								$('#networks_selection_sortable').append(`
									<a href="#" data-network="${network}" style="--bsb-color:${networks[ network ].color}">
										${ bsb_icon.replace( '{{PATH}}', networks[ network ].icon[ icon_index ] ) }
										<span>${networks[ network ].title}</span>
										${ networks[ network ].icon.length > 1 ? `<em class="change-icon" title="change icon">&olarr;</em>` : '' }
										<em class="remove" title="remove">&times;</em>
									</a>
								`);
							});
						}
					}

					renderNetworksSelection();

					$('#networks_search')
					.focus(function(){
						$(this).autocomplete( 'search', '' );
					})
					.autocomplete({
						minLength: 0,
						scroll: true,
						source: function( request, response ){
							var networks_selection = $('#networks_selection').val();
							var searchTerm = request.term.replace( /[^a-z0-9]+/g, '' );
							var matches = Object.keys( networks ).filter(function( item ){
								return networks_selection.indexOf( item ) === -1 && item.indexOf( searchTerm ) !== -1
							});

							var data = [];
							$.each( matches, function( i, network ){
								data.push({
									label: networks[ network ].title,
									value: network
								});
							});

							response( data );
						},
						select: function( event, ui ){
							var networks_selection = $('#networks_selection').val();
							if( networks_selection ){
								networks_selection = networks_selection.split(',');
							}else{
								networks_selection = [];
							}
							networks_selection.push( ui.item.value );
							networks_selection = networks_selection.filter(function( value, index, self ){
								return self.indexOf( value ) === index;
							});
							$('#networks_selection').val( networks_selection.join(',') );
							$('#networks_search').val('');
							renderNetworksSelection();
							return false;
						}
					});

					$('#networks_selection_sortable').sortable({
						placeholder: 'ui-state-highlight',
						start: function( e, ui ){
							ui.placeholder.css( 'background-color', ui.helper.css('--bsb-color') );
							ui.placeholder.width( ui.helper.width() );
							ui.placeholder.height( ui.helper.height() );
						},
						update: function( e, ui ){
							networks_selection = [];
							$('#networks_selection_sortable [data-network]').each(function( i, el ){
								networks_selection.push( $(el).attr('data-network') );
							});
							$('#networks_selection').val( networks_selection.join(',') );
						}
					});
					$('#networks_selection_sortable').disableSelection();

					$(document).on('click', '#networks_selection_sortable a', function(e){
						e.preventDefault();
					});

					$(document).on('click', '#networks_selection_sortable a em.change-icon', function(e){
						e.preventDefault();
						let network = $(this).parent().attr('data-network');
						let item = networks[ network ];
						let custom_icons = $('#custom_icons').val() ? JSON.parse( $('#custom_icons').val() ) : {};
						let icon_index = custom_icons.hasOwnProperty( network ) ? parseInt( custom_icons[ network ] ) : 0;

						icon_index++;

						if( item.icon.length <= icon_index ){
							icon_index = 0;
						}

						custom_icons[ network ] = icon_index;

						$('#custom_icons').val( JSON.stringify( custom_icons ) );

						renderNetworksSelection();
					});

					$(document).on('click', '#networks_selection_sortable a em.remove', function(e){
						e.preventDefault();
						$(this).parent().remove();
						networks_selection = [];
						$('#networks_selection_sortable [data-network]').each(function( i, el ){
							networks_selection.push( $(el).attr('data-network') );
						});
						$('#networks_selection').val( networks_selection.join(',') );
					});

					var loaded_styles = {};

					$('select[name=buttons_style]').on('change', function(){
						let style_name = this.value.split('/').pop().slice(0,-4);
						let currentClass = ' ' + $('.better_share_buttons').attr('class') + ' ';
						currentClass = currentClass.replace(/\sbsb-style-[^\s]+/g, '');
						currentClass += ' ' + 'bsb-style-' + style_name;
						$('.better_share_buttons').attr( 'class', currentClass.trim() );

						if( ! loaded_styles.hasOwnProperty( style_name ) ){
							$.ajax({
								dataType: 'json',
								url: this.value.slice(0,-4) + '.json',
								async: false, 
								success: function( json, status ){
									if( status == 'success' ){
										loaded_styles[ style_name ] = json;
									}else{
										loaded_styles[ style_name ] = {};
									}
								}
							});
						}
						$.each( loaded_styles[ style_name ], function( key, value ){
							$('.share_buttons_settings [name="' + key + '"]').val( value ).trigger('input');
						});
					});

					$('.desing-item').on( 'input', function(){
						var styles = '';
						$('.desing-item').each(function( i, el ){
							if( el.type == 'checkbox' ){
								if( el.checked ){
									$('.better_share_buttons').addClass( el.name );
								}else{
									$('.better_share_buttons').removeClass( el.name );
								}
							}else{
								styles += '--' + el.name.replace( '_', '-' ) + ':' + el.value + 'px;';
							}
						});
						$('.better_share_buttons').attr( 'style', styles );
					});
				});
			</script><?php
		}

		function save_post_share_buttons( $post_id ){
			if( ! isset( $_POST['networks_selection'] ) ) return;
			// check nonce
			if( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'update-post_' . $post_id ) ) return;	
			// check ajax
			if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
			// check permissions
			if( ! current_user_can( 'edit_post', $post_id ) ) return;
			// save data
			$share_buttons_settings = array(
				'networks_selection' => sanitize_text_field( $_POST['networks_selection'] ),
				'custom_icons' => sanitize_text_field( $_POST['custom_icons'] ),
				'visible_buttons' => intval( $_POST['visible_buttons'] ),
				'buttons_style' => sanitize_text_field( $_POST['buttons_style'] ),
				'bsb_mx' => intval( $_POST['bsb_mx'] ),
				'bsb_my' => intval( $_POST['bsb_my'] ),
				'bsb_px' => intval( $_POST['bsb_px'] ),
				'bsb_py' => intval( $_POST['bsb_py'] ),
				'bsb_br' => intval( $_POST['bsb_br'] ),
				'bsb_iz' => intval( $_POST['bsb_iz'] ),
				'bsb_fz' => intval( $_POST['bsb_fz'] ),
				'bsb_lls' => intval( $_POST['bsb_lls'] ),
				'bsb_lrs' => intval( $_POST['bsb_lrs'] ),
			);
			update_post_meta( $post_id, 'share_buttons_settings', $share_buttons_settings );
		}

		function esc_svg_path_d( $string ){
			return preg_replace( '/[^MmLlHhVvCcSsQqTtAaZz0-9\.,\-\s\t]/', ' ', $string );
		}

		function better_share_buttons_shortcode( $atts, $content, $tag ){
			if( is_admin() ) return true;

			if( ! isset( $atts['id'] ) || ! intval( $atts['id'] ) || get_post_type( $atts['id'] ) != 'share_buttons' ) return;

			$share_buttons_settings = get_post_meta( $atts['id'], 'share_buttons_settings', 1 );
			$share_buttons_settings = is_array( $share_buttons_settings ) ? array_merge( $this->defaults, $share_buttons_settings ) : $this->defaults;

			wp_enqueue_style( 'better-share-buttons', $share_buttons_settings['buttons_style'] );

			$networks_selection = explode( ',', $share_buttons_settings['networks_selection'] );

			ob_start(); ?>
			<div class="better_share_buttons_wrapper">
				<div class="<?php echo esc_attr( $this->get_classes( $share_buttons_settings, $atts['id'] ) ) ?>" style="<?php echo esc_attr( $this->get_custom_styles( $share_buttons_settings, $atts['id'] ) ) ?>"><?php
					$i = 1;
					foreach( $networks_selection as $slug ){
						$link_atts = $this->get_link_atts( $slug );
						
						if( ! $link_atts ) continue;

						if( ( $i - 1 ) == $share_buttons_settings['visible_buttons'] ){
							echo apply_filters(
								'better_share_buttons_more',
								'<a href="#" style="--bsb-color:#fd6454" onClick="javascript:document.querySelectorAll(\'.better_share_buttons.limited\').forEach(el=>el.classList.remove(\'limited\'))" data-network="more" aria-label="' . esc_attr_x( 'More...', 'network', 'better-share-buttons' ) . '" class="better_share_buttons_more"><svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M18 14V8h-4v6H8v4h6v6h4v-6h6v-4h-6z"/></svg><span>' . esc_html_x( 'More...', 'network', 'better-share-buttons' ) . '</span></a>',
								$share_buttons_settings,
								$atts['id']
							);
						}

						if( ! is_array( $this->networks[ $slug ]['icon'] ) ){
							$this->networks[ $slug ]['icon'] = array(
								$this->networks[ $slug ]['icon']
							);
						}

						$icons = apply_filters( 'share_buttons_icons', $this->networks[ $slug ]['icon'], $slug, $share_buttons_settings, $atts['id'] );
						$custom_icons = json_decode( $share_buttons_settings['custom_icons'] );
						$icon_index = isset( $custom_icons->{$slug} ) ? intval( $custom_icons->{$slug} ) : 0;

						echo apply_filters(
							'better_share_buttons_link',
							'<a href="' . esc_url( $link_atts['url'] ) . '"' . ( isset( $link_atts['target'] ) && $link_atts['target'] ? ' target="' . esc_attr( $link_atts['target'] ) . '" rel="noopener"' : '' ) . ' style="--bsb-color:' . esc_attr( $link_atts['color'] ) . '" onClick="' . esc_attr( $link_atts['onClick'] ) . '" data-network="' . esc_attr( sanitize_title( $link_atts['slug'] ) ) . '" aria-label="' . esc_attr( $link_atts['title'] ) . '">' . str_replace( '{{PATH}}', $this->esc_svg_path_d( $icons[ $icon_index ] ), '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="{{PATH}}"/></svg>' ) . '<span>' . esc_html( $link_atts['title'] ) . '</span>' . '</a>',
							$slug,
							$share_buttons_settings,
							$atts['id']
						);

						$i++;
					} ?>
				</div>
			</div><?php
			return ob_get_clean();
		}
	}

	$better_share_buttons_var = new better_share_buttons();
}