<?php

namespace App\Security;

use App\Library\Resources\ResourceResponse;
use App\Modules\Users\Entity\User;
use App\Modules\Users\Transformers\Auth\ReactLoginResponseTransformer;
use Exception;
use League\Fractal\Resource\Item;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\Token\JWTPostAuthenticationToken;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\HttpUtils;

class ReactLoginAuthenticator implements AuthenticatorInterface {
	public function __construct(
		private HttpUtils $httpUtils,
		private UserProviderInterface $userProvider,
		private JWTTokenManagerInterface $jwtManager,
		private ReactLoginResponseTransformer $loginResponseTransformer
  ) {}

	public function supports(Request $request): bool {
		return $this->httpUtils->checkRequestPath($request, '/api/login');
	}

	public function authenticate(Request $request): Passport {
		$data = json_decode($request->getContent(), true);
		if(!$data || empty($data))
			throw new BadRequestHttpException('Invalid JSON.');

		if(empty($data['email']) || empty($data['password']))
			throw new BadRequestHttpException('Login Credentials not provided.', code: 422);

		$userBadge = new UserBadge($data['email']);
		$passport = new Passport($userBadge, new PasswordCredentials($data['password']));

		return $passport;
	}

	public function createToken(Passport $passport, string $firewallName): TokenInterface {
		$user = $passport->getUser();
		if(!$user instanceof User)
			throw new Exception('Expected User Entity, received: '.get_class($user));

		return new JWTPostAuthenticationToken(
			$user,
			$firewallName,
			[],
			$this->jwtManager->createFromPayload($user, [
				'name' => $user->getName(),
				'email' => $user->getEmail(),
				'id' => $user->getId()
			])
		);
	}

	public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response {
		if(!$token instanceof JWTPostAuthenticationToken)
			return null;

		return new JsonResponse(
			(new ResourceResponse(new Item($token, $this->loginResponseTransformer)))->getResponse()->toArray()
		);
	}

	public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response {
		return null;
	}
}
