<?php
?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
<br>
<div class="container">
  <h3>Create Event</h3>
  <hr>
  <form action="<?php _e(esc_url (admin_url ('admin-post.php'))) ?>">
    <input type="hidden" name="action" value="create_event">
    <div class="form-group row">
      <label for="title">Title</label>
      <input maxlength="32" class="form-control" type="text" name="title" required>
    </div>
    <div class="form-group row">
      <label for="description">Description</label>
      <input maxlength="140" class="form-control" type="text" name="description" required>
    </div>
    <div class="form-group row">
      <label for="m">Month</label>
      <select class="form-control" name="month">
        <option value="1">January</option>
        <option value="2">February</option>
        <option value="3">March</option>
        <option value="4">April</option>
        <option value="5">May</option>
        <option value="6">June</option>
        <option value="7">July</option>
        <option value="8">August</option>
        <option value="9">September</option>
        <option value="10">October</option>
        <option value="11">November</option>
        <option value="12">December</option>
      </select>
    </div>
    <div class="form-group row">
      <label for="d">Day</label>
      <input type="number" name="day" value="<?php _e (date ('j')) ?>" min="1" max="31" class="form-control">
    </div>
    <div class="form-group row">
      <label>Year</label>
      <input class="form-control" type="number" name="year" value="<?php _e (date ('Y')) ?>">
    </div>
    <div class="form-group row">
      <label for="hour">Hour</label>
      <input class="form-control" type="number" name="hour" min="1" max="12" value="<?php _e (date ('g')) ?>">
    </div>
    <div class="form-group row">
      <label class="form-check-label">
        <input class="form-check-input" type="radio" name="ampm" value="am" <?php date ('a') == 'am' ? _e ('checked') : _e ('') ?>>
        am
      </label>
      <label class="form-check-label">
        <input class="form-check-input" type="radio" name="ampm" value="pm" <?php date ('a') == 'pm' ? _e ('checked') : _e ('') ?>>
        pm
      </label>
    </div>
    <div class="form-group">
      <input class="btn btn-primary" type="submit" value="Create Event">
    </div>
  </form>
</div>