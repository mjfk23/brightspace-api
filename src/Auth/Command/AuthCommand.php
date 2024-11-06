<?php

declare(strict_types=1);

namespace Brightspace\Api\Auth\Command;

use Brightspace\Api\Auth\Factory\LoginTokenFactory;
use Brightspace\Api\Auth\Factory\OAuthTokenFactory;
use Brightspace\Api\Auth\Model\LoginCredentials;
use Brightspace\Api\Auth\Model\AuthConfig;
use Gadget\Http\OAuth\OAuthToken;
use Gadget\Http\OAuth\OAuthTokenCache;
use Gadget\Io\JSON;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: 'brightspace:api:auth')]
final class AuthCommand extends Command
{
    /**
     * @param AuthConfig $authConfig
     * @param LoginTokenFactory $loginTokenFactory
     * @param OAuthTokenFactory $oauthTokenFactory
     * @param OAuthTokenCache $oauthTokenCache
     */
    public function __construct(
        private AuthConfig $authConfig,
        private LoginTokenFactory $loginTokenFactory,
        private OAuthTokenFactory $oauthTokenFactory,
        private OAuthTokenCache $oauthTokenCache
    ) {
        parent::__construct();
    }


    /** @inheritdoc */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $oauthToken = $this->getOAuthToken($input, $output);
        $this->oauthTokenCache->set($this->authConfig->defaultKey, $oauthToken);
        $output->writeln(JSON::encode($oauthToken, JSON_PRETTY_PRINT));

        return self::SUCCESS;
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return OAuthToken
     */
    private function getOAuthToken(
        InputInterface $input,
        OutputInterface $output
    ): OAuthToken {
        return $this->oauthTokenCache->get($this->authConfig->defaultKey) ??
            $this->oauthTokenFactory->fromLoginToken($this->getLoginToken($input, $output));
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return string
     */
    private function getLoginToken(
        InputInterface $input,
        OutputInterface $output
    ): string {
        $username = $this->getUsername($input, $output);
        $password = $this->getPassword($input, $output);
        $mfaToken = $this->getMFAToken($input, $output);

        return $this->loginTokenFactory->create(new LoginCredentials(
            $username,
            $password,
            $mfaToken
        ));
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return string
     */
    private function getUsername(
        InputInterface $input,
        OutputInterface $output
    ): string {
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');
        $user = $helper->ask($input, $output, new Question('Username: '));
        return is_scalar($user) ? strval($user) : '';
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return string
     */
    private function getPassword(
        InputInterface $input,
        OutputInterface $output
    ): string {
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');
        $question = new Question('Password: ');
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $pass = $helper->ask($input, $output, $question);
        return is_scalar($pass) ? strval($pass) : '';
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    private function getMFAToken(
        InputInterface $input,
        OutputInterface $output
    ): int {
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');
        $mfaToken = $helper->ask($input, $output, new Question('MFA Token: '));
        return is_scalar($mfaToken) ? intval($mfaToken) : 0;
    }
}
