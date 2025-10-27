@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Product Management</h5>
        <a href="{{ route('products.create') }}" class="btn btn-light btn-sm">
            <i class="bi bi-plus-lg"></i> Add Product Jonathan Non
        </a>
    </div>

    <div class="card-body">
        {{-- Search Form --}}
        <form method="POST" action="{{ route('products.search') }}" class="row g-2 mb-3">
            @csrf
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search" value="">
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary" type="submit">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </form>

        {{-- Table --}}
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Image</th>
                    <th style="width: 30%">Name</th>
                    <th style="width: 15%">Price</th>
                    <th style="width: 25%">Category</th>
                    <th style="width: 30%">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td>
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="Product Image" class="img-thumbnail" style="max-height: 60px; max-width: 60px;">
                        @else
                            <span class="text-muted">No Image</span>
                        @endif
                    </td>
                    <td>{{ $product->name }}</td>
                    <td>PHP{{ number_format($product->price, 2) }}</td>
                    <td>{{ $product->category->name ?? 'N/A' }}</td>
                    <td>
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?')">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
