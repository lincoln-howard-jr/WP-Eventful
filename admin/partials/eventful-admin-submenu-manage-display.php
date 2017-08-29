<?php
  class DateManager {
    public function __construct () {
      // handle db operations
      $this->event = new Event ();
      // setup date constants
      $this->month = date ('m');
      if (isset ($_GET ['month'])) {
        $this->month = $_GET ['month'];
      }
      $this->year = date ('Y');
      if (isset ($_GET ['year'])) {
        $this->year = $_GET ['year'];
      }
      $this->next_query = array('page' => 'eventful_manage', 'month' => ($this->month + 1 == 12 ? 0 : $this->month + 1), 'year' => ($this->month + 1 == 12 ? $this->year + 1 : $this->year));
      $this->last_query = array('page' => 'eventful_manage', 'month' => ($this->month - 1 == -1 ? 11 : $this->month - 1), 'year' => ($this->month - 1 == -1 ? $this->year - 1 : $this->year));
      $this->all_query =  array ('page' => 'eventful_manage', 'all' => true);
      $this->next_url = esc_url (add_query_arg ($this->next_query, get_admin_url () . 'admin.php'));
      $this->last_url = esc_url (add_query_arg ($this->last_query, get_admin_url () . 'admin.php'));
      $this->all_url = esc_url (add_query_arg ($this->all_query, get_admin_url () . 'admin.php'));
      $this->timestamp = mktime (0, 0, 0, $this->month, 1, $this->year);
      $this->month_title = 'Manage Events in ' . date ('F, Y', $this->timestamp);
      if (isset ($_GET ['all'])) {
        $this->month_title = 'Manage All Events';
      }
      $this->day_of_week = date ('w', $this->timestamp);
      $this->position = 0;
      $this->current = 1;
      $this->max = date ('t', $this->timestamp);
    }
    public function render_events  () {
      $list = (isset ($_GET ['all']) ? $this->event->readAll () : $this->event->byMonth ($this->month, $this->year));
      foreach ($list as $key => $value) {
        _e ('<tr>');
        _e ('<td>' . $value->title . '</td>');
        _e ('<td>' . $value->description . '</td>');
        _e ('<td>' . $value->day . '</td>');
        _e ('<td>' . $value->hour . $value->ampm . '</td>');
        _e ('<td>' . '<a href="' . get_admin_url () . "admin.php?page=eventful_edit&id=" . $value->id . ' " class="btn btn-secondary"><i class="fa fa-pencil"></i></a>' . '</td>');
        _e ('<td><form action="' . esc_url( admin_url ('admin-post.php')) .'" method="post"><input type="hidden" name="id" value="' . $value->id . '"><input type="hidden" name="action" value="delete_event"><button class="btn btn-secondary" type="submit"><i class="fa fa-trash"/></button></form></td>');
        _e ('</tr>');
      }
    }
  }
  $dm = new DateManager ();
?>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
<link href="https://fonts.googleapis.com/css?family=Zilla+Slab" rel="stylesheet">
<style>
  .zilla {
    font-family: 'Zilla Slab', serif;
  }
  .eventful-zilla {
    font-family: 'Zilla Slab', serif;
    padding: 10px 20px;
    border: 1px solid #aaa;
  }
  .eventful-zilla:hover {
    box-shadow: 1px 1px 5px #999;
  }
  .eventful-cell {
    height: 64px;
    border: 1px #fff solid;
    background-color: #f8f8f8;
    width: 14.2%;
    vertical-align: top;
    margin: 0;
  }
  .eventful-cell-number {
    float: left;
    margin: 0;
  }
</style>
<br>
<div class="container zilla">
  <div class="row">
    <div class="col-4">
      <div class="btn-group" role="group">
        <a href="<?php _e($dm->last_url) ?>" class="btn btn-secondary"><i class="fa fa-chevron-left"></i></a>
        <a href="<?php _e($dm->next_url) ?>" class="btn btn-secondary"><i class="fa fa-chevron-right"></i></a>
        <a href="<?php _e($dm->all_url) ?>" class="btn btn-secondary">All</a>
      </div>
    </div>
    <div class="col-8">
      <h3><?php _e ($dm->month_title) ?></h3>
    </div>
  </div>
  <br>
  <table class="table">
    <thead>
      <tr>
        <th>Title</th>
        <th>Description</th>
        <th>Day</th>
        <th>Time</th>
        <th>Edit</th>
        <th>Delete</th>
      </tr>
    </thead>
    <tbody>
      <?php $dm->render_events (); ?>
    </tbody>
  </table>
</div>