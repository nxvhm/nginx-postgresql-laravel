<?php

namespace App\Modules\Users\Listeners\Auth;

use App\Modules\Users\Services\Authorization;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTAuthenticatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Psr\Log\LoggerInterface;
class JwtAuthenticatedListener implements EventSubscriberInterface {

	private $authorization;

	public function __construct(Authorization $auth) {
		$this->authorization = $auth;
	}

	public function onTokenAuthenticated(AuthenticationSuccessEvent $event) {
		$user = $event->getAuthenticationToken()->getUser();
		$this->authorization->initUserPermissions($user);
		$user->setRoles(
			$this->authorization->getGrantedPermissions($this->authorization->getUserPermissions())
		);
		$event->getAuthenticationToken()->setUser($user);
	}

	public static function getSubscribedEvents() {
		return [
				AuthenticationSuccessEvent::class => 'onTokenAuthenticated',
				JWTAuthenticatedEvent::class => 'onTokenAuthenticated'
		];
	}
}
