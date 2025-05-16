<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;

#[AsCommand(name: 'app:string-hash')]
class StringHasher extends Command {
	protected function execute(InputInterface $input, OutputInterface $output): int {
		$factory = new PasswordHasherFactory([
			'common' => ['algorithm' => 'bcrypt']
		]);
		$hasher = $factory->getPasswordHasher('common');
		echo $hasher->hash($input->getArgument('inputString')) . PHP_EOL;
		return Command::SUCCESS;
	}

	protected function configure(): void {
			$this
				->addArgument('inputString', InputArgument::REQUIRED, 'String to hash')
				->setDescription('Hash a string using the password hasher')
				->setHelp('This command allows you to hash a string to use as a password')
			;
	}
}
