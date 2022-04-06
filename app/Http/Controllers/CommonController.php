<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Encryption\EncryptionServiceProvider;
use Illuminate\Contracts\Encryption\Encrypter;
use DB;
use App\SellerModel;
use App\seller_kyc;
use App\Models\common;
use Session;
use Mail;
use Hash;
use File;
use Image;
use Auth;
use Storage;
use DateTime;
class CommonController extends Controller {


    public function generateToken(){

            $request_id = time();//rand(12345,99999);
        
           $url =  'http://60.254.21.72:5101/api/TerraFns/GenerateToken?requestId='.$request_id.'';
           $curl = curl_init();
           curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'[    
                    {        
                        "USERID": "AMARKC",
                        "PASSWORD": "AmarKC@123",
                        "DURATION": 1,
                        "DURATION_TYPE" : "D"
                    }
                ]',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
                ));

                $response = curl_exec($curl);
                curl_close($curl);
                $result = json_decode($response);
                $token = $result->DATA[0]->TOKEN;
                return $token;
               
        
    }
 
 
 public function getcategory($token){
        $request_id = time();//rand(123456,999999);
        $url =  'http://60.254.21.72:5101/api/TerraFns/GetCategoryList?requestId='.$request_id.'';
       
     
          $headers = array(
           "Content-Length:0",
           "Authorization: Bearer {$token}",
        );
    
   $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
     CURLOPT_HTTPHEADER =>$headers, 
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
   
     
 }
 function GetSubCategoryList($category,$token){
     
         $request_id = time().rand(123,999);
         $url =  'http://60.254.21.72:5101/api/TerraFns/GetSubCategoryList?requestId='.$request_id.'&category='.$category;
           $headers = array(
               "Content-Length:0",
               "Authorization: Bearer {$token}",
            );
    
       $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
         CURLOPT_HTTPHEADER =>$headers, 
        ));
    
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
     
 }
 
 function store_category(){
     
     $token = $this->generateToken();
     $category_data = $this->getcategory($token);
     $result = json_decode($category_data);
     $StatusCode = $result->OUTPUT[0]->StatusCode;
     
    if($StatusCode == 1){
        foreach ($result->DATA as $key => $object) {
              $catname =  $object->CATEGORYNAME;
              $is_exit = $this->is_category_exit($catname);
                if($is_exit ==false){
                           $data['catname']=$catname;
                           Common::add_details('category',$data);
                       
                }
        }
     
    }
     
 }
 
 function is_category_exit($catname){
     
    if (DB::table('category')->where('catname',$catname)->exists()) {
        return true;
    }else{
        return false;
    }
 }
 
  function is_subcategory_exit($code){
     
    if (DB::table('subcategory')->where('code',$code)->exists()) {
        return true;
    }else{
        return false;
    }
 }


 function getcategoryidbyname($catname){
    $query = DB::table('category')->where('catname',$catname)->select('id');
   
 }
 
 public function storesubcategory(){  
     
    $token = $this->generateToken();
    $category_data = Common::getDetails('category');
    
    foreach($category_data as $row){
         $cat_id = $row->id; 
         $catname = $row->catname;
         $subcategory_data = $this->GetSubCategoryList($catname,$token);
         $result1 = json_decode($subcategory_data);
         foreach ($result1->DATA as $key => $object) {
               $code =  $object->CODE;
               
                     $subdata['name'] =  $object->DESCRIPTION;
                     $subdata['img'] ='default.png';
                     $subdata['cat_id'] =  $cat_id;
                     $subdata['code'] =  $object->CODE;
                     
                $is_subexit = $this->is_subcategory_exit($code);
                if($is_subexit ==false){
                    Common::add_details('subcategory',$subdata);
                } else {
                    $where =array('code'=>$code);
                    Common::update_details('subcategory',$subdata,$where);
                }
             
         }
         //print_r($subdata);
    }
 }

    
    //function getPopulateCountries(){
    function getcurldata($url){

        $token = $this->generateToken();
      
        $headers = array(
               "Content-Length:0",
               "Authorization: Bearer {$token}",
            );
    
       $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
         CURLOPT_HTTPHEADER =>$headers, 
        ));
    
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
        
    }
    function storecountry(){
        
        $url = 'http://60.254.21.72:5101/api/TerraFns/PopulateCountries?requestId='.$request_id;
        $subcategory_data = $this->getcurldata($url);
        $result1 = json_decode($subcategory_data);
        foreach ($result1->DATA as $key => $object) {
             $data['COUNTRYID'] =  $object->COUNTRYID;
             $data['COUNTRYNAME'] =  $object->COUNTRYNAME;
             
             $where = array('COUNTRYID'=>$data['COUNTRYID']);
             $is_subexit = $this->check_exit('country',$where);
             
             if($is_subexit ==false){
                Common::add_details('country',$data);
            } else {
                $where =array('COUNTRYID'=>$data['COUNTRYID']);
                Common::update_details('country',$data,$where);
            }

        }

    }
    
      function storestate(){
          
         $request_id = time().rand(123,999);
         $url = 'http://60.254.21.72:5101/api/TerraFns/PopulateStates?requestId='.$request_id;
        
        $state_data = $this->getcurldata($url);
        $result1 = json_decode($state_data);
        foreach ($result1->DATA as $key => $object) {
            
             $data['STATEID'] =  $object->STATEID;
             $data['STATENAME'] =  $object->STATENAME;
             $data['COUNTRYID'] =  $object->COUNTRYID;
             
             $where = array('STATEID'=>$data['STATEID']);
             $is_subexit = $this->check_exit('state',$where);
             
             if($is_subexit ==false){
                Common::add_details('state',$data);
            } else {
                $where =array('STATEID'=>$data['STATEID']);
                Common::update_details('state',$data,$where);
            }
        }
      }


    function storecity(){
        
        $request_id = time().rand(123,999);
         $url = 'http://60.254.21.72:5101/api/TerraFns/PopulateCities?requestId='.$request_id;
        
        $city_data = $this->getcurldata($url);
        $result1 = json_decode($city_data);
        foreach ($result1->DATA as $key => $object) {
            
             $data['CITYID'] =  $object->CITYID;
             $data['CITYNAME'] =  $object->CITYNAME;
             $data['STATEID'] =  $object->STATEID;
             
             $where = array('CITYID'=>$data['CITYID']);
             $is_subexit = $this->check_exit('cities',$where);
             
             if($is_subexit ==false){
                Common::add_details('cities',$data);
            } else {
                $where =array('CITYID'=>$data['CITYID']);
                Common::update_details('cities',$data,$where);
            }
        }
        
    } 
    function check_exit($table,$where){

        if (DB::table($table)->where($where)->exists()) {
            return true;
        }else{
            return false;
        }
    }
    
    function storePopulateCustomers(){
        
          $request_id = time().rand(123,999);
          $url = 'http://60.254.21.72:5101/api/TerraFns/PopulateCustomers?requestId='.$request_id;
          $cust_data = $this->getcurldata($url);
          $result = json_decode($cust_data);
        //  print_r($result);

          foreach ($result->DATA as $key => $object) {
            
            $BROKERID = $object->BROKERID;
            $TRANSPORTERID =  $object->TRANSPORTERID;
            
            $BROKERID =  ($BROKERID == null) ? "0" : $BROKERID; 
            $TRANSPORTERID =  ($TRANSPORTERID == null) ? "0" : $TRANSPORTERID; 
            
            
            
            $data['CUSTOMERID'] =  $object->CUSTOMERID;
            $data['NAME'] =  $object->NAME;
            $data['BLOCKSTATUS'] =  $object->BLOCKSTATUS;
            $data['BROKERID'] =    $BROKERID;
            $data['TRANSPORTERID'] =  $TRANSPORTERID;
            $data['ADDRESS1'] =  ($object->ADDRESS1 == null) ? "NA" :  $object->ADDRESS1; 
            $data['ADDRESS2'] =  ($object->ADDRESS2 == null) ? "NA" :  $object->ADDRESS2; 
            $data['ADDRESS3'] =  ($object->ADDRESS3 == null) ? "NA" :  $object->ADDRESS3;
            $data['PINCODE'] =  ($object->PINCODE == null) ? "NA" :  $object->PINCODE;
            $data['CITYID'] =  $object->CITYID;
            $data['STATEID'] =  $object->STATEID;
            $data['COUNTRYID'] =  $object->COUNTRYID;
            $data['PHONE'] =  $object->PHONE;
            $data['MOBILE'] =  $object->MOBILE;
            $data['EMAIL'] =  ($object->EMAIL == null) ? "NA" :  $object->EMAIL;
            $data['WEBSITE'] = ($object->WEBSITE == null) ? "NA" :  $object->WEBSITE;
            $data['PAN'] =  ($object->PAN == null) ? "NA" :  $object->PAN; 
            $data['GSTIN'] =  ($object->GSTIN == null) ? "NA" :  $object->GSTIN; 
            $data['DELADDRESS1'] =  ($object->DELADDRESS1 == null) ? "NA" :  $object->DELADDRESS1; 
            $data['DELADDRESS2'] =  ($object->DELADDRESS2 == null) ? "NA" :  $object->DELADDRESS2; 
            $data['DELADDRESS3'] =  ($object->DELADDRESS3 == null) ? "NA" :  $object->DELADDRESS3;  
            $data['DELMOBILE'] =  ($object->DELMOBILE == null) ? "NA" :  $object->DELMOBILE; 
            $data['DELCITYID'] = ($object->DELCITYID == null) ? "0" :  $object->DELCITYID;  
            $data['DELSTATEID'] =  ($object->DELSTATEID == null) ? "0" :  $object->DELSTATEID;  
            $data['DELCOUNTRYID'] = ($object->DELCOUNTRYID == null) ? "0" :  $object->DELCOUNTRYID;  
            $data['DISTANCE'] =   ($object->DISTANCE == null) ? "NA" :  $object->DISTANCE; 

        $data['type'] = '0';

           // print_r($data);
            
            
            $where = array('CUSTOMERID'=>$data['CUSTOMERID']);
            $is_subexit = $this->check_exit('customers',$where);
            
            if($is_subexit ==false){
                Common::add_details('customers',$data);
            } else {
                $where =array('CUSTOMERID'=>$data['CUSTOMERID']);
                Common::update_details('customers',$data,$where);
            }
       }
        
      
        
    }

     function storePopulateBrokers(){
        
          $request_id = time().rand(123,999);
          $url = 'http://60.254.21.72:5101/api/TerraFns/PopulateBrokers?requestId='.$request_id;
          $cust_data = $this->getcurldata($url);
          $result = json_decode($cust_data);
        //  print_r($result);

          foreach ($result->DATA as $key => $object) {
            
            $BROKERID = $object->BROKERID;
            // $TRANSPORTERID =  $object->TRANSPORTERID;
            
            // $BROKERID =  ($BROKERID == null) ? "0" : $BROKERID; 
            // $TRANSPORTERID =  ($TRANSPORTERID == null) ? "0" : $TRANSPORTERID; 
            
            
            
            $data['CUSTOMERID'] =  $object->BROKERID;
            $data['NAME'] =  $object->NAME;
            $data['BLOCKSTATUS'] =  $object->BLOCKSTATUS;
            $data['BROKERID'] =    0;
            $data['TRANSPORTERID'] =  0;
            $data['ADDRESS1'] =  ($object->ADDRESS1 == null) ? "NA" :  $object->ADDRESS1; 
            $data['ADDRESS2'] =  ($object->ADDRESS2 == null) ? "NA" :  $object->ADDRESS2; 
            $data['ADDRESS3'] =  ($object->ADDRESS3 == null) ? "NA" :  $object->ADDRESS3;
            $data['PINCODE'] =  ($object->PINCODE == null) ? "NA" :  $object->PINCODE;
            $data['CITYID'] =  ($object->CITYID == null) ? '0' :  $object->CITYID;;
            $data['STATEID'] =  ($object->STATEID == null) ? '0' :  $object->STATEID;
            $data['COUNTRYID'] =  ($object->COUNTRYID == null) ? '0' :  $object->COUNTRYID;
            $data['PHONE'] =  $object->PHONE;
            $data['MOBILE'] =  $object->MOBILE;
            $data['EMAIL'] =  ($object->EMAIL == null) ? "NA" :  $object->EMAIL;
            $data['WEBSITE'] = ($object->WEBSITE == null) ? "NA" :  $object->WEBSITE;
            $data['PAN'] =  ($object->PAN == null) ? "NA" :  $object->PAN; 
            $data['GSTIN'] =  ($object->GSTIN == null) ? "NA" :  $object->GSTIN; 
            $data['DELADDRESS1'] =  ($object->DELADDRESS1 == null) ? "NA" :  $object->DELADDRESS1; 
            $data['DELADDRESS2'] =  ($object->DELADDRESS2 == null) ? "NA" :  $object->DELADDRESS2; 
            $data['DELADDRESS3'] =  ($object->DELADDRESS3 == null) ? "NA" :  $object->DELADDRESS3;  
            $data['DELMOBILE'] =  ($object->DELMOBILE == null) ? "NA" :  $object->DELMOBILE; 
            $data['DELCITYID'] = ($object->DELCITYID == null) ? "0" :  $object->DELCITYID;  
            $data['DELSTATEID'] =  ($object->DELSTATEID == null) ? "0" :  $object->DELSTATEID;  
            $data['DELCOUNTRYID'] = ($object->DELCOUNTRYID == null) ? "0" :  $object->DELCOUNTRYID;  
            $data['DISTANCE'] =   ($object->DISTANCE == null) ? "NA" :  $object->DISTANCE; 

        $data['type'] = '1';

           // print_r($data);
            
            
            $where = array('CUSTOMERID'=>$data['BROKERID']);
            $is_subexit = $this->check_exit('customers',$where);
            
            if($is_subexit ==false){
                Common::add_details('customers',$data);
            } else {
                $where =array('CUSTOMERID'=>$data['CUSTOMERID']);
                Common::update_details('customers',$data,$where);
            }
       }
        
      
        
    }

    function storePopulateProducts(){
        
          $request_id = time().rand(123,999);
          $url = 'http://60.254.21.72:5101/api/TerraFns/GetProducts?requestId='.$request_id;
          $cust_data = $this->getcurldata($url);
          $result = json_decode($cust_data);
        //  print_r($result);

          foreach ($result->DATA as $key => $object) {
            
            // $CODE = $object->CODE;       
            
            $data['CODE'] =  $object->CODE;
            $data['DESCRIPTION'] =  $object->DESCRIPTION;
             $data['CATEGORYNAME'] =  $object->CATEGORYNAME;
              $data['SUBCATEGORYID'] =  $object->SUBCATEGORYID;
               $data['SUBCATEGORY_DESC'] =  $object->SUBCATEGORY_DESC;

           // print_r($data);            
            
            $where = array('CODE'=>$data['CODE']);
            $is_subexit = $this->check_exit('products',$where);
            
            if($is_subexit ==false){
                Common::add_details('products',$data);
            } else {
                $where =array('CODE'=>$data['CODE']);
                Common::update_details('products',$data,$where);
            }
       }     
    }
    function storePopulateSaleName(){
        
          $request_id = time().rand(123,999);
          $url = 'http://60.254.21.72:5101/api/TerraFns/QualitySalenameMapping?requestId='.$request_id;
          $cust_data = $this->getcurldata($url);
          $result = json_decode($cust_data);
        //  print_r($result);

          foreach ($result->DATA as $key => $object) {
            
            // $CODE = $object->CODE;       
            
            $data['SALENAMEID'] =  $object->SALENAMEID;
            $data['SALENAME'] =  $object->SALENAME;
             $data['QUALITYID'] =  $object->QUALITYID;
              $data['QUALITYNAME'] =  $object->QUALITYNAME;

           // print_r($data);            
            
            $where = array('SALENAMEID'=>$data['SALENAMEID']);
            $is_subexit = $this->check_exit('tbl_salename',$where);
            
            if($is_subexit ==false){
                Common::add_details('tbl_salename',$data);
            } else {
                $where =array('SALENAMEID'=>$data['SALENAMEID']);
                Common::update_details('tbl_salename',$data,$where);
            }
       }     
    }
    
 
}
