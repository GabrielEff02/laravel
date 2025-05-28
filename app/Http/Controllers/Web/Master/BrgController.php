<?php

namespace App\Http\Controllers\Web\Master;

use App\Http\Controllers\Controller;
// ganti 1

use App\Models\Brg;
use App\Models\BrgDetail;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;



include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";





// ganti 2
class BrgController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
        session(['back_urls' => $backUrls]);
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

    public function index()
    {
        $this->pushToBackStack();
        // ganti 3
        return view('master.brg.index');
    }

    // ganti 4

    public function getBrg(Request $request)
    {
        // Cek apakah ini request DataTable (pakai parameter draw)
        if (!$request->has('draw')) {
            $columns = [
                ['data' => 'DT_RowIndex', 'title' => 'No.', 'orderable' => false, 'searchable' => false, 'width' => '20px', 'className' => 'dt-center'],
                ['data' => 'nama', 'title' => 'Nama Barang', 'width' => '150px'],
                ['data' => 'category_name', 'title' => 'Kategori',  'width' => '60px'],
                ['data' => 'total_produk', 'title' => 'Total Produk',  'width' => '80px', 'className' => 'dt-center'],
                ['data' => 'harga', 'title' => 'Harga', 'width' => '40px', 'className' => 'dt-right'],
                ['data' => 'satuan', 'title' => 'Satuan', 'width' => '45px'],
                ['data' => 'deskripsi', 'title' => 'Deskripsi', 'width' => '800px'],
                ['data' => 'url', 'title' => 'Url', 'width' => '200px'],
                ['data' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '40px', 'className' => 'dt-center'],
            ];

            return response()->json(['columns' => $columns]);
        }

        // Kalau ini request datatable (ada draw), baru ambil data
        $id = DB::table('brg as b')
            ->join('brgd as bd', 'b.brg_id', '=', 'bd.brg_id')
            ->join('categories as c', 'b.category_id', '=', 'c.category_id')
            ->join('satuan as s', 's.id', '=', 'b.satuan')
            ->select(
                'b.brg_id',
                'b.nama',
                'b.harga',
                's.nama as satuan',
                'b.deskripsi',
                'b.url',
                'c.category_name',
                DB::raw('SUM(bd.quantity) as total_produk')
            )
            ->groupBy(
                'b.brg_id',
                'b.nama',
                'b.harga',
                's.nama',
                'b.deskripsi',
                'b.url',
                'c.category_name'
            );

        return Datatables::of($id)
            ->addIndexColumn()
            ->editColumn('deskripsi', function ($row) {
                return $row->deskripsi ?: '<span class="text-muted">-</span>';
            })
            ->filter(function ($query) use ($request) {
                if ($search = $request->input('search.value')) {
                    if (preg_match('/^\d+$/', $search)) {
                        $query->havingRaw('CAST(SUM(bd.quantity) AS CHAR) LIKE ?', ["%{$search}%"]);
                    } else {
                        $query->where(function ($q) use ($search) {
                            $q->where('b.nama', 'like', "%{$search}%")
                                ->orWhere('b.harga', 'like', "%{$search}%")
                                ->orWhere('s.satuan', 'like', "%{$search}%")
                                ->orWhere('b.deskripsi', 'like', "%{$search}%")
                                ->orWhere('b.url', 'like', "%{$search}%")
                                ->orWhere('c.category_name', 'like', "%{$search}%");
                        });
                    }
                }
            })
            ->editColumn('harga', function ($row) {
                return number_format($row->harga, 0, ',', '.');
            })
            ->addColumn('action', function ($row) {
                $btnPrivilege = '
    <a class="dropdown-item" href="' . url('master/brg/edit/' . $row->brg_id) . '">
        <i class="fas fa-pen text-primary"></i>&nbsp;&nbsp;&nbsp; Edit
    </a>
    <a class="dropdown-item" href="' . url('master/brg/show/' . $row->brg_id) . '">
        <i class="fas fa-boxes text-success"></i>&nbsp;&nbsp;&nbsp; Edit Stok
    </a>
    <hr>
    <a class="dropdown-item text-danger" onclick="return confirm(\'Apakah anda yakin ingin hapus?\')" href="' . url('master/brg/delete/' . $row->brg_id) . '">
        <i class="fas fa-trash-alt"></i>&nbsp; Hapus
    </a>';


                return '
                <div class="dropdown show" style="text-align: center">
                    <a class="btn btn-secondary dropdown-toggle btn-sm" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-bars"></i>
                    </a>
                    <div class="dropdown-menu" >' . $btnPrivilege . '</div>
                </div>';
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
        $this->pushToBackStack(['/master/brg/create', '/master/brg/store']);


        $categories = DB::table('categories')
            ->select('category_id AS value', 'category_name AS label')
            ->get()
            ->map(function ($item) {
                $item->label = ucwords(strtolower(preg_replace_callback(
                    '/[^\s\-&]+/',
                    fn($matches) => ucfirst($matches[0]),
                    $item->label
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
        $satuan = DB::table('satuan')
            ->select('id AS value', 'nama AS label')
            ->get();

        $form = [
            ['label' => 'Nama Barang', 'value' => 'nama', 'type' => 'string'],
            ['label' => 'Harga Barang Rp.', 'value' => 'harga', 'type' => 'number'],
            ['label' => 'Kategori Barang', 'value' => 'category_id', 'type' => 'selection', 'option' => $categories],
            ['label' => 'Satuan', 'value' => 'satuan', 'type' => 'selection', 'option' => $satuan],
            ['label' => 'Deskripsi Barang', 'value' => 'deskripsi', 'type' => 'string'],
            ['label' => 'Gambar Produk', 'value' => 'url', 'type' => 'image', 'path' => 'img/gambar_produk/'],
        ];
        $data = ['forms' => $form, 'listCabang' => $listCabang, 'backUrl' => $this->popBackStack()];
        return view('master.brg.create', $data);
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
            'nama' => 'required|string',
            'harga' => 'required|string',
            'category_id' => 'required|exists:categories,category_id',
            'satuan' => 'required|string',
            'deskripsi' => 'nullable|string',
            'compan_code' => 'required|array',
            'jumlah' => 'required|array',
            'url' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validasi gambar opsional
        ]);

        try {
            DB::beginTransaction();

            $nama = ucwords(strtolower($validated['nama']));
            $harga = str_replace('.', '', $validated['harga']);

            $category = DB::table('categories')
                ->where('category_id', $validated['category_id'])
                ->first();

            if (!$category) {
                throw new \Exception('Kategori tidak ditemukan.');
            }

            // Format nama folder & file
            $cleanCategory = str_replace(' ', '_', strtolower($category->category_name));
            $cleanProduct = preg_replace('/[^a-z0-9]/i', '', strtolower($validated['nama']));

            $imagePath = null;
            $file = $request->file('url');
            if ($file) {
                $extension = $file->getClientOriginalExtension();
                $fileName = $cleanProduct . '.' . $extension;

                // Simpan file ke storage Laravel dan ke folder public
                $file->storeAs('public/img/gambar_produk/' . $cleanCategory, $fileName);
                $file->move(public_path('img/gambar_produk/' . $cleanCategory), $fileName);

                // Simpan path relatif
                $imagePath = $cleanCategory . '/' . $fileName;
            }

            // Simpan data utama ke tabel brg
            $id = new Brg();
            $id->nama = $nama;
            $id->harga = $harga;
            $id->category_id = $validated['category_id'];
            $id->satuan = $validated['satuan'];
            $id->deskripsi = $validated['deskripsi'] ?? null;
            $id->url = $imagePath; // null jika tidak upload gambar
            $id->save();

            // Simpan data ke tabel detail distribusi barang
            foreach ($validated['compan_code'] as $index => $compan_code) {
                $jumlah = $validated['jumlah'][$index];
                if (!empty($compan_code) && !empty($jumlah)) {
                    $detail = new BrgDetail();
                    $detail->brg_id = $id->brg_id;
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

    public function show(Brg $id)
    {
        $this->pushToBackStack(['/master/brg/show', '/master/brg/storeBrgd']);


        $idDetail = DB::table('brgd')->where('brg_id', $id->brg_id)->get();
        $compan = DB::table('compan')->select('name', 'compan_code')->get();
        $categories = DB::table('categories')
            ->where('category_id', $id->category_id)
            ->value('category_name');
        $satuan = DB::table('satuan')
            ->where('id', $id->satuan)
            ->value('nama');

        // Tambahkan properti baru ke objek $id
        $id->category_name = $categories;
        $id->satuan = $satuan;
        $data = [
            'header'        => $id,
            'detail'        => $idDetail,
            'company'        => $compan,
            'backUrl' => $this->popBackStack(),
        ];

        return view('master.brg.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 15

    public function edit(Brg $id)
    {
        $this->pushToBackStack(skipPatterns: ['/master/brg/edit', '/master/brg/update']);

        $categories = DB::table('categories')
            ->select('category_id AS value', 'category_name AS label')
            ->get()
            ->map(function ($item) {
                $item->label = ucwords(strtolower(preg_replace_callback(
                    '/[^\s\-&]+/',
                    fn($matches) => ucfirst($matches[0]),
                    $item->label
                )));
                return $item;
            });

        $satuan = DB::table('satuan')
            ->select('id AS value', 'nama AS label')
            ->get();
        $form = [
            ['label' => 'Nama Barang', 'value' => 'nama', 'type' => 'string'],
            ['label' => 'Harga Barang Rp.', 'value' => 'harga', 'type' => 'number'],
            ['label' => 'Kategori Barang', 'value' => 'category_id', 'type' => 'selection', 'option' => $categories],
            ['label' => 'Satuan', 'value' => 'satuan', 'type' => 'selection', 'option' => $satuan],
            ['label' => 'Deskripsi Barang', 'value' => 'deskripsi', 'type' => 'string'],
            ['label' => 'Gambar Produk', 'value' => 'url', 'type' => 'image', 'path' => 'img/gambar_produk/'],
        ];
        $id['primaryKey'] = $id['brg_id'];

        $data = [
            'data'        => $id,
            'forms'        => $form,
            'backUrl' => $this->popBackStack(),
        ];
        return view('master.brg.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 18



    public function update(Request $request, Brg $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|string',
            'category_id' => 'required|exists:categories,category_id',
            'satuan' => 'required|string',
            'deskripsi' => 'nullable|string',
            'url' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $harga = (int) str_replace('.', '', $request->harga);

            $category = DB::table('categories')->where('category_id', $request->category_id)->first();
            if (!$category) {
                throw new \Exception("Kategori tidak ditemukan.");
            }

            // Format nama folder & nama file
            $cleanCategory = str_replace(' ', '_', strtolower($category->category_name));
            $cleanProduct = preg_replace('/[^a-z0-9]/i', '', strtolower($request->nama));
            $path = public_path('img/gambar_produk/' . $cleanCategory);
            if ($request->hasFile('url')) {
                // Hapus gambar lama jika ada
                if ($id->url && file_exists(public_path('img/gambar_produk/' . $id->url))) {
                    unlink(public_path('img/gambar_produk/' . $id->url));
                }

                $file = $request->file('url');
                $extension = $file->getClientOriginalExtension();
                $fileName = $cleanProduct . '_' . time() . '.' . $extension;

                // Simpan file
                $file->move($path, $fileName);

                // Simpan path relatif ke database
                $id->url = $cleanCategory . '/' . $fileName;
            }

            // Simpan data lainnya
            $id->nama = $request->nama;
            $id->harga = $harga;
            $id->category_id = $request->category_id;
            $id->satuan = $request->satuan;
            $id->deskripsi = $request->deskripsi;
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

    public function destroy(Brg $id)
    {
        DB::beginTransaction();

        try {
            // Hapus gambar jika ada
            if ($id->url && file_exists(public_path('img/gambar_produk/' . $id->url))) {
                unlink(public_path('img/gambar_produk/' . $id->url));
            }

            DB::table('brgd')->where('brg_id', $id->brg_id)->delete();

            $id->delete();

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
            $id_id = $request->input('brg_id');
            $companCodes = $request->input('compan_code');
            $jumlahStok = $request->input('jumlah');

            foreach ($companCodes as $index => $code) {
                $qty = intval($jumlahStok[$index]);

                BrgDetail::updateOrCreate(
                    [
                        'brg_id' => $id_id,
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
