<?php
    class TreasureHunt {
        private $gridColumn = 8; 
        private $gridRow = 6; 
        private $coord = [];
        private $clearPathCoord = [];
        private $currUserPosition = null;
        
        function __construct()
        {
            $this->_setGridValues();
            $this->_generateGrid();
        }

        public function startHunt()
        {
            /**
             * BEGIN navigate by user from starting position by order
             * Up/North = A step(s)
             * Right/East = B step(s)
             * Down/South = C step(s)
             * note: 
             * - if there is an obstacle, then position coord same as the last navigate position  
             * - if navigatte user input is not number, it will turn the values to be 0
             */ 
            echo PHP_EOL."===============".PHP_EOL;
            
            echo "Navigate position (step): ".PHP_EOL;

            // navigate how many step for Up/North
            $up = $this->_readInput('Up/North');
            // navigate how many step for Right/East
            $right = $this->_readInput('Right/East');
            // navigate how many step for Down/South
            $down = $this->_readInput('Down/South');

            /**
             * END navigate by user from starting position by order
             */ 

            // // generate probable coord points where the treasure might be localted
            echo PHP_EOL."===============".PHP_EOL;
            echo "Probable Treasure Locations: ".PHP_EOL.$this->_setProbableTreasureCoord($up, $right, $down);
            
            // generate grid after navigate position by user
            echo PHP_EOL."===============".PHP_EOL;
            $this->_generateGrid();
        }


        /**
         * set grid coord values 
         */
        private function _setGridValues()
        {
            for($i = 0; $i < $this->gridRow; $i++) {
                for($j = 0; $j < $this->gridColumn; $j++) {
                    // BEGIN set obstacle for grid
                    if(
                        ($i == 0 || $i == ($this->gridRow - 1)) 
                        && ($j >= 0 && $j < $this->gridColumn)
                    ) {
                        $this->coord[$i][$j] = "#";
                    } else if(
                        ($j == 0 || $j == ($this->gridColumn - 1)) 
                        && ($i >= 0 && $i < $this->gridRow)
                    ) {
                        $this->coord[$i][$j] = "#";
                    } else if($i == 2 && ($j >= 2 && $j <= 4)) {
                        $this->coord[$i][$j] = "#";
                    } else if($i == 3 && ($j == 4 || $j == 6)) {
                        $this->coord[$i][$j] = "#";
                    } else if($i == 4 && $j == 2) {
                        $this->coord[$i][$j] = "#";
                    }
                    // END set obstacle for grid 

                    // BEGIN set starting position
                    else if($i == 4 && $j == 1) {
                        $this->coord[$i][$j] = "\e[0;31mX\e[0m";

                        $userPosition = new stdClass();
                        $userPosition->row = $i;
                        $userPosition->column = $j;
                        $this->currUserPosition = $userPosition;
                    }
                    // END set starting position
                    else {
                        $this->coord[$i][$j] = ".";
                        
                        // add coord to clearPathCoord
                        $clearPath = new stdClass();
                        $clearPath->row = $i;
                        $clearPath->column = $j;
                        $this->clearPathCoord["{$clearPath->row}::{$clearPath->column}"] = $clearPath;
                    }
                }
            }
        }

        /**
         * change grid value coord according user navigate values
         * @param int $val the value from user navigate
         * @param string $navigate the direction from user navigate 
         */
        private function _setCoordValuesByUserNavigate($val = 0, $navigate = "up") {
            while($val != 0) {
                switch($navigate) {
                    case "right":
                        $currColumn = ($this->currUserPosition->column + 1);
                        if($currColumn >= 0) {
                            if($this->coord[$this->currUserPosition->row][$currColumn] == ".") {
                                $this->coord[$this->currUserPosition->row][$currColumn] = "\e[0;31mX\e[0m";
                                $this->currUserPosition->column = $currColumn;

                                // delete coord from clearPathCoord
                                if(array_key_exists($this->currUserPosition->row."::".$currColumn, $this->clearPathCoord)) {
                                    unset($this->clearPathCoord[$this->currUserPosition->row."::".$currColumn]);
                                }
                            }
                        }
                        break;
                    case "up":
                    case "down":
                        $currRow = ($this->currUserPosition->row - 1);
                        if($navigate == "down") $currRow = ($this->currUserPosition->row + 1);
                        
                        if($currRow >= 0) {
                            if($this->coord[$currRow][$this->currUserPosition->column] == ".") {
                                $this->coord[$currRow][$this->currUserPosition->column] = "\e[0;31mX\e[0m";
                                $this->currUserPosition->row = $currRow;

                                // delete coord from clearPathCoord
                                if(array_key_exists($currRow."::".$this->currUserPosition->column, $this->clearPathCoord)) {
                                    unset($this->clearPathCoord[$currRow."::".$this->currUserPosition->column]);
                                }
                            }
                        }
                        break;
                }
                $val--;
            }
        }
        
        /**
         * generate probable treasure coord point values from user navigate values
         * @param int $up
         * @param int $right
         * @param int $down
         */
        private function _setProbableTreasureCoord($up = 0, $right = 0, $down = 0) {
            if($up) $this->_setCoordValuesByUserNavigate($up, "up");
            if($right) $this->_setCoordValuesByUserNavigate($right, "right");
            if($down) $this->_setCoordValuesByUserNavigate($down, "down");

            
            // get list of probable treasure coord from clearPathCoord after user navigate process 
            $listTreasureCoord = [];
            foreach($this->clearPathCoord as $key => $coord) {
                $listTreasureCoord[] = "({$coord->row}, {$coord->column})";
                $this->coord[$coord->row][$coord->column] = "\e[0;32m$\e[0m"; // set symbol "$" for treasure coord from clearPathCoord
            }
            return implode(PHP_EOL, $listTreasureCoord);
        }

        /**
         * generate grid for grid coord values
         */
        private function _generateGrid()
        {
            for($i = 0; $i < $this->gridRow; $i++) {
                for($j = 0; $j < $this->gridColumn; $j++) {
                    echo $this->coord[$i][$j];
                    if($j == ($this->gridColumn - 1)) {
                        echo PHP_EOL."";
                    }
                }
            }
        }

        private function _readInput($label) 
        {
            echo $label.": ";
            while(true)
            {
                $input = trim(fgets(STDIN));
                if(preg_match('{^[0-9]{1}$}',$input) == false)
                {
                    echo "\e[0;31mWrong Input ".$label."! Must number 0-9 and 1 digit\e[0m".PHP_EOL;
                    echo $label.': ';
                    continue;
                }
                break;
            }
            return $input;
        }
    }
?>