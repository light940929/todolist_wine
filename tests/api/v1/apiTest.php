<?php

require __DIR__.'/vendor/autoload.php';
use GuzzleHttp\Client;
use GuzzleHttp\Post\PostFile;
use GuzzleHttp\Exception\RequestException;


class ApiTest extends PHPUnit_Framework_TestCase
{
	private $apiKey = Null;
	private $userId = Null;
	private $categoryId = Null;
	private $listId = Null;
	

	function __construct(){
	
		$this->apiKey;
		$this->userId;
		$this->categoryId;
		$this->listId;
	}
	
	public function generateRandomString($length = 6) {
		
		return substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
	}
	
	
	/**
	 * Tests register status is green OK
	 */
	public function testRegisterStatus()
	{
		
		echo "\n ===testRegisterStatus===";
		//$client = new GuzzleHttp\Client();
		$client = new \GuzzleHttp\Client(['defaults' => ['verify' => false]]);
		$request = $client->createRequest('POST', 'https://localhost/todolist_wine/api/v1/register');
		$postBody = $request->getBody();
		 
		// $postBody is an instance of GuzzleHttp\Post\PostBodyInterface
	
		$postBody->setField('name', $this->generateRandomString());
		$postBody->setField('email', $this->generateRandomString().'@gmail.com');
		$postBody->setField('password', '123456');
		
		echo json_encode($postBody->getFields());
	 
		// Send the POST request	 
		$response = $client->send($request);
		//echo $response->getStatusCode();	 
		//echo $response->getEffectiveUrl();
	 
		$this->assertEquals($response->getStatusCode(), 201);
		echo $response->getBody();	
	}
	
    
    
    /**
    * Tests login status is green OK
    */ 
    
	public function testLoginStatus()
    {
    	echo "\n ===testLoginStatus===";
    	//$client = new GuzzleHttp\Client();
    	$client = new \GuzzleHttp\Client(['defaults' => ['verify' => false]]); 
    	$request = $client->createRequest('POST', 'https://localhost/todolist_wine/api/v1/login');
    	$postBody = $request->getBody();
    	
    	// $postBody is an instance of GuzzleHttp\Post\PostBodyInterface
    	$postBody->setField('email', 'amanda@gmail.com');
    	$postBody->setField('password', '123456');
    	
    	//echo $postBody->getField('email');
    	//echo $postBody->getField('password');  
    		
    	echo json_encode($postBody->getFields());
    	//{"email":"light@gmail.com","password":"123456"}
    	
    	// Send the POST request   	
    	$response = $client->send($request);
    	//echo $response->getStatusCode();
    	//echo $response->getEffectiveUrl();
  	
    	$this->assertEquals($response->getStatusCode(), 201);
    	echo $jsonRes = $response->getBody(); 	
    	$obj = json_decode($jsonRes);
    	$this->apiKey = $obj->apiKey;
    	$this->userId = $obj->id;
    }
    
    
  
    /**
     * Tests get all categories is green OK
     */
	 public function testAllCategoriesStatus()
	 {
	 	$this->testLoginStatus();	
	 	echo "\n ===testAllCategoriesStatus===";
	    //$client = new GuzzleHttp\Client();
	    $client = new \GuzzleHttp\Client(['defaults' => ['verify' => false]]);
	    $request = $client->createRequest('GET', 'https://localhost/todolist_wine/api/v1/categories');
	    // Set a single value for a header
	    //$request->setHeader('Authorization', '6545da6ee30d714124324f90d7c80960');
	    $request->setHeader('Authorization', $this->apiKey);
	    //echo "Authorization"+$request->getHeader('Authorization');
	        
	    $response = $client->send($request);
	    //echo $response->getStatusCode(); 
	    //echo $response->getEffectiveUrl();
        
	    $this->assertEquals($response->getStatusCode(), 200);	    
	    echo $jsonRes = $response->getBody();
	    $rest = $jsonRes->__toString();
        $array = json_decode($rest, true);    
	    $this->categoryId = $array['categories'][0]['id'];	    	
	  }
     
 
    
    /**
     * Tests get a category is green OK
     */  
    public function testGetCategoryStatus()
    {
    	$this->testAllCategoriesStatus(); 	
        echo "\n ===testGetCategoryStatus==="; 
    	//$client = new GuzzleHttp\Client();
    	$client = new \GuzzleHttp\Client(['defaults' => ['verify' => false]]);
    	$request = $client->createRequest('GET','https://localhost/todolist_wine/api/v1/categories/'.$this->categoryId );
    	// Set a single value for a header
    	$request->setHeader('Authorization', $this->apiKey);
    	//echo $request->getHeader('Authorization');
	 
    	$response = $client->send($request);
    	//echo $response->getStatusCode();
    	//echo $response->getEffectiveUrl();

    	$this->assertEquals($response->getStatusCode(), 200);
    	echo $jsonRes = $response->getBody();
    	$obj = json_decode($jsonRes);	
    	$this->categoryId = $obj->id;
    }
    
    
   
	
	/**
	 * Tests post a category is green OK
	 */

