<?php

function get_fields () {
  return array (
    'eventful_is_event',
    'eventful_month',
    'eventful_day',
    'eventful_year',
    'eventful_hour',
    'eventful_minute',
    'eventful_ampm'
  );
}

//making the meta box (Note: meta box != custom meta field)
function eventful_is_event () {
   add_meta_box(
       'eventful-meta-checkbox',       // $id
       'Eventful',                  // $title
       'show_eventful_meta_checkbox' // $callback
   );
}

function show_eventful_meta_checkbox ($post) {
  wp_nonce_field( basename( __FILE__ ), 'eventful_meta_nonce' );

  $current_eventful_is_event = get_post_meta ($post->ID, 'eventful_is_event', true);
  $is_event = ($current_eventful_is_event == 'on') ? 'checked' : '' ;
  function isMonth ($value, $post) {
    $month = get_post_meta ($post->ID, 'eventful_month', true);
    if ($month == $value) {
      _e ('selected');
    }
  }
  $current_eventful_day = get_post_meta ($post->ID, 'eventful_day', true);
  if (!$current_eventful_day) {
    $current_eventful_day = 1;
  }
  $current_eventful_year = get_post_meta ($post->ID, 'eventful_year', true);
  if (!$current_eventful_year) {
    $current_eventful_year = (int) date ('Y');
  }
  function isHour ($value, $post) {
    $hour = get_post_meta ($post->ID, 'eventful_hour', true);
    if ($hour == $value) {
      _e ('selected');
    }
  }
  function isMinute ($value, $post) {
    $minute = get_post_meta ($post->ID, 'eventful_minute', true);
    if ($minute == $value) {
      _e ('selected');
    }
  }
  function ampm ($value, $post) {
    $ampm = get_post_meta ($post->ID, 'eventful_ampm', true);
    if ($ampm == $value) {
      _e ('selected');
    }
  }

  $current_eventful_repeats = get_post_meta ($post->ID, 'eventful_repeats', true);
  $repeats = ($current_eventful_repeats == 'on') ? 'checked' : '' ;

  ?>
    <div class="inside">
      <p>
        <h5>Is Event</h5>
        <input type="radio" name="eventful_is_event" <?php _e ($is_event) ?> >
      </p>
      <p>
        <h5>Date</h5>
        <select name="eventful_month">
          <option value="1" <?php isMonth (1, $post) ?> >January</option>
          <option value="2" <?php isMonth (2, $post) ?>>February</option>
          <option value="3" <?php isMonth (3, $post) ?>>March</option>
          <option value="4" <?php isMonth (4, $post) ?>>April</option>
          <option value="5" <?php isMonth (5, $post) ?>>May</option>
          <option value="6" <?php isMonth (6, $post) ?>>June</option>
          <option value="7" <?php isMonth (7, $post) ?>>July</option>
          <option value="8" <?php isMonth (8, $post) ?>>August</option>
          <option value="9" <?php isMonth (9, $post) ?>>September</option>
          <option value="10" <?php isMonth (10, $post) ?>>October</option>
          <option value="11" <?php isMonth (11, $post) ?>>November</option>
          <option value="12" <?php isMonth (12, $post) ?>>December</option>
        </select>
        -
        <input type="number" name="eventful_day" min="1" max="31" value="<?php _e ($current_eventful_day) ?>">
        -
        <input type="number" name="eventful_year" value="<?php _e ($current_eventful_year) ?>">
      </p>
      <p>
        <h5>Time</h5>
        <select name="eventful_hour">
          <option value="1" <?php isHour (1, $post) ?> >1</option>
          <option value="2" <?php isHour (2, $post) ?> >2</option>
          <option value="3" <?php isHour (3, $post) ?> >3</option>
          <option value="4" <?php isHour (4, $post) ?> >4</option>
          <option value="5" <?php isHour (5, $post) ?> >5</option>
          <option value="6" <?php isHour (6, $post) ?> >6</option>
          <option value="7" <?php isHour (7, $post) ?> >7</option>
          <option value="8" <?php isHour (8, $post) ?> >8</option>
          <option value="9" <?php isHour (9, $post) ?> >9</option>
          <option value="10" <?php isHour (10, $post) ?> >10</option>
          <option value="11" <?php isHour (11, $post) ?> >11</option>
          <option value="12" <?php isHour (12, $post) ?> >12</option>
        </select>
        :
        <select name="eventful_minute">
          <option value="0" <?php isMinute (0, $post) ?> >00</option>
          <option value="15" <?php isMinute (15, $post) ?> >15</option>
          <option value="30" <?php isMinute (30, $post) ?> >30</option>
          <option value="45" <?php isMinute (45, $post) ?> >45</option>
        </select>
        &nbsp;
        <select name="eventful_ampm">
          <option value="am" <?php ampm ('am', $post) ?> >am</option>
          <option value="pm" <?php ampm ('pm', $post) ?> >pm</option>
        </select>
      </p>
      <p>
        <h5>Repeats</h5>
        <input type="radio" name="eventful_repeats" <?php _e ($repeats) ?> >
      </p>
    </div>
  <?php
}

function save_eventful_meta ($post_id) {
  // verify meta box nonce
  if ( !isset( $_POST['eventful_meta_nonce'] ) || !wp_verify_nonce( $_POST['eventful_meta_nonce'], basename( __FILE__ ) ) ){
    return;
  }
  // return if autosave
  if (defined ('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }
  // permissions
  if (!current_user_can ('edit_post', $post_id)) {
    return;
  }
  $fields = get_fields ();
  foreach ($fields as $value) {
    update_post_meta ($post_id, $value, sanitize_text_field ($_POST [$value]));
  }
}

add_action('add_meta_boxes', 'eventful_is_event');
add_action ('save_post', 'save_eventful_meta');