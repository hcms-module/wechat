<?php

namespace App\Application\Wechat\Service\Work;

use App\Application\Wechat\Model\WechatWorkDepartment;
use App\Application\Wechat\Model\WechatWorkUser;
use App\Exception\ErrorException;

class User extends AbstractWorkObject
{

    /**
     * @param string $userid
     * @return mixed|string
     * @throws ErrorException
     */
    public function getOpenId(string $userid): string
    {
        return $this->postJson('/cgi-bin/user/convert_to_openid', compact('userid'))['openid'] ?? "";
    }

    /**
     * 获取所有部门的用户
     *
     * @return bool
     * @throws ErrorException
     */
    public function getUsersByAllDepartment(): bool
    {
        $departments = WechatWorkDepartment::where('corp_id', $this->corp_id)
            ->get();
        foreach ($departments as $department) {
            $this->getUsersByDepartment($department->id);
        }

        return true;
    }

    /**
     * 获取指定部门的用户
     *
     * @param int $departmentId
     * @return array
     * @throws ErrorException
     */
    public function getUsersByDepartment(int $departmentId): array
    {
        $user_list = $this->get('/cgi-bin/user/simplelist', ['department_id' => $departmentId])['userlist'];
        foreach ($user_list as $user) {
            $userid = $user['userid'] ?? 0;
            if ($userid) {
                $this->getUserById($userid);
                usleep(300);
            }
        }

        return [];
    }

    /**
     * 获取用户信息
     *
     * @param string $userid
     * @return WechatWorkUser
     * @throws ErrorException
     */
    public function getUserById(string $userid): WechatWorkUser
    {
        $info = $this->get('/cgi-bin/user/get', compact('userid'));
        $user = WechatWorkUser::firstOrCreate([
            'userid' => $info['userid'] ?? '',
            'corp_id' => $this->corp_id,
        ]);
        $user->name = $info['name'] ?? '';
        $user->department = $info['department'] ?? [];
        $user->main_department = $info['main_department'] ?? 0;
        $user->position = $info['position'] ?? '';
        $user->status = $info['status'] ?? 0;
        $user->isleader = $info['isleader'] ?? 0;
        $user->telephone = $info['telephone'] ?? '';
        $user->enable = $info['enable'] ?? 0;
        $user->alias = $info['alias'] ?? '';
        $user->direct_leader = $info['direct_leader'] ?? [];
        $user->gender = $info['gender'] ?? '';
        $user->avatar = $info['avatar'] ?? '';
        $user->qr_code = $info['qr_code'] ?? '';
        $user->address = $info['address'] ?? '';
        $user->save();

        return $user;
    }
}