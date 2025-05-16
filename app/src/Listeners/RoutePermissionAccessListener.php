<?php

namespace App\Listeners;

use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;
use App\Modules\Users\Services\Authorization;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RoutePermissionAccessListener implements EventSubscriberInterface
{

	public function __construct(private RouterInterface $router, private Authorization $auth) {
	}

	public static function getSubscribedEvents() {
		return [
			KernelEvents::CONTROLLER => 'checkPermissions',
		];
	}

	public function checkPermissions(ControllerEvent $event) {
		if(!$event->isMainRequest())
			return;

		$request = $event->getRequest();
		$route = $this->router->getRouteCollection()->get($request->attributes->get('_route'));

		## TODO: IMPLEMENT SOME FORM OF LIST NOTATION SO MORE THEN ONE PERMISSION
		## CAN BE CHECKED AT ONCE (WITH LOGICAL `AND` OR `OR`)
		$permission = $route->getOption('permission');
		if(empty($permission))
			return;

		if(!$this->auth->can($permission)) {
			throw new HttpException(403, 'You are not authorized to perform this request.');
		}
	}
}
