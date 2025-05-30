<?php

namespace App\Http\Controllers\Web\Master;

use App\Http\Controllers\Controller;
// ganti 1


use App\Models\Compan;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;



include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";





// ganti 2
class CompanController extends Controller
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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->pushToBackStack();

        // ganti 3
        return view('master.compan.index');
    }

    // ganti 4

    public function getCompan(Request $request)
    {
        // Cek apakah ini request DataTable (pakai parameter draw)
        if (!$request->has('draw')) {
            $columns = [
                ['data' => 'DT_RowIndex', 'title' => 'No.', 'orderable' => false, 'searchable' => false, 'width' => '20px', 'className' => 'dt-center'],
                ['data' => 'compan_code', 'title' => 'Kode Perusahaan'],
                ['data' => 'name', 'title' => 'Nama Perusahaan'],
                ['data' => 'address', 'title' => 'Alamat Perusahaan'],
                ['data' => 'phone', 'title' => 'Nomor Perusahaan'],
                ['data' => 'image_url', 'title' => 'url',  'width' => '200px'],
                ['data' => 'lat', 'title' => 'latitude',  'width' => '200px'],
                ['data' => 'lang', 'title' => 'longitude',  'width' => '200px'],
                ['data' => 'action', 'title' => 'Action', 'orderable' => false, 'width' => '200px', 'searchable' => false],

            ];

            return response()->json(['columns' => $columns]);
        }

        // Kalau ini request datatable (ada draw), baru ambil data
        $id = Compan::get();

        return Datatables::of($id)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '
                <div class="btn-group" role="group" aria-label="Aksi">
                    <a href="' . url('master/compan/edit/' . $row->id) . '" class="btn btn-sm btn-primary mx-2" style="border-radius: 10px;">
                        <i class="fas fa-pen"></i> Edit
                    </a>
                    <a href="' . url('master/compan/show/' . $row->id) . '" class="btn btn-sm btn-success mx-2" style="border-radius: 10px;">
                        <i class="fas fa-eye"></i> Lihat
                    </a>
                    <a href="' . url('master/compan/delete/' . $row->id) . '" 
                    class="btn btn-sm btn-danger mx-2 " style="border-radius: 10px;"
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
        $this->pushToBackStack(['/master/compan/create', '/master/compan/store']);

        $form = [
            ['label' => 'Kode Perusahaan', 'value' => 'compan_code', 'type' => 'string'],
            ['label' => 'Nama Perusahaan', 'value' => 'name', 'type' => 'string'],
            ['label' => 'Alamat Perusahaan', 'value' => 'address', 'type' => 'string'],
            ['label' => 'Nomor Telepon', 'value' => 'phone', 'type' => 'number'],
            ['label' => 'Latitude', 'value' => 'lat', 'type' => 'number'],
            ['label' => 'Longitude', 'value' => 'lang', 'type' => 'number'],
            ['label' => 'Gambar Perusahaan', 'value' => 'image_url', 'type' => 'image', 'path' => 'img/compan/'],
        ];
        $data = ['backUrl' => $this->popBackStack(), 'forms' => $form];
        return view('master.compan.create', $data);
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
            'compan_code' => 'required|string',
            'name' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'lat' => 'required|string',
            'lang' => 'required|string',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $name = ucwords(strtolower($validated['name']));
            $cleanName = preg_replace('/[^a-z0-9]/i', '', strtolower($validated['name']));

            $file = $request->file('url');
            if ($file) {
                $extension = $file->getClientOriginalExtension();
                $fileName = $cleanName . '.' . $extension;

                $file->storeAs('public/img/compan/' . $fileName);
                $file->move(public_path('img/compan/'), $fileName);
                $imagePath = $fileName;
            } else {
                $imagePath = '';
            }

            $id = new Compan();
            $id->name = $name;
            $id->compan_code = $validated['compan_code'];
            $id->address = $validated['address'];
            $id->phone = $validated['phone'];
            $id->lat = $validated['lat'];
            $id->lang = $validated['lang'];
            $id->code = MD5($name);
            $id->image_url = $imagePath;
            $id->save();



            DB::commit();
            return redirect()->back()->with('success', 'Data berhasil disimpan.');
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

    public function show(Compan $id)
    {
        $this->pushToBackStack(['/master/compan/show']);

        $form = [
            ['label' => 'Kode Perusahaan', 'value' => 'compan_code', 'type' => 'string'],
            ['label' => 'Nama Perusahaan', 'value' => 'name', 'type' => 'string'],
            ['label' => 'Alamat Perusahaan', 'value' => 'address', 'type' => 'textarea'],
            ['label' => 'Nomor Telepon', 'value' => 'phone', 'type' => 'number'],
            ['label' => 'Latitude', 'value' => 'lat', 'type' => 'number'],
            ['label' => 'Longitude', 'value' => 'lang', 'type' => 'number'],
            ['label' => 'Gambar Perusahaan', 'value' => 'image_url', 'type' => 'image', 'path' => 'img/compan/'],
            ['label' => 'Barcode', 'value' => 'code', 'type' => 'barcode'],
        ];
        $id['primaryKey'] = $id['id'];

        $data = [
            'backUrl' => $this->popBackStack(),
            'data'        => $id,
            'forms'        => $form,
        ];


        return view('master.compan.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 15

    public function edit(Compan $id)
    {
        $this->pushToBackStack(skipPatterns: ['/master/compan/edit', '/master/compan/update']);
        $form = [
            ['label' => 'Kode Perusahaan', 'value' => 'compan_code', 'type' => 'string'],
            ['label' => 'Nama Perusahaan', 'value' => 'name', 'type' => 'string'],
            ['label' => 'Alamat Perusahaan', 'value' => 'address', 'type' => 'string'],
            ['label' => 'Nomor Telepon', 'value' => 'phone', 'type' => 'number'],
            ['label' => 'Latitude', 'value' => 'lat', 'type' => 'number'],
            ['label' => 'Longitude', 'value' => 'lang', 'type' => 'number'],
            ['label' => 'Gambar Perusahaan', 'value' => 'image_url', 'type' => 'image', 'path' => 'img/compan/'],
            ['label' => 'Barcode', 'value' => 'code', 'type' => 'barcode'],
        ];
        $id['primaryKey'] = $id['id'];

        $data = [
            'backUrl' => $this->popBackStack(),
            'data'        => $id,
            'forms'        => $form,
        ];
        return view('master.compan.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 18



    public function update(Request $request, Compan $id)
    {
        $validated = $request->validate([
            'compan_code' => 'required|string',
            'name' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'lat' => 'required|string',
            'lang' => 'required|string',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        DB::beginTransaction();

        try {

            $name = ucwords(strtolower($validated['name']));
            $cleanName = preg_replace('/[^a-z0-9]/i', '', strtolower($validated['name']));

            $path = public_path('img/compan/');
            if ($request->hasFile('image_url')) {
                // Hapus gambar lama jika ada
                if ($id->category_image && file_exists(public_path('img/compan/' . $id->image_url))) {
                    unlink(public_path('img/compan/' . $id->image_url));
                }

                $file = $request->file('image_url');
                $extension = $file->getClientOriginalExtension();
                $fileName = $cleanName . '_' . time() . '.' . $extension;

                // Simpan file
                $file->move($path, $fileName);

                // Simpan path relatif ke database
                $id->image_url = $fileName;
            }

            $id->name = $name;
            $id->compan_code = $validated['compan_code'];
            $id->address = $validated['address'];
            $id->phone = $validated['phone'];
            $id->lat = $validated['lat'];
            $id->lang = $validated['lang'];
            $id->code = MD5($name);
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

    public function destroy(Compan $id)
    {

        $id->delete();

        return redirect()->back()->with('success', 'Data barang dan distribusinya berhasil dihapus.');
    }
}
