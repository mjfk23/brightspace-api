<?php

declare(strict_types=1);

namespace Brightspace\Api\Auth\Factory;

use Brightspace\Api\Auth\Model\Config;
use Brightspace\Api\Auth\Model\LoginContext;
use Brightspace\Api\Auth\Model\LoginCredentials;
use Brightspace\Api\Core\BrightspaceApiClient;
use Gadget\Http\ApiClient;
use Gadget\Security\MFA\TOTP;
use Psr\Http\Message\ResponseInterface;

final class LoginTokenFactory
{
    public const LOGIN_URI         = '/d2l/lp/auth/login/login.d2l';
    public const MFA_URI           = '/d2l/lp/auth/twofactorauthentication/TwoFactorCodeEntry.d2l';
    public const PROCESS_LOGIN_URI = '/d2l/lp/auth/login/ProcessLoginActions.d2l';
    public const HOME_URI          = '/d2l/home';


    private LoginContext $context;


    /**
     * @param Config $config
     * @param ApiClient $apiClient
     * @param LoginCredentials|null $credentials
     */
    public function __construct(
        private Config $config,
        private ApiClient $apiClient,
        private LoginCredentials|null $credentials = null
    ) {
        $this->context = new LoginContext();
    }


    /**
     * @param LoginCredentials|null $credentials
     * @return string
     */
    public function create(LoginCredentials|null $credentials = null): string
    {
        $credentials ??= $this->credentials;
        if ($credentials === null) {
            throw new \RuntimeException();
        }

        $this->context = new LoginContext($credentials);
        return $this
            ->submitCredentials()
            ->processMFA()
            ->getLoginToken();
    }


    /**
     * @return self
     */
    private function submitCredentials(): self
    {
        return $this->apiClient->sendApiRequest(
            method: 'POST',
            uri: "d2l://web" . self::LOGIN_URI,
            headers: ['Content-Type' => 'application/x-www-form-urlencoded'],
            body: [
                'd2l_referrer' => '',
                'noredirect'   => '1',
                'loginPath'    => self::LOGIN_URI,
                'userName'     => $this->context->user,
                'password'     => $this->context->pass
            ],
            parseResponse: $this->getTokenFromResponse(...)
        );
    }


    /**
     * @return self
     */
    private function processMFA(): self
    {
        return ($this->context->mfa !== null && in_array(self::MFA_URI, $this->context->location, true))
            ? $this
                ->generateMFA()
                ->submitMFA()
                ->processLoginActions()
            : $this;
    }


    /**
     * @return string
     */
    private function getLoginToken(): string
    {
        return in_array(self::HOME_URI, $this->context->location, true)
            ? $this->context->loginToken
            : throw new \RuntimeException("Error logging in");
    }


    /**
     * @return self
     */
    private function generateMFA(): self
    {
        list(
            $this->context->xsrfName,
            $this->context->xsrfCode,
            $this->context->hitCodeSeed
        ) = $this->parseMFA($this->apiClient->sendApiRequest(
            method: 'GET',
            uri: "d2l://web" . self::MFA_URI,
            headers: ['Cookie' => $this->context->loginToken],
            parseResponse: ApiClient::rawResponse(...)
        ));

        $rightNow = time();
        $this->context->hitCode = intval($this->context->hitCodeSeed) + ((1000 * $rightNow + 100000000) % 100000000);
        $this->context->mfaCode = match (true) {
            is_string($this->context->mfa) => (new TOTP())
                ->setKey($this->context->mfa)
                ->setCurrentTime($rightNow)
                ->generate(),
            is_int($this->context->mfa) => strval($this->context->mfa),
            default => ''
        };

        return $this;
    }


    /**
     * @return self
     */
    private function submitMFA(): self
    {
        return $this->apiClient->sendApiRequest(
            method: 'POST',
            uri: "d2l://web" .  self::MFA_URI . "?ou={$this->config->orgId}&d2l_rh=rpc&d2l_rt=call",
            headers: [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Cookie' => $this->context->loginToken
            ],
            body: [
                'd2l_rf' => 'VerifyPin',
                'params' => '{"param1":"' . $this->context->mfaCode . '"}',
                "{$this->context->xsrfName}" => $this->context->xsrfCode,
                'd2l_hitcode' => $this->context->hitCode,
                'd2l_action' => 'rpc'
            ],
            parseResponse: $this->getTokenFromResponse(...)
        );
    }


    /**
     * @return self
     */
    private function processLoginActions(): self
    {
        return $this->apiClient->sendApiRequest(
            method: 'GET',
            uri: "d2l://web" . self::PROCESS_LOGIN_URI,
            headers: ['Cookie' => $this->context->loginToken],
            parseResponse: $this->getTokenFromResponse(...)
        );
    }


    /**
     * @param ResponseInterface $response
     * @return array{string,string,string}
     */
    private function parseMFA(ResponseInterface $response): array
    {
        /**
         * @param string[]|false $grep
         * @return string
         */
        $subject = fn (array|false $grep): string => is_array($grep) ? (string)(end($grep) ?? "") : "";

        $matches = [];
        preg_match(
            '/\\\"P\\\"\:\[(.*)\]/',
            $subject(preg_grep(
                '/.*D2L\.LP\.Web\.Authentication\.Xsrf\.Init/',
                explode("\n", $response->getBody()->getContents())
            )),
            $matches
        );

        /** @var array{string,string,string} */
        return array_slice([
            ...array_map(
                fn(string $v) => trim($v, '\"'),
                explode(",", $matches[1] ?? ",,")
            ),
            '',
            '',
            '0'
        ], 0, 3);
    }


    /**
     * @param ResponseInterface $response
     * @return self
     */
    private function getTokenFromResponse(ResponseInterface $response): self
    {
        $this->context->loginToken = implode("; ", array_map(
            fn($v) => trim(explode(";", $v)[0] ?? ''),
            $response->getHeader("Set-Cookie")
        ));

        $this->context->location = $response->getHeader('Location');

        return $this;
    }
}
