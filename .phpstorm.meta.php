<?php

namespace PHPSTORM_META {

    use Hector\Orm\Entity\Entity;

    override(Entity::get(0), map(['' => '@']));
}