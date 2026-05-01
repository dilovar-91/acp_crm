<?php

namespace App\Http\Controllers;

use App\Models\Fare;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Image;

class FareController extends Controller
{
    public function fares(){
        $cars= QueryBuilder::for(Fare::class)
            ->allowedFilters([AllowedFilter::exact('showroom_id'),AllowedFilter::exact('type'), AllowedFilter::scope('withTrashed')])->orderBy('created_at', 'DESC')->get();
        return response()->json($cars, 201);
    }

    public function save(Request $request){

        if (isset($request->item['id']) ){
            $item = Fare::where('id', $request->item['id'])->first();
        }
        else $item = new Fare;
        $item->showroom_id = $request->item['showroom_id'];
        $item->client_name = $request->item['client_name'];
        $item->type = $request->item['type'];
        $item->phone = $request->item['phone'];
        $item->region = $request->item['region'];
        $item->save();
        return response()->json($item, 201);
    }

    public function create(Request $request){

        Log::emergency($request->images);

        /*$this->validate($request, [
            'client_name' => 'required',
            'phone'=>'required',
            'type'=>'required',
            'showroom_id'=>'required'
        ]);*/

        //return $request;

        $pictures = array();
        if(count($request->images)>0) {
            $files = $request->images;
            foreach ($files as $file) {
                try {
                    $file = base64_decode($file);
                    $safeName = "fire_".uniqid()."_".time().".jpg";
                    $success = file_put_contents(public_path().'/images/fares/'.$safeName, $file);
                    if($success) {
                        $pictures[] = $safeName;
                    }

                } catch (\Exception $e) {
                   Log::error($e);
                   continue;
                }
            }
        }



        $item = new Fare;
        $item->showroom_id = $request->showroom_id;
        $item->client_name = $request->client_name;
        $item->type = $request->type ?? null;
        $item->phone = $request->phone ?? null;
        $item->region = $request->region ?? null;
        $item->pictures = $pictures;
        $item->save();
        return response()->json($item, 201);
    }

    public function  delete(Request $request){
        $item = Fare::find($request->id);
        $item->delete();
        return response()->json($item, 200);
    }


    public function addFare(Request $request)
    {
        $this->validate($request, [
            'client_name' => 'required|string|max:255',
            'showroom_id' => 'required|number',
            'type_id' => 'required|number',
            'price' => 'required|numeric',
        ]);
        $product = new Product;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->save();
        $pictures = [];

        if($request->hasfile('images'))
        {

            foreach($request->file('images') as $file)
            {
                $name1=$file->getClientOriginalName();
                $resize = Image::make($request->file)->resize(1280, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->encode('jpg');
                $name = time().$name1;
                $pictures[] = \File::put(public_path('images/fares/') . $name, $resize->__toString());
            }
        }
        foreach ($request->file('images') as $imagefile) {
            $pictures[] = $imagefile->store('/images/fares');
        }

    }
}
