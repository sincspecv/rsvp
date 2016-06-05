<?php
ini_set('display_errors', 1);

function solvePuzzle($num) {

    $num = (string)$num;
    //Set Array for number comparison
    $numberArray = array("0" => array(1, 2, 3, 5, 7),
                         "1" => array(4, 6, 9, 0),
                         "2" => array(8));
    //get lenth of $num and separate each number
    $numLength = strlen($num);
    for ($l = 0; $l < $numLength; $l++) {
      $numSplit[$l] = $num[$l];
    }

    //Compare to $numberArray to get number of holes
    $l = 0;
    $numberOfHoles = 0;
    while ($l < $numLength) {
      if (in_array($numSplit[$l], $numberArray[0])) {
        $numberOfHoles == $numberOfHoles;
      }
      if (in_array($numSplit[$l], $numberArray[1])) {
        $numberOfHoles == $numberOfHoles++;
      }
      if (in_array($numSplit[$l], $numberArray[2])) {
        $numberOfHoles == $numberOfHoles += 2;
      }
      $l++;
    }

    return $numberOfHoles;
}

$x = solvePuzzle(63011118);
echo $x;

?>
