<?php
session_start();

include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Library.utility.php');

// Include files
Library::using(Library::CORLY_SERVICE_PLUGIN);

/**
 * PluginController short summary.
 *
 * PluginController description.
 *
 * @version 1.0
 * @author Filip
 */
class PluginController
{
    // Method constants
    const QUERY = "QUERY";
    const GET = "GET";
    
    // Controller service
    private $PluginService;
    
    /**
     * Plugin controller constructor
     */
    public function __construct()  {
        $this->PluginService = new PluginService();
    }
    
    /**
     * Get detail of given plugin
     * @param mixed $pluginId 
     * @return plugin with detail
     */
    public function Get($pluginId)  {
        // Create new plugin entity
        $plugin = new stdClass();
        $plugin->Id = $pluginId;
        
        // Return result
        return $this->PluginService->GetDetail($plugin);
    }
    
    /**
     * Get list of all plugins
     * @return list of plugins
     */
    public function Query() {
        return $this->PluginService->GetList();
    }
}

// Extract json data
$rawData = file_get_contents('php://input');
$data = json_decode($rawData);

// Check GET requests
if (isset($_GET["method"]))	{
	// Init result
	$result = new stdClass();
	$PluginController = new PluginController();

	switch ($_GET["method"]) {
		case PluginController::GET:
			$result = $PluginController->Get($data);
			break;

		case PluginController::QUERY:
			$result = $PluginController->Query();
			break;

		default:
			$result = false;
			break;
	}

	// Return answer to client
	exit(json_encode($result));
}