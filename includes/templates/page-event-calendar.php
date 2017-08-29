<?php
/*
Template Name: EventfulCalendar
*/
?>
<!DOCTYPE html>
<html>
<head>
  <title>Events</title>
  <?php
    wp_head ();
  ?>
</head>
<body>
<?php get_header () ?>
<?php
  _e (do_shortcode ('[eventful]'));
  wp_footer ();
?>
</body>
</html>