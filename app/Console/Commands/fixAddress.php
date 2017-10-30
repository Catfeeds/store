<?php

namespace App\Console\Commands;

use App\Models\City;
use Illuminate\Console\Command;

class fixAddress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'FixAddress';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return mixed
     */
    public function handle()
    {
        //
        $this->setCity();
    }
    public function setCity()
    {
        $cities = City::where('pid','!=',0)->get();
        for ($i=0;$i<count($cities);$i++){
            $url = 'http://api.map.baidu.com/geocoder/v2/?address='.$cities[$i]->name.'&output=json&ak=ghjW6DPclbHFsGSxdkwp3GWczKSmjT3f';
            $data = $this->getCityInfo($url);
            if ($data['status']==0){
                $result = $data['result']['location'];
                $cities[$i]->latitude = $result['lat'];
                $cities[$i]->longitude = $result['lng'];
                $cities[$i]->save();
            }
            echo 'FINISH'.$i;
        }
        return "SUCCESS";
    }
    public function getCityInfo($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        if($output === FALSE ){
            return false;
        }
        curl_close($curl);
        return json_decode($output,JSON_UNESCAPED_UNICODE);
    }
}
