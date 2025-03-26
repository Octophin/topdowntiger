<?php

header('Content-type: application/json');

  try {
    // connect to MongoHQ assuming your MONGOHQ_URL environment
    // variable contains the connection string
    $connection_url =  "mongodb://tdt:allthescores@oceanic.mongohq.com:10096/topdowntiger_scoreboard";
 
     // create the mongo connection object
    $m = new MongoClient($connection_url);
 
    // extract the DB name from the connection path
    $url = parse_url($connection_url);
    $db_name = preg_replace('/\/(.*)/', '$1', $url['path']);
 
    // use the database we connected to
    $db = $m->selectDB($db_name);
 
    // print out last collection
      $collection = $db->selectCollection("scores");
 
//Check if a valid post request has been made

if ($_REQUEST['name']){

 	// Get the scores
	
$score = intval($_REQUEST['score']);
	
	$score = array(
   "name" => $_REQUEST['name'],
   "level" => $_REQUEST['level'],
   "control" => $_REQUEST['control'],
   "score" => $score
   );

$collection->insert( $score );

}

if ($_REQUEST['scoreboard']){
	
$gamemode = $_REQUEST['scoreboard'];

      // only print out the top scores
      $cursor = $collection->find(array('level' => $gamemode));
	  $cursor->sort(array('score' => -1));
      $cursor->limit(100);
	  $scoreboard = array();
      foreach( $cursor as $index=>$doc ) {
      array_push($scoreboard, $doc);
      }
echo json_encode($scoreboard);

}
    // disconnect from server
    $m->close();
  } catch ( MongoConnectionException $e ) {
    die('Error connecting to MongoDB server');
  } catch ( MongoException $e ) {
    die('Mongo Error: ' . $e->getMessage());
  } catch ( Exception $e ) {
    die('Error: ' . $e->getMessage());
  }
?>