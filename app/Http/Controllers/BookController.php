<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use Response;
use DataTables;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            return datatables()->of(Book::select('*'))
                ->addColumn('action', 'action')
                ->addColumn('cover', 'image')
                ->editColumn('status', function ($row) {
                    return $row->status ? 'Published' : 'Not Published';
                })
                ->rawColumns(['action', 'cover'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('home');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'name' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $bookId = $request->book_id;

        // Mengelola gambar
        $image = $request->hidden_image;

        if ($files = $request->file('image')) {
            // Hapus file lama jika ada
            if ($request->hidden_image) {
                \File::delete('public/book/' . $request->hidden_image);
            }

            // Masukkan file baru
            $destinationPath = 'public/book/'; // upload path
            $prefixImage = date('YmdHis') . "." . $files->getClientOriginalExtension();
            $files->move(public_path($destinationPath), $prefixImage);
            $image = $prefixImage;
        }

        // Temukan atau buat buku baru
        $book = Book::find($bookId) ?? new Book();
        // Set atribut
        $book->cover = $image;
        $book->name = $request->name;
        $book->author = $request->author;
        $book->description = $request->description;
        $book->status = $request->status;

        // Simpan buku
        $book->save();

        return Response::json($book);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Temukan buku berdasarkan ID
        $book = Book::find($id);

        // Jika buku tidak ditemukan, kembalikan respons error
        if (!$book) {
            return response()->json(['error' => 'Book not found'], 404);
        }

        // Kembalikan buku sebagai respons JSON
        return response()->json($book);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $book = Book::find($id);

        // Jika buku tidak ditemukan, kembalikan respons error
        if (!$book) {
            return response()->json(['error' => 'Book not found'], 404);
        }

        // Hapus file gambar jika ada
        if ($book->cover) {
            \File::delete('public/book/' . $book->image);
        }

        // Hapus buku
        $book->delete();

        return Response::json(['success' => true]);
    }
}
