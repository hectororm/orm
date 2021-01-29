<?php

namespace PHPSTORM_META {

    use Hector\Orm\Entity\Entity;
    use Hector\Orm\Query\Builder;

    override(Entity::get(), map(['' => '@']));
}