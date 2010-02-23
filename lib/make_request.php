<?php
/**
 * Copyright (c) 2010, Christopher Cowan, Plus 3 Network Inc.
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 * 
 *  * Redistributions of source code must retain the above copyright notice, this
 *    list of conditions and the following disclaimer. 
 * 
 *  * Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution. 
 * 
 *  * Neither the name of the Plus 3 Network Inc nor the names of its contributors
 *    may be used to endorse or promote products derived from this software 
 *    without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @author Christopher Cowan
 */
 
/**
 * This will make a web request and then return the response and headers as an associated array.
 *
 * @return array
 * @author Christopher Cowan
 **/
function make_request($url, $method='GET', $options=array())
{
    # instanciate the curl request
    $curl = curl_init($url);
    
    # set the default options
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HEADER, 1);
    
    # set the request method
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    
    # set cookies
    if(isset($options['cookies']) && is_array($options['cookies'])) {
        $cookies = to_params($options['cookies'], ';');
        curl_setopt($curl, CURLOPT_COOKIE, $cookies);
    }
    
    # set custom headers
    if(isset($options['headers']) && is_array($options['headers'])) {
        $headers = array();
        foreach($options['headers'] as $key=>$val) {
            $headers[] = "{$key}: {$val}";
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    }
    
    # post arguments
    if(isset($options['post_fields']) && is_array($options['post_fields'])) {
        curl_setopt($curl, CURLOPT_POSTFIELDS, $options['post_fields']);
    }
    
    # post body
    if(isset($options['post_body'])) {
        curl_setopt($curl, CURLOPT_POSTFIELDS, $options['post_body']);
    }
    
    # get the response
    $response = curl_exec($curl);
    curl_close($curl);
    
    # seperate the header form the body, also handle 2 response codes
    $buffer = preg_split('/\r\n\r\n/', $response);
    if(count($buffer) == 3) {
        $results = array('body'=>$buffer[2]);
        preg_match('/\r\n\r\nHTTP\/\d\.\d\s+(\d+)/', $response, $matches);
        $results['status_code'] = (integer) $matches[1];
    } else {
        $results = array('body'=>$buffer[1]);
        preg_match('/HTTP\/\d\.\d\s+(\d+)/', $response, $matches);
        $results['status_code'] = (integer) $matches[1];
    }
    
    
    # split up headers
    preg_match_all('/([^\r\n:]+):\s+([^\r\n]+)/', $buffer[0], $matches);
    foreach($matches[1] as $key=>$val) {
        if(isset($results['headers'][$val])) {
            if(!is_array($results['headers'][$val])) {
                $results['headers'][$val] = array($results['headers'][$val]);
            } 
            $results['headers'][$val][] = $matches[2][$key];
        } else {
            $results['headers'][$val] = $matches[2][$key];
        }
    }
    
    # parse cookies
    if(isset($results['headers']['Set-Cookie'])) {
        $results['cookies'] = array();
        if(is_array($results['headers']['Set-Cookie'])) {
           $set_cookies = $results['headers']['Set-Cookie']; 
        } else {
            $set_cookies = array($results['headers']['Set-Cookie']);
        }
        
        foreach($set_cookies as $cookie) {
            preg_match('/^([^=]+)=([^;]+)/', $cookie, $matches);
            $results['cookies'][$matches[1]] = $matches[2];
            
        }
    }
    return $results;
} // END function web_request()

/**
 * This will convert a has to params
 *
 * @return string
 * @author Christopher Cowan
 **/
function to_params($array, $sep='&')
{
    $return = '';
    $_sep = '';
    foreach($array as $key=>$val) {
        $return .= $_sep . $key . '=' . urlencode($val); 
        $_sep = $sep;
    }
    return $return;
} // END function to_params()