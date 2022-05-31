<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/4/27 10:19
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service\Lib;

use Hyperf\Contract\SessionInterface;
use Hyperf\Di\Annotation\Inject;
use Symfony\Component\HttpFoundation\Session\SessionBagInterface;

class WechatRequestSession implements \Symfony\Component\HttpFoundation\Session\SessionInterface
{
    /**
     * @Inject()
     */
    protected SessionInterface $session;

    public function start(): bool
    {
        return $this->session->start();
    }

    public function getId(): string
    {
        return $this->session->getId();
    }

    public function setId(string $id)
    {
        $this->session->setId($id);
    }

    public function getName(): string
    {
        return $this->session->getName();
    }

    public function setName(string $name)
    {
        $this->session->setName($name);
    }

    public function invalidate(int $lifetime = null): bool
    {
        return $this->session->invalidate($lifetime);
    }

    public function migrate(bool $destroy = false, int $lifetime = null): bool
    {
        return $this->session->migrate($destroy, $lifetime);
    }

    public function save()
    {
        $this->session->save();
    }

    public function has(string $name): bool
    {
        return $this->session->has($name);
    }

    public function get(string $name, $default = null)
    {
        return $this->session->get($name, $default);
    }

    public function set(string $name, $value)
    {
        $this->session->get($name, $value);
    }

    public function all()
    {
        $this->session->all();
    }

    public function replace(array $attributes)
    {
        $this->session->replace($attributes);
    }

    public function remove(string $name)
    {
        $this->session->remove($name);
    }

    public function clear()
    {
        $this->session->clear();
    }

    public function isStarted(): bool
    {
        return $this->session->isStarted();
    }

    public function registerBag(SessionBagInterface $bag)
    {
        // TODO: Implement registerBag() method.
    }

    public function getBag(string $name)
    {
        // TODO: Implement getBag() method.
    }

    public function getMetadataBag()
    {
        // TODO: Implement getMetadataBag() method.
    }
}