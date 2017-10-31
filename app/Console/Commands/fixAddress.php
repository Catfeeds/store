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
        echo count($provinces)."provinces";
        echo count($cities)."cities";
        echo count($dist);
        for ($i=0;$i<count($provinces);$i++){
            $province = new City();
            $province->id = $provinces[$i]['id'];
            $province->name = $provinces[$i]['fullname'];
            $province->latitude = $provinces[$i]['location']['lat'];
            $province->longitude = $provinces[$i]['location']['lng'];
            $province->save();
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
