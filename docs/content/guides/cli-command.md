---
title: "CLI Command"
description: ""
summary: ""
date: 2024-04-24T19:58:35+02:00
lastmod: 2024-05-23T19:58:35+02:00
draft: false
weight: 820
toc: true
seo:
  title: "" # custom title (optional)
  description: "Integrate PHP validation into your Symfony Console commands to validate user-provided command-line arguments." # custom description (recommended)
  canonical: "" # custom canonical URL (optional)
  noindex: false # false (default) or true
---

The validation library can be seamlessly integrated with
[Symfony Console](https://symfony.com/doc/current/components/console.html) applications to ensure the integrity of
user-provided command-line arguments.

```php
use Norvica\Validation\Exception\PropertyRuleViolation;
use Norvica\Validation\Rule\Email;
use Norvica\Validation\Rule\Password;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:create-user')]
class ExampleCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setDescription('Creates a new user.')
            ->addArgument('email', InputArgument::REQUIRED, 'User E-mail address')
            ->addArgument('password', InputArgument::REQUIRED, 'User password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try{
            $data = $this->validate($input);
        } catch (PropertyRuleViolation $e) {
            $output->writeln("<error>{$e->getPath()}: {$e->getText()}</error>");

            return Command::INVALID;
        }

        // Access normalized data
        $email = $data['email'];
        $password = $data['password'];
        // ... your business logic here ...

        return Command::SUCCESS;
    }

    /**
     * @throws PropertyRuleViolation
     */
    private function validate(InputInterface $input): array
    {
        $payload = [
            'email' => $input->getArgument('email'),
            'password' => $input->getArgument('password'),
        ];

        $rules = [
            'email' => new Email(),
            'password' => new Password(),
        ];

        // Instantiate validator (or inject as a dependency)
        $validator = new Validator();

        $result = $validator->validate($payload, $rules);

        return $result->normalized;
    }
}
```

The provided code example demonstrates a basic integration pattern but can be further refined depending on your
application's structure and dependency injection approach.