	 public function testPostCategoryStatus()
	 {
	 	
	 	$this->testLoginStatus();	 	
	 	echo "\n ===testPostCategoryStatus===";
	 	//$client = new GuzzleHttp\Client();
	 	$client = new \GuzzleHttp\Client(['defaults' => ['verify' => false]]);
	 	$request = $client->createRequest('POST', 'https://localhost/todolist_wine/api/v1/categories');
	 	// Set a single value for a header
	 	$request->setHeader('Authorization', $this->apiKey);
	 	//echo $request->getHeader('Authorization');
	 
	 	$postBody = $request->getBody();
	  
	 	// $postBody is an instance of GuzzleHttp\Post\PostBodyInterface
	 	$postBody->setField('category', 'phpUnitestPost');
	 	echo json_encode($postBody->getFields());
	 	$response = $client->send($request);
	 	//echo $response->getStatusCode();
	 	//echo $response->getEffectiveUrl();
	
	 	$this->assertEquals($response->getStatusCode(), 201);
	 	echo $jsonRes = $response->getBody();
    	$obj = json_decode($jsonRes);
    	$this->categoryId = $obj->category_id;
	 }
	 
	 
	
	/**
	 * Tests put a category is green OK
	 */
	 public function testPutCategoryStatus()
	 {
	 	
	 	$this->testPostCategoryStatus(); 	
	 	echo "\n ===testPutCategoryStatus===";
	 	//$client = new GuzzleHttp\Client();
	 	$client = new \GuzzleHttp\Client(['defaults' => ['verify' => false]]);
	 	$request = $client->createRequest('PUT', 'https://localhost/todolist_wine/api/v1/categories/'.$this->categoryId, ['json' => ['name' => 'phpUnitestPut']]);

	 	// Set a single value for a header
	 	$request->setHeader('Authorization', $this->apiKey);
	 	//echo $request->getHeader('Authorization');

	 	$response = $client->send($request);
	 	//echo $response->getStatusCode();
	 	//echo $response->getEffectiveUrl();
	 	
	 	$this->assertEquals($response->getStatusCode(), 200);
	 	echo $response->getBody();
	 }
	
	 
	
	/**
	 * Tests delete a category is green OK
	 */
	 public function testDelCategoryStatus()
	 {
	 	$this->testPostCategoryStatus();	 	
	 	echo "\n ===testDelCategoryStatus===";
	 	//$client = new GuzzleHttp\Client();
	 	$client = new \GuzzleHttp\Client(['defaults' => ['verify' => false]]);	
	 	$request = $client->createRequest('DELETE', 'https://localhost/todolist_wine/api/v1/categories/'.$this->categoryId);
	 	// Set a single value for a header
	 	$request->setHeader('Authorization', $this->apiKey);
	 	//echo $request->getHeader('Authorization');
	
	 	$response = $client->send($request);
	 	//echo $response->getStatusCode();
	 	//echo $response->getEffectiveUrl();

	 	$this->assertEquals($response->getStatusCode(), 200);
	 	echo $response->getBody();
	 }
	 
	 
	
	/**
	 * Tests get all lists is green OK
	 */
	 public function testAllListsStatus()
	 {
	 	$this->testGetCategoryStatus(); 	
	 	echo "\n ===testAllListsStatus===";
	 	//$client = new GuzzleHttp\Client();
	 	$client = new \GuzzleHttp\Client(['defaults' => ['verify' => false]]);
	
	 	$request = $client->createRequest('GET', 'https://localhost/todolist_wine/api/v1/categories/'.$this->categoryId.'/lists');
	 	// Set a single value for a header
	 	$request->setHeader('Authorization', $this->apiKey);
	 	//echo $request->getHeader('Authorization');

	 	$response = $client->send($request);
	 	//echo $response->getStatusCode();
	 	//echo $response->getEffectiveUrl();
	 	
	 	$this->assertEquals($response->getStatusCode(), 200);
	 	echo $jsonRes = $response->getBody();
	    $rest = $jsonRes->__toString();
        $array = json_decode($rest, true);	    
	    echo $this->listId = $array['lists'][0]['id'];	
	 }
	
	
	/**
	 * Tests get a list is green OK
	 */
	 public function testGetListStatus()
	 {
	 	$this->testAllListsStatus(); 	
	 	echo "\n ===testGetListStatus===";
	 	//$client = new GuzzleHttp\Client();
	 	$client = new \GuzzleHttp\Client(['defaults' => ['verify' => false]]);
	 	$request = $client->createRequest('GET', 'https://localhost/todolist_wine/api/v1/categories/'.$this->categoryId.'/lists/'.$this->listId);
	 	// Set a single value for a header
	 	$request->setHeader('Authorization', $this->apiKey);
	 	//echo $request->getHeader('Authorization');
	 	$response = $client->send($request);
	 	//echo $response->getStatusCode();
	 	//echo $response->getEffectiveUrl();

	 	$this->assertEquals($response->getStatusCode(), 200);
	 	echo $jsonRes = $response->getBody();	
    	$obj = json_decode($jsonRes);
    	$this->listId = $obj->id;
	 }

	 
	
