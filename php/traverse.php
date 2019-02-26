<?php

require 'sequence.php';

$addresses = [1, 2, 3, 4, 5, 6];

// Turn on one by one. Delay in between next
//Sequence::traverseOn($addresses, 2000);

// Turn on and off one by one. Delay in between turning off.
//Sequence::traverseToggle($addresses);

// Turn all on at once. Delay before turning all off at once
/*
$options = [
    'leaveOn' => true,
    'onDelay' => 1000,
    'offDelay' => 2000
];
Sequence::allOnOff($addresses, 5, $options);
*/

// Turn on/off 2, go back 1, go forward 2, go back 1 etc etc
Sequence::traverseSkipBack($addresses);
