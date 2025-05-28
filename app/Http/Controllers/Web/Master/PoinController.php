<?php

namespace App\Http\Controllers\Web\Master;

use App\Http\Controllers\Controller;

use App\Models\Poin;
use App\Models\PoinDetail;
use Illuminate\Http\Request;
use DataTables;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;



include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";

use PHPJasperXML;




// ganti 2
class PoinController extends Controller
{
    private function pushToBackStack(array $skipPatterns = [])
    {
        $backUrls = session('back_urls', []);
        $current = url()->current();
        $prev = url()->previous();

        // Default skip patterns kalau kosong
        if (empty($skipPatterns)) {
            $skipPatterns = [];
        }

        if (empty($backUrls)) {
            $backUrls[] = $prev;
            $backUrls[] = $current;
        } else {
            $backUrls[] =  $current;
        }
    }
    private function popBackStack()
    {
        $backUrls = session('back_urls', []);


        array_pop($backUrls);

        // Ambil URL sebelumnya atau fallback ke route default
        $previous = array_pop($backUrls);
        $backUrls[] = $previous;

        session(['back_urls' => $backUrls]);

        return $previous;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->pushToBackStack();

        // ganti 3
        return view('master.poin.index');
    }

    // ganti 4

    public function getPoin(Request $request)
    {


        if (!$request->has('draw')) {
            $columns = [
                ['data' => 'DT_RowIndex', 'title' => 'No.', 'orderable' => false, 'searchable' => false, 'width' => '20px', 'className' => 'dt-center'],
                ['data' => 'product_name', 'title' => 'Nama Produk', 'width' => '150px'],
                ['data' => 'total_produk', 'title' => 'Total Produk', 'width' => '80px', 'className' => 'dt-right'],
                ['data' => 'price', 'title' => 'Harga', 'width' => '40px', 'className' => 'dt-center'],
                ['data' => 'product_description', 'title' => 'Deskripsi', 'width' => '400px',],
                ['data' => 'image_url', 'title' => 'Url', 'width' => '200px'],
                ['data' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'className' => 'dt-center', 'width' => '40px'],
            ];

            return response()->json(['columns' => $columns]);
        }

        $id = DB::table('products as p')
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



        return Datatables::of($id)
            ->addIndexColumn()

            ->editColumn('price', function ($row) {
                return number_format($row->price, 0, ',', '.');
            })
            ->editColumn('product_description', function ($row) {
                return $row->product_description ?: '<span class="text-muted">-</span>';
            })


            ->addColumn('action', function ($row) {

                $btnPrivilege = '								
                    <a class="dropdown-item" href="' . url('master/poin/edit/' . $row->product_id) . '">
                        <i class="fas fa-pen text-primary"></i>&nbsp&nbsp;&nbsp;; Edit
                    </a>
                    <a class="dropdown-item" href="' . url('master/poin/show/' . $row->product_id) . '">
                        <i class="fas fa-boxes text-success"></i>&nbsp;&nbsp;&nbsp; Edit Stok
                    </a>
                    <hr>
                    <a class="dropdown-item text-danger" onclick="return confirm(&quot;Apakah anda yakin ingin hapus?&quot;)" href="' . url('master/poin/delete/' . $row->product_id) . '">
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
        $this->pushToBackStack(['/master/poin/create', '/master/poin/store']);

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

        $form = [
            ['label' => 'Nama Produk', 'value' => 'product_name', 'type' => 'string'],
            ['label' => 'Harga Produk (Poin)', 'value' => 'price', 'type' => 'number'],
            ['label' => 'Deskripsi Produk', 'value' => 'product_description', 'type' => 'string'],
            ['label' => 'Gambar Produk', 'value' => 'image_url', 'type' => 'image', 'path' => 'img/gambar_produk_tukar_poin/'],
        ];


        $data = ['backUrl' => $this->popBackStack(), 'forms' => $form, 'listCabang' => $listCabang];

        return view('master.poin.create', $data);
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
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $product_name = ucwords(strtolower($validated['product_name']));
            $price = str_replace('.', '', $validated['price']);
            $cleanProduct = preg_replace('/[^a-z0-9]/i', '', strtolower($validated['product_name']));

            $file = $request->file('image_url');
            if ($file) {
                $extension = $file->getClientOriginalExtension();
                $fileName = $cleanProduct . '.' . $extension;

                $file->storeAs('public/img/gambar_produk_tukar_poin/' . $fileName);
                $file->move(public_path('img/gambar_produk_tukar_poin/'), $fileName);
                $imagePath = $fileName;
            } else {
                $imagePath = '';
            }

            $id = new Poin();
            $id->product_name = $product_name;
            $id->price = $price;
            $id->product_description = $validated['product_description'];
            $id->image_url = $imagePath;
            $id->save();

            foreach ($validated['compan_code'] as $index => $compan_code) {
                $jumlah = $validated['jumlah'][$index];
                if (!empty($compan_code) && !empty($jumlah)) {
                    $detail = new PoinDetail();
                    $detail->product_id = $id->product_id;
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

    public function show(Poin $id)
    {
        $this->pushToBackStack(['/master/poin/show', '/master/poin/storePoind']);


        $product_id = $id->product_id;
        $productDetail = DB::table('productd')->where('product_id', $product_id)->get();
        $compan = DB::table('compan')->select('name', 'compan_code')->get();

        // Tambahkan properti baru ke objek $id
        $data = [
            'backUrl' => $this->popBackStack(),
            'header'        => $id,
            'detail'        => $productDetail,
            'company'        => $compan,
        ];

        return view('master.poin.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 15

    public function edit(Poin $id)
    {
        $this->pushToBackStack(skipPatterns: ['/master/poin/edit', '/master/poin/update']);
        $form = [
            ['label' => 'Nama Produk', 'value' => 'product_name', 'type' => 'string'],
            ['label' => 'Harga Produk (Poin)', 'value' => 'price', 'type' => 'number'],
            ['label' => 'Deskripsi Barang', 'value' => 'product_description', 'type' => 'string'],
            ['label' => 'Gambar Produk', 'value' => 'image_url', 'type' => 'image', 'path' => 'img/gambar_produk_tukar_poin/'],
        ];
        $id['primaryKey'] = $id['product_id'];

        $data = [
            'backUrl' => $this->popBackStack(),
            'forms' => $form,
            'data' => $id,
        ];
        return view('master.poin.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 18



    public function update(Request $request, Poin $id)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'price' => 'required|string',
            'product_description' => 'nullable|string',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $harga = (int) str_replace('.', '', $request->price);


            // Format nama folder & nama file
            $cleanProduct = preg_replace('/[^a-z0-9]/i', '', strtolower($request->product_name));
            $path = public_path('img/gambar_produk_tukar_poin/');
            if ($request->hasFile('image_url')) {
                // Hapus gambar lama jika ada
                if ($id->image_url && file_exists(public_path('img/gambar_produk_tukar_poin/' . $id->image_url))) {
                    unlink(public_path('img/gambar_produk_tukar_poin/' . $id->image_url));
                }

                $file = $request->file('image_url');
                $extension = $file->getClientOriginalExtension();
                $fileName = $cleanProduct . '_' . time() . '.' . $extension;

                // Simpan file
                $file->move($path, $fileName);

                // Simpan path relatif ke database
                $id->image_url = $fileName;
            }

            // Simpan data lainnya
            $id->product_name = $request->product_name;
            $id->price = $harga;
            $id->product_description = $request->product_description;
            $id->save();

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

    public function destroy(Poin $id)
    {
        DB::beginTransaction();

        try {
            // Hapus gambar jika ada
            if ($id->image_url && file_exists(public_path('img/gambar_produk_tukar_poin/' . $id->image_url))) {
                unlink(public_path('img/gambar_produk_tukar_poin/' . $id->image_url));
            }

            DB::table('productd')->where('product_id', $id->product_id)->delete();

            $id->delete();

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
