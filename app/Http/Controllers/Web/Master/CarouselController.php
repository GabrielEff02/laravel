<?php

namespace App\Http\Controllers\Web\Master;

use App\Http\Controllers\Controller;
use App\Models\Carousel;
// ganti 1

use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;



include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";





// ganti 2
class CarouselController extends Controller
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
        return view('master.carousel.index');
    }

    // ganti 4

    public function getCarousel(Request $request)
    {
        // Cek apakah ini request DataTable (pakai parameter draw)
        if (!$request->has('draw')) {
            $columns = [
                ['data' => 'DT_RowIndex', 'title' => 'No.', 'orderable' => false, 'searchable' => false, 'width' => '20px', 'className' => 'dt-center'],
                ['data' => 'title', 'title' => 'Nama Carousel'],
                ['data' => 'image_url', 'title' => 'url',  'width' => '200px'],
                ['data' => 'action', 'title' => 'Action', 'orderable' => false, 'width' => '200px', 'searchable' => false],

            ];

            return response()->json(['columns' => $columns]);
        }

        // Kalau ini request datatable (ada draw), baru ambil data
        $id = Carousel::get();

        return Datatables::of($id)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '
                <div class="btn-group" role="group" aria-label="Aksi">
                    <a href="' . url('master/carousel/edit/' . $row->id) . '" class="btn btn-sm btn-primary mx-2" style="border-radius: 10px;">
                        <i class="fas fa-pen"></i> Edit
                    </a>
                    <a href="' . url('master/carousel/show/' . $row->id) . '" class="btn btn-sm btn-success mx-2" style="border-radius: 10px;">
                        <i class="fas fa-eye"></i> Lihat
                    </a>
                    <a href="' . url('master/carousel/delete/' . $row->id) . '" 
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
        $this->pushToBackStack(['/master/carousel/create', '/master/carousel/store']);

        $form = [
            ['label' => 'Nama Carousel', 'value' => 'title', 'type' => 'string'],
            ['label' => 'Gambar Carousel', 'value' => 'url', 'type' => 'image', 'path' => 'img/carousel/'],
        ];
        $data = ['backUrl' => $this->popBackStack(), 'forms' => $form];
        return view('master.carousel.create', $data);
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
            'title' => 'required|string',
            'url' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $title = ucwords(strtolower($validated['title']));
            $cleanTitle = preg_replace('/[^a-z0-9]/i', '', strtolower($validated['title']));

            $file = $request->file('url');
            if ($file) {
                $extension = $file->getClientOriginalExtension();
                $fileName = $cleanTitle . '.' . $extension;

                $file->storeAs('public/img/carousel/' . $fileName);
                $file->move(public_path('img/carousel/'), $fileName);
                $imagePath = $fileName;
            } else {
                $imagePath = '';
            }

            $id = new Carousel();
            $id->title = $title;
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

    public function show(Carousel $id)
    {
        $this->pushToBackStack(['/master/carousel/show']);

        $form = [
            ['label' => 'Nama Carousel', 'value' => 'title', 'type' => 'string'],
            ['label' => 'Gambar Carousel', 'value' => 'image_url', 'type' => 'image', 'path' => 'img/carousel/'],
        ];
        $id['primaryKey'] = $id['id'];

        $data = [
            'backUrl' => $this->popBackStack(),
            'data'        => $id,
            'forms'        => $form,
        ];


        return view('master.carousel.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 15

    public function edit(Carousel $id)
    {
        $this->pushToBackStack(skipPatterns: ['/master/carousel/edit', '/master/carousel/update']);
        $form = [
            ['label' => 'Nama Carousel', 'value' => 'title', 'type' => 'string'],
            ['label' => 'Gambar Carousel', 'value' => 'image_url', 'type' => 'image', 'path' => 'img/carousel/'],
        ];
        $id['primaryKey'] = $id['id'];

        $data = [
            'backUrl' => $this->popBackStack(),
            'data'        => $id,
            'forms'        => $form,
        ];
        return view('master.carousel.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 18



    public function update(Request $request, Carousel $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        DB::beginTransaction();

        try {


            // Format nama folder & nama file
            $cleanProduct = preg_replace('/[^a-z0-9]/i', '', strtolower($request->title));
            $path = public_path('img/carousel/');
            if ($request->hasFile('image_url')) {
                // Hapus gambar lama jika ada
                if ($id->category_image && file_exists(public_path('img/carousel/' . $id->image_url))) {
                    unlink(public_path('img/carousel/' . $id->image_url));
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
            $id->title = $request->title;
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

    public function destroy(Carousel $id)
    {

        $id->delete();

        return redirect()->back()->with('success', 'Data barang dan distribusinya berhasil dihapus.');
    }
}
