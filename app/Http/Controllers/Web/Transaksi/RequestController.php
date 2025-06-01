<?php

namespace App\Http\Controllers\Web\Transaksi;

use App\Http\Controllers\Controller;
use App\Models\Compan;
// ganti 1


use App\Models\Requests;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Else_;

include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";





// ganti 2
class RequestController extends Controller
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
        return view('transaksi.request.index');
    }

    // ganti 4

    public function getRequest(Request $request)
    {
        if (!$request->has('draw')) {
            $columns = [
                ['data' => 'DT_RowIndex', 'title' => 'No.', 'orderable' => false, 'searchable' => false, 'width' => '20px', 'className' => 'dt-center'],
                ['data' => 'REQUEST_DATE', 'title' => 'Waktu Request'],
                ['data' => 'username', 'title' => 'Username'],
                ['data' => 'PRODUCT_NAME', 'title' => 'Nama Produk'],
                ['data' => 'QUANTITY', 'title' => 'Jumlah Produk', 'className' => 'dt-center'],
                ['data' => 'PRICE', 'title' => 'Harga', 'className' => 'dt-center'],
                ['data' => 'status', 'title' => 'Status', 'className' => 'dt-center'],
                ['data' => 'compan_code', 'title' => 'Cabang'],
                ['data' => 'action', 'title' => 'Action', 'orderable' => false, 'width' => '66px', 'searchable' => false, 'className' => 'dt-center'],
            ];

            return response()->json(['columns' => $columns]);
        }

        $id = Requests::orderBy('request_date', 'desc')->get();

        return Datatables::of($id)
            ->addIndexColumn()
            ->editColumn('price', function ($row) {
                if ($row['price'] == 0 || $row['price'] == null) {
                    return '-';
                }
                return $row['price'];
            })->editColumn('compan_code', function ($row) {
                if ($row['compan_code'] == 0) {
                    return '-';
                }
                return Compan::where('compan_code', $row['compan_code'])->value('name');
            })
            ->addColumn('action', function ($row) {

                return '
                <div class="btn-group" role="group" aria-label="Aksi">
                    <a href="' . url('transaksi/request/edit/' . $row->REQUEST_ID) . '" class="btn btn-sm btn-primary mx-2"  style="border-radius: 10px;">
                        <i class="fas fa-pen"></i> Edit
                    </a>
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
        $this->pushToBackStack(['/transaksi/request/create', '/transaksi/request/store']);
        $form = [

            ['label' => 'Nama Requests', 'value' => 'nama', 'type' => 'string'],
        ];
        $data = ['backUrl' => $this->popBackStack(), 'forms' => $form];
        return view('transaksi.request.create', $data);
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
        ]);

        try {
            DB::beginTransaction();
            $id = new Requests();
            $id->nama = ucwords(strtolower($validated['nama']));
            $id->save();

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

    public function show(Requests $id)
    {
        $this->pushToBackStack(['/transaksi/request/show']);


        $form = [
            ['label' => 'Nama Requests', 'value' => 'nama', 'type' => 'string'],
        ];
        $id['primaryKey'] = $id['id'];

        $data = [
            'backUrl' => $this->popBackStack(),
            'data'        => $id,
            'forms'        => $form,
        ];


        return view('transaksi.request.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 15

    public function edit(Requests $id)
    {
        $this->pushToBackStack(skipPatterns: ['/transaksi/request/edit', '/transaksi/request/update']);
        $form = [
            ['label' => 'Waktu Request', 'value' => 'REQUEST_DATE', 'type' => 'string'],
            ['label' => 'Username', 'value' => 'username', 'type' => 'string'],
            ['label' => 'Nama Produk', 'value' => 'PRODUCT_NAME', 'type' => 'string'],
            ['label' => 'Jumlah Produk', 'value' => 'QUANTITY', 'type' => 'string'],
            ['label' => 'Harga', 'value' => 'PRICE', 'type' => 'number', 'readonly' => false],
            ['label' => 'Cabang', 'value' => 'compan_code', 'type' => 'selection', 'readonly' => false],
        ];
        $listCabang = DB::table('compan')
            ->select('compan_code AS value', 'name as label')
            ->get()
            ->map(function ($item) {
                $item->label = ucwords(strtolower(preg_replace_callback(
                    '/[^\s\-&]+/',
                    fn($matches) => ucfirst($matches[0]),
                    $item->label
                )));
                return $item;
            });
        $id['primaryKey'] = $id['REQUEST_ID'];
        $form = [
            ['label' => 'Waktu Request', 'value' => 'REQUEST_DATE', 'type' => 'string'],
            ['label' => 'Username', 'value' => 'username', 'type' => 'string'],
            ['label' => 'Nama Produk', 'value' => 'PRODUCT_NAME', 'type' => 'string'],
            ['label' => 'Jumlah Produk', 'value' => 'QUANTITY', 'type' => 'string'],
            [
                'label' => 'Terima Permintaan Produk',
                'value' => 'accepted',
                'type' => 'selection',
                'readonly' => false,
                'option' => [['value' => 1, 'label' => 'Diterima'], ['value' => 0, 'label' => 'Ditolak']]
            ],
            [
                'label' => 'Harga',
                'value' => 'PRICE',
                'type' => 'number',
                'readonly' => false,
                'checkbox' => true,
                'checkboxForm' => ['label' => 'Harga Satuan?', 'value' => 'satuan', 'type' => 'checkbox', 'readonly' => false]
            ],
            ['label' => 'Cabang', 'value' => 'compan_code', 'type' => 'selection', 'readonly' => false, 'option' => $listCabang],

        ];
        $data = [
            'backUrl' => $this->popBackStack(),
            'data'        => $id,
            'forms'        => $form,
        ];
        return view('transaksi.request.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 18



    public function update(Request $request, Requests $id)
    {

        $validated = $request->validate([
            'PRICE' => 'nullable|string|max:255',
            'accepted' => 'required',
            'satuan' => 'nullable',
            'compan_code' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            if ($validated['accepted'] == '1') {
                $price = str_replace('.', '', $validated['PRICE']);
                $id->PRICE = (!empty($validated['satuan'])) ? $price * $id->QUANTITY : $price;


                $id->compan_code = $validated['compan_code'];
                $id->status = 'Waiting';
            } else {
                $id->status = 'Rejected';
            }
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

    public function destroy(Requests $id)
    {

        $id->delete();

        return redirect()->back()->with('success', 'Data barang dan distribusinya berhasil dihapus.');
    }
}
