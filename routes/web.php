<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $mainFlight = [];
    try {
        $crawler = Goutte::request('GET', 'http://localhost/roster.html');
        $table = $crawler->filterXPath('//table[1]')->filter('tr[style="height:11px"]')->each(function ($tr, $i) {
            return $tr->filter('td')->each(function ($td, $i) {
                return trim($td->text());
            });
        });

        if(empty($table))throw new Exception('No table found');
        
        $rowCount = count($table);
        $columnWiseData = [];
        for($i = 0; $i < $rowCount; $i++){
            $columnValue = array_column($table, $i);
            $columnWiseData[] = $columnValue;
        }

        
        foreach ($columnWiseData as  $value) {
            foreach ($value as $columnkey => $data) {
                $flight = [];

                if(is_numeric($data)){
                    $dataKey = array_search($data, $value);
                    $loopValue = $columnkey + 5;
                    for($j = $columnkey; $loopValue >= $j; $j++ ){
                        $flight[] = $value[$j];
                    }

                    if(count($flight) === 6){
                        $mainFlight[] =[
                            "Flight Number" => $flight[0],
                            "Report Time" => $flight[1],
                            "Departure Time" => $flight[2],
                            "Departure Airport" => $flight[3],
                            "Arrival Time" => $flight[4],
                            "Arrival Airport" => $flight[5]
                        ]; 
                    }
                    
                }
            }
        }
        if(empty($mainFlight)) throw new Exception('No record found');
        
    } catch (Throwable $e) {
        $mainFlight = [
            "error" => $e->getMessage()
        ];
    }

    echo json_encode($mainFlight);
    
});
