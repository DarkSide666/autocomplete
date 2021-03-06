<?php
namespace darkside666\autocomplete;

// http://book.agiletoolkit.org/addons.html#initiator-class
class Initiator extends \Controller_Addon {
	public $addon_name = 'AutoComplete field for Agile Toolkit';

	function init(){
		parent::init();

        // Route pages of this add-on with following prefix
		//$this->routePages('darkside666_autocomplete');


        // Add add-on locations to pathfinder
        $loc = $this->addLocation([
            'template'  => 'templates',
            'public'    => 'public',
            'js'        => 'public/js',
            'css'       => 'public/css',
        ]);
        // hackish and incorrect way to set baseURL of add-on
        $loc->setBaseURL($this->app->url('/').'../../vendor/' . __NAMESPACE__);
    }
}
