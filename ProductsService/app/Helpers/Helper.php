<?php 
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class Helper
{
	public static function success($msg="success",$data=[],$status_code=200)
	{
		return Helper::response($msg,$data,$status_code);
	}
	
	public static function fail($msg="fail",$data=[],$status_code=400)
	{
		return Helper::response($msg,$data,$status_code);
	}

	public static function response($msg,$data,$status_code)
	{
	    if($data["per_page"]??"")
	    {
	        $respose=[
	            'status'   =>  $status_code,
	            'msg'      =>  app('translator')->get('msg.'.$msg),
	            'data'     =>  $data["data"],
	            'paginator'=>[
	                "total"        =>  $data["total"],
	                "currentPage"  =>  $data["current_page"],
	                "perPage"      =>  $data["per_page"],
	                "pages"        =>  ceil($data["total"]/$data["per_page"])-1,
	                "first_page_url"=>"https://domain.com?page=1"
	            ]
	        ];
	    }
	    else
	    {
	        $respose=[
	            'status'   =>  $status_code,
	            'msg'      =>  app('translator')->get('msg.'.$msg),
	            'data'     =>  $data,
	        ];
	    }
	    return response()->json($respose,$status_code);
	}

	/**
	 * @param string $url
	 * @param string $urlApi
	 * @param string $user
	 * @param string $pass
	 * @param array $headers
	 * @param boolean $debug
	 * @param array $data
	 * @param string $method
	 * @param string $accept
	 * @return mixed|array
	 */
	public static function callAPI($url, $urlApi, $user, $pass, $headers, $debug = false, $data=[], $method='GET', $accept='json') {
	    $pos = strpos($url,'https://');
	    if ($pos === false){
	        //$url = 'https://' . $url;
	    }
	    
	    $curl = curl_init($url.$urlApi);
	    
	    if(isset($user) && !empty($user)){
	        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	        $userpwd = $user . ":" . $pass;
	        curl_setopt($curl, CURLOPT_USERPWD, $userpwd);
	    }
	    
	    $request_headers = array();
	    $request_headers[] = 'Accept: '. 'application/json';
	    if(isset($headers) && !empty($headers)){
	        foreach($headers as $key=>$value){
	            $request_headers[] = "$key: $value";
	        }
	    }
	    
	    switch ($method) {
	        case "GET":
	            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
	            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
	            break;
	        case "POST":
	            if(isset($data) && !empty($data)){
	                curl_setopt($curl, CURLOPT_POST, true);
	                if($accept != 'json'){
	                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	                }else{
	                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
	                }
	            }else{
	                curl_setopt($curl, CURLOPT_POSTFIELDS, '');
	            }
	            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
	            break;
	        case "PUT":
	            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
	            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
	            break;
	        case "DELETE":
	            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
	            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
	            break;
	    }
	    
	    // 	    if(!isset($method) || empty($method)){
	    // 		    if(isset($data) && !empty($data)){
	    // 				curl_setopt($curl, CURLOPT_POST, true);
	    // 				curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	    // 		    }
	    // 		}
	    
	    curl_setopt($curl, CURLOPT_HTTPHEADER, $request_headers);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($curl, CURLOPT_SSLVERSION, 'DEFAULT:!DH');
	    curl_setopt($curl, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT:!DH');
	    
	    
	    $curl_response = curl_exec($curl);
	    
	    if ($debug) {
	        $o = null;
	        $o['response'] 	= $curl_response;
	        $o['info'] 		= curl_getinfo($curl);
	        curl_close($curl);
	        return $o;
	    }
	    
	    curl_close($curl);
	    return $curl_response;
	}
}