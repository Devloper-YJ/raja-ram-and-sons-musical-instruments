<?php get_header(); ?>

<div class="max-w-[1400px] mx-auto px-4 sm:px-6 py-10 sm:py-16 flex-1 min-h-[60vh]">
    <?php
    if ( have_posts() ) {
        while ( have_posts() ) {
            the_post();
            ?>
            <h1 class="text-3xl font-serif font-bold text-brand-dark mb-8 border-b pb-4"><?php the_title(); ?></h1>
            
            <!-- This is where WooCommerce will output its default UI -->
            <div class="woocommerce-content-area w-full">
                <?php the_content(); ?>
            </div>
            <?php
        }
    }
    ?>
</div>

<?php get_footer(); ?>