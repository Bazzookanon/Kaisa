@extends('layouts.app')

@section('title', 'Category')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Category Management</h5>
        <a href="{{ route('categories.create') }}" class="btn btn-light btn-sm">
            <i class="bi bi-plus-lg"></i> Add Category
        </a>
    </div>

    <div class="card-body">
        {{-- Search Form --}}
        <form method="POST" action="{{ route('categories.search') }}" class="row g-2 mb-3">
            @csrf
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search by name..." value="">
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
                    <th style="width: 30%">Name</th>
                    <th style="width: 30%">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                <tr>
                    <td>{{ $category->name }}</td>
                    <td>
                        <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this category?')">
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
