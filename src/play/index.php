<?php   // BY: JOSHUA ZAMORA AND AIRAM MARTINEZ
require_once "Board.php";
require_once "Random.php";
require_once "Smart.php";
error_reporting(E_ALL & ~E_NOTICE);

define('PID', "pid");
define('MOVE', "move");
define('WRITE', dirname(dirname(__FILE__))."/writable/");

main();

function main() {
    $files = scandir(WRITE, 1);
    $acknowledgeMessage = array();
    $acknowledgeMessage['response'] = true;

    if (!isset($_GET[PID])) {       // Checks if pid is defined
        $acknowledgeMessage['response'] = false;
        $acknowledgeMessage['reason'] = "Pid not specified";
    }
    elseif (!isset($_GET[MOVE])) {      // Checks if move is defined
        $acknowledgeMessage['response'] = false;
        $acknowledgeMessage['reason'] = "Move not specified";
    }
    elseif (!in_array($_GET[PID], $files)) {    // Checks if pid is located in writable directory
        $acknowledgeMessage['response'] = false;
        $acknowledgeMessage['reason'] = "Unknown pid";
    }
    elseif ($_GET[MOVE] < 0 || $_GET[MOVE] > 6) {   // Checks for valid move
        $acknowledgeMessage['response'] = false;
        $acknowledgeMessage['reason'] = "Invalid slot, " . $_GET[MOVE];
    }

    if ($acknowledgeMessage['response'] == false) {
        echo json_encode($acknowledgeMessage);
        exit;
    }

    $fileContents = file_get_contents(WRITE . $_GET[PID]);  // Retrieves pid file contents

    $board = new Board();
    $board->board = json_decode($fileContents); // decodes 2d array to board object

    if ($board->board[0][$_GET[MOVE]] != 0) {
        $acknowledgeMessage['response'] = false;
        $acknowledgeMessage['reason'] = "Column is full";
        echo json_encode($acknowledgeMessage);
        exit;
    }

    $acknowledgeMessage['ack_move'] = array();
    $acknowledgeMessage['ack_move']['slot'] = 'None';
    $acknowledgeMessage['ack_move']['isWin'] = false;
    $acknowledgeMessage['ack_move']['isDraw'] = false;
    $acknowledgeMessage['ack_move']['row'] = !$board->checkForWinningRow(1) ? [] : $board->checkForWinningRow(1);

    $acknowledgeMessage['move'] = array();
    $acknowledgeMessage['move']['slot'] = 'None';
    $acknowledgeMessage['move']['isWin'] = false;
    $acknowledgeMessage['move']['isDraw'] = false;
    $acknowledgeMessage['move']['row'] = !$board->checkForWinningRow(2) ? [] : $board->checkForWinningRow(2);

    if ($acknowledgeMessage['ack_move']['row'] != false) {
        $acknowledgeMessage['ack_move']['isWin'] = true;
        unlink(WRITE . $_GET[PID]);
    }
    elseif ($board->boardIsFull()) {
        $acknowledgeMessage['ack_move']['isDraw'] = true;
        $acknowledgeMessage['move']['isDraw'] = true;
        unlink(WRITE . $_GET[PID]);
    }
    elseif ($acknowledgeMessage['move']['row'] != false) {
        $acknowledgeMessage['move']['isWin'] = true;
        unlink(WRITE . $_GET[PID]);
    }
    else {
        $board->insertDisc($_GET[MOVE], 1);

        $acknowledgeMessage['ack_move']['row'] = !$board->checkForWinningRow(1) ? [] : $board->checkForWinningRow(1);

        if ($acknowledgeMessage['ack_move']['row'] != false) {
            $acknowledgeMessage['ack_move']['isWin'] = true;
            unlink(WRITE . $_GET[PID]);
        }
        else {
            $random = new Random();
            $smart = new Smart();

            if ($_GET[PID][0] == "R")
                $computedMove = $random->GetComputedCoordinates($board);
            else
                $computedMove = $smart->GetComputedCoordinates($board);

            $board->insertDisc($computedMove, 2);

            $acknowledgeMessage['ack_move']['slot'] = $_GET[MOVE];
            $acknowledgeMessage['move']['slot'] = $computedMove;
            $acknowledgeMessage['move']['row'] = !$board->checkForWinningRow(2) ? [] : $board->checkForWinningRow(2);

            if ($acknowledgeMessage['move']['row'] != false) {
                $acknowledgeMessage['move']['isWin'] = true;
                unlink(WRITE . $_GET[PID]);
            }
            else {
                file_put_contents(WRITE . $_GET[PID], json_encode($board->board));  // Writes array back into file
            }
        }
    }

    echo nl2br("\n" . json_encode($acknowledgeMessage) . "\n\n");   // prints message to user
}
