<?php

declare(strict_types=1);

namespace App\Application\Wechat\Controller;

use App\Annotation\Api;
use App\Annotation\View;
use App\Application\Okr\Service\TestOaService;
use App\Application\Wechat\Event\Work\OpenMsgEvent;
use App\Application\Wechat\Model\WechatOpenworkMsg;
use App\Application\Wechat\Service\WorkService;
use App\Controller\AbstractController;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Work\Message;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Psr\EventDispatcher\EventDispatcherInterface;

#[Controller(prefix: "/wechat/work")]
class WorkController extends AbstractController
{

    #[Inject]
    protected WorkService $workService;

    #[Inject]
    protected EventDispatcherInterface $eventDispatcher;


    #[Api]
    #[GetMapping]
    public function jssdk()
    {
        $url = urldecode($this->request->input('url'));
        $jsApiList = $this->request->input('jsApiList', []);
        $openTagList = $this->request->input('openTagList', []);
        $config = $this->workService->getApp()
                ->getUtils()
                ->buildJsSdkConfig($url, $jsApiList, $openTagList,
                    true) + ['agentId' => $this->workService->getAgentId()];

        return compact('config');
    }


    #[View]
    #[GetMapping]
    public function web()
    {

    }

    #[Api]
    #[GetMapping("oauth/back")]
    public function oauthBack()
    {
        $user = $this->workService->getApp()
            ->getOAuth()
            ->userFromCode($this->request->input('code'));

        return ['user' => $user->getRaw()];
    }

    #[GetMapping]
    public function oauth()
    {
        //默认是自定义回调链接，如果前端有传，就以前端为准
        $redirect_url = $this->request->input('redirect_url', url("/wechat/work/oauth/back", [], true));
        $redirect_url = urldecode($redirect_url);
        $url = $this->workService->getApp()
            ->getOAuth()
            ->redirect($redirect_url);

        return $this->response->redirect($url);
    }

    #[Api]
    #[GetMapping]
    public function index()
    {
//        $res = $this->workService->user->getUsersByAllDepartment();

//        $res = $this->workService->oa->getApprovalDetail("202507270006");
        $res = (new TestOaService())->setCreatorUserid('HuangZhenLian')
            ->setTitle("这是标题111222")
            ->setContent("这是简介\n这是简介")
            ->setDate(date("Y-m-d", time() - 30 * 86400))
            ->setPeople("HuangZhenLian")
            ->setUseTemplateApprover(0)
            ->submit();

        return compact('res');
    }

    /**
     * @throws \ReflectionException
     * @throws InvalidArgumentException
     * @throws \Throwable
     */
    #[RequestMapping]
    public function msg()
    {
        $echostr = $this->request->input('echostr');
        if ($echostr) {
            return $this->workService->getServer($this->request)
                ->serve();
        }
        /**
         * @var Message $message
         */
        $message = $this->workService->getServer($this->request)
            ->getDecryptedMessage();
        $to_user_name = $message->ToUserName ?? '';
        $from_user_name = $message->FromUserName ?? '';
        $msg_type = $message->MsgType ?? '';
        $msg = WechatOpenworkMsg::create([
            'to_user_name' => $to_user_name,
            'from_user_name' => $from_user_name,
            'msg_type' => $msg_type,
            'msg_content' => $message->toJson(),
        ]);
        if ($msg instanceof WechatOpenworkMsg) {
            //出发消息接收事件
            $this->eventDispatcher->dispatch(new OpenMsgEvent($msg));
        }

        return $this->workService->getServer($this->request)
            ->serve();
    }
}
