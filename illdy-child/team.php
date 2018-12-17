<?php /* Template Name: Okomo - Team */ ?>

<?php get_header(); ?>

<div class="test-div">
  <div class="test-div-div">
    
  </div>
</div>

<?php the_content(); ?>

<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
 
 <div class="project__info__description">
   <p><?php if(get_post_meta($post->ID, 'team_subtitel', true)) echo get_post_meta($post->ID, 'team_subtitel', true); ?>  </p>
 </div>

<?php $image_id = get_post_meta($post->ID, 'hero_image', true);
$image_url = wp_get_attachment_image_src( $image_id )[0]; ?>

<div class="bild">
<img src="<?php if($image_url) echo $image_url ?>" alt="" srcset="">
</div> 


<?php endwhile; else : endif; ?>


<?php get_footer(); ?>
