<?php
  class DateManager {
    public function __construct () {
      // setup date constants
      $this->month = (int) date ('m');
      if (isset ($_GET ['month'])) {
        $this->month = $_GET ['month'];
      }
      $this->year = (int) date ('Y');
      if (isset ($_GET ['year'])) {
        $this->year = $_GET ['year'];
      }
      // what was the query
      $this->query_text = (isset ($_GET ['q'])) ? $_GET ['q'] : '';
      $this->query_tags = implode (', ', explode (' ', $this->query_text));
      $this->query_html = esc_html ($this->query_text);
      // urls for different modes
      $this->mode = isset ($_GET ['view']) ? $_GET ['view'] : 'list';
      $this->list_mode = 'list';
      $this->calendar_mode = 'calendar';
      $this->list_query = array ('page' => 'eventful', 'month' => $this->month, 'year' => $this->year, 'view' => $this->list_mode);
      $this->calendar_query = array ('page' => 'eventful', 'month' => $this->month, 'year' => $this->year, 'view' => $this->calendar_mode);
      $this->list_url = esc_url (add_query_arg ($this->list_query, $_SERVER ['REQUEST_URI']));
      $this->calendar_url = esc_url (add_query_arg ($this->calendar_query, $_SERVER ['REQUEST_URI']));
      // urls for different months
      $this->next_query = array('page' => 'eventful', 'month' => ($this->month + 1 == 12 ? 0 : $this->month + 1), 'year' => ($this->month + 1 == 12 ? $this->year + 1 : $this->year), 'view' => $this->mode);
      $this->last_query = array('page' => 'eventful', 'month' => ($this->month - 1 == -1 ? 11 : $this->month - 1), 'year' => ($this->month - 1 == -1 ? $this->year - 1 : $this->year), 'view' => $this->mode);
      $this->next_url = esc_url (add_query_arg ($this->next_query, $_SERVER ['REQUEST_URI']));
      $this->last_url = esc_url (add_query_arg ($this->last_query, $_SERVER ['REQUEST_URI']));
      // beginning of the month
      $this->timestamp = mktime (0, 0, 0, $this->month, 1, $this->year);
      // calendar view 'title'
      $this->month_title = date ('F, Y', $this->timestamp);
      $this->short_month_title = date ('M, y', $this->timestamp);
      // stuff for looping over all days in the month
      $this->day_of_week = date ('w', $this->timestamp);
      // the cell number we are in
      $this->position = 0;
      // current day of month
      $this->current = 1;
      // last day of month
      $this->max = date ('t', $this->timestamp);
      // call the appropriate
      if ($this->mode == 'calendar') {
        $this->calendar ();
      } else if ($this->mode == 'list') {
        $this->list ();
      } else if ($this->mode == 'search') {
        $this->search ();
      }
    }
    public function get_search_contents () {
      $args = array (
        'meta_query' => array (
          'key' => 'is_event',
          'value' => 'on'
        ),
        'tag' => $this->query_tags
      );
      $search_results = new WP_Query ($args);
      if (!$search_results->have_posts ()) {
        _e ('<li class="list-group-item">');
        _e ('<span class="eventful-list-content ma">');
        _e ('No events found!');
        _e ('</span>');
        _e ('</li>');
      }
      while ($search_results->have_posts ()) {
        $search_results->the_post ();
        $minute = get_post_meta (get_the_ID (), 'eventful_minute', true);
        if (strlen ($minute) == 1) {
          $minute = '00';
        }
        _e ('<li class="list-group-item">');
        _e ('<span class="eventful-cell-number">');
        _e (
          sprintf (
            '%s/%s/%s',
            get_post_meta (get_the_ID (), 'eventful_month', true),
            get_post_meta (get_the_ID (), 'eventful_day', true),
            get_post_meta (get_the_ID (), 'eventful_year', true)
          )
        );
        _e ('</span>');
        _e ('<span class="eventful-list-content ma">');
        _e ('<a href="');
        the_permalink ();
        _e ('">');
        _e ('<b>');
        _e (get_post_meta (get_the_ID (), 'eventful_hour', true));
        _e (':');
        _e ($minute);
        _e (' ');
        _e (get_post_meta (get_the_ID (), 'eventful_ampm', true));
        _e ('</b>');
        _e (' - ');
        _e (the_title ());
        _e ('</a>');
        _e ('</span>');
        _e ('</li>');
      }
      wp_reset_postdata ();
    }
    public function get_list_contents () {
      $has_events = false;
      while ($this->current <= $this->max) {
        $args = array (
          'meta_query' => array (
            array (
              'key' => 'eventful_day',
              'value' => $this->current
            ),
            array (
              'key' => 'eventful_month',
              'value' => $this->month
            ),
            array (
              'key' => 'eventful_year',
              'value' => $this->year
            )
          )
        );
        $daily_events = new WP_Query ($args);
        while ($daily_events->have_posts ()) {
          $daily_events->the_post ();
          $minute = get_post_meta (get_the_ID (), 'eventful_minute', true);
          if (strlen ($minute) == 1) {
            $minute = '00';
          }
          _e ('<li class="list-group-item">');
          _e ('<span class="eventful-cell-number">');
          _e (sprintf ('%s/%s/%s', $this->month, $this->current, $this->year));
          _e ('</span>');
          _e ('<span class="eventful-list-content ma">');
          _e ('<a href="');
          the_permalink ();
          _e ('">');
          _e ('<b>');
          _e (get_post_meta (get_the_ID (), 'eventful_hour', true));
          _e (':');
          _e ($minute);
          _e (' ');
          _e (get_post_meta (get_the_ID (), 'eventful_ampm', true));
          _e ('</b>');
          _e (' - ');
          _e (the_title ());
          _e ('</a>');
          _e ('</span>');
          _e ('</li>');
          $has_events = true;
        }
        wp_reset_postdata ();
        $this->current += 1;
      }        
      if (!$has_events) {
        _e ('<li class="list-group-item">');
        _e ('<span class="eventful-list-content ma">');
        _e ('No events this month!');
        _e ('</span>');
        _e ('</li>');
      }
    }
    // call in every cell to generate day number and cell data
    public function get_cell_contents () {
      // open day number tag
      _e ('<span class="eventful-cell-number">');
      // check if we don't need to write a number 
      if ($this->day_of_week > $this->position) {
        // move to the next cell
        $this->position += 1;
        // don't write a number and close tag
        _e ('</span>');
      } else if ($this->current <= $this->max) {
        // do all this if we are still in the month
        // write the day numberand close tag
        _e ($this->current);
        _e ('</span>');
        // generate wp query args for today
        $args = array (
          'meta_query' => array (
            array (
              'key' => 'eventful_day',
              'value' => $this->current
            ),
            array (
              'key' => 'eventful_month',
              'value' => $this->month
            ),
            array (
              'key' => 'eventful_year',
              'value' => $this->year
            )
          )
        );
        // this holds all events for the day
        $daily_events = new WP_Query ($args);
        // loop over all posts
        while ($daily_events->have_posts ()) {
          // get the current post and move on
          $daily_events->the_post ();
          // fix minute
          $minute = get_post_meta (get_the_ID (), 'eventful_minute', true);
          if (strlen ($minute) == 1) {
            $minute = '00';
          }
          // echo markup
          _e ('<br>');
          _e ('<a href="');
          the_permalink ();
          _e ('">');
          _e ('<b>');
          _e (get_post_meta (get_the_ID (), 'eventful_hour', true));
          _e (':');
          _e ($minute);
          _e (' ');
          _e (get_post_meta (get_the_ID (), 'eventful_ampm', true));
          _e ('</b>');
          _e (' - ');
          _e (the_title ());
          _e ('</a>');
        }
        // needed when using custom queries
        wp_reset_postdata ();
        // move to next cell
        $this->position += 1;
        // move to next day
        $this->current += 1;
        // move to next day of week
        $this->day_of_week += 1;
        $this->day_of_week = $this->day_of_week % 7;
      }
    }
    public function query () {
      ?>
        <input type="hidden" name="month" value="<?php _e ($this->month) ?>">
        <input type="hidden" name="year" value="<?php _e ($this->year) ?>">
        <input type="hidden" name="view" value="search">
      <?php
    }
    function calendar () {
      ?>
      <br>
      <div class="container eventful-zilla ">
        <div class="row hidden-sm-down">
          <div class="col-2 ma text-center">
            <div class="btn-group align-middle" role="group">
              <a href="<?php _e($this->last_url) ?>" class="btn btn-secondary"><i class="fa fa-chevron-left"></i></a>
              <a href="<?php _e($this->next_url) ?>" class="btn btn-secondary"><i class="fa fa-chevron-right"></i></a>
            </div>
          </div>
          <div class="col-md-4 hidden-sm-down text-center ma">
            <h3 class="ma"><?php _e ($this->month_title) ?></h3>
          </div>
          <div class="col-4 hidden-sm-down ma">
            <form>
              <?php $this->query () ?>
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-search"></i>
                </span>
                <input type="text" name="q">
              </div>
            </form>
          </div>
          <div class="col-2 text-center ma">
            <div class="btn-group align-middle" role="group">
              <a href="<?php _e ($this->list_url) ?>" class="btn btn btn-secondary"><i class="fa fa-list"></i></a>
              <button disabled class="btn btn btn-secondary"><i class="fa fa-calendar"></i></button>
            </div>
          </div>
        </div>

        <div class="row hidden-md-up">
          <div class="col-4 ma text-center">
            <div class="btn-group align-middle" role="group">
              <a href="<?php _e($this->last_url) ?>" class="btn btn-secondary"><i class="fa fa-chevron-left"></i></a>
              <a href="<?php _e($this->next_url) ?>" class="btn btn-secondary"><i class="fa fa-chevron-right"></i></a>
            </div>
          </div>
          <div class="col-4 hidden-md-up text-center ma">
            <h3 class="ma"><?php _e ($this->short_month_title) ?></h3>
          </div>
          <div class="col-4 text-center ma">
            <div class="btn-group align-middle" role="group">
              <a href="<?php _e ($this->list_url) ?>" class="btn btn btn-secondary"><i class="fa fa-list"></i></a>
              <button disabled class="btn btn btn-secondary"><i class="fa fa-calendar"></i></button>
            </div>
          </div>
        </div>
        <br>
        <div class="row">
          <table class="w-100">
            <thead>
              <tr>
                <th>Sun</th>
                <th>Mon</th>
                <th>Tue</th>
                <th>Wed</th>
                <th>Thu</th>
                <th>Fri</th>
                <th>Sat</th>
              </tr>
            </thead>
            <tbody>
              <tr class="eventful-row">
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
              </tr>
              <tr class="eventful-row">
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
              </tr>
              <tr class="eventful-row">
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
              </tr>
              <tr class="eventful-row">
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
              </tr>
              <tr class="eventful-row">
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
              </tr>
              <tr class="eventful-row">
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
                <td class="eventful-cell">
                  <?php $this->get_cell_contents (); ?>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <?php
    }
    function list () {
      ?>
      <br>
      <div class="container eventful-zilla">
        <div class="row hidden-sm-down">
          <div class="col-2 ma text-center">
            <div class="btn-group align-middle" role="group">
              <a href="<?php _e($this->last_url) ?>" class="btn btn-secondary"><i class="fa fa-chevron-left"></i></a>
              <a href="<?php _e($this->next_url) ?>" class="btn btn-secondary"><i class="fa fa-chevron-right"></i></a>
            </div>
          </div>
          <div class="col-md-4 hidden-sm-down text-center ma">
            <h3 class="ma"><?php _e ($this->month_title) ?></h3>
          </div>
          <div class="col-4 hidden-sm-down ma">
            <form>
              <?php $this->query () ?>
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-search"></i>
                </span>
                <input type="text" name="q">
              </div>
            </form>
          </div>
          <div class="col-2 text-center ma">
            <div class="btn-group align-middle" role="group">
              <button disabled class="btn btn btn-secondary"><i class="fa fa-list"></i></button>
              <a href="<?php _e ($this->calendar_url) ?>" class="btn btn btn-secondary"><i class="fa fa-calendar"></i></a>
            </div>
          </div>
        </div>

        <div class="row hidden-md-up">
          <div class="col-4 ma text-center">
            <div class="btn-group align-middle" role="group">
              <a href="<?php _e($this->last_url) ?>" class="btn btn-secondary"><i class="fa fa-chevron-left"></i></a>
              <a href="<?php _e($this->next_url) ?>" class="btn btn-secondary"><i class="fa fa-chevron-right"></i></a>
            </div>
          </div>
          <div class="col-4 hidden-md-up text-center ma">
            <h3 class="ma"><?php _e ($this->short_month_title) ?></h3>
          </div>
          <div class="col-4 text-center ma">
            <div class="btn-group align-middle" role="group">
              <button disabled class="btn btn btn-secondary"><i class="fa fa-list"></i></button>
              <a href="<?php _e ($this->calendar_url) ?>" class="btn btn btn-secondary"><i class="fa fa-calendar"></i></a>
            </div>
          </div>
        </div>
        <br>
        <div class="row">
          <div class="col-8 offset-2">
            <ul class="list-group">
              <?php $this->get_list_contents () ?>
            </ul>
          </div>
        </div>
      </div>

      <?php
    }

    function search () {
      ?>
      <br>
      <div class="container eventful-zilla">
        <div class="row hidden-sm-down">
          <div class="col-2 ma text-center">
            <div class="btn-group align-middle" role="group">
              <a href="<?php _e($this->last_url) ?>" class="btn btn-secondary"><i class="fa fa-chevron-left"></i></a>
              <a href="<?php _e($this->next_url) ?>" class="btn btn-secondary"><i class="fa fa-chevron-right"></i></a>
            </div>
          </div>
          <div class="col-md-4 hidden-sm-down text-center ma">
            <h3 class="ma">Search: <?php _e ($this->query_html) ?></h3>
          </div>
          <div class="col-4 hidden-sm-down ma">
            <form>
              <?php $this->query () ?>
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-search"></i>
                </span>
                <input type="text" name="q">
              </div>
            </form>
          </div>
          <div class="col-2 text-center ma">
            <div class="btn-group align-middle" role="group">
              <a href="<?php _e ($this->list_url) ?>" class="btn btn btn-secondary"><i class="fa fa-list"></i></a>
              <a href="<?php _e ($this->calendar_url) ?>" class="btn btn btn-secondary"><i class="fa fa-calendar"></i></a>
            </div>
          </div>
        </div>

        <div class="row hidden-md-up">
          <div class="col-4 ma text-center">
            <div class="btn-group align-middle" role="group">
              <a href="<?php _e($this->last_url) ?>" class="btn btn-secondary"><i class="fa fa-chevron-left"></i></a>
              <a href="<?php _e($this->next_url) ?>" class="btn btn-secondary"><i class="fa fa-chevron-right"></i></a>
            </div>
          </div>
          <div class="col-4 hidden-md-up text-center ma">
            <h3 class="ma"><?php _e ($this->query_html) ?></h3>
          </div>
          <div class="col-4 text-center ma">
            <div class="btn-group align-middle" role="group">
              <a href="<?php _e ($this->list_url) ?>" class="btn btn btn-secondary"><i class="fa fa-list"></i></a>
              <a href="<?php _e ($this->calendar_url) ?>" class="btn btn btn-secondary"><i class="fa fa-calendar"></i></a>
            </div>
          </div>
        </div>
        <br>
        <div class="row">
          <div class="col-8 offset-2">
            <ul class="list-group">
            <?php $this->get_search_contents () ?>
            </ul>
          </div>
        </div>
      </div>

      <?php
    }
  }
  $dm = new DateManager ();