<?php

namespace Core;


use Twig\Extra\Intl\IntlExtension;

/**
 * View
 *
 * PHP version 7.0
 */
class View
{

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
		
		/* Searches and finds the template case in-sensitive */
		$di = new \RecursiveDirectoryIterator(DIR_VIEWS);
		foreach (new \RecursiveIteratorIterator($di) as $filename => $file) {
			$f = str_replace("\\","/", $filename);
			if (strpos(strtolower($f), strtolower($template)) !== false) {
				$template = ltrim(str_replace(DIR_VIEWS, "", $filename),"\\");
				continue;
			}
		}

        if ($twig === null) {
            $loader = new \Twig\Loader\FilesystemLoader(DIR_VIEWS);
			$twig = new \Twig\Environment($loader, [
				'debug' => true,
			]);
			$twig->addExtension(new IntlExtension());
			$twig->addExtension(new \Twig\Extension\DebugExtension());
        }
		
		$twig->addGlobal('session', $_SESSION);

		/* Move these filters to their own class file */

		$twig->addFilter( new \Twig\TwigFilter('to_array', function ($stdClassObject) {
			$response = array();
			foreach ((array) $stdClassObject as $key => $value) {
				$response[] = array($key, $value);
			}
			
			return $response;
		}));
		
		$twig->addFilter( new \Twig\TwigFilter('rag', function ($ogFloat) {
			
			$num = floatval($ogFloat);
			
			if($num < 0){
				return "rag-red";
			}elseif($num ==0){
				return "rag-orange";
			}else{
				return "rag-green";
			}
			
		}));	

		$twig->addFilter( new \Twig\TwigFilter('as_thumbnail', function ($image) {
			return '<div class="image-thumbnail"><img src="' . $image . '"></div>';
		}));
			



        echo $twig->render($template, $args);

		/* We can clear any toasts now we have rendered */
		toastManager()->clear();
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
