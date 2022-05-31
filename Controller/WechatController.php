<?php

declare(strict_types=1);

namespace App\Application\Wechat\Controller;

use App\Annotation\Api;
use App\Annotation\View;
use App\Application\Admin\Controller\AdminAbstractController;
use App\Application\Admin\Middleware\AdminMiddleware;
use App\Application\Wechat\Model\WechatApp;
use App\Application\Wechat\Service\WechatSetting;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\PostMapping;

/**
 * @Middleware(AdminMiddleware::class)
 * @Controller(prefix="/wechat/wechat")
 */
class WechatController extends AdminAbstractController
{

    /**
     * @Inject()
     */
    protected WechatSetting $setting;

    /**
     * @Api()
     * @GetMapping(path="edit/info")
     */
    public function appEditInfo()
    {
        $id = intval($this->request->input('id', 0));
        $app = WechatApp::find($id);

        return compact('app');
    }

    /**
     * @Api()
     * @PostMapping(path="edit")
     */
    public function appSubmitEdit()
    {
        $validator = $this->validationFactory->make($this->request->all(), [
            'app_key' => 'required',
            'app_id' => 'required',
            'app_secret' => 'required',
        ], [
            'app_key.required' => '请输入应用key',
            'app_id.required' => '请输入app_id',
            'app_secret.required' => '请输入app_secret',
        ]);
        if ($validator->fails()) {
            return $this->returnErrorJson($validator->errors()
                ->first());
        }
        $id = intval($this->request->post('id', 0));
        $app_key = $this->request->post('app_key', '');
        if (WechatApp::where('app_key', $app_key)
                ->whereNotIn('id', [$id])
                ->count() > 0) {
            return $this->returnErrorJson('该应用key已经存在');
        }
        $app_model = WechatApp::findOrNew($id);
        $app_model->app_key = $this->request->post('app_key', '');
        $app_model->app_type = intval($this->request->post('app_type', 0));
        $app_model->app_id = $this->request->post('app_id', '');
        $app_model->app_secret = $this->request->post('app_secret', '');
        $app_model->token = $this->request->post('token', '');
        $app_model->aes_key = $this->request->post('aes_key', '');

        return $app_model->save() ? $this->returnSuccessJson() : $this->returnErrorJson();
    }

    /**
     * @Api()
     * @PostMapping(path="index/delete")
     */
    public function appDelete()
    {
        $id = intval($this->request->input('id', 0));
        $app = WechatApp::find($id);
        if (!$app) {
            return $this->returnErrorJson('找不到该记录');
        }

        return $app->delete() ? $this->returnSuccessJson() : $this->returnErrorJson();
    }

    /**
     * @Api()
     * @GetMapping(path="index/lists")
     */
    public function appLists()
    {
        $where = [];
        $lists = WechatApp::where($where)
            ->orderBy('id', 'DESC')
            ->paginate();

        return compact('lists');
    }

    /**
     * @View()
     * @GetMapping(path="edit")
     */
    public function edit()
    {
        $id = intval($this->request->input('id', 0));

        return ['title' => $id > 0 ? '编辑应用' : '新增应用'];
    }

    /**
     * @View()
     * @GetMapping(path="index")
     */
    public function index() { }

    /**
     * @Api()
     * @PostMapping(path="work/setting")
     */
    public function workSetting()
    {
        $post_data = $this->request->post();

        return $this->setting->saveWorkSetting($post_data) ? [] : $this->returnErrorJson();
    }

    /**
     * @Api()
     * @GetMapping(path="work/setting")
     */
    public function workSettingInfo()
    {
        $setting = $this->setting->getWorkSetting();

        return compact('setting');
    }

    /**
     * @View()
     * @GetMapping(path="work")
     */
    public function work()
    {

    }
}
