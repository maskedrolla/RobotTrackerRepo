<form action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> method="post">
	<h4>Bibblio Mars Robot Tracker</h4>
	<textarea name="comment" rows="15" cols="70"><?php echo $comment;?></textarea>
	
	<br />
	<input type="submit">
</form>

<?php

if (isset($_POST["comment"])) {
    
    $array    = preg_split("/\r\n|[\r\n]/", $_POST["comment"]);
    $gridSize = $array[0]; // Restablish the grid size in a variable
    
    //array_shift($carryover); // Remove the grid size from the array
    
    $arraySize  = count($array);
    $robotCount = 0;
    
    //$arraySize = floor($arraySize/3) * 3;
    
    // Create the boolean for losing a robot off grid
    if (!isset($lost)) {
        $lost = false;
    }
    if (!isset($lostX)) {
        $lostX = 0;
    }
    if (!isset($lostY)) {
        $lostY = 0;
    }
    
    // Create the boolean for checking if another robot is following the same path as a previously lost reobot
    if (!isset($stopMovements)) {
        $stopMovements = false;
    }
    
    // Grid information
    $gridSize = explode(" ", $gridSize);
    if (count($gridSize) != 2) {
        echo "You must provide information for one 'x' axis and one 'y' axis, no more or no less. Please try again. -- *Grid Size Error*<br />";
    } elseif ($gridSize[0] > 50 || $gridSize[1] > 50) {
        echo "A single coordinate cannot be greater than 50. Please try again. -- *Grid Size Error*<br />";
    } elseif ($gridSize[0] < 0 || $gridSize[1] < 0) {
        echo "A single coordinate cannot be less than 0. Please try again. -- *Grid Size Error*<br />";
    } else {
        echo "The grid you have identified will be <strong><u>" . $gridSize[0] . " wide x " . $gridSize[1] . " high</u></strong><br />";
    }
    
    
    if ($arraySize > 0) {
        echo "_______________________<br />";
        for ($position = 0; $position < $arraySize; $position += 3) {
            
            $robotCount++; // Capture which robot you are working on at the moment
            $providedPosition  = $position + 1; // Capture the position provided for each robot
            $instructions      = $position + 2; // Capture the movement instructions provided for each robot
            $processingResults = "<br /> Robot Movement Results: <br />"; // variable to house the string, for future displaying
            
            $robotPosition = explode(" ", $array[$providedPosition]); // Break apart the provided posistion details into x, y, and direction
            
            // Work on provided movement instructions
            $instructions       = strtolower($array[$instructions]);
            $instructionsArray  = str_split($instructions);
            $instructionsLength = count($instructionsArray);
            
            // Assign the robots starting location and direction
            $robotX              = $robotPosition[0];
            $robotY              = $robotPosition[1];
            $robotDirection      = strtolower($robotPosition[2]);
            $robotDirectionStart = $robotDirection;
            
            // Check the submitted robot position
            if (count($robotPosition) != 3) {
                echo "You have not provided the proper amount of information. Please try again. - *Robot Position*<br />";
            } elseif (($robotPosition[0] > $gridSize[0]) || ($robotPosition[1] > $gridSize[1])) {
                echo "The robot coordinates you provided for Robot #" . $robotCount . " do not fit on the specified grid size (<strong><u>" . $gridSize[0] . " wide x " . $gridSize[1] . " high</u></strong>). Please try again.  - *Robot Position*<br />";
            } elseif (($robotPosition[0] < 0) || ($robotPosition[1] < 0)) {
                echo "Robot #" . $robotCount . " cannot have a position with a negative coordinate. Please try again.  - *Robot Position*<br />";
            } else {
                echo "<br /><br /><h4>Robot #" . $robotCount . " </h4> This robot starts at <strong><u>x = " . $robotPosition[0] . ",  y = " . $robotPosition[1] . "</u></strong> facing to the <strong><u>" . strtoupper($robotPosition[2]) . "</u></strong>";
                echo "<br />Planned movements are <strong><u>" . strtoupper($instructions) . "</u></strong>";
            }
            
            
            
            
            //echo "<br />" . $robotX . " - " . $robotY . " - " . $robotDirection . "<br />";
            //echo "<br />" . $instructions . " - " . $instructionsLength . "<br />";
            
            for ($i = 0; $i < $instructionsLength; $i++) {
                if ($instructionsArray[$i] == "r" || $instructionsArray[$i] == "l") {
                    //directionChange($robotDirection, $instructionsArray[$i]);
                    if ($robotDirection == 'w') {
                        if ($instructionsArray[$i] == 'l') {
                            $robotDirection = 's';
                        } elseif ($instructionsArray[$i] == 'r') {
                            $robotDirection = 'n';
                        }
                    } elseif ($robotDirection == 'n') {
                        if ($instructionsArray[$i] == 'l') {
                            $robotDirection = 'w';
                        } elseif ($instructionsArray[$i] == 'r') {
                            $robotDirection = 'e';
                        }
                    } elseif ($robotDirection == 'e') {
                        if ($instructionsArray[$i] == 'l') {
                            $robotDirection = 'n';
                        } elseif ($instructionsArray[$i] == 'r') {
                            $robotDirection = 's';
                        }
                    } elseif ($robotDirection == 's') {
                        if ($instructionsArray[$i] == 'l') {
                            $robotDirection = 'e';
                        } elseif ($instructionsArray[$i] == 'r') {
                            $robotDirection = 'w';
                        }
                    }
                    
                    $processingResults .= "<br />Turned <strong><u>" . strtoupper($instructionsArray[$i]) . "</u></strong>, the new direction is <strong><u>" . strtoupper($robotDirection) . "</u></strong>";
                }
                
                if ($instructionsArray[$i] == 'f') {
                    if ($robotDirection == 'w') {
                        $robotX--;
                    } elseif ($robotDirection == 'n') {
                        $robotY++;
                    } elseif ($robotDirection == 'e') {
                        $robotX++;
                    } elseif ($robotDirection == 's') {
                        $robotY--;
                    }
                    
                    
                    $processingResults .= "<br />Moved <strong><u>forward</u></strong>, one spot to the <strong><u>" . strtoupper($robotDirection) . "</u></strong>";
                }
                
                if ($lost == true && $lostY == $robotY && $lostX == $robotX) {
                    $stopMovements = true;
                } elseif (($robotX > $gridSize[0]) || ($robotY > $gridSize[1]) || ($robotY < 0) || ($robotX < 0)) {
                    $lost   = true;
                    $lostX  = $robotX;
                    $lostY  = $robotY;
                    $robotX = 0;
                    $robotY = 0;
                }
                
            }
            
            if ($stopMovements) {
                echo "<br /><span style='color:red'>We already lost on robot at going to those coordinates, we aren't doing that again. Skipping the assigned movements, for the sake of the planet.</span>";
            } elseif ($lost) {
                echo "<br /><span style='color:red'>LOST ROBOT - " . $lostX . " X coordinate and " . $lostY . " Y coordinate</span>";
            } else {
                echo $processingResults;
                echo "<br /><span style='color:green'>This robot ends at <strong><u>x = " . $robotX . ",  y = " . $robotY . "</u></strong> facing to the <strong><u>" . strtoupper($robotDirection) . "</u></strong></span>";
            }
            
            
        }
        
    }
    
}
?>