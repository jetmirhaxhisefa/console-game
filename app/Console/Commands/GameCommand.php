<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

class GameCommand extends Command {
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'play:game';
    /**
     * Paylines
     *
     * @var array
     */
    protected $paylines = [
        [0, 3, 6, 9, 12],
        [1, 4, 7, 10, 13],
        [2, 5, 8, 11, 14],
        [0, 4, 8, 10, 12],
        [2, 4, 6, 10, 14]
    ];

    private $bet = 100;

    private $symbols = ['9', '10', 'J', 'Q', 'K', 'A', 'cat', 'dog', 'monkey', 'bird'];

    private $playBoard = [];

    /**
     * Gets called when command : "php artisan play:game" gets executed
     */
    public function handle(){
        $this->playBoard = $this->generatePlayBoard();

        // TODO print play board to verify the results
//        print_r($this->playBoard);

        $gameResult = $this->playGame();
        $this->printResults($gameResult);
    }

    /**
     * Play game after board is generated
     *
     * @return array
     */
    private function playGame(){
        $gameResult = [];

        foreach($this->paylines as $pKey => $payline){

            $matched = 1;
            foreach ($payline as $key => $item) {
                if(count($this->paylines) <= ($key+1)) {
                    break;
                }

                if($this->playBoard[$item] === $this->playBoard[$payline[$key+1]]){
                    $matched++;
                }else{
                    $matched = 1;
                }

                if($matched > 2){
                    $gameResult[$pKey] = ["payline" => $payline, "matches" => $matched];
                }
            }
        }

        return $gameResult;
    }

    /**
     * Print results (for demo purposes)
     * --- Added some extra code just to make it more readable in commanline
     *
     * @param $gameResult
     */
    private function printResults($gameResult){
        $matched_paylines = "";
        $win_amout = 0;
        foreach($gameResult as $key => $p){
            $matched_paylines .= "{".implode(" ", $p["payline"]) . " : " . $p["matches"]."}";

            if(count($gameResult)-1 > $key){
                $matched_paylines .= ", ";
            }

            switch ($p["matches"]){
                case 3:
                    $win_amout += ($this->bet * 20) / 100;
                    break;
                case 4:
                    $win_amout += ($this->bet * 200) / 100;
                    break;
                case 5:
                    $win_amout += ($this->bet * 1000) / 100;
                    break;
                default:
                    break;
            }

        }

        $board = $this->generateFinalBoard();
        // To get data in form of json object
        echo json_encode([
            "board" => $board,
            "paylines" => $matched_paylines,
            "bet_amount" => $this->bet,
            "total_win" => $win_amout,
        ]);
        echo "\n\n-------------------------";

        echo "\n\n\n";
        echo "Board: [" . implode(", ", $board). "]\n";
        echo "Paylines: [" . $matched_paylines. "]\n";
        echo "Win: " . $win_amout. "\n";
    }

    /**
     * Generate random board
     *
     * @return array
     */
    public function generatePlayBoard(){
        $board = [];
        for($i=0; $i < 15 ;$i++){
            $rand = rand(0,9);
            $board[] = $this->symbols[$rand];
        }
        return $board;
    }

    /**
     * Generates the board to be printed in the end
     * This is the table generated from the play board after the game ends
     *
     * @return array
     */
    private function generateFinalBoard(){
        $bordStructure = [0, 3, 6, 9, 12, 1, 4, 7, 10, 13, 2, 5, 8, 11, 14];
        $board = [];

        foreach($bordStructure as $val){
            $board[] = $this->playBoard[$val];
        }
        return $board;
    }



}