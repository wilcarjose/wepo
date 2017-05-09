<?php

namespace Wilcar\Wepo\Factories;

class RepositoryFactory
{
    protected $repoEnds;

    public function __construct()
    {
        $this->repoEnds = config('wepo.repository_ends', 'Repository');
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function get($name)
    {
        $name = studly_case($name);

        $repositoryClass = $this->getRepository($name);

        return new $repositoryClass($this->getModel($name));
    }

    protected function getModel($name)
    {
        if (str_contains($name, '\\') || ends_with($name, $this->repoEnds)) {
            return null;
        }

        return config('wepo.models_namespace').$name;
    }

    protected function getRepository($name)
    {
        if (str_contains($name, '\\')) {
            return $this->setRepoName($name);
        }

        return config('wepo.repositories_namespace').$this->setRepoName($name);
    }

    protected function setRepoName($name)
    {
        if (!ends_with($name, $this->repoEnds)) {
            $name = $name.$this->repoEnds;
        }

        return $name;
    }
}
