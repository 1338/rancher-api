# rancher-api
rancher API v3 for php

I found easy implementations for rancher API v3 lacking, so here this is.

# Currrent state
There still is a lot of API to implement, pulls are welcome.

## Example importing rancher "projects", where RancherConfig contains:
- name
- host
- token
- secret
```php
<?php

namespace App\Command;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use App\Repository\RancherConfigRepository;
use Doctrine\ORM\EntityManagerInterface;
use Rootshell\RancherApi\Rancher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RancherGetProjectsCommand extends Command
{
    protected static $defaultName = 'rancher:populate:projects';
    /**
     * @var RancherConfigRepository
     */
    private $rancherConfigRepository;
    /**
     * @var ProjectRepository
     */
    private $projectRepository;
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(RancherConfigRepository $rancherConfigRepository, ProjectRepository $projectRepository, EntityManagerInterface $entityManager)
    {
        $this->rancherConfigRepository = $rancherConfigRepository;
        $this->projectRepository = $projectRepository;
        $this->entityManager = $entityManager;
        Command::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('name', InputArgument::OPTIONAL, 'RancherConfig Name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $name = $input->getArgument('name');
        $rancherOption = $this->rancherConfigRepository->findAll();

        if(empty($name)) {
            $rancherOption[] = 'all';
            $name = $io->choice('Select config to check', $rancherOption);
        }

        $entityList = [];
        if($name != 'all') {
            $entityList = [$name];
        } else {
            $key = array_search($name, $rancherOption);
            if($key){
                unset($rancherOption[$key]);
            }
            $entityList = $rancherOption;
        }

        foreach ($entityList as $rancherEntity) {
            if(is_string($rancherEntity)) {
                $rancherEntity = $this->rancherConfigRepository->findOneBy(['name' => $rancherEntity]);
            }
            $io->writeln("getting projects for: {$rancherEntity->getName()}");
            $rancher = new Rancher($rancherEntity->getHost(), $rancherEntity->getAccessToken(), $rancherEntity->getSecret());
            $projects = $rancher->projects()->getAll();
            foreach ($projects as $project) {
                $entity = $this->projectRepository->findOneBy(['rancher_id' => $project->id]);
                if(!$entity) {
                    $entity = new Project();
                    $entity->setRancherId($project->id);
                    $io->writeln("Don't have an project with the id: {$project->id} which has the name: {$project->name}");
                }
                $entity->setName($project->name);
                $entity->setUuid($project->uuid);

                $this->entityManager->persist($entity);
            }

        }
        $this->entityManager->flush();

        $io->success('Entities updated!');
        return 0;
    }
}
```
