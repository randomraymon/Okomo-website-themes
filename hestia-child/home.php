<?php /* Template Name: Okomo - Home */ ?>

<?php get_header(); ?>



<h1> This is a test 123456789 </h1>

<div class="grid-x">
  <div class="cell small-12 medium-4 large-4">
    <p>hey</p>
  </div>
</div>

<div class="menu">
  <div class="menu__unter-menu menu__unter-menu--closed">
    <ul class="menu__unter-menu__list">
      <li>1</li>
      <li>2</li>
      <li>3</li>
      <li>4</li>
    </ul>
  </div>
</div>

<p>
  <?php echo hallo; ?>
</p>

<?php 

  echo '<p>' .  hallo . '</p>'; 
  
?>

<?php the_content(); ?>

<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>

      <div class="project__info__description">
      <p><?php if(get_post_meta($post->ID, 'introduction', true)) echo get_post_meta($post->ID, 'introduction', true); ?>  </p>
      </div>

      <div class="project__info__description">
      <p><?php if(get_post_meta($post->ID, 'main_home_titel', true)) echo get_post_meta($post->ID, 'main_home_titel', true); ?>  </p>
      </div>

      <div class="project__info__description">
      <p><?php if(get_post_meta($post->ID, 'main_home_subtitel', true)) echo get_post_meta($post->ID, 'main_home_subtitel', true); ?>  </p>
      </div>



<?php $image_id = get_post_meta($post->ID, 'hero_image', true);
$image_url = wp_get_attachment_image_src( $image_id )[0]; ?>

<div class="bild">
<img src="<?php if($image_url) echo $image_url ?>" alt="" srcset="">
</div>


<?php endwhile; else : endif; ?>


<?php get_footer(); ?>
