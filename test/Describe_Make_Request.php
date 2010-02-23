<?php
require_once(dirname(__FILE__).'/test_helper.php');
/**
 * Spec for WebRequest
 *
 * This will test the web_request method that we use for unit testing
 *
 * @package default
 * @author Christopher Cowan
 **/
class Describe_Make_Request extends PHPUnit_Framework_TestCase 
{
    /**
     * The URL for the test
     *
     * @var string
     **/
    private $test_url = 'http://localhost:8001/echo';
    
    /**
     * It should return an array with the response and header
     *
     * @return void
     * @author Christopher Cowan
     * @test
     **/
    public function It_should_return_an_array_with_the_response_and_header()
    {
        $response = make_request($this->test_url, 'GET');
        $this->assertArrayHasKey( 'body', $response );
        $this->assertArrayHasKey( 'headers', $response );
        $this->assertArrayHasKey( 'status_code', $response );
        $this->assertEquals( $response['status_code'], 200);
    } // END function It_should_return_an_array_with_the_response_and_header
    
    /**
     * It should return a numeric status code
     *
     * @return void
     * @author Christopher Cowan
     * @test
     **/
    public function It_should_return_a_numeric_status_code()
    {
        $response = make_request($this->test_url, 'GET');
        $this->assertTrue(is_numeric($response['status_code']));
        $this->assertEquals( $response['status_code'], 200);
    } // END function It_should_return_a_numeric_status_code
    
    /**
     * It should return an array for the headers
     *
     * @return void
     * @author Christopher Cowan
     * @test
     **/
    public function It_should_return_an_array_for_the_headers()
    {
        $response = make_request($this->test_url, 'GET');
        $this->assertTrue(is_array($response['headers']));
        $this->assertEquals( $response['status_code'], 200);
    } // END function It_should_return_an_array_for_the_headers
    
    /**
     * It should send post requests with post fields
     *
     * @return void
     * @author Christopher Cowan
     * @test
     **/
    public function It_should_send_post_requests_with_post_fields()
    {
        $post_fields = array('foo'=>'bar');
        $response = make_request($this->test_url, 'POST', array('post_fields'=>$post_fields));
        $data = json_decode($response['body'], true);
        $this->assertEquals( $response['status_code'], 200);
        $this->assertEquals( $post_fields, $data['_POST']);
        
    } // END function It_should_send_post_requests_with_post_fields
    
    /**
     * It should send a delete request
     *
     * @return void
     * @author Christopher Cowan
     * @test
     **/
    public function It_should_send_a_delete_request()
    {
        $response = make_request($this->test_url, 'DELETE');
        $data = json_decode($response['body'], true);
        $this->assertEquals( $response['status_code'], 200);
        $this->assertEquals( 'DELETE', $data['_SERVER']['REQUEST_METHOD']);
        
    } // END function It_should_send_a_delete_request
    
    /**
     * It should send a put request
     *
     * @return void
     * @author Christopher Cowan
     * @test
     **/
    public function It_should_send_a_put_request()
    {
        $response = make_request($this->test_url, 'PUT', array('headers'=>array('Content-Length'=>0)));
        $this->assertEquals( $response['status_code'], 200);
        $data = json_decode($response['body'], true);
        $this->assertEquals('PUT', $data['_SERVER']['REQUEST_METHOD']);
    } // END function It_should_send_a_put_request
    
    
    /**
     * It should send custom headers
     *
     * @return void
     * @author Christopher Cowan
     * @test
     **/
    public function It_should_send_custom_headers()
    {
        $response = make_request($this->test_url, 'GET', array('headers'=>array('X-Foo'=>'Bar')));
        $this->assertEquals( $response['status_code'], 200);
        $data = json_decode($response['body'],true);
        $this->assertEquals( 'Bar', $data['_HEADERS']['X-Foo']);
        
    } // END function It_should_send_custom_headers
    
    /**
     * It should set a cookie
     *
     * @return void
     * @author Christopher Cowan
     * @test
     **/
    public function It_should_set_a_cookie()
    {
        $response = make_request($this->test_url, 'GET', array('cookies'=>array('foo'=>'"bar"')));
        $this->assertEquals( $response['status_code'], 200 );
        $data = json_decode($response['body'], true);
        $this->assertEquals( $data['_COOKIE']['foo'], '"bar"' );
    } // END function It_should_set_a_cookie
    
    /**
     * It should have cookies in the response
     *
     * @return void
     * @author Christopher Cowan
     * @test
     **/
    public function It_should_have_cookies_in_the_response()
    {
        $response = make_request($this->test_url, 'GET');
        $this->assertArrayHasKey( 'cookies', $response );
        $this->assertEquals( 'test', $response['cookies']['example'] );
    } // END function It_should_have_cookies_in_the_response
    
    
    /**
     * It should have more then 1 item in the headers set cookie array
     *
     * @return void
     * @author Christopher Cowan
     * @test
     **/
    public function It_should_have_more_then_1_item_in_the_headers_set_cookie_array()
    {
        $response = make_request($this->test_url, 'GET');
        $this->assertGreaterThan( 1, count($response['headers']['Set-Cookie']));
        $this->assertGreaterThan( 1, count($response['cookies']) );
    } // END function It_should_have_more_then_1_item_in_the_headers_set_cookie_array
    
} // END class Describe_WebRequest 