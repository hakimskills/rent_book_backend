<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    // 📚 List all books by owner
    public function index()
    {
        return Book::where('user_id', Auth::id())->get();
    }

    // ➕ Add a new book
    public function store(Request $request)
{
    $request->validate([
        'title'       => 'required|string|max:255',
        'author'      => 'required|string|max:255',
        'description' => 'nullable|string',
        'category'    => 'nullable|string',
        'cover'       => 'nullable|image|max:2048',
    ]);

    $coverPath = $request->file('cover')?->store('covers', 'public');

    $book = Book::create([
        'user_id'     => Auth::id(),
        'title'       => $request->title,
        'author'      => $request->author,
        'description' => $request->description,
        'category'    => $request->category,
        'status'      => 'available',
        'cover'       => $coverPath,
    ]);

    return response()->json([
        'message' => 'Book created successfully',
        'book' => $book
    ], 201);
}


    // 🔄 Update a book
    public function update(Request $request, Book $book)
    {
        $this->authorize('update', $book); // optional: policy

        $request->validate([
            'title'       => 'sometimes|string|max:255',
            'author'      => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'category'    => 'nullable|string',
            'status'      => 'nullable|in:available,rented',
            'cover'       => 'nullable|image|max:2048',
        ]);

        // Optional: delete old cover if replaced
        if ($request->hasFile('cover')) {
            if ($book->cover) {
                Storage::disk('public')->delete($book->cover);
            }
            $book->cover = $request->file('cover')->store('covers', 'public');
        }

        $book->update($request->only(['title', 'author', 'description', 'category', 'status']));

        return response()->json($book);
    }

    // ❌ Delete a book
    public function destroy(Book $book)
    {
        $this->authorize('delete', $book); // optional: policy

        // Delete cover image if exists
        if ($book->cover) {
            Storage::disk('public')->delete($book->cover);
        }

        $book->delete();
        return response()->json(['message' => 'Book deleted']);
    }
}
