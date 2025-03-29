<?php get_header(); ?>

<div class="container">
    <h1>Books Archive</h1>

    <form method="GET" action="">
        <select name="genre" onchange="this.form.submit()">
            <option value="">All Genres</option>
            <?php
            $terms = get_terms(array('taxonomy' => 'genre', 'hide_empty' => true));
            foreach ($terms as $term) {
                $selected = (isset($_GET['genre']) && $_GET['genre'] == $term->slug) ? 'selected' : '';
                echo '<option value="' . esc_attr($term->slug) . '" ' . $selected . '>' . esc_html($term->name) . '</option>';
            }
            ?>
        </select>
    </form>

    <?php
    $args = array('post_type' => 'book', 'posts_per_page' => 10);
    if (isset($_GET['genre']) && $_GET['genre'] != '') {
        $args['tax_query'] = array(array('taxonomy' => 'genre', 'field' => 'slug', 'terms' => $_GET['genre']));
    }
    $book_query = new WP_Query($args);
    if ($book_query->have_posts()) : ?>
        <div class="book-list">
            <?php while ($book_query->have_posts()) : $book_query->the_post(); ?>
                <div class="book-item">
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <?php if (has_post_thumbnail()) : ?>
                        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium'); ?></a>
                    <?php endif; ?>
                    <p><strong>Author:</strong> <?php echo esc_html(get_post_meta(get_the_ID(), '_author_name', true)); ?></p>
                    <p><?php the_excerpt(); ?></p>
                </div>
            <?php endwhile; ?>
        </div>
        <?php the_posts_pagination(); ?>
    <?php else : ?>
        <p>No books found.</p>
    <?php endif;
    wp_reset_postdata(); ?>
</div>

<?php get_footer(); ?>
