<?php

declare(strict_types=1);

namespace App\Application\Wechat\Controller;

use App\Annotation\View;
use App\Application\Admin\Controller\AdminAbstractController;
use App\Application\Admin\Middleware\AdminMiddleware;
use App\Application\Wechat\Model\WechatApp;
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
     * @GetMapping(path="edit/info")
     */
    public function appEditInfo()
    {
        $id = intval($this->request->input('id', 0));
        $app = WechatApp::find($id);

        return $this->returnSuccessJson(compact('app'));
    }

    /**
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
     * @GetMapping(path="index/lists")
     */
    public function appLists()
    {
        $where = [];
        $lists = WechatApp::where($where)
            ->orderBy('id', 'DESC')
            ->paginate();

        return $this->returnSuccessJson(compact('lists'));
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
}
