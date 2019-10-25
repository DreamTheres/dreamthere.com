<?php

class MY_Loader extends CI_Loader {

    /**
     * List of loaded services
     *
     * @var	array
     */
    protected $_ci_services = array();

    /**
     * List of paths to load services from
     *
     * @var	array
     */
    protected $_ci_service_paths = array(APPPATH);

    public function __construct() {
        parent::__construct();
    }

    /**
     * Add Package Path
     *
     * Prepends a parent path to the library, model, service, helper and config
     * path arrays.
     *
     * @see	CI_Loader::$_ci_library_paths
     * @see	CI_Loader::$_ci_model_paths
     * @see	CI_Loader::$_ci_service_paths
     * @see CI_Loader::$_ci_helper_paths
     * @see CI_Config::$_config_paths
     *
     * @param	string	$path		Path to add
     * @param 	bool	$view_cascade	(default: TRUE)
     * @return	object
     */
    public function add_package_path($path, $view_cascade = TRUE) {
        $path = rtrim($path, '/') . '/';

        array_unshift($this->_ci_library_paths, $path);
        array_unshift($this->_ci_model_paths, $path);
        array_unshift($this->_ci_service_paths, $path);
        array_unshift($this->_ci_helper_paths, $path);

        $this->_ci_view_paths = array($path . 'views/' => $view_cascade) + $this->_ci_view_paths;

        // Add config file path
        $config = & $this->_ci_get_component('config');
        $config->_config_paths[] = $path;

        return $this;
    }

    /**
     * Remove Package Path
     *
     * Remove a path from the library, model, helper and/or config
     * path arrays if it exists. If no path is provided, the most recently
     * added path will be removed removed.
     *
     * @param	string	$path	Path to remove
     * @return	object
     */
    public function remove_package_path($path = '') {
        $config = & $this->_ci_get_component('config');

        if ($path === '') {
            array_shift($this->_ci_library_paths);
            array_shift($this->_ci_model_paths);
            array_shift($this->_ci_serivce_paths);
            array_shift($this->_ci_helper_paths);
            array_shift($this->_ci_view_paths);
            array_pop($config->_config_paths);
        } else {
            $path = rtrim($path, '/') . '/';
            foreach (array('_ci_library_paths', '_ci_model_paths', '_ci_service_paths', '_ci_helper_paths') as $var) {
                if (($key = array_search($path, $this->{$var})) !== FALSE) {
                    unset($this->{$var}[$key]);
                }
            }

            if (isset($this->_ci_view_paths[$path . 'views/'])) {
                unset($this->_ci_view_paths[$path . 'views/']);
            }

            if (($key = array_search($path, $config->_config_paths)) !== FALSE) {
                unset($config->_config_paths[$key]);
            }
        }

        // make sure the application default paths are still in the array
        $this->_ci_library_paths = array_unique(array_merge($this->_ci_library_paths, array(APPPATH, BASEPATH, CUSTOMPATH)));
        $this->_ci_helper_paths = array_unique(array_merge($this->_ci_helper_paths, array(APPPATH, BASEPATH, CUSTOMPATH)));
        $this->_ci_model_paths = array_unique(array_merge($this->_ci_model_paths, array(APPPATH, CUSTOMPATH)));
        $this->_ci_service_paths = array_unique(array_merge($this->_ci_service_paths, array(APPPATH, CUSTOMPATH)));
        $this->_ci_view_paths = array_merge($this->_ci_view_paths, array(APPPATH . 'views/' => TRUE));
        $config->_config_paths = array_unique(array_merge($config->_config_paths, array(APPPATH, CUSTOMPATH)));

        return $this;
    }

    /**
     * Service Loader
     *
     * Loads and instantiates services.
     *
     * @param	string	$service		Service name
     * @param	string	$name		An optional object name to assign to
     * @param	bool	$db_conn	An optional database connection configuration to initialize
     * @return	object
     */
    public function service($service, $name = '', $db_conn = FALSE) {
        if (empty($service)) {
            return $this;
        } elseif (is_array($service)) {
            foreach ($service as $key => $value) {
                is_int($key) ? $this->service($value, '', $db_conn) : $this->service($key, $value, $db_conn);
            }

            return $this;
        }

        $path = '';

        // Is the service in a sub-folder? If so, parse out the filename and path.
        if (($last_slash = strrpos($service, '/')) !== FALSE) {
            // The path is in front of the last slash
            $path = substr($service, 0, ++$last_slash);

            // And the service name behind it
            $service = substr($service, $last_slash);
        }

        if (empty($name)) {
            $name = $service;
        }

        if (in_array($name, $this->_ci_services, TRUE)) {
            return $this;
        }

        $CI = & get_instance();
        if (isset($CI->$name)) {
            throw new RuntimeException('The service name you are loading is the name of a resource that is already being used: ' . $name);
        }

        if ($db_conn !== FALSE && !class_exists('CI_DB', FALSE)) {
            if ($db_conn === TRUE) {
                $db_conn = '';
            }

            $this->database($db_conn, FALSE, TRUE);
        }

        // Note: All of the code under this condition used to be just:
        //
		//       load_class('Service', 'core');
        //
		//       However, load_class() instantiates classes
        //       to cache them for later use and that prevents
        //       MY_Service from being an abstract class and is
        //       sub-optimal otherwise anyway.
        if (!class_exists('CI_Service', FALSE)) {
            $app_path = APPPATH . 'core' . DIRECTORY_SEPARATOR;
            if (file_exists($app_path . 'Service.php')) {
                require_once($app_path . 'Service.php');
                if (!class_exists('CI_Service', FALSE)) {
                    throw new RuntimeException($app_path . "Service.php exists, but doesn't declare class CI_Service");
                }
            } elseif (!class_exists('CI_Service', FALSE)) {
                require_once(BASEPATH . 'core' . DIRECTORY_SEPARATOR . 'Service.php');
            }

            $class = config_item('subclass_prefix') . 'Service';
            if (file_exists($app_path . $class . '.php')) {
                require_once($app_path . $class . '.php');
                if (!class_exists($class, FALSE)) {
                    throw new RuntimeException($app_path . $class . ".php exists, but doesn't declare class " . $class);
                }
            }
        }

        $service = ucfirst($service);
        if (!class_exists($service, FALSE)) {
            foreach ($this->_ci_service_paths as $ser_path) {
                if (!file_exists($ser_path . 'services/' . $path . $service . '.php')) {
                    continue;
                }

                require_once($ser_path . 'services/' . $path . $service . '.php');
                if (!class_exists($service, FALSE)) {
                    throw new RuntimeException($ser_path . "services/" . $path . $service . ".php exists, but doesn't declare class " . $service);
                }

                break;
            }

            if (!class_exists($service, FALSE)) {
                throw new RuntimeException('Unable to locate the service you have specified: ' . $service);
            }
        } elseif (!is_subclass_of($service, 'CI_Service')) {
            throw new RuntimeException("Class " . $service . " already exists and doesn't extend CI_Service");
        }

        $this->_ci_services[] = $name;
        $CI->$name = new $service();
        return $this;
    }

}
