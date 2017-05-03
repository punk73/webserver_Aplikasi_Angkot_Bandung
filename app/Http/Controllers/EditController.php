<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Http\Requests\UploadRequest;
use App\trip;
use App\trip_new;
use Fpdf;
use Auth;
use Session;
//use Illuminate\Http\Request;
use DB;

class EditController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        
    }

    public function upload($img){
      // return $img;
      $photo = $img->getClientOriginalName();
      //$destination = base_path() . '/public/images';
      //$img->move($destination, $photo);
      return $img->store();
      //$img->store('images/');

      
    }

    public function update(Request $request){
        
     $trip_short_name = $request->namaTrayek ; // kalau dari ajax, namaTrayek teh dari JSON yang dikirim.
     $route_id = $request->route_id ; //trip::find($id);
     $fare_id = $request->fare_id;
     $route_color = $request->route_color;
     $price = $request->price;
     //$fare_rule = $request->fare_rule;
     $file = $request->file('file');
     $image = $request->image;
     $shape_id = $request->shape_id;
     $keterangan = $request->keterangan;
     $trip_headsign = $request->trip_headsign;


     DB::select("UPDATE trips SET trip_short_name = '".$trip_short_name."', trip_headsign = '".$trip_headsign."', shape_id = '".$shape_id."', ket = '".$keterangan."' where route_id ='".$route_id."'");
     DB::select("UPDATE route SET route_short_name = '".$trip_headsign."', route_color = '".$route_color."', image = '".$image."' where route_id ='".$route_id."'");
     DB::select("UPDATE fare_rule SET fare_id = '".$fare_id."' where route_id = '".$route_id."'");

     if( $request->hasFile('file') && !file_exists( public_path('images/'.$file->getClientOriginalName() ) )){
        $imageName = $file->getClientOriginalName();
        $file->storeAs('images', $imageName);//move(public_path('images'), $imageName );
        
     }

     session::flash("flash_notification", [
            "level"=>"success" ,
            "message"=>"berhasil memperbarui data"
            ]);

     //return back();
     
     //return view('map.edit',compact('trip', 'fare_attributes'));
     return redirect('edit'); 

    }

    public function update_points(Request $request){
      $data = $request->data;

      if(!empty($data)){
        $route_id = $request->route_id;
        foreach ($data as $key => $value) {
          # code...
          //DB::select("UPDATE shapes SET shape_pt_lat = '".$value['shape_pt_lat']."', shape_pt_lon = '".$value['shape_pt_lon'] ."' where shape_id ='".$value['shape_id']."'");
          $gabungShapeId[] = $value['shape_id'];
          if( $value['id'] == '' ){
            DB::select("INSERT INTO shapes value ('','".$value['shape_id']."','".$value['shape_pt_lat']."','".$value['shape_pt_lon']."','0','','','')");
            //$result[] = "INSERT INTO shapes value ('','".$value['shape_id']."','".$value['shape_pt_lat']."','".$value['shape_pt_lon']."','0','','','')";
          }
          else
          {

            DB::select("UPDATE shapes SET shape_pt_lat = '".$value['shape_pt_lat']."', shape_pt_lon = '".$value['shape_pt_lon'] ."' where shape_id ='".$value['shape_id']."'");
            //$result[] = "UPDATE shapes SET shape_pt_lat = '".$value['shape_pt_lat']."', shape_pt_lon = '".$value['shape_pt_lon']."' where shape_id ='".$value['shape_id']."'";
          }
        }

        $implode = implode(", ", $gabungShapeId ) ;
        DB::select("UPDATE trips SET shape_id = '".$implode."' where route_id ='".$route_id."' ");
        return 'Ok';
      }
      else
      {
        return 'empty';
      }

    }

    public function save_fare_attributes(Request $request){
      $price_fare_attributes = $request->price_fare_attributes;
      $fare_id = $request->fare_id;
      if(!empty($price_fare_attributes))
      {
        DB::select("INSERT INTO fare_attributes VALUES ('".$fare_id."','".$price_fare_attributes."','IDR','','') ");
        return "OK";

      }

    }
    
}
