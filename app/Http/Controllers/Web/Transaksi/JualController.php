<?php

namespace App\Http\Controllers\Web\Transaksi;

use App\Http\Controllers\Controller;
// ganti 1

use App\Models\Brg;
use App\Models\BrgDetail;
use App\Models\Jual;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\DB;



include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";





// ganti 2
class JualController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // ganti 3
        return view('transaksi.jual.index');
    }

    // ganti 4

    public function getJual(Request $request)
    {
        if (!$request->has('draw')) {
            $columns = [
                ['data' => 'DT_RowIndex', 'title' => 'No.', 'orderable' => false, 'searchable' => false, 'width' => '20px', 'className' => 'dt-center'],
                ['data' => 'name', 'title' => 'Nama Konsumen', 'width' => '150px'],
                ['data' => 'address', 'title' => 'Alamat Konsumen',  'width' => '60px'],
                ['data' => 'transaction_date', 'title' => 'Waktu Transaksi', 'width' => '200px', 'className' => 'dt-center'],
                ['data' => 'compan_name', 'title' => 'Cabang', 'width' => '150px'],
                ['data' => 'total_amount', 'title' => 'Total Belanja', 'width' => '40px', 'className' => 'dt-right'],
                ['data' => 'total_quantity', 'title' => 'Total Barang', 'width' => '40px', 'className' => 'dt-center'],
                ['data' => 'status', 'title' => 'Status', 'width' => '150px', 'className' => 'dt-center'],
                ['data' => 'is_delivery', 'title' => 'Pengiriman', 'width' => '100px'],
                ['data' => 'driver_name', 'title' => 'Nama Driver', 'width' => '200px',],
                ['data' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '40px', 'className' => 'dt-center'],
            ];

            return response()->json(['columns' => $columns]);
        }

        // Query utama
        $query = DB::table('jual as j')
            ->join('users as u', 'u.username', '=', 'j.username')
            ->join('compan as c', 'c.compan_code', '=', 'j.compan_code')
            ->leftJoin('drivers as d', 'd.driver_id', '=', 'j.driver_id')
            ->join('juald as jd', 'jd.transaction_id', '=', 'j.transaction_id')
            ->select(
                'j.transaction_id',
                'u.name',
                'j.address',
                'c.name AS compan_name',
                'j.total_amount',
                DB::raw('SUM(jd.quantity) AS total_quantity'),
                DB::raw("CASE 
            WHEN j.status = 0 THEN 'Barang Belum Siap' 
            WHEN j.status = 1 THEN 'Barang Sudah Siap' 
            WHEN j.status = 2 THEN 'Barang Sedang Diantar' 
            ELSE 'Pesanan Selesai' 
            END AS status"),
                DB::raw("CASE 
            WHEN j.is_delivery = 1 THEN 'Dikirim' 
            ELSE 'Tidak Dikirim' 
            END AS is_delivery"),
                DB::raw("DATE_FORMAT(j.transaction_date, '%H:%i %d-%m-%Y') AS transaction_date"),
                DB::raw('IF(j.is_delivery = 1, IF(j.driver_id = 0 OR j.driver_id IS NULL, "Belum Ditetapkan", d.driver_name), "-") AS driver_name')
            )
            ->groupBy(
                'j.transaction_id',
                'u.name',
                'j.address',
                'c.name',
                'j.total_amount',
                'j.status',
                'j.is_delivery',
                'j.transaction_date',
                'j.driver_id',
                'd.driver_name'
            );

        return Datatables::of($query)
            ->addIndexColumn()
            ->editColumn('total_amount', function ($row) {
                return number_format($row->total_amount, 0, ',', '.');
            })
            ->filter(function ($query) use ($request) {
                if ($search = $request->input('search.value')) {
                    $query->where(function ($q) use ($search) {
                        $q->where('u.name', 'like', "%{$search}%")
                            ->orWhere('j.address', 'like', "%{$search}%")
                            ->orWhere('c.name', 'like', "%{$search}%")
                            ->orWhere('d.driver_name', 'like', "%{$search}%")
                            ->orWhere('j.total_amount', 'like', "%{$search}%")
                            ->orWhereRaw("(CASE 
                            WHEN j.is_delivery = 1 THEN 'Dikirim' 
                            ELSE 'Tidak Dikirim' 
                            END) LIKE ?", ["%{$search}%"])
                            ->orWhereRaw("IF(j.driver_id = 0 OR j.driver_id IS NULL, 'Belum Ditetapkan', d.driver_name) LIKE ?", ["%{$search}%"])
                            ->orWhereRaw("(CASE 
                            WHEN j.status = 0 THEN 'Barang Belum Siap' 
                            WHEN j.status = 1 THEN 'Barang Sudah Siap' 
                            WHEN j.status = 2 THEN 'Barang Sedang Diantar' 
                            ELSE 'Pesanan Selesai' 
                            END) LIKE ?", ["%{$search}%"]);
                    });
                }
            })
            ->addColumn('action', function ($row) {
                $btnPrivilege = '
        <a class="dropdown-item" href="' . url('transaksi/jual/edit/' . $row->transaction_id) . '">
            <i class="fas fa-pen text-primary"></i>&nbsp;&nbsp;&nbsp; Edit
        </a>
        <a class="dropdown-item" href="' . url('transaksi/jual/show/' . $row->transaction_id) . '">
            <i class="fas fa-boxes text-success"></i>&nbsp;&nbsp;&nbsp; Edit Stok
        </a>';

                return '
        <div class="dropdown show" style="text-align: center">
            <a class="btn btn-secondary dropdown-toggle btn-sm" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bars"></i>
            </a>
            <div class="dropdown-menu">' . $btnPrivilege . '</div>
        </div>';
            })
            ->setRowAttr([
                'class' => 'clickable-row',
                'data-id' => function ($row) {
                    return $row->transaction_id;
                }
            ])
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getJualDriver(Request $request)
    {
        if (!$request->has('draw')) {
            $columns = [
                ['data' => 'DT_RowIndex', 'title' => 'No.', 'orderable' => false, 'searchable' => false, 'width' => '20px', 'className' => 'dt-center'],
                ['data' => 'name', 'title' => 'Nama Konsumen', 'width' => '150px'],
                ['data' => 'address', 'title' => 'Alamat Konsumen',  'width' => '60px'],
                ['data' => 'compan_name', 'title' => 'Cabang', 'width' => '150px'],
                ['data' => 'transaction_date', 'title' => 'Waktu Transaksi', 'width' => '200px', 'className' => 'dt-center'],
                ['data' => 'total_amount', 'title' => 'Total Belanja', 'width' => '40px', 'className' => 'dt-right'],
                ['data' => 'total_quantity', 'title' => 'Total Barang', 'width' => '40px', 'className' => 'dt-center '],
                ['data' => 'driver_name', 'title' => 'Nama Driver', 'width' => '200px', 'className' => 'dt-center driver'],
            ];

            return response()->json(['columns' => $columns]);
        }

        // Query utama
        $query = DB::table('jual as j')
            ->join('users as u', 'u.username', '=', 'j.username')
            ->join('compan as c', 'c.compan_code', '=', 'j.compan_code')
            ->leftJoin('drivers as d', 'd.driver_id', '=', 'j.driver_id')
            ->join('juald as jd', 'jd.transaction_id', '=', 'j.transaction_id')

            ->select(
                'j.transaction_id',
                'u.name',
                'j.address',
                DB::raw('SUM(jd.quantity) AS total_quantity'),
                'c.name AS compan_name',
                'j.total_amount',
                DB::raw("DATE_FORMAT(j.transaction_date, '%H:%i:%s %d-%m-%Y') AS transaction_date"),
                DB::raw('IF(j.driver_id = 0 OR j.driver_id IS NULL, "Belum Ditentukan", d.driver_name) AS driver_name')
            )
            ->where('j.status', '=', '0')
            ->where('j.is_delivery', '=', '1')
            ->groupBy(
                'j.transaction_id',
                'u.name',
                'j.address',
                'c.name',
                'j.total_amount',
                'j.status',
                'j.is_delivery',
                'j.transaction_date',
                'j.driver_id',
                'd.driver_name'
            );

        return Datatables::of($query)
            ->addIndexColumn()
            ->editColumn('total_amount', function ($row) {
                return number_format($row->total_amount, 0, ',', '.');
            })
            ->editColumn('transaction_id', function ($row) {
                return '<input type="text" class="form-control transaction_id" required
                                        id="transaction_id" name="transaction_id"
                                        >' . $row->transaction_id . '</input>';
            })
            ->editColumn('driver_name', function ($row) {
                $drivers = DB::table('drivers')->select('*')->where('status', '=', '1')->where('manager', '=', '0')->get();

                $html = '<select name="driver[' . $row->transaction_id . ']">';
                $html .= '<option value="">-- Pilih Nama Driver --</option>';

                foreach ($drivers as $driver) {
                    $html .= '<option value="' . $driver->driver_id . '">' . $driver->driver_name . '</option>';
                }

                $html .= '</select>';

                return $html;
            })
            ->filter(function ($query) use ($request) {
                if ($search = $request->input('search.value')) {
                    $query->where(function ($q) use ($search) {
                        $q->where('u.name', 'like', "%{$search}%")
                            ->orWhere('j.address', 'like', "%{$search}%")
                            ->orWhere('c.name', 'like', "%{$search}%")
                            ->orWhere('d.driver_name', 'like', "%{$search}%")
                            ->orWhere('j.total_amount', 'like', "%{$search}%")
                            ->orWhereRaw("(CASE 
                                WHEN j.is_delivery = 1 THEN 'Dikirim' 
                                ELSE 'Tidak Dikirim' 
                                END) LIKE ?", ["%{$search}%"])
                            ->orWhereRaw("IF(j.driver_id = 0 OR j.driver_id IS NULL, 'Belum Ditentukan', d.driver_name) LIKE ?", ["%{$search}%"])
                            ->orWhereRaw("(CASE 
                                WHEN j.status = 0 THEN 'Barang Belum Siap' 
                                WHEN j.status = 1 THEN 'Barang Sudah Siap' 
                                WHEN j.status = 2 THEN 'Barang Sedang Diantar' 
                                ELSE 'Pesanan Selesai' 
                                END) LIKE ?", ["%{$search}%"]);
                    });
                }
            })
            ->setRowAttr([
                'class' => 'clickable-row',
                'data-id' => function ($row) {
                    return $row->transaction_id;
                }
            ])
            ->rawColumns(['driver_name'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('transaksi.jual.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    public function store(Request $request)
    {
        DB::beginTransaction(); // <-- tambahkan ini

        try {
            $assignedDrivers = collect($request->driver)->filter();

            foreach ($assignedDrivers as $transactionId => $driverId) {
                DB::table('jual')
                    ->where('transaction_id', $transactionId)
                    ->update([
                        'driver_id' => $driverId,
                        'status' => 1
                    ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Data barang berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with(
                'error',
                'Gagal menyimpan data: ' . $e->getMessage()
            );
        }
    }






    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 12

    public function show(Jual $id)
    {
        $name = DB::table('users')
            ->where('username', $id->username)
            ->value('name');
        $phone = DB::table('users')
            ->where('username', $id->username)
            ->value('phone');
        $email = DB::table('users')
            ->where('username', $id->username)
            ->value('email');
        $driver_name = DB::table('drivers')
            ->where('driver_id', $id->driver_id)
            ->value('driver_name');
        $compan_name = DB::table('compan')
            ->where('compan_code', $id->compan_code)
            ->value('name');
        $total_quantity = DB::table('jual as j')
            ->join('juald as jd', 'j.transaction_id', '=', 'jd.transaction_id')
            ->where('j.transaction_id', '=', $id->transaction_id)
            ->select(DB::raw('SUM(jd.quantity) AS total_quantity'))
            ->first();;
        $id->phone = $phone;
        $id->name = $name;
        $id->email = $email;
        $id->driver_name = $driver_name;
        $id->compan_name = $compan_name;
        $id->total_quantity = $total_quantity;
        $detailBarang = DB::table('juald as jd')
            ->join('brg as b', 'b.brg_id', '=', 'jd.brg_id')
            ->select('jd.*', 'b.nama', 'b.harga', 'b.satuan', 'b.url')
            ->where('transaction_id', '=', $id->transaction_id)
            ->get();

        $data = ['header' => $id, 'detail' => $detailBarang];
        // return redirect()->back()->with('success', 'Data barang berhasil disimpan.' . json_encode($data));


        return view('transaksi.jual.show', $data);
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

        $satuan = [
            ['value' => "buah", 'label' => 'Buah'],
            ['value' => "ons", 'label' => 'Ons'],
            ['value' => "kg", 'label' => 'KG'],
            ['value' => "ikat", 'label' => 'Ikat'],
            ['value' => "pack", 'label' => 'Pack'],
            ['value' => "pcs", 'label' => 'Pcs'],
            ['value' => "box", 'label' => 'Box'],
            ['value' => "roll", 'label' => 'Roll']
        ];
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
        ];
        return view('transaksi.jual.edit', $data);
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

            // Ambil nama kategori
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
