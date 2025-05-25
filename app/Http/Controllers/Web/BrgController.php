<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
// ganti 1

use App\Models\Brg;
use App\Models\BrgDetail;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\DB;



include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";

use PHPJasperXML;




// ganti 2
class BrgController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // ganti 3
        return view('brg.index');
    }

    // ganti 4

    public function getBrg(Request $request)
    {
        // ganti 5

        if ($request->session()->has('periode')) {
            $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];
        } else {
            $periode = '';
        }



        $brg = DB::table('brg as b')
            ->join('brgd as bd', 'b.brg_id', '=', 'bd.brg_id')
            ->join('categories as c', 'b.category_id', '=', 'c.category_id')
            ->select(
                'b.brg_id',
                'b.brg_name',
                'b.price',
                'b.per',
                'b.brg_deskripsi',
                'b.url',
                'c.category_name',
                DB::raw('SUM(bd.quantity) as total_produk')
            )
            ->groupBy(
                'b.brg_id',
                'b.brg_name',
                'b.price',
                'b.per',
                'b.brg_deskripsi',
                'b.url',
                'c.category_name'
            )
            ->get();



        return Datatables::of($brg)
            ->addIndexColumn()
            ->editColumn('brg_deskripsi', function ($row) {
                return $row->brg_deskripsi ?: '<span class="text-muted">-</span>';
            })

            ->addColumn('action', function ($row) {

                $btnPrivilege = '								
    <a class="dropdown-item" href="brg/edit/' . $row->brg_id . '">
        <i class="fas fa-pen text-primary"></i>&nbsp&nbsp;&nbsp;; Edit
    </a>
    <a class="dropdown-item" href="brg/show/' . $row->brg_id . '">
        <i class="fas fa-boxes text-success"></i>&nbsp;&nbsp;&nbsp; Edit Stok
    </a>
    <hr>
    <a class="dropdown-item text-danger" onclick="return confirm(&quot;Apakah anda yakin ingin hapus?&quot;)" href="brg/delete/' . $row->brg_id . '">
        <i class="fas fa-trash-alt"></i>&nbsp; Hapus
    </a>';

                $actionBtn =
                    '
                    <div class="dropdown show" style="text-align: center">
                        <a class="btn btn-secondary dropdown-toggle btn-sm" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-bars"></i>
                        </a>

                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            

                            ' . $btnPrivilege . '
                        </div>
                    </div>
                    ';

                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $categories = DB::table('categories')
            ->select('category_id', 'category_name')
            ->get()
            ->map(function ($item) {
                $item->category_name = ucwords(strtolower(preg_replace_callback(
                    '/[^\s\-&]+/',
                    fn($matches) => ucfirst($matches[0]),
                    $item->category_name
                )));
                return $item;
            });

        $listCabang = DB::table('compan')
            ->select('compan_code', 'name')
            ->get()
            ->map(function ($item) {
                $item->name = ucwords(strtolower(preg_replace_callback(
                    '/[^\s\-&]+/',
                    fn($matches) => ucfirst($matches[0]),
                    $item->name
                )));
                return $item;
            });

        return view('brg.create', compact('categories', 'listCabang'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    public function store(Request $request)
    {
        $validated = $request->validate([
            'brg_name' => 'required|string',
            'price' => 'required|string',
            'category_id' => 'required|exists:categories,category_id',
            'per' => 'required|string',
            'brg_deskripsi' => 'nullable|string',
            'compan_code' => 'required|array',
            'jumlah' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            $brg_name = ucwords(strtolower($validated['brg_name']));
            $price = str_replace('.', '', $validated['price']);

            $category = DB::table('categories')
                ->where('category_id', $validated['category_id'])
                ->first();

            if (!$category) {
                throw new \Exception('Kategori tidak ditemukan.');
            }

            // Format nama folder & file
            $cleanCategory = str_replace(' ', '_', strtolower($category->category_name));
            $cleanProduct = preg_replace('/[^a-z0-9]/i', '', strtolower($validated['brg_name']));

            $file = $request->file('imageUpload');
            $extension = $file->getClientOriginalExtension();

            // Simpan file ke public storage
            $file->storeAs('public/img/gambar_produk/' . $cleanCategory, $cleanProduct . '.' . $extension);
            // Simpan ke database dengan path relatif
            $brg = new Brg();
            $brg->brg_name = $brg_name;
            $brg->price = $price;
            $brg->category_id = $validated['category_id'];
            $brg->per = $validated['per'];
            $brg->brg_deskripsi = $validated['brg_deskripsi'] ?? null;
            $brg->url =  $cleanCategory . '/' . $cleanProduct . '.' . $extension;
            $brg->save();
            $file->move(public_path('img/gambar_produk/' . $cleanCategory), $cleanProduct . '.' . $extension);
            foreach ($validated['compan_code'] as $index => $compan_code) {
                $jumlah = $validated['jumlah'][$index];
                if (!empty($compan_code) && !empty($jumlah)) {
                    $detail = new BrgDetail();
                    $detail->brg_id = $brg->brg_id;
                    $detail->compan_code = $compan_code;
                    $detail->quantity = $jumlah;
                    $detail->save();
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Data barang berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }





    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 12

    public function show(Brg $brg)
    {

        $brg_id = $brg->brg_id;
        $brgDetail = DB::table('brgd')->where('brg_id', $brg_id)->get();
        $compan = DB::table('compan')->select('name', 'compan_code')->get();
        $categories = DB::table('categories')
            ->where('category_id', $brg->category_id)
            ->value('category_name');

        // Tambahkan properti baru ke objek $brg
        $brg->category_name = $categories;
        $data = [
            'header'        => $brg,
            'detail'        => $brgDetail,
            'company'        => $compan,
        ];

        return view('brg.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 15

    public function edit(Brg $brg)
    {

        $categories = DB::table('categories')
            ->select('category_id', 'category_name')
            ->get()
            ->map(function ($item) {
                $item->category_name = ucwords(strtolower(preg_replace_callback(
                    '/[^\s\-&]+/',
                    fn($matches) => ucfirst($matches[0]),
                    $item->category_name
                )));
                return $item;
            });

        $listCabang = DB::table('compan')
            ->select('compan_code', 'name')
            ->get()
            ->map(function ($item) {
                $item->name = ucwords(strtolower(preg_replace_callback(
                    '/[^\s\-&]+/',
                    fn($matches) => ucfirst($matches[0]),
                    $item->name
                )));
                return $item;
            });
        $data = [
            'brg'        => $brg,
            'listCabang'        => $listCabang,
            'categories'        => $categories,
        ];
        return view('brg.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 18



    public function update(Request $request, Brg $brg)
    {
        $request->validate([
            'brg_name' => 'required|string|max:255',
            'price' => 'required|string',
            'category_id' => 'required|exists:categories,category_id',
            'per' => 'required|string',
            'brg_deskripsi' => 'nullable|string',
            'imageUpload' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $harga = (int) str_replace('.', '', $request->price);

            // Ambil nama kategori
            $category = DB::table('categories')->where('category_id', $request->category_id)->first();
            if (!$category) {
                throw new \Exception("Kategori tidak ditemukan.");
            }

            // Format nama folder & nama file
            $cleanCategory = str_replace(' ', '_', strtolower($category->category_name));
            $cleanProduct = preg_replace('/[^a-z0-9]/i', '', strtolower($request->brg_name));
            $path = public_path('img/gambar_produk/' . $cleanCategory);
            if ($request->hasFile('imageUpload')) {
                // Hapus gambar lama jika ada
                if ($brg->url && file_exists(public_path('img/gambar_produk/' . $brg->url))) {
                    unlink(public_path('img/gambar_produk/' . $brg->url));
                }

                $file = $request->file('imageUpload');
                $extension = $file->getClientOriginalExtension();
                $fileName = $cleanProduct . '_' . time() . '.' . $extension;

                // Simpan file
                $file->move($path, $fileName);

                // Simpan path relatif ke database
                $brg->url = $cleanCategory . '/' . $fileName;
            }

            // Simpan data lainnya
            $brg->brg_name = $request->brg_name;
            $brg->price = $harga;
            $brg->category_id = $request->category_id;
            $brg->per = $request->per;
            $brg->brg_deskripsi = $request->brg_deskripsi;
            $brg->save();

            DB::commit();
            return redirect()->back()->with('success', 'Data barang berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 22

    public function destroy(Brg $brg)
    {
        DB::beginTransaction();

        try {
            // Hapus gambar jika ada
            if ($brg->url && file_exists(public_path('img/gambar_produk/' . $brg->url))) {
                unlink(public_path('img/gambar_produk/' . $brg->url));
            }

            DB::table('brgd')->where('brg_id', $brg->brg_id)->delete();

            $brg->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Data barang dan distribusinya berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus barang: ' . $e->getMessage());
        }
    }




    public function storeBrgd(Request $request)
    {
        $request->validate([
            'brg_id' => 'required|integer|exists:brg,brg_id',
            'compan_code' => 'required|array',
            'jumlah' => 'required|array',
        ]);

        try {
            $brg_id = $request->input('brg_id');
            $companCodes = $request->input('compan_code');
            $jumlahStok = $request->input('jumlah');

            foreach ($companCodes as $index => $code) {
                $qty = intval($jumlahStok[$index]);

                BrgDetail::updateOrCreate(
                    [
                        'brg_id' => $brg_id,
                        'compan_code' => $code,
                    ],
                    [
                        'quantity' => $qty,
                    ]
                );
            }

            return redirect()->back()->with('success', 'Data stok berhasil disimpan.');
        } catch (\Exception $e) {

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data stok. Silakan coba lagi.');
        }
    }
}
