<?php

namespace App\Http\Controllers\Web\Master;

use App\Http\Controllers\Controller;
// ganti 1

use App\Models\Driver;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;



include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";

use PHPJasperXML;




// ganti 2
class DriverController extends Controller
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
        return view('master.driver.index');
    }

    // ganti 4

    public function getDriver(Request $request)
    {
        if (!$request->has('draw')) {
            $columns = [
                ['data' => 'DT_RowIndex', 'title' => 'No.', 'orderable' => false, 'searchable' => false, 'className' => 'dt-center', 'width' => '20px'],
                ['data' => 'username', 'title' => 'Username', 'width' => '100px'],
                ['data' => 'driver_name', 'title' => 'Nama Driver', 'width' => '150px'],
                ['data' => 'address', 'title' => 'Alamat', 'width' => '200px'],
                ['data' => 'phone', 'title' => 'Nomor Telepon', 'className' => 'dt-center', 'width' => '100px'],
                ['data' => 'email', 'title' => 'Email', 'width' => '100px'],
                ['data' => 'license_number', 'title' => 'Plat Nomor', 'className' => 'dt-center', 'width' => '80px'],
                ['data' => 'status', 'title' => 'Status', 'width' => '50px', 'className' => 'dt-center'],
                ['data' => 'manager', 'title' => 'Manager', 'width' => '50px', 'className' => 'dt-center'],
                ['data' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'className' => 'dt-center', 'width' => '40px'],
            ];

            return response()->json(['columns' => $columns]);
        }



        $driver = DB::table('drivers')->select('*')->get();



        return Datatables::of($driver)
            ->addIndexColumn()
            ->editColumn('license_number', function ($row) {
                preg_match('/^([A-Za-z]+)(\d+)([A-Za-z]+)$/', $row->license_number, $matches);

                if ($matches) {
                    $huruf_depan = $matches[1];  // L
                    $angka_tengah = $matches[2]; // 1213
                    $huruf_belakang = $matches[3]; // JKL

                    return $huruf_depan . " " . $angka_tengah . " " . $huruf_belakang;
                    // Output: L 1213 JKL
                } else {
                    return $row->license_number;
                }
            })
            ->editColumn('status', function ($row) {
                return $row->status == 1 ? 'Aktif' : 'Tidak Aktif';
            })
            ->editColumn('manager', function ($row) {
                return $row->manager == 1 ? '<i class="fas fa-check"></i>' : '';
            })
            ->addColumn('action', function ($row) {

                $btnPrivilege = '<a class="dropdown-item" href="' . url('master/driver/edit/' . $row->driver_id) . '">
                        <i class="fas fa-pen text-primary"></i>&nbsp&nbsp;&nbsp;; Edit
                    </a>
                    <hr>
                    <a class="dropdown-item text-danger" onclick="return confirm(&quot;Apakah anda yakin ingin hapus?&quot;)" href="' . url('master/driver/delete/' . $row->driver_id) . '">
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
            ->rawColumns(['action', 'manager'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->pushToBackStack(['/master/driver/create', '/master/driver/store']);
        $form = [
            ['label' => 'Username', 'value' => 'username', 'type' => 'string'],
            ['label' => 'Nama Driver', 'value' => 'name', 'type' => 'string'],
            ['label' => 'Alamat', 'value' => 'address', 'type' => 'string'],
            ['label' => 'Nomor Telepon', 'value' => 'phone', 'type' => 'number'],
            ['label' => 'Email', 'value' => 'email', 'type' => 'string'],
            ['label' => 'Plat Nomor', 'value' => 'license_number', 'type' => 'string'],
            ['label' => 'Password', 'value' => 'password', 'type' => 'password'],
            ['label' => 'Konfirmasi Password', 'value' => 'password_confirmation', 'type' => 'password'],
            ['label' => 'Status', 'value' => 'status', 'type' => 'selection', 'option' => [['value' => 1, 'label' => 'Aktif'], ['value' => 0, 'label' => 'Tidak Aktif']]],

        ];

        return view('master.driver.create', ['backUrl' => $this->popBackStack(), 'forms' => $form]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:users,username',
            'name' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email',
            'license_number' => 'required|string|max:20',
            'password' => 'required|confirmed|min:6',
            'status' => 'required|in:0,1',
        ]);
        try {
            Driver::create([
                'username' => $request->username,
                'driver_name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
                'license_number' => $request->license_number,
                'password' => MD5($request->password),
                'status' => $request->status,
            ]);

            return redirect()->back()->with('success', 'Driver berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }





    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 12

    public function resetPassword($id)
    {
        $driver = Driver::where('driver_id', $id)->firstOrFail();
        $driver->password = MD5('drivertiara'); // default password
        $driver->save();

        return redirect()->back()->with('success', 'Password berhasil direset ke drivertiara.');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 15

    public function edit(Driver $id)
    {
        $this->pushToBackStack(skipPatterns: ['/master/driver/edit', '/master/driver/update']);
        $form = [
            ['label' => 'Username', 'value' => 'username', 'type' => 'string', 'readonly' => true],
            ['label' => 'Nama Driver', 'value' => 'driver_name', 'type' => 'string'],
            ['label' => 'Alamat', 'value' => 'address', 'type' => 'string'],
            ['label' => 'Nomor Telepon', 'value' => 'phone', 'type' => 'number'],
            ['label' => 'Email', 'value' => 'email', 'type' => 'string'],
            ['label' => 'Plat Nomor', 'value' => 'license_number', 'type' => 'string'],
            ['label' => 'Status', 'value' => 'status', 'type' => 'selection', 'option' => [['value' => 1, 'label' => 'Aktif'], ['value' => 0, 'label' => 'Tidak Aktif']]],

        ];
        $id['primaryKey'] = $id['driver_id'];
        return view('master.driver.edit', ['data' => $id, 'backUrl' => $this->popBackStack(), 'forms' => $form]);
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
        $request->validate([
            'username'            => 'required|string|max:255',
            'driver_name'            => 'required|string|max:255',
            'address'         => 'required|string|max:255',
            'phone'           => 'required|string|max:20',
            'email'           => 'required|email|max:255',
            'license_number'  => 'required|string|max:20',
            'status'          => 'required|in:0,1',
        ]);

        $driver = Driver::where('username', $request->username)->first();

        if (!$driver) {
            return redirect()->back()->with('error', 'Data driver tidak ditemukan.');
        }

        $driver->driver_name           = $request->driver_name;
        $driver->address        = $request->address;
        $driver->phone          = $request->phone;
        $driver->email          = $request->email;
        $driver->license_number = $request->license_number;
        $driver->status         = $request->status;

        try {
            $driver->save();
            return redirect()->back()->with('success', 'Data driver berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data driver: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */
    public function show(Driver $id)
    {
        $this->pushToBackStack(['/master/driver/show']);

        return view('master.driver.show', ['backUrl' => $this->popBackStack(), 'data' => $id, 'forms' => $form]);
    }
    // ganti 22

    public function destroy($username)
    {
        $driver = Driver::where('driver_id', $username)->firstOrFail();
        $driver->delete();

        return redirect()->back()->with('success', 'Driver berhasil dihapus.');
    }
}