	/**
	 * Tests post a list is green OK
	 */
	 public function testPostListStatus()
	 {
	 	$this->testAllListsStatus();	
	 	echo "\n ===testPostListStatus===";
	 	//$client = new GuzzleHttp\Client();
	 	$client = new \GuzzleHttp\Client(['defaults' => ['verify' => false]]);
	 	$request = $client->createRequest('POST', 'https://localhost/todolist_wine/api/v1/categories/'.$this->categoryId.'/lists');
	 	// Set a single value for a header
	 	$request->setHeader('Authorization', $this->apiKey);
	 	//echo $request->getHeader('Authorization');

	 	$postBody = $request->getBody();
  
	 	// $postBody is an instance of GuzzleHttp\Post\PostBodyInterface
	 	$postBody->setField('list', 'phpUnitestPost');
	 	echo json_encode($postBody->getFields());
	 	$response = $client->send($request);
	 	//echo $response->getStatusCode();
	 	//echo $response->getEffectiveUrl();

	 	$this->assertEquals($response->getStatusCode(), 201);
	 	echo $jsonRes = $response->getBody();
    	$obj = json_decode($jsonRes);
    	$this->listId = $obj->list_id;
	 }
	
	
	
	/**
	 * Tests put a list is green OK
	 */
	 public function testPutListStatus()
	 { 
	 	$this->testPostListStatus();	 	
	 	echo "\n ===testPutListStatus===";
	 	//$client = new GuzzleHttp\Client();
	 	$client = new \GuzzleHttp\Client(['defaults' => ['verify' => false]]);
	 	$request = $client->createRequest('PUT', 'https://localhost/todolist_wine/api/v1/categories/'.$this->categoryId.'/lists/'.$this->listId, ['json' => ['name' => 'phpUnitestPut', 'status' => '0']]);
	 	// Set a single value for a header
	 	$request->setHeader('Authorization', $this->apiKey);
	 	//echo $request->getHeader('Authorization');
	 	
	 	$response = $client->send($request);
	 	//echo $response->getStatusCode();
	 	//echo $response->getEffectiveUrl();
	 	$this->assertEquals($response->getStatusCode(), 200);
	 	echo $response->getBody();
	 }
	
	 
	
	/**
	 * Tests delete a list is green OK
	 */
	 public function testDelListStatus()
	 {
	 	$this->testPostListStatus(); 	
	 	echo "\n ===testDelCategoryStatus===";
		//$client = new GuzzleHttp\Client();
	 	$client = new \GuzzleHttp\Client(['defaults' => ['verify' => false]]);
	 	$request = $client->createRequest('DELETE', 'https://localhost/todolist_wine/api/v1/categories/'.$this->categoryId.'/lists/'.$this->listId);
	 	// Set a single value for a header
	 	$request->setHeader('Authorization', $this->apiKey);
	 	//echo $request->getHeader('Authorization');

	 	$response = $client->send($request);
	 	//echo $response->getStatusCode();
	 	//echo $response->getEffectiveUrl();
	 	$this->assertEquals($response->getStatusCode(), 200);
	 	echo $response->getBody();
	 }
	 
	 
	
	/**
	 * Tests logout status is green OK
	 */
	 public function testLogoutStatus()
	 {
	 	$this->testLoginStatus();	
	 	echo "\n ===testLogoutStatus===";
	 	//$client = new GuzzleHttp\Client();
	 	$client = new \GuzzleHttp\Client(['defaults' => ['verify' => false]]);
	 	$request = $client->createRequest('DELETE', 'https://localhost/todolist_wine/api/v1/login/'.$this->userId);
	 	// Set a single value for a header
	 	$request->setHeader('Authorization', $this->apiKey);
	 	//echo $request->getHeader('Authorization');

		$response = $client->send($request);
	 	//echo $response->getStatusCode();
	 	//echo $response->getEffectiveUrl();

	 	$this->assertEquals($response->getStatusCode(), 200);
	 	echo $response->getBody();
	 }

}

//$A=new ApiTest("","","","");





