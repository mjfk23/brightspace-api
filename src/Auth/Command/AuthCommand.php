<?php

declare(strict_types=1);

namespace Brightspace\Api\Auth\Command;

use Brightspace\Api\Auth\Factory\AuthCodeFactory;
use Brightspace\Api\Auth\Factory\LoginTokenFactory;
use Brightspace\Api\Auth\Model\LoginCredentials;
use Brightspace\Api\Auth\Model\Config;
use Gadget\Http\OAuth\Cache\TokenCache;
use Gadget\Http\OAuth\Factory\TokenFactory;
use Gadget\Http\OAuth\Model\Token;
use Gadget\Io\JSON;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: 'brightspace:api:auth')]
final class AuthCommand extends Command
{
    /**
     * @param Config $config
     * @param LoginTokenFactory $loginTokenFactory
     * @param AuthCodeFactory $authCodeFactory
     * @param TokenFactory $tokenFactory
     * @param TokenCache $tokenCache
     */
    public function __construct(
        private Config $config,
        private LoginTokenFactory $loginTokenFactory,
        private AuthCodeFactory $authCodeFactory,
        private TokenFactory $tokenFactory,
        private TokenCache $tokenCache
    ) {
        parent::__construct();
    }


    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->addOption('use-default', null, InputOption::VALUE_NONE, 'Use default credentials');
    }


    /** @inheritdoc */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $token = $this->tokenCache->get($this->config->tokenCacheKey)
            ?? $this->createToken($input, $output);
        $output->writeln(JSON::encode($token, JSON_PRETTY_PRINT));

        return self::SUCCESS;
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return Token
     */
    private function createToken(
        InputInterface $input,
        OutputInterface $output
    ): Token {
        $credentials = $this->getCredentials($input, $output);
        $loginToken = $this->loginTokenFactory->create($credentials);
        $authCode = $this->authCodeFactory->createFromLoginToken($loginToken);
        $token = $this->tokenFactory->createFromAuthCode($authCode);
        $this->tokenCache->set($this->config->tokenCacheKey, $token);
        return $token;
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return LoginCredentials|null
     */
    private function getCredentials(
        InputInterface $input,
        OutputInterface $output
    ): LoginCredentials|null {
        if ($input->getOption('use-default') === true) {
            return null;
        }
        $username = $this->getUsername($input, $output);
        $password = $this->getPassword($input, $output);
        $mfaToken = $this->getMFAToken($input, $output);

        return new LoginCredentials(
            $username,
            $password,
            $mfaToken
        );
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
