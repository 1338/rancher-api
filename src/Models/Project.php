<?php

namespace Rootshell\RancherApi\Models;

use Rootshell\RancherApi\Models\AbstractModel;

class Project extends AbstractModel {

    public $actions;

    public $enableMonitoring;

    public $exportYaml;

    public $setpodsecuritypolicytemplate;

    public $annotations;

    public $baseType;

    public $clusterId;

    public $conditions;

    public $created;

    public $createdTS;

    public $creatorId;

    public $description;

    public $enableProjectMonitoring;

    public $id;

    public $labels;

    public $links;

    public $name;

    public $namespaceId;

    public $podSecurityPolicyTemplateId;

    public $state;

    public $transitioning;

    public $transitioningMessage;

    public $type;

    public $uuid;

    public $_readOnlyFields = [
        'type'
    ];
}