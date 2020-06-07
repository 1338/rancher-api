<?php
namespace Rootshell\RancherApi\Resources;

use Rootshell\RancherApi\Models\Project;

class Projects {
    private $client;
    private $endpoint;

    public function __construct($client)
    {
        $this->client = $client;
        $this->endpoint = 'projects';
    }

    private function format($container, $tmp) {
        unset($container->links);
        unset($container->actions);

        $tmp->set($container);

        unset($tmp->_readOnlyFields);

        return $tmp;
    }

    /**
     * @param array $criteria
     *
     * @return array
     */
    public function findBy($criteria) {
        $retn = [];

        $containers = $this->client->request('GET', $this->endpoint.'/?'.http_build_query($criteria), [])->data;
        foreach($containers as $key=>$container)
        {
            if($container->type != "project")
                continue;

            array_push($retn, $this->format($container, new Project()));
        }
        return $retn;
    }

    /**
     * @param array $criteria
     *
     * @return Project|null
     */
    public function findOneBy($criteria) {
        $containers = $this->findBy($criteria);
        if (count($containers) > 0) {
            return $this->format($containers[0], new Project());
        }
        return NULL;
    }

    public function getAll() {
        $retn = [];

        $containers = $this->client->request('GET', $this->endpoint, [])->data;
        foreach($containers as $key=>$container)
        {
            if($container->type != "project")
                continue;

            array_push($retn, $this->format($container, new Project()));
        }
        return $retn;
    }

    public function get($id) {
        $container = $this->client->request('GET', $this->endpoint.'/'.$id, []);
        return $this->format($container, new Project());
    }

    public function create(Project $cont) {
        $container = $this->client->request('POST', $this->endpoint, $cont->toArray());
        return $this->format($container, new Project());
    }

    public function remove($id) {
        $container = $this->client->request('DELETE', $this->endpoint.'/'.$id, []);
        return $this->format($container, new Project());
    }

    public function start($id) {
        $container = $this->client->request('POST', $this->endpoint.'/'.$id.'?action=start', []);
        return $this->format($container, new Project());
    }

    public function stop($id) {
        $container = $this->client->request('POST', $this->endpoint.'/'.$id.'?action=stop', []);
        return $this->format($container, new Project());
    }

    public function restart($id) {
        $container = $this->client->request('POST', $this->endpoint.'/'.$id.'?action=restart', []);
        return $this->format($container, new Project());
    }
}