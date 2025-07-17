<?php

namespace App\Application\Wechat\Service\Work;

use App\Application\Wechat\Model\WechatWorkDepartment;
use EasyWeChat\Kernel\Exceptions\BadResponseException;
use EasyWeChat\Kernel\HttpClient\AccessTokenAwareClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Department extends AbstractWorkObject
{
    public function getDepartment(int $id): WechatWorkDepartment
    {
        $department = $this->client->get('/cgi-bin/department/get', compact('id'))
            ->toArray()['department'] ?? [];
        $department_model = WechatWorkDepartment::firstOrCreate([
            'id' => $department['id'],
            'corp_id' => $this->corp_id,
        ]);
        $department_model->name = $department['name'] ?? '';
        $department_model->parentid = $department['parentid'] ?? 0;
        $department_model->order = $department['order'] ?? 0;
        $department_model->department_leader = $department['department_leader'] ?? "";
        $department_model->save();

        return $department_model;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws BadResponseException
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getDepartmentList(int $id = 0): array
    {
        $departments = $this->client->get('/cgi-bin/department/list', compact('id'))
            ->toArray()['department'] ?? [];
        $res = [];
        foreach ($departments as $department) {
            $department_model = WechatWorkDepartment::firstOrCreate([
                'id' => $department['id'],
                'corp_id' => $this->corp_id,
            ]);
            $department_model->name = $department['name'] ?? '';
            $department_model->parentid = $department['parentid'] ?? 0;
            $department_model->order = $department['order'] ?? 0;
            $department_model->department_leader = $department['department_leader'] ?? "";
            $department_model->save();
            $res[] = $department_model;
        }

        return $res;
    }
}