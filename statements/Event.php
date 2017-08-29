<?php

/**
* Eventful Event type abstracting over the database.
*/
class Event {

  function __construct () {
    global $wpdb;
    $this->table = $wpdb->prefix . 'eventfulevent';
  }

  public function read ($id) {
    global $wpdb;
    $sql = sprintf('SELECT * FROM %s WHERE id = %s', $this->table, $id);
    return $wpdb->get_row ($sql);
  }

  public function readAll () {
    global $wpdb;
    $sql = sprintf('SELECT * FROM %s', $this->table);
    return $wpdb->get_results ($sql);
  }

  public function delete ($id) {
    global $wpdb;
    $wpdb->delete ($this->table, array ('id' => $id));
  }

  public function update ($id, $data) {
    global $wpdb;
    $wpdb->update ($this->table, $data, array ('id' => $id));
  }

  public function byMonth ($month, $year) {
    global $wpdb;
    $sql = sprintf ('SELECT * FROM %s WHERE month = %s AND year = %s', $this->table, $month, $year);
    return $wpdb->get_results ($sql);
  }

  public function byDay ($month, $day, $year) {
    global $wpdb;
    $sql = sprintf ('SELECT * FROM %s WHERE month = %s AND day = %s AND year = %s', $this->table, $month, $day, $year);
    return $wpdb->get_results ($sql);
  }

  public function create ($data) {
    global $wpdb;
    $wpdb->insert ($this->table, $data);
  }


}