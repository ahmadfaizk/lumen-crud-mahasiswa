<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Mahasiswa;
use Validator;

class MahasiswaController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index() {
        $mahasiswa = Mahasiswa::all();
        return response()->json([
            'error' => false,
            'message' => 'Succes Get List Mahasiswa',
            'data' => $mahasiswa
        ]);
    }

    public function get($id) {
        $mahasiswa = Mahasiswa::find($id);
        if($mahasiswa == null) {
            return  response()->json([
                'error' => true,
                'message' => 'Error, ID Mahasiswa Not Found',
                'data' => null
            ]);
        } else {
            return response()->json([
                'error' => false,
                'message' => 'Succes Get Mahasiswa',
                'data' => $mahasiswa
            ]);
        }
    }

    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'nrp' => 'required|int|unique:mahasiswa',
            'nama' => 'required|string',
            'foto' => 'required|image',
            'alamat' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => 'Error',
                'errors' => $validator->errors(),
                'data' => null
            ]);
        }

        $foto = $request->file('foto');
        $t = time();
        $foto_name = $t . '.' . $foto->getClientOriginalExtension();
        $foto->move('foto', $foto_name);

        $mahasiswa = Mahasiswa::create([
            'nrp' => $request->nrp,
            'nama' => $request->nama,
            'foto' => $foto_name,
            'alamat' => $request->alamat
        ]);

        return response()->json([
            'error' => false,
            'message' => 'Succes Add Mahasiswa!',
            'data' => $mahasiswa
        ]);
    }

    public function update(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|int',
            'nrp' => 'required|int',
            'nama' => 'required|string',
            'foto' => 'nullable|image',
            'alamat' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => 'Error',
                'errors' => $validator->errors(),
                'data' => null
            ]);
        }

        $id = $request->id;
        $mahasiswa = Mahasiswa::find($id);
        if($mahasiswa == null) {
            return  response()->json([
                'error' => true,
                'message' => 'Error, ID Mahasiswa Not Found',
                'data' => ''
            ]);
        }

        if($request->hasFile('foto')) {
            unlink('foto/' . $mahasiswa->foto);
            $foto = $request->file('foto');
            $t = time();
            $foto_name = $t . '.' . $foto->getClientOriginalExtension();
            $foto->move('foto', $foto_name);
            $mahasiswa->foto = $foto_name;
        }

        $mahasiswa->nama = $request->nama;
        $mahasiswa->nrp = $request->nrp;
        $mahasiswa->alamat = $request->alamat;
        $mahasiswa->save();

        return response()->json([
            'error' => false,
            'message' => 'Succes Update Mahasiswa!',
            'data' => $mahasiswa
        ]);
    }

    public function delete(Request $request) {
        $id = $request->id;
        $mahasiswa = Mahasiswa::find($id);
        if($mahasiswa == null) {
            return  response()->json([
                'error' => true,
                'message' => 'Error, ID Mahasiswa Not Found',
                'data' => ''
            ]);
        }
        $file_path = 'foto/'.$mahasiswa->foto;
        unlink($file_path);
        $mahasiswa->delete();
        return response()->json([
            'error' => false,
            'message' => 'Succes Delete Mahasiswa',
            'data' => $mahasiswa
        ]);
    }
}