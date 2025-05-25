<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;


use App\Models\Brg;
use App\Models\BrgDetail;
use Illuminate\Http\Request;
use DataTables;
use Auth;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;


include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";

use PHPJasperXML;




// ganti 2
class CategoryController extends Controller
{

    public function get_category()
    {
        $categories = DB::table('categories')->select('category_id', 'category_name')->get();
        return view('brg.create', compact('categories'));
    }
}
