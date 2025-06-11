<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BaseController;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\BookResource;

class BookController extends BaseController
{
    public function index()
    {
        $books = Book::all();
        return $this->sendResponse(BookResource::collection($books), 'Books retrieved successfully.');
    }

    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required|string',
            'author' => 'required|string',
            'publication' => 'required|string'
        ]);

        if($validator->fails())
        {
            return $this->sendError('Validation Error', $validator->errors());
        }

        $book = Book::create($input);

        return $this->sendResponse(new BookResource($book), 'Book registered successfully');
    }

    public function show($id)
    {
        $book = Book::find($id);

        if(is_null($book)){
            return $this->sendError('Book not found');
        }

        return $this->sendResponse(new BookResource($book), 'Book retrieved successfully');
    }

    public function update(Request $request, Book $book)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required|string',
            'author' => 'required|string',
            'publication' => 'required|string'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error', $validator->errors());
        }

        $book->name = $input['name'];
        $book->author = $input['author'];
        $book->publication = $input['publication'];

        $book->save();

        return $this->sendResponse(new BookResource($book),'Book updated successfully');
    }

    public function destroy(Book $book)
    {
        $book->delete();
        
        return $this->sendResponse([], 'Book deleted successfully');
    }
}
