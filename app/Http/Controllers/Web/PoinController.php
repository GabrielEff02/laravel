<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

use App\Models\Poin;
use App\Models\PoinDetail;
use Illuminate\Http\Request;
use DataTables;

use Illuminate\Support\Facades\DB;



include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";

use PHPJasperXML;




// ganti 2
class PoinController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // ganti 3
        return view('poin.index');
    }

    // ganti 4

    public function getPoin(Request $request)
    {
        // ganti 5

        if ($request->session()->has('periode')) {
            $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];
        } else {
            $periode = '';
        }



        $poin = DB::table('products as p')
            ->join('productd as pd', 'p.product_id', '=', 'pd.product_id')
            ->select(
                'p.product_id',
                'p.product_name',
                'p.price',
                'p.image_url',
                'p.product_description',
                DB::raw('SUM(pd.quantity) as total_produk')
            )
            ->groupBy(
                'p.product_id',
                'p.product_name',
                'p.price',
                'p.image_url',
                'p.product_description',
            )
            ->get();



        return Datatables::of($poin)
            ->addIndexColumn()
            ->editColumn('product_description', function ($row) {
                return $row->product_description ?: '<span class="text-muted">-</span>';
            })

            ->addColumn('action', function ($row) {

                $btnPrivilege = '								
                    <a class="dropdown-item" href="poin/edit/' . $row->product_id . '">
                        <i class="fas fa-pen text-primary"></i>&nbsp&nbsp;&nbsp;; Edit
                    </a>
                    <a class="dropdown-item" href="poin/show/' . $row->product_id . '">
                        <i class="fas fa-boxes text-success"></i>&nbsp;&nbsp;&nbsp; Edit Stok
                    </a>
                    <hr>
                    <a class="dropdown-item text-danger" onclick="return confirm(&quot;Apakah anda yakin ingin hapus?&quot;)" href="poin/delete/' . $row->product_id . '">
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

        return view('poin.create', [
            'listCabang' => $listCabang
        ]);
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
            'product_name' => 'required|string',
            'price' => 'required|string',
            'product_description' => 'nullable|string',
            'compan_code' => 'required|array',
            'jumlah' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            $product_name = ucwords(strtolower($validated['product_name']));
            $price = str_replace('.', '', $validated['price']);

            $cleanProduct = preg_replace('/[^a-z0-9]/i', '', strtolower($validated['product_name']));

            $file = $request->file('imageUpload');
            $extension = $file->getClientOriginalExtension();

            // Simpan file ke public storage
            $file->storeAs('public/img/gambar_produk_tukar_poin/' . $cleanProduct . '.' . $extension);
            // Simpan ke database dengan path relatif
            $poin = new Poin();
            $poin->product_name = $product_name;
            $poin->price = $price;
            $poin->product_description = $validated['product_description'] ?? null;
            $poin->image_url =  $cleanProduct . '.' . $extension;
            $poin->save();
            $file->move(public_path('img/gambar_produk_tukar_poin/'), $cleanProduct . '.' . $extension);
            foreach ($validated['compan_code'] as $index => $compan_code) {
                $jumlah = $validated['jumlah'][$index];
                if (!empty($compan_code) && !empty($jumlah)) {
                    $detail = new PoinDetail();
                    $detail->product_id = $poin->product_id;
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

    public function show(Poin $poin)
    {

        $product_id = $poin->product_id;
        $productDetail = DB::table('productd')->where('product_id', $product_id)->get();
        $compan = DB::table('compan')->select('name', 'compan_code')->get();

        // Tambahkan properti baru ke objek $poin
        $data = [
            'header'        => $poin,
            'detail'        => $productDetail,
            'company'        => $compan,
        ];

        return view('poin.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 15

    public function edit(Poin $poin)
    {


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
            'poin'        => $poin,
            'listCabang'        => $listCabang,
        ];
        return view('poin.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 18



    public function update(Request $request, Poin $poin)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'price' => 'required|string',
            'product_description' => 'nullable|string',
            'imageUpload' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $harga = (int) str_replace('.', '', $request->price);

            // Ambil nama kategori

            // Format nama folder & nama file
            $cleanProduct = preg_replace('/[^a-z0-9]/i', '', strtolower($request->product_name));
            $path = public_path('img/gambar_produk_tukar_poin/');
            if ($request->hasFile('imageUpload')) {
                // Hapus gambar lama jika ada
                if ($poin->image_url && file_exists(public_path('img/gambar_produk_tukar_poin/' . $poin->image_url))) {
                    unlink(public_path('img/gambar_produk_tukar_poin/' . $poin->image_url));
                }

                $file = $request->file('imageUpload');
                $extension = $file->getClientOriginalExtension();
                $fileName = $cleanProduct . '_' . time() . '.' . $extension;

                // Simpan file
                $file->move($path, $fileName);

                // Simpan path relatif ke database
                $poin->image_url = $fileName;
            }

            // Simpan data lainnya
            $poin->product_name = $request->product_name;
            $poin->price = $harga;
            $poin->product_description = $request->product_description;
            $poin->save();

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

    public function destroy(Poin $poin)
    {
        DB::beginTransaction();

        try {
            // Hapus gambar jika ada
            if ($poin->image_url && file_exists(public_path('img/gambar_produk_tukar_poin/' . $poin->image_url))) {
                unlink(public_path('img/gambar_produk_tukar_poin/' . $poin->image_url));
            }

            DB::table('productd')->where('product_id', $poin->product_id)->delete();

            $poin->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Data barang dan distribusinya berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus barang: ' . $e->getMessage());
        }
    }




    public function storePoind(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'compan_code' => 'required|array',
            'jumlah' => 'required|array',
        ]);

        try {
            $product_id = $request->input('product_id');
            $companCodes = $request->input('compan_code');
            $jumlahStok = $request->input('jumlah');

            foreach ($companCodes as $index => $code) {
                $qty = intval($jumlahStok[$index]);

                PoinDetail::updateOrCreate(
                    [
                        'product_id' => $product_id,
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
                ->with('error', 'Terjadi kesalahan saat menyimpan data stok:' . $e . '.');
        }
    }
}
