<?php

include('Writer.php');

$writer = new \EnvWriter\Writer;

$writer->disable('TEST_1')
->save();

var_dump($writer->enabled('TEST_1'));

// $writer->set('TEST_1', 'DAN2')
//     ->save();


// var_dump($writer->get('TEST_1'));

// $writer->disable('TEST_1')
//     ->save();

// var_dump($writer->get('TEST_1'));

// var_dump($writer->get('TEST_1'));

// $writer->enable('TEST_1')
//     ->save();



//var_dump($writer->get('TEST_1'));

// $writer->unset('TEST_1')
//     ->save();

//var_dump($writer->get('TEST_1'));    


var_dump('======');