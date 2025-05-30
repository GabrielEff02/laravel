<?php

namespace App\Http\Controllers\Web\Master;

use App\Http\Controllers\Controller;
// ganti 1


use App\Models\Kategori;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;



include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";





// ganti 2
class KategoriController extends Controller
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
        return view('master.kategori.index');
    }

    // ganti 4

    public function getKategori(Request $request)
    {
        // Cek apakah ini request DataTable (pakai parameter draw)
        if (!$request->has('draw')) {
            $columns = [
                ['data' => 'DT_RowIndex', 'title' => 'No.', 'orderable' => false, 'searchable' => false, 'width' => '20px', 'className' => 'dt-center'],
                ['data' => 'category_name', 'title' => 'Nama Kategori'],
                ['data' => 'category_image', 'title' => 'url',  'width' => '200px'],
                ['data' => 'action', 'title' => 'Action', 'orderable' => false, 'width' => '200px', 'searchable' => false],

            ];

            return response()->json(['columns' => $columns]);
        }

        // Kalau ini request datatable (ada draw), baru ambil data
        $id = Kategori::get();

        return Datatables::of($id)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '
                <div class="btn-group" role="group" aria-label="Aksi">
                    <a href="' . url('master/kategori/edit/' . $row->category_id) . '" class="btn btn-sm btn-primary mx-2" style="border-radius: 10px;">
                        <i class="fas fa-pen"></i> Edit
                    </a>
                    <a href="' . url('master/kategori/show/' . $row->category_id) . '" class="btn btn-sm btn-success mx-2" style="border-radius: 10px;">
                        <i class="fas fa-eye"></i> Lihat
                    </a>
                    <a href="' . url('master/kategori/delete/' . $row->category_id) . '" 
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
        $this->pushToBackStack(['/master/kategori/create', '/master/kategori/store']);

        $form = [
            ['label' => 'Nama Kategori', 'value' => 'category_name', 'type' => 'string'],
            ['label' => 'Gambar Kategori', 'value' => 'url', 'type' => 'image', 'path' => 'img/kategori/'],
        ];
        $data = ['backUrl' => $this->popBackStack(), 'forms' => $form];
        return view('master.kategori.create', $data);
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
            'category_name' => 'required|string',
            'url' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $category_name = ucwords(strtolower($validated['category_name']));
            $cleanCategory = preg_replace('/[^a-z0-9]/i', '', strtolower($validated['category_name']));

            $file = $request->file('url');
            if ($file) {
                $extension = $file->getClientOriginalExtension();
                $fileName = $cleanCategory . '.' . $extension;

                $file->storeAs('public/img/kategori/' . $fileName);
                $file->move(public_path('img/kategori/'), $fileName);
                $imagePath = $fileName;
            } else {
                $imagePath = '';
            }

            $id = new Kategori();
            $id->category_name = $category_name;
            $id->category_image = $imagePath;
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

    public function show(Kategori $id)
    {
        $this->pushToBackStack(['/master/kategori/show']);

        $form = [
            ['label' => 'Nama Kategori', 'value' => 'category_name', 'type' => 'string'],
            ['label' => 'Gambar Kategori', 'value' => 'category_image', 'type' => 'image', 'path' => 'img/kategori/'],
        ];
        $id['primaryKey'] = $id['category_id'];

        $data = [
            'backUrl' => $this->popBackStack(),
            'data'        => $id,
            'forms'        => $form,
        ];


        return view('master.kategori.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 15

    public function edit(Kategori $id)
    {
        $this->pushToBackStack(skipPatterns: ['/master/kategori/edit', '/master/kategori/update']);
        $form = [
            ['label' => 'Nama Kategori', 'value' => 'category_name', 'type' => 'string'],
            ['label' => 'Gambar Kategori', 'value' => 'category_image', 'type' => 'image', 'path' => 'img/kategori/'],
        ];
        $id['primaryKey'] = $id['category_id'];

        $data = [
            'backUrl' => $this->popBackStack(),
            'data'        => $id,
            'forms'        => $form,
        ];
        return view('master.kategori.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 18



    public function update(Request $request, Kategori $id)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
            'category_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        DB::beginTransaction();

        try {


            // Format nama folder & nama file
            $cleanProduct = preg_replace('/[^a-z0-9]/i', '', strtolower($request->category_name));
            $path = public_path('img/kategori/');
            if ($request->hasFile('category_image')) {
                // Hapus gambar lama jika ada
                if ($id->category_image && file_exists(public_path('img/kategori/' . $id->category_image))) {
                    unlink(public_path('img/kategori/' . $id->category_image));
                }

                $file = $request->file('category_image');
                $extension = $file->getClientOriginalExtension();
                $fileName = $cleanProduct . '_' . time() . '.' . $extension;

                // Simpan file
                $file->move($path, $fileName);

                // Simpan path relatif ke database
                $id->category_image = $fileName;
            }

            // Simpan data lainnya
            $id->category_name = $request->category_name;
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

    public function destroy(Kategori $id)
    {

        $id->delete();

        return redirect()->back()->with('success', 'Data barang dan distribusinya berhasil dihapus.');
    }
}
