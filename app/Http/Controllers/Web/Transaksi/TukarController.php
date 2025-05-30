<?php

namespace App\Http\Controllers\Web\Transaksi;

use App\Http\Controllers\Controller;
// ganti 1

use App\Models\Tukar;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\DB;



include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";





// ganti 2
class TukarController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {


        // ganti 3
        return view('transaksi.tukar.index');
    }

    // ganti 4

    public function getTukar(Request $request)
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
                ['data' => 'driver_name', 'title' => 'Nama Driver', 'width' => '200px',]
            ];

            return response()->json(['columns' => $columns]);
        }

        // Query utama
        $query = DB::table('transactions as j')
            ->join('users as u', 'u.username', '=', 'j.username')
            ->join('compan as c', 'c.compan_code', '=', 'j.compan_code')
            ->leftJoin('drivers as d', 'd.driver_id', '=', 'j.driver_id')
            ->join('transaction_items as jd', 'jd.transaction_id', '=', 'j.transaction_id')
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
            ->setRowAttr([
                'class' => 'clickable-row',
                'data-id' => function ($row) {
                    return $row->transaction_id;
                }
            ])
            ->make(true);
    }

    public function getTukarKirim(Request $request)
    {
        if (!$request->has('draw')) {
            $columns = [
                ['data' => 'DT_RowIndex', 'title' => 'No.', 'orderable' => false, 'searchable' => false, 'width' => '20px', 'className' => 'dt-center'],
                ['data' => 'name', 'title' => 'Nama Konsumen', 'width' => '150px'],
                ['data' => 'address', 'title' => 'Alamat Konsumen',  'width' => '200px'],
                ['data' => 'compan_name', 'title' => 'Cabang', 'width' => '150px'],
                ['data' => 'transaction_date', 'title' => 'Waktu Transaksi', 'width' => '200px', 'className' => 'dt-center'],
                ['data' => 'total_amount', 'title' => 'Total Belanja', 'width' => '100px', 'className' => 'dt-right'],
                ['data' => 'total_quantity', 'title' => 'Total Barang', 'width' => '100px', 'className' => 'dt-center '],
                ['data' => 'driver_name', 'title' => 'Nama Driver', 'width' => '200px', 'className' => 'dt-center driver'],
            ];

            return response()->json(['columns' => $columns]);
        }

        // Query utama
        $query = DB::table('transactions as j')
            ->join('users as u', 'u.username', '=', 'j.username')
            ->join('compan as c', 'c.compan_code', '=', 'j.compan_code')
            ->leftJoin('drivers as d', 'd.driver_id', '=', 'j.driver_id')
            ->join('transaction_items as jd', 'jd.transaction_id', '=', 'j.transaction_id')

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

                $html = '<select class="form-control  mr-1 "  name="driver[' . $row->transaction_id . ']">';
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
    public function getTukarAmbil(Request $request)
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
                ['data' => 'checkbox', 'title' => 'Barang Sudah Siap?', 'width' => '60px', 'orderable' => false, 'searchable' => false, 'className' => 'dt-center']
            ];

            return response()->json(['columns' => $columns]);
        }

        // Query utama
        $query = DB::table('transactions as j')
            ->join('users as u', 'u.username', '=', 'j.username')
            ->join('compan as c', 'c.compan_code', '=', 'j.compan_code')
            ->join('transaction_items as jd', 'jd.transaction_id', '=', 'j.transaction_id')

            ->select(
                'j.transaction_id',
                'u.name',
                'j.address',
                DB::raw('SUM(jd.quantity) AS total_quantity'),
                'c.name AS compan_name',
                'j.total_amount',
                'j.status',
                DB::raw("DATE_FORMAT(j.transaction_date, '%H:%i:%s %d-%m-%Y') AS transaction_date")
            )
            ->where('j.status', '=', '0')
            ->where('j.is_delivery', '=', '0')
            ->groupBy(
                'j.transaction_id',
                'u.name',
                'j.address',
                'c.name',
                'j.total_amount',
                'j.transaction_date',
                'j.status'
            );

        return Datatables::of($query)
            ->addIndexColumn()
            ->editColumn('total_amount', function ($row) {
                return number_format($row->total_amount, 0, ',', '.');
            })
            ->editColumn('checkbox', function ($row) {
                return '
                    <input type="checkbox"
                        style="transform: scale(1.5);"
                        class="form-check-input row-checkbox"
                        id="status[' . $row->transaction_id  . ']"
                        name="status[' . $row->transaction_id  . ']"
                        value="1">
                    ';
            })

            ->filter(function ($query) use ($request) {
                if ($search = $request->input('search.value')) {
                    $query->where(function ($q) use ($search) {
                        $q->where('u.name', 'like', "%{$search}%")
                            ->orWhere('j.address', 'like', "%{$search}%")
                            ->orWhere('c.name', 'like', "%{$search}%")
                            ->orWhere('j.total_amount', 'like', "%{$search}%");
                    });
                }
            })
            ->setRowAttr([
                'class' => 'clickable-row',
                'data-id' => function ($row) {
                    return $row->transaction_id;
                }
            ])
            ->rawColumns(['checkbox'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('transaksi.tukar.create');
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
                DB::table('transactions')
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

    public function show(Tukar $id)
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
        $total_quantity = DB::table('transactions as j')
            ->join('transaction_items as jd', 'j.transaction_id', '=', 'jd.transaction_id')
            ->where('j.transaction_id', '=', $id->transaction_id)
            ->select(DB::raw('SUM(jd.quantity) AS total_quantity'))
            ->first();


        $id->phone = $phone;
        $id->name = $name;
        $id->email = $email;
        $id->driver_name = $driver_name;
        $id->compan_name = $compan_name;
        $id->total_quantity = $total_quantity;
        $detailBarang = DB::table('transaction_items as jd')
            ->join('products as b', 'b.product_id', '=', 'jd.product_id')
            ->select(
                'jd.product_id',
                'b.product_name',
                'b.price',
                'b.image_url',
                DB::raw('SUM(jd.quantity) as quantity'),
                DB::raw('SUM(jd.total_price) as total_price')
            )
            ->where('jd.transaction_id', '=', $id->transaction_id)
            ->groupBy('jd.product_id', 'b.product_name', 'b.price', 'b.image_url')
            ->get();


        $data = ['header' => $id, 'detail' => $detailBarang];
        // return redirect()->back()->with('success', 'Data barang berhasil disimpan.' . json_encode($data));


        return view('transaksi.tukar.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 15

    public function edit()
    {

        return view('transaksi.tukar.edit',);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 18



    public function update(Request $request)
    {

        DB::beginTransaction(); // <-- tambahkan ini

        try {
            $assignedDrivers = collect($request->status)->filter();

            foreach ($assignedDrivers as $transactionId => $status) {
                DB::table('transactions')
                    ->where('transaction_id', $transactionId)
                    ->update([
                        'status' => $status
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
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 22


}
