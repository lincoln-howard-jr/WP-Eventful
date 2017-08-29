<?php
  function render () {
    $event = new Event ();
    $data = isset ($_GET ['id']) ? array ($event->read ($_GET ['id'])) : $event->readAll ();
    foreach ($data as $key => $value) {
      _e ('
        <div class="row">
          <form action="' . esc_url( admin_url ('admin-post.php')) . '" method="post">
            <input type="hidden" name="action" value="update_event">
            <input type="hidden" name="id" value="' . $value->id . '">
            <td>
              <input class="form-control" maxlength="32" type="text" name="title" value="' . $value->title . '">
            </td>
            <td>
              <input class="form-control" maxlength="140" type="text" name="description" value="' . $value->description . '">
            </td>
            <td>
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
            </td>
            <td class="align-middle">
              <input class="form-control" type="number" name="day" value="' . $value->day . '">
            </td>
            <td class="align-middle">
              <input class="form-control" type="number" name="year" value="' . $value->year . '">
            </td>
            <td class="align-middle">
              <input class="form-control" type="number" name="hour" value="' . $value->hour . '">
            </td>
            <td>
              <select class="form-control" name="ampm">
                <option value="am"' . ($value->ampm == 'am' ? 'checked' : '') . '>AM</option>
                <option value="pm" ' . ($value->ampm == 'pm' ? 'checked' : '') . '>PM</option>
              </select>
            </td>
            <td class="align-middle">
              <button type="submit" class="btn btn-secondary">Save</button>
            </td>
          </form>
        </div>
      ');
    }
  }
?>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
<link href="https://fonts.googleapis.com/css?family=Zilla+Slab" rel="stylesheet">
<style>
  .zilla {
    font-family: 'Zilla Slab', serif;
  }
  .editable-grid {
    display: table;
  }
  .editable-grid-row {
    display: table-row;
  }
  .editable-grid-head {
    display: table-cell;
  }
</style>
<br>
<div class="container-full zilla">
  <div class="row">
    <div class="col-4">
    </div>
    <div class="col-8">
      <h3>Month Title</h3>
    </div>
  </div>
  <br>
  <div class="row editable-grid">
    <div class="col-12 editable-grid-row">
      <div class="editable-grid-head">Title</div>
      <div class="editable-grid-head">Description</div>
      <div class="editable-grid-head">Month</div>
      <div class="editable-grid-head">Day</div>
      <div class="editable-grid-head">Year</div>
      <div class="editable-grid-head">Time</div>
      <div class="editable-grid-head"></div>
      <div class="editable-grid-head">Save</div>
    </div>
  </div>
</div>