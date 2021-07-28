<?php
namespace Core;

use \Core\Services\AuthenticationService as Authentication;
use \Core\Services\NotificationService as Notifications;
use \Core\Services\SessionService as Session;
use \Core\Services\FilterService as Filter;

/**
 * Base controller
 *
 * PHP version 7.0
 */
abstract class Controller
{

	public $post;
	public $get;
	public $put;
	public $delete;

    /**
     * Parameters from the matched route
     * @var array
     */
    protected $route_params = [];
	
    /**
     * Parameters for the title and description of this page
     * @var array
     */	
	public $page_data = ["title" => "", "description" => "", "nav" => []];
	
    /**
     * Notifications for nav bar
     * @var array
     */		
	public $notifications = [];

    /**
     * Page requires authentication
     * @var bool
     */
	protected $authentication = false;

    /**
     * Class constructor
     *
     * @param array $route_params  Parameters from the route
     *
     * @return void
     */
    public function __construct($route_params)
    {
        $this->route_params = $route_params;
		$this->notifications = array('notifications' => Notifications::fetch());
		$this->inputData();
		$this->navigation();
    }
	
	public function navigation(){
		
		$this->page_data['nav'] = [
			'sales' => ['title' => 'Sales', 'link' => '#', 'subitems' => [
				['type' => 'item', 'title' => 'New Sale', 'link' => '/sale/new'],
				['type' => 'item', 'title' => 'All Sales', 'link' => '/sale/list?orderby=id&&order=desc'],
				['type' => 'divider'],				
				['type' => 'item', 'title' => 'Pending Payment', 'link' => '/sale/list?orderby=id&order=desc&search=%22Un-Paid%22'],
				['type' => 'item', 'title' => 'Pending Dispatch', 'link' => '/sale/list?orderby=id&order=desc&search=%22Paid%22'],
				['type' => 'item', 'title' => 'Highiest Sale Price', 'link' => '/sale/list?orderby=gross_amount&&order=desc'],				
			]],
			'purchases' => ['title' => 'Purchases', 'link' => '#', 'subitems' => [
				['type' => 'item', 'title' => 'New Purchase', 'link' => '/purchase/new'],
				['type' => 'item', 'title' => 'All Purchase', 'link' => '/purchase/list?orderby=id&order=desc'],	
				['type' => 'divider'],				
				['type' => 'item', 'title' => 'For Sale', 'link' => '/purchase/list?orderby=id&order=desc&search=%22for%20sale%22'],
				['type' => 'item', 'title' => 'Sold', 'link' => '/purchase/list?orderby=id&order=desc&search=%22sold%22'],	
				['type' => 'item', 'title' => 'Held', 'link' => '/purchase/list?orderby=id&order=desc&search=%held%22'],	
				['type' => 'item', 'title' => 'Requires Repair', 'link' => '/purchase/list?orderby=id&order=desc&search=%2222requires%20repair%22'],	
				['type' => 'divider'],				
				['type' => 'item', 'title' => 'New Purchase Expense', 'link' => '/expense/new'],	
				['type' => 'item', 'title' => 'All Purchase Expenses', 'link' => '/expense/list?orderby=id&order=desc'],	
				['type' => 'item', 'title' => 'Purchase Categories', 'link' => '/purchaseCategory/list'],	
				['type' => 'item', 'title' => 'Stock List', 'link' => '/stock/list'],					
			]],
			'financials' => ['title' => 'Financials', 'link' => '#', 'subitems' => [
				['type' => 'item', 'title' => 'Accounts', 'link' => '/account/list'],
				['type' => 'item', 'title' => 'Withdrawals', 'link' => '/withdrawal/list'],				
				['type' => 'item', 'title' => 'Transfers', 'link' => '/transfer/list'],				
				['type' => 'item', 'title' => 'Buyouts', 'link' => '/buyout/list'],				
			]],						
			'reporting' => ['title' => 'Reporting', 'link' => '#', 'subitems' => [
				['type' => 'item', 'title' => 'Account Statement', 'link' => '/report/accounts/statement'],
				['type' => 'item', 'title' => 'Stock Statement', 'link' => '/report/stock/statement'],				
				['type' => 'item', 'title' => 'Sales Statement', 'link' => '/report/sales/statement'],				
				['type' => 'item', 'title' => 'Undervalued', 'link' => '/report/purchases/underValued'],				
			]],						
			'configuration' => ['title' => 'Configuration', 'link' => '#', 'subitems' => [
				['type' => 'item', 'title' => 'Payment Vendors', 'link' => '/paymentVendor/list'],
				['type' => 'item', 'title' => 'Sale Vendors', 'link' => '/saleVendor/list'],				
				['type' => 'item', 'title' => 'Purchase Vendors', 'link' => '/purchaseVendor/list'],
				['type' => 'divider'],
				['type' => 'item', 'title' => 'User Management', 'link' => '/user/list'],	
				['type' => 'divider'],				
				['type' => 'item', 'title' => 'Import Purchases', 'link' => '/import/purchase'],
				['type' => 'item', 'title' => 'Update', 'link' => '/update'],
				['type' => 'item', 'title' => 'Settings', 'link' => '/settings/edit'],				
			]],					
			'help' => ['title' => 'Help', 'link' => '#', 'subitems' => [
				['type' => 'item', 'title' => 'Wiki', 'link' => 'https://github.com/louisvarley/RetroSeller/wiki'],			
			]],	

		];
		
		
		$this->page_data['nav'] = Filter::action("nav_menu", $this->page_data['nav']);
		
	}
	
	
	public function authenticationCheck(){
		
		
		if($this->authentication){

			if(!Authentication::loggedIn()){
			
				header('Location: /login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
				die();
			}
		}
		
	}

    /**
     * Magic method called when a non-existent or inaccessible method is
     * called on an object of this class. Used to execute before and after
     * filter methods on action methods. Action methods need to be named
     * with an "Action" suffix, e.g. indexAction, showAction etc.
     *
     * @param string $name  Method name
     * @param array $args Arguments passed to the method
     *
     * @return void
     */
    public function __call($name, $args)
    {
        $method = $name . 'Action';
		
        if (method_exists($this, $method)) {
			
			/* Handle Authentication Requests */
			$this->authenticationCheck();
			
			/* Update Last Activity Time */
			Session::activity();
			
            if ($this->before() !== false) {
                call_user_func_array([$this, $method], $args);
                $this->after();
            }
        } else {
            throw new \Exception("Method $method not found in controller " . get_class($this));
        }
    }

    /**
     * Before filter - called before an action method.
     *
     * @return void
     */
    protected function before()
    {
    }

    /**
     * After filter - called after an action method.
     *
     * @return void
     */
    protected function after()
    {
    }
	
	public function inputData(){
		
		if($this->isPUT()){
			mb_parse_str(file_get_contents("php://input"),$this->put);
		}
		
		if($this->isPOST()){
			$this->post = $_POST;
		}		
		
		$this->files = $_FILES;
		$this->get = $_GET;		
		
	}
	
	public function isGET(){
		if($_SERVER['REQUEST_METHOD'] == 'GET')
			return true;
	}
	
	public function isPUT(){
		if($_SERVER['REQUEST_METHOD'] == 'PUT')
			return true;		
	}
	
	public function isPOST(){
		if($_SERVER['REQUEST_METHOD'] == 'POST')
			return true;		
	}
	
	public function isDELETE(){
		if($_SERVER['REQUEST_METHOD'] == 'DELETE')
			return true;		
	}	

	public function requestMethod(){

		return $_SERVER['REQUEST_METHOD'];
	}

	public function render($template, $array = null){
		
		
		View::renderTemplate($template, array_merge(
				$this->route_params, 
				$this->page_data,
				$this->notifications,
				empty($array) ? [] : $array)
		);
		
	}

}
