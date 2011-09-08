<?php
/**
 * File for the Console Commands of the Application
 *
 * PHP Version 5.3
 *
 * @category Application
 * @package  CIPS
 * @author   Alfred Danda <alfred.danda@gmail.com>
 * @license  MIT License
 * @link     Project
 */

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

$console = new Application();

$console->register('build')
->setDefinition(array(
    new InputArgument('slug', InputArgument::OPTIONAL, 'Project slug')
))
->setDescription('Build Projects')
->setHelp(<<<EOF
Without any arguments, the <info>build</info> command builds the latest commit
of all configured Projects one after the other:

    <info>./cips build</info>

The command loads Project configurations from
<comment>/config/projects.php</comment>.

Data (repository, DB, ...) are stored in <comment>/data/</comment>.

Pass the Project slug to build a specific Project:

    <info>./cips build your_project_slug</info>
EOF
)->setCode(function (InputInterface $input, OutputInterface $output) use ($app)
{
    $output->write("\n<info>Starting building ...</info>\n");

    $projects = require __DIR__.'/../config/projects.php';
    $config   = require __DIR__.'/../config/config.php';

    if ($slug = $input->getArgument('slug')) {
        if (!array_key_exists($slug, $projects)) {
            $output->writeln(
                "\n".sprintf(
                    '<error>Project "%s" does not exist.</error>'."\n", $slug
                )
            );

            return 1;
        }

        $projects = array($projects[$slug]);
    }

    foreach ($projects as $project) {
        $output->writeln(
            "\n".sprintf('<info>Building Project "%s"</info>', $project->getName())
        );
        $project->checkout($app['build.path'])
            ->build(
                $app['build.path'],
                $app['db'],
                $app['mailer'],
                $app['twig'],
                $config['email_sender']
            );
        $output->writeln(
            sprintf(
                '<info>Finished building Project "%s"</info>', $project->getName()
            )."\n"
        );
    }

    $output->write("\n<info>Finished building</info>\n");
});

$console->register('checkout')
->setDefinition(array(
    new InputArgument('slug', InputArgument::REQUIRED, 'Project slug')
))
->setDescription('Checkout Project')
->setHelp(<<<EOF
The <info>checkout</info> command checks out the Source
of the given Project:

    <info>./cips checkout your_project_slug</info>

The command loads Project configurations from
<comment>/config/projects.php</comment>.
EOF
)->setCode(function (InputInterface $input, OutputInterface $output) use ($app)
{
    $output->write("\n<info>Start check out ...</info>\n");

    $projects = require __DIR__.'/../config/projects.php';

    $slug = $input->getArgument('slug');
    if (!array_key_exists($slug, $projects)) {
        $output->writeln(
            "\n".sprintf('<error>Project "%s" does not exist.</error>'."\n", $slug)
        );

        return 1;
    }

    $project = $projects[$slug];

    $project->checkout($app['build.path']);

    $output->write("\n<info>Finished check out</info>\n");
});

$console->register('update')
->setDefinition(array(
    new InputArgument('slug', InputArgument::OPTIONAL, 'Project slug')
))
->setDescription('Update Projects')
->setHelp(<<<EOF
Without any arguments, the <info>update</info> command updates all configured
Projects one after the other:

    <info>./cips update</info>

The command loads Project configurations from
<comment>/config/projects.php</comment>.

Pass the Project slug to update a specific Project:

    <info>./cips update your_project_slug</info>
EOF
)->setCode(function (InputInterface $input, OutputInterface $output) use ($app)
{
    $output->write("\n<info>Starting update ...</info>\n");

    $projects = require __DIR__.'/../config/projects.php';

    if ($slug = $input->getArgument('slug')) {
        if (!array_key_exists($slug, $projects)) {
            $output->writeln(
                "\n".sprintf('<error>Project "%s" does not exist.</error>'."\n",
                $slug)
            );

            return 1;
        }

        $projects = array($projects[$slug]);
    }

    foreach ($projects as $project) {
        $output->writeln(
            "\n".sprintf('<info>Updating Project "%s"</info>', $project->getName())
        );
        $project->update($app['build.path']);
        $output->writeln(
            sprintf(
                '<info>Finished updating Project "%s"</info>',
                $project->getName()
            )."\n"
        );
    }

    $output->write("\n<info>Finished update</info>\n");
});

$console->register('db:create')
->setDefinition(array())
->setDescription('Build the Database')
->setHelp(<<<EOF
The <info>db:create</info> command creates the Database if she doesn't exist:

    <info>./cips db:create</info>

EOF
)->setCode(function (InputInterface $input, OutputInterface $output) use ($app)
{
    $output->write("\n<info>Starting creating Database ...</info>\n");

    $app['db'];

    $output->write("\n<info>Finished building Database</info>\n");
});

$console->register('db:delete')
->setDefinition(array())
->setDescription('Delete the Database')
->setHelp(<<<EOF
The <info>db:delete</info> command deletes the Database:

    <info>./cips db:delete</info>

EOF
)->setCode(function (InputInterface $input, OutputInterface $output) use ($app)
{
    $output->write("\n<info>Start deleting Database ...</info>\n");

    unlink($app['db.path']);

    $output->write("\n<info>Finished deleting Database</info>\n");
});

$console->register('db:reset')
->setDefinition(array())
->setDescription('Resets the Database')
->setHelp(<<<EOF
The <info>db:reset</info> command deletes the Database and builds an empty new
one:

    <info>./cips db:reset</info>

EOF
)->setCode(function (InputInterface $input, OutputInterface $output) use ($app)
{
    $output->write("\n<info>Start reseting Database ...</info>\n");

    unlink($app['db.path']);
    $app['db'];

    $output->write("\n<info>Finished reseting Database</info>\n");
});

return $console;