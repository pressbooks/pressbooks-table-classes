<?php
/**
 * Plugin Name:     Pressbooks Table Classes
 * Plugin URI:      https://github.com/pressbooks/pressbooks-table-classes
 * Description:     Add custom table, cell and row classes to the Pressbooks table editor.
 * Author:          Pressbooks
 * Author URI:      https://pressbooks.com
 * Text Domain:     pressbooks-table-classes
 * Domain Path:     /languages
 * Version:         1.0.0
 * License:					GPLv2
 * GitHub Plugin URI: https://github.com/pressbooks/pressbooks-table-classes
 *
 * @package         Pressbooks_Table_Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// -------------------------------------------------------------------------------------------------------------------
// Check minimum requirements
// -------------------------------------------------------------------------------------------------------------------

if ( ! @include_once( WP_PLUGIN_DIR . '/pressbooks/compatibility.php' ) ) {
	add_action( 'admin_notices', function () {
		echo '<div id="message" class="error fade"><p>' . __( 'Cannot find Pressbooks install.', 'pressbooks-table-classes' ) . '</p></div>';
	} );
	return;
} elseif ( ! pb_meets_minimum_requirements() ) {
	return;
}

add_action( 'admin_menu', 'pb_tc_add_admin_menu', 30 );
add_action( 'admin_init', 'pressbooks_table_classes_init' );


function pb_tc_add_admin_menu() {
	add_options_page( __( 'Table Classes', 'pressbooks-table-classes' ), __( 'Table Classes', 'pressbooks-table-classes' ), 'manage_options', 'pressbooks_table_classes', 'pb_tc_options_page' );
}


function pressbooks_table_classes_init() {
	register_setting( 'pressbooks_table_classes', 'pressbooks_table_classes', 'pb_tc_sanitize' );

	add_settings_section(
		'pb_tc_pressbooks_table_classes_section',
		__( 'Table Classes', 'pressbooks-table-classes' ),
		'pressbooks_table_classes_section_callback',
		'pressbooks_table_classes'
	);

	add_settings_field(
		'table_classes',
		__( 'Custom Table Classes', 'pressbooks-table-classes' ),
		'table_classes_render',
		'pressbooks_table_classes',
		'pb_tc_pressbooks_table_classes_section',
		array(
			__( 'Class names must be comma-separated and cannot contain spaces.', 'pressbooks-table-classes' )
		)
	);

	add_settings_field(
		'cell_classes',
		__( 'Custom Cell Classes', 'pressbooks-table-classes' ),
		'cell_classes_render',
		'pressbooks_table_classes',
		'pb_tc_pressbooks_table_classes_section',
		array(
			__( 'Class names must be comma-separated and cannot contain spaces.', 'pressbooks-table-classes' )
		)
	);

	add_settings_field(
		'row_classes',
		__( 'Custom Row Classes', 'pressbooks-table-classes' ),
		'row_classes_render',
		'pressbooks_table_classes',
		'pb_tc_pressbooks_table_classes_section',
		array(
			__( 'Class names must be comma-separated and cannot contain spaces.', 'pressbooks-table-classes' )
		)
	);
}

function table_classes_render( $args ) {
	$options = get_option( 'pressbooks_table_classes' ); ?>
	<input type='text' name='pressbooks_table_classes[table_classes]' value='<?php echo @$options['table_classes']; ?>' class='regular-text'>
	<p class='description'><?php echo $args[0]; ?></p>
<?php }


function cell_classes_render( $args ) {
	$options = get_option( 'pressbooks_table_classes' ); ?>
	<input type='text' name='pressbooks_table_classes[cell_classes]' value='<?php echo @$options['cell_classes']; ?>' class='regular-text'>
	<p class='description'><?php echo $args[0]; ?></p>
<?php }


function row_classes_render( $args ) {
	$options = get_option( 'pressbooks_table_classes' ); ?>
	<input type='text' name='pressbooks_table_classes[row_classes]' value='<?php echo @$options['row_classes']; ?>' class='regular-text'>
	<p class='description'><?php echo $args[0]; ?></p>
<?php }

function pressbooks_table_classes_section_callback() {
	echo __( 'Add custom table, cell and row classes for the table editor below.', 'pressbooks-table-classes' );
}

function pb_tc_options_page() {
?><form action='options.php' method='post'>
	<?php
		settings_fields( 'pressbooks_table_classes' );
		do_settings_sections( 'pressbooks_table_classes' );
		submit_button();
	?>
	</form>
<?php }

function pb_tc_sanitize( $input ) {
	$output = array();
	foreach ( $input as $key => $value ) {
		if ( '' !== rtrim( $value ) ) {
			$output[ $key ] = sanitize_text_field( $value );
		}
	}
	return $output;
}

function pb_tc_table_classes( $classes ) {
	$options = get_option( 'pressbooks_table_classes' );
	if ( isset( $options['table_classes'] ) ) {
		$table_classes = explode( ',', $options['table_classes'] );
		if ( ! is_array( $table_classes ) ) {
			$table_classes = array( $table_classes );
		}
		foreach ( $table_classes as $class ) {
			$classes[] = array(
				'title' => ucwords( str_replace( array( '-', '_' ), array( ' ', ' ' ), $class ) ),
				'value' => $class,
			);
		}
	}
	return $classes;
}

function pb_tc_cell_classes( $classes ) {
	$options = get_option( 'pressbooks_table_classes' );
	if ( isset( $options['cell_classes'] ) ) {
		$cell_classes = explode( ',', $options['cell_classes'] );
		if ( ! is_array( $cell_classes ) ) {
			$cell_classes = array( $cell_classes );
		}
		foreach ( $cell_classes as $class ) {
			$classes[] = array(
				'title' => ucwords( str_replace( array( '-', '_' ), array( ' ', ' ' ), $class ) ),
				'value' => $class,
			);
		}
	}
	return $classes;
}

function pb_tc_row_classes( $classes ) {
	$options = get_option( 'pressbooks_table_classes' );
	if ( isset( $options['row_classes'] ) ) {
		$row_classes = explode( ',', $options['row_classes'] );
		if ( ! is_array( $row_classes ) ) {
			$row_classes = array( $row_classes );
		}
		foreach ( $row_classes as $class ) {
			$classes[] = array(
				'title' => ucwords( str_replace( array( '-', '_' ), array( ' ', ' ' ), $class ) ),
				'value' => $class,
			);
		}
	}
	return $classes;
}

add_filter( 'pressbooks_editor_table_classes', 'pb_tc_table_classes' );
add_filter( 'pressbooks_editor_cell_classes', 'pb_tc_cell_classes' );
add_filter( 'pressbooks_editor_row_classes', 'pb_tc_row_classes' );
