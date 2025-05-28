<?php

namespace App\Http\Controllers\Web\Master;

use App\Http\Controllers\Controller;
// ganti 1


use App\Models\Satuan;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;



include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";





// ganti 2
class SatuanController extends Controller
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
        return view('master.satuan.index');
    }

    // ganti 4

    public function getSatuan(Request $request)
    {
        // Cek apakah ini request DataTable (pakai parameter draw)
        if (!$request->has('draw')) {
            $columns = [
                ['data' => 'DT_RowIndex', 'title' => 'No.', 'orderable' => false, 'searchable' => false, 'width' => '20px', 'className' => 'dt-center'],
                ['data' => 'nama', 'title' => 'Nama Satuan'],
                ['data' => 'action', 'title' => 'Action', 'orderable' => false, 'width' => '200px', 'searchable' => false],
            ];

            return response()->json(['columns' => $columns]);
        }

        // Kalau ini request datatable (ada draw), baru ambil data
        $id = Satuan::get();

        return Datatables::of($id)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {



                return '
                <div class="btn-group" role="group" aria-label="Aksi">
                    <a href="' . url('master/satuan/edit/' . $row->id) . '" class="btn btn-sm btn-primary mx-2"  style="border-radius: 10px;">
                        <i class="fas fa-pen"></i> Edit
                    </a>
                    <a href="' . url('master/satuan/show/' . $row->id) . '" class="btn btn-sm btn-success mx-2"  style="border-radius: 10px;">
                        <i class="fas fa-eye"></i> Lihat
                    </a>
                    <a href="' . url('master/satuan/delete/' . $row->id) . '" 
                    class="btn btn-sm btn-danger mx-2"  style="border-radius: 10px;"
                    onclick="return confirm(\'Apakah anda yakin ingin hapus?\')">
                        <i class="fas fa-trash-alt"></i> Hapus
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
        $this->pushToBackStack(['/master/satuan/create', '/master/satuan/store']);
        $form = [

            ['label' => 'Nama Satuan', 'value' => 'nama', 'type' => 'string'],
        ];
        $data = ['backUrl' => $this->popBackStack(), 'forms' => $form];
        return view('master.satuan.create', $data);
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
            $id = new Satuan();
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

    public function show(Satuan $id)
    {
        $this->pushToBackStack(['/master/satuan/show']);


        $form = [
            ['label' => 'Nama Satuan', 'value' => 'nama', 'type' => 'string'],
        ];
        $id['primaryKey'] = $id['id'];

        $data = [
            'backUrl' => $this->popBackStack(),
            'data'        => $id,
            'forms'        => $form,
        ];


        return view('master.satuan.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 15

    public function edit(Satuan $id)
    {
        $this->pushToBackStack(skipPatterns: ['/master/satuan/edit', '/master/satuan/update']);
        $form = [
            ['label' => 'Nama Satuan', 'value' => 'nama', 'type' => 'string'],
        ];
        $id['primaryKey'] = $id['id'];

        $data = [
            'backUrl' => $this->popBackStack(),
            'data'        => $id,
            'forms'        => $form,
        ];
        return view('master.satuan.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 18



    public function update(Request $request, Satuan $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {



            $id->nama = $request->nama;
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

    public function destroy(Satuan $id)
    {

        $id->delete();

        return redirect()->back()->with('success', 'Data barang dan distribusinya berhasil dihapus.');
    }
}
