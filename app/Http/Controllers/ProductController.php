<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductFile;
use App\Models\ProductDetail;
use Illuminate\Support\Facades\Session;

class ProductController extends Controller
{   
    public function index(Request $request)
    {
        $results = ProductFile::orderBy('id', 'desc')->get();
        return view('product_index', compact(
                'results'
        ));
    }
    
    public function upload(Request $request)
    {
        $input = $request->all();
        if ($request->file('csv_file')){
            $file = $request->file('csv_file');
            $ori_filename = $file->getClientOriginalName();
            $fileName = $file->hashName();
            $file->move(public_path(ProductFile::FILEPATH), $fileName);
            
            $file_path = ProductFile::FILEPATH.$fileName;
            
            ProductFile::updateOrCreate([
                'filename' => $ori_filename,
            ], [
                'filename' => $ori_filename,
                'filepath' => $file_path,
                'status' => ProductFile::STATUS_PENDING,
                'remark' => null
            ]);
            Session::flash('success', 'Uploaded successfully!'); 
            return response()->json([
                'status' => 1
            ]);
        }
        return response()->json([
            'status' => 0
        ]);
    }
    
    public function detail(Request $request)
    {
        $id = $request->id;
        $results = ProductDetail::where('product_file_id', $id)->get();
        return view('product_detail', compact(
            'results'
        ));
    }
    
    public function delete(Request $request)
    {
        $id = $request->id;
        $result = ProductFile::where('id', $id)->get()->first();
        if(!empty($result)){
            unlink($result->filepath);
            ProductFile::where('id', $id)->delete();
            ProductDetail::where('product_file_id', $id)->delete();
            return redirect(route('product.index'))->with('success', 'Deleted successfully!');
        }
        
        return redirect(route('product.index'))->with('success', 'Failed!');
    }
}