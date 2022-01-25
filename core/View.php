<?php

namespace Core;


use Twig\Extra\Intl\IntlExtension;
use \Core\Services\ToastService as Toast;
use \Core\Services\PluginService as Plugins;

/**
 * View
 *
 * PHP version 7.0
 */
class View
{

	private static $templateDirs = [];

    /**
     * Render a view file
     *
     * @param string $view  The view file
     * @param array $args  Associative array of data to display in the view (optional)
     *
     * @return void
     */
    public static function render($view, $args = [])
    {
        extract($args, EXTR_SKIP);

        $file = dirname(__DIR__) . "/app/Views/$view";  // relative to Core directory

        if (is_readable($file)) {
            require $file;
        } else {
            throw new \Exception("$file not found");
        }
    }

    /**
     * Render a view template using Twig
     *
     * @param string $template  The template file
     * @param array $args  Associative array of data to display in the view (optional)
     *
     * @return void
     */
    public static function renderTemplate($template, $args = [])
    {
        static $twig = null;
		
		self::$templateDirs[] = DIR_VIEWS;
		
		/* Include all plugins as template dirs */
		foreach(Plugins::list() as $plugin){
			self::$templateDirs[] = $plugin->directory . '/' . 'Views';
		}		
		
		/* Search every template directory for a potential location */
		foreach(self::$templateDirs as $templateDir){
			
			/* Searches and finds the template case in-sensitive */
			$di = new \RecursiveDirectoryIterator($templateDir);
			foreach (new \RecursiveIteratorIterator($di) as $filename => $file) {
				$f = str_replace("\\","/", $filename);

				if (strpos(strtolower($f), strtolower($template)) !== false) {
					$template = ltrim(str_replace(DIR_VIEWS, "", $filename),"\\");
					continue;
				}
				
			}
		
		}
		
		/* String if contains root */
		if (strpos(strtolower($template), strtolower(DIR_ROOT)) !== false) {
			$template = explode("Views", $template)[1];
		}

        if ($twig === null) {
            $loader = new \Twig\Loader\FilesystemLoader(self::$templateDirs);
			$twig = new \Twig\Environment($loader, [
				'debug' => true,
			]);
			$twig->addExtension(new IntlExtension());
			$twig->addExtension(new \Twig\Extension\DebugExtension());
        }
		
		$twig->addGlobal('session', $_SESSION);
		
		Toast::throwSuccess("hello",time());
		
		/* Move these filters to their own class file */
		$twig->addFilter( new \Twig\TwigFilter('to_array', function ($stdClassObject) {
			$response = array();
			foreach ((array) $stdClassObject as $key => $value) {
				$response[] = array($key, $value);
			}
			
			return $response;
		}));
		
		$twig = \Core\TwigFilters::Apply($twig);

        echo $twig->render($template, $args);

		/* We can clear any toasts now we have rendered */
		Toast::clear();

    }
	
    /**
     * Redirect to another page
     *
     * @param string $url URL to redirect
     * @param array $id ID to pass to the page (optional)
     *
     * @return void
     */
    public static function redirect($url, $id = [])
    {
        header("Location: $url" . ($id ? "//$id/" : null));
		die();
    }	
}
