<?php

declare(strict_types=1);

namespace App\Application\Wechat\Controller;

use App\Annotation\Api;
use App\Controller\AbstractController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;


#[Controller(prefix: "/wechat/test")]
class TestController extends AbstractController
{

    #[Api]
    #[GetMapping("index")]
    public function index()
    {
    }
}
