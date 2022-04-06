<?php  
 namespace App\Http\Controllers;  
 use Illuminate\Http\Request;  
 class FirebaseController extends Controller  
 {  
   protected $db;  
   public function __construct() {  
     $this->db = app('firebase.firestore')->database();  
   } 
   public function index(Request $request)  
   {  
    //  return view('chat');
     $docRef = $this->db->collection('Demo');  
     $query = $docRef;  
     if(isset($request->search))  
       $query = $docRef->where('name', '=', $request->search);  
     $documents = $query->documents();  
     foreach ($documents as $document) {  
         print_r($document->id());
         echo "<br>";
         $abc = $document->id();
         echo $abc;  
       }  
       $docRef = $this->db->collection('Messages');  
      $query = $docRef;
      if(isset($request->search))  
      $query = $docRef->where('name', '=', $request->search);  
    $records = $query->documents();
    foreach($records as $record){
      // dd($records);
      echo $record['message'];
    }
     }  
     
     public function demo(){
      $docRef = $this->db->collection('Channels');  
       $query = $docRef;
       if(isset($request->search))  
       $query = $docRef->where('name', '=', $request->search);  
     $documents = $query->documents();
     foreach ($documents as $document) {
     $abc = $document->id();
    //  echo $abc;
     }
     $docRef = $this->db->collection('Messages');  
     $query = $docRef;
     if(isset($request->search))  
     $query = $docRef->where('demo_id', '=', $request->search); 
   $records = $query->documents();
  //  dd($records);
       return view('newchat',compact('documents','records'));
     }

     public function testing(){
      $docRef = $this->db->collection('Demo')->document('Admin');
      $snapshot = $docRef->snapshot();
      
      if ($snapshot->exists()) {
        $test = $snapshot->data();
        echo $test['Name'];
        echo "<br>";
        echo $test['Email'];
      } else {
          echo "Not found";
      }
     }
     public function userchathistory(Request $request){
       $id = $request->id;
      //  echo $id; die();
    //   $docRef = $this->db->collection('Channels');  
    //    $query = $docRef;
    //    if(isset($request->search))  
    //    $query = $docRef->where('name', '=', $request->search);  
    //  $documents = $query->documents();
    $docRef = $this->db->collection('Messages');  
      $query = $docRef;
      $query = $docRef->where('channel_id', '=', $id);  
      // print_r($query); die();
    $records = $query->documents();
    // print_r($records); die();
    foreach($records as $record){
    $message = $record['message']; 
    echo $message;
    }   

    // $recordarray = (array)$records;
    // echo gettype($recordarray); die();
    // echo "After conversion :";
    // print_r($recordarray); die();
   // echo gettype($records); die();
      //  return view('newchat',compact('documents','records'));
     }

 }  