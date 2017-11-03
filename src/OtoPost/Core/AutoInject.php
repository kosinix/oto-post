<?php

namespace OtoPost\Core;

/**
 * Class that auto injects the service locator object
 */
class AutoInject {

    protected $plugin;

	public function inject( $plugin ){
        $this->plugin = $plugin;
    }
}