<?php
/*
Plugin Name: Softwareseni Custommeta
Description: Make custom meta for assignment training.
Version: 1.0.0
Author: Priya
Author URI: http://softwareseni.co.id/
License: GPLv2
*/


if ( ! defined( 'SS_CUSTOMMETA_URL' ) ) {
	define( 'SS_CUSTOMMETA_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'SS_CUSTOMMETA_PATH' ) ) {
	define( 'SS_CUSTOMMETA_PATH', plugin_dir_path( __FILE__ ) );
}


add_action( 'init', 'create_team_member' );

function create_team_member() {
    register_post_type( 'team_members',
        array(
            'labels' => array(
                'name' => 'Team Member',
                'singular_name' => 'Team Member',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New Team Member',
                'edit' => 'Edit',
                'edit_item' => 'Edit Team Member',
                'new_item' => 'New Team Member',
                'view' => 'View',
                'view_item' => 'View Team Member',
                'search_items' => 'Search Team Members',
                'not_found' => 'No Team Members found',
                'not_found_in_trash' => 'No Team Members found in Trash',
                'parent' => 'Parent Team Member'
            ),
 
            'public' => true,
            'menu_position' => 15,
            'supports' => array( 'title', 'editor', 'comments', 'thumbnail', 'custom-fields' ),
            'taxonomies' => array( '' ),
            'menu_icon' => 'dashicons-networking',
            'has_archive' => true
        )
    );
}

add_action( 'admin_init', 'custom_metadata_admin' );

function custom_metadata_admin() {
    add_meta_box( 'team_members_meta_box',
        'Team Member Details',
        'display_team_members_meta_box',
        'team_members', 'normal', 'high'
    );
}

function display_team_members_meta_box( $team_member ) {
    // Retrieve current name of the Director and Movie Rating based on review ID
    
    $position = esc_attr( get_post_meta( $team_member->ID, 'position', true ) );
    $email = esc_attr( get_post_meta( $team_member->ID, 'email', true ) );
    $phone = esc_attr( get_post_meta( $team_member->ID, 'phone', true ) );
    $website = esc_attr( get_post_meta( $team_member->ID, 'website', true ) );
    $image = esc_url( get_post_meta( $team_member->ID, 'image', true ) );
    ?>
    <table class="wp-list-table widefat fixed striped table-view-list testimonials">
        <tr>
            <th style="width: 15%; text-align: right; font-weight: bold;">Position</th>
            <td style="width: 70%"><input type="text" size="40" name="tm_position" value="<?php echo $position; ?>" /></td>
        </tr>
        <tr>
            <th style="width: 15%; text-align: right; font-weight: bold;">Email</th>
            <td><input type="email" size="40" name="tm_email" value="<?php echo $email; ?>" /></td>
        </tr>
        <tr>
            <th style="width: 15%; text-align: right; font-weight: bold;">Phone</th>
            <td><input type="text" size="40" name="tm_phone" value="<?php echo $phone; ?>" /></td>
        </tr>
        <tr>
            <th style="width: 15%; text-align: right; font-weight: bold;">Website</th>
            <td><input type="url" size="40" name="tm_website" value="<?php echo $website; ?>" /></td>
        </tr>
        <tr>
            <th style="width: 15%; text-align: right; font-weight: bold;">Image</th>
            <td>
            	<a href="#" class="tm_upload_image_button button button-secondary"><?php _e('Upload Image'); ?></a>
            	<input type="hidden" id="tm_image" name="tm_image" value="<?php echo $image; ?>" />
            	<p>
	            	<img id="tm_image_display" width="auto" height="150" src="<?php echo $image; ?>" class="attachment-post-thumbnail size-post-thumbnail" alt="" loading="lazy">
	            </p>
            </td>
        </tr>
    </table>
    <?php
}

add_action( 'admin_enqueue_scripts', 'tm_include_script' );

function tm_include_script() {
  
    if ( ! did_action( 'wp_enqueue_media' ) ) {
        wp_enqueue_media();
    }
  	wp_enqueue_script( 'tm-image-js', SS_CUSTOMMETA_URL . '/js/ss-custommeta.js', array('jquery'), '1.0.0', true );
}

add_action('save_post', 'tm_save_postdata', 10, 2);

function tm_save_postdata($post_id, $team_member)
{
    if ( $team_member->post_type == 'team_members' ) {
        // Store data in post meta table if present in post data
        if ( isset( $_POST['tm_position'] ) && $_POST['tm_position'] != '' ) {
            update_post_meta( $post_id, 'position', $_POST['tm_position'] );
        }

        if ( isset( $_POST['tm_email'] ) && $_POST['tm_email'] != '' ) {
            update_post_meta( $post_id, 'email', $_POST['tm_email'] );
        }

        if ( isset( $_POST['tm_phone'] ) && $_POST['tm_phone'] != '' ) {
            update_post_meta( $post_id, 'phone', $_POST['tm_phone'] );
        }

        if ( isset( $_POST['tm_website'] ) && $_POST['tm_website'] != '' ) {
            update_post_meta( $post_id, 'website', $_POST['tm_website'] );
        }

        if (array_key_exists('tm_image', $_POST)) {
	        update_post_meta(
	            $post_id,
	            'image',
	            $_POST['tm_image']
	        );
	    }
    }
}

add_filter( 'rwmb_meta_boxes', 'mb_register_meta_boxes' );

function mb_register_meta_boxes( $meta_boxes ) {
    $prefix = 'mb_';

    $meta_boxes[] = [
        'title'      => esc_html__( 'MB Team Member', 'online-generator' ),
        'id'         => 'mb_team_member',
        'post_types' => ['team_members'],
        'context'    => 'normal',
        'fields'     => [
            [
                'type'       => 'text',
                'name'       => esc_html__( 'MB Position', 'online-generator' ),
                'id'         => $prefix . 'mb_position',
                'attributes' => [
                    'required' => true,
                ],
            ],
            [
                'type' => 'text',
                'name' => esc_html__( 'MB Email', 'online-generator' ),
                'id'   => $prefix . 'mb_email',
            ],
            [
                'type' => 'text',
                'name' => esc_html__( 'MB Phone', 'online-generator' ),
                'id'   => $prefix . 'mb_phone',
            ],
            [
                'type' => 'url',
                'name' => esc_html__( 'MB Website', 'online-generator' ),
                'id'   => $prefix . 'mb_website',
            ],
            [
                'type'             => 'image_advanced',
                'name'             => esc_html__( 'MB Image', 'online-generator' ),
                'id'               => $prefix . 'mb_image',
                'max_file_uploads' => 1,
            ],
        ],
    ];

    return $meta_boxes;
}

add_shortcode( 'ss_team_member', 'ss_team_member_shortcode' );

function ss_team_member_shortcode( $atts, $content = '' ) {	
	ob_start();

	$datas = get_teams( 5 );

	$displays = isset( $atts['displays'] ) ? $atts['displays'] : '';
	$fields_displays = explode(',', $displays);

	if( $datas ){
		echo '<div class="row">';
		foreach ($datas as $data) {
			$position = '';
			if( ( '' != $displays &&  in_array('position', $fields_displays) ) || '' == $displays ) {
				$position = esc_attr( get_post_meta( $data['ID'], 'position', true ) );
			}

			$email = '';
			if( ( '' != $displays &&  in_array('email', $fields_displays) ) || '' == $displays ) {
				$email = esc_attr( get_post_meta( $data['ID'], 'email', true ) );
			}

			$phone = '';
			if( ( '' != $displays &&  in_array('phone', $fields_displays) ) || '' == $displays ) {
				$phone = esc_attr( get_post_meta( $data['ID'], 'phone', true ) );
			}

			$website = '';
			if( ( '' != $displays &&  in_array('website', $fields_displays) ) || '' == $displays ) {
				$website = esc_attr( get_post_meta( $data['ID'], 'website', true ) );
			}

			$image = '';
			if( ( '' != $displays &&  in_array('image', $fields_displays) ) || '' == $displays ) {
				$image = esc_url( get_post_meta( $data['ID'], 'image', true ) );
			}
			?>		
			<div class="column">
				<p>
	            	<img width="auto" height="150" src="<?php echo $image; ?>" class="attachment-post-thumbnail size-post-thumbnail" alt="" loading="lazy">
	            </p>
			  	<h3><?php echo $data['post_title']; ?></h3>
			  	<strong><?php echo $position; ?></strong>
			  	<div class="note"><?php echo $email; ?></div>
			  	<div class="note"><?php echo $phone; ?></div>
			  	<div class="note"><?php echo $website; ?></div>
			</div>

			<style>
				* {
				  box-sizing: border-box;
				}

				/* Create two equal columns that floats next to each other */
				.column {
				  float: left;
				  width: 30%;
				  padding: 10px;
				  margin: 10px;
				  height: 300px; /* Should be removed. Only for demonstration */
				}

				/* Clear floats after the columns */
				.row:after {
				  content: "";
				  display: table;
				  clear: both;
				}

				img {
				  border-radius: 90%;
				}

				strong{
					font-size: 15px;
				}

				.note{
					font-size: 12px;
				}
			</style>
			<?php
		}
	}	echo '</div>';

	$content .= ob_get_contents();
	ob_end_clean();

	return $content;
}

if ( function_exists( 'rwmb_meta' ) ) {

	add_shortcode( 'ss_mb_team_member', 'ss_mb_team_member_shortcode' );

	function ss_mb_team_member_shortcode( $atts, $content = '' ) {
		ob_start();

		$datas = get_teams( 5 );

		$displays = isset( $atts['displays'] ) ? $atts['displays'] : '';
		$fields_displays = explode(',', $displays);

		if( $datas ){
			echo '<div class="row">';
			$prefix = 'mb_';
			foreach ($datas as $data) {
				$position = rwmb_meta( $prefix .'mb_position', '', $data['ID'] );
			    $email = rwmb_meta( $prefix .'mb_email', '', $data['ID'] );
			    $phone = rwmb_meta( $prefix .'mb_phone', '', $data['ID'] );
			    $website = rwmb_meta( $prefix .'mb_website', '', $data['ID'] );
			    $images = rwmb_meta( $prefix .'mb_image', array( 'limit' => 1 ), $data['ID'] );
			    $image = reset( $images );

			    $position = '';
				if( ( '' != $displays &&  in_array('position', $fields_displays) ) || '' == $displays ) {
					$position = rwmb_meta( $prefix .'mb_position', '', $data['ID'] );
				}

				$email = '';
				if( ( '' != $displays &&  in_array('email', $fields_displays) ) || '' == $displays ) {
					$email = rwmb_meta( $prefix .'mb_email', '', $data['ID'] );
				}

				$phone = '';
				if( ('' != $displays &&  in_array('phone', $fields_displays) ) || '' == $displays ) {
					$phone = rwmb_meta( $prefix .'mb_phone', '', $data['ID'] );
				}

				$website = '';
				if( ('' != $displays &&  in_array('website', $fields_displays) ) || '' == $displays ) {
					$website = rwmb_meta( $prefix .'mb_website', '', $data['ID'] );
				}

				$image = '';
				if( ('' != $displays && in_array('image', $fields_displays) ) || '' == $displays ) {
					$images = rwmb_meta( $prefix .'mb_image', array( 'limit' => 1 ), $data['ID'] );
			    	$image = reset( $images );
				}
				?>		
				<div class="column">
					<p>
		            	<img width="auto" height="150" src="<?php echo $image['url']; ?>" class="mb attachment-post-thumbnail size-post-thumbnail" alt="" loading="lazy">
		            </p>
				  	<h3><?php echo $data['post_title']; ?></h3>
				  	<strong><?php echo $position; ?></strong>
				  	<div class="note"><?php echo $email; ?></div>
				  	<div class="note"><?php echo $phone; ?></div>
				  	<div class="note"><?php echo $website; ?></div>
				</div>

				<style>
					* {
					  box-sizing: border-box;
					}

					/* Create two equal columns that floats next to each other */
					.column {
					  float: left;
					  width: 30%;
					  padding: 10px;
					  margin: 10px;
					  height: 300px; /* Should be removed. Only for demonstration */
					}

					/* Clear floats after the columns */
					.row:after {
					  content: "";
					  display: table;
					  clear: both;
					}

					.mb {
					  border-radius: 50%;
					}

					strong{
						font-size: 15px;
					}

					.note{
						font-size: 12px;
					}
				</style>
				<?php
			}
		}	echo '</div>';

		$content .= ob_get_contents();
		ob_end_clean();

		return $content;
	}

}


function get_teams( $limit = 5 ){
	global $wpdb;

    $custom_post_type = 'team_members';
    $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->posts} WHERE post_type = %s and post_status = 'publish' LIMIT %d", $custom_post_type, $limit ), ARRAY_A );

    if ( ! $results ){
        return;
    } else {
    	return $results;
    }
}



?>