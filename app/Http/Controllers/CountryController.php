<?php

namespace App\Http\Controllers;

use App\Http\Requests\CountryRequest;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\Facades\DataTables;

class CountryController extends Controller
{
    public function index()
    {
        return view('index');
    }
    public function store(CountryRequest $request)
    {
        $request->validate([
            'country_name' => 'required',
            'capital_city' => 'required',
        ]);
        $country = Country::create([
            'country_name' => $request->country_name,
            'capital_city' => $request->capital_city,
        ]);
        if ($country) {
            return response()->json([
                'status' => 1,
                'message' => 'Country Created Successfully',
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Country Not Created',
            ]);
        }

    }
    public function getAllCountries(Request $request)
    {
        if ($request->ajax()) {
            $data = Country::select(['id', 'country_name', 'capital_city'])->orderBy('country_name', 'asc');
            return DataTables::of($data)->addColumn('actions', fn($row) => 
            
            '<div class="d-flex justify-content-center ">
                            <button style="margin-right: 1px;" type="button" class="btn btn-primary btn-sm me-2 mr-1.5  edit" data-id="'.$row['id'].'" id="edit">Edit</button>
                            <button type="button" class="btn btn-danger btn-sm delete" data-id="'.$row['id'].'" id="deleteButton">Delete</button>
                </div>')->addColumn('checkbox',function($row){
                    $encryptedId = encrypt($row->id);
                return '<input type="checkbox" class="checkbox" name="country_checkbox" data-id="'.$encryptedId.'">';
                })->rawColumns(['actions','checkbox'])
            ->make(true);
        };

    }
    public function get_country(Request $request)
    {
        $country_id = $request->id;
        $country = Country::findOrFail($country_id);
        return response()->json(['data' => $country]);
    }
    public function update(Request $request)
    {
        $country = Country::findOrFail($request->country_id);
        $request->validate([
            'country_name' => ['required','string','max:255',"unique:countries,country_name,$country->id"],
            'capital_city' => ['required','string','max:255',"unique:countries,capital_city,$country->id"],
        ]);
        $country->update([
            'country_name' => $request->country_name,
            'capital_city' => $request->capital_city,
        ]);
        if ($country) {
            return response()->json([
                'status' => 1,
                'message' => 'Country Updated Successfully',
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Country Not Updated',
            ]);
        }

    }
    
   public function destroy(Request $request)
   {
       $country = Country::findOrFail($request->id);
       $country->delete();
       if ($country) {
           return response()->json([
               'status' => 1,
               'message' => 'Country Deleted Successfully',
           ]);
       } else {
           return response()->json([
               'status' => 0,
               'message' => 'Country Not Deleted',
           ]);
       }
   }
    public function multiple_delete(Request $request)
    {
        $encryptedIds = $request->ids;

        if (!is_array($encryptedIds)) {
            return response()->json([
                'status' => 0,
                'message' => 'Invalid request format.',
            ], 400);
        }

        $ids = [];

        foreach ($encryptedIds as $encryptedId) {
            try {
                $ids[] = decrypt($encryptedId);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Failed to decrypt IDs.',
                ], 400);
            }
        }

        $deleted = Country::whereIn('id', $ids)->delete();

        if ($deleted) {
            return response()->json([
                'status' => 1,
                'message' => 'Countries Deleted Successfully.',
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'No countries were deleted.',
            ]);
        }
    }
  
}