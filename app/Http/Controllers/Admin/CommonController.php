<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Services\QiNiuService;
use Illuminate\Http\Request;

class CommonController extends Controller
{
    /**
     *  七牛云
     * @var QiNiuService
     */
    private QiNiuService $qiNiuService;

    public function __construct( QiNiuService $qiNiuService )
    {
        $this->qiNiuService = $qiNiuService;
    }

    /**
     * Notes: 获取七牛上传TOKEN
     * User: 一颗地梨子
     * DateTime: 2022/3/5 15:38
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQiNiuToken ()
    {
        return $this->ok($this->qiNiuService->getToken());
    }
}
