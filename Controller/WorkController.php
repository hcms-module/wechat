<?php

declare(strict_types=1);

namespace App\Application\Wechat\Controller;

use App\Annotation\Api;
use App\Application\Wechat\Service\WorkService;
use App\Controller\AbstractController;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Work\Message;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;

#[Controller(prefix: "/wechat/work")]
class WorkController extends AbstractController
{

    #[Inject]
    protected WorkService $workService;

    #[Api]
    #[GetMapping]
    public function index()
    {
        $res = $this->workService->oa->getApprovalDetail("202507160001");

        return compact('res');
    }

    /**
     * @throws \ReflectionException
     * @throws InvalidArgumentException
     * @throws \Throwable
     */
    #[GetMapping]
    public function msg()
    {
        // 新增成员事件
        $this->workService->getServer($this->request)
            ->handleUserCreated(function (Message $message, \Closure $next) {
                //TODO: 处理事件
                return $next($message);
            });
    }
}
