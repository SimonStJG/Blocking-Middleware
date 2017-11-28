<?php

/**
 * Refresh randomlinks and randomcat lists.
 *
 * For both lists, if there are fewer than argv[2] elements in the list, then add an additional
 * arg[1] elements.
 */

$dir = dirname(__FILE__);
include "$dir/../api/1.2/libs/DB.php";
include "$dir/../api/1.2/libs/services.php";
include "$dir/../api/1.2/libs/amqp.php";

# TODO What are the correct defaults here?
$DEFAULT_LIST_SIZE_THRESHOLD = 100;
$DEFAULT_NUM_ADDITIONAL_ITEMS = 200;

$conn = db_connect();
$redis = redis_connect("cache");

$key = "randomlinks";
$loader = new UrlLoader($conn);

if ($argc < 3) {
    print "Insuffient command line arguments provided, using defaults\n";
    $list_size_threshold = $DEFAULT_LIST_SIZE_THRESHOLD;
    $num_additional_items = $DEFAULT_NUM_ADDITIONAL_ITEMS;
} else {
    $list_size_threshold = $argv[2];
    $num_additional_items = $argv[1];
}
print "Running with: \n";
print "  list_size_threshold: $list_size_threshold\n";
print "  num_additional_items: $num_additional_items\n";

try {
  print $redis->lLen($key);
  if ($redis->lLen($key) < $list_size_threshold) {
      $links = $loader->get_unreported_blocks($num_additional_items);
      print sprintf("Found %d unreported blocks\n", length($links));
      foreach ($links as $url) {
          $redis->rPush($key, $url);
      }
  }

  $key = "randomcat";
  $loader = new DMOZCategoryLoader($conn);

  if ($redis->lLen($key) < $list_size_threshold) {
      $cats = $loader->random($num_additional_items);
      print sprintf("Found %d random categories\n", length($cats));
      foreach ($cats as $cat) {
          $redis->rPush($key, $cat);
      }
  }
} catch (Exception $e) {
  print "refresh_random_links failed: $e\n";
  print $e.message;
  exit(1);
}
