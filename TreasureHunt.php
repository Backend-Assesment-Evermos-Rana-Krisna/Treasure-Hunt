<?php
    class TreasureHunt {
        private $coord = [
            ["#","#","#","#","#","#","#","#"],
            ["#",".",".",".",".",".",".","#"],
            ["#",".","#","#","#",".",".","#"],
            ["#",".",".",".",".","#",".","#"],
            ["#",".","#",".",".",".",".","#"],
            ["#","#","#","#","#","#","#","#"]
        ];
        private $clearPath = [];
        private $position, $treasure;
        private $found = false;
        
        function __construct(array $options = [])
        {
            $this->treasure = new stdClass();

            $this->position = new stdClass();
            $this->position->row = 4;
            $this->position->column = 1;

            $this->_generatePlayerPosition();
            $this->_generateRandomTreasure(count($options) > 1 && $options[1] == "treasure" ? true : false);
            $this->_generateGrid();
        }

        private function _generateRandomTreasure($show = false)
        {
            while(true)
            {
                $posY = rand(0,count($this->coord)-1);
                $posX = rand(0,count(max($this->coord))-1);
                if($this->coord[$posY][$posX] == "."){
                    $this->treasure->row = $posY;
                    $this->treasure->column = $posX;
                    if($show == true){
                        $this->coord[$posY][$posX] = "\e[1;32m$\e[0m";
                    }
                    break;
                }
            }
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

            // // // generate probable coord points where the treasure might be localted
            // echo PHP_EOL."===============".PHP_EOL;
            // echo "Probable Treasure Locations: ".PHP_EOL.$this->_setProbableTreasureCoord($up, $right, $down);
            
            // // generate grid after navigate position by user
            // echo PHP_EOL."===============".PHP_EOL;
            // $this->_generateGrid();
            echo PHP_EOL."============================".PHP_EOL;
            echo "Probable Treasure Locations: ".PHP_EOL.$this->_setProbableTreasureCoord($up, $right, $down).PHP_EOL;

            // generate grid after navigate position by user
            echo PHP_EOL."============================".PHP_EOL;
            if ($this->found == true) {
                echo "Wohooo!!! Congratulations, You found the treasure...".PHP_EOL;
            } else {
                echo "Sorry, You have not made it, please try again...".PHP_EOL;
            }
            echo $this->_generateGrid();
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
                        $currColumn = ($this->position->column + 1);
                        if($currColumn == $this->treasure->column && $this->position->row == $this->treasure->row){
                            $this->found = true;
                        }
                        if($currColumn >= 0) {
                            if($this->coord[$this->position->row][$currColumn] == ".") {
                                $this->coord[$this->position->row][$currColumn] = "\e[0;31mX\e[0m";
                                $this->position->column = $currColumn;

                                // delete coord from clearPath
                                if(array_key_exists($this->position->row."::".$currColumn, $this->clearPath)) {
                                    unset($this->clearPath[$this->position->row."::".$currColumn]);
                                }
                            }
                        }
                        break;
                    case "up":
                    case "down":
                        $currRow = ($this->position->row - 1);
                        if($navigate == "down") $currRow = ($this->position->row + 1);
                        if($this->position->column == $this->treasure->column && $currRow == $this->treasure->row){
                            $this->found = true;
                        }
                        
                        if($currRow >= 0) {
                            if($this->coord[$currRow][$this->position->column] == ".") {
                                $this->coord[$currRow][$this->position->column] = "\e[0;31mX\e[0m";
                                $this->position->row = $currRow;

                                // delete coord from clearPath
                                if(array_key_exists($currRow."::".$this->position->column, $this->clearPath)) {
                                    unset($this->clearPath[$currRow."::".$this->position->column]);
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

            // get list of probable treasure coord from clearPath after user navigate process 
            $listTreasureCoord = [];
            foreach($this->clearPath as $key => $coord) {
                $listTreasureCoord[] = "({$coord->row}, {$coord->column})";
                $this->coord[$coord->row][$coord->column] = "\e[1;37m$\e[0m"; // set symbol "$" for treasure coord from clearPath
            }
            $this->coord[$this->treasure->row][$this->treasure->column] = "\e[1;32m".($this->found == true ? 'X':'$')."\e[0m";
            return implode(PHP_EOL, $listTreasureCoord);
        }

        /**
         * generate grid for grid coord values
         */
        private function _generateGrid()
        {
            for ($x = 0; $x < count($this->coord); $x++) {
                for ($y = 0; $y < count(max($this->coord)); $y++) {
                    if($this->coord[$x][$y] == "." || $this->coord[$x][$y] == "$")
                    {
                        $clearPath = new stdClass();
                        $clearPath->row = $x;
                        $clearPath->column = $y;
                        $this->clearPath["{$clearPath->row}::{$clearPath->column}"] = $clearPath;
                    }
                    echo $this->coord[$x][$y];
                }
                echo PHP_EOL;
            }
        }

        private function _generatePlayerPosition()
        {
            if($this->coord[$this->position->row][$this->position->column] == "."){
                $this->coord[$this->position->row][$this->position->column] = "\e[0;31mX\e[0m";
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