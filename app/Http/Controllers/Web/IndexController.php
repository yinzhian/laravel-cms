<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Services\Union\AliService;
use App\Http\Services\Union\ZtkService;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    private ZtkService $ztkService;
    private AliService $aliService;

    public function __construct( ZtkService $ztkService, AliService $aliService )
    {
        $this->ztkService = $ztkService;
        $this->aliService = $aliService;
    }

    public function demo ( Request $request )
    {

        dd("demo");
    }
}
