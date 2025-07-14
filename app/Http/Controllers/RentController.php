<?php

namespace App\Http\Controllers;

use App\Models\Rent;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RentController extends Controller
{
    // 🧾 View user's rents
    public function index()
    {
        return Rent::with('book')->where('user_id', Auth::id())->get();
    }

    // 🚀 Rent a book
    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $book = Book::findOrFail($request->book_id);

        if ($book->status !== 'available') {
            return response()->json(['message' => 'Book not available'], 400);
        }

        $rent = Rent::create([
            'user_id'    => Auth::id(),
            'book_id'    => $book->id,
            'rented_at'  => now(),
            'status'     => 'active',
        ]);

        $book->update(['status' => 'rented']);

        return response()->json($rent, 201);
    }

    // 🔙 Return a book
    public function returnBook($id)
    {
        $rent = Rent::where('id', $id)
                    ->where('user_id', Auth::id())
                    ->where('status', 'active')
                    ->firstOrFail();

        $rent->update([
            'returned_at' => now(),
            'status'      => 'returned',
        ]);

        $rent->book->update(['status' => 'available']);

        return response()->json(['message' => 'Book returned successfully']);
    }
}

