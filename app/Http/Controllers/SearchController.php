<?php
// app/Http/Controllers/SearchController.php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');
        $results = Product::search($query)->get();
        return response()->json($results);
    }
}
