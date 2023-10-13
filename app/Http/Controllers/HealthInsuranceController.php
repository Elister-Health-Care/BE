<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestCreateHealthInsurance;
use App\Http\Requests\RequestUpdateHealthInsurance;
use App\Services\HealthInsuranceService;
use Illuminate\Http\Request;
use KubAT\PhpSimple\HtmlDomParser;

class HealthInsuranceController extends Controller
{
    protected HealthInsuranceService $healthInsuranceService;

    public function __construct(HealthInsuranceService $healthInsuranceService)
    {
        $this->healthInsuranceService = $healthInsuranceService;
    }

    public function add(RequestCreateHealthInsurance $request)
    {
        return $this->healthInsuranceService->add($request);
    }

    public function edit(RequestUpdateHealthInsurance $request, $id)
    {
        return $this->healthInsuranceService->edit($request, $id);
    }

    public function delete($id)
    {
        return $this->healthInsuranceService->delete($id);
    }

    public function all(Request $request)
    {
        // $file_name = file_get_html("https://vnexpress.net/giao-duc/du-hoc");

        $htmlurl = 'https://www.google.com/search?q=du+hoc';
        $dom = HtmlDomParser::str_get_html( $htmlurl );
        $title = $dom->find('h3', 0); // Lấy nội dung của thẻ <title>

        dd($dom);
        return response()->json([
            'data' => $title,
        ]);

        dd($dom);


        // In ra kết quả
        echo $title;
        return response()->json([
            'data' => $title,
        ]);
        
        return $this->healthInsuranceService->all($request);
    }

    public function details(Request $request, $id)
    {
        return $this->healthInsuranceService->details($request, $id);
    }
}
