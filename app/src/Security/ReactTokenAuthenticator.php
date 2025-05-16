<?php

namespace App\Security;

use App\Modules\Users\Entity\User;
use App\Modules\Users\Repository\UserRepository;
use App\Modules\Users\Services\Authorization;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\Token\JWTPostAuthenticationToken;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\TokenExtractorInterface;

class ReactTokenAuthenticator extends AbstractAuthenticator {

	private array $payload;

	public function __construct(
		private UserRepository $userRepository,
		private JWTTokenManagerInterface $jwtManager,
		private JWTEncoderInterface $encoder,
		private TokenExtractorInterface $tokenExtractor,
		private Authorization $auth,
	) {}

	public function supports(Request $request): ?bool {
		return true;
	}

	public function authenticate(Request $request): Passport {
		$token = $this->tokenExtractor->extract($request);
		if(empty($token))
			throw new BadRequestException('Missing or Invalid Authorization provided', 400);

		try {
			$this->payload = $this->encoder->decode($token);
		} catch (\Throwable $e) {
			throw new BadRequestException('Invalid or Expired Authorization Token', 400);
		}

		$passport = new SelfValidatingPassport(
			new UserBadge(
				$this->payload['email'],
				fn(string $userIdentifier): ?UserInterface => $this->userRepository->findOneBy(['email' => $userIdentifier])
		));

		$passport->setAttribute('payload', $this->payload);
		$passport->setAttribute('token', $token);

		return $passport;
	}

	public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response {
		return null;
	}

	public function createToken(Passport $passport, string $firewallName): TokenInterface {
		$user = $passport->getUser();
		if(!$user instanceof User)
			throw new Exception('Expected User Entity, received: '.get_class($user));

		$this->auth->initUserPermissions($user);
		return new JWTPostAuthenticationToken(
			$user,
			$firewallName,
			$this->auth->getGrantedPermissions($this->auth->getUserPermissions()),
			$this->jwtManager->createFromPayload($user, [
				'name' => $user->getName(),
				'email' => $user->getEmail(),
				'id' => $user->getId()
			])
		);
	}

	public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
	{
			$data = [
					// you may want to customize or obfuscate the message first
					'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

					// or to translate this message
					// $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
			];

			return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
	}
}
