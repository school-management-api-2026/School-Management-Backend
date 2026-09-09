<?php

namespace App\Http\Controllers;

use App\Models\BookAuthors;
use App\Models\Books;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = Books::with('book_authors.author', 'book_copies')->get();

        return response()->json([
            'message' => 'Get all books successfully',
            'data' => $books,
        ], 200);
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
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'isbn' => 'required|string|max:50|unique:books,isbn',
            'category' => 'nullable|string|max:100',
        ]);

        $book = Books::create($validated);

        $authorId = $request->input('author_id');
        if ($authorId) {
            BookAuthors::create(['book_id' => $book->id, 'author_id' => $authorId]);
        }

        $book->load('book_authors.author', 'book_copies');

        return response()->json([
            'message' => 'Created book successfully',
            'data' => $book,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $book = Books::with('book_authors.author', 'book_copies')->findOrFail($id);

        return response()->json([
            'message' => 'Get book by id successfully',
            'data' => $book,
        ], 200);
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
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'isbn' => 'required|string|max:50|unique:books,isbn,' . $id,
            'category' => 'nullable|string|max:100',
        ]);

        $book = Books::findOrFail($id);
        $book->update($validated);

        $authorId = $request->input('author_id');
        if ($authorId) {
            BookAuthors::where('book_id', $book->id)->delete();
            BookAuthors::create(['book_id' => $book->id, 'author_id' => $authorId]);
        }

        $book->load('book_authors.author', 'book_copies');

        return response()->json([
            'message' => 'Updated book successfully',
            'data' => $book,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $book = Books::findOrFail($id);
        $book->delete();

        return response()->json([
            'message' => 'Delete book successfully',
        ], 200);
    }
}