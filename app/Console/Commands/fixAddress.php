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
        $data = $this->getCityInfo('http://apis.map.qq.com/ws/district/v1/list?key=FF2BZ-H34WP-GQPDC-VFKIS-P7DDH-BCFNG');
        $provinces = $data['result'][0];
        $cities = $data['result'][1];
        $dist = $data['result'][2];
        for ($i=0;$i<count($provinces);$i++){
            $city = new City();
            $city->id = $provinces[$i]['id'];
            $city->name = $provinces[$i]['fullname'];
            $city->latitude = $provinces[$i]['location']['lat'];
            $city->longitude = $provinces[$i]['location']['lng'];
            echo "finish".$city->name."\n";
            $city->save();
            if (isset($provinces[$i]['cidx'])){
                echo $provinces[$i]['cidx'][0]."==".$provinces[$i]['cidx'][1];
                $ci = array_slice($cities,$provinces[$i]['cidx'][0],$provinces[$i]['cidx'][1]);
                for ($j=0;$j<count($ci);$j++){
                    $city1 = new City();
                    $city1->id = $ci[$j]['id'];
                    $city1->pid = $provinces[$i]['id'];
                    $city1->latitude = $ci[$j]['location']['lat'];
                    $city1->longitude = $ci[$j]['location']['lng'];
                    $city1->name = $ci[$j]['fullname'];
                    echo "finish".$city1->name."\n";
                    $city1->save();
                    if (isset($ci[$i]['cidx'])){
                        echo $ci[$i]['cidx'][0]."==".$ci[$i]['cidx'][1];
                        $ix = array_slice($dist,$ci[$i]['cidx'][0],$ci[$i]['cidx'][1]);
                        for ($k = 0;$k<count($ix);$k++){
                            $city2 = new City();
                            $city2->id = $ix[$k]['id'];
                            $city2->pid = $ci[$i]['id'];
                            $city2->latitude = $ix[$k]['location']['lat'];
                            $city2->longitude = $ix[$k]['location']['lng'];
                            $city2->name = $ix[$k]['fullname'];
                            echo "finish".$city2->name."\n";
                            $city2->save();
                        }
                    }
                }
            }
        }
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
