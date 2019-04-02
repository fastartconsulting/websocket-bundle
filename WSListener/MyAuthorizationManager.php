<?php

namespace FAC\WebSocketBundle\WSListener;

use Thruway\Event\MessageEvent;
use Thruway\Event\NewRealmEvent;
use Thruway\Module\RealmModuleInterface;
use Thruway\Module\RouterModuleClient;

class MyAuthorizationManager extends RouterModuleClient implements RealmModuleInterface {

    /**
     * @var string
     */
    private $realm;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $realm = "realm1";
        $this->realm = $realm;
        parent::__construct($realm);
    }

    /**
     * Listen for Router events.
     * Required to add the authorization module to the realm
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'new_realm' => ['handleNewRealm', 10]
        ];
    }

    /**
     * @param NewRealmEvent $newRealmEvent
     */
    public function handleNewRealm(NewRealmEvent $newRealmEvent)
    {
        $realm = $newRealmEvent->realm;

        if ($realm->getRealmName() === $this->getRealm()) {
            $realm->addModule($this);
        }
    }

    /**
     * @return array
     */
    public function getSubscribedRealmEvents()
    {
        return [
            'PublishMessageEvent'   => ['authorize', 100],
            'SubscribeMessageEvent' => ['authorize', 100],
            'RegisterMessageEvent'  => ['authorize', 100],
            'CallMessageEvent'      => ['authorize', 100],
        ];
    }

    /**
     * @param MessageEvent $msg
     * @return bool
     */
    public function authorize(MessageEvent $msg)
    {
        //var_dump($msg->session->getAuthenticationDetails()->getAuthId());
        if ($msg->session->getAuthenticationDetails()->getAuthId() === 'username') {
            return true;
        }
        return false;
    }
}
