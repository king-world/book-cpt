<?php
/**
 * Plugin Name: Book CPT
 * Description: This plugin registers a "Book" custom post type with a "Genre" taxonomy.
 * Version: 1.0
 * Author: Ubong Obot
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Book_Custom_Post_Type {
    public function __construct() {
        add_action('init', [$this, 'register_book_cpt']);
        add_action('init', [$this, 'register_genre_taxonomy']);
        add_action('add_meta_boxes', [$this, 'add_book_meta_boxes']);
        add_action('save_post', [$this, 'save_book_meta']);
        add_filter('the_content', [$this, 'display_author_name']);
        add_filter('template_include', [$this, 'load_archive_template']);
        register_activation_hook(__FILE__, [$this, 'activate_plugin']);
    }

    // Register the Custom Post Type
    public function register_book_cpt() {
        $args = array(
            'labels'             => array(
                'name'               => 'Books',
                'singular_name'      => 'Book',
                'menu_name'          => 'Books',
                'name_admin_bar'     => 'Book',
                'add_new'            => 'Add New',
                'add_new_item'       => 'Add New Book',
                'new_item'           => 'New Book',
                'edit_item'          => 'Edit Book',
                'view_item'          => 'View Book',
                'all_items'          => 'All Books',
                'search_items'       => 'Search Books',
                'not_found'          => 'No books found',
                'not_found_in_trash' => 'No books found in Trash'
            ),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'book'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-book-alt',
            'supports'           => array('title', 'editor', 'thumbnail')
        );

        register_post_type('book', $args);
    }

    // Register the Genre Taxonomy
    public function register_genre_taxonomy() {
        $args = array(
            'labels'            => array(
                'name'              => 'Genres',
                'singular_name'     => 'Genre',
                'search_items'      => 'Search Genres',
                'all_items'         => 'All Genres',
                'edit_item'         => 'Edit Genre',
                'update_item'       => 'Update Genre',
                'add_new_item'      => 'Add New Genre',
                'new_item_name'     => 'New Genre Name',
                'menu_name'         => 'Genres'
            ),
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'hierarchical'      => false,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'genre')
        );

        register_taxonomy('genre', 'book', $args);
    }

    // Add Custom Meta Boxes
    public function add_book_meta_boxes() {
        add_meta_box('book_details', 'Book Details', [$this, 'book_details_callback'], 'book', 'normal', 'high');
        add_meta_box('author_name', 'Author Name', [$this, 'author_name_callback'], 'book', 'normal', 'high');
    }


    //Book Details Meta Box Callback
    public function book_details_callback($post) {
        $book_details = get_post_meta($post->ID, '_book_details', true);
        echo '<textarea name="book_details" style="width:100%; height:100px;">' . esc_textarea($book_details) . '</textarea>';
    }
    
    
    // Author Meta Box Callback
    public function author_name_callback($post) {
        $author_name = get_post_meta($post->ID, '_author_name', true);
        echo '<input type="text" name="author_name" value="' . esc_attr($author_name) . '" style="width:100%;">';
    }

    // Save Meta Box Data
    public function save_book_meta($post_id) {
        if (array_key_exists('book_details', $_POST)) {
            update_post_meta($post_id, '_book_details', sanitize_textarea_field($_POST['book_details']));
        }
        if (array_key_exists('author_name', $_POST)) {
            update_post_meta($post_id, '_author_name', sanitize_text_field($_POST['author_name']));
        }
    }

    // Display Author Name on Frontend
    public function display_author_name($content) {
        if (is_singular('book')) {
            $author_name = get_post_meta(get_the_ID(), '_author_name', true);
            $book_details = get_post_meta(get_the_ID(), '_book_details', true);
            if (!empty($author_name)) {
                $content .= '<p><strong>Author:</strong> ' . esc_html($author_name) . '</p>';
            }
            if (!empty($book_details)) {
                $content .= '<p><strong>Book Details:</strong> ' . esc_html($book_details) . '</p>';
            }
        }
        return $content;
    }

    // Load Archive Template from Plugin
    public function load_archive_template($template) {
        if (is_post_type_archive('book')) {
            $plugin_template = plugin_dir_path(__FILE__) . 'templates/archive-book.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }
        return $template;
    }

    // Flush Rewrite Rules on Activation
    public function activate_plugin() {
        $this->register_book_cpt();
        flush_rewrite_rules();
    }
}

// Initialize the Plugin
new Book_Custom_Post_Type();
