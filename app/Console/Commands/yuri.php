<?php

namespace App\Console\Commands;

use App\Indodax\Indodax;
use Carbon\Carbon;
use Illuminate\Console\Command;

class yuri extends Command
{
    protected $balance = [];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yuri:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start Yuri Trading Bot';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $indodax = new Indodax;

        //get account info
        $this->info("==============================");
        $this->info("YURI CRYPTO TRADING BOT V.1.0");
        $this->info("==============================");

        $this->line("Getting account info...");
        $info =  $indodax->getInfo()->return;
        $this->info("Server time\t: " . date('d-m-Y H:i:s', (int) $info->server_time));
        $this->info("Name\t\t: ". $info->name);
        $this->info("Email\t\t: ". $info->email);
        $this->table(['Currency', 'Balance', 'Balance Hold'], $this->balance($info->balance, $info->balance_hold));
        $pair = $this->ask('What you want to trade [pair]: ');
        $strategy = $this->choice('What is your startegy?',[
            'Follow The King',
            'EMA'
        ]);

        $seconds= 5;
        while(true){
            if($strategy == 'Follow The King'){
                $this->followTheKing($indodax, $pair);
            } else if($strategy == 'EMA'){
                $this->EMA($indodax, $pair);
            }
            sleep($seconds);
        }

        // return 0;
    }

    public function balance($balance, $balance_hold)
    {
        $temp = [];
        $balance_hold = (array) $balance_hold;
        foreach((array) $balance as $key => $value){
            $temp[] = [
                'currency' => $key,
                'balance' => $value,
                'balance_hold' => $balance_hold[$key]
            ];
        }
        $this->balance = $temp;
        return $temp;
    }

    public function followTheKing($indodax, $pair)
    {
        //get current market trade
        $trades = $indodax->trades(str_replace('_', '', $pair));
        $buyers = [];
        $sellers = [];
        foreach($trades as $trade){
            $data = (array) $trade;
            if($trade->type == 'buy'){
                $buyers[] = $data;
            } else {
                $sellers[] = $data;
            }
        }

        //search the king
        $buyersKing = $this->searchTheKing($buyers);
        $sellersKing = $this->searchTheKing($sellers);

        //check current position
        $currentPosition = 'buy';
        $last_price = 0;
        $pair1 = explode('_', $pair)[0];
        $pair2 = explode('_', $pair)[1];
        $balance_cur1 = $this->getBalance($pair1);
        $balance_cur2 = $this->getBalance($pair2);
        $spend = ($balance_cur2 * 0.1);

        $closePrice = $indodax->ticker(str_replace('_', '', $pair))->ticker->last;
        $orders = $indodax->openOrders($pair)->return->orders;

        if(!empty($orders)){
            $submit_time = Carbon::parse((int) $orders[0]->submit_time);
            if($submit_time->diffInHours(date('d-m-Y H:i:s')) >= 1){
                $indodax->cancleOrder($pair, $orders[0]->order_id, $orders[0]->type);
            }

            $this->line("No Action.");
            return;
            // return 1; //no action wait until current order done
        }

        $history =  $indodax->orderHistory($pair)->return->orders;
        if(!empty($history)){
            foreach($history as $his){
                if($his->status == 'filled'){
                    $currentPosition = $his->type == 'buy' ? 'sell' : 'buy';
                    $last_price = $his->price;

                    break;
                }
            }

            if($last_price == 0){
                $currentPosition = 'buy';
            }
        } else {
            $currentPosition = 'buy';
        }

        if($currentPosition == 'sell'){
            $price = $closePrice * ( 1 + 0.002 );

            if($price > ($last_price * (1 - 0.002))){
                $indodax->trade('sell', $pair1, $pair2, $price, $balance_cur1, $spend);
                $this->info("SELL: " . $pair. "\tPRICE: " . $price . "\tAMOUNT: " . $balance_cur1);
            }

            // return 1;
        } else {
            $price = $closePrice * ( 1 - 0.002 );
            $trade = $indodax->trade('buy', $pair1, $pair2, $price, $balance_cur1, $spend);
            $this->info("BUY: " . $pair. "\tPRICE: " . $price . "\tAMOUNT: " . $spend);
        }
        $this->info('Listenning..');
        return;
    }

    /**
     * search the king by higest amount
     *
     * @param mixed $trades
     *
     * @return array
     */
    public function searchTheKing($trades)
    {
        $temp = [
            'amount' => 0,
            'price' => 0
        ];

        foreach($trades as $trade){
            if($temp['amount'] < $trade['amount']){
                $temp['amount'] = $trade['amount'];
                $temp['price'] = $trade['price'];
            }
        }

        return $temp;
    }


    public function getBalance($currency)
    {
        foreach($this->balance as $balance){
            if($balance['currency'] == $currency){
                return $balance['balance'];
            }
        }
    }

    public function EMA($indodax, $pair)
    {
        dd($indodax->depth(str_replace('_', '', $pair)));
        $data = [];
        foreach($indodax->trades(str_replace('_', '', $pair)) as $trade){
            // dd($trade);
            $trade->date = date('d-m-Y H:i:s', (int) $trade->date);
            $data[] = (array) $trade;
        }

        $this->table(["date", "type", "price", "amount"], $data);

        dd('ada');
    }

}
