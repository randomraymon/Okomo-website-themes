<?php /* Template Name: Okomo - Home */ ?>

<?php get_header(); ?>

<h1> This is a test 123456789 </h1>

<?php the_content(); ?>

<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
 
 <div class="project__info__description">
   <p><?php if(get_post_meta($post->ID, 'home_subtitel', true)) echo get_post_meta($post->ID, 'home_subtitel', true); ?>  </p>
 </div>

<?php $image_id = get_post_meta($post->ID, 'hero_image', true);
$image_url = wp_get_attachment_image_src( $image_id )[0]; ?>

<div class="bild">
<img src="<?php if($image_url) echo $image_url ?>" alt="" srcset="">
</div> 


<?php endwhile; else : endif; ?>


<?php get_footer(); ?>
