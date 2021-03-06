<?php   // BY: JOSHUA ZAMORA AND AIRAM MARTINEZ

class Board
{
    public $board;

    function __construct() {
        $this->board = array(           // Constructs a 6 x 7 2d array
            array(0, 0, 0, 0, 0, 0, 0),
            array(0, 0, 0, 0, 0, 0, 0),
            array(0, 0, 0, 0, 0, 0, 0),
            array(0, 0, 0, 0, 0, 0, 0),
            array(0, 0, 0, 0, 0, 0, 0),
            array(0, 0, 0, 0, 0, 0, 0)
        );
    }

    function insertDisc($xCoordinate, $player) {
        for ($yCoordinate = 5; $yCoordinate > -1; $yCoordinate--) { // Inserts disc at bottom index of column
            if ($this->board[$yCoordinate][$xCoordinate] == 0) {
                $this->board[$yCoordinate][$xCoordinate] = $player;
                return;
            }
        }
    }

    function boardIsFull() {
        for ($xCoordinate = 0; $xCoordinate < 7; $xCoordinate++)
            if ($this->board[0][$xCoordinate] == 0) return false;   // If the top of the board is occupied

        return true;
    }

    function winningRow($player) {
        for ($i = 0; $i < 6; $i++) {     // Checks Horizontal groups
            for ($j = 0; $j < 4; $j++) {
                if ($this->board[$i][$j    ] == $player &&
                    $this->board[$i][$j + 1] == $player &&
                    $this->board[$i][$j + 2] == $player &&
                    $this->board[$i][$j + 3] == $player)
                    return array($i, $j, $i, ($j+1), $i, ($j+2), $i, ($j+3));
            }
        }

        for ($i = 0; $i < 3; $i++) {     // Checks vertical groups
            for ($j = 0; $j < 7; $j++) {
                if ($this->board[$i    ][$j] == $player &&
                    $this->board[$i + 1][$j] == $player &&
                    $this->board[$i + 2][$j] == $player &&
                    $this->board[$i + 3][$j] == $player)
                    return array($i, $j, $i, ($j+1), $i, ($j+2), $i, ($j+3));
            }
        }

        for ($i = 3; $i < 6; $i++) {     // Checks Ascending diagonal groups
            for ($j = 0; $j < 4; $j++) {
                if ($this->board[$i    ][$j    ] == $player &&
                    $this->board[$i - 1][$j + 1] == $player &&
                    $this->board[$i - 2][$j + 2] == $player &&
                    $this->board[$i - 3][$j + 3] == $player)
                    return array($i, $j, $i, ($j+1), $i, ($j+2), $i, ($j+3));
            }
        }

        for ($i = 3; $i < 6; $i++) {     // Checks Descending Diagonal groups
            for ($j = 3; $j < 7; $j++) {
                if ($this->board[$i    ][$j    ] == $player &&
                    $this->board[$i - 1][$j - 1] == $player &&
                    $this->board[$i - 2][$j - 2] == $player &&
                    $this->board[$i - 3][$j - 3] == $player)
                    return array($i, $j, $i, ($j-1), $i, ($j-2), $i, ($j-3));
            }
        }

        return array();
    }
}
